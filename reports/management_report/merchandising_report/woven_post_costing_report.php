<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Post Costing Report.
Functionality	:
JS Functions	:
Created by		:	Fuad
Creation date 	: 	21-03-2015
Updated by 		: 	zakaria joy
Update date		:	30-05-2023
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
echo load_html_head_contents("Post Costing Report","../../../", 1, 1, $unicode,1,1);
?>

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";
 	function two_slct_field_validation_with_or_con(control,msg_text)
	{
		control=control.split("*");
		msg_text=msg_text.split("*");
		var bgcolor='-moz-linear-gradient(bottom, rgb(254,151,174) 0%, rgb(255,255,255) 10%, rgb(254,151,174) 96%)';
		var new_elem="";
		document.getElementById(control[0]).style.backgroundImage="";
		document.getElementById(control[1]).style.backgroundImage="";
		//var cls=$('#'+control[i]).attr('class');
		if ( trim(document.getElementById(control[0]).value)==0 && trim(document.getElementById(control[1]).value)==0){
			if(trim(document.getElementById(control[0]).value)==0){
				document.getElementById(control[0]).focus();
				document.getElementById(control[0]).style.backgroundImage=bgcolor;
				$('#messagebox_main', window.parent.document).fadeTo(100,1,function(){
					$(this).html('Please Select  '+msg_text[0]+' field Value').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);

				});
				return 0;
			}
			if(trim(document.getElementById(control[1]).value)==0){
				document.getElementById(control[1]).focus();
				document.getElementById(control[1]).style.backgroundImage=bgcolor;
				$('#messagebox_main', window.parent.document).fadeTo(100,1,function(){
					$(this).html('Please Select  '+msg_text[1]+' field Value').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);

				});
				return 0;
			}

		}
		else
	  		return 1;
			//}

	}
	function fn_report_generated(action)
	{

		var pre_cost_version=document.getElementById('cbo_pre_cost_class').value;
		$("#report_type").val(action);

		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else
		{
			/* if(report==1){
				var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*cbo_year*txt_order_no*txt_file_no*txt_ref_no*hide_order_id*txt_date_from*txt_date_to*shipping_status*cbo_order_status*cbo_pre_cost_class*cbo_status*cbo_brand_id*cbo_season_year*txt_season_id',"../../../");
			}
			else if(report==2){
				var data="action=report_generate_actual_cost"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*cbo_year*txt_order_no*txt_file_no*txt_ref_no*hide_order_id*txt_date_from*txt_date_to*shipping_status*cbo_order_status*cbo_pre_cost_class*cbo_status*cbo_brand_id*cbo_season_year*txt_season_id',"../../../");
			}
			else if(report==4){
				var data="action=report_generate3"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*cbo_year*txt_order_no*txt_file_no*txt_ref_no*hide_order_id*txt_date_from*txt_date_to*shipping_status*cbo_order_status*cbo_pre_cost_class*cbo_status*cbo_brand_id*cbo_season_year*txt_season_id',"../../../");
			}
			else
			{
				var data="action=report_generate2"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*cbo_year*txt_order_no*txt_file_no*txt_ref_no*hide_order_id*txt_date_from*txt_date_to*shipping_status*cbo_order_status*cbo_pre_cost_class*cbo_status*cbo_brand_id*cbo_season_year*txt_season_id',"../../../");
				
			} */

			var data="action="+action+""+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*cbo_year*txt_order_no*txt_file_no*txt_ref_no*hide_order_id*txt_date_from*txt_date_to*shipping_status*cbo_order_status*cbo_pre_cost_class*cbo_status*cbo_brand_id*cbo_season_year*txt_season_id',"../../../");


			freeze_window(3);
			if(pre_cost_version==1)
			{
				http.open("POST","requires/woven_post_costing_report_controller.php",true);
			}
			else
			{
				http.open("POST","requires/woven_post_costing_report_controller2.php",true);
			}
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split("****");
			$('#report_container2').html(response[0]);
			var report_type=document.getElementById("report_type").value;
			if(report_type=='report_generate')
			{				
				document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:135px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:120px"/>';
				var tableFilters = 
				{
					col_19: "none",
					col_20: "none",
					col_21: "none",
					col_22: "none",
					col_23: "none",
					col_24: "none",
					col_25: "none",
					col_26: "none",
					col_27: "none",
					col_28: "none",
					col_29: "none",
					col_30: "none",
					col_31: "none",
					col_32: "none",
					col_33: "none",
					col_34: "none",
					col_35: "none",
					
					
					col_operation: {
					id: [],
					col: [],
					operation: [],
					write_method: []
					}
				} 
				setFilterGrid("table_body",-1,tableFilters);
			}
			else if(report_type=='report_generate3'){				
				document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:135px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window_rpt3()" value="Html Preview" name="Print" class="formbutton" style="width:120px"/>';
				var tabtableFilters={};
				setFilterGrid("table_body",-1,tableFilters);
			}
			else if(report_type=='report_generate4'){
				document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:135px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window_rpt4()" value="Html Preview" name="Print" class="formbutton" style="width:120px"/>';
			}
	 		show_msg('3');
			release_freezing();
		}
	}

	function openmypage_order()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}

		var companyID = $("#cbo_company_name").val();
		var cbo_pre_cost_class = $("#cbo_pre_cost_class").val();
		var page_link='requires/woven_post_costing_report_controller.php?action=order_no_search_popup&companyID='+companyID+'&cbo_pre_cost_class='+cbo_pre_cost_class;
		var title='Order No Search';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_no=this.contentDoc.getElementById("hide_order_no").value;
			var order_id=this.contentDoc.getElementById("hide_order_id").value;

			$('#txt_order_no').val(order_no);
			$('#hide_order_id').val(order_id);
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#div_buyer').hide();
		$('#div_summary').hide();
		$("#table_header_1 tbody tr:first").hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#div_buyer').show();
		$('#div_summary').show();
		$("#table_header_1 tbody tr:first").show();
	}
	function new_window_rpt3(){
		$("#table_header_1 tbody tr:first").hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+ '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		$("#table_header_1 tbody tr:first").show();
	}
	function new_window_rpt4()
	{
		$('#div_buyer').hide();
		$('#div_summary').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+ '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		$('#div_buyer').show();
		$('#div_summary').show();
	}

	function new_window2(comp_div, container_div)
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+ '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById(container_div).innerHTML+'</body</html>'); d.close();
	}

	function openmypage(po_id,type,tittle)
	{
		var popup_width='';
		if(type=="dye_fin_cost")
		{
			popup_width='1140px';
		}
		else if(type=="fabric_purchase_cost")
		{
			popup_width='740px';
		}
		else popup_width='1060px';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_post_costing_report_controller.php?po_id='+po_id+'&action='+type, tittle, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '../../');
	}

	function openmypage_mkt(mkt_data,type,tittle)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_post_costing_report_controller.php?mkt_data='+mkt_data+'&action='+type, tittle, 'width=660px, height=200px, center=1, resize=0, scrolling=0', '../../');
	}

	function openmypage_actual(po_id,type,tittle,popup_width,company)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_post_costing_report_controller2.php?po_id='+po_id+'&action='+type+'&company_id='+company, tittle, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '../../');
	}

	function generate_ex_factory_popup(action,job,id,width)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_post_costing_report_controller2.php?action='+action+'&job='+job+'&id='+id, 'Ex-Factory Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}

	function generate_po_report(company_name,po_id,job_no,action,type)
	{
		//var report_title='PO Detail';
		popup_width='940px';
		//alert(po_id);
		//emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_post_costing_report_controller.php?po_id='+po_id+'&company_name='+company_name+'&action='+type, report_title, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '../../');

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_post_costing_report_controller.php?action='+action+'&po_id='+po_id+'&job_no='+job_no+'&company_name='+company_name, 'PO Detail', 'width='+popup_width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}
	 function openmypage_season()
    {
        if(form_validation('cbo_company_name','Company Name')==false)
        {
            return;
        }
        var companyID = $("#cbo_company_name").val();
        var buyerID = $("#cbo_buyer_name").val();
        var txt_job_no = $("#txt_job_no").val();
        var page_link='requires/woven_post_costing_report_controller.php?action=search_season_popup&companyID='+companyID+'&buyerID='+buyerID+'&txt_job_no='+txt_job_no;
        var title='Season Search';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=350px,center=1,resize=1,scrolling=0','../../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var hide_season=this.contentDoc.getElementById("hide_season").value;
			var hide_season_id=this.contentDoc.getElementById("hide_season_id").value;

            $('#txt_season').val(hide_season);
			$('#txt_season_id').val(hide_season_id);
        }
    }
	
	function fnc_brandload()
	{
		var buyer=$('#cbo_buyer_name').val();
		if(buyer!=0)
		{
			load_drop_down( 'requires/woven_post_costing_report_controller', buyer, 'load_drop_down_brand', 'brand_td');
		}
	}
	
</script>

</head>

<body onLoad="set_hotkey(); fnc_brandload();">

<form id="cost_breakdown_rpt">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:1650px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel">
            <fieldset style="width:1650px;">
                <table class="rpt_table" width="1650" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                	<thead>
                    	<tr>
                            <th class="must_entry_caption">Company Name</th>
                            <th>Buyer Name</th>
                              <th>Brand</th>
                              
                            <th>Job Year</th>
                    		<th>Job No</th>
                            <th>Order No</th>
                            <th>Season</th>
               				 <th>Season Year</th>
                            <th>File No</th>
                     		<th>Ref. No</th>
							<th>PO Status</th>
                            <th>Order Status</th>
                            <th>Shipment Status</th>
							<th>Budget Version</th>
                            <th>Shipment Date</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td>
                            <?
                                echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/woven_post_costing_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <?
                                //echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                                echo create_drop_down( "cbo_buyer_name", 120, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
                            ?>
                        </td>
                        <td id="brand_td">
                        <?
                        echo create_drop_down( "cbo_brand_id", 100, $blank_array,"", 1, "-- All Brand --", $selected, "",0,"" );
                        ?>
                    </td>
                        <td>
                        	<?
								echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", 1, "",0,"" );//date("Y",time())
							?>
                        </td>
                        <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" placeholder="Write" /></td>
                        <td>
                            <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:100px" placeholder="Browse Or Write" onDblClick="openmypage_order();" onChange="$('#hide_order_id').val('');" autocomplete="off">
                            <input type="hidden" name="hide_order_id" id="hide_order_id" readonly>
                        </td>
                          <td align="center">
                        <input type="text" name="txt_season" id="txt_season" class="text_boxes" style="width:80px" placeholder="Browse" onDblClick="openmypage_season();" readonly/>
                        <input type="hidden" name="txt_season_id" id="txt_season_id" style="width:50px;"/>
                    </td>
                    <td><? echo create_drop_down( "cbo_season_year", 60, create_year_array(),"", 1,"-- All --", "", "",0,"" ); ?></td>
                        <td>
                           <input type="text" name="txt_file_no" id="txt_file_no" class="text_boxes_numeric" style="width:60px"  placeholder="Write" >
                          </td>
                          <td>
                           <input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:70px"  placeholder="Write" >
                        </td>
                        <td>
                            <?php
							echo create_drop_down("cbo_order_status", 85, $row_status, "", 0, "", 1, "");
							?>
                        </td>
                          <td>
                            <?php
							echo create_drop_down("cbo_status", 85, $order_status, "", 1, "--All--","", "");
							?>
                        </td>
                        <td>
                        	<?
								echo create_drop_down( "shipping_status", 100, $shipment_status,"", 1, "-- Select --", 0, "",0,'','','','','' );
							?>
                        </td>
						 <td width="" align="center">
							<?
								$pre_cost_class_arr = array(1=>'Pre Cost 1',2=>'Pre Cost 2');
								//$dd="search_populate(this.value)";
								echo create_drop_down( "cbo_pre_cost_class", 80, $pre_cost_class_arr,"",0, "--Select--", 2,"",1 );
                            ?>
                        </td>
                        <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:53px" placeholder="From Date" >&nbsp; To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:53px"  placeholder="To Date" ></td>
                        <td>
                        	<input type="hidden" name="report_type" id="report_type">
                        </td>
                    </tr>
					<tr>
                        <td colspan="16" align="center">
                            <? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
					<tr>
						<td colspan="16" align="center">
							<input type="button" id="show_button1" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated('report_generate')" />
							<input type="button" id="show_button3" class="formbutton" style="width:70px" value="Show 2" onClick="fn_report_generated('report_generate2')" />
							<input type="button" id="show_button4" class="formbutton" style="width:70px" value="Show 3" onClick="fn_report_generated('report_generate3')" />
							<input type="button" id="show_button5" class="formbutton" style="width:70px" value="Show 4" onClick="fn_report_generated('report_generate4')" />
                            <input type="button" id="show_button2" class="formbutton" style="width:90px" value="Pre VS Actual" onClick="fn_report_generated('report_generate_actual_cost')" />
						</td>
					</tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
