<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create PO received Status report
				
Functionality	:	
JS Functions	:
Created by		:	Mohammad Shafiqur Rahman Sumon
Creation date 	: 	20-08-2019
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
echo load_html_head_contents("PO received status report","../../../", 1, 1, $unicode,1,1); 

?>	

<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
    
	function generate_report(type)
	{
		var cbo_company_name         = $("#cbo_company_name").val();
		var cbo_working_company_name = $("#cbo_working_company_name").val();
		var cbo_shipment_type 	     = $("#cbo_shipment_type").val();	
		var cbo_buyer = $("#cbo_buyer").val();			
		var from_date = $("#txt_date_from").val();
		var to_date   = $("#txt_date_to").val();
		var cbo_year  = $("#cbo_year_selection").val();		

        
		if ((cbo_company_name == 0 && from_date == "") || (cbo_company_name == 0 && to_date == "")) {	
			//$("#curr_date_range").addClass('must_entry_caption');
			//alert("if block");
			if( form_validation('cbo_working_company_name*txt_date_from*txt_date_to','Working Company*From Date*To Date')==false )
			{
				return;
			}
		}
		if (cbo_company_name > 0 && from_date == "" && to_date == "") {	
			//$("#curr_date_range").addClass('must_entry_caption');
			//alert("if block");
			if( form_validation('cbo_year_selection','Year')==false )
			{
				return;
			}
		}
		if (cbo_working_company_name > 0 && $("#cbo_year_selection").val() == 0) {	
			//$("#curr_date_range").addClass('must_entry_caption');
			//alert("if block");
			if( form_validation('cbo_year_selection','Year')==false )
			{
				return;
			}
		}
		// if ($("#cbo_company_name").val() > 0 && $("#cbo_working_company_name").val() > 0) 
        // {	
			
		// 	alert("You should select either company or working company name.");
        //     return;
			
		// }
		if (cbo_company_name == 0 && cbo_working_company_name== 0) 
        {				
			if( form_validation('cbo_working_company_name','Working Company')==false )
			{
				return;
			}			
		}

        
		var dataString = "&cbo_company_name="+cbo_company_name+"&working_company_name="+cbo_working_company_name+"&cbo_buyer="+cbo_buyer+"&cbo_year="+cbo_year+"&cbo_shipment_type="+cbo_shipment_type+"&from_date="+from_date+"&to_date="+to_date+"&rpt_type="+type;
		//alert(dataString);return;
        if(type==1)
        {
		    var data="action=generate_report"+dataString;            
        }else if(type==2) {
		    var data="action=inter_company_export_proceed"+dataString;    
        }

		freeze_window(3);
		http.open("POST","requires/po_received_status_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}

	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
            //alert(http.responseText);
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

			
			//setFilterGrid("scroll_body",tableFilters,-1);,tableFilters
			setFilterGrid("scroll_body",-1);
			
			show_msg('3');
			release_freezing();
		}
	} 

    function fn_check_company() {
        
        if($("#cbo_company_name").val() > 0)
        {
            $("#cbo_working_company_name").attr("disabled",true);
        }else{
            $("#cbo_company_name").attr("disabled",true);
        }
    }

	function openmypage(rec_id,prod_id,action)
	{ //alert(des_prod)
		var companyID = $("#cbo_company_name").val();
		var popup_width='1100px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/stylewise_yarn_stock_report_controller.php?companyID='+companyID+'&rec_id='+rec_id+'&action='+action+'&prod_id='+prod_id, 'Details Veiw', 'width='+popup_width+', height=250px,center=1,resize=0,scrolling=0','../../');
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
        <form name="po_received_status_report_1" id="po_received_status_report_1" autocomplete="off" > 
            <div style="width:100%;" align="center">
                <h3 style="width:1050px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
                <div style="width:100%;" id="content_search_panel">
                    <fieldset style="width:950px;">
                        <table class="rpt_table" width="1050" cellpadding="0" cellspacing="0" border="1" rules="all">
                            <thead>
                                <tr> 	 	
                                    <th>Company</th> 
                                    <th>Working Company</th>
                                    <th>Buyer</th>                               
                                    <th>Type</th>                              
                                    <th class="must_entry_caption" colspan="2">Date Range</th>                              
                                    <th><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton"/></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="general">
                                    <td>
                                        <? 
                                        echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- All Company --", $selected, "load_drop_down( 'requires/po_received_status_report_controller', this.value, 'load_drop_down_buyer', 'buyer' );" );
                                        ?>                            
                                    </td>
                                    <td>
                                        <? 
                                            echo create_drop_down( "cbo_working_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- All Company --", $selected, "load_drop_down( 'requires/po_received_status_report_controller', this.value, 'load_drop_down_buyer', 'buyer' );" );
                                        ?>
                                    </td>
                                    <td id="buyer"> 
                                        <? 
                                            echo create_drop_down( "cbo_buyer", 130, $blank_array,"", 1, "--All Buyer--", $selected, "",0,"" );
                                        ?>
                                    </td>
                                    <td>
                                        <? 
                                        	$shipment_type_arr=array(1=>"Pub Ship Date", 2=>"PO Receive Date");
                                            echo create_drop_down( "cbo_shipment_type", 120, $shipment_type_arr,"", "", "", 1, "", 0, "" );
                                        ?>
                                    </td>
                                    <td>
                                        <input name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:90px" placeholder="From Date">
                                    </td>
                                    <td>
                                        <input name="txt_date_to" id="txt_date_to"  class="datepicker" style="width:90px" placeholder="To Date">
                                    </td>
                                    <td colspan="2">
                                        <input type="button" name="search" id="search1" value="PO Receive Status Report" onClick="generate_report(1)" style="" class="formbutton"/> 
                                        
                                    </td>
                                </tr>	
                                <tr>
                                    <td colspan="6" align="center"><? echo load_month_buttons(1); ?></td>
                                    <td align="center">
                                    <input type="button" name="search" id="search1" value="Inter Company Export Proceed" onClick="generate_report(2)" style="" class="formbutton" />
                                    </td>
                                </tr>
                            </tbody>                            
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
