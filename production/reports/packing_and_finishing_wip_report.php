<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Packing and Finishing WIP Report.
Functionality	:	
JS Functions	:
Created by		:	Kamrul Hasan
Creation date 	: 	12-11-2022
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
echo load_html_head_contents("Date Wise Production Report", "../../", 1, 1,$unicode,'','');
$yr = date("Y");
?>	

<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
 	function open_int_ref()
 	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer=$("#cbo_buyer_name").val();
		var job_no=$("#txt_int_ref_no").val();
		var job_id=$("#hidden_int_ref_id").val();
		
		var page_link='requires/packing_and_finishing_wip_report_controller.php?action=int_ref_wise_search&company='+company+'&buyer='+buyer; 
		var title="Search Int.Ref Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var int_ref_data=this.contentDoc.getElementById("selected_id").value;
			var int_ref_data=int_ref_data.split("_");
			var int_ref_hidden_id=int_ref_data[0];
			var int_ref_no=int_ref_data[1];
				
			$("#txt_int_ref_no").val(int_ref_no);
			$("#hidden_int_ref_id").val(int_ref_hidden_id); 
		}
 	}	
 	 
	function open_job_no()
	{
		if( form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer=$("#cbo_buyer_name").val();
		var cbo_year=$("#cbo_year").val();
	    var page_link='requires/packing_and_finishing_wip_report_controller.php?action=job_wise_search&company='+company+'&buyer='+buyer+'&cbo_year='+cbo_year;
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var job_data=this.contentDoc.getElementById("selected_id").value;
			//alert(job_data); // 
			var job_data=job_data.split("_");
			var job_hidden_id=job_data[0];
			var job_no=job_data[1];
			
			$("#txt_job_no").val(job_no);
			$("#hidden_job_id").val(job_hidden_id); 
			//alert($("#hidden_job_id").val())
		}
	}
	
	 
	function generate_report(type)
	{
	
		
		
			if($("#txt_job_no").val()=="" && $("#txt_int_ref_no").val()=="")
			{
				
				if( form_validation('cbo_company_name*txt_production_date','Company Name*Production Date')==false )
				{
					return;
				}
			
		}
		
	
		var report_title=$( "div.form_caption" ).html();
		var data="action=generate_report"+get_submitted_data_string('cbo_company_name*txt_int_ref_no*cbo_buyer_name*cbo_floor_name*txt_job_no*txt_production_date',"../../")+'&report_title='+report_title+'&type='+type;
		freeze_window(3);
		http.open("POST","requires/packing_and_finishing_wip_report_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window()
	{
		// document.getElementById('scroll_body').style.overflow="auto";
		// document.getElementById('scroll_body').style.maxHeight="none"; 
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		// document.getElementById('scroll_body').style.overflowY="auto"; 
		// document.getElementById('scroll_body').style.maxHeight="400px";
	}
	
	function reset_form()
	{
		$("#hidden_int_ref_id").val("");
	
		$("#hidden_job_id").val("");
		
	}


	
	
		 	 
</script>

</head>
 
<body onLoad="set_hotkey();">
  	<div style="width:100%;" align="center"> 
   	<? echo load_freeze_divs ("../../",'');  ?>
    <h3 style="width:770px;  margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
       <div style="width:100%;" align="center" id="content_search_panel">
    <form id="dateWiseProductionReport_1">    
      <fieldset style="width:770px;">
            <table class="rpt_table" width="750" cellpadding="0" cellspacing="0" align="center" border="1" rules="all">
               	<thead>                    
                    <tr>
                        <th class="must_entry_caption" width="130">Company Name</th>
						<th width="120">Int. Ref No </th>
						<th width="120" >Buyer Name</th>
                        <th width="120" >Floor</th>
                       <th width="100">Job No</th>
                        <th width="90" class="must_entry_caption">Production Date </th>
                      
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:60px" value="Reset" onClick="reset_form()"/></th>
                    </tr>   
              	</thead>
                <tbody>
                <tr class="general">
                    <td> 
                        <?
                            echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, ";load_drop_down( 'requires/packing_and_finishing_wip_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/packing_and_finishing_wip_report_controller',this.value, 'load_drop_down_floor', 'floor_td' );" );
                        ?>
                    </td>
					 <td>
                       <input type="text" id="txt_int_ref_no"  name="txt_int_ref_no"  style="width:90px" class="text_boxes" onDblClick="open_int_ref()" placeholder="Browse/Write" />
                       	<input type="hidden" id="hidden_int_ref_id"  name="hidden_int_ref_id" />
                    
                       
                    </td> 
					<td id="buyer_td">
                        <? 
                        echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                        ?>
                    </td>
                  
                    <td id="floor_td">
                        <? 
                        echo create_drop_down( "cbo_floor_name", 120, $blank_array,"", 1, "-- Select Floor --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td>
                       <input type="text" id="txt_job_no"  name="txt_job_no"  style="width:90px" class="text_boxes" onDblClick="open_job_no()" placeholder="Browse/Write" />
                    </td>
                   
                  
                   <td>
                    <input name="txt_production_date" id="txt_production_date" class="datepicker" style="width:70px"  value="" >
                    </td>
                     
                    <td>
					    
                       
                        <input type="button" id="show" class="formbutton" style="width:60px" value="Show" onClick="generate_report(2)" />
                      
                    </td>
                    
                </tr>
                </tbody>
            </table>
           
      </fieldset>
    
 </form> 
 </div>  
    <div id="report_container" style="padding: 10px;"></div>
    <div id="report_container2"></div>  
 </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
