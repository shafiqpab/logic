<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Location Wise Yarn Stock Report

Functionality	:
JS Functions	:
Created by		:	Md. Nuruzzaman
Creation date 	: 	18-10-2021
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
echo load_html_head_contents("Location Wise Yarn Stock Report","../../../", 1, 1, $unicode,1,1);
?>

<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	/*
	|--------------------------------------------------------------------------
	| for func_reset_field
	|--------------------------------------------------------------------------
	|
	*/
	function func_reset_field()
	{
		$("#txt_supplier").val('');
		$("#cbo_supplier").val('');
		$("#txt_store").val('');
		$("#cbo_store").val('');
		$("#txt_floor").val('');
		$("#cbo_floor").val('');
		$("#txt_room").val('');
		$("#cbo_room").val('');
	}

	/*
	|--------------------------------------------------------------------------
	| for openmypage_company
	|--------------------------------------------------------------------------
	|
	*/
	function openmypage_company()
	{
		var cbo_company = $("#cbo_company_name").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/location_wise_yarn_stock_report_controller.php?action=company_popup&cbo_company='+cbo_company, 'Company Details', 'width=325px,height=250px,center=1,resize=0,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var selected_name=this.contentDoc.getElementById("selected_name").value;
			var selected_id=this.contentDoc.getElementById("selected_id").value;

			$("#txt_company").val(selected_name);
			$("#cbo_company_name").val(selected_id);
			func_reset_field();
		}
	}

	/*
	|--------------------------------------------------------------------------
	| for openmypage_supplier
	|--------------------------------------------------------------------------
	|
	*/
	function openmypage_supplier()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}

		var company_id = $("#cbo_company_name").val();
		var cbo_supplier = $("#cbo_supplier").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/location_wise_yarn_stock_report_controller.php?action=supplier_popup&company_id='+company_id+'&cbo_supplier='+cbo_supplier, 'Supplier Details', 'width=325px,height=370px,center=1,resize=0,scrolling=0','../../');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var selected_name=this.contentDoc.getElementById("selected_name").value;
			var selected_id=this.contentDoc.getElementById("selected_id").value;
			$("#txt_supplier").val(selected_name);
			$("#cbo_supplier").val(selected_id);
		}
	}

	/*
	|--------------------------------------------------------------------------
	| for openmypage_yarn_type
	|--------------------------------------------------------------------------
	|
	*/
	function openmypage_yarn_type()
	{
		var cbo_yarn_type = $("#cbo_yarn_type").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/location_wise_yarn_stock_report_controller.php?action=yarn_type_popup&cbo_yarn_type='+cbo_yarn_type, 'Yarn Type', 'width=325px,height=370px,center=1,resize=0,scrolling=0','../../');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var selected_name=this.contentDoc.getElementById("selected_name").value;
			var selected_id=this.contentDoc.getElementById("selected_id").value;
			$("#txt_yarn_type").val(selected_name);
			$("#cbo_yarn_type").val(selected_id);
		}
	}

	/*
	|--------------------------------------------------------------------------
	| for openmypage_yarn_count
	|--------------------------------------------------------------------------
	|
	*/
	function openmypage_yarn_count()
	{
		var cbo_yarn_count = $("#cbo_yarn_count").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/location_wise_yarn_stock_report_controller.php?action=yarn_count_popup&cbo_yarn_count='+cbo_yarn_count, 'Yarn Count', 'width=325px,height=370px,center=1,resize=0,scrolling=0','../../');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var selected_name=this.contentDoc.getElementById("selected_name").value;
			var selected_id=this.contentDoc.getElementById("selected_id").value;
			$("#txt_yarn_count").val(selected_name);
			$("#cbo_yarn_count").val(selected_id);
		}
	}

	/*
	|--------------------------------------------------------------------------
	| for openmypage_composition
	|--------------------------------------------------------------------------
	|
	*/
	function openmypage_composition()
	{
		var pre_composition_id = $("#cbo_composition").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/location_wise_yarn_stock_report_controller.php?action=composition_popup&pre_composition_id='+pre_composition_id, 'Composition Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var composition_des=this.contentDoc.getElementById("hidden_composition").value;
			var composition_id=this.contentDoc.getElementById("hidden_composition_id").value;
			$("#txt_composition").val(composition_des);
			$("#cbo_composition").val(composition_id);
		}
	}

	/*
	|--------------------------------------------------------------------------
	| for openmypage_store
	|--------------------------------------------------------------------------
	|
	*/
	function openmypage_store()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}

		var company_id = $("#cbo_company_name").val();
		var cbo_store = $("#cbo_store").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/location_wise_yarn_stock_report_controller.php?action=store_popup&company_id='+company_id+'&cbo_store='+cbo_store,'Store Popup', 'width=325px,height=370px,center=1,resize=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var selected_name=this.contentDoc.getElementById("selected_name").value;
			var selected_id=this.contentDoc.getElementById("selected_id").value;
			$("#txt_store").val(selected_name);
			$("#cbo_store").val(selected_id);
		}
	}

	/*
	|--------------------------------------------------------------------------
	| for openmypage_floor
	|--------------------------------------------------------------------------
	|
	*/
	function openmypage_floor()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}

		var company_id = $("#cbo_company_name").val();
		var cbo_store = $("#cbo_store").val();
		var cbo_floor = $("#cbo_floor").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/location_wise_yarn_stock_report_controller.php?action=floor_popup&company_id='+company_id+'&cbo_store='+cbo_store+'&cbo_floor='+cbo_floor,'Floor Popup', 'width=475px,height=370px,center=1,resize=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var selected_name=this.contentDoc.getElementById("selected_name").value;
			var selected_id=this.contentDoc.getElementById("selected_id").value;
			$("#txt_floor").val(selected_name);
			$("#cbo_floor").val(selected_id);
		}
	}

	/*
	|--------------------------------------------------------------------------
	| for openmypage_room
	|--------------------------------------------------------------------------
	|
	*/
	function openmypage_room()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}

		var company_id=$("#cbo_company_name").val();
		var cbo_store = $("#cbo_store").val();
		var cbo_floor = $("#cbo_floor").val();
		var cbo_room = $("#cbo_room").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/location_wise_yarn_stock_report_controller.php?action=room_popup&company_id='+company_id+'&cbo_store='+cbo_store+'&cbo_floor='+cbo_floor+'&cbo_room='+cbo_room,'Floor Popup', 'width=595px,height=370px,center=1,resize=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var selected_name=this.contentDoc.getElementById("selected_name").value;
			var selected_id=this.contentDoc.getElementById("selected_id").value;
			$("#txt_room").val(selected_name);
			$("#cbo_room").val(selected_id);
		}
	}

	/*
	|--------------------------------------------------------------------------
	| for generate_report
	|--------------------------------------------------------------------------
	|
	*/
	function generate_report(type)
	{
		if( form_validation('cbo_company_name*txt_date_from','Company Name*Date')==false )
		{
			return;
		}

		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_supplier = $("#cbo_supplier").val();
		var cbo_dyed_type = $("#cbo_dyed_type").val();
		var cbo_yarn_type = $("#cbo_yarn_type").val();
		var cbo_yarn_count 	= $("#cbo_yarn_count").val();
		var cbo_composition = $("#cbo_composition").val();
		var txt_lot_no 	= $("#txt_lot_no").val();
		var cbo_store = $("#cbo_store").val();
		var cbo_floor = $("#cbo_floor").val();
		var cbo_room = $("#cbo_room").val();
		var value_with 	= $("#cbo_value_with").val();
		var from_date 	= $("#txt_date_from").val();
		var cbo_get_upto_qnty = $("#cbo_get_upto_qnty").val();
		var txt_qnty = $("#txt_qnty").val();
		if(cbo_get_upto_qnty!=0 && txt_qnty*1<=0)
		{
			alert("Please Insert Qty.");
			$("#txt_qnty").focus();
			return;
		}

		var data="action=generate_report&cbo_company_name="+cbo_company_name+"&cbo_supplier="+cbo_supplier+"&cbo_dyed_type="+cbo_dyed_type+"&cbo_yarn_type="+cbo_yarn_type+"&cbo_yarn_count="+cbo_yarn_count+"&cbo_composition="+cbo_composition+"&txt_lot_no="+txt_lot_no+"&cbo_store="+cbo_store+"&cbo_floor="+cbo_floor+"&cbo_room="+cbo_room+"&value_with="+value_with+"&from_date="+from_date+"&type="+type+"&get_upto_qnty="+cbo_get_upto_qnty+"&txt_qnty="+txt_qnty;
		freeze_window(3);
		http.open("POST","requires/location_wise_yarn_stock_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	/*
	|--------------------------------------------------------------------------
	| for generate_report_reponse
	|--------------------------------------------------------------------------
	|
	*/
	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split("####");
			$("#report_container2").html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//alert(response[2]);
			if(response[2]==1)
			{
				setFilterGrid("table_body",-1);
			}
			else if(response[2]==2)
			{
				setFilterGrid("table_body",-1);
			}

			show_msg('3');
			release_freezing();
		}
	}

	/*
	|--------------------------------------------------------------------------
	| for print preview
	|--------------------------------------------------------------------------
	|
	*/
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$("#table_body tr:first").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><style></style></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="350px";
		$("#table_body tr:first").show();
	}
	function generate_report_exel_only(excl_no)
	{

		if( form_validation('cbo_company_name*txt_date_from','Company Name*Date')==false )
		{
			return;
		}

		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_supplier = $("#cbo_supplier").val();
		var cbo_dyed_type = $("#cbo_dyed_type").val();
		var cbo_yarn_type = $("#cbo_yarn_type").val();
		var cbo_yarn_count 	= $("#cbo_yarn_count").val();
		var cbo_composition = $("#cbo_composition").val();
		var txt_lot_no 	= $("#txt_lot_no").val();
		var cbo_store = $("#cbo_store").val();
		var cbo_floor = $("#cbo_floor").val();
		var cbo_room = $("#cbo_room").val();
		var value_with 	= $("#cbo_value_with").val();
		var from_date 	= $("#txt_date_from").val();
		var cbo_get_upto_qnty = $("#cbo_get_upto_qnty").val();
		var txt_qnty = $("#txt_qnty").val();
		if(cbo_get_upto_qnty!=0 && txt_qnty*1<=0)
		{
			alert("Please Insert Qty.");
			$("#txt_qnty").focus();
			return;
		}

		var report_title=$( "div.form_caption" ).html();

	    //var data="action=report_generate_exel_only_3" + get_submitted_data_string('cbo_company_name*cbo_supplier*cbo_dyed_type*cbo_yarn_type*cbo_yarn_count*cbo_composition*txt_lot_no*cbo_store*cbo_floor*cbo_room*cbo_get_upto_qnty*txt_qnty',"../../../")+'&report_title='+report_title+'&value_with='+value_with;
	    var data="action=report_generate_exel_only&cbo_company_name="+cbo_company_name+"&cbo_supplier="+cbo_supplier+"&cbo_dyed_type="+cbo_dyed_type+"&cbo_yarn_type="+cbo_yarn_type+"&cbo_yarn_count="+cbo_yarn_count+"&cbo_composition="+cbo_composition+"&txt_lot_no="+txt_lot_no+"&cbo_store="+cbo_store+"&cbo_floor="+cbo_floor+"&cbo_room="+cbo_room+"&value_with="+value_with+"&from_date="+from_date+"&get_upto_qnty="+cbo_get_upto_qnty+"&txt_qnty="+txt_qnty+"&report_title="+report_title;


	  http.open("POST","requires/location_wise_yarn_stock_report_controller.php",true);
	  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	  http.send(data);
	  http.onreadystatechange = generate_report_reponse_exel_only;
	  freeze_window(2);
	}

	function generate_report_reponse_exel_only()
	{
	  if(http.readyState == 4)
	  {
	    var reponse=trim(http.responseText).split("####");
	   //$("#report_container2").html(reponse[0]);
	    if(reponse!='')
	    {
	      $('#aa1').removeAttr('href').attr('href','requires/'+reponse[0]);
	      document.getElementById('aa1').click();
	    }
	    show_msg('3');
	    release_freezing();
	  }

	}
</script>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="left">
		<? echo load_freeze_divs ("../../../",$permission);  ?>
		<form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" >
			<div style="width:100%;" align="center">
				<h3 style="width:1580px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
				<div style="width:100%;" id="content_search_panel">
					<fieldset style="width:1500px;">
						<table class="rpt_table" width="1500" cellpadding="0" cellspacing="0" border="1" rules="all">
							<thead>
								<tr>
									<th>Company</th>
									<th>Supplier</th>
									<th>Dyed Type</th>
									<th>Yarn Type</th>
									<th>Count</th>
									<th>Composition</th>
									<th>Lot</th>
									<th>Store</th>
									<th>Floor</th>
									<th>Room</th>
									<th>Get Upto</th>
									<th>Qty.</th>
									<th>Value</th>
									<th class="must_entry_caption">Date</th>
									<th colspan="2"><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" /></th>
								</tr>
							</thead>
							<tr class="general">
								<td>
                                    <input type="text" id="txt_company" name="txt_company" class="text_boxes" style="width:110px" value="" onDblClick="openmypage_company();" placeholder="Browse" readonly />
                                    <input type="hidden" id="cbo_company_name" name="cbo_company_name" />
								</td>
								<td>
                                    <input type="text" id="txt_supplier" name="txt_supplier" class="text_boxes" style="width:100px" value="" onDblClick="openmypage_supplier();" placeholder="Browse" readonly />
                                    <input type="hidden" id="cbo_supplier" name="cbo_supplier" class="text_boxes" />
								</td>
								<td align="center">
									<?
									$dyedType=array(0=>'All',1=>'Dyed Yarn',2=>'Non Dyed Yarn');
									echo create_drop_down( "cbo_dyed_type", 80, $dyedType,"", 0, "--Select--", $selected, "", "","");
									?>
								</td>
								<td>
                                    <input style="width:100px;" name="txt_yarn_type" id="txt_yarn_type" class="text_boxes" onDblClick="openmypage_yarn_type()" placeholder="Browse" readonly />
                                    <input type="hidden" name="cbo_yarn_type" id="cbo_yarn_type" class="text_boxes"/>
								</td>
								<td>
                                    <input style="width:80px;" name="txt_yarn_count" id="txt_yarn_count" class="text_boxes" onDblClick="openmypage_yarn_count()" placeholder="Browse" readonly />
                                    <input type="hidden" name="cbo_yarn_count" id="cbo_yarn_count" class="text_boxes"/>
								</td>
								<td>
									<input type="text" id="txt_composition" name="txt_composition" class="text_boxes" style="width:100px" value="" onDblClick="openmypage_composition();" placeholder="Browse" readonly />
									<input type="hidden" id="cbo_composition" name="cbo_composition" />
								</td>
								<td>
									<input type="text" id="txt_lot_no" name="txt_lot_no" class="text_boxes" style="width:60px" value="" />
								</td>
                                <td>
                                    <input style="width:100px;" name="txt_store" id="txt_store" class="text_boxes" onDblClick="openmypage_store()" placeholder="Browse" readonly />
                                    <input type="hidden" name="cbo_store" id="cbo_store" class="text_boxes"/>
                                </td>
                                <td>
                                    <input style="width:100px;" name="txt_floor" id="txt_floor" class="text_boxes" onDblClick="openmypage_floor()" placeholder="Browse" readonly />
                                    <input type="hidden" name="cbo_floor" id="cbo_floor" class="text_boxes"/>
                                </td>
                                <td>
                                    <input style="width:100px;" name="txt_room" id="txt_room" class="text_boxes" onDblClick="openmypage_room()" placeholder="Browse" readonly />
                                    <input type="hidden" name="cbo_room" id="cbo_room" class="text_boxes"/>
                                </td>
                                <td>
		                            <?
		                                echo create_drop_down( "cbo_get_upto_qnty", 70, $get_upto,"", 1, "- All -", 0, "",0 );
		                            ?>
		                        </td>
		                        <td>
		                            <input type="text" id="txt_qnty" name="txt_qnty" class="text_boxes_numeric" style="width:30px" value="" />
		                        </td>
								<td>
									<?
									$valueWithArr=array(0=>'Value With 0',1=>'Value Without 0');
									echo create_drop_down( "cbo_value_with", 110, $valueWithArr,"",0,"",1,"","","");
									?>
								</td>
								<td align="center">
									<input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y");?>" class="datepicker" style="width:55px" readonly/>
								</td>
								<td colspan="2">
									<input type="button" name="search" id="search1" value="Show" onClick="generate_report(1)" style="width:60px;" class="formbutton" />
									<input type="button" name="search2" id="search2" value="Show 2" onClick="generate_report(2)" style="width:60px;" class="formbutton" />
									<input type="button" name="search3" id="search3" value="Excel Only" onClick="generate_report_exel_only(1)" style="width:70px;" class="formbutton" />
									<a href="" id="aa1"></a>

								</td>
							</tr>
							<tr>
								<td colspan="11" align="center"><? echo load_month_buttons(1); ?></td>
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
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$("#cbo_value_with").val(1);
</script>
</html>