<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Date Wise Defect  Report
Functionality	:	
JS Functions	:
Created by		:	Aziz
Creation date 	: 	13-FEB-2021
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
echo load_html_head_contents("Date Wise Defect Report", "../../", 1, 1, $unicode, 1, 1);
//echo load_html_head_contents("Date Wise Defect Report", "../../", "", $popup, 1,1);
?>	
<script src="../../Chart.js-master/Chart.js"></script>
<script src="../../ext_resource/hschart/hschart.js"></script>
<script type="text/javascript">



function hs_chart(gtype,cData,dataTitle){
	//	alert(cData);
	var cData=eval(cData);

    $('#container'+gtype).highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie',
			animation:true,
			borderColor: "#4572A7"
        },
        title: {
            text: 'TOP 6 DEFECT '+dataTitle,
			style: {
				 fontSize: '16px',
				 fontWeight: 'bold'
			  }
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>',
			backgroundColor: 'rgba(219,219,216,0.8)',
			borderWidth:2
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            name: dataTitle,
            colorByPoint: true,
				data: cData
        		}]
    });

}
//Measurement Start
function hs_chart_mm(gtype,cData,dataTitle){
		
	var cData=eval(cData);
	//alert(cData);
    $('#container'+gtype).highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie',
			animation:true,
			borderColor: "#4572A7"
        },
        title: {
            text: 'Measurement '+dataTitle,
			style: {
				 fontSize: '16px',
				 fontWeight: 'bold'
			  }
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>',
			backgroundColor: 'rgba(219,219,216,0.8)',
			borderWidth:2
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            name: dataTitle,
            colorByPoint: true,
				data: cData
        		}]
    });

}
//MM End
</script>

<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	var tableFiltersSummary =
	{
		col_operation: {
			id: ['tot_order_qty_id','tot_cutting_qty_id','tot_input_qty_id','tot_poly_qty_id','tot_reject_qty_finishing_id','tot_finishing_qty_id','tot_air_qty_foc_id','tot_air_qty_claim_id','tot_sea_qty_id','tot_shipment_qty_id','tot_excess_qty_id','tot_short_qty_id'],
			col: [3,4,5,6,7,8,9,10,11,12,13,14],
			operation: ['sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum'],
			write_method: ['innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML']
		}
	} 

	function open_job_no()
	{
		$("#txt_job_no").val("");
		var page_link='requires/date_wise_sewing_defect_report_controller.php?action=job_popup';
		var title="Search Job Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform = this.contentDoc.forms[0];
			var job_id  = this.contentDoc.getElementById("hide_job_id").value;
			var job_no  = this.contentDoc.getElementById("hide_job_no").value;
			$("#txt_job_no").val(job_no);
			$("#hidden_job_id").val(job_id);
		}
	}
	
	function open_order_no()
	{
		$("#txt_order_no").val("");
		var page_link='requires/date_wise_sewing_defect_report_controller.php?action=order_popup';
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform  = this.contentDoc.forms[0]; 
			var order_id = this.contentDoc.getElementById("hide_order_id").value;
			var order_no = this.contentDoc.getElementById("hide_order_no").value;
			$("#txt_order_no").val(order_no);
			$("#hidden_order_id").val(order_id); 
		}
	}

	function fn_generate_report(type)
	{

		 var txt_ref_no=document.getElementById('txt_ref_no').value;
		  var cbo_line_id=document.getElementById('cbo_line_id').value;
		  var date_from=document.getElementById('txt_date_from').value;
		//alert(date_from);
		if(txt_ref_no=="" || txt_ref_no==0)
		{  
			if(form_validation('txt_date_from','Prod Date')==false )
			{
			 return;
			}
		}
		var report_title=$( "div.form_caption" ).html();
//alert(cbo_line_id);
		var data="action=generate_report"+get_submitted_data_string('cbo_lc_location*cbo_company_name*cbo_work_company*cbo_wk_location*cbo_floor_name*cbo_line_id*txt_ref_no*hide_order_id*txt_style_ref*txt_style_id*txt_date_from',"../../")+'&type='+type+'&report_title='+report_title+'&date_from='+date_from;
		//var data="action=generate_report"+get_submitted_data_string('cbo_lc_location*cbo_company_name*cbo_work_company',"../../")+'&type='+type+'&report_title='+report_title+'&date_from='+date_from;
		//alert(data);
		 
		freeze_window(3);
		http.open("POST","requires/date_wise_sewing_defect_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			 
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			//setFilterGrid("table_body_summary",-1,tableFiltersSummary);
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none"; 
		//$(".flt").css("display","none");
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		//document.getElementById('scroll_body').style.overflowY="scroll"; 
		//document.getElementById('scroll_body').style.maxHeight="400px";
		//$(".flt").css("display","block");
	}
	
	function openmypage_challan_popup(company_id,work_comp_ids,order_id,job_no,buyer_id,location_ids,floor_ids,txt_date_from,txt_date_to,action)
	{
		var data=company_id+'**'+work_comp_ids+'**'+order_id+'**'+job_no+'**'+buyer_id+'**'+location_ids+'**'+floor_ids+'**'+txt_date_from+'**'+txt_date_to;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/date_wise_sewing_defect_report_controller.php?data='+data+'&action='+action, 'Channan Details', 'width=630px,height=400px,center=1,resize=0,scrolling=0','../');
	}
	 

	function reset_form()
	{
		$("#hidden_style_id").val("");
		$("#hidden_order_id").val("");
		$("#hidden_job_id").val("");
		
	}

	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=='#33CC00')
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor='#33CC00';
		}
	}	

	function fn_on_change()
	{
		var cbo_company = $("#cbo_company_name").val();
		//var cbo_working_company_name = $("#cbo_working_company_name").val();
		load_drop_down( 'requires/date_wise_sewing_defect_report_controller', cbo_company, 'load_drop_down_location', 'td_lc_location' );
		set_multiselect('cbo_lc_location','0','0','','0','fn_on_change_floor()');
	}
	function fn_on_change_wk()
	{
		var wk_company_name = $("#cbo_work_company").val();
		//var cbo_working_company_name = $("#cbo_working_company_name").val();
		load_drop_down( 'requires/date_wise_sewing_defect_report_controller', wk_company_name, 'load_drop_down_location_wk', 'location_wk_td' );
		set_multiselect('cbo_wk_location','0','0','','0','fn_on_change_floor()');
	}
	function fn_on_change_floor()
	{
		var wk_location = $("#cbo_wk_location").val();
		if(wk_location=="")
		{
			var wk_location = $("#cbo_lc_location").val();
		}
		//var cbo_working_company_name = $("#cbo_working_company_name").val();
		load_drop_down( 'requires/date_wise_sewing_defect_report_controller', wk_location, 'load_drop_down_floor', 'floor_wk_td' );
		set_multiselect('cbo_floor_name','0','0','','0','fn_on_change_line()');
	}
	function fn_on_change_line()
	{
		
		var wk_company_name = $("#cbo_work_company").val();
		var wk_location = $("#cbo_wk_location").val();
		var wk_floor_name = $("#cbo_floor_name").val();
	

		//var cbo_working_company_name = $("#cbo_working_company_name").val();
		load_drop_down( 'requires/date_wise_sewing_defect_report_controller', wk_company_name+'_'+ wk_location +'_'+ wk_floor_name, 'load_drop_down_floor_line', 'line_wk_td' );
		set_multiselect('cbo_line_id','0','0','','0','0');
	}
	
	function openmypage_order(type)
    {
         
      //  var style_owner = $("#cbo_style_owner").val();
		var company_name = $("#cbo_company_name").val();
		var lc_location = $("#cbo_lc_location").val();
	
			if(form_validation('cbo_company_name','Company')==false)
			{
				return;
			}
		 
			
			if(type==1)
			{
				var title="Master Style";
			}
			else
			{
				var title="Merch Style";
			}
				//alert(company_name);
		//var page_link='requires/date_wise_sewing_defect_report_controller.php?action=order_no_search_popup&work_company='+work_company+'&company_name='+company_name+'&lc_location='+lc_location+'&type='+type;
		var page_link='requires/date_wise_sewing_defect_report_controller.php?action=order_no_search_popup&company_name='+company_name+'&lc_location='+lc_location+'&type='+type;
	//	alert(page_link);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=880px,height=420px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var theemail=this.contentDoc.getElementById("hide_order_id"); 
            var theemailv=this.contentDoc.getElementById("hide_ref_no");
            var response=theemail.value.split('_');
            if (theemail.value!="")
            {
               // freeze_window(5);
			  // alert(type);
				if(type==1)
				{
                document.getElementById("hide_order_id").value=theemail.value;
                document.getElementById("txt_ref_no").value=theemailv.value;//
				}
				else
				{
				document.getElementById("txt_style_id").value=theemail.value;
               	 document.getElementById("txt_style_ref").value=theemailv.value;//
				}
               // release_freezing();
            }
        }
    }
	
</script>

</head>
 
<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",'');  ?>
	<h3 style="width:1470px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
   	    <div style="width:100%;" align="center" id="content_search_panel">
			<form id="dateWiseProductionReport_1">    
  			<fieldset style="width:1470px;">
        		<table class="rpt_table" width="1470" cellpadding="0" cellspacing="0" align="center" border="1" rules="all">
	                <thead>                    
	                    <tr>
	                        <th width="150">LC Company</th>
                            <th width="150">LC Location</th>
	                        <th width="150" class="must_entry_caption">W. Company</th>
                             <th width="150">W. Location</th>
	                        <th width="150">Floor</th>
	                        <th width="120">Line</th>
	                        <th width="100">Master Style</th>
	                        <th width="100">Merch Style</th>
	                        <th width="200" class="must_entry_caption">Prod. Date</th>
	                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:50px" value="Reset" onClick="reset_form()"/></th>
	                    </tr>   
	                </thead>
            		<tbody>
		                <tr class="general">
		                    <td align="center" id="td_lc_company"> 
		                        <?
		                            echo create_drop_down( "cbo_company_name", 150, "SELECT id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id,company_name", 0, "-- Select Company --", "", "" );
		                        ?>
		                    </td>
							  <td align="center" id="td_lc_location">
		                        <?
		                            echo create_drop_down( "cbo_lc_location", 150, $blank_array,"","", "-- Select location --", "", "" );
		                        ?>
		                    </td>

		                    <td align="center" id="td_wk_company">
		                        <?
		                            echo create_drop_down( "cbo_work_company", 150, "SELECT id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id,company_name", 0, "-- Select Company --", "", "" );
		                        ?>
		                    </td>

		                    <td align="center" id="location_wk_td"> 
		                        <?
		                            echo create_drop_down( "cbo_wk_location", 150, $blank_array,"","", "-- Select location --", "", "" );
		                            // echo create_drop_down( "cbo_location_name", 200, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0  group by id,location_name  order by location_name","id,location_name", 0, "-- Select location --", $selected, "" );
		                        ?>
		                    </td>

		                    <td align="center" id="floor_wk_td"> 
		                        <?
		                        	//echo create_drop_down( "cbo_floor_name", 150, $blank_array,"","", "-- Select floor --", "", "" );
		                             echo create_drop_down( "cbo_floor_name", 150, $blank_array,"", 0, "-- Select Floor --", $selected, "" );
		                        ?>
		                    </td>

		                    <td align="center" id="line_wk_td">
		                        <? 
								echo create_drop_down( "cbo_line_id", 120, $blank_array,"", "", "-- Select --", $selected, "",0,0 );
		                        ?>
		                    </td>

		                    <td>
		                        <input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:80px" placeholder="Browse Or Write" onDblClick="openmypage_order(1);" onChange="$('#hide_order_id').val('');" autocomplete="off">
                                <input type="hidden" name="hide_order_id" id="hide_order_id" readonly/>
		                    </td>

		                    <td>
		                   <input type="text" id="txt_style_ref" name="txt_style_ref" class="text_boxes" style="width:80px" onDblClick="openmypage_order(2);"
                       placeholder="Wr./Br. Order"  />    <input type="hidden" name="txt_style_id" id="txt_style_id" readonly>
		                    </td>

		                    <td>
                             <?
                           $date=date('d-m-Y', strtotime('-1 day', strtotime(date('d-m-Y'))))
							?>
		                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="Prod. Date" value="<? echo $date;?>" >
		                    	
		                    </td>

		                    <td>
		                        <input type="button" id="show_button" class="formbutton" style="width:40px" value="Show" onClick="fn_generate_report(1)" />  &nbsp;					
                                <input type="button" id="show_button" class="formbutton" style="width:40px" value="Sewing" onClick="fn_generate_report(2)" />  
                                 &nbsp;					
                                <input type="button" id="show_button" class="formbutton" style="width:120px" value="AltDefect/SpotDefect" onClick="fn_generate_report(3)" />                       
		                    </td>
		                </tr>
            		</tbody>
            		<tfoot>
                    <tr>
                        <td colspan="8" align="center">
							<? //echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </tfoot>
        		</table>
  			</fieldset>   
			</form> 
		</div>  
		<div id="report_container" ></div>
		<div id="report_container2"></div>  
        <div id="graph_container" ></div>
 	</div>
</body>
<script> 
	set_multiselect('cbo_company_name','0','0','','0','fn_on_change()');
	set_multiselect('cbo_work_company','0','0','','0','fn_on_change_wk()');
	set_multiselect('cbo_wk_location','0','0','','fn_on_change_floor()');
	set_multiselect('cbo_lc_location','0','0','','0');
	set_multiselect('cbo_line_id','0','0','','0');
	set_multiselect('cbo_floor_name','0','0','','0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
