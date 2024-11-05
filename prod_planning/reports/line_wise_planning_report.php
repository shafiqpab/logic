<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Page Will Create Line Wise Planning Report
Functionality	:	
JS Functions	:
Created by		:	Rehan Uddin 
Creation date 	: 	11-FEB-2019
Updated by 		: 		
Update date		: 	27-06-2021	   
QC Performed BY	:	Version 2 is developed by REZA. If any query plz contact me. cell: 01511100004	
QC Date			:	
Comments		:
*/

session_start();

extract($_REQUEST);



if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');

$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Line Wise Planning Report","../../", 1, 1, $unicode,1,1);
?>	
<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	
	var tableFilters = 
	 {
		col_33: "none",
		col_operation: {
		id: ["total_order_qnty","total_order_qnty_in_pcs","value_tot_cm_cost","value_tot_cost","value_order","value_margin","value_tot_trims_cost","value_tot_embell_cost"],
	    col: [9,11,25,26,29,30,31,32],
	    operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
	    write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 }
	 
	function openmypage_style()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_year_id = $("#cbo_year").val();
		//var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/line_wise_planning_report_controller.php?action=style_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='Style No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_no=this.contentDoc.getElementById("hide_style_no").value;
			var style_id=this.contentDoc.getElementById("hide_style_id").value;
			$('#txt_style_no').val(style_no);
			$('#txt_style_id').val(style_id);	 
		}
	}
	
	 
	
	function fn_report_generated(type)
	{
		if(form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false)
		{
			return;
		}
		else
		{	
			var data="action=report_generate&type="+type+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_floor_id*cbo_buyer_name*txt_date_from*txt_date_to*txt_style_no*txt_style_id',"../../");
			freeze_window(3);
			http.open("POST","requires/line_wise_planning_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			 
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]); 
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
			
			setFilterGrid("table_body",-1,tableFilters);
			setFilterGrid("tbl_header",-1);
			
	 		show_msg('3');
			release_freezing();
		}
	}
	
	
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		 
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_print.css" type="text/css"  /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		 
	}
	
	function getCompanyId() 
	{
	    var company_id = document.getElementById('cbo_company_id').value;
	    //var search_type = document.getElementById('cbo_search_by').value;
	    if(company_id !='') {
		  var data="action=load_drop_down_location&data="+company_id;
		  //alert(data);die;
		  http.open("POST","requires/line_wise_planning_report_controller.php",true); 
		  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		  http.send(data); 
		  http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText);
	              $('#location_td').html(response);
	              set_multiselect('cbo_location_id','0','0','','0');
	              setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_location_id,'0');getLocationId();") ,3000)]; 
	              //========================
	              load_drop_down( 'requires/line_wise_planning_report_controller', company_id, 'load_drop_down_buyer', 'buyer_td' );
	          }			 
	      };
	    }         
	}

	function getLocationId() 
	{
	    var company_id = document.getElementById('cbo_company_id').value;
	    var location_id = document.getElementById('cbo_location_id').value;
	    if(company_id !='') {
		  var data="action=load_drop_down_floor&data="+company_id+'_'+location_id;
		  //alert(data);die;
		  http.open("POST","requires/line_wise_planning_report_controller.php",true);
		  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		  http.send(data); 
		  http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText);
	              $('#floor_td').html(response);
	              set_multiselect('cbo_floor_id','0','0','','0');
	          }			 
	      };
	    }         
	}
</script>
</head>
 
<body onLoad="set_hotkey();">
		 
<form id="cost_breakdown_rpt">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:1120px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:1120px;">
                <table class="rpt_table" width="1110" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                	<thead>
                    	<tr>                   
                            <th class="must_entry_caption">Company Name</th>
                            <th class="">Location</th>
                            <th class="">Floor</th>
                             <th>Buyer Name</th>
                             <th>Style</th>
                             <th class="must_entry_caption" id="td_date_caption">Plan Date</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general" id="company_td">
                        <td> 
                           <?
								echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 0, "-- Select Company --", $selected, "load_drop_down( 'requires/line_wise_planning_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>

                        <td id="location_td"> 
                           <?
								echo create_drop_down( "cbo_location_id", 150, "select id,location_name from lib_location where status_active=1 ","id,location_name", 1, "-- Select Location --", $selected, "" );
                            ?>
                        </td>

                        <td id="floor_td"> 
                           <?
								echo create_drop_down( "cbo_floor_id", 150, "select id,floor_name from lib_prod_floor  where status_active=1 and production_process=5 ","id,floor_name", 1, "-- Select Floor --", $selected, "" );
                            ?>
                        </td>
                       
                        
                          <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                         <td>
                            <input type="text" id="txt_style_no" name="txt_style_no" class="text_boxes" style="width:80px" onDblClick="openmypage_style();" placeholder="Wr./Br. style" />
                             <input type="hidden" id="txt_style_id" name="txt_style_id"/>
                        </td>
                         
                        
                        <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" >&nbsp; To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" ></td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:100px" value="Summary" onClick="fn_report_generated(1)" />
                            <input type="button" id="show_button" class="formbutton" style="width:100px" value="Details" onClick="fn_report_generated(2)" />
                        </td>
                    </tr>
                    </tbody>
                </table>
                <table>
                    <tr>
                        <td>
                            <? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </table> 
            </fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
   
 </form>    
</body>
<script>
	/*set_multiselect('cbo_company_id','0','0','','0');
	set_multiselect('cbo_floor_id','0','0','','0');
	set_multiselect('cbo_location_id','0','0','','0');

	$("#multiselect_dropdown_table_headercbo_company_id a").click(function()
	{
		load_buyer_buyer();
 	});
	function load_buyer_buyer()
	{
		var company=$("#cbo_company_id").val();
 		load_drop_down( 'requires/line_wise_planning_report_controller',company, 'load_drop_down_buyer', 'buyer_td' );
 		load_drop_down( 'requires/line_wise_planning_report_controller',company, 'load_drop_down_location', 'location_td' );
		set_multiselect('cbo_location_id','0','0','','0');
	}
	//=====================================================================

	$("#multiselect_dropdown_table_headercbo_location_id a").click(function()
	{
		load_floor();
 	});
	function load_floor()
	{
		var company=$("#cbo_company_id").val();
		var location=$("#cbo_location_id").val();
 		load_drop_down( 'requires/line_wise_planning_report_controller',company, 'load_drop_down_floor', 'floor_td' );
		
	}*/
</script>
<script>
	set_multiselect('cbo_company_id','0','0','0','0');	
	set_multiselect('cbo_location_id','0','0','','0');
	set_multiselect('cbo_floor_id','0','0','','0');
	setTimeout[($("#company_td a").attr("onclick","disappear_list(cbo_company_id,'0');getCompanyId();") ,3000)]; 
	setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_location_id,'0');getLocationId();") ,3000)]; 
	// $('#cbo_location').val(0);
</script>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
