<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yarn Stock Ledger
				
Functionality	:	
JS Functions	:
Created by		:	Bilas 
Creation date 	: 	24-08-2013
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

function generate_report(type)
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
	var value_with 	= $("#cbo_value_with").val();
	//alert(value_with);//Tipu
	var store_wise 	= $("#cbo_store_wise").val();
	var store_name 	= $("#cbo_store_name").val();	
	var cbo_supplier = $("#cbo_supplier").val();
	var cbo_get_upto = $("#cbo_get_upto").val();
	var txt_days = $("#txt_days").val();
	var cbo_get_upto_qnty = $("#cbo_get_upto_qnty").val();
	var txt_qnty = $("#txt_qnty").val();
	var txt_composition = $("#txt_composition").val();
	var txt_composition_id = $("#txt_composition_id").val();
	
	var lot_search_type = 0
	if ($('#lot_search_type').is(":checked"))
	{
	   lot_search_type = 1;
	}

	if(cbo_get_upto!=0 && txt_days*1<=0)
	{
		alert("Please Insert Days.");	
		$("#txt_days").focus();
		return;
	}
	
	if(cbo_get_upto_qnty!=0 && txt_qnty*1<=0)
	{
		alert("Please Insert Qty.");	
		$("#txt_qnty").focus();
		return;
	}
	
	var show_val_column='';
	if(type==1 || type ==8 || type ==9)
	{
		var r=confirm("Press \"OK\" to open with Rate & value column\nPress \"Cancel\" to open without Rate & value column");
		if (r==true)
		{
			show_val_column="1";
		}
		else
		{
			show_val_column="0";
		}
	}
	
	var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_dyed_type="+cbo_dyed_type+"&cbo_yarn_type="+cbo_yarn_type+"&txt_count="+txt_count+"&txt_lot_no="+txt_lot_no+"&from_date="+from_date+"&to_date="+to_date+"&store_wise="+store_wise+"&store_name="+store_name+"&value_with="+value_with+"&cbo_supplier="+cbo_supplier+"&show_val_column="+show_val_column+"&get_upto="+cbo_get_upto+"&txt_days="+txt_days+"&get_upto_qnty="+cbo_get_upto_qnty+"&txt_qnty="+txt_qnty+"&type="+type+"&txt_composition="+txt_composition+"&txt_composition_id="+txt_composition_id+"&lot_search_type="+lot_search_type;
 	var data="action=generate_report"+dataString;
	freeze_window(3);
	http.open("POST","requires/daily_yarn_cumulative_stock_report_controller.php",true);
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
		

		show_msg('3');
		release_freezing();
	}
} 
 

function new_window()
{
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="none"; 
	
	//$("#table_body tr:first").hide();
	
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><style></style></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close(); 
	document.getElementById('scroll_body').style.overflow="auto"; 
	document.getElementById('scroll_body').style.maxHeight="350px";
	
	//$("#table_body tr:first").show();
}

function openmypage(prod_id,action)
{
	var companyID = $("#cbo_company_name").val();
	var popup_width='1170px';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_yarn_cumulative_stock_report_controller.php?companyID='+companyID+'&prod_id='+prod_id+'&action='+action, 'Yarn Allocation Statement', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
}

function openmypage_stock(prod_id,action)
{
	//alert(prod_id);
	var popup_width='750px';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_yarn_cumulative_stock_report_controller.php?prod_id='+prod_id+'&action='+action, 'Yarn Stock Details', 'width='+popup_width+', height=400px,center=1,resize=0,scrolling=0','../../');
}


function openmypage_trans(prod_id,trans_type,store_name,from_date,to_date,action)
{
	if (action='action_remarks') {action='action_remarks';}else{action=action;}
	var popup_width='450px';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_yarn_cumulative_stock_report_controller.php?prod_id='+prod_id+'&trans_type='+trans_type+'&store_name='+store_name+'&from_date='+from_date+'&to_date='+to_date+'&action='+action, 'Yarn Transfer Details', 'width='+popup_width+', height=200px,center=1,resize=0,scrolling=0','../../');
}

function openmypage_remarks(prod_id)
{
	var popup_width='450px';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_yarn_cumulative_stock_report_controller.php?prod_id='+prod_id+'&action=mrr_remarks', 'Yarn Transfer Details', 'width=570px,height=350px,center=1,resize=0,scrolling=0','../../');
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
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_yarn_cumulative_stock_report_controller.php?action=composition_popup&pre_composition_id='+pre_composition_id, 'Composition Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0','../../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var composition_des=this.contentDoc.getElementById("hidden_composition").value; //Access form field with id="emailfield"
		var composition_id=this.contentDoc.getElementById("hidden_composition_id").value;
		$("#txt_composition").val(composition_des);
		$("#txt_composition_id").val(composition_id);
		
	}
}


function show_test_report(company_id, productID)
{
	generate_report_file(company_id + '*' + productID, 'yarn_test_report','requires/daily_yarn_cumulative_stock_report_controller');
}
function generate_report_file(data, action, page) 
{
	window.open("requires/daily_yarn_cumulative_stock_report_controller.php?data=" + data + '&action=' + action, true);
}
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../../",$permission);  ?>    		 
    <form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" > 
    <div style="width:100%;" align="center">
        <h3 style="width:1550px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
        <div style="width:100%;" id="content_search_panel">
            <fieldset style="width:1550px;">
                <table class="rpt_table" width="1550" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th>Company</th> 
                            <th>Supplier</th>                               
                            <th>Dyed Type</th>
                            <th>Yarn Type</th>
                            <th>Count</th>
                            <th>Composition</th>
                            <th>Lot<br><input type="checkbox" name="lot_search_type" id="lot_search_type" title="Lot Search start with"></th>
                            <th>Value</th>
                            <th class="must_entry_caption" colspan="2">Date</th>
                            <th>Store Wise</th>
                            <th>Store Name</th>
                            <th>Get Upto</th>
                            <th>Days</th>
                            <th>Get Upto</th>
                            <th>Qty.</th>
                            <th colspan="2"><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" /></th>
                        </tr>
                    </thead>
                    <tr>
                        <td>
							<? 
                               echo create_drop_down( "cbo_company_name", 110, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- All Company --", $selected, "load_drop_down( 'requires/daily_yarn_cumulative_stock_report_controller', this.value, 'load_drop_down_supplier', 'supplier' );load_drop_down( 'requires/daily_yarn_cumulative_stock_report_controller', this.value+'**'+document.getElementById('cbo_store_wise').value, 'load_drop_down_store', 'store_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/daily_yarn_cumulative_stock_report_controller' );get_php_form_data( this.value, 'company_wise_report_button_setting','requires/daily_yarn_cumulative_stock_report_controller' );" );
                            ?>                            
                        </td>
                        <td id="supplier"> 
							<?
                            	
							echo create_drop_down("cbo_supplier", 120, "select c.supplier_name,c.id from lib_supplier c where c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 0, "-- Select --", 0, "", 0);
								
								//echo create_drop_down( "cbo_supplier", 120, $blank_array,"",0, "--- Select Supplier ---", $selected, "",0);
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
                               //echo create_drop_down( "cbo_yarn_type", 80, $yarn_type,"", 1, "--Select--", 0, "",0 );
                                echo create_drop_down("cbo_yarn_type",100,$yarn_type,"",0, "-- Select --", $selected, "");
							?>
                        </td>
                        <td>
                            <?
                                echo create_drop_down("cbo_yarn_count",90,"select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count","id,yarn_count",0, "-- Select --", $selected, "");
                            ?>
                        </td>
                        <td>
                            <!-- <input type="text" id="txt_composition" name="txt_composition" class="text_boxes" style="width:70px" value="" onKeyPress="return validate(event);" /> -->
                            <input type="text" id="txt_composition" name="txt_composition" class="text_boxes" style="width:70px" value="" onDblClick="openmypage_composition();" placeholder="Browse" readonly />

                            <input type="hidden" id="txt_composition_id" name="txt_composition_id" class="text_boxes" style="width:70px" value=""  />
                        </td>
                        <td>
                            <input type="text" id="txt_lot_no" name="txt_lot_no" class="text_boxes" style="width:45px" value="" />
                        </td>
                        <td>
                            <?   
                                $valueWithArr=array(0=>'Value With 0',1=>'Value Without 0');
                                echo create_drop_down( "cbo_value_with", 110, $valueWithArr,"",0,"",1,"","","");
                                //$field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name, $additionalClass, $additionalAttributes 
                            ?>
                        </td>
                        <td align="center">
                             <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:55px" readonly/>
                          </td>
                        <td align="center">
                            
                             <input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date("d-m-Y");?>" class="datepicker" style="width:55px" readonly/>
                        </td>
                        <td> 
                            <?
                                echo create_drop_down( "cbo_store_wise", 50, $yes_no,"", 0, "--Select--", 2, "load_drop_down( 'requires/daily_yarn_cumulative_stock_report_controller', document.getElementById('cbo_company_name').value+'**'+this.value, 'load_drop_down_store', 'store_td' );",0 );
                            ?>
                        </td>
                        <td id="store_td">
                            <? 
                                echo create_drop_down( "cbo_store_name", 100, $blank_array,"", 1, "-- All Store --", $storeName, "",1 );
                            ?>
                        </td>
                        <td> 
                            <?
								$get_upto=array(1=>"Greater Than",2=>"Less Than",3=>"Greater/Equal",4=>"Less/Equal",5=>"Equal");
                                echo create_drop_down( "cbo_get_upto", 70, $get_upto,"", 1, "- All -", 0, "",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_days" name="txt_days" class="text_boxes_numeric" style="width:30px" value="" />
                        </td>
                        <td> 
                            <?
                                echo create_drop_down( "cbo_get_upto_qnty", 70, $get_upto,"", 1, "- All -", 0, "",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_qnty" name="txt_qnty" class="text_boxes_numeric" style="width:30px" value="" />
                        </td>
                        <td colspan="2">
                        	<input type="button" name="search" id="search5" value="Show" onClick="generate_report(5)" style="width:80px;display:display;" class="formbutton" />

                            <!-- <input type="button" name="search" id="search1" value="Show" onClick="generate_report(1)" style="width:60px;display:display;" class="formbutton" />

                            <input type="button" name="search" id="search9" value="Show 2" onClick="generate_report(9)" style="width:60px;display:display;" class="formbutton" /> -->

                        </td>
                    </tr>

                    
                    <tr>
                        <td colspan="18">&nbsp;&nbsp;&nbsp;&nbsp;<? echo load_month_buttons(1); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                        	
                        	 <!--  
                        	<input type="button" name="search" id="search2" value="Count Wise Summ." onClick="generate_report(2)" style="width:100px;display:display;" class="formbutton" />
                            <input type="button" name="search" id="search3" value="Type Wise Summ." onClick="generate_report(3)" style="width:100px;display:display;" class="formbutton" />
                            <input type="button" name="search" id="search4" value="Composition Wise Summ." onClick="generate_report(4)" style="width:140px;display:display;" class="formbutton" />
                            <input type="button" name="search" id="search6" value="Count & Type Wise - 2" onClick="generate_report(7)" style="width:140px;display:display;" class="formbutton" />
                            <input type="button" name="search" id="search7" value="Report" onClick="generate_report(8)" style="width:90px;display:display;" class="formbutton" />
                            <input type="button" name="search" id="search" value="MRR Wise Stock" onClick="generate_report(6)" style="width:100px; display:display;" class="formbutton" />

                        </td>
                    </tr>
                     -->
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
