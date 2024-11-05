<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Floor wise Daily RMG Production Report.
Functionality	:	
JS Functions	:
Created by		:	Thorat
Creation date 	: 	17-May-2022
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
echo load_html_head_contents("Piece Rate WO Report", "../../", 1, 1,$unicode,'','');
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$("#table_body tr:first").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
        '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
            d.close(); 
            document.getElementById('scroll_body').style.overflowY="scroll";
            document.getElementById('scroll_body').style.maxHeight="300px";
            $("#table_body tr:first").show();
	}
    function open_job_no()
	{
		var company_id=$("#cbo_company_id").val();
        //alert(company_id); 
	
	    var page_link='requires/piece_rate_wo_report_controller.php?action=job_popup&company_id='+company_id;
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var job_data=this.contentDoc.getElementById("selected_id").value;
			//alert(job_data); // Jov ID
			var job_data=job_data.split("_");
			var job_hidden_id=job_data[0];
			var job_no=job_data[1];
			//var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#txt_job_no").val(job_no);
			$("#hidden_job_id").val(job_hidden_id); 
			//alert($("#hidden_job_id").val())
		}
	}
    function open_order_no()
	{
		var job_no=$("#txt_job_no").val();
		var job_id=$("#hidden_job_id").val();
		var order_id=$("#txt_order_no").val();
		var company_id=$("#cbo_company_id").val();

	    var page_link='requires/piece_rate_wo_report_controller.php?action=order_wise_search&company_id='+company_id+'&job_id='+job_id+'&job_no='+job_no
		//alert(page_link);return; 
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=580px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]; 
				var prodID=this.contentDoc.getElementById("txt_selected_id").value;
				//alert(prodID); // product ID
				var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
				$("#txt_order_no").val(prodDescription);
				$("#hidden_order_id").val(prodID); 
			}
	} 
    function open_wo_no()
	{
		var job_no=$("#txt_job_no").val();
		var job_id=$("#hidden_job_id").val();
		var company_id=$("#cbo_company_id").val();

	    var page_link='requires/piece_rate_wo_report_controller.php?action=work_order_wise_search&company_id='+company_id+'&job_id='+job_id+'&job_no='+job_no
		//alert(page_link);return; 
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=580px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]; 
				var prodID=this.contentDoc.getElementById("txt_selected_id").value;
				//alert(prodID); // product ID
				var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
				$("#txt_wo_no").val(prodDescription);
				$("#hidden_txt_wo_id").val(prodID); 
			}
	} 
    function fn_report_generated(type)
    {
		if (type==1) 
        {
                if (form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false)
            {
                return;
            }
            else
            {
                var data="action=report_generate&type="+type+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_service_id*cbo_buyer_name*txt_job_no*hidden_job_id*txt_order_no*hidden_order_id*txt_wo_no*hidden_txt_wo_id*cbo_rate_for*txt_date_from*txt_date_to',"../../");

                freeze_window(3);
                http.open("POST","requires/piece_rate_wo_report_controller.php",true);
                http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                http.send(data);
                http.onreadystatechange = fn_report_generated_reponse;
            }
        }
    }
    function fn_report_generated_reponse()
	{	
		if(http.readyState == 4) 
		{
			//alert (http.responseText);
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[2]+'" style="text-decoration:none"><input type="button" value="Excel Preview (Summary)" name="excel" id="excel" class="formbutton" style="width:165px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview (Summary)" name="Print" class="formbutton" style="width:165px"/>&nbsp;&nbsp;<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(2)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            
             setFilterGrid("table_body",-1);
			show_msg('3');
			release_freezing();
		}
        
	} 
</script>
</head>
<body onLoad="set_hotkey();">
<form id="partyStatementReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:100%; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel','')">-Search Panel</h3>
         <div id="content_search_panel" >      
         <fieldset style="width:100%;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th width="140" class="must_entry_caption">Company</th>
                    <th width="120" >Location</th>
                    <th width="140" >Service Provider</th>
                    <th width="120">Buyer Name</th>
                    <th width="60">Job No</th>
                    <th width="60">Order No</th>
                    <th width="60">WO NO</th>
                    <th width="120">WO FOR</th>

                    <th width="120" class="must_entry_caption" colspan="2">Date Range</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                </thead>
                <tbody>
                    <tr class="general" >
                        <td>
                            <? echo create_drop_down( "cbo_company_id", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/piece_rate_wo_report_controller', this.value, 'load_drop_down_location', 'location_td' );" ); ?>
                        </td>
                        <td id="location_td">
                        <? echo create_drop_down( "cbo_location_id", 120, $blank_array,"", 1, "-Select Location-", $selected, "",0,"" ); 
                        ?>
                        </td>
                        <td>
                             <? 
                             //echo create_drop_down( "cbo_service_id", 120, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by  buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );

                             $sql="SELECT a.id,a.supplier_name FROM lib_supplier a,lib_supplier_party_type b,lib_supplier_tag_company c WHERE a.id=b.supplier_id and a.id=c.supplier_id and b.party_type  in(22,36) order by supplier_name";

                            /// echo $sql;

                             echo create_drop_down( "cbo_service_id", 160, $sql,"id,supplier_name", 1, "-- Select Location --", $selected, "","","","","","",3 );  
                             
                             ?>
                        </td>
                        <td align="center">
                            <? 
                            echo create_drop_down( "cbo_buyer_name", 120, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by  buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_job_no"  name="txt_job_no"  style="width:60px" class="text_boxes" onDblClick="open_job_no()" placeholder="Browse" readonly />
                            <input type="hidden" id="hidden_job_id"  name="hidden_job_id" />
                        </td>
                        <td>
                            <input type="text" id="txt_order_no"  name="txt_order_no"  style="width:60px" class="text_boxes" onDblClick="open_order_no()" placeholder="Browse" readonly />
                            <input type="hidden" id="hidden_order_id"  name="hidden_order_id" />
                        </td>
                        <td>
                            <input type="text" id="txt_wo_no"  name="txt_wo_no"  style="width:60px" class="text_boxes" onDblClick="open_wo_no()" placeholder="Browse" readonly />
                            <input type="hidden" id="hidden_txt_wo_id"  name="hidden_txt_wo_id" />
                        </td>
                        <td>
                           <?
							echo create_drop_down("cbo_rate_for", 120, $rate_for,"", 1,"-- Select --", 0,"","","20,30,35,40");
                        ?>
                        </td>

                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date" >
                        </td>
                            <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To Date" >
                        </td>
                            <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1);" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="11" align="center"><? echo load_month_buttons(1); ?></td>
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
