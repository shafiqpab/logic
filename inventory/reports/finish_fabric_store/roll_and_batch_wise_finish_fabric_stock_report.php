<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Roll and Batch Wise Finich Fabric Stock Report

Functionality	:
JS Functions	:
Created by		: 
Creation date 	: 07/12/2023
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
		var data="action=report_generate"+ get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_buyer_id*cbo_year*txt_job_no*txt_book_no*txt_book_id*txt_batch_no*txt_batch_id*cbo_store_id*txt_date_from*txt_date_to*txt_date_from_booking*txt_date_to_booking*txt_date_from_receive*txt_date_to_receive*txt_product_id*txt_roll_no',"../../../")+'&report_title='+report_title+'&type='+type;
		//alert (data);
		freeze_window(3);
		http.open("POST","requires/roll_and_batch_wise_finish_fabric_stock_report_controller.php",true);
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
			//setFilterGrid("table_body",-1);
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	    '<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
	}

	function openmypage_job()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year").val();
		var page_link='requires/roll_and_batch_wise_finish_fabric_stock_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;

			$('#txt_job_no').val(job_no);
			$('#hide_job_id').val(job_id);
		}
	}

	function openmypage_booking()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('txt_job_no').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/roll_and_batch_wise_finish_fabric_stock_report_controller.php?action=booking_no_popup&data='+data,'Booking No Popup', 'width=1050px,height=420px,center=1,resize=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var booking_no=this.contentDoc.getElementById("selected_booking_no");
			var booking_id=this.contentDoc.getElementById("selected_booking_id");
			$('#txt_book_no').val(booking_no.value);
			$('#txt_book_id').val(booking_id.value);			
		}
	}
	function openPopupBatch()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year_selection").val();
		var txt_job_no = $("#txt_job_no").val();
		var txt_book_no = $("#txt_book_no").val();
		var txt_book_id = $("#txt_book_id").val();
		
		var page_link='requires/roll_and_batch_wise_finish_fabric_stock_report_controller.php?action=batch_popup&companyID='+ companyID +'&buyer_name='+ buyer_name+'&txt_job_no='+ txt_job_no+'&txt_book_no='+ txt_book_no+'&txt_book_id='+ txt_book_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Batch Search', 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var batch_no=this.contentDoc.getElementById("hide_batch_no").value;
			var batch_id=this.contentDoc.getElementById("hide_batch_id").value;
			$('#txt_batch_no').val(batch_no);
			$('#txt_batch_id').val(batch_id);
		}
	}
	function openmypage_item()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_id").val();	
		var page_link='requires/roll_and_batch_wise_finish_fabric_stock_report_controller.php?action=item_description_search&company='+company; 
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			var prodNo=this.contentDoc.getElementById("txt_selected_no").value; // product Serial No
			$("#txt_product").val(prodDescription);
			$("#txt_product_id").val(prodID);
		}
	}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>
    <form name="batchwisefinishfabricstock_1" id="batchwisefinishfabricstock_1" autocomplete="off" >
    <h3 style="width:1480px; margin-top:20px;" align="" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,
    'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:1480px;">
                <table class="rpt_table" width="1480" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th width="120" class="must_entry_caption">Company</th>
                            <th width="100">Location</th>
                            <th width="90">Buyer</th>
                            <th width="70">Year</th>
                            <th width="90">Job No</th>
                            <th width="90">Booking No</th>
                            <th width="90">Batch No</th>
                            <th width="125">Fabric Des.</th>
                            <th width="70">Roll no</th>
                            <th width="100">Store</th>
                            <th width="150">Batch Date</th>
                            <th width="150">Booking Date</th>
                            <th width="150">Receive Date</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton"  onClick="reset_form('batchwisefinishfabricstock_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr align="center" class="general">
                        <td>
                            <?
                               echo create_drop_down( "cbo_company_id", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/roll_and_batch_wise_finish_fabric_stock_report_controller',this.value, 'load_drop_down_location', 'location_td'); load_drop_down('requires/roll_and_batch_wise_finish_fabric_stock_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td'); load_drop_down( 'requires/roll_and_batch_wise_finish_fabric_stock_report_controller',this.value, 'load_drop_down_store', 'store_td' );" );
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
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:70px" onDblClick="openmypage_job();" placeholder="Browse" readonly/>
                            <input type="hidden" id="txt_job_id" name="txt_job_id" class="text_boxes" style="width:60px" />
                        </td>
                        <td>
                            <input type="text" id="txt_book_no" name="txt_book_no" class="text_boxes" style="width:70px" onDblClick="openmypage_booking();" placeholder="Write/Browse" />
                            <input type="hidden" id="txt_book_id" name="txt_book_id" class="text_boxes" style="width:60px" />
                        </td>
						<td>
                            <input type="text" id="txt_batch_no" name="txt_batch_no" class="text_boxes" style="width:100px" onDblClick="openPopupBatch()" placeholder="Browse/Write" />
                            <input type="hidden" id="txt_batch_id" name="txt_batch_id"/>
                        </td>
						<td align="center">
							<input type="text" style="width:125px;"  name="txt_product" id="txt_product"  ondblclick="openmypage_item()"  class="text_boxes" placeholder="Dubble Click For Item"  readonly />   
							<input type="hidden" name="txt_product_id" id="txt_product_id"/> 
						</td>
						<td>
                            <input type="text" id="txt_roll_no" name="txt_roll_no" class="text_boxes" style="width:70px" placeholder="Write" />
                        </td>
                        <td id="store_td">
                        <?
                            echo create_drop_down( "cbo_store_id", 90, $blank_array,"",1, "--Select Store--", 1, "" );
						?>
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
                            <input type="text" name="txt_date_from_receive" id="txt_date_from_receive" value="<?// echo date("d-m-Y", time());?>" class="datepicker" style="width:50px;" readonly placeholder="From Date"/>
                            <input type="text" name="txt_date_to_receive" id="txt_date_to_receive" value="<?// echo date("d-m-Y", time());?>" class="datepicker" style="width:50px;" readonly  placeholder="To Date"/>
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
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
