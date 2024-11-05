<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Report Will Create Color Wise knitting production.
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	21-07-2016
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
echo load_html_head_contents("Color Wise Knitting Production Report", "../../", 1, 1,'','','');
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	var tableFilters = 
	 {
		col_47: "none",
		col_operation: {
			id: ["value_total_grey_fab_qnty","value_total_knit_qnty","value_total_knit_balance","value_total_grey_delivery","value_total_delivery_balance","value_total_grey_receive","value_total_receive_balance","value_total_grey_issue","value_total_in_hand"],
	//    col: [9,10,11,12,13,14,15,16,17],
	   col: [11,12,13,14,15,16,17,18,19],
	   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}	
	}
	function fn_report_generated(type)
	{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year*cbo_search_by*txt_search_string*txt_date_from*txt_date_to*cbo_floor_id',"../../")+'&report_title='+report_title;
		freeze_window(type);
		http.open("POST","requires/color_wise_knitting_production_report_controller.php",true);
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
			document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			setFilterGrid("tbl_list_search",-1,tableFilters);
			//append_report_checkbox('table_header_1',1);
			// $("input:checkbox").hide();
			show_msg('3');
			release_freezing();
		}
	}
	
	function fn_chang_caption()
	{
		$("#txt_search_string").val("");
		var search_id = $("#cbo_search_by").val();
		if(search_id==1) $("#search_by_caption").html("Job No");
		else if(search_id==2) $("#search_by_caption").html("Style No");
		else if(search_id==3) $("#search_by_caption").html("Order No");
		else if(search_id==4) $("#search_by_caption").html("File No");
		else if(search_id==5) $("#search_by_caption").html("Ref. No");
		else $("#search_by_caption").html("Job No");
		
		
	}
	
	function openmypage_knitting(job_no,body_part_id,construction,fabric_color_id,all_mst_id,action)
	{ //alert(des_prod)
		var companyID = $("#cbo_company_name").val();
		var popup_width='660px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/color_wise_knitting_production_report_controller.php?companyID='+companyID+'&job_no='+job_no+'&body_part_id='+body_part_id+'&construction='+construction+'&fabric_color_id='+fabric_color_id+'&all_mst_id='+all_mst_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../');
	}
	
	function openpage(action,data)
	{ 
		 emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/color_wise_knitting_production_report_controller.php?action='+action+'&data='+data, 'Details Info', 'width=660px,height=390px,center=1,resize=0,scrolling=0','../');
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
<form id="knittingStatusReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1100px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1100px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Buyer Name</th>
                    <th>Job Year</th>
                    <th>Search By</th>
                    <th id="search_by_caption">Job No</th>
                    <th>Knitting Production Floor</th>
                    <th>Ship Date</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('knittingStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px" /></th>
                </thead>
                <tbody>
                    <tr align="center" class="general">
                        <td> 
                        <?
                        	echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "- Select Company -", $selected, "load_drop_down( 'requires/color_wise_knitting_production_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/color_wise_knitting_production_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );" );
                        ?>
                        </td>
                        <td id="buyer_td">
                        <? 
                        	echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "- All Buyer -", $selected, "",0,"" );
                        ?>
                        </td>
                        <td>
                        <?
                        	echo create_drop_down( "cbo_year", 80, create_year_array(),"", 1,"-- All --", 0, "",0,"" );
                        ?>
                        </td>
                        <td>
                        <?
							$search_by_arr=array(1=>"Job Wise",2=>"Style Wise",3=>"Order Wise",4=>"File No Wise",5=>"Ref No Wise");
                        	echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"", 0,"", 1, "fn_chang_caption()",0,"" );
                        ?>
                        </td>
                        <td><input type="text" id="txt_search_string" name="txt_search_string" class="text_boxes" style="width:100px" placeholder="Write" /></td>
                        <td id="floor_td">
							<?
								$arr=array();
								echo create_drop_down("cbo_floor_id", 100, $arr, "", 1, "-- Select Floor --", 0, "", 1);
							?>
                        </td>
                        <td align="center">
                        <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:70px" placeholder="From Date" readonly />
                        To
                        <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:70px" placeholder="To Date" readonly />
                        </td>
                        <td>
                        <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(3)" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
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
