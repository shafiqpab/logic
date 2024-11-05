<?
/*-------------------------------------------- Comments -----------------------
Purpose         :   Responsible wise TNA failed status Report 
Functionality	:	
JS Functions	:
Created by		:	Al-Hasan
Creation date 	: 	02-JAN-2024
Updated by 		: 		
Update date		: 	
QC Performed BY	:	 
QC Date			:	
Comments		:
*/ 
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
$_SESSION['page_permission'] = $permission;
echo load_html_head_contents("Line Wise Planning Report","../../", 1, 1, $unicode,1,1);
?>	
<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	 

    // task popup.
    function openmypage_task()
	{ 
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
        var company = $("#cbo_company_id").val();	
        var tna_task = $("#txt_taks_name").val();
        var tna_task_id = $("#tna_task_id").val();
        var tna_task_id_no = $("#tna_task_id_no").val();
        var page_link='requires/responsible_wise_tna_failed_status_controller.php?action=task_popup&company='+company+'&tna_task='+tna_task+'&tna_task_id='+tna_task_id+'&tna_task_id_no='+tna_task_id_no;   
        var title="Search Task Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
            var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
            var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
            var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
            $("#txt_taks_name").val(style_des);
            $("#tna_task_id").val(style_id); 
            $("#tna_task_id_no").val(style_des_no);
            if(style_des!="")
            {
                $('#from_date_html').html('');
                $('#from_date_html').html('TNA From Date');
                $('#to_date_html').html('');
                $('#to_date_html').html('TNA To Date');
            }
            else
            {
                $('#from_date_html').html('');
                $('#from_date_html').html('Ship From Date');
                $('#to_date_html').html('');
                $('#to_date_html').html('Ship To Date');
            }
		}
	}

    function buyer_multipule(){
        set_multiselect('cbo_buyer_id','0','0','','0');
    }

    // team leader.
    function openmypage_team_leader()
	{ 
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
        var txt_team_leader = $("#txt_team_leader").val();
        var txt_team_leader_id = $("#txt_team_leader_id").val();
        var txt_team_leader_no = $("#txt_team_leader_no").val();
        
        var page_link='requires/responsible_wise_tna_failed_status_controller.php?action=team_leader_popup&txt_team_leader='+txt_team_leader+'&txt_team_leader_id='+txt_team_leader_id+'&txt_team_leader_no='+txt_team_leader_no;   
        var title="Search Task Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=310px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
            var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
            var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
            var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
            $("#txt_team_leader").val(style_des);
            $("#txt_team_leader_id").val(style_id);
            $("#txt_team_leader_no").val(style_des_no);
		}
	}

    function fn_report_generate(type)
	{ 
        if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		else
		{	 
			var data="action=report_generate&type="+type+get_submitted_data_string('cbo_company_id*cbo_buyer_id*tna_task_id*txt_team_leader_id*cbo_order_status*cbo_shipment_status*cbo_date_category*txt_date_from*txt_date_to',"../../../");
			freeze_window(3);
			http.open("POST","requires/responsible_wise_tna_failed_status_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generate_reponse;
		}
	}
    function fn_report_generate_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split("****");
			$('#report_container2').html(response[0]);
			release_freezing(); 
			if(response[1] == 1 || response[1] == 2 || response[1] == 3 || response[1] == 4 || response[1] == 5 || response[1] == 6 || response[1] == 7)
			{
				document.getElementById('report_container').innerHTML='<a href="requires/'+response[2]+'" style="text-decoration:none"><input type="button" value="Excel Preview-ui" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview-8" name="Print" class="formbutton" style="width:100px"/>';
				release_freezing();
			}
			else
			{
				document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				release_freezing();
			}
			if(response[1]==1)
			{
				setFilterGrid("table_body",-1,tableFilters);
			}
			if(response[1]==2 || response[1]==3)
			{ 
				setFilterGrid("table_body",-1);
			}
			if(response[1]==6)
			{
				setFilterGrid("table_body",-1,tableFilters_3);
			}
			
			show_msg('3');
			release_freezing();
		}
	}
</script>
</head>
<body onLoad="set_hotkey();">
    <form id="cost_breakdown_rpt">
        <div style="width:100%;" align="center">
            <? echo load_freeze_divs ("../"); ?>
            <h3 align="left" id="accordion_h1" style="width:1030px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
                <div id="content_search_panel"> 
                <fieldset style="width:1030px;">
                    <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <tr>                   
                                <th class="must_entry_caption">Company Name</th>
                                <th class="">Buyer</th>
                                <th class="">Task</th>
                                <th id="td_search_by">Team Leader</th>
                                <th>Order Status</th>
                                <th>Shipment Status</th> 
                                <th>Date Category</th>
                                <th>From Date</th>
                                <th>To Date</th>
                                <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr class="general" id="company_td">
                            <td> 
                                <?= create_drop_down("cbo_company_id", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/responsible_wise_tna_failed_status_controller', this.value, 'load_drop_down_buyer', 'buyer_id' );buyer_multipule()");
                                ?>
                            </td>
                            <td id="buyer_id">
                                <?= create_drop_down("cbo_buyer_id", 120, "select id, buyer_name from lib_buyer where status_active=1 order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
                                ?>
                            </td>
                            <td align="center">
                                <input style="width:100px;"  name="txt_taks_name" id="txt_taks_name"  ondblclick="openmypage_task()"  class="text_boxes" placeholder="Browse" readonly/>   
                                <input type="hidden" name="tna_task_id" id="tna_task_id"/>
                            </td> 
                            <td>
                                <input type="text" id="txt_team_leader" name="txt_team_leader" onDblClick="openmypage_team_leader();" placeholder="Browse" class="text_boxes" style="width:80px"/>
                                <input type="hidden" id="txt_team_leader_id" name="txt_team_leader_id"/>
                                <input type="hidden" id="txt_team_leader_no" name="txt_team_leader_no"/>
                            </td>
                            <td>
                                <?
                                $orderStatus_arr=array(1=>'Confirmed', 2=>'Projected');
                                echo create_drop_down("cbo_order_status", 100, $orderStatus_arr,"", 0, "-- All --", $selected, "",0,"");
                                ?>
                            </td>
                            <td>
                                <?
                                $shipStatus_arr=array(1=>'ALL (Pending+Partial)', 4=>'Full Shipment/Closed');
                                echo create_drop_down("cbo_shipment_status", 100, $shipStatus_arr,"", 0, "-- All --", $selected, "", 0, "");
                                ?>
                            </td>
                            <td>
                                <?
                                $dateCategory_arr=array(1=>'Plan Start Date', 2=>'Plan Finish Date');
                                echo create_drop_down("cbo_date_category", 100, $dateCategory_arr,"", 0, "-- All --", $selected, "", 0, "");
                                ?>
                            </td>
                            <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" ></td>
                            
                            <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" ></td>
                            <td>
                                <input type="button" name="search" id="search" value="Show" onClick="fn_report_generate(1)" style="width:60px" class="formbutton"/> 
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <table>
                        <tr>
                            <td>
                                <?= load_month_buttons(1);?>
                            </td>
                        </tr>
                    </table> 
                </fieldset>
            </div>
        </div>
        <br>
        <div id="report_container" align="center"></div>
        <div id="report_container2"></div>
    </form>  
</body>
<script>
	set_multiselect('cbo_buyer_id','0','0','','0');
	setTimeout[($("#company_td a").attr("onclick","disappear_list(cbo_company_id,'0');getCompanyID();") ,3000)]; 
	setTimeout[($("#buyer_id a").attr("onclick","disappear_list(cbo_buyer_id,'0');getBuyerID();") ,3000)]; 
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>