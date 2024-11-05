<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Cut Off Date Wise Production Followup Report.
Functionality	:	
JS Functions	:
Created by		:	Abu Sayed 
Creation date 	: 	24-03-2021
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
echo load_html_head_contents("Cut Off Date Wise Production Followup Report", "../../", 1, 1,$unicode,1,1);

?>	

<script>
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';



    var tableFilters = 
	{
		
		
        col_operation: { 
			id: ["total_color_wise_qty","total_fabric_required","total_fabric_rcv","total_fabric_balance","total_cut","total_cut_balance","total_print_sent","total_print_rcvd","total_print_balance","total_emb_sent","total_emb_rcvd","total_emb_balance","total_swe_in","total_swe_out","total_swe_balance","total_iron","total_iron_balance","total_finish","total_finish_balance","total_ex_factory","total_exf_balance"],
			col: [10,15,16,17,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
 	}	

	function fn_report_generated()
	{   
		if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Start Date*End Date')==false )
		{
			return;
		}
	
		var report_title=$( "div.form_caption" ).html(); 
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*hidden_job_id*txt_style_no*hidden_style_id*txt_po_no*hidden_po_id*cbo_date_type*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/cut_off_date_wise_production_followup_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####"); 
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            // setFilterGrid("table_body",-1,tableFilters);
			release_freezing();
		}
	}

	 
	 
    function new_window()
    {
        document.getElementById('scroll_body').style.overflow="auto";
        document.getElementById('scroll_body').style.maxHeight="none"; 
        $(".flt").css('display','none');
           
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
    '<html><head><link rel="stylesheet" href="../../css/style_print.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
        d.close(); 
        
        document.getElementById('scroll_body').style.overflowY="auto"; 
        document.getElementById('scroll_body').style.maxHeight="400px";
        $(".flt").css('display','block');
      } 

      function openmypage_fabric_recive(fab_consumption_per_dzan,booking_no,order_qty,cutup_date,job,color_id,po_Id,action)
	{
		var popup_width='700px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/cut_off_date_wise_production_followup_report_controller.php?fab_consumption_per_dzan='+fab_consumption_per_dzan+'&booking_no='+booking_no+'&order_qty='+order_qty+'&cutup_date='+cutup_date+'&job='+job+'&color_id='+color_id+'&poId='+po_Id+'&action='+action, 'Cut off Date Wise Fabric Recv Qty', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../');
	}

</script>

</head>
 
<body onLoad="set_hotkey();">

<form id="lineWiseProductionReport_1">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../",'');  ?>
               
         <fieldset style="width:1030px;">
            <legend>Search Panel</legend>
            <table class="rpt_table" width="1030px" cellpadding="0" cellspacing="0" border="1" align="center">
               <thead>                    
                    <tr>
                        <th width="150" class=""> Company </th>
                        <th width="150" class="">Buyer</th>
                        <th width="80">Job NO</th>
                        <th width="80">Style</th>
                        <th width="80">Order</th> 
                        <th class="must_entry_caption">Date Type</th>
                        <th colspan="2" id="date_type" class="must_entry_caption">Cut Off Date </th> 
                        <th width="100"><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                    </tr>    
                 </thead>
                <tbody>
                <tr >
                    <td width="150"> 
                        <?
                            echo create_drop_down( "cbo_company_name", 150, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/cut_off_date_wise_production_followup_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );set_multiselect('cbo_buyer_name','0','0','','0');" );
                        ?>
                    </td> 
                    <td align="center" id="buyer_td">
                        <? 
                        echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select --", 0, "",0 );
                        ?>
                    </td>   
                    <td>
                        <input style="width:120px;"  name="txt_job_no" id="txt_job_no"  onDblClick="__open_search_by(1)"  class="text_boxes" placeholder="write"  />   
                        <input type="hidden" name="hidden_job_id" id="hidden_job_id"/>
                    </td>
                    <td>
                     <input style="width:120px" id="txt_style_no"  name="txt_style_no" onDblClick="__open_search_by(2)"  class="text_boxes"  placeholder="write"  />
                     <input type="hidden" name="hidden_style_id" id="hidden_style_id"/>
                    </td>
                    
                    <td>
                        <input style="width:120px;"  name="txt_po_no" id="txt_po_no"  onDblClick="__open_search_by(3)"  class="text_boxes" placeholder="write"   />   
                        <input type="hidden" name="hidden_po_id" id="hidden_po_id"/>
                    </td>
                    <td> 
                        <?
                            $typeArr=array(1=>'Cut Off Date',2=>'Ship Date');
                            echo create_drop_down( "cbo_date_type", 120, $typeArr,"", 0, "-- Select --", $selected, "$('#date_type').text($('#cbo_date_type :selected').text());" );
                        ?>
                    </td>
                    
                    <td>
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" >
                    </td>  
                    <td>
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"  placeholder="To Date"  >
                    </td>                
                    <td width="100">
                        <input type="button" id="show_button" class="formbutton" style="width:90px" value="Show" onClick="fn_report_generated(0)" />
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
    
    <div id="report_container" align="center" style="padding: 10px;"></div>
    <div id="report_container2"></div>
 </form>    
</body>
<script>
    set_multiselect('cbo_buyer_name','0','0','','0');
</script> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

