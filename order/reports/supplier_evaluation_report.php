<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Supplier Evaluation Report	
Functionality	:	
JS Functions	:
Created by		:	Md Mamun Ahmed Sagor
Creation date 	: 	22-09-2021
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

//------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Supplier Evaluation Report","../../", 1, 1, $unicode,1,1);
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	
	var tableFilters = 
	 {
		col_30: "none",
		col_operation: {
		id: ["total_buyer_po_quantity","value_total_buyer_po_value","parcentages","total_current_ex_Fact_Qty","value_total_current_ex_fact_value","mt_total_ex_fact_qty","value_mt_total_ex_fact_value"],
	    col: [2,3,4,5,6,7,8],
	    operation: ["sum","sum","sum","sum","sum","sum","sum"],
	    write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 } 
	 
	function fn_report_generated(type)
	{		

		if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false)
		{
			return;
		}
		else
		{
			if(type==1)
			{
			
				var data="action=report_generate&reportType="+type+get_submitted_data_string('cbo_company_name*cbo_supplier_name*cbo_buyer_name*cbo_trims_name*txt_order_no*txt_date_from*txt_date_to',"../../");
			}
			
			//alert(data);return;
			freeze_window(3);
			http.open("POST","requires/supplier_evaluation_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");

			$("#report_container2").html(response[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
	 		setFilterGrid("table_body",-1);
			
	 		show_msg('3');
			release_freezing();
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	    '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="330px";
		$("#table_body tr:first").show();
	}	
	
	function change_colors(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
			document.getElementById(v_id+'1').bgColor=e_color;
			document.getElementById(v_id+'2').bgColor=e_color;
			document.getElementById(v_id+'3').bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
			document.getElementById(v_id+'1').bgColor="#33CC00";
			document.getElementById(v_id+'2').bgColor="#33CC00";
			document.getElementById(v_id+'3').bgColor="#33CC00";
		}
	}
	
	function getBuyerId() 
	{
	    var company_name = document.getElementById('cbo_supplier_name').value;
		//alert(company_name)
	    if(company_name !='') {
		  var data="action=load_drop_down_buyer&data="+company_name;
		  //alert(data);die;
		  http.open("POST","requires/supplier_evaluation_report_controller.php",true);
		  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		  http.send(data); 
		  http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText);
	              $('#buyer_td').html(response);
				  
				  set_multiselect('cbo_buyer_name','0','0','','0');
	          }			 
	      };
	    }         
	}	
	
</script>

</head>
 
<body onLoad="set_hotkey();">
<div style="width:1050px" align="center">
<form id="monthly_ex_factory" name="monthly_ex_factory">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:900px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:900px;">
                <table class="rpt_table" width="900" cellpadding="1" cellspacing="2" align="center" rules="all">
                	<thead>
                    	<tr>                   
                            <th width="13" class="must_entry_caption">Company Name</th>
                             <th width="130">Supplier</th>
							 <th width="130">Buyer Name</th>
							 <th width="130">Trims Name</th>
                             <th width="80">Order No</th>                            
                            <th width="80" colspan="2" class="must_entry_caption">Receive Date Range</th>
                            <th>                           
                            	<input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:80px" onClick="reset_form('','report_container*report_container2','','','')" />
                            </th>
                        </tr>
                     </thead>
                     <tbody>
                         <tr>
							 <td id="lccompany_td"><? echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/supplier_evaluation_report_controller',this.value, 'load_drop_down_supplier', 'supplier_td' );set_multiselect('cbo_supplier_name','0','0','0','0');load_drop_down( 'requires/supplier_evaluation_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );set_multiselect('cbo_buyer_name','0','0','0','0');" ); ?>
							</td>
                            <td id="supplier_td"><? echo create_drop_down( "cbo_supplier_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Supplier --", $selected, "" ); ?>
							</td>
                            <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); ?></td>
							<td id="trims_td"><? echo create_drop_down( "cbo_trims_name", 130, "select id,item_name from lib_item_group where item_category=4 and status_active=1 and is_deleted=0 order by item_name","id,item_name", 1, "-- All Trims --", $selected, "",0,"" ); ?></td>
							<td><input name="txt_order_no" id="txt_order_no"  class="text_boxes" style="width:80px"  placeholder="Write" /></td> 
                           
						   
							<td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date"/></td>
                       		<td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"  placeholder="To Date" /></td>
                            <td>
                                <input type="button" id="show_button" align="center" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated(1);" />                              
                            </td>
                        </tr>
						<tr>
                        	<td align="center" colspan="7"><? echo load_month_buttons(1); ?></td>
                  		  </tr>
                    </tbody>
                </table>               
            </fieldset>
        </div>
    </div>
    </form>

    <!--<div id="report_container" align="center"></div>-->
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
    <div style="display:none" id="data_panel"></div>  
 </div>    
</body>
<script>
	set_multiselect('cbo_supplier_name','0','0','0','0');
	setTimeout[($("#supplier_td a").attr("onclick","disappear_list(cbo_supplier_name,'0'); getBuyerId();"),3000)]; 	
	
	set_multiselect('cbo_buyer_name','0','0','','0');
	set_multiselect('cbo_trims_name','0','0','','0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
