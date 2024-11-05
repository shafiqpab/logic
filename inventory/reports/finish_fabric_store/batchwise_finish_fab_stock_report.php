<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Batch Wise Finich Fabric Stock Report

Functionality	:
JS Functions	:
Created by		: Mohammad Shafiqur Rahman
Creation date 	: 5/6/2019
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
echo load_html_head_contents("Batch Wise Finich Fabric Stock Report","../../../", 1, 1, $unicode,1,1);
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";


	function generate_report(type)
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+ get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_buyer_id*cbo_year*txt_job_no*txt_booking_no*txt_batch_no*cbo_store_id*txt_date_from*txt_date_to*txt_date_from_booking*txt_date_to_booking*cbo_filter_by',"../../../")+'&report_title='+report_title+'&type='+type;
		//alert (data);
		freeze_window(3);
		http.open("POST","requires/batchwise_finish_fab_stock_report_controller.php",true);
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
			setFilterGrid("table_body",-1);
			show_msg('3');
			release_freezing();
		}
	}

	function generate_details_report() {
        if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false )
        {
            return;
        }
        var report_title=$( "div.form_caption" ).html();
        var data="action=generate_details_report"+ get_submitted_data_string
        ('cbo_company_id*cbo_item_category*cbo_product_category*txt_date_from*txt_date_to*cbo_buyer_id*txt_job_no*txt_job_no_show*txt_style_no*cbo_year_selection',"../../../")+'&report_title='+report_title;
        //alert (data);
        freeze_window(3);
        http.open("POST","requires/buyer_wise_finish_fabric_received_issued_stock_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = generate_details_report_reponse;
    }

    function generate_details_report_reponse() {
        if(http.readyState == 4)
        {
            var reponse=trim(http.responseText).split("**");
            $("#report_container2").html(reponse[0]);
            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body",-1);
			setFilterGrid("table_body_id",-1);
            show_msg('3');
            release_freezing();
        }
    }

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tr:first').hide();
		$('#table_body_id tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	    '<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		//$('#table_body tr:first').hide();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		//$('#scroll_body tr:first').show();
	}

	function openmypage_job()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year_selection").val();
		//var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/buyer_wise_finish_fabric_received_issued_stock_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;

			$('#txt_job_no_show').val(job_no);
			$('#txt_job_no').val(job_id);
		}
	}
	function openmypage_location()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			//alert("hello company");
			$("#show_textcbo_company_id").css({"background-image": "-moz-linear-gradient(center bottom, rgb(254, 151, 174) 0%, rgb(255, 255, 255) 10%, rgb(254, 151, 174) 96%)"}).focus();
			return;
		}
		var companyID = $("#cbo_company_id").val();
		//var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year").val();
		//var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/batchwise_finish_fab_stock_report_controller.php?action=location_popup&companyID='+companyID+'&cbo_year_id='+cbo_year_id;
		var title='Loction Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var locations=this.contentDoc.getElementById("txt_location").value;
			var location_ids=this.contentDoc.getElementById("hide_location_id").value;
			//alert(locations);
			$('#txt_location').val(locations);
			$('#hidden_location_id').val(location_ids);
		}
	}
	function openmypage_buyer()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			$("#show_textcbo_company_id").css({"background-image": "-moz-linear-gradient(center bottom, rgb(254, 151, 174) 0%, rgb(255, 255, 255) 10%, rgb(254, 151, 174) 96%)"}).focus();
			return;
		}
		var companyID = $("#cbo_company_id").val();
		//var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year").val();
		//var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/batchwise_finish_fab_stock_report_controller.php?action=buyer_popup&companyID='+companyID+'&cbo_year_id='+cbo_year_id;
		var title='Buyer Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var buyers=this.contentDoc.getElementById("txt_buyer").value;
			var buyer_ids=this.contentDoc.getElementById("hidden_buyer_ids").value;

			$('#txt_buyer').val(buyers);
			$('#hidden_buyer_ids').val(buyer_ids);
		}
	}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>
    <form name="batchwisefinishfabricstock_1" id="batchwisefinishfabricstock_1" autocomplete="off" >
    <h3 style="width:1330px; margin-top:20px;" align="" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,
    'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:1330px;">
                <table class="rpt_table" width="1330" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th width="120">Company</th>
                            <th width="100">Location</th>
                            <th width="90">Buyer</th>
                            <th width="70">Year</th>
                            <th width="90">Job No</th>
                            <th width="90">Booking No</th>
                            <th width="90">Batch No</th>
                            <th width="100">Store</th>
                            <th width="150">Batch Date</th>
                            <th width="150">Booking Date</th>
                            <th width="80">Filter By</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton"  onClick="reset_form('batchwisefinishfabricstock_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr align="center" class="general">
                        <td>
                            <?
                               echo create_drop_down( "cbo_company_id", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/batchwise_finish_fab_stock_report_controller',this.value, 'load_drop_down_location', 'location_td'); load_drop_down('requires/batchwise_finish_fab_stock_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td'); load_drop_down( 'requires/batchwise_finish_fab_stock_report_controller',this.value, 'load_drop_down_store', 'store_td' );" );
                            ?>
                        </td>
                        <td id="location_td">
							<?
								echo create_drop_down("cbo_location_id", 90, $blank_arra, "",1, "-Select Location-", "", "");
							?>
                        </td>
                        <td id="buyer_td">
							<?
								echo create_drop_down("cbo_buyer_id", 90, $blank_arra, "",1, "-Select Buyer-", "", "");
							?>
                        </td>
                        <td>
                            <?
                                echo create_drop_down("cbo_year", 70, create_year_array(), "", 1, "-- All --", date("Y", time()), "", 0, "");
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:90px" placeholder="Write Job No" />
                            <!-- <input type="hidden" id="txt_job_id" name="txt_job_no" /> -->
                        </td>
                         <td>
                            <input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes" style="width:90px"  placeholder="Write Booking No" />
                            <!-- <input type="hidden" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:100px" /> -->
                        </td>
                        <td>
                            <input type="text" id="txt_batch_no" name="txt_batch_no" class="text_boxes" style="width:90px"  placeholder="Write Batch No" />
                        </td>
                        <td id="store_td">
                        <?
                               echo create_drop_down( "cbo_store_id", 90, "select comp.id, comp.store_location from lib_store_location comp where comp.status_active=1 and comp.is_deleted=0 $sotre_cond order by comp.store_location","id,store_location", 1, "-Select Store-", $selected, "" );
                            ?>
                            <!-- <input type="text" id="txt_store_no" name="txt_store_no" class="text_boxes" style="width:100px"  placeholder="Browse Store" ondblclick="openmypage_store();"/> -->
                        </td>

                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" value="<? //echo date("d-m-Y", time());?>" class="datepicker" style="width:50px;" readonly placeholder="From Date"/>
                            <input type="text" name="txt_date_to" id="txt_date_to" value="<? //echo date("d-m-Y", time());?>" class="datepicker" style="width:50px;" readonly  placeholder="To Date"/>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from_booking" id="txt_date_from_booking" value="<?// echo date("d-m-Y", time());?>" class="datepicker" style="width:50px;" readonly placeholder="From Date"/>
                            <input type="text" name="txt_date_to_booking" id="txt_date_to_booking" value="<?// echo date("d-m-Y", time());?>" class="datepicker" style="width:50px;" readonly  placeholder="To Date"/>
                        </td>
                        <td>
                        <?
                            $filter_by = array( 1=>"Excess",2=>"Short",3=>"All" );
                            echo create_drop_down("cbo_filter_by", 80, $filter_by, "", 1, "-- Select --", date("Y", time()), "", 0, "");
                        ?>
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                            <input type="button" name="report" id="report" value="Report" onClick="generate_report(2)" style="width:70px"
                                   class="formbutton" />
                        </td>
                    </tr>
                    </tbody>
                    <tfoot>
                    	<tr>
                    		<td colspan="9" align="center">
                    			<?  echo load_month_buttons(1); ?>
                    		</td>
                    	</tr>
                    </tfoot>
                </table>
            </fieldset>
        </div>

    </form>
</div>
	<div id="report_container" align="center"></div>
    <div id="report_container2"></div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	//set_multiselect('cbo_company_id','0','0','','0');
	//set_multiselect('cbo_store_no','0','0','','0');
</script>
</html>
