<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Supplier Wise Goods Receive Statement Report
				
Functionality	:	
JS Functions	:
Created by		:	Aziz 
Creation date 	: 	22/09/2015
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
echo load_html_head_contents("Supplier Wise Goods Receive Statement","../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";


	var tableFilters = 
	 {
		col_30: "none",
		col_operation: {
		id: ["value_total_receive","value_total_issue"],
	   col: [16,17],
	   operation: ["sum","sum"],
	   write_method: ["innerHTML","innerHTML"]
		}
	 }
	 
	 var tableFilters2 = 
	 {
		col_30: "none",
		col_operation: {
		id: ["value_total_receive","value_total_issue"],
	   col: [19,20],
	   operation: ["sum","sum"],
	   write_method: ["innerHTML","innerHTML"]
		}
	 }
	  
	  
	 


	function openmypage_style()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();
		var txt_style_ref_no = $("#txt_style_ref_no").val();
		var txt_style_ref_id = $("#txt_style_ref_id").val();
		var txt_style_ref = $("#txt_style_ref").val();
		var cbo_year = $("#cbo_year").val();
		//var txt_style_ref_no = $("#txt_style_ref_no").val();
		var page_link='requires/supplier_wise_goods_recevied_statement_report_controller.php?action=style_refarence_surch&company='+company+'&buyer='+buyer+'&txt_style_ref_no='+txt_style_ref_no+'&txt_style_ref_id='+txt_style_ref_id+'&txt_style_ref='+txt_style_ref+'&cbo_year='+cbo_year;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			$("#txt_style_ref").val(style_des);
			$("#txt_style_ref_id").val(style_id); 
			$("#txt_style_ref_no").val(style_no); 
		}
	}
	
	
	function openmypage_order()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();	
		var txt_order_id_no = $("#txt_order_id_no").val();
		var txt_order_id = $("#txt_order_id").val();
		var txt_order = $("#txt_order").val();
		var page_link='requires/supplier_wise_goods_recevied_statement_report_controller.php?action=order_surch&company='+company+'&buyer='+buyer+'&txt_order_id_no='+txt_order_id_no+'&txt_order_id='+txt_order_id+'&txt_order='+txt_order;  
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=520px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			$("#txt_order").val(style_des);
			$("#txt_order_id").val(style_id); 
			$("#txt_order_id_no").val(style_des_no);
		}
	}

// Supplier
function openmypage_supplier()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();	
		var txt_supplier_id_no = $("#txt_supplier_id_no").val();
		var txt_supplier_id = $("#txt_supplier_id").val();
		var txt_supplier = $("#txt_supplier").val();
		var page_link='requires/supplier_wise_goods_recevied_statement_report_controller.php?action=supplier_popup&company='+company+'&buyer='+buyer+'&txt_supplier_id_no='+txt_supplier_id_no+'&txt_supplier_id='+txt_supplier_id;  
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var supplier_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var supplier_name=this.contentDoc.getElementById("txt_selected").value; // product Description
			var supplier_id_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			$("#txt_supplier").val(supplier_name);
			$("#txt_supplier_id").val(supplier_id); 
			$("#txt_supplier_id_no").val(supplier_id_no);
		}
	}

// Item 
function openmypage_item()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();	
		var txt_supplier_id = $("#txt_supplier_id").val();
		var cbo_item_category = $("#cbo_item_cat").val();
		//var txt_order = $("#txt_order").val();
		var page_link='requires/supplier_wise_goods_recevied_statement_report_controller.php?action=item_item_popup&company='+company+'&buyer='+buyer+'&txt_supplier_id='+txt_supplier_id+'&cbo_item_category='+cbo_item_category;  
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var item_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var item_name=this.contentDoc.getElementById("txt_selected").value; // product Description
			//var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			$("#txt_item").val(item_name);
			$("#txt_item_id").val(item_id); 
			//$("#txt_order_id_no").val(style_des_no);
		}
	}
	// WO  Order 
function openmypage_wo_order()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();	
		var txt_supplier_id = $("#txt_supplier_id").val();
		var cbo_item_category = $("#cbo_item_cat").val();
		//var txt_order = $("#txt_order").val();
		var page_link='requires/supplier_wise_goods_recevied_statement_report_controller.php?action=item_wo_order_popup&company='+company+'&buyer='+buyer+'&txt_supplier_id='+txt_supplier_id+'&cbo_item_category='+cbo_item_category;  
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var wo_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var wo_prefix=this.contentDoc.getElementById("txt_selected").value; // product Description
			//var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			$("#txt_wo_order").val(wo_prefix);
			$("#txt_wo_order_id").val(wo_id); 
			//$("#txt_order_id_no").val(style_des_no);
		}
	}	
	
function reset_field()
{
	reset_form('item_receive_issue_1','report_container2','','','','');
}
function job_order_per()
{
	var item_val=$('#cbo_item_cat').val();
	if((item_val==2)||(item_val==3)||(item_val==4)||(item_val==13)||(item_val==14))
	{
		$('#txt_style_ref').attr("disabled",false);
		$('#txt_order').attr("disabled",false);
		$('#cbo_buyer_name').attr("disabled",false);
		$('#cbo_order_type').attr("disabled",false);
	}
	
	else
	{
		$('#cbo_order_type').attr("disabled",true).val(0);
		$('#txt_style_ref').attr("disabled",true).val('');
		$('#txt_order').attr("disabled",true).val('');
		$('#cbo_buyer_name').attr("disabled",true).val('');
	}
	if(item_val==1)
	{
		$('#cbo_dyed_type').attr("disabled",false);
		//$('#show_textcbo_source').attr("disabled",false);
		$('#cbo_yarn_count').attr("disabled",false);
	}
	else
	{
		$('#cbo_dyed_type').attr("disabled",true).val('');
		//$('#show_textcbo_source').attr("disabled",true).val('');
		$('#cbo_yarn_count').attr("disabled",true).val('');
	}
	
}

function  generate_report(rptType)
{
	var cbo_item_cat = $("#cbo_item_cat").val();
	var cbo_company_name = $("#cbo_company_name").val();
	var cbo_buyer_name = $("#cbo_buyer_name").val();
	var txt_style_ref = $("#txt_style_ref").val();
	var txt_style_ref_id = $("#txt_style_ref_id").val();	
	var txt_order = $("#txt_order").val();
	var txt_order_id = $("#txt_order_id").val();
	var txt_date_from = $("#txt_date_from").val(); 
	var txt_date_to = $("#txt_date_to").val();
	var cbo_currency = $("#cbo_currency").val();
	var txt_supplier_id = $("#txt_supplier_id").val();
	var txt_wo_order_id = $("#txt_wo_order_id").val();	
	var txt_wo_order = $("#txt_wo_order").val();
	var txt_item_id = $("#txt_item_id").val();
	var txt_item = $("#txt_item").val();
	var cbo_season_id = $("#cbo_season_id").val();
	
	
	if(txt_style_ref!="" || txt_order!="" )
	{
		if( form_validation('cbo_item_cat*cbo_company_name','Item Cetagory*Company Name')==false )
		{
			return;
		}
	}
	else
	{
		if( form_validation('cbo_company_name*txt_supplier*cbo_item_cat*cbo_currency','Company Name*Supplier*Category*Form Date*To Date*Currency')==false )
		{
			return;
		}
	}
	
	 
	var dataString = "&cbo_item_cat="+cbo_item_cat+"&cbo_company_name="+cbo_company_name+"&cbo_buyer_name="+cbo_buyer_name+"&txt_style_ref="+txt_style_ref+"&txt_style_ref_id="+txt_style_ref_id+"&txt_order="+txt_order+"&txt_order_id="+txt_order_id+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&cbo_currency="+cbo_currency+"&txt_wo_order="+txt_wo_order+"&txt_wo_order_id="+txt_wo_order_id+"&txt_item_id="+txt_item_id+"&txt_item="+txt_item+"&txt_supplier_id="+txt_supplier_id+"&cbo_season_id="+cbo_season_id+"&rptType="+rptType;
	
	var data="action=generate_report"+dataString;
	
	
	// for big amount of data(created for Youth group.) 
		// var data="action=generate_report2"+dataString;

	//alert(data);
	freeze_window(5);
	http.open("POST","requires/supplier_wise_goods_recevied_statement_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_report_reponse; 
}

function generate_report_reponse()
{	
	if(http.readyState == 4) 
	{
		//alert(http.responseText);	 
		var reponse=trim(http.responseText).split("**");
		//alert(reponse[0]);
		$("#report_container2").html(reponse[0]);
		document.getElementById('report_container').innerHTML=report_convert_button('../../');
		//append_report_checkbox('table_header_1',1);
		
		release_freezing();
		show_msg('3');
		//document.getElementById('report_container').innerHTML=report_convert_button('../../');
	}
} 

function fn_change_base(str)
{
	//alert(str);	
	if(str==1)
	{
		$("#up_tr_date").html("");
		$("#up_tr_date").html("Transaction Date Range");
		$('#up_tr_date').attr('style','color:blue');
	}
	else
	{
		$("#up_tr_date").html("");
		$("#up_tr_date").html("Insert Date Range");
		$('#up_tr_date').attr('style','color:blue');
	}
}
	
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
    <form name="item_receive_issue_1" id="item_receive_issue_1" autocomplete="off" >
    <h3 align="left" id="accordion_h1" style="width:1260px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div style="width:1260px;" align="center" id="content_search_panel">
        <fieldset style="width:1250px;">
                <table class="rpt_table" width="1250" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                    	<th width="100" class="must_entry_caption">Company</th>
                        <th width="80" class="must_entry_caption">Supplier</th>
                        <th width="120" class="must_entry_caption">Item Category</th>
                        <th width="80">Item Group</th>
                        <th width="80">WO Order No</th>
                        <th width="100">Buyer Name</th>
                        <th width="80">Job NO.</th>
                        <th width="80">Order No.</th>
                        <th width="80">Season</th>
                        <th width="160" id="up_tr_date">Date Range</th>
                        <th width="90" class="must_entry_caption">Currency</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:90px" class="formbutton" onClick="reset_field()" /></th>
                    </tr>
                </thead>
                <tr class="general">
                	<td>
                            <?
                        	echo create_drop_down( "cbo_company_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/supplier_wise_goods_recevied_statement_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
							
                        ?>                          
                    </td>
                     <td align="center" id="supplier">
                        <input type="text" style="width:80px;"  name="txt_supplier" id="txt_supplier"  ondblclick="openmypage_supplier()"  class="text_boxes" placeholder="Browse"   />   
                        <input type="hidden" name="txt_supplier_id" id="txt_supplier_id" />
                        <input type="hidden" id="txt_supplier_id_no" name="txt_supplier_id_no" />    
                                     
                     </td>
                     <td>
						<?
                        	echo create_drop_down( "cbo_item_cat", 130, $item_category,"", 0, "-- Select Item --", $selected, "",0,"4,11" );
							
                        ?>
                        
                    </td>
                     <td>  
                         <input type="text" style="width:80px;"  name="txt_item" id="txt_item"  ondblclick="openmypage_item()"  class="text_boxes" placeholder="Browse"   />   
                        <input type="hidden" name="txt_item_id" id="txt_item_id" style="width:30px;" class="text_boxes" />
                    </td>
                     <td>
                    	 <input type="text" style="width:80px;"  name="txt_wo_order" id="txt_wo_order"  ondblclick="openmypage_wo_order()"  class="text_boxes" placeholder="Browse"   />   
                        <input type="hidden" name="txt_wo_order_id" id="txt_wo_order_id" style="width:30px;" class="text_boxes" />
                    </td>
                    <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); ?></td>
                    <td align="center">
                        <input style="width:80px;"  name="txt_style_ref" id="txt_style_ref"  ondblclick="openmypage_style()"  class="text_boxes" placeholder="Browse or Write"   />   
                        <input type="hidden" name="txt_style_ref_id" id="txt_style_ref_id"/>    <input type="hidden" name="txt_style_ref_no" id="txt_style_ref_no"/>            
                    </td>
                     <td align="center">
                        <input type="text" style="width:80px;"  name="txt_order" id="txt_order"  ondblclick="openmypage_order()"  class="text_boxes" placeholder="Browse or Write"   />   
                        <input type="hidden" name="txt_order_id" id="txt_order_id"/> <input type="hidden" name="txt_order_id_no" id="txt_order_id_no"/>               
                    </td>
                    <td id="season_td"><? echo create_drop_down( "cbo_season_id", 80, $blank_array,'', 1, "-- Select Season--",$selected, "" ); ?></td>
                    <td>
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px;" placeholder="From Date" readonly/>TO
                    	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To Date" style="width:55px;" readonly/>
                    </td>
                    <td>
                    	<?
                        echo create_drop_down( "cbo_currency", 90, $currency,"", 1, "Select Currency", 0, "",0,"1,2" );
                        ?>
                    </td>
                    <td>
                        <input type="button" name="search" id="search" value="Summary" onClick="generate_report(1)" style="width:55px" class="formbutton" />
                        <input type="button" name="search" id="search" value="Details" onClick="generate_report(2)" style="width:55px" class="formbutton" />
                    </td>
                </tr>
                <tr>
                	<td colspan="13" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                </tr>
            </table> 
        </fieldset> 
    </div>
        <!-- Result Contain Start-------------------------------------------------------------------->
        	<div id="report_container" align="center"></div>
            <div id="report_container2"></div> 
        <!-- Result Contain END-------------------------------------------------------------------->
    </form>    
</div>    
</body>
<script>
	set_multiselect('cbo_item_cat','0','0','','0');
</script>  

<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
