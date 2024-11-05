<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Style Closing Report V2.
Functionality	:	
JS Functions	:
Created by		:	
Creation date 	: 	15-05-2021
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
echo load_html_head_contents("Status Report Style Wise","../../../", 1, 1, $unicode,1,1);
?>

<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	var permission = '<? echo $permission; ?>';

    function fn_report_generated(type)
	{
        var txt_job_no = $("#txt_job_no").val();
		var cbo_ship_status = $("#cbo_ship_status").val();
		var txt_ref_no = $("#txt_ref_no").val();
		var txt_style = $("#txt_style").val();
		var txt_conv_rate = $("#txt_conv_rate").val();
        
        if(txt_conv_rate=="")
		{
			if(form_validation('txt_conv_rate','Conversion Rate')==false)
			{
				return;
			}
		}
        if(txt_job_no!="" || txt_ref_no!="" || txt_style!="")
		{
			if(form_validation('cbo_company_name','Company Name')==false)
			{
				return;
			}
		}
        else
		{				
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Search Type*Shipment Form Date*Shipment To Date')==false)
			{
				return;
			}
		}
        var report_title=$( "div.form_caption" ).html();	
		if(type==1)
		{
			var action="action=report_generate";
		}

        var data=action+get_submitted_data_string('cbo_company_name*txt_conv_rate*txt_job_no*txt_date_from*txt_date_to*cbo_ship_status*txt_ref_no*txt_style',"../../../")+'&report_title='+report_title+'&type='+type;
        //alert(data);return;
        freeze_window(3);
        http.open("POST","requires/status_report_style_wise_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;

        function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			//alert(reponse[0]);
			//console.log(reponse);
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+reponse[3]+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//alert(reponse[2]+'='+reponse[3]);
			//setFilterGrid("table_body2",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}	
    }

</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
        <form id="orderStatusReport" name="orderStatusReport">
            <? echo load_freeze_divs ("../../../",$permission);  ?>
            <h3 align="left" id="accordion_h1" style="width:855px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:855px;">
            <table class="rpt_table" width="855" cellpadding="0" cellspacing="0" align="center" rules="all">
                    <thead>
                        <th class="must_entry_caption" width="130">Company</th>
                        <th  width="60">Internal Ref.</th>
                        <th  width="100">Job No</th>
                        <th  width="100">Style Ref.</th>
                        <th  width="120">Shipping Status</th>
                        <th width="160" class="must_entry_caption">Pub. Shipment Date</th>
                        <th width="100" class="must_entry_caption">Conversion Rate</th>
                        <th width="50"><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:55px" onClick="reset_form('orderStatusReport','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tr class="general">                   
                        <td> 
							<?
                            echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_ref_no" name="txt_ref_no" class="text_boxes" style="width:60px"  placeholder="Write"  />                           
                        </td>
                        <td align="center">
                            <input style="width:100px;" name="txt_job_no" id="txt_job_no" class="text_boxes" placeholder="Write"/>           
                        </td>
                        <td>
                            <input type="text" id="txt_style" name="txt_style" class="text_boxes" style="width:80px"  placeholder="Write"  />                           
                        </td> 
                        <td align="center">
				            <?
				            echo create_drop_down( "cbo_ship_status", 120, $shipment_status,"",0, "", 0,'',0 );?>
				        </td>

                        <td>
                            <input type="text" id="txt_date_from" name="txt_date_from" class="datepicker" style="width:60px" readonly>To
                            <input type="text" id="txt_date_to" name="txt_date_to" class="datepicker" style="width:60px" readonly>
                        </td> 
                        <td>
                        	<input type="text" id="txt_conv_rate" name="txt_conv_rate" class="text_boxes" style="width:80px"  placeholder="Write" value="80" />     
                        </td>
                        <td>
                        	<input type="button" id="show_button" class="formbutton" style="width:55px" value="Show" onClick="fn_report_generated(1)" />
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="10" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                    </tr>
            </table>
            </fieldset>
            </div>
        </form>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div> 
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>