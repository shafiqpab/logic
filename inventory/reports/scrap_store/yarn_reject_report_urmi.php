<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yarn Reject Stock Report
				
Functionality	:	
JS Functions	:
Created by		:	Rakib
Creation date 	: 	30-04-2020
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
//-------------------------------------------------------------------------------------------------
echo load_html_head_contents('Yarn Reject Stock Report', '../../../', 1, 1, $unicode, '', '', '');
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = '../logout.php';	

	var tableFilters = 
	{
		col_15: "none",
		col_operation: {
		id: ["value_tot_opening_stock","value_tot_reject_qty","value_tot_scrap_out_qty","value_tot_closingStock"],
		col: [5,6,7,8],
		operation: ["sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	
	function openmypage_item_account()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('txt_item_acc').value+"_"+document.getElementById('txt_product_id_des').value;
		 //alert(data);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/yarn_reject_report_urmi_controller.php?action=item_account_popup&data='+data,'Item Account Popup', 'width=510px,height=400px,center=1,resize=0','../../')
		
		emailwindow.onclose=function()
		{
			var item_account_id=this.contentDoc.getElementById("item_account_id").value;
			var item_account_val=this.contentDoc.getElementById("item_account_val").value;
			document.getElementById("txt_product_id").value=item_account_id;
			document.getElementById("txt_item_acc").value=item_account_val;
		}
	}

	function openmypage_party()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
	
		var txt_knit_comp_id = $("#txt_knit_comp_id").val();
		var page_link='requires/yarn_reject_report_urmi_controller.php?action=party_popup&companyID='+companyID;
		var title='Supplier Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=430px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var party_name=this.contentDoc.getElementById("hide_party_name").value;
			var party_id=this.contentDoc.getElementById("hide_party_id").value;
			
			$('#txt_supplier').val(party_name);
			$('#txt_supplier_id').val(party_id);	 
		}
	}
	
	function generate_report(type)
	{
		if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*Date Form*Date To')==false )
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html(); 
		var cbo_company_id = $("#cbo_company_id").val();
		var txt_supplier_id = $("#txt_supplier_id").val();
		var txt_lot_no = $("#txt_lot_no").val();

		var txt_product_id = $("#txt_product_id").val();
		var from_date = $("#txt_date_from").val();
		var to_date = $("#txt_date_to").val();
		var cbo_search_by_zero = $("#cbo_search_by_zero").val();
		
		var dataString = "&cbo_company_id="+cbo_company_id+"&txt_product_id="+txt_product_id+"&from_date="+from_date+"&to_date="+to_date+"&cbo_search_by_zero="+cbo_search_by_zero+"&report_title="+report_title+"&type="+type+"&txt_supplier_id="+txt_supplier_id+"&txt_lot_no="+txt_lot_no;
		//alert(dataString);
		var data="action=generate_report"+dataString;
		//alert (data);
		freeze_window(operation);
		http.open("POST","requires/yarn_reject_report_urmi_controller.php",true);
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
			setFilterGrid("table_body",-1,tableFilters);
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
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		$("#table_body tr:first").show();
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="250px";
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
	
	function openmypage_yarn(prod_id,company_id,action,title,popup_width,from_date,to_date)
	{
		//alert(prod_id);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/yarn_reject_report_urmi_controller.php?prod_id='+prod_id+'&action='+action+'&company_id='+company_id+'&from_date='+from_date+'&to_date='+to_date, title, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '../../');
	}

</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?>    		 
        <form name="yarnrejectstock_1" id="yarnrejectstock_1" autocomplete="off" > 
        <h3 style="width:1020px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
        <div id="content_search_panel" style="width:1020px" >      
            <fieldset>  
                <table class="rpt_table" width="1020" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <th class="must_entry_caption">Company</th>
                        <th>Item Description</th>
                        <th>Supplier</th>
                        <th>Lot</th>
                        <th>Product Id</th>
                        <th>Value Opening Qty.</th>
                        <th class="must_entry_caption" colspan="2">Date</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('yarnrejectstock_1','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td>
                                <? 
                                echo create_drop_down( "cbo_company_id", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "" );
                                ?>                            
                            </td>
                            <td>
                            	<input style="width:150px;" name="txt_item_acc" id="txt_item_acc" class="text_boxes" onDblClick="openmypage_item_account()" placeholder="Browse" readonly />
                                <input type="hidden" name="txt_product_id_des" id="txt_product_id_des" style="width:90px;"/>
                            </td>
                            <td>
                            	<input style="width:100px;" name="txt_supplier" id="txt_supplier" class="text_boxes" onDblClick="openmypage_party()" placeholder="Browse" readonly />
                                <input type="hidden" name="txt_supplier_id" id="txt_supplier_id" style="width:90px;"/>
                            </td>
                             <td>
                                <input type="text" name="txt_lot_no" id="txt_lot_no" style="width:80px;" class="text_boxes" placeholder="Write"/>  
                            </td>
                            <td>
                                <input type="text" name="txt_product_id" id="txt_product_id" style="width:100px;" class="text_boxes" placeholder="Write"/>  
                            </td>
                            <td align="center">
	                        	<?
								$value_with_zero_arr=array(1=>"Value With 0",2=>"Value Without 0");
								echo create_drop_down( "cbo_search_by_zero", 120, $value_with_zero_arr,"",0, "--Select--", "","",0 );
	                        	?>
                        	</td> 
                            <td>
                                <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:70px;" readonly />	
                            </td>
                            <td>
                                <input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:70px;" readonly />          
                            </td>
                            <td>
                                <input type="button" name="search" id="search" value="Product" onClick="generate_report(1)" style="width:100px" class="formbutton" />
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7" align="center"><? echo load_month_buttons(1); ?></td>
                        </tr>
                    </tfoot>
                </table> 
            </fieldset> 
            </div>
            <br /> 
            <div id="report_container" align="center"></div>
            <div id="report_container2"></div> 
        </form>    
    </div>
</body>  
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
