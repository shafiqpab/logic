<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This file for HnM Order Status Report
Functionality	:	
JS Functions	:
Created by		:	md mamun ahmed sagor
Creation date 	: 	27-09-2022
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Order Monitoring Report","../../../", 1, 1, $unicode,1,1);
$weekArr=array();
 for($i=1;$i<=52;$i++){
  $weekArr[$i]="week-".$i;
}
?>	
<script> 
	var permission = '<? echo $permission; ?>';	
		
	function fn_report_generated(type)
	{		
		//alert(type);		
		
		 if($('#txt_job_no').val()!='' || $('#txt_style_no').val()!='' || $('#txt_order_no').val()!='' || $('#cbo_season').val()!='' || $('#cbo_week').val()!=''){
			var file="cbo_company_name";
			var message="Comapny";
		}	
		else{
			var file="cbo_company_name*txt_date_from*txt_date_to";
			var message="Comapny*From Date*To Date";
		}
			
		if (form_validation(file,message)==false)
		{
			return;
		}
		else
		{			
			if(type==1){
				var action="report_generate";	
			}else{
				var action="report_generate2";	
			}
			var report_title=$( "div.form_caption" ).html();	
			var data="action="+action+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_order_no*txt_date_from*txt_date_to*txt_job_no*txt_job_id*txt_style_no*txt_style_id*txt_order_id*cbo_year*cbo_season*cbo_order_status*cbo_week',"../../../")+'&report_title='+report_title;
			
			
			
			freeze_window();
			http.open("POST","requires/hnm_order_status_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			$('#data_panel').html( '<br><b>Convert To </b><a href="requires/' + reponse[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>' );
			$('#data_panel').append( '&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>' );		
			$('#report_container').html(reponse[0]);
			
			
			var tableFilters = {
					col_operation: {
					id: ["td_po_quantity","value_td_po_total","td_gf_qnty","td_gp_qty","td_gp_to_qty","td_daying_qnty","td_ff_qnty","td_fabrics_avl_qnty","td_blance","td_cut_qnty","td_print_sent","td_print_recv","td_emb_issue_qnty","td_emb_rec_qnty","td_sp_issue_qnty","td_sp_rec_qnty","td_sewing_in_qnty","td_sewing_out_qnty","td_sewing_finish_qnty","td_ex_qnty","td_short_ship_qnty","td_short_ship_val","td_excess_ship_qnty","td_excess_ship_val","td_ship_bal_qnty"],
					col: [8,10,17,18,19,21,22,23,24,25,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41], 
					operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
					write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				}
			
				
				<!--cut==24 id-->
				
			setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{ 
		document.getElementById('scroll_body').style.overflowY="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		$("#table_body tr:first").hide();
		 
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="350px";
		
		$("#table_body tr:first").show();
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
	
	
	function date_fill_change(str)
	{
		if (str==1)
		{
			document.getElementById('search_date_td').innerHTML='Ship Date';
		}
		else if(str==2)
		{			
			document.getElementById('search_date_td').innerHTML='Original Ship Date';
		}

		else if(str==4)
		{			
			document.getElementById('search_date_td').innerHTML='Extended Ship Date';
		}

		else if(str==5)
		{			
			document.getElementById('search_date_td').innerHTML='In-Active Date';
		}
		else if(str==6)
		{			
			document.getElementById('search_date_td').innerHTML='Cancel Date';
		}

		else
		{
			document.getElementById('search_date_td').innerHTML='Insert Date';
		}
	}
	

	function generate_fabric_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
			d.close();
		}
	}
	function openmypage_booking(type)
	{

			if( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
			var title="";
	

			if(type==1){var title='Job No Search';}
			else if(type==2){var title='Style No Search';}
			else{var title='Order No Search';}

			var widthVal='1055px';
			var companyID = $("#cbo_company_name").val();
			var buyer_name = $("#cbo_buyer_name").val();
			var cbo_year  = $("#cbo_year").val();
			var page_link='requires/hnm_order_status_report_controller.php?action=job_style_order_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&type='+type+'&cbo_year='+cbo_year;
			
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+widthVal+',height=370px,center=1,resize=1,scrolling=0','../../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				// alert(no+','+id);
				if(type==1)
				{
					var no=this.contentDoc.getElementById("txt_select_no").value;
					var id=this.contentDoc.getElementById("txt_select_id").value;
			
					$('#txt_job_no').val(no);
					$('#txt_job_id').val(id);
					
				}else if(type==2)
				{
					
					var no=this.contentDoc.getElementById("txt_select_no").value;
					var id=this.contentDoc.getElementById("txt_select_id").value;
					
					$('#txt_style_no').val(no);
					$('#txt_style_id').val(id);
					
				}
				else
				{
				
					var no=this.contentDoc.getElementById("txt_select_no").value;
					var id=this.contentDoc.getElementById("txt_select_id").value;
					$('#txt_order_no').val(no);
					$('#txt_order_id').val(id);
				}	
				
				
			}
	  }
		
</script>
</head>

<body onLoad="set_hotkey();">
<form id="Order_monitoring_report">
    <div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../../");  ?>
        <h3 style="width:1300px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
            <fieldset style="width:1300px;">
                <table class="rpt_table" cellpadding="1" cellspacing="0" rules="all" border="1">
                    <thead>
                        <th width="150" class="must_entry_caption">Company</th>
                      	<th width="120">Buyer</th>
                        <th width="100">Season</th>  
						<th width="70">Week</th>              	
                        <th width="100">Year</th>               	
                        <th width="100">Job No.</th> 
						<th width="100">Style No.</th> 
                        <th width="100">PO No.</th>   
						<th width="80">Order Status</th>                 	
                        <th width="250" id="search_date_td">Pub. Ship Date</th>
                        <th width="100"><input type="reset" id="reset_btn" class="formbutton" style="width:60px" value="Reset" /></th>
                    </thead>
                    <tr class="general">
                        <td> 
							<?
                           	 // echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                           	  echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", 0, "load_drop_down( 'requires/hnm_order_status_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                            ?>
                        </td>
                      
                        <td id="buyer_td">
							<? 
                            	echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- All --", $selected, "",1,"" );
                            ?>
                        </td>
                        <td id="season_td">
							<? 
							echo create_drop_down( "cbo_season", 100, "select id,season_name  from lib_buyer_season where status_active=1 and is_deleted=0 order by season_name","id,season_name", 1, "--All Season--", 0, "" );
                        ?>
                        </td>
						<td>
							<? 
							
							echo create_drop_down( "cbo_week", 70, $weekArr,"", 1, "--All Week--", 0, "" );
                        ?>
                        </td>
                        <td id="year_td">
							<? 
							$year_current=date("Y");
							echo create_drop_down( "cbo_year", 100, $year,"", 1, "--All Year--", $year_current, "" );
                        ?>
                        </td>
                        <td>
                        	<input type="text"  id="txt_job_no" name="txt_job_no" class="text_boxes"  onDblClick="openmypage_booking(1);" placeholder="Write / Browse" >
							<input type="hidden" id="txt_job_id" name="txt_job_id" value=""/>
                        </td>
                        <td>
                        	<input type="text"  id="txt_style_no" name="txt_style_no" class="text_boxes" onDblClick="openmypage_booking(2);" placeholder="Write / Browse">
							<input type="hidden" id="txt_style_id" name="txt_style_id" value=""/>
                        </td>
						<td>
                        	<input type="text"  id="txt_order_no" name="txt_order_no" class="text_boxes" onDblClick="openmypage_booking(3);" placeholder="Write / Browse">
							<input type="hidden" id="txt_order_id" name="txt_order_id" value=""/>
                        </td>
                        <td>
							<? 
							$status_arr=array(1=>"Confirm",2=>"Projected");
							echo create_drop_down( "cbo_order_status", 80, $status_arr,"", 1, "--select--", $selected, "" );
                        ?>
                        </td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" value="" >&nbsp; To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"  placeholder="To Date" value=""  >
                        </td>
                        <td>
                        	<input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_report_generated(1)" />
							<input type="button" id="show_button" class="formbutton" style="width:100px" value="Weekly Summary" onClick="fn_report_generated(2)" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="9" align="center">
							<? echo load_month_buttons(1); ?>
                            
                        </td>
                    </tr>
                </table> 
            <br /> 
            </fieldset>
        </div>
    </div>
    <fieldset>
        <div id="data_panel" align="center"></div>
        <div id="report_container" align="center"></div>
    </fieldset>
</form>   
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
// set_multiselect('cbo_company_name','0','0','','0');
//$("#cbo_active_status").val(1);
$("#multiselect_dropdown_table_headercbo_company_name").click(function(){
	var data=$("#cbo_company_name").val();
	load_drop_down( 'requires/hnm_order_status_report_controller',data, 'load_drop_down_buyer', 'buyer_td' );
  
});
</script>
</html>