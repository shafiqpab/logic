<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Page Will Create Sewing Plan Wise Cutting Plan Report
Functionality	:	
JS Functions	:
Created by		:	Kamrul Hasan
Creation date 	: 	18-June-2023
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

$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sewing Plan Wise Cutting Plan Report","../../", 1, 1, $unicode,1,1);
?>	
<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	
	
	
	function fn_report_generate(type)
	{
		
		
		if(($("#txt_date_from").val() =='' && $("#txt_date_to").val() =='' && $("#txt_job_no").val() =='' && $("#txt_order_no").val() =='' && $("#txt_internal_ref_no").val() =='') || $("#cbo_company_id").val() ==''){
			if(form_validation("cbo_company_id*txt_date_from*txt_date_to","Company Name*From Date*To Date")==false)
			{
				alert ('Please Select Company and Plan Date ');
				return;
			}
		}
		else
		{	
			var data="action=report_generate&type="+type+get_submitted_data_string('cbo_company_id*cbo_buyer_name*txt_date_from*txt_date_to*txt_job_no*txt_order_no*txt_internal_ref_no*txt_cutting_per',"../../");
			//  alert(data);
			freeze_window(3);
			http.open("POST","requires/sewing_plan_wise_cutting_plan_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_report_reponse;  
		}
	}
		
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			//alert (http.responseText);
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);  
			//alert(reponse[1]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid('tbl_list_search',-1);
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
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="all" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="auto"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
	}
	

	function getCompanyId() 
	{
	    var company_id = document.getElementById('cbo_company_id').value;
	    //var search_type = document.getElementById('cbo_search_by').value;
	    if(company_id !='') {
		  var data="action=load_drop_down_buyer&data="+company_id;
		  //alert(data);die;
		  http.open("POST","requires/sewing_plan_wise_cutting_plan_report_controller.php",true); 
		  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		  http.send(data); 
		  http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              load_drop_down( 'requires/sewing_plan_wise_cutting_plan_report_controller', company_id, 'load_drop_down_buyer', 'buyer_td' );
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
         <h3 align="left" id="accordion_h1" style="width:1020px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:1020px;">
                <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                	<thead>
                    	<tr>                   
                            <th class="must_entry_caption">Company Name</th>
                             <th>Buyer Name</th>
                             <th >Job No</th>
							 <th >Order No</th>
                             <th>M.Style/Int.Ref.</th>
                             <th class="must_entry_caption" id="td_date_caption" colspan="3">Plan Date</th>
							 <th>Cutting %</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general" id="company_td">
                        <td> 
                           <?
								echo create_drop_down( "cbo_company_id", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 0, "-- Select Company --", $selected, "load_drop_down( 'requires/sewing_plan_wise_cutting_plan_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                                             
                          <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                        
                          <td>
						  <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:80px"  placeholder="Wr." />
                            
                        </td>
                         <td>
						 <input type="text" id="txt_order_no" name="txt_order_no" class="text_boxes" style="width:80px"  placeholder="Wr." />
                            
                        </td>
                         <td>
							 <input type="text" id="txt_internal_ref_no" name="txt_internal_ref_no" value="" class="text_boxes" style="width:80px" placeholder="Wr."/>
                        </td>
                         
                        
                        <td>
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" ></td>
                        <td>To</td>
                        <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" ></td>

						<td>
							 <input type="text" id="txt_cutting_per" name="txt_cutting_per" value="" class="text_boxes_numeric" style="width:80px" />
                        </td>
                                             
                        
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generate(1)" />
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
	set_multiselect('cbo_company_id','0','0','0','0');	
	
	setTimeout[($("#company_td a").attr("onclick","disappear_list(cbo_company_id,'0');getCompanyId();") ,3000)]; 
</script>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
