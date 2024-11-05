<?
/*-------------------------------------------- Comments
Purpose			: 	Booking wise yarn allocation details
				
Functionality	:	
JS Functions	:
Created by		:	Md Didarul Alam 
Creation date 	: 	04-07-2020
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
echo load_html_head_contents("Daily Yarn Stock","../../../", 1, 1, $unicode,1,1); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

function generate_report(report_type='')
{

	if( form_validation('txt_date_from*txt_date_to','From Date*To Date')==false )
	{
		return;
	}
	
	var cbo_company_name = $("#cbo_company_name").val();
	var cbo_dyed_type = $("#cbo_dyed_type").val();
	var cbo_yarn_type = $("#cbo_yarn_type").val();
	var txt_count 	= $("#cbo_yarn_count").val();
	var txt_lot_no 	= $("#txt_lot_no").val();
	var from_date 	= $("#txt_date_from").val();
	var to_date 	= $("#txt_date_to").val();
	var cbo_supplier = $("#cbo_supplier").val();	
	var txt_composition = $("#txt_composition").val();
	var txt_composition_id = $("#txt_composition_id").val();
	
	var lot_search_type = 0

	if ($('#lot_search_type').is(":checked"))
	{
	   lot_search_type = 1;
	}

	if (report_type == 3) 
	{
		var action ='generate_report_only_excel';
    }
    else
    {
		var action ='generate_report';
	}

	var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_dyed_type="+cbo_dyed_type+"&cbo_yarn_type="+cbo_yarn_type+"&txt_count="+txt_count+"&txt_lot_no="+txt_lot_no+"&from_date="+from_date+"&to_date="+to_date+"&cbo_supplier="+cbo_supplier+"&txt_composition="+txt_composition+"&txt_composition_id="+txt_composition_id+"&lot_search_type="+lot_search_type+"&report_type="+report_type;
 	var data="action="+action+dataString;

	freeze_window(3);
	http.open("POST","requires/booking_wise_yarn_allocation_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_report_reponse;  
}

function generate_report_reponse()
{	
	if(http.readyState == 4) 
	{	 
		console.log(http.responseText);
 		var reponse=trim(http.responseText).split("**");

 		if(reponse[2]==3)
		{
			//$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			document.getElementById('excel').click();
				
			show_msg('3');
			release_freezing();				
			return;	
		}

		$("#report_container2").html(reponse[0]);  
		document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		
		if(reponse[2]==1)
		{
			var tableFilters = 
			{ 
			    col_0: "none", 
			    col_operation: {
				id: ["value_total_allocation_qty","value_total_issue_qty","value_total_issue_return_qty","value_total_balance"],
				col: [17,18,19,20],
				operation: ["sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
			    }
		    }
		}
		else
		{
			var tableFilters = 
			{ 
			    col_0: "none", 
			    col_operation: {
				id: ["value_total_allocation_qty","value_total_issue_qty","value_total_issue_return_qty","value_total_balance"],
				col: [18,19,20,21],
				operation: ["sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
			    }
		    }
		}
		
		setFilterGrid("table_body",-1,tableFilters);

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
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><style></style></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close(); 
	document.getElementById('scroll_body').style.overflow="auto"; 
	document.getElementById('scroll_body').style.maxHeight="350px";
}

function validate(e)
{
	var key;
	var keychar;
	if (window.event)
		key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;
	keychar = String.fromCharCode(key);
	// control keys
	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
	return true;
	// numbers
	else if ((("%").indexOf(keychar) > -1))
		return false;
	else
		return true;
}

$(document).ready(function() 
{
	$('#txt_composition').bind('copy paste cut',function(e) {
		e.preventDefault(); //disable cut,copy,paste
	});
});


function openmypage_composition()
{
	var pre_composition_id = $("#txt_composition_id").val();
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/booking_wise_yarn_allocation_report_controller.php?action=composition_popup&pre_composition_id='+pre_composition_id, 'Composition Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0','../../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var composition_des=this.contentDoc.getElementById("hidden_composition").value; //Access form field with id="emailfield"
		var composition_id=this.contentDoc.getElementById("hidden_composition_id").value;
		$("#txt_composition").val(composition_des);
		$("#txt_composition_id").val(composition_id);
		
	}
}
</script>

</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../../",$permission);  ?>    		 
    <form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" > 
    <div style="width:100%;" align="center">
        <h3 style="width:1030px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
        <div style="width:100%;" id="content_search_panel">
            <fieldset style="width:1030px;">
                <table class="rpt_table" width="1030" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th>Company</th> 
                            <th>Supplier</th>                               
                            <th>Dyed Type</th>
                            <th>Yarn Type</th>
                            <th>Count</th>
                            <th>Composition</th>
                            <th>Lot<br><input type="checkbox" name="lot_search_type" id="lot_search_type" title="Lot Search start with"></th>
                            <!--<th>Value</th>-->
                            <th class="must_entry_caption" colspan="2">Date</th>
                            <th colspan="3"><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" /></th>
                        </tr>
                    </thead>
                    <tr>
                        <td>
							<? 
                               echo create_drop_down( "cbo_company_name", 110, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- All Company --", $selected, "load_drop_down( 'requires/booking_wise_yarn_allocation_report_controller', this.value, 'load_drop_down_supplier', 'supplier' );get_php_form_data( this.value, 'eval_multi_select', 'requires/booking_wise_yarn_allocation_report_controller' );" );
                            ?>                            
                        </td>
                        <td id="supplier"> 
							<?
                            	
							echo create_drop_down("cbo_supplier", 120, "select c.supplier_name,c.id from lib_supplier c where c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 0, "-- Select --", 0, "", 0);
                            ?>
                           </td>
                        
                        <td align="center">
                            <?   
                                $dyedType=array(0=>'All',1=>'Dyed Yarn',2=>'Non Dyed Yarn');
                                echo create_drop_down( "cbo_dyed_type", 80, $dyedType,"", 0, "--Select--", $selected, "", "","");
                            ?>              
                        </td>

                        <td> 
                            <?
                                echo create_drop_down("cbo_yarn_type",100,$yarn_type,"",0, "-- Select --", $selected, "");
							?>
                        </td>
                        <td>
                            <?
                                echo create_drop_down("cbo_yarn_count",90,"select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count","id,yarn_count",0, "-- Select --", $selected, "");
                            ?>
                        </td>
                        <td>
                         <input type="text" id="txt_composition" name="txt_composition" class="text_boxes" style="width:70px" value="" onDblClick="openmypage_composition();" placeholder="Browse" readonly />

                            <input type="hidden" id="txt_composition_id" name="txt_composition_id" class="text_boxes" style="width:70px" value=""  />
                        </td>
                        <td>
                            <input type="text" id="txt_lot_no" name="txt_lot_no" class="text_boxes" style="width:45px" value="" />
                        </td>
                        <!--
                        <td>
                            <?   
                               // $valueWithArr=array(0=>'Value With 0',1=>'Value Without 0');
                               // echo create_drop_down( "cbo_value_with", 110, $valueWithArr,"",0,"",1,"","","");
                         ?>
                        </td>
                    	-->
                        <td align="center"> 
                             <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:55px" readonly/>
                          </td>
                        <td align="center">
                            
                             <input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date("d-m-Y");?>" class="datepicker" style="width:55px" readonly/>
                        </td>
                        <td colspan="2" align="center">
                            <input type="button" name="search" id="search1" value="Show" onClick="generate_report(1)" style="width:60px;display:display;" class="formbutton" />
                        </td>

                        <td colspan="2" align="center">
                            <input type="button" name="search" id="search1" value="Ageing Report" onClick="generate_report(2)" style="width:60px;display:display;" class="formbutton" />
                        </td>

                    </tr>

                    <tr>
                        <td colspan="9">&nbsp;&nbsp;&nbsp;&nbsp;<? echo load_month_buttons(1); ?></td>
                        <td colspan="3" align="center">
                            <input type="button" name="search" id="search1" value="Excel Generate" onClick="generate_report(3)" style="width:100px;display:display;" class="formbutton" />
                        </td>
                    </tr>

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
</body> 

<script>
	set_multiselect('cbo_yarn_count*cbo_supplier*cbo_yarn_type','0*0*0','0*0*0','','0*0*0');
</script> 
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$("#cbo_value_with").val(1);
</script> 
</html>
