<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Reject Stock Report
				
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	28-05-2015
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
echo load_html_head_contents("Reject Stock Report","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	var tableFilters = 
	{
		col_30: "none",
		col_operation: {
		id: ["value_tot_opening","value_tot_reject_qty","value_tot_scrap_out_qty","value_tot_closingStock"],
		col: [6,7,8,9],
		operation: ["sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 
	
	var tableFilters2 = 
	{
		col_30: "none",
		col_operation: {
		id: ["value_tot_opening","value_tot_reject_qty","value_tot_scrap_out_qty","value_tot_closingStock"],
		col: [10,11,12,13],
		operation: ["sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	
	function openmypage_item_account()
	{
		if( form_validation('cbo_company_id*cbo_category_id','Company Name*Item Category')==false )
		{
			return;
		}
		 var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_category_id').value+"_"+document.getElementById('txt_item_acc').value+"_"+document.getElementById('txt_product_id_des').value;
		 //alert(data);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/reject_report_controller.php?action=item_account_popup&data='+data,'Item Account Popup', 'width=510px,height=400px,center=1,resize=0','../../')
		
		emailwindow.onclose=function()
		{
			var item_account_id=this.contentDoc.getElementById("item_account_id").value;
			var item_account_val=this.contentDoc.getElementById("item_account_val").value;
			document.getElementById("txt_product_id").value=item_account_id;
			document.getElementById("txt_item_acc").value=item_account_val;
		}
	}
	
	function generate_report(rpt_type)
	{
		if( form_validation('cbo_company_id*cbo_category_id*txt_date_from*txt_date_to','Company Name*Item Category*Date Form*Date To')==false )
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html(); 
		var cbo_company_id = $("#cbo_company_id").val();
		//var txt_product_id_des = $("#txt_product_id_des").val();
		var txt_product_id = $("#txt_product_id").val();
		var cbo_category_id = $("#cbo_category_id").val();
		var cbo_year = $("#cbo_year").val();
		var txt_job_no = $("#txt_job_no").val();
		var from_date = $("#txt_date_from").val();
		var to_date = $("#txt_date_to").val();
		var cbo_search_by_zero = $("#cbo_search_by_zero").val();
		
		var dataString = "&cbo_company_id="+cbo_company_id+"&cbo_category_id="+cbo_category_id+"&txt_product_id="+txt_product_id+"&cbo_year="+cbo_year+"&txt_job_no="+txt_job_no+"&from_date="+from_date+"&to_date="+to_date+"&cbo_search_by_zero="+cbo_search_by_zero+"&report_title="+report_title+"&rpt_type="+rpt_type;
		//alert(dataString);
		var data="action=generate_report"+dataString;
		//alert (data);
		freeze_window(5);
		http.open("POST","requires/reject_report_controller.php",true);
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
			
			if(reponse[2]==1)
			{
				setFilterGrid("table_body",-1,tableFilters);
			}
			else
			{
				setFilterGrid("table_body",-1,tableFilters2);
			}
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
	
	function openmypage_job()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var cbo_year_id = $("#cbo_year").val();
		var page_link='requires/reject_report_controller.php?action=job_no_popup&companyID='+companyID+'&cbo_year_id='+cbo_year_id;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_job_no').val(job_no);
			$('#txt_job_id').val(job_id);	 
		}
	}

</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?>    		 
        <form name="rejectstock_1" id="rejectstock_1" autocomplete="off" > 
         <h3 style="width:1140px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:1140px" >      
            <fieldset>  
                <table class="rpt_table" width="1130" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <th class="must_entry_caption">Company</th>
                        <th class="must_entry_caption">Item Category</th>
                        <th>Item Description</th>
                        <th>Product Id</th>
                        <th>Year</th>
                        <th>Job</th>
                        <th>Value Reject Qnty.</th>
                        <th class="must_entry_caption" colspan="2">Transaction Date</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('rejectstock_1','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td>
                                <? 
                                    echo create_drop_down( "cbo_company_id", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "" );
                                ?>                            
                            </td>
                            <td>
                                <? 
                                   echo create_drop_down( "cbo_category_id", 120, $item_category,"", 1, "--Select Category--", "", "" );
                                ?>
                           </td>
                            <td>
                            	<input style="width:120px;" name="txt_item_acc" id="txt_item_acc" class="text_boxes" onDblClick="openmypage_item_account()" placeholder="Browse" readonly />
                                <input type="hidden" name="txt_product_id_des" id="txt_product_id_des" style="width:90px;"/>
                            </td>
                            <td>
                                <input type="text" name="txt_product_id" id="txt_product_id" style="width:60px;" class="text_boxes" placeholder="Write"/>  
                            </td>
                            <td> 
                                <?
                                    echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
                                ?>
                            </td>
                            <td>
                                <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes_numeric" style="width:80px" onDblClick="openmypage_job();" placeholder="Browse/Write" />
                                <input type="hidden" id="txt_job_id" name="txt_job_id"/>
                            </td> 
                            <td align="center">
	                        	<?
									$value_with_zero_arr=array(1=>"Value With 0",2=>"Value Without 0");
									echo create_drop_down( "cbo_search_by_zero", 130, $value_with_zero_arr,"",0, "--Select--", "","",0 );
	                        	?>
                        	</td>                          
                            <td>
                                    <input type="text" name="txt_date_from" id="txt_date_from" value=" <? echo date("d-m-Y", time() - 86400);?> " class="datepicker" style="width:60px;" readonly />                    							
                           </td>
                           <td>
                                <input type="text" name="txt_date_to" id="txt_date_to" value=" <? echo date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:60px;" readonly />                        
                            </td>
                            <td>
                                <input type="button" name="search" id="search" value="Prod. Wise" onClick="generate_report(1)" style="width:70px" class="formbutton" />&nbsp;
                                <input type="button" name="search" id="search" value="Job Wise" onClick="generate_report(2)" style="width:80px" class="formbutton" />
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="9" align="center"><? echo load_month_buttons(1);  ?></td>
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
