<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Returnable Item Painding Report
				
Functionality	:	
JS Functions	:
Created by		:	wayasel
Creation date 	: 	24-1-2022
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
echo load_html_head_contents("Returnable Item Receive Report","../../", 1, 1, $unicode,1,0); 
?>
<script>
    	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

    function openmypage_gate()
	{
		if( form_validation('cbo_item_cat*cbo_company_name','Item Category*Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var category= $("#cbo_item_cat").val();	
	
		var page_link='requires/returnable_item_painding_report_contorller.php?action=gatePass_search&company='+company+'&category='+category;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=500px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var sysNumber=(this.contentDoc.getElementById("hidden_sys_number").value).split('_');; // system number
		
			$("#txt_gate_id").val(sysNumber[0]);
			$("#txt_gate_pass").val(sysNumber[1]);			
		}
	}
			
	function  generate_report(type)
	{
		if( form_validation('txt_date_from*txt_date_to','Date From*Date To')==false )
		{
			return;
		} 
			
		var cbo_company_name= $("#cbo_company_name").val();
		var cbo_department_name= $("#cbo_department_name").val();
		var cbo_location_id = $("#cbo_location_id").val();
		var cbo_item_cat 	 = $("#cbo_item_cat").val();
		var txt_gate_pass 	= $("#txt_gate_pass").val();
		var txt_gate_id 	= $("#txt_gate_id").val();
		var txt_date_from 	= $("#txt_date_from").val(); 
		var txt_date_to 	= $("#txt_date_to").val();
				
		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_location_id="+cbo_location_id+"&cbo_item_cat="+cbo_item_cat+"&txt_gate_pass="+txt_gate_pass+"&txt_gate_id="+txt_gate_id+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&cbo_department_name="+cbo_department_name+"&type="+type;

		var data="action=generate_report"+dataString;
		freeze_window(3);
		http.open("POST","requires/returnable_item_painding_report_contorller.php",true);
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
		setFilterGrid("table_body_1",-1);
		show_msg('3');
		release_freezing();
		}
	} 

	function getLocationName() 
	{
	    var cbo_company_name = document.getElementById('cbo_company_name').value;
	    if(cbo_company_name !='') {
		  var data="action=load_drop_down_location&data="+cbo_company_name;
		  //alert(data);die;
		  http.open("POST","requires/returnable_item_painding_report_contorller.php",true);
		  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		  http.send(data); 
		  http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText);
	              $('#location_td').html(response);
	              set_multiselect('cbo_location_id','0','0','','0');
	            //   setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_location_id,'0');getLocationId();") ,3000)]; 
	          }			 
	      };
	    }         
	}
	function fnc_return_qnty(cbo_company,gate_pass_id,gate_in_id,item_description) 
	{
		page_link='requires/returnable_item_painding_report_contorller.php?action=return_qnty_data&cbo_company='+cbo_company+'&gate_pass_id='+gate_pass_id+'&gate_in_id='+gate_in_id+'&item_description='+item_description;
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Return Qty Popup', 'width=700px, height=350px, center=1, resize=0, scrolling=0','');
		emailwindow.onclose=function(){}
	}
    
</script>


<body onLoad="set_hotkey()">
 <form name="item_receive_issue_1" id="item_receive_issue_1" autocomplete="off" >
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
   <div style="width:970px;" align="center">
    <h3 align="center" id="accordion_h1" style="width:970px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    </div>
    <div style="width:970px;" align="center" id="content_search_panel">
        <fieldset style="width:970px;">
             <table class="rpt_table" width="970" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                <thead>
                    <tr>
                        <th width="130">Item Category</th>                                
                        <th width="150">Company</th>                                
                        <th width="130">Location</th>
                        <th width="130">Department </th>
						<th width="120">Gate Pass No</th>
                        <th width="170" colspan="2" class="must_entry_caption">Date Range</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_field()" /></th>
                    </tr>
                </thead>
                <tr class="general">
					<td>
						<?	echo create_drop_down( "cbo_item_cat", 120, $item_category,"", 1, "--- ALL Category ---", $selected, "","","",0 ); ?>
                    </td>
                    <td>
                        <?
                        echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "getLocationName();load_drop_down( 'requires/returnable_item_painding_report_contorller',this.value, 'load_drop_down_com_department', 'com_department_td' )" );
                        ?>                          
                    </td>
					<td id="location_td" >
						<? echo create_drop_down( "cbo_location_id", 120, $blank_array,"", 1, "-- All  --", 0, "",0 );?>
					</td> 
					<td id="com_department_td" >
						<? 
							echo create_drop_down( "cbo_department_name", 152, $blank_array,"", 1, "-- Select  --", 0, "",0 );								
						?>
					</td>                    
                    <td align="center">
						<input style="width:110px;"  name="txt_gate_pass" id="txt_gate_pass"  ondblclick="openmypage_gate()"  class="text_boxes" placeholder="Browse" readonly  />   
                        <input type="hidden" name="txt_gate_id" id="txt_gate_id"/>          
                    </td>
                    <td colspan="2">
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:65px;" readonly/> 
                    	To
                    	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:65px;" readonly/>
                    </td>
                    <td>
                      <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:100px" class="formbutton" />   
                    </td>
                </tr>
                <tr>
                	<td colspan="11" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                </tr>
            </table>  
        </fieldset> 
           
    </div>
    	<div id="report_container" align="center"></div>
        <div id="report_container2"></div> 
</div> 
 </form>    
</body> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
<script>
	set_multiselect('cbo_location_id','0','0','0','0');
</script>
</html>