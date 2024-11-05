<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Deleted Fabric Booking List Report.
Functionality	:	
JS Functions	:
Created by		:	Md Mamun Ahmed Sagor
Creation date 	: 	22-12-2022
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
CRM ID			:23406
Update CRM ID	:
*/

session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Deleted Fabric Booking List Report","../../../", 1, 1, $unicode,1,1);
?>	
<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	var permission = '<? echo $permission; ?>';
	var tableFilters = {
						col_operation: {
						id: ["total_wo_fin_qty","total_wo_grey_qty"],
						col: [9,10],
						operation: ["sum","sum"],
						write_method: ["innerHTML","innerHTML"]
						}
					}
		function fn_report_generated()
		{
			
			var txt_wo_no = $("#txt_wo_no").val();
			var txt_date_from = $("#txt_date_from").val();
			var txt_date_to = $("#txt_date_to").val();
			
				if(txt_wo_no=="" && txt_date_from=="" && txt_date_to==""){
					if(form_validation('txt_date_from*txt_date_to','Form Date*To Date')==false)
					{
						return;
					}
				}

				
			
			var report_title=$( "div.form_caption" ).html();	
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_wo_no*txt_wo_id*txt_date_from*txt_date_to*cbo_year',"../../../")+'&report_title='+report_title;
			//alert(data);return;
			

			$.ajax({
					url: 'requires/deleted_fabric_booking_list_report_controller.php',
					type: 'POST',
					data: data,
					success: function(data){
						release_freezing();
					var response=trim(data).split("**");			
					$('#report_container2').html(response[0]);				 
					document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1);" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';			
					setFilterGrid("table_body",-1,tableFilters);
					show_msg('3');
					}
				});
		}
		
	
	function new_window(type)
	{
		
		 
		  
		 $('.scroll_div_inner').css('overflow','auto');
		 $('.scroll_div_inner').css('maxHeight','none');
		 
		$("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	 '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		$('.scroll_div_inner').css('overflow','scroll');
		$('.scroll_div_inner').css('maxHeight','480px');
		$("#table_body tr:first").show();
	}

	function openmypage_wo()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();	
		
    	//	var cbo_year = $("#cbo_year").val();
		var txt_order_id_no = $("#txt_order_id_no").val();
		var txt_wo_id = $("#txt_wo_id").val();
		var txt_wo_no = $("#txt_wo_no").val();
		var page_link='requires/deleted_fabric_booking_list_report_controller.php?action=work_order_popup&company='+company+'&buyer_name='+buyer+'&txt_order_id_no='+txt_order_id_no+'&txt_wo_id='+txt_wo_id+'&txt_wo_no='+txt_wo_no; 
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			$("#txt_wo_no").val(style_des);
			$("#txt_wo_id").val(style_id); 
			$("#txt_order_id_no").val(style_des_no);
		}
	}

	function fn_order_disable(type_id)
	{
		if(type_id==2)
		{
			$('#txt_wo_no').attr("disabled",true);
		}
		else
		{
			$('#txt_wo_no').attr("disabled",false);
		}
	}
	
	
	
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
        <form id="orderStatusReport" name="orderStatusReport">
			<? echo load_freeze_divs ("../../../"); ?>
            <h3 align="left" id="accordion_h1" style="width:750px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:750px;">
                <table class="rpt_table" width="750" cellpadding="0" cellspacing="0" align="center" rules="all">
                    <thead>
                       
						<th class="" width="130">Company Name</th>
                        <th width="130">Buyer Name</th>
						<th width="60">Year</th>
						<th  width="120">WO No</th>
                   
					
                        <th width="170" class="must_entry_caption">Date Range</th>
                       
                        <th><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:70px" onClick="reset_form('orderStatusReport','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tr class="general">
					            
                        <td> 
							<?
                            echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/deleted_fabric_booking_list_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
			 
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); ?></td>
                        <td><?
						$year=date("Y",time()) ;
						 echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --",$year , "",0,"" );?>	</td>
                      
                         <td align="center">
                            <input style="width:120px;" name="txt_wo_no" id="txt_wo_no" onDblClick="openmypage_wo()" class="text_boxes" placeholder="Write/Browse"  />   
                            <input type="hidden" name="txt_wo_id" id="txt_wo_id"/> <input type="hidden" name="txt_order_id_no" id="txt_order_id_no"/>               
                        </td>
					
					
                        <td>
                            <input type="text" id="txt_date_from" name="txt_date_from" class="datepicker" style="width:60px" readonly>To
                            <input type="text" id="txt_date_to" name="txt_date_to" class="datepicker" style="width:60px" readonly>
                        </td>
                       
                        <td>
                        	<input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated()" />
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="9" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                    </tr>
                </table>
            </fieldset>
            </div>
        </form>
    </div> 
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>  
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
