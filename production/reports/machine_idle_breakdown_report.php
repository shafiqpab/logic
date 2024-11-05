<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Machine Idle Breakdown
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	23-04-2015
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
echo load_html_head_contents("Machine Idle Breakdown", "../../", 1, 1,$unicode,1,1);
?>	
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';	

	var tableFilters = 
	{
		col_30: "none",
		col_operation: {
		id: ["tot_qnty"],
		col: [6],
		operation: ["sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 
	
	function openmypage_machine()
	{
		if(form_validation('cbo_company_id*cbo_machine_type','Company Name*Machine Type')==false)
		{
			return;
		}
		 var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_location_id').value+"_"+document.getElementById('cbo_machine_type').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/machine_idle_breakdown_report_controller.php?action=machine_no_popup&data='+data,'Machine Name Popup', 'width=470px,height=420px,center=1,resize=0','../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("hid_machine_id");
			var theemailv=this.contentDoc.getElementById("hid_machine_name");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_machine_id").value=theemail.value;
			    document.getElementById("txt_machine_name").value=theemailv.value;
				release_freezing();
			}
		}
	}
	
	function fn_report_generated(RptType)
	{
		if(RptType==2)
		{
			if( form_validation('cbo_company_id*cbo_machine_type*cbo_search_by*txt_date_from*txt_date_to','Company Name*Machine Type*Based On*Date From*Date To')==false )
			{
				return;
		    }
		}else
		{
			if( form_validation('cbo_company_id*cbo_machine_type*txt_date_from*txt_date_to','Company Name*Machine Type*Date From*Date To')==false )
			{
				return;
		    }

		}	
			
		var from_date = $('#txt_date_from').val();
		var to_date = $('#txt_date_to').val();
		var datediff = date_diff( 'd', from_date, to_date )+1;
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_machine_type*cbo_search_by*cbo_floor_id*txt_date_from*txt_date_to*txt_machine_name*txt_machine_id',"../../")+'&report_title='+report_title+'&datediff='+datediff+'&RptType='+RptType;
		freeze_window(3);
		http.open("POST","requires/machine_idle_breakdown_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;  
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			var tot_rows=reponse[2];
			//alert (reponse[2]);return;
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			//setFilterGrid("tbl_issue_status",-1,tableFilters);
	 		show_msg('3');
			release_freezing();
		}
	}
	
	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
	}	
	
	function openmypage_idle(machine_id,date,action)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/machine_idle_breakdown_report_controller.php?machine_id='+machine_id+'&date='+date+'&action='+action, 'Cause of Machine Idle', 'width=600px,height=450px,center=1,resize=0,scrolling=0','../');
	}

	function dynamic_ttl_change(type)
 {
	//alert(type);
	if(type==1)
	{
		$("#search_by_text").text(' Reporting Date ');

	}else if(type==2)
	{
		$("#search_by_text").text(' From Date ');
	}

 }
</script>
</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>    		 
        <form name="machineidlebreakdown_1" id="machineidlebreakdown_1" autocomplete="off" > 
         <h3 style="width:850px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:850px" >      
            <fieldset>  
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>                    
                    <th width="140" class="must_entry_caption">Company Name</th>
                    <th width="120">Location</th>
                    <th width="120">Floor</th>
					<th width="90" class="must_entry_caption">Machine Type</th>
					<th width="90">Based On</th>
                    <th colspan="2" class="must_entry_caption" id="search_by_text">Date</th>
                    <th width="100" >Machine Name</th>
                    <th width="70" colspan="2"><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('machineidlebreakdown_1','report_container*report_container2','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td> 
							<?
								echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down('requires/machine_idle_breakdown_report_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/machine_idle_breakdown_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );" );
                            ?>
                        </td>
                        <td id="location_td">
							<? 
								echo create_drop_down( "cbo_location_id", 120, $blank_array,"", 1, "-- Select --", $selected, "",1,"" );
                            ?>
                        </td>
                        <td id="floor_td">
                            <? echo create_drop_down( "cbo_floor_id", 120, $blank_array,"", 1, "-- Select Floor --", 0, "",1 ); ?>
                        </td>
                        <td>
							<? 
								echo create_drop_down( "cbo_machine_type", 90, $machine_category,"", 1, "-- Select --", $selected, "",0,"1,2,4" );
                            ?>
                        </td>
						<td>
						 
						   <?
						
						    $search_by_arr=array(1=>"Reporting Date",2=>"From Date");
						   ?>
                            <? echo create_drop_down( "cbo_search_by", 100, $search_by_arr,'',1, "-- Select--", 0,"dynamic_ttl_change(this.value);" );
                            ?>
                            
                        </td>
                        <td align="center">
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:65px" value="<? echo date("d-m-Y", time() - 604800);?>" placeholder="From Date" >
                        </td>
                        <td align="center">
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:65px" value="<? echo date("d-m-Y", time() - 86400);?>" placeholder="To Date"  >
                        </td>
                        <td align="center">
                            <input type="text" name="txt_machine_name" id="txt_machine_name" class="text_boxes" style="width:95px" placeholder="Browse Machine" onDblClick="openmypage_machine()" readonly />
                            <input type="hidden" name="txt_machine_id" id="txt_machine_id" class="text_boxes" style="width:80px"  />
                        </td>
                        <td align="center">
                            <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1)" />
                        </td>
                        <td align="center">
                            <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show 2" onClick="fn_report_generated(2)" />
                        </td>
                    </tr>
                </tbody>
                <tr>
                    <td colspan="8" align="center">
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
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script> $('#cbo_location_id').val(0); </script>
</html>
