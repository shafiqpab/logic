<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Fabric Receive Status Report.
Functionality	:	
JS Functions	:
Created by		:	Fuad 
Creation date 	: 	11-08-2013
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
echo load_html_head_contents("Booking Approval Status Report", "../../", 1, 1,'',1,1);

?>	

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';
 
function fn_report_generated()
{
	if (form_validation('cbo_company_name','Comapny Name')==false)
	{
		return;
	}
	
	var report_title=$( "div.form_caption" ).html();
	var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_booking_no*hide_booking_id*txt_job_no*hide_job_id*cbo_type*cbo_date_by*txt_date_from*txt_date_to*cbo_booking_type*txt_app_date',"../../")+'&report_title='+report_title;
	freeze_window(3);
	http.open("POST","requires/fabric_booking_approval_status_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fn_report_generated_reponse;
}
	

function fn_report_generated_reponse()
{
 	if(http.readyState == 4) 
	{
  		var response=trim(http.responseText).split("####");
		$('#report_container2').html(response[0]);
		document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;&nbsp;<input type="button" onClick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
		var tableFilters = { col_0: "none" }
		setFilterGrid("tbl_list_search",-1);
		show_msg('3');
		release_freezing();
 	}
	
}

function openmypage(type)
{
	if(form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}
	
	var companyID = $("#cbo_company_name").val();
	var buyerID = $("#cbo_buyer_name").val();
	
	var page_link='requires/fabric_booking_approval_status_report_controller.php?action=search_popup&companyID='+companyID+'&buyerID='+buyerID+'&type='+type;
	if(type==1) var title='Booking No Search'; else var title='Job No Search';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var hide_id=this.contentDoc.getElementById("hide_id").value;
		var hide_no=this.contentDoc.getElementById("hide_no").value;

		if(type==1)
		{
			$('#txt_booking_no').val(hide_no);
			$('#hide_booking_id').val(hide_id);	
		}
		else
		{
			$('#txt_job_no').val(hide_no);
			$('#hide_job_id').val(hide_id);	
		}
	}
}

function openImgFile(job_no,action)
{
	var page_link='requires/fabric_booking_approval_status_report_controller.php?action='+action+'&job_no='+job_no;
	if(action=='img') var title='Image View'; else var title='File View';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../');
	
}


function generate_worder_report(type,booking_no,company_id,order_id,fabric_nature,fabric_source,job_no,approved)
{
	var data="action=show_fabric_booking_report"+
				'&txt_booking_no='+"'"+booking_no+"'"+
				'&cbo_company_name='+"'"+company_id+"'"+
				'&txt_order_no_id='+"'"+order_id+"'"+
				'&cbo_fabric_natu='+"'"+fabric_nature+"'"+
				'&cbo_fabric_source='+"'"+fabric_source+"'"+
				'&id_approved_id='+"'"+approved+"'"+
				'&txt_job_no='+"'"+job_no+"'";
				
	if(type==1)	
	{			
		http.open("POST","../../order/woven_order/requires/short_fabric_booking_controller.php",true);
	}
	else if(type==2)
	{
		http.open("POST","../../order/woven_order/requires/fabric_booking_controller.php",true);
	}
	else
	{
		http.open("POST","../../order/woven_order/requires/sample_booking_controller.php",true);
	}
	
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_fabric_report_reponse;
}

function generate_fabric_report_reponse()
{
	if(http.readyState == 4) 
	{
		var w = window.open("Surprise", "_blank");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title></head><body>'+http.responseText+'</body</html>');
		d.close();
	}
}

function generate_worder_report2(type,booking_no,company_id,order_id,fabric_nature,fabric_source,job_no,approved)
{
	var data="action=show_fabric_booking_report3"+
				'&txt_booking_no='+"'"+booking_no+"'"+
				'&cbo_company_name='+"'"+company_id+"'"+
				'&txt_order_no_id='+"'"+order_id+"'"+
				'&cbo_fabric_natu='+"'"+fabric_nature+"'"+
				'&cbo_fabric_source='+"'"+fabric_source+"'"+
				'&id_approved_id='+"'"+approved+"'"+
				'&txt_job_no='+"'"+job_no+"'";
				
	if(type==1)	
	{			
		http.open("POST","../../order/woven_order/requires/short_fabric_booking_controller.php",true);
	}
	else if(type==2)
	{
		http.open("POST","../../order/woven_order/requires/fabric_booking_controller.php",true);
	}
	else
	{
		http.open("POST","../../order/woven_order/requires/sample_booking_controller.php",true);
	}
	
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_fabric_report_reponse2;
}

function generate_fabric_report_reponse2()
{
	if(http.readyState == 4) 
	{
		var w = window.open("Surprise", "_blank");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title></head><body>'+http.responseText+'</body</html>');
		d.close();
	}
}


function generate_fabric_report2(type,booking_no,company_id,order_id,fabric_nature,fabric_source,job_no,approved)
{
	if(type!=2)
	{
		alert("Short Fabric or Sample Booking\n No Suplimetary for this.");
		return;
	}
	
	var data="action=show_fabric_booking_report2"+
				'&txt_booking_no='+"'"+booking_no+"'"+
				'&cbo_company_name='+"'"+company_id+"'"+
				'&txt_order_no_id='+"'"+order_id+"'"+
				'&cbo_fabric_natu='+"'"+fabric_nature+"'"+
				'&cbo_fabric_source='+"'"+fabric_source+"'"+
				'&id_approved_id='+"'"+approved+"'"+
				'&txt_job_no='+"'"+job_no+"'";
		
	http.open("POST","../../order/woven_order/requires/fabric_booking_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_fabric_report2_reponse;
}


function generate_fabric_report2_reponse()
{
	if(http.readyState == 4) 
	{
		var w = window.open("Surprise", "_blank");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title></head><body>'+http.responseText+'</body</html>');
		d.close();
	}
}

function new_window()
{
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="none";
	
	$("#tbl_list_search tr:first").hide();
	
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'+
'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close();
	
	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="330px";
	
	$("#tbl_list_search tr:first").show();
}


function generate_worder_report3(txt_booking_no,cbo_company_name,txt_order_no_id,cbo_fabric_natu,cbo_fabric_source,txt_job_no,id_approved_id,print_id,entry_form,type,i,fabric_nature)
	{
		if(print_id==53)
		{
			var report_title='Main Fabric Booking';
		}
		else
		{
			var report_title='Budget Main Fabric Booking';
		}
		
		if(print_id==85 || print_id==53 || print_id==143){
			var show_yarn_rate='';
			var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
			if (r==true)
			{
				show_yarn_rate=1;
			}
			else
			{
				show_yarn_rate=0;
			}
		}

		var data="action="+type+
		'&txt_booking_no='+"'"+txt_booking_no+"'"+
		'&cbo_company_name='+"'"+cbo_company_name+"'"+
		'&txt_order_no_id='+"'"+txt_order_no_id+"'"+
		'&cbo_fabric_natu='+"'"+cbo_fabric_natu+"'"+
		'&cbo_fabric_source='+"'"+cbo_fabric_source+"'"+
		'&id_approved_id='+"'"+id_approved_id+"'"+
		'&report_title='+report_title+
		'&txt_job_no='+"'"+txt_job_no+"'"+
		'&show_yarn_rate='+show_yarn_rate+
		'&path=../../';


		
		
		if(print_id==53)
		{
			var report_title='Main Fabric Booking';
		}
		else
		{
			var report_title='Short Fabric Booking';
		}
		
		if(print_id==8){var type="show_fabric_booking_report";}
		else if(print_id==9){var type="show_fabric_booking_report3";}
		else if(print_id==10){var type="show_fabric_booking_report4";}
		else if(print_id==46){var type="show_fabric_booking_urmi";}
		else if(print_id==2){var type="show_fabric_booking_report";}
		else if(print_id==38){var type="show_fabric_booking_report";}
		else if(print_id==39){var type="show_fabric_booking_report2";}
		else if(print_id==64){var type="show_fabric_booking_report3";}


		if(print_id==46 || print_id==8 || print_id==9 || print_id==10 )
		{
			var show_yarn_rate='';
			var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
			if (r==true)
			{
				show_yarn_rate=1;
			}
			else
			{
				show_yarn_rate=0;
			}
			
			
			var data="action="+type+
			'&txt_booking_no='+"'"+txt_booking_no+"'"+
			'&cbo_company_name='+"'"+cbo_company_name+"'"+
			'&txt_order_no_id='+"'"+txt_order_no_id+"'"+
			'&cbo_fabric_natu='+"'"+cbo_fabric_natu+"'"+
			'&cbo_fabric_source='+"'"+cbo_fabric_source+"'"+
			'&id_approved_id='+"'"+id_approved_id+"'"+
			'&report_title='+"Short Fabric Booking"+
			'&txt_job_no='+"'"+txt_job_no+"'"+
			'&report_type='+"'1'"+
			'&show_yarn_rate='+show_yarn_rate+
			'&path=../../';
		}
		
		else if(print_id==93 || print_id==2 || print_id==3 || print_id==4 || print_id==5 || print_id==6 || print_id==7)
		{
			var show_yarn_rate='';
			var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
			if (r==true)
			{
				show_yarn_rate=1;
			}
			else
			{
				show_yarn_rate=0;
			}
			var data="action="+type+
			'&txt_booking_no='+"'"+txt_booking_no+"'"+
			'&cbo_company_name='+"'"+cbo_company_name+"'"+
			'&txt_order_no_id='+"'"+txt_order_no_id+"'"+
			'&cbo_fabric_natu='+"'"+cbo_fabric_natu+"'"+
			'&cbo_fabric_source='+"'"+cbo_fabric_source+"'"+
			'&id_approved_id='+"'"+id_approved_id+"'"+
			'&report_title='+"Budget Wise Fabric Booking"+
			'&txt_job_no='+"'"+txt_job_no+"'"+
			'&show_yarn_rate='+show_yarn_rate+
			'&path=../../';
		}
		
		else if(print_id==38 || print_id==39 || print_id==64)
		{
			var show_yarn_rate='';
			var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
			if (r==true)
			{
				show_yarn_rate=1;
			}
			else
			{
				show_yarn_rate=0;
			}
			
			var data="action="+type+
			'&txt_booking_no='+"'"+txt_booking_no+"'"+
			'&cbo_company_name='+"'"+cbo_company_name+"'"+
			'&txt_order_no_id='+"'"+txt_order_no_id+"'"+
			'&cbo_fabric_natu='+"'"+cbo_fabric_natu+"'"+
			'&cbo_fabric_source='+"'"+cbo_fabric_source+"'"+
			'&id_approved_id='+"'"+id_approved_id+"'"+
			'&report_title='+"Sample Fabric Booking"+
			'&txt_job_no='+"'"+txt_job_no+"'"+
			'&show_yarn_rate='+show_yarn_rate+
			'&path=../../';
		}
		
		

			
		freeze_window(5);
		if(print_id==45 || print_id==53 || print_id==93 || print_id==2 || print_id==3 || print_id==4 || print_id==5 || print_id==6 || print_id==7)
		{
			http.open("POST","../../order/woven_order/requires/fabric_booking_urmi_controller.php",true);
		}
		else if(print_id==46 || print_id==8 || print_id==9 || print_id==10)
		{
			http.open("POST","../../order/woven_order/requires/short_fabric_booking_controller.php",true);
		}
		else if(print_id==39 || print_id==38 || print_id==64)
		{
			http.open("POST","../../order/woven_order/requires/sample_booking_controller.php",true);
		}
		else if(print_id==85 || print_id==84 || print_id==151 || print_id==143 )
		{
			http.open("POST","../../order/woven_order/requires/partial_fabric_booking_controller.php",true);
		}
		else
		{
			if(fabric_nature == 3){

				if(entry_form==118 ) //(print_id==45 || print_id==53 || print_id==93 || print_id==73 || || print_id==2)
				{
					http.open("POST","../../order/woven_gmts/requires/fabric_booking_urmi_controller.php",true);
				}
				else if( entry_form==108) //&& (print_id==85 || print_id==143 || print_id==160)
				{
					http.open("POST","../../order/woven_gmts/requires/partial_fabric_booking_controller.php",true);
				}			
				else if(entry_form==86)
				{
					http.open("POST","../../order/woven_gmts/requires/fabric_booking_controller.php",true);
				}
				else {
					http.open("POST","../../order/woven_order/requires/fabric_booking_controller.php",true);
				}

			}
			else{
			
				if(entry_form==118 ) //print_id==45 || print_id==53 || print_id==93 || print_id==73
				{
					http.open("POST","../../order/woven_order/requires/fabric_booking_urmi_controller.php",true);
				}
				else if(entry_form==108 ) //print_id==85 || print_id==143
				{
					http.open("POST","../../order/woven_order/requires/partial_fabric_booking_controller.php",true);
				}			
				else if(entry_form==86)
				{
					http.open("POST","../../order/woven_order/requires/fabric_booking_controller.php",true);
				}
			}
			
		}
		
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4) 
		    {
				var w = window.open("Surprise", "_blank");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+http.responseText+'</body</html>');
				d.close();
				release_freezing();
		   }
			
		}
	}
	










</script>
</head>
<body onLoad="set_hotkey();">
<form id="approvalStatusReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",''); ?>
         <h3 style="width:1110px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
         <fieldset style="width:1110px;">
             <table class="rpt_table" width="1110" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Buyer Name</th>
                    <th>Booking No</th>
                    <th>Booking Type</th>
                    <th>Date Type</th>
                    <th>Date Range</th>
                    <th>Approval Date</th>
                    <th>Job No</th>
                    <th>Type</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('approvalStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/fabric_booking_approval_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:100px" placeholder="Browse" onDblClick="openmypage(1);" readonly>
                            <input type="hidden" name="hide_booking_id" id="hide_booking_id" readonly>
                        </td>
                        <td>
                        	<?
								$search_by_date=array(0=>"All",1=>"Main",2=>"Short",3=>"Sample");
								echo create_drop_down( "cbo_booking_type", 75, $search_by_date,"",0, "", "1",'',0 );
							?>
                        </td>
                        <td>
                        	<?
								$search_by_date=array(1=>"Booking",2=>"Insert");
								echo create_drop_down( "cbo_date_by", 80, $search_by_date,"",0, "", "",'',0 );
							?>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;" placeholder="From Date" readonly/>                    							
                            To
                            <input type="text" name="txt_date_to" id="txt_date_to" placeholder="To Date" class="datepicker" style="width:70px;" readonly />
                        </td>
                        <td><input type="text" id="txt_app_date" name="txt_app_date" style="width:80px;" class="datepicker"></td>
                         <td>
                            <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px" placeholder="Browse" onDblClick="openmypage(2);" readonly>
                            <input type="hidden" name="hide_job_id" id="hide_job_id" readonly>
                        </td>
                        <td>
                        	<?
								$search_by_arr=array(1=>"Pending",2=>"Full Approved");
								echo create_drop_down( "cbo_type", 100, $search_by_arr,"",0, "", "",'',0 );
							?>
                        </td> 
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated()" />
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="8" align="center"><? echo load_month_buttons(1);  ?></td>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
    	</div>
        <div id="report_container" align="center"></div>
        <div id="report_container2" align="center"></div>
    </div>
 </form>   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
