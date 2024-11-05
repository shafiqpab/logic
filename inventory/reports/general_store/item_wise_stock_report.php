<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Item Wise Stock Report
				
Functionality	:	
JS Functions	:
Created by		:	Nayem 
Creation date 	: 	31-10-2021
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
$user_id = $_SESSION['logic_erp']['user_id'];

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Item Wise Stock Report","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function openmypage_group()
	{
		var category=document.getElementById('cbo_category_id').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/item_wise_stock_report_controller.php?category='+category+'&action=item_group_popup','Search Item Group', 'width=950px,height=400px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var data=this.contentDoc.getElementById("item_id").value.split('_');
			$("#txt_item_group_id").val(data[0]);
			$("#txt_item_group").val(data[1]);
		}
	}
	function openmypage_item()
	{
		var cbo_company_name = $("#cbo_company_id").val();	
		var cbo_category_id = $("#cbo_category_id").val();
		var page_link='requires/item_wise_stock_report_controller.php?action=item_description_popup&cbo_company_name='+cbo_company_name+'&cbo_category_id='+cbo_category_id; 
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=870px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var data=this.contentDoc.getElementById("txt_selected_id").value.split('_');
			$("#txt_product_id").val(data[0]);
			$("#txt_description").val(data[1]); 
		}
	}

	function generate_report(operation)
	{
		var cbo_company_id = $("#cbo_company_id").val();
		var cbo_category_id = $("#cbo_category_id").val();
		var txt_item_group = $("#txt_item_group").val();
		var txt_item_group_id = $("#txt_item_group_id").val();
		var txt_item_code = $("#txt_item_code").val();
		var txt_description = $("#txt_description").val();	
		var txt_product_id = $("#txt_product_id").val();
		var report_title=$( "div.form_caption" ).html();
        var cbo_store_name = $("#cbo_store_name").val();
		if(cbo_category_id=='' && txt_description=='')
		{
			alert("Please Select Item Category or Item Description");
			return;
		}
		
		var dataString = "&cbo_company_id="+cbo_company_id+"&cbo_category_id="+cbo_category_id+"&txt_description="+txt_description+"&txt_product_id="+txt_product_id+"&txt_item_group_id="+txt_item_group_id+"&txt_item_group_id="+txt_item_group_id+"&txt_item_code="+txt_item_code+"&report_title="+report_title+"&cbo_store_name="+cbo_store_name;
		var data="action=generate_report"+dataString;
		freeze_window(operation);
		http.open("POST","requires/item_wise_stock_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}

	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body",-1);
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('.fltrow').hide(); 
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		$('.fltrow').show();
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="250px";
	}
	
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission);  ?>   		 
    <form name="itemWiseStock_1" id="itemWiseStock_1" autocomplete="off" > 
    <h3 style="width:900px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div id="content_search_panel" style="width:100%;" align="center">
        <fieldset style="width:900px;">
			<table class="rpt_table" width="900" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th width="130" >Company</th>
                        <th width="130" >Item Category</th>    
						<th width="110">Item Group</th>                            
						<th width="110">Item Code</th>                            
                        <th width="130" >Item Description</th>
                        <th width="100">Store</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('itemWiseStock_1','','','','','')" /></th>
                    </tr>
                </thead>
                <tr class="general" align="center">
                    <td>
						<? 
							echo create_drop_down( "cbo_company_id", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
						?>                            
                    </td>
					<td align="center">
						<?
							echo create_drop_down( "cbo_category_id", 130, $general_item_category,"", 1, "-- Select Item --", $selected, "",0,"" );
						?>                           
                    </td>
                    <td align="center">
						<input style="width:110px;"  name="txt_item_group" id="txt_item_group"  ondblclick="openmypage_group()"  class="text_boxes" placeholder="Browse"  readonly />   
                        <input type="hidden" name="txt_item_group_id" id="txt_item_group_id"/>                         
                    </td>
					<td align="center">
						<input style="width:110px;"  name="txt_item_code" id="txt_item_code" class="text_boxes" placeholder="Write" />
                    </td>
                    <td align="center">
                        <input style="width:130px;"  name="txt_description" id="txt_description" ondblclick="openmypage_item()"  class="text_boxes" placeholder="Write or Browse" />   
                        <input type="hidden" name="txt_product_id" id="txt_product_id"/>                
                    </td>
                    <td id="store_td">
                        <?   
                            echo create_drop_down( "cbo_store_name", 130, $blank_array,"", 1, "-- Select Store --", 0, "", 0 );
                        ?>
                    </td>
                    <td>
                        <input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:70px" class="formbutton" />
                    </td>
                </tr>
            </table> 
        </fieldset> 
    </div>
    <br /> 
        <!-- Result Contain Start-->
		<div id="report_container" align="center"></div>
		<div id="report_container2"></div> 
        <!-- Result Contain END-->
    </form>    
</div>    
</body>  
<script>
	set_multiselect('cbo_company_id','0','0','0','0');
	set_multiselect('cbo_category_id','0','0','0','0');
	$("#multi_select_cbo_company_id a").click(function(){
		load_company_store();
 	});
	function load_company_store()
	{
		var company=$("#cbo_company_id").val();
		load_drop_down( 'requires/item_wise_stock_report_controller', company, 'load_drop_down_store', 'store_td' );
	}
</script> 
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
