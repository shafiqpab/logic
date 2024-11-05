<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yarn Stock Ledger
				
Functionality	:	
JS Functions	:
Created by		:	Md. Nuruzzaman
Creation date 	: 	09.10.2021
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
echo load_html_head_contents("Yarn Transfer Report","../../../", 1, 1, $unicode,1,1); 
?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1)
	window.location.href = "../logout.php";

//for openmypage_company	
function openmypage_company(str)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/yarn_transfer_report_controller.php?action=company_popup', 'Company Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0','../../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var selected_name=this.contentDoc.getElementById("selected_name").value;
		var selected_id=this.contentDoc.getElementById("selected_id").value;
		
		if(str ==1)
		{
			$("#txt_from_company").val(selected_name);
			$("#cbo_from_company").val(selected_id);
		}
		else
		{
			$("#txt_to_company").val(selected_name);
			$("#cbo_to_company").val(selected_id);
		}
	}
}

//for openmypage_store
function openmypage_store(str)
{
	if(str ==1)
	{
		var cbo_company_name = $("#cbo_from_company").val();
	}
	else
	{
		var cbo_company_name = $("#cbo_to_company").val();		
	}

	//emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/store_wise_item_stock_report_controller.php?action=store_popup&cbo_company_name='+cbo_company_name+'&cbo_item_category='+cbo_item_category+'&cbo_store='+cbo_store, 'Store Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0','../../');
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/yarn_transfer_report_controller.php?action=store_popup&cbo_company_name='+cbo_company_name, 'Store Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0','../../');

	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var selected_name=this.contentDoc.getElementById("selected_name").value;
		var selected_id=this.contentDoc.getElementById("selected_id").value;
		if(str ==1)
		{
			$("#txt_from_store").val(selected_name);
			$("#cbo_from_store").val(selected_id);
		}
		else
		{
			$("#txt_to_store").val(selected_name);
			$("#cbo_to_store").val(selected_id);
		}
	}
}

//openmypage_composition
function openmypage_composition()
{
	var pre_composition_id = $("#txt_composition_id").val();
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_yarn_stock_report_controller.php?action=composition_popup&pre_composition_id='+pre_composition_id, 'Composition Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0','../../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var composition_des=this.contentDoc.getElementById("hidden_composition").value; //Access form field with id="emailfield"
		var composition_id=this.contentDoc.getElementById("hidden_composition_id").value;
		$("#txt_composition").val(composition_des);
		$("#txt_composition_id").val(composition_id);
		
	}
}


//for generate_report
function generate_report(type)
{
	if( form_validation('txt_date_from*txt_date_to','From Date*To Date')==false )
	{
		return;
	}
	
	var cbo_from_company = $("#cbo_from_company").val();
	var cbo_to_company = $("#cbo_to_company").val();
	var cbo_supplier = $("#cbo_supplier").val();
	var cbo_dyed_type = $("#cbo_dyed_type").val();
	var cbo_yarn_type = $("#cbo_yarn_type").val();
	var txt_count 	= $("#cbo_yarn_count").val();
	var txt_composition_id = $("#txt_composition_id").val();
	var txt_lot_no 	= $("#txt_lot_no").val();
	var cbo_from_store 	= $("#cbo_from_store").val();
	var cbo_to_store 	= $("#cbo_to_store").val();
	var cbo_transfer_criteria 	= $("#cbo_transfer_criteria").val();
	var from_date 	= $("#txt_date_from").val();
	var to_date 	= $("#txt_date_to").val();

	var dataString = "&cbo_from_company="+cbo_from_company+
	"&cbo_to_company="+cbo_to_company+
	"&cbo_supplier="+cbo_supplier+
	"&cbo_dyed_type="+cbo_dyed_type+
	"&cbo_yarn_type="+cbo_yarn_type+
	"&txt_count="+txt_count+
	"&txt_composition_id="+txt_composition_id+
	"&txt_lot_no="+txt_lot_no+
	"&cbo_from_store="+cbo_from_store+
	"&cbo_to_store="+cbo_to_store+
	"&cbo_transfer_criteria="+cbo_transfer_criteria+
	"&from_date="+from_date+
	"&to_date="+to_date;
 	var data="action=generate_report"+dataString;
	freeze_window(3);
	http.open("POST","requires/yarn_transfer_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_report_reponse;  
}

//for generate_report_reponse
function generate_report_reponse()
{	
	if(http.readyState == 4) 
	{	 
 		var reponse=trim(http.responseText).split("**");
		$("#report_container2").html(reponse[0]);  
		document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		
		var tableFilters = { 
			//col_0: "none", 
			col_operation: {
				id: ["value_total_transfer_qty","value_total_transfer_amount"],
				col: [16,18],
				operation: ["sum","sum"],
				write_method: ["innerHTML","innerHTML"]
			}
		}
		setFilterGrid("table_body",-1,tableFilters);
		show_msg('3');
		release_freezing();
	}
} 

//for new_window
function new_window()
{
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="none"; 
	$("#table_body tr:first").hide();
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="all" /><style></style></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	document.getElementById('scroll_body').style.overflow="auto"; 
	document.getElementById('scroll_body').style.maxHeight="350px";
	$("#table_body tr:first").show();
	d.close(); 
}
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../../",$permission);  ?>    		 
    <form name="frm_yarn_transfer_rpt_1" id="frm_yarn_transfer_rpt_1" autocomplete="off" > 
    <div style="width:100%;" align="center">
        <h3 style="width:1450px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
        <div style="width:100%;" id="content_search_panel">
            <fieldset style="width:1450px;">
                <table class="rpt_table" width="1450" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th>From Company</th> 
                            <th>To Company</th> 
                            <th>Supplier</th>                               
                            <th>Dyed Type</th>
                            <th>Yarn Type</th>
                            <th>Count</th>
                            <th>Composition</th>
                            <th>Lot</th>
                            <th>From Store</th>
                            <th>To Store</th>
                            <th>Transfer Criteria</th>
                            <th class="must_entry_caption" colspan="2">Date</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" /></th>
                        </tr>
                    </thead>
                    <tr>
                        <td>
							<input type="text" id="txt_from_company" name="txt_from_company" class="text_boxes" style="width:110px" value="" onDblClick="openmypage_company(1);" placeholder="Browse" readonly />
							<input type="hidden" id="cbo_from_company" name="cbo_from_company" />
                        </td>
                        <td>
							<input type="text" id="txt_to_company" name="txt_to_company" class="text_boxes" style="width:110px" value="" onDblClick="openmypage_company(2);" placeholder="Browse" readonly />
							<input type="hidden" id="cbo_to_company" name="cbo_to_company" />
                        </td>
                        <td id="supplier"> 
							<?
							echo create_drop_down("cbo_supplier", 110, "select c.supplier_name,c.id from lib_supplier c where c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 0, "-- Select --", 0, "", 0);
                            ?>
                           </td>
                        <td align="center">
                            <?   
                                $dyedType=array(1=>'Dyed Yarn',2=>'Non Dyed Yarn');
                                echo create_drop_down( "cbo_dyed_type", 110, $dyedType,"", 0, "", $selected, "", "","");
                            ?>              
                        </td>
                        <td> 
                            <?
                                echo create_drop_down("cbo_yarn_type",110,$yarn_type,"",0, "-- Select --", $selected, "");
							?>
                        </td>
                        <td>
                            <?
                                echo create_drop_down("cbo_yarn_count",110,"select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count","id,yarn_count",0, "-- Select --", $selected, "");
                            ?>
                        </td>
                        <td> 
                            <input type="text" id="txt_composition" name="txt_composition" class="text_boxes" style="width:100px" value="" onDblClick="openmypage_composition();" placeholder="Browse" readonly />
                            <input type="hidden" id="txt_composition_id" name="txt_composition_id" class="text_boxes" style="width:100px" value=""  />
                        </td>
                        <td> 
                            <input type="text" id="txt_lot_no" name="txt_lot_no" class="text_boxes" style="width:100px" value="" />
                        </td>
                        <td> 
                            <input type="text" id="txt_from_store" name="txt_from_store" class="text_boxes" style="width:100px" value="" onDblClick="openmypage_store(1);" placeholder="Browse" readonly />
                            <input type="hidden" id="cbo_from_store" name="cbo_from_store" />
                        </td>
                        <td> 
                            <input type="text" id="txt_to_store" name="txt_to_store" class="text_boxes" style="width:100px" value="" onDblClick="openmypage_store(2);" placeholder="Browse" readonly />
                            <input type="hidden" id="cbo_to_store" name="cbo_to_store" />
                        </td>
                        <td> 
                            <?
                                echo create_drop_down("cbo_transfer_criteria", 110, $item_transfer_criteria, '', 0, '', $selected, '', '','1,2');
							?>
                        </td>
                        <td align="center">
                             <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:55px" readonly/>
                          </td>
                        <td align="center">
                            
                             <input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date("d-m-Y");?>" class="datepicker" style="width:55px" readonly/>
                        </td>
                        <td colspan="2">
                            <input type="button" name="search" id="search1" value="Show" onClick="generate_report(1)" style="width:60px;display:display;" class="formbutton" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="18">&nbsp;&nbsp;&nbsp;&nbsp;<? echo load_month_buttons(1); ?></td>
                    </tr>
                </table> 
            </fieldset> 
		</div>
    </div>
    <br /> 
        <!-- Result Contain Start-->
        <div id="report_container" align="left"></div>
        <div id="report_container2" style=" margin-top:5px; margin-left:5px;"></div> 
        <!-- Result Contain END-->
    </form>    
</div>    
</body> 
<script>
	set_multiselect('cbo_supplier*cbo_dyed_type*cbo_yarn_type*cbo_yarn_count*cbo_transfer_criteria','0*0*0*0*0','0*0*0*0*0','','0*0*0*0*0');
</script> 
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>