<?

/*-------------------------------------------- Comments -----------------------

Purpose			: 	This Form Will Create Accessories Followup Report.

Functionality	:	

JS Functions	:

Created by		:	
Creation date 	: 	17-04-2017
Updated by 		: 		

Update date		: 		   

QC Performed BY	:		

QC Date			:	

Comments		:

*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------

echo load_html_head_contents("Accessories Followup Report", "../", 1, 1,$unicode,'1','');

?>
<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 

	var permission = '<? echo $permission; ?>';

	function fn_report_generated(type)
	{			
		var report_title=$( "div.form_caption" ).html();	
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_location_id*txt_date_from*txt_date_to',"../")+'&report_title='+report_title+'&type='+type;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/production_summary_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_response;
	}

	function fn_report_generated_response()
	{
		if(http.readyState == 4) 
		{
        var response=trim(http.responseText).split("####");
        $('#report_container2').html(response[0]);
        document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		show_msg('3');
		release_freezing();
		}
	}
    function new_window()
    {
        
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
    '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body style="font-size:12px; font-family:Arial Narrow">'+document.getElementById('report_container2').innerHTML+'</body</html>');
        d.close();
    }

    function openmypage_popup(company_id,location,type,action,is_booking,title,is_knitting)
    {
        var from_date=$("#txt_date_from").val();
        var to_date=$("#txt_date_to").val();
         var page_link='requires/production_summary_report_controller.php?company_id='+company_id+'&location='+location+'&type='+type+'&action='+action+'&from_date='+from_date+'&to_date='+to_date+'&is_booking='+is_booking+'&is_knitting='+is_knitting;    
         emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,  title, 'width=520px,height=320px,center=1,resize=0,scrolling=0','')
       // emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=840px,height=370px,center=1,resize=0,scrolling=0','')

    }

    function openmypage_knit_popup(company_id,location,action,title,date)
    {
         var page_link='requires/production_summary_report_controller.php?company_id='+company_id+'&location='+location+'&action='+action+'&date='+date;    
         emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,  title, 'width=820px,height=420px,center=1,resize=0,scrolling=0','')
    }

    function openmypage_dyeing_popup(company_id,location,action,title,date)
    {
         var page_link='requires/production_summary_report_controller.php?company_id='+company_id+'&location='+location+'&action='+action+'&date='+date;    
         emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,  title, 'width=820px,height=420px,center=1,resize=0,scrolling=0','')
    }


    function openmypage_rmg_popup(company_id,location,action,title,date,type,emble_type,is_booking,is_knitting)
    {
         var page_link='requires/production_summary_report_controller.php?company_id='+company_id+'&location='+location+'&action='+action+'&date='+date+'&type='+type+'&emble_type='+emble_type+'&is_booking='+is_booking+'&is_knitting='+is_knitting;    
         emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,  title, 'width=820px,height=420px,center=1,resize=0,scrolling=0','')
    }

    function openmypage_emb_popup(company_id,location,action,title,date,type,emble_type,is_booking,is_knitting)
    {
         var page_link='requires/production_summary_report_controller.php?company_id='+company_id+'&location='+location+'&action='+action+'&date='+date+'&type='+type+'&emble_type='+emble_type+'&is_booking='+is_booking+'&is_knitting='+is_knitting;    
         emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,  title, 'width=820px,height=420px,center=1,resize=0,scrolling=0','')
    }

</script>

</head>
<body onLoad="set_hotkey();">

<form id="accessoriesFollowup_report">

    <div style="width:100%;" align="center">

        <? echo load_freeze_divs ("../",''); ?>

        <h3 align="left" id="accordion_h1" style="width:800px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>

        <div id="content_search_panel" > 

            <fieldset style="width:800px;">

                <table class="rpt_table" width="780" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">

                	<thead>

                   		<tr>
                            <th>Working Factory</th>
                            <th>Location</th>
                            <th>Date Range</th>

                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                        </tr>

                     </thead>

                    <tbody>
                    <tr class="general">
                       	<td> 
                           <?
								echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down('requires/production_summary_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                            ?>
                        </td>

                        <td id="location_td">
							<? 
								echo create_drop_down( "cbo_location_id", 140, $blank_array,"", 1, "-- Select --", $selected, "",1,"" );
                            ?>
                        </td>
                        <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" value="<? $date = new DateTime('now');$date->modify('first day of this month');
                        echo $date->format('d-m-Y');?>" >&nbsp; To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" 
                        value="<? $date = new DateTime('now');$date->modify('last day of this month');
                        echo $date->format('d-m-Y');?>" ></td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated('1')" />

                            <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show2" onClick="fn_report_generated('2')" />


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

    </div></br>

    

    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>

 </form>    

</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>

</html>

