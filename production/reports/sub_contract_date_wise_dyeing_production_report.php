<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Sub-Contract Date Wise Dyeing Production Report
Functionality	:	
JS Functions	:
Created by		:	MD MAMUN AHMED SAGOR
Creation date 	: 	30-10-2022
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
echo load_html_head_contents("Sub-Contract Date Wise Dyeing Production Report", "../../", 1, 1,$unicode,1,1);
?>	
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';	

	var tableFilters = 
	{
		col_30: "none",
		col_operation: {
		id: ["tot_qnty"],
		col: [6],
		operation: ["sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 
	
	function openmypage_machine()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		 var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_location_id').value+"_"+document.getElementById('cbo_floor_id').value;
		 // alert(data);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/sub_contract_date_wise_dyeing_production_report_controller.php?action=machine_no_popup&data='+data,'Machine Name Popup', 'width=470px,height=420px,center=1,resize=0','../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("hid_machine_id");
			var theemailv=this.contentDoc.getElementById("hid_machine_name");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_machine_id").value=theemail.value;
			    document.getElementById("txt_machine_name").value=theemailv.value;
				release_freezing();
			}
		}
	}
	
	function fn_report_generated(operation)
	{
		
		var txt_batch = $('#txt_batch').val();
		var txt_booking_no = $('#txt_booking_no').val();
		if(txt_batch!="" || txt_booking_no!="")
		{
			if(form_validation('cbo_company_id','Company')==false)
			{
				return;
			}
		}
		else
		{
			if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false )
			{
				return;
			}
		}
		/*if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false )
		{
			return;
		}*/
			
		var from_date = $('#txt_date_from').val();
		var to_date = $('#txt_date_to').val();
	
		
		   var report_title=$( "div.form_caption" ).html();
	
			var data="action="+operation+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_buyer_name*txt_booking_no*txt_order_no*txt_batch*hidden_color_id*txt_color_range*cbo_floor_id*txt_machine_name*txt_machine_id*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
		
	
		
		freeze_window(3);
		http.open("POST","requires/sub_contract_date_wise_dyeing_production_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;  
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			var tot_rows=reponse[2];
			//alert (reponse[2]);return;
			$('#report_container2').html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("tbl_issue_status",-1,tableFilters);
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
	
	function openmypage_idle(machine_id,date,action)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sub_contract_date_wise_dyeing_production_report_controller.php?machine_id='+machine_id+'&date='+date+'&action='+action, 'Cause of Machine Idle', 'width=600px,height=450px,center=1,resize=0,scrolling=0','../');
	}

	$(function()
		{
			$('#txt_batch_color').keyup(function() 
			{
				$('#color_suggest').show('fast');
				var color_name = $(this).val();
				$.ajax({
					url: 'requires/sub_contract_date_wise_dyeing_production_report_controller.php',
					type: 'POST',
					data: 'color_name='+color_name+'&action=color_name_suggestion',
					beforeSend:function(data)
					{
						$('#color_suggest').text('Loading...');
						//$('#color_suggest').show('.loader');
					},
					success:function(data)
					{
						$('#color_suggest').html(data);
					}
				});
			});
			$('#txt_batch_color').blur(function()
			{
			    $('#color_suggest').hide('fast');
			});
		});
	function set_color_id(id,color_name)
	{
		//alert(color_name);
		$('#txt_batch_color').val(color_name);
		$('#hidden_color_id').val(id);
		$('#color_suggest').hide('fast');		
	}

	$(function()
	{
		$('#txt_batch_color').keyup(function() 
		{
			var color = $('#txt_batch_color').val();
			if (color=="") 
			{
				//alert('ok');
				$('#hidden_color_id').val("");
			}			
		});
	});
	
	function order_type()
	{
		var order_type = $('#cbo_order_type').val();
		if (order_type==2) 
		{			
			$('#txt_booking_no').hide();
			$('#th_booking_no').hide();
			$('#txt_order_no').show();
			$('#th_order_no').show();
		}
		else
		{
			$('#txt_booking_no').show();
			$('#th_booking_no').show();
			$('#txt_order_no').hide();
			$('#th_order_no').hide();
		}
	}
	
	function openmypage_fabricBooking()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'Booking Selection Form';

			var page_link = 'requires/sub_contract_date_wise_dyeing_production_report_controller.php?cbo_company_id='+cbo_company_id+'&action=fabricBooking_popup';
			var popup_width="1070px";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=400px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var theemail=this.contentDoc.getElementById("hidden_booking_no");
				var theemailv=this.contentDoc.getElementById("hidden_booking_id");
				//var theemailv=this.contentDoc.getElementById("booking_without_order");
				var response=theemail.value.split('_');
				if (theemail.value!="")
				{
					freeze_window(5);
					document.getElementById("txt_booking_no").value=theemail.value;
				    document.getElementById("txt_booking_no_id").value=theemailv.value;
				    //document.getElementById("booking_without_order").value=theemailv.value;
					release_freezing();
				}
			}
		}	
	}
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><link rel="stylesheet" href="../../amchart/plugins/export.css" type="text/css" media="all" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="auto"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
	}
</script>
<style type="text/css">
	#color_suggest{
		display: none;
		position: absolute;
		width: 112px;
		max-height: 150px;
		border: 1px solid #72b42d;
		background: #B8C7B9 50% 50% repeat;
		overflow: hidden;
		overflow-y: scroll;
		opacity: 0.9;
	}
	#color_suggest ul{
		list-style: none;
		max-height: 150px;
	}
	#color_suggest ul li{
		padding: 2px 3px;
		font-weight: normal;
		font-size: 12px;
		cursor: pointer;
	}
	#color_suggest ul li:hover {
    background-color: yellow;
    border-radius: 7px;
    border: 1px solid #6666FF;
	background-image: -moz-linear-gradient(bottom,rgb(136,170,214) 7%,rgb(194,220,255)10%,rgb(136,170,214)96%);
	color: #ffffff;
	}
	.loader
	{
	  border: 3px solid #f3f3f3;
	  border-radius: 50%;
	  border-top:3px solid #3498db;
	  width: 10px;
	  height: 10px;
	  -webkit-animation: spin 1s linear infinite; /* Safari */
	  animation: spin 1s linear infinite;
	}
	@keyframes spin 
	{
	  0% { transform: rotate(0deg); }
	  100% { transform: rotate(360deg); }
	}
</style>
</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>    		 
        <form name="machinewiseproduction_1" id="machinewiseproduction_1" autocomplete="off" > 
         <h3 style="width:1130px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:1130px" >      
            <fieldset>  
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" align="center" rules="all" border="1">
                <thead>                    
                    <th width="100" class="must_entry_caption">Company Name</th>
                    <th width="100">Location</th>
                  
                    <th width="100">Buyer</th>
                    <th width="80"><span id="th_booking_no">Booking No.</span><span id="th_order_no" style="display: none;">Order No.</span></th>
                    <th width="80">Batch</th>
                    <th width="80">Batch Color</th>
                    <th width="80">Color Range</th>
                    <th width="100">Floor</th>
                    <th width="100">Machine Name</th>
                    <th width="" class="must_entry_caption">Production Date</th>
                </thead>
                <tbody>
                    <tr>
                        <td> 
							<?
								echo create_drop_down( "cbo_company_id", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sub_contract_date_wise_dyeing_production_report_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/sub_contract_date_wise_dyeing_production_report_controller',document.getElementById('cbo_order_type').value+'_'+this.value, 'load_drop_down_buyer', 'buyer_td' );" );
						    ?>
						</td>
						<td id="location_td">
						    <? 
						    	echo create_drop_down( "cbo_location_id", 100, $blank_array,"", 1, "-- Select Location --", 0, "",1 ); 
						    ?>
						</td>
                       
                        <td id="buyer_td">
                        	<? 
						    	echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- Select Buyer --", 0, ""); 
						    ?>
                        </td>
                        <td align="center">
                            <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:80px"  placeholder="Double Click to Search" onDblClick="openmypage_fabricBooking();" readonly>
							<input type="hidden" name="txt_booking_no_id" id="txt_booking_no_id" class="text_boxes">

                            <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:80px; display: none" placeholder="Order No"/>
                        </td>
                        <td align="center">
                            <input type="text" name="txt_batch" id="txt_batch" class="text_boxes" style="width:80px" placeholder="Batch"/>
                        </td>
                        <td align="center" id="color_td">
                            <input type="text" name="txt_batch_color" id="txt_batch_color" class="text_boxes ui-autocomplete-input" style="width:80px" placeholder="Batch Color"/>
                            <input type="hidden" name="hidden_color_id" id="hidden_color_id" style="width:100px" placeholder="Batch Color"/>
                            <div id="color_suggest"><span class="loader"></span></div>
                        </td>
                        <td align="center">
                            <? echo create_drop_down( "txt_color_range", 80, $color_range,"", 1, "-- Select Color Range --", 0, "" ); ?>
                        </td>
                        <td id="floor_td">
                            <? echo create_drop_down( "cbo_floor_id", 100, $blank_array,"", 1, "-- Select Floor --", 0, "",1 ); ?>
                        </td>
                        <td align="center">
                            <input type="text" name="txt_machine_name" id="txt_machine_name" class="text_boxes" style="width:100px" placeholder="Browse Machine" onDblClick="openmypage_machine()" readonly />
                            <input type="hidden" name="txt_machine_id" id="txt_machine_id" class="text_boxes" style="width:80px"  />
                        </td>
                        <td align="center">
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" value="<? echo date("d-m-Y", time() - 1296000);?>" placeholder="From Date" > To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" value="<? echo date("d-m-Y", time() - 86400);?>" placeholder="To Date"  >
                        </td>
                        
                    </tr>
                </tbody>
                <tr>
                    <td colspan="8" align="center">
                        <? echo load_month_buttons(1); ?>
                    </td>
					<td align="center" colspan="4">
                        <input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated('report_generate')" /> &nbsp;
                       
                       
                        <input type="reset" id="reset_btn" class="formbutton" style="width:50px" value="Reset" onClick="reset_form('machinewiseproduction_1','report_container*report_container2','','','')" />
                    </td>
                   
                </tr>
            </table> 
        </fieldset>
        </div>
        <div id="report_container" align="center"c style="padding: 5px 0;"></div>
        <div id="report_container2" align="left"></div>
    </form> 
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script> //$('#cbo_location_id').val(0); </script>
</html>
