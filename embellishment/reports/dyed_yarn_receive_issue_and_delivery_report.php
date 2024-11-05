<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Date Wise Embroidery Bill Status Report.
Functionality	:	
JS Functions	:
Created by		:	Sakib Ahamed
Creation date 	: 	20-12-2023
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
echo load_html_head_contents("Dyed Yarn Receive Issue and Delivery Report", "../../", 1, 1,$unicode,1,1);
?>	
</head>
<script>
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
    function search_by(val)
    {
        $('#txt_search_string').val('');
        if(val==1 || val==0) $('#search_by_td').html('YD Job No');
        else if(val==2) $('#search_by_td').html('W/O No');
        else if(val==3) $('#search_by_td').html('Buyer Style');
        else if(val==4) $('#search_by_td').html('Buyer Job');
    }
    function fn_report_generated(operation)
	{
		if (form_validation('cbo_company_name*cbo_within_group','Company Name*Within Group')==false)
		{
			return;
		}

		var report_title=$( "div.form_caption" ).html();
		
		var data="action=report_generate_"+operation+get_submitted_data_string('cbo_company_name*cbo_within_group*cbo_party_name*search_by*txt_search_string*cbo_pro_type*cbo_order_type*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;			
		
		freeze_window(3);
		http.open("POST","requires/dyed_yarn_receive_issue_and_delivery_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function fn_report_generated_reponse()
		{			
			if(http.readyState == 4) 
			{   
				show_msg('3');
				var reponse=trim(http.responseText).split("**"); 
				$('#report_container2').html(reponse[0]);
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				setFilterGrid("table_body",-1);
				release_freezing();
			}
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
		'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="all"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		
        document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="350px";
		$("#table_body tr:first").show();
	}

    function yd_print_report(update_id,cbo_company_name,cbo_within_group)
    {   
        var action = 'yarn_dyeing_order_entry_print';
        var data  = cbo_company_name+'*'+update_id+'*'+cbo_within_group;
        window.open("../../yarn_dyeing/order_material/requires/yd_order_entry_controller.php?data=" + data+'&action='+action, true );
    }
</script>
<body onLoad="set_hotkey();">
    <? echo load_freeze_divs ("../../",'');  ?>
    
    <div align="center">
        <h3 style="width:950px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <form name="searchorderfrm_<?php echo $tblRow;?>" id="searchorderfrm_<?php echo $tblRow;?>" autocomplete="off">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" style="width: 950px;">
                <thead>
                    
                    <tr>
                        <th width="120" class="must_entry_caption" >Company Name</th>
                        <th width="80" class="must_entry_caption" >Within Group</th>
                        <th width="120">Party Name</th>
                        <th width="80">Search By</th>
                        <th width="80" id="search_by_td">YD Job No</th>
                        <th width="70">Prod. Type</th>
                        <th width="70">Order Type</th>
                        <th width="160">Date Range</th>
                        <th width="160">
                            <input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width: 70px" />
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="general">
                        <td>
                            <?php echo create_drop_down('cbo_company_name', 120, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", 'id,company_name', 1, '-- Select Company --', $cbo_company_name, "load_drop_down( 'requires/dyed_yarn_receive_issue_and_delivery_report_controller', this.value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_party', 'party_td' );",0); ?>
                        </td>
                        <td> 
                            <?php echo create_drop_down('cbo_within_group', 80, $yes_no, '', 1, '-- Select Within Group --', $cbo_within_group, "load_drop_down( 'requires/dyed_yarn_receive_issue_and_delivery_report_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_party', 'party_td' );",0); ?>
                        </td>
                        <td id="party_td"> 
                            <?php 

                                if($cbo_within_group==1 && $cbo_company_name!=0)
                                {

                                    echo create_drop_down( "cbo_party_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "-- Select Party --", $cbo_party_name, "",0);
                                }
                                elseif($cbo_within_group==2 && $cbo_company_name!=0)
                                {
                                    echo create_drop_down( "cbo_party_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_name' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",$cbo_party_name, "",0 );
                                }
                                else
                                {
                                    echo create_drop_down('cbo_party_name', 120, $blank_array, '', 1, '-- Select Party --', $selected, "",0);
                                }

                            ?>
                        </td>
                        <td>
                            <?
                                $search_by_arr=array(1=>"YD Job No",2=>"W/O No",3=>"Buyer Style",4=>"Buyer Job");
                                echo create_drop_down( "search_by",80, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                            ?>
                        </td>
                        <td align="center">
                            <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:80px" placeholder="Write" />
                        </td>
                        <td>
                            <? echo create_drop_down( "cbo_pro_type",70, $w_pro_type_arr,"",1, "--Select--",$cbo_pro_type,'',0 );?>
                        </td>
                        <td>
                            <? echo create_drop_down( "cbo_order_type",70, $w_order_type_arr,"",1, "--Select--",$cbo_order_type,'',0 ); ?>
                        </td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From">
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To">
                        </td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="fn_report_generated(1);" style="width:70px;" />
                            <input type="button" name="button2" class="formbutton" value="Show 2" onClick="fn_report_generated(2);" style="width:70px;" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="10" align="center" valign="middle">
                            <? echo load_month_buttons(1);  ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div>
    <div id="report_container" align="center" style="padding: 5px;"></div>
    <div id="report_container2" align="left"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>