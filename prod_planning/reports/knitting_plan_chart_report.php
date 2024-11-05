<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Fabric Receive Status Report.
Functionality	:	
JS Functions	:
Created by		:	Fuad 
Creation date 	: 	23-02-2013
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
echo load_html_head_contents("Knitting Plan Chart Report", "../../", 1, 1,'',1,1);

?>	

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';
 
function fn_report_generated(type)
{
	if (form_validation('cbo_company_name*txt_date_from*txt_date_to','Comapny Name*Date From*Date To')==false)
	{
		return;
	}
	var report_title=$( "div.form_caption" ).html();
	var data="action=report_generate&type="+ type+ get_submitted_data_string('cbo_company_name*cbo_floor_id*txt_date_from*txt_date_to*txt_machine_id',"../../") +'&report_title='+ report_title;
	freeze_window(3);
	http.open("POST","requires/knitting_plan_chart_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fn_report_generated_reponse;
}
	

function fn_report_generated_reponse()
{
 	if(http.readyState == 4) 
	{
  		var response=trim(http.responseText).split("####");
		$('#report_container2').html(response[0]);
		document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:155px"/></a>'; 
		
		var tot_date=response[2];
		
		var tdid=new Array();
		var td_col=new Array();
		var td_op=new Array();
		var td_method=new Array();
		var col_id_r=7;
		
		tdid.push('value_capacity');
		td_col.push(col_id_r); 
		td_op.push("sum");
		td_method.push("innerHTML");
		
		for(var i=1; i<=tot_date; i++)
		{
			col_id_r=col_id_r+1; td_col.push(col_id_r);

			tdid.push('value_qnty_'+i);
			td_op.push("sum");
			td_method.push("innerHTML");
		}

		var tableFilters = { 
			//col_0: "none" 
			col_operation: {
							   id: tdid,
							   col: td_col,
							   operation: td_op,
							   write_method: td_method
							}
		}
		//setFilterGrid("tbl_list_search",-1,tableFilters);
		setFilterGrid("summary_tbl",-1);
		
		//append_report_checkbox('table_header_1',1);
		// $("input:checkbox").hide();
		show_msg('3');
		release_freezing();
 	}
}

function openmypage(program_id,type)
{
	if(type==1)
	{
		var width='790px';
	}
	else{
		var width='990px';
	}


	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/knitting_plan_chart_report_controller.php?program_id='+ program_id + '&type=' + type + '&action=plan_deails', 'Detail Veiw', 'width='+width+', height=410px,center=1,resize=0,scrolling=0','../');
}

function openmypage_machine()
{
	if(form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_floor_id').value;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/knitting_plan_chart_report_controller.php?action=machine_no_popup&data='+data,'Machine Name Popup', 'width=470px,height=420px,center=1,resize=0','../')
	
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
</script>


</head>
 
<body onLoad="set_hotkey();">

<form id="knittingPlanChartReport_1">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../",'');  ?>
         
         <h3 style="width:700px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:700px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="120" class="must_entry_caption">Company Name</th>
                    <th width="120">Production Floor</th>
                    <th width="160" class="must_entry_caption">Date</th>
					<th width="100" >Machine Name</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('knittingPlanChartReport_1','report_container*report_container2','','','')" class="formbutton" style="width:100px" /></th>
                </thead>
                <tbody>
                    <tr align="center">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 110, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/knitting_plan_chart_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );" );
                            ?>
                        </td>
                        <td id="floor_td">
							<? echo create_drop_down( "cbo_floor_id", 120, $blank_array,"", 1, "-- Select Floor --", 0, "",0 ); ?>
                        </td>
                        <td align="center">
                             <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y"); ?>" class="datepicker" style="width:55px" readonly/>
                             To
                             <input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date('d-m-Y', strtotime("+14 days", strtotime(date("d-m-Y")))); ?>" class="datepicker" style="width:55px" readonly/>
                        </td>
						<td align="center">
                            <input type="text" name="txt_machine_name" id="txt_machine_name" class="text_boxes" style="width:100px" placeholder="Browse Machine" onDblClick="openmypage_machine()" readonly />
                            <input type="hidden" name="txt_machine_id" id="txt_machine_id" class="text_boxes" style="width:80px"  />
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:60px" value="Show" onClick="fn_report_generated(1)" />
                            <input type="button" id="show_button" class="formbutton" style="width:60px" value="Sales" onClick="fn_report_generated(2)" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    	</div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
