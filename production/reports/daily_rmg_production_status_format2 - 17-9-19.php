<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Report Will Create Order wise Production Report.
Functionality	:	
JS Functions	:
Created by		:	Rehan Uddin
Creation date 	: 	28-03-2018
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
echo load_html_head_contents("Daily RMG Production Report", "../../", 1, 1,$unicode,'1','1');

?>	
<style type="text/css">
	.inf {
    height: 0px;
	}
</style>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	var tableFilters = 
	{
		loader: true,
		loader_text: "Filtering Data...",
		col_51:'none',
		col_operation:
		{
			id: ["grand_total_order","grand_today_lay","grand_total_lay","grand_today_cut","grand_total_cut","grand_today_sewin","grand_total_sewin","grand_today_sewout","grand_total_sewout","grand_today_sew_rej","grand_total_sewrej","grand_sewingwip","grand_today_poly","grand_total_poly","grand_poly_wip","grand_today_pack","grand_total_pack","grand_pack_wip","grand_today_exfac","grand_total_exfac","grand_exfac_wip"],
			col: [ 10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML" ,"innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML" ,"innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}	
	}
	// 2nd filter start
	var tableFilters2 = 
	{
		loader: true,
		loader_text: "Filtering Data...",
		col_51:'none',
		col_operation:
		{
			id: ["grand_order_qty","grand_fab_req","grand_fab_issue","grand_issue_balance","grand_cut_qty","grand_cut_today","grand_cut_total","grand_cut_balance","grand_input_today","grand_input_total","grand_input_balance","grand_inhand_qty"],
			col: [7,11,12,13,14,17,18,19,20,21,22,23],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML" ,"innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}	
	}
 	

 	function fnc_load_report_format(data)
 	{
 		var report_ids = return_global_ajax_value( data, 'load_report_format', '', 'requires/daily_rmg_production_status_format2_controller');
  		print_report_button_setting(report_ids);
 	}
 	

 	function ClearTextBoxValues()
 	{
 		$("#cbo_buyer_name").val('');
	   // $("#cbo_job_year").val('');
	   $("#txt_style_ref_no").val('');
	   $("#txt_style_ref_id").val('');
	   $("#txt_style_ref").val('');
	   $("#txt_order_id_no").val('');
	   $("#txt_order_id").val('');
	   $("#txt_order").val('');
	   $("#txt_internal_ref").val('');
	   $("#txt_style_ref_number").val('');
	   
	}

 	function print_report_button_setting(report_ids)
 	{
 		if(trim(report_ids)=="")
		{
 			$("#show_button").show();
			$("#show_button2").show();
			$("#show_button1").show();
			$("#show_button3").show();
			$("#show_button4").show();
		}
		else
		{
			var report_id=report_ids.split(",");
			$("#show_button").hide();
			$("#show_button2").hide();
			$("#show_button1").hide();
			$("#show_button3").hide();
			$("#show_button4").hide();
			for (var k=0; k<report_id.length; k++)
			{
				if(report_id[k]==124)
				{
					$("#show_button").show();
				}
				else if(report_id[k]==125)
				{
					$("#show_button2").show();
				}
				else if(report_id[k]==126)
				{
					$("#show_button1").show();
				}
				if(report_id[k]==127)
				{
					$("#show_button3").show();
				}

				if(report_id[k]==128)
				{
					$("#show_button4").show();
				}
				
			}
		}
		

	}


			
	function fn_report_generated(type)
	{
		freeze_window(3);
		if (form_validation('cbo_company_name','Comapny Name')==false)  
		{
			release_freezing();
			return;
		}

		else
		{
			var data="action=report_generate"+"&type="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_location*cbo_floor*cbo_job_year*txt_job_no*hidden_job_id*txt_production_date',"../../");

			http.open("POST","requires/daily_rmg_production_status_format2_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;

		}
		
		
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{

 			var reponse=trim(http.responseText).split("**");
			show_msg('3');
			release_freezing();
			$('#report_container2').html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			//append_report_checkbox('table_header_1',1);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			reponse[2] == 0 ? setFilterGrid("table_body",-1,tableFilters) : setFilterGrid("table_body",-1,tableFilters2);
			// setFilterGrid("table_body",-1,tableFilters);
		  
		}
	}

	function new_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$("#table_body tr:first").hide();
 		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		//$(htmlSearchValue).prependTo("table#table_body tbody");
		//document.getElementById('scroll_body').style.overflowY="auto";
		$("#table_body tr:first").show(); 
		document.getElementById('scroll_body').style.maxHeight="425px";
	}


function openmypage_remarks(po,item,country,color,action)
{
	var data=po+'**'+item+'**'+country+'**'+color;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_rmg_production_status_format2_controller.php?data='+data+'&action='+action, 'Remarks View', 'width=650px,height=450px,center=1,resize=0,scrolling=0','../');
}


function openmypage_cutting_sewing_total(po,item,cutting,color,type,action)
{
	var data=po+'**'+item+'**'+cutting+'**'+type+'**'+color;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_rmg_production_status_format2_controller.php?data='+data+'&action='+action, 'Remarks View', 'width=650px,height=450px,center=1,resize=0,scrolling=0','../');
}
 function openmypage_fab_issue(batch_id,action)
{

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_rmg_production_status_format2_controller.php?action='+action+'&batch_id='+batch_id, 'Remarks View', 'width=680px,height=250px,center=1,resize=0,scrolling=0','../');
}


function openmypage_job()
	{
		 
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();
		var job_year = $("#cbo_job_year").val();
		
		var page_link='requires/daily_rmg_production_status_format2_controller.php?action=job_popup&company='+company+'&buyer='+buyer+'&job_year='+job_year;
		var title="Search Job Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_id=this.contentDoc.getElementById("selected_id").value;  
			var job_no=this.contentDoc.getElementById("selected_name").value; 
 			$("#hidden_job_id").val(job_id);
			$("#txt_job_no").val(job_no);
			  
		}
	}
function getCompanyId() 
	{
	    var company_id = document.getElementById('cbo_company_name').value;
	    if(company_id !='') {
	      var data="action=load_drop_down_location&data="+company_id;
	      http.open("POST","requires/daily_rmg_production_status_format2_controller.php",true);
	      http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	      http.send(data); 
	      http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              	var response = trim(http.responseText);
	              	$('#location_td').html(response);
					set_multiselect('cbo_location','0','0','','0');
					setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_location,'0');getLocationId();") ,3000)];	
	          }			 
	      };
	    }   
	}	

function getButtonSetting()
	{
		 var company_id = document.getElementById('cbo_company_name').value;
		get_php_form_data(company_id,'print_button_variable_setting','requires/daily_rmg_production_status_format2_controller' );
	}
	
function print_report_button_setting(report_ids) 
    {
        //alert(report_ids);
        $('#show_button').hide();
        var report_id=report_ids.split(",");
        report_id.forEach(function(items){
            if(items==108){$('#show_button').show();}
            });
    }	 
	 
</script>

</head>
 
<body onLoad="set_hotkey();">

<form id="dailyRmgProductionReport">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../",'');  ?>
         
         <h3 style="width:960px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:960px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
               <thead>                    
	               	<th width="180" class="must_entry_caption">Working Company</th>
	               	<th width="120">Location</th>
	               	<th width="110">Floor</th>
	               	<th width="120">Buyer Name</th>
	               	<th width="60">Job Year</th>
	               	<th width="100">Job No</th>
 	               	<th width="90">Production Date</th>
 	               	<th colspan="2" width="90">
	               		<input type="reset" id="reset_btn" class="formbutton" style="width:90px; " value="Reset" onClick="reset_form('dailyRmgProductionReport','report_container*report_container2','','','')" />
	               	</th>
                 </thead>
                <tbody>
                <tr class="general">
                    <td id="company_td"> 
                        <?
                            echo create_drop_down( "cbo_company_name", 180, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                        ?>
                    </td>
                    <td id="location_td">
                    	<? 
                            echo create_drop_down( "cbo_location", 120, $blank_array,"", 1, "-- Select --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td id="floor_td" >
                    	<? 
                            echo create_drop_down( "cbo_floor", 110, $blank_array,"", 1, "-- Select --", $selected, "",1,"" );
                        ?>
                    </td>

                     
                    <td id="buyer_td">
                        <? 
                            echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                        ?>
                    </td>
                    
                    <td align="center">
					<?
                        $year_current=date("Y");
                        echo create_drop_down( "cbo_job_year", 60, $year,"", 1, "All",$year_current,'','');
                    ?>
                    </td>

                    <td align="center">
                    <input type="text" name="txt_job_no" id="txt_job_no" value="" style="width: 100px;text-align: center;" placeholder="  Browse" class="text_boxes"   ondblclick="openmypage_job();">
                    <input type="hidden" name="hidden_job_id" id="hidden_job_id">
					 
                    </td>
                     
                       
                    <td><input name="txt_production_date" value="<? echo date('d-m-Y');?>" id="txt_production_date" class="datepicker" style="width:90px">&nbsp; </td>
                     
                    <td>
                        <input type="button" id="show_button" class="formbutton" style="width:90px;display: none; " value="Show" onClick="fn_report_generated(0)"/> 
                    </td>
                    <td>
                        <!--<input type="button" id="show_button" class="formbutton" style="width:90px;  " value="Cutting Status" onClick="fn_report_generated(1)"/> -->
                    </td>
                </tr>
                </tbody>
            </table>
           
            <br />
        </fieldset>
    </div>
    </div>
        
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_multiselect('cbo_company_name','0','0','0','0');
	setTimeout[($("#company_td a").attr("onclick","disappear_list(cbo_company_name,'0');getCompanyId();getButtonSetting();") ,3000)];
  	$("#multiselect_dropdown_table_headercbo_company_name a").click(function(){
		load_buyer_location();
 	});
	function load_buyer_location()
	{
		var company=$("#cbo_company_name").val();
 		load_drop_down( 'requires/daily_rmg_production_status_format2_controller',company, 'load_drop_down_buyer', 'buyer_td' );
		load_drop_down( 'requires/daily_rmg_production_status_format2_controller', company, 'load_drop_down_location', 'location_td' )
	}
</script>

<script>
$('#cbo_location').val(0);
</script>
<!--<script class="include" type="text/javascript" src="../../js/chart/logic_chart.js"></script>-->
</html>
