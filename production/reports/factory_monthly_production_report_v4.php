<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Multiple Factory Monthly Production Report
				
Functionality	:	
JS Functions	:
Created by		:	Kamrul Hasan
Creation date 	: 	10-02-2018
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
echo load_html_head_contents("Multiple Factory Monthly Production Report","../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
 
function open_job_no()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
    {
        return;
    }
		var company = $("#cbo_company_id").val();
		var buyer=$("#cbo_buyer_name").val();
		var cbo_year=$("#cbo_year").val();
	    var page_link='requires/factory_monthly_production_report_v4_controller.php?action=job_popup&buyer='+buyer+'&cbo_year='+cbo_year;
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var job_data=this.contentDoc.getElementById("selected_id").value;
			//alert(job_data); // Jov ID
			var job_data=job_data.split("_");
			var job_hidden_id=job_data[0];
			var job_no=job_data[1];
			//var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#txt_job_no").val(job_no);
			$("#hidden_job_id").val(job_hidden_id); 
			//alert($("#hidden_job_id").val())
		}
	}
function openmypage_intref()
{    
    if(form_validation('cbo_company_id','Company Name')==false)
    {
        return;
    }
    var company = $("#cbo_company_id").val();
    var page_link='requires/factory_monthly_production_report_v4_controller.php?action=intref_search_popup&company='+company; 
    var title="Search Int Ref Popup";
    
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=0,scrolling=0','../')
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0];
        var int_ref_no=this.contentDoc.getElementById("hide_int_ref_no").value;
        var int_ref_id=this.contentDoc.getElementById("hide_int_ref_id").value;
                  
        $("#txt_int_ref").val(int_ref_no);
        $("#hide_int_ref_id").val(int_ref_id);  
     
    }
}
    function fn_generate_report(type)
	{
	
		
		if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*Date')==false )
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();

		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_floor_id*cbo_group_name*txt_job_no*hidden_job_id*txt_int_ref*cbo_buyer_name*txt_date_from*txt_date_to',"../../")+'&type='+type+'&report_title='+report_title;

		
		freeze_window(3);
		http.open("POST","requires/factory_monthly_production_report_v4_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			//alert (http.responseText);
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[2]+'" style="text-decoration:none"><input type="button" value="Excel Preview (Summary)" name="excel" id="excel" class="formbutton" style="width:165px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview (Summary)" name="Print" class="formbutton" style="width:165px"/>&nbsp;&nbsp;<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(2)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			setFilterGrid("table_body",-1);
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
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="auto"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
	}
	
	function openmypage2(date,company_id,po_id,location,action,source)
	{
		var popupWidth = "width=800px,height=350px,";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/factory_monthly_production_report_v4_controller.php?date='+date+'&company_id='+company_id+'&po_id='+po_id+'&location_id='+location+'&action='+action+'&sewing_source='+source, 'Production Quantity Details', popupWidth+'center=1,resize=0,scrolling=0','../');
			
	}
function fn_disable_com(str)
{
		if(str==2){$("#cbo_company_id").attr('disabled','disabled');}
		else{ $('#cbo_company_id').removeAttr("disabled");}
		if(str==1){$("#cbo_working_company_id").attr('disabled','disabled');}
		else{ $('#cbo_working_company_id').removeAttr("disabled");}
}

	
	
	function getWorkingCompanyId() 
	{
	    var working_company_id = document.getElementById('cbo_company_id').value;
	    //var search_type = document.getElementById('cbo_search_by').value;
		//alert(working_company_id);
	    if(working_company_id !='') {
		  var data="action=load_drop_down_location&data="+working_company_id;
		  //alert(data);die;
		  http.open("POST","requires/factory_monthly_production_report_v4_controller.php",true);
		  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		  http.send(data); 
		  http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText);
	              $('#location_td').html(response);
	              set_multiselect('cbo_location_id','0','0','','0');
	              setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_location_id,'0');getLocationId();") ,3000)]; 
	             
	          }			 
	      };
	    }         
	}

	function getLocationId() 
	{
	    var working_company_id = document.getElementById('cbo_company_id').value;
	    var location_id = document.getElementById('cbo_location_id').value;
	    if(working_company_id !='') {
		  var data="action=load_drop_down_floor&data="+working_company_id+'_'+location_id;
		  //alert(data);die;
		  http.open("POST","requires/factory_monthly_production_report_v4_controller.php",true);
		  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		  http.send(data); 
		  http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText);
	              $('#floor_td').html(response);
	              set_multiselect('cbo_floor_id','0','0','','0'); 
				  setTimeout[($("#floor_td a").attr("onclick","disappear_list(cbo_floor_id,'0');getFloorId();") ,3000)]; 
	          }			 
	      };
	    }         
	}
	function getFloorId() 
	{
	    var working_company_id = document.getElementById('cbo_company_id').value;
	    var location_id = document.getElementById('cbo_location_id').value;
	    var floor_id = document.getElementById('cbo_floor_id').value;
	    if(working_company_id !='') {
		  var data="action=load_drop_down_group&data="+working_company_id+'_'+location_id+'_'+floor_id;
		  //alert(data);die;
		  http.open("POST","requires/factory_monthly_production_report_v4_controller.php",true);
		  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		  http.send(data); 
		  http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText);
	              $('#group_td').html(response);
	             
	          }			 
	      };
	    }         
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission); ?>   		 
        <form name="factorymonthlyproduction_1" id="factorymonthlyproduction_1" autocomplete="off" > 
        <h3 style="width:1020px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel','')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:1020px" align="center" >      
            <fieldset>  
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th class="must_entry_caption"width="150">Company Name</th>
                    <th width="100">Location</th>
					<th class="" width="100" >Floor</th>
                    <th width="100" >Floor Group</th>
					<th width="80">Job No</th>
                    <th width="80">Int. Ref.</th>
                	<th width="100" >Buyer</th>
                    <th class="must_entry_caption">Production Date</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" onClick="reset_form('factorymonthlyproduction_1','report_container*report_container2','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td id="cbo_company_id_td"> 
							<?
								echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 0, "-- Select Company --", $selected, "load_drop_down( 'requires/factory_monthly_production_report_v4_controller', this.value, 'load_drop_down_location', 'location_td' )" );
                            ?>
                        </td>
                        
						<td id="location_td">
                            <? 
                                echo create_drop_down( "cbo_location_id", 100, $blank_array,"", 1, "-- Select Location --", "", "" );
                            ?>                            
                        </td>                        
                        <td id="floor_td">
                            <? 
                                echo create_drop_down( "cbo_floor_id", 100, $blank_array,"", 1, "-- Select Floor --", "", "" );
                            ?>                            
                        </td>    
						<td align="center" id="group_td">
							<? 
							echo create_drop_down( "cbo_group_name", 100, $blank_array,"", 1, "-- Select Group --", "", ""  );
							?>
						</td>
						<td>
						<input type="text" id="txt_job_no"  name="txt_job_no"  style="width:80px" class="text_boxes" onDblClick="open_job_no()" placeholder="Browse" readonly />
						<input type="hidden" id="hidden_job_id"  name="hidden_job_id" />
						</td>
                        
						
						<td align="center">
                         <input name="txt_int_ref" id="txt_int_ref" class="text_boxes" style="width:80px" placeholder="Browse" onDblClick="openmypage_intref()" readonly>
                       </td>
						<td align="center">
                        <? 
                        // echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "Select Buyer", 0, "",0 );
                        echo create_drop_down( "cbo_buyer_name", 100, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by  buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
                        ?>
                    </td>
						<td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date" readonly > To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date" readonly >
                        </td>
                        <td align="center">
                            <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_generate_report(1)" />
                        </td>
                    </tr>
                </tbody>
                <tr>
                    <td colspan="9">
                        <? echo load_month_buttons(1); ?>
                    </td>
                </tr>
            </table> 
        </fieldset>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2" align="left"></div>
    </form> 
    </div>
</body>
<script>
	set_multiselect('cbo_company_id','0','0','0','0');	
	set_multiselect('cbo_location_id','0','0','','0');
	set_multiselect('cbo_floor_id','0','0','','0');
	

	setTimeout[($("#cbo_company_id_td a").attr("onclick","getWorkingCompanyId();disappear_list(cbo_company_id,'0');") ,3000)]; 
	setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_location_id,'0');getLocationId();") ,3000)]; 
	setTimeout[($("#floor_td a").attr("onclick","disappear_list(cbo_floor_id,'0');getFloorId();") ,3000)]; 

	// $('#cbo_location').val(0);
</script>
<!-- <script>$('#cbo_location').val(0); </script> -->
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
