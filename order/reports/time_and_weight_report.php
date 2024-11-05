<?

/*-------------------------------------------- Comments -----------------------

Purpose			: 	This Form Will Create Time And Weight Report.

Functionality	:	

JS Functions	:

Created by		:	md mamun ahmed sagor 

Creation date 	: 	28-04-2022

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

echo load_html_head_contents("Accessories Followup Report", "../../", 1, 1,$unicode,'1','');

?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	

	function fn_report_generated(){

		if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false){
			return;
		}
		else{	

			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to*txt_style_ref*cbo_date_type',"../../");

			freeze_window(3);
			http.open("POST","requires/time_and_weight_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}

	}


	function fn_report_generated_reponse(){

		if(http.readyState == 4){
			var reponse=trim(http.responseText).split("****");
			var tot_rows=reponse[2];
			var search_by=reponse[3];
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+tot_rows+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//setFilterGrid("table_body_style",-1);

			show_msg('3');
			release_freezing();
		}

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

	



	function new_window(html_filter_print)

	{

		document.getElementById('scroll_body').style.overflow="auto";

		document.getElementById('scroll_body').style.maxHeight="none";

		

		if(html_filter_print*1>1) $("#table_body tr:first").hide();

		

		var w = window.open("Surprise", "#");

		var d = w.document.open();

		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+

	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');

		d.close();

		

		document.getElementById('scroll_body').style.overflowY="scroll";

		document.getElementById('scroll_body').style.maxHeight="400px";

		

		if(html_filter_print*1>1) $("#table_body tr:first").show();

	}	

	

	

	function generate_report(company,job_no,type)

	{

		var data="action="+type+"&txt_job_no='"+job_no+"'&cbo_company_name="+company;

		http.open("POST","../woven_order/requires/pre_cost_entry_controller.php",true);

		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");

		http.send(data);

		http.onreadystatechange = fnc_generate_report_reponse;

	}

	

	function fnc_generate_report_reponse()

	{

		if(http.readyState == 4) 

		{

			var w = window.open("Surprise", "#");

			var d = w.document.open();

			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+

	'<html><body>'+http.responseText+'</body</html>');

			d.close();

		}

	}

	

	function openmypage(po_id,item_name,action)

	{

		var popup_width='600px';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/time_and_weight_report_controller.php?po_id='+po_id+'&item_name='+item_name+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');

	}

	

</script>



</head>



<body onLoad="set_hotkey();">

<form id="accessoriesFollowup_report">

    <div style="width:100%;" align="center">

        <? echo load_freeze_divs ("../../",''); ?>

        <h3 align="left" id="accordion_h1" style="width:800px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>

        <div id="content_search_panel" > 

            <fieldset style="width:800px;">

                <table class="rpt_table" width="800" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">

                	<thead>

                   		<tr>          
                            <th>Company Name</th>
                            <th>Buyer Name</th>						
                            <th>Style Ref.</th>		
							<th>Type</th>   				
                            <th>Date</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                        </tr>

                     </thead>

                    <tbody>

                    <tr class="general">

                        <td><? echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/time_and_weight_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );?>
					    </td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );?></td>
                       
                        <td align="center"><input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:120px" placeholder="Styel Ref" ></td>
                        <td align="center"><? 
							$date_type_arr = array(1=>"Insert Date",2=>"Sample Date");
							echo create_drop_down( "cbo_date_type", 100, $date_type_arr,"",0, "", "",'',0 );//search_by(this.value)
                         ?>
                        </td>
						<td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" >&nbsp; To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" ></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated()" /></td>

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

    </div>

    

    <div id="report_container" align="center"></div>

    <div id="report_container2"></div>

 </form>    

</body>

<script>

	/*set_multiselect('cbo_item_group','0','0','0','0');*/

</script>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>

