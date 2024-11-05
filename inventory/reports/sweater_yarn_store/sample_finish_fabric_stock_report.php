<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Sample Finish Fabric Stock Report
				
Functionality	:	
JS Functions	:
Created by		:	Nayem
Creation date 	: 	27-11-2021
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
echo load_html_head_contents("Sample Finish Fabric Stock Report","../../../", 1, 1, $unicode,1,1); 

?>	

<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function openmypage_style_no()
	{
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		var data = $("#cbo_company_name").val()+"_"+$("#cbo_buyer_id").val();

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sample_finish_fabric_stock_report_controller.php?data='+data+'&action=style_no_popup', 'Style No Search', 'width=700px,height=420px,center=1,resize=0,scrolling=0','../');

		emailwindow.onclose=function()
		{
			var theemailid=this.contentDoc.getElementById("txt_style_id").value;
			var response=theemailid.split('_');
			if ( theemailid!="" )
			{
				freeze_window(5);
				$("#hidden_style_id").val(response[0]);
				$("#txt_style_no").val(response[1]);
				release_freezing();
			}
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		
		//$("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><style></style></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="350px";
		
		//$("#table_body tr:first").show();
	}

	function generate_report(type)
	{
		if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*WO Date*WO Date')==false )
		{
			return;
		}

		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_buyer = $("#cbo_buyer_id").val();
		var txt_style_no = $("#txt_style_no").val();
		var from_date 	= $("#txt_date_from").val();
		var to_date 	= $("#txt_date_to").val();

		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_buyer="+cbo_buyer+"&txt_style_no="+txt_style_no+"&from_date="+from_date+"&to_date="+to_date+"&rpt_type="+type;
		//alert(dataString);
		var data="action=generate_report"+dataString;
		freeze_window(3);
		http.open("POST","requires/sample_finish_fabric_stock_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}

	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
			//alert(reponse);
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			if(reponse[2]==1)
			{
				setFilterGrid("scroll_body",-1);
			}
			show_msg('3');
			release_freezing();
		}
	} 
 
	function fn_photo_view(photo_location)
	{ 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sample_finish_fabric_stock_report_controller.php?photo_location='+photo_location+'&action=photo_view'+'&permission='+permission, "Photo View", 'width=460px,height=300px,center=1,resize=1,scrolling=0','../')
	}

	function openmypage(rec_id,prod_id,action)
	{ //alert(des_prod)
		var companyID = $("#cbo_company_name").val();
		var popup_width='1100px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sample_finish_fabric_stock_report_controller.php?companyID='+companyID+'&rec_id='+rec_id+'&action='+action+'&prod_id='+prod_id, 'Details Veiw', 'width='+popup_width+', height=250px,center=1,resize=0,scrolling=0','../../');
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#scroll_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#scroll_body tr:first').show();
		//document.getElementById('scroll_body').style.maxWidth="120px";
	}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../../", $permission);  ?>    		 
    <form name="stylewise_yarn_stock_ledger_1" id="stylewise_yarn_stock_ledger_1" autocomplete="off" > 
    <div style="width:100%;" align="center">
        <h3 style="width:850px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
        <div style="width:100%;" id="content_search_panel">
            <fieldset style="width:850px;">
                <table class="rpt_table" width="850" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th class="must_entry_caption" >Company</th> 
                            <th>Buyer</th>                               
                            <th>Brand</th>                               
                            <th>Merch/Style Ref</th>
                            <th class="must_entry_caption" colspan="2">WO Date</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
							<? 
                               echo create_drop_down( "cbo_company_name", 110, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/sample_finish_fabric_stock_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>                            
                        </td>
                        <td id="buyer_td"> 
							<? 
								echo create_drop_down( "cbo_buyer_id", 100, $blank_array,"", 1, "--Select Buyer--", 0, "" );
                            ?>
                        </td>
                        <td>
                             <input type="text" name="txt_brand" id="txt_brand" class="text_boxes" placeholder="Write" />
                        </td>
                        <td>
                             <input type="text" name="txt_style_no" id="txt_style_no" class="text_boxes" placeholder="Write/Browse" onDblClick="openmypage_style_no()"/>
                             <input type="hidden" name="hidden_style_id" id="hidden_style_id" />
                        </td>     
                        <td align="center">
                             <input type="text" name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:55px" placeholder="From Date" readonly/>
                          </td>
                        <td align="center">
                            
                             <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To Date" readonly/>
                        </td>
                        <td >
                            <input type="button" name="search" id="search1" value="Show" onClick="generate_report(1)" style="width:60px;" class="formbutton" />
                        </td>
                    </tr>	
					<tr>
						<td colspan="7" align="center">
							<? echo load_month_buttons(1); ?> 
						</td>
					</tr>				
                    
                </table> 
            </fieldset> 
		</div>
    </div>
    <br /> 
        <!-- Result Contain Start-->
         
        	<div id="report_container" align="center"></div>
            <div id="report_container2" style="margin-left:5px"></div> 
        
        <!-- Result Contain END-->
    
    
    </form>    
</div>   
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
<script>
	setFilterGrid('rpt_tablelist_view',-1);
</script>
</body> 


</html>
