<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Style wise Yarn Cost Report [Sales]
Functionality	:
JS Functions	:
Created by		:	Abu Sayed
Creation date 	: 	27-02-2023
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
//echo load_html_head_contents("Style wise Yarn Cost Report [Sales]", "../../../", 1, 1,'',1,1);
echo load_html_head_contents("Style wise Yarn Cost Report [Sales]","../../../", 1, 1, $unicode,1,1);
?>

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";
var permission = '<? echo $permission; ?>';

function fn_report_generated(type)
{
	var txt_job_no=$("#txt_job_no").val();
	var txt_style_no=$("#txt_style_no").val();
	var txt_sales_order_no=$("#txt_sales_order_no").val();

	if (form_validation('cbo_company_name','Comapny Name')==false)
	{
		return;
	}
	if(txt_job_no =="" && txt_style_no =="" && txt_sales_order_no =="" )
    {
        alert("Please select either Job No ...");
        return;
    }
	// else
	// {
		if(type==1)
		{
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_sales_order_no*txt_sales_order_id*cbo_year*txt_job_no*txt_style_no',"../../../")+ "&type=" + type;
		}

		freeze_window(3);
		http.open("POST","requires/style_wise_yarn_cost_sales_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	//}
}


function fn_report_generated_reponse()
{
 	if(http.readyState == 4)
	{
  		var response=trim(http.responseText).split("****");
  		var rpt_type = response[2];
		$('#report_container2').html(response[0]);
		document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';



		var tableFilters =
		{
			col_operation:
			{
				id: ["total_order_qnty","value_tot_mkt_required","value_tot_required_cost","value_tot_booking_qty","value_yarn_iss_qty","value_yarn_iss_cost","value_req_bal_qty","value_cost_bal_cost"],
				col: [10,15,16,17,18,19,20,21],
				operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}
		}
	 	setFilterGrid("table_body",-1,tableFilters);
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
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close();
	$("#table_body tr:first").show();

	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="400px";
}


function generate_worder_report(type,booking_no,company_id,order_id,fabric_nature,fabric_source,job_no,approved,action,entryForm)
{
	var data="action="+action+
				'&txt_booking_no='+"'"+booking_no+"'"+
				'&cbo_company_name='+"'"+company_id+"'"+
				'&txt_order_no_id='+"'"+order_id+"'"+
				'&cbo_fabric_natu='+"'"+fabric_nature+"'"+
				'&cbo_fabric_source='+"'"+fabric_source+"'"+
				'&id_approved_id='+"'"+approved+"'"+
				'&txt_job_no='+"'"+job_no+"'";
				//alert(action)
	if(type==1)
	{
		http.open("POST","../../../order/woven_order/requires/short_fabric_booking_controller.php",true);
	}
	else if(type==2)
	{
		if(entryForm==118)
		{
			http.open("POST","../../../order/woven_order/requires/fabric_booking_urmi_controller.php",true);
		}
		else
		{
			http.open("POST","../../../order/woven_order/requires/fabric_booking_controller.php",true);
		}
	}
	else
	{
		http.open("POST","../../../order/woven_order/requires/sample_booking_controller.php",true);
	}

	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_fabric_report_reponse;
}

function generate_fabric_report_reponse()
{
	if(http.readyState == 4)
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
		d.close();
	}
}

function openmypage(po_id,type,tittle,company_id)
{
	var popup_width='';
	if(type=="yarn_issue_cost")
	{
		popup_width='990px';
	}
	else
	{
		popup_width='880px';
	}

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_yarn_cost_sales_report_controller.php?po_id='+po_id+'&action='+type+'&company_id='+company_id, tittle, 'width='+popup_width+', height=420px, center=1, resize=0, scrolling=0', '../../');
}

function date_fill_change(str)
{
	if (str==1)
	{
		document.getElementById('search_date_td').innerHTML='Ship Date';
	}
	else if(str==2)
	{
		document.getElementById('search_date_td').innerHTML='Ref. Close Date';
	}
	else
	{
		document.getElementById('search_date_td').innerHTML='Ship Date';
	}
}


function openmypage_sales_order_no()
{
	var companyID = $("#cbo_company_name").val();
	var year = $("#cbo_year").val();

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_yarn_cost_sales_report_controller.php?action=sales_order_no_popup&companyID='+companyID+'&year='+year, 'Sales Order Info Details', 'width=510px,height=340px,center=1,resize=0,scrolling=0','../../');

	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var job_no_des=this.contentDoc.getElementById("hidden_job_no").value; //Access form field with id="emailfield"
		var job_no_id=this.contentDoc.getElementById("hidden_job_no_id").value;
		var po_job_no_prefix=this.contentDoc.getElementById("hidden_po_job_no_prefix").value;

		$("#txt_sales_order_no").val(job_no_des);
		$("#txt_sales_order_id").val(job_no_id);
		$("#txt_job_no").val(po_job_no_prefix);
	}
}

</script>

<style>
	hr
	{
		color: #676767;
		background-color: #676767;
		height: 1px;
	}
</style>
</head>

<body onLoad="set_hotkey();">

<form id="fabricReceiveStatusReport_1">
    <div style="width:100%;" align="center">

        <? echo load_freeze_divs ("../../../",'');  ?>

         <h3 style="width:725px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
         <div id="content_search_panel" >
         <fieldset style="width:725px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Buyer Name</th>
                    <th>Job Year</th>
                    <th>Job No</th>
                    <th>Style  No</th>
                    <th>Sales Order No</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('fabricReceiveStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:80px" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td>
                            <?
                                echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/style_wise_yarn_cost_sales_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <?
                                echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
						    ?>

                        </td>
                        <td>
                        	<?
								echo create_drop_down( "cbo_year", 80, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
							?>
                        </td>
                        <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" placeholder="Write" /></td>
						<td><input type="text" name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:100px" placeholder="Write" /></td>
                        <td>
							<input type="text" name="txt_sales_order_no" id="txt_sales_order_no" class="text_boxes" style="width:130px" onDblClick="openmypage_sales_order_no();" placeholder="Write/Browse" />
							<input type="hidden" id="txt_sales_order_id" name="txt_sales_order_id" class="text_boxes" style="width:70px" value=""  />
						</td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_report_generated(1)" />
                        </td>
                    </tr>
                </tbody>
            </table>
            <table>
            	<tr>
                	<td>
 						<? //echo load_month_buttons(1); ?>
                   	</td>
                </tr>
            </table>
            <br />
        </fieldset>
    	</div>
    </div>
    <div style="display:none" id="data_panel"></div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$('#cbo_discrepancy').val(0);
</script>
</html>
