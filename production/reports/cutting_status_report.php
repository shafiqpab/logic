<?php
/*********************************************** Comments *************************************
*	Purpose			:
*	Functionality	:
*	JS Functions	:
*	Created by		:	Md. Shafiqul Islam Shafiq
*	Creation date 	: 	29-09-2018
*	Updated by 		:
*	Update date		:
*	QC Performed BY	:
*	QC Date			:
*	Comments		:
************************************************************************************************/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Cutting Status Report", "../../", 1, 1, $unicode,1,1);
?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission = '<? echo $permission; ?>';

var tableFilters =
	{
		col_operation: {
			//id: ["grand_color_total","grand_fab_req","grand_fin_fab_req","grand_fab_issued_balance","grand_fab_possible_qty","grand_today_lay","grand_total_lay","grand_lay_balance","grand_today_cutting","grand_total_cutting","grand_cut_balance","grand_today_send","grand_total_send","grand_today_rcv","grand_total_rcv","grand_embl_balance","grand_today_input","grand_total_input","grand_input_balance","grand_inhand_qty"],
			//col: [17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50],
			//col: [9,11,12,13,14,15,16,17,18,19,20,21,22,23,24,,25,26,27,28,29],
			//operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			//write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
 	}

function fn_show_report(type)
{
	//alert("su..re"); return;
	var company=$("#cbo_company_name").val();
	var job=$("#txt_job_no").val();
	var date=$("#txt_date").val();
	var style=$("#txt_style_ref_id").val();
	var po=$("#txt_order_no").val();

	if(job=='' && style=='' && po=='')
	{
	  if (form_validation('cbo_company_name*txt_date','Company Name*Cutting Date')==false)
	  {
		  return;
	  }
     }



	// else if (form_validation('cbo_company_name*txt_order_no','Company Name*Order No')==false)
	// {
	// 	return;
	// }
	// else if($('#txt_ref_no').val()=="" && $('#txt_order_no').val()=="" &&  $('#txt_date').val()=="" )
	// {
	// 	form_validation('txt_date','Cutting Date')==false
	// 	return;
	// }


		var data="action=report_generate&type="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_working_company_name*cbo_location_name*txt_ref_no*txt_job_no*txt_internal_ref_no*txt_date*txt_job_no_hidden*txt_order_no*hide_order_id*cbo_style_owner',"../../");
		freeze_window('3');
		http.open("POST","requires/cutting_status_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;

}

function fn_report_generated_reponse()
{
 	if(http.readyState == 4)
	{
  		//alert(http.responseText);
		show_msg('3');
		var response=trim(http.responseText).split("####");
		$('#report_container2').html(response[0]);
		setFilterGrid("table_filter",-1,tableFilters);
		document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>'+'&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';

		release_freezing();
 	}
}

function new_window()
{
	document.getElementById('scroll_body').style.overflow='auto';
	document.getElementById('scroll_body').style.maxHeight='none';
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close();
	document.getElementById('scroll_body').style.overflowY='scroll';
	document.getElementById('scroll_body').style.maxHeight='300px';
}

//job search popup
function openmypage_job()
{
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}

	var companyID = $("#cbo_company_name").val();
	var buyer_name = $("#cbo_buyer_name").val();
	var sytle_ref_no = $("#txt_ref_no").val();
	var page_link='requires/cutting_status_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&sytle_ref_no='+sytle_ref_no;
	var title='Job No Search';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var job_no=this.contentDoc.getElementById("hide_job_no").value;
		$('#txt_job_no').val(job_no);
	}
}

function openmypage_order()
{
	if(form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}

	var companyID = $("#cbo_company_name").val();
	var job_no = $("#txt_job_no").val();
	var page_link='requires/cutting_status_report_controller.php?action=order_no_search_popup&companyID='+companyID+'&job_no='+job_no;
	var title='Order No Search';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var order_no=this.contentDoc.getElementById("hide_order_no").value;
		var order_id=this.contentDoc.getElementById("hide_order_id").value;

		$('#txt_order_no').val(order_no);
		$('#hide_order_id').val(order_id);
	}
}

// for report lay chart
function generate_report_lay_chart(data)
{
	var action	= 'cut_lay_entry_report_print';
	window.open("../../prod_planning/cutting_plan/requires/cut_and_lay_ratio_wise_entry_controller_urmi.php?data=" + data+'&action='+action, true );
}

function onchange_buyer()
{
	if($('#cbo_buyer_name').val() !=0)
	{
		document.getElementById("cap_cut_date").style.color = "blue";
	}
	else
	{
		document.getElementById("cap_cut_date").style.color = "";
	}
}


function openmypage_style()
{
	if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();
		var buyer = $("#cbo_buyer_name").val();
		//var job_year = $("#cbo_job_year").val();
		//var txt_style_ref_no = $("#txt_ref_no").val();
		var txt_style_ref_id = $("#txt_style_ref_id").val();
		var txt_style_ref = $("#txt_style_ref").val();
		var page_link='requires/cutting_status_report_controller.php?action=style_search_popup&company='+company+'&buyer='+buyer+'&txt_style_ref_no='+txt_style_ref_no+'&txt_style_ref_id='+txt_style_ref_id+'&txt_style_ref='+txt_style_ref;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=440px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			//var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			//var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			data=style_no.split("_");
			//$("#txt_style_ref").val(data[3]);
			//

			$('#txt_style_ref_id').val(data[0]);
			$('#txt_job_no').val(data[1]);
			$("#txt_job_no_hidden").val(data[1]);
	  		$('#txt_ref_no').val(data[2]);
	  		$('#txt_job_no').attr('disabled','true');
		}
}

function generate_report_bundle_list(cut_no)
{
    var action = "cut_lay_bundle_print";
    var data = return_global_ajax_value( cut_no, 'str_data_from_cut_no', '', 'requires/cutting_status_report_controller');
    var data = data.trim();
    //alert (data);
    window.open("../../prod_planning/cutting_plan/requires/cut_and_lay_ratio_wise_entry_controller_urmi.php?data=" + data+'&action='+action, true );
}


function openmypage_fab_issue(po_id,color,type,action)
{
	var data=po_id+'_'+color+'_'+type;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/cutting_status_report_controller.php?action='+action+'&data='+data, 'Finish Fabric Issue', 'width=680px,height=250px,center=1,resize=0,scrolling=0','../');
}

function openmypage_production_popup(po,item,color,wo_company,type,day,action,title,popup_width,popup_height)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/cutting_status_report_controller.php?po='+po+'&action='+action+'&item='+item+'&color='+color+'&wo_company='+wo_company+'&type='+type+'&day='+day+get_submitted_data_string('txt_date',"../../"), title, 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');
}

function getCompanyId()
{
    var company_id = document.getElementById('cbo_company_name').value;
    //var search_type = document.getElementById('cbo_search_by').value;
    if(company_id !='') {
      var data="action=load_drop_down_buyer&data="+company_id;
      //alert(data);die;
      http.open("POST","requires/cutting_status_report_controller.php",true);
      http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
      http.send(data);
      http.onreadystatechange = function(){
          if(http.readyState == 4)
          {
              var response = trim(http.responseText);
              //$('#location_td').html(response);
              $('#buyer_td').html(response);
             // set_multiselect('cbo_location','0','0','','0');
              //set_multiselect('cbo_buyer_name','0','0','','0');
             // fn_buyer_visibility(search_type);
          }
      };
    }
}

function load_location_dropdown(){
    if ($('#cbo_company_name').val() != "" ){
        load_drop_down( 'requires/cutting_status_report_controller', $('#cbo_company_name').val(), 'load_drop_down_location', 'location_td');
        set_multiselect('cbo_location_name','0','0','','0');
    }else{
        if($('#cbo_working_company_name').val() != ""){
            load_drop_down( 'requires/cutting_status_report_controller', $('#cbo_working_company_name').val(), 'load_drop_down_location', 'location_td');
            set_multiselect('cbo_location_name','0','0','','0');
        }
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
<form id="cuttingLayProductionReport_1">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1520px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
         <div id="content_search_panel">
         <fieldset style="width:1520px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th class="must_entry_caption">Company Name</th>
                   	<th>Style Owner</th>
                   	<th>Working Company</th>
                   	<th>Location</th>
                    <th>Buyer Name</th>
                    <th>Style Ref.</th>
                    <th>Job No</th>
                    <th>Internal Ref</th>
                    <th>PO NO.</th>
                    <th class="must_entry_caption">Cutting Date</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('cuttingLayProductionReport_1','','','','')" class="formbutton" style="width:50px" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td id="company_td">
							<? echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );

							//get_php_form_data(this.value,'size_wise_repeat_cut_no','../../prod_planning/cutting_plan/requires/cut_and_lay_entry_controller' );
							?>
                        	<input type="hidden" name="size_wise_repeat_cut_no" id="size_wise_repeat_cut_no" readonly>
                        </td>

                        <td>
							<?
							echo create_drop_down( "cbo_style_owner", 142, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select  --", $selected, 1 );
							?>
                        </td>

                        <td>
                        <?
	                       echo create_drop_down( "cbo_working_company_name", 142, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select  --", $selected, 1 );// get_php_form_data(this.value,'size_wise_repeat_cut_no','requires/cut_and_lay_ratio_wise_entry_controller_urmi' );
	                     ?>
                        </td>
                        <td id="location_td">
                        <?
	                       echo create_drop_down( "cbo_location_name", 142, "select id,location_name from lib_location comp where status_active=1 and is_deleted=0  order by location_name","id,location_name", 0, "-- Select  --", $selected, "" );// get_php_form_data(this.value,'size_wise_repeat_cut_no','requires/cut_and_lay_ratio_wise_entry_controller_urmi' );
	                     ?>
                        </td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "","","" ); ?></td>


                        <td>
                       <!-- <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:100px;" />-->
                        <input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:100px" placeholder="Browse"  onDblClick="openmypage_style();" readonly  />
                        <input type="hidden" name="txt_style_ref_id" id="txt_style_ref_id"/>    <input type="hidden" name="txt_style_ref_no" id="txt_style_ref_no"/>                       </td>
                        <td>
                            <!--<input type="text" name="txt_cutting_no" id="txt_cutting_no" class="text_boxes" style="width:100px;" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).ondblclick()" />-->
                          <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:100px" onDblClick="openmypage_job();"    />
                            <input type="hidden" name="update_id"  id="update_id" readonly />
                            <input type="hidden" name="txt_job_no_hidden"  id="txt_job_no_hidden"  />
                        </td>
						<td>
                            <input type="text" name="txt_internal_ref_no" id="txt_internal_ref_no" class="text_boxes" style="width:130px" placeholder="Write" autocomplete="off" >
                        </td>
                        <td>
                            <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:100px" placeholder="Browse Or Write" onDblClick="openmypage_order();" onChange="$('#hide_order_id').val('');" autocomplete="off">
                            <input type="hidden" name="hide_order_id" id="hide_order_id" readonly>
                        </td>

                        <td><input name="txt_date" id="txt_date" class="datepicker" style="width:70px"  placeholder="From Date" readonly></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:100px" value="Today Production" onClick="fn_show_report(1)" />&nbsp;
                        <input type="button" id="show_button" class="formbutton" style="width:100px" value="Production Wise" onClick="fn_show_report(2)" />
						<input type="button" id="show_button" class="formbutton" style="width:100px" value="Cutting Details" onClick="fn_show_report(3)" />
						<input type="button" id="show_button" class="formbutton" style="width:100px" value="Cutting Wise" onClick="fn_show_report(4)" />
                        </td>
                    </tr>
                    <!-- <tr>
                	<td colspan="10"><? //echo load_month_buttons(1); ?></td>
               		</tr> -->
                </tbody>
            </table>

        </fieldset>
    	</div>
         <div style="display:none" id="data_panel"></div>
    	<div id="report_container" align="center" style="padding: 10px;"></div>
    	<div id="report_container2" align="left"></div>
    </div>

 </form>
</body>
<script>
	set_multiselect('cbo_company_name','0','0','','0');
	setTimeout[($("#company_td a").attr("onclick","disappear_list(cbo_company_name,'0');getCompanyId();load_location_dropdown();") ,3000)];
	set_multiselect('cbo_working_company_name','0','0','','','load_location_dropdown()');
	set_multiselect('cbo_location_name','0','0','','0');
</script>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<?
		$sql=sql_select("select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name");
		$company_id='';
		$is_single_select=0;
		if(count($sql)==1){
			$company_id=$sql[0][csf('id')];
			$is_single_select=1;
			?>
			<script>
			console.log('Arnab');
			set_multiselect('cbo_company_name','0','<? echo $is_single_select?>','<? echo $company_id?>','0');
			
			</script>
			
			<?
		}
		
		?>

</html>