<?
/*-------------------------------------------- Comments
Purpose			: 	This report will decscribe item wise Inquery that are created from Library

Functionality	:
JS Functions	:
Created by		:	Mohammad Shafiqur Rahman
Creation date 	: 	18-12-2018
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

$user_id = $_SESSION['logic_erp']['user_id'];

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("General Store Item Inquery Report","../../../", 1, 1, $unicode,1,1);
?>
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function openmypage_item()
	{
		if( form_validation('cbo_company_name*cbo_item_cat','Company Name*Item Category')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();
		var cbo_item_cat = $("#cbo_item_cat").val();

        if($("#txt_item_group").val()){
            var txt_item_group = $("#txt_item_group").val();
            var txt_item_group_id = $("#txt_item_group_id").val();
            //alert(txt_item_group_id);
        }else{
            //alert("hello mai");
            var txt_item_group = "";
            var txt_item_group_id = "";
        }
        if($("#txt_item_sub_group_id").val()){
            var txt_item_sub_group_id = $("#txt_item_sub_group_id").val();
        }else{
            var txt_item_sub_group_id = "";
        }

        if(txt_item_group !="" && txt_item_sub_group_id != ""){
            var page_link='requires/item_inquiry_report_controller.php?action=item_description_search&company='+company+'&cbo_item_cat='+cbo_item_cat+'&txt_item_group='+txt_item_group+'&txt_item_group_id='+txt_item_group_id+'&txt_item_sub_group_id='+txt_item_sub_group_id;
        }else if(txt_item_group !="" && txt_item_sub_group_id == ""){
            var page_link='requires/item_inquiry_report_controller.php?action=item_description_search&company='+company+'&cbo_item_cat='+cbo_item_cat+'&txt_item_group='+txt_item_group+'&txt_item_group_id='+txt_item_group_id;
        }else if(txt_item_group =="" && txt_item_sub_group_id != ""){
            var page_link='requires/item_inquiry_report_controller.php?action=item_description_search&company='+company+'&cbo_item_cat='+cbo_item_cat+'&txt_item_sub_group_id='+txt_item_sub_group_id;
        }else{
            var page_link='requires/item_inquiry_report_controller.php?action=item_description_search&company='+company+'&cbo_item_cat='+cbo_item_cat;
        }

		//var page_link='requires/item_inquiry_report_controller.php?action=item_description_search&company='+company+'&cbo_item_cat='+cbo_item_cat+'&txt_item_group='+txt_item_group+'&txt_item_sub_group_id='+txt_item_sub_group_id;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=670px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var prodID=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			var prodNo=this.contentDoc.getElementById("txt_selected_no").value; // product Serial No
			$("#txt_product").val(prodDescription);
			$("#txt_product_id").val(prodID);
			$("#txt_product_no").val(prodNo);
		}
	}

    function openmypage_itemgroup()
	{
        //alert("hello kitti");
		if( form_validation('cbo_company_name*cbo_item_cat','Company Name*Item Category')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();
		var cbo_item_category_id = $("#cbo_item_cat").val();
		var txt_item_group = $("#txt_item_group").val();
		var txt_item_group_id = $("#txt_item_group_id").val();
		var txt_item_group_no = $("#txt_item_group_no").val();
		var page_link='requires/item_inquiry_report_controller.php?action=item_group_search_popup&company='+company+'&cbo_item_category_id='+cbo_item_category_id+'&txt_item_group='+txt_item_group+'&txt_item_group_id='+txt_item_group_id+'&txt_item_group_no='+txt_item_group_no;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=450px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var item_group_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var item_group_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var item_group_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			$("#txt_item_group").val(item_group_des);
			$("#txt_item_group_id").val(item_group_id);
			$("#txt_item_group_no").val(item_group_no);
		}
	}

    function openmypage_itemSubgroup()
	{
		if( form_validation('cbo_company_name*cbo_item_cat','Company Name*Item Category')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();
		var cbo_item_category_id = $("#cbo_item_cat").val();
		var txt_item_group = $("#txt_item_group").val();
		var txt_item_group_id = $("#txt_item_group_id").val();
		var txt_item_group_no = $("#txt_item_group_no").val();
		var txt_item_sub_group = $("#txt_item_sub_group").val();
		var txt_item_sub_group_id = $("#txt_item_sub_group_id").val();
		var txt_item_sub_group_no = $("#txt_item_sub_group_no").val();
		var page_link='requires/item_inquiry_report_controller.php?action=item_sub_group_search_popup&company='+company+'&cbo_item_category_id='+cbo_item_category_id+'&txt_item_sub_group='+txt_item_sub_group+'&txt_item_sub_group_id='+txt_item_sub_group_id+'&txt_item_sub_group_no='+txt_item_sub_group_no+'&txt_item_group='+txt_item_group+'&txt_item_group_id='+txt_item_group_id+'&txt_item_group_no='+txt_item_group_no;
		var title="Search Item Sub Group Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=320px,height=400px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var item_sub_group_id=this.contentDoc.getElementById("txt_selected_id").value; // sub group code
			var item_sub_group_des=this.contentDoc.getElementById("txt_selected").value; // sub group name
			var item_sub_group_no=this.contentDoc.getElementById("txt_selected_no").value; // sub group
			//alert(style_no);
			$("#txt_item_sub_group").val(item_sub_group_des);
			$("#txt_item_sub_group_id").val(item_sub_group_id);
			$("#txt_item_sub_group_no").val(item_sub_group_no);
		}
	}

	function generate_report(operation)
	{
		if( form_validation('cbo_company_name*cbo_item_cat','Company Name*Item Category')==false )
		{
			return;
		}
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_item_cat = $("#cbo_item_cat").val();
		var txt_product_id = $("#txt_product_id").val();
		var txt_item_group_id = $("#txt_item_group_id").val();
		var txt_item_sub_group_id = $("#txt_item_sub_group_id").val();
		var txt_item_sub_group = $("#txt_item_sub_group").val();
		var report_title=$( "div.form_caption" ).html();

		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_item_cat="+cbo_item_cat+"&txt_product_id="+txt_product_id+"&txt_item_group_id="+txt_item_group_id+"&txt_item_sub_group_id="+txt_item_sub_group_id+"&txt_item_sub_group="+txt_item_sub_group+"&report_title="+report_title;
		var data="action=generate_report"+dataString;
        //alert(dataString);
		freeze_window(operation);
		http.open("POST","requires/item_inquiry_report_controller.php",true);
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
			setFilterGrid('item_inquiry_rpt_tbl_body',-1);
			release_freezing();
		}
	}

	function new_window()
	{
			$(".flt").hide();
			document.getElementById('scroll_body1').style.overflow="auto";
			document.getElementById('scroll_body1').style.maxHeight="none";
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close();
			$(".flt").show();
			document.getElementById('scroll_body1').style.overflow="auto";
			document.getElementById('scroll_body1').style.maxHeight="350px";
	}

	function change_color(v_id,e_color)
	{
		//alert(e_color);
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
	}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission);  ?>
    <form name="item_inquiry_report" id="item_inquiry_report" autocomplete="off" >
    <h3 style="width:850px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div id="content_search_panel" style="width:100%;" align="center">
        <fieldset style="width:850px;">
			<table class="rpt_table" width="850" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th width="150" class="must_entry_caption">Company</th>
                        <th width="150" class="must_entry_caption">Item Category</th>
                        <th width="130">Item Group</th>
                        <th width="130">Sub Group</th>
                        <th width="140">Item Description</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('item_inquiry_report','report_container*report_container2','','','')" /></th>
                    </tr>
                </thead>
                <tr class="general" align="center">
                    <td>
                            <?
                               echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/item_inquiry_report_controller', this.value, 'load_drop_down_store', 'store_td' );" );
                            ?>
                    </td>
                    <td align="center">
                            <?
								echo create_drop_down( "cbo_item_cat", 150, $item_category,"", 1, "-- Select Item --", $selected, "",0,"" );
							?>
                    </td>
                    <td align="center">
                        <input style="width:120px;"  name="txt_item_group" id="txt_item_group" onDblClick="openmypage_itemgroup()" class="text_boxes" placeholder="Browse"/>
                        <input type="hidden" name="txt_item_group_id" id="txt_item_group_id"/>
                          <input type="hidden" name="txt_item_group_no" id="txt_item_group_no"/>
                    </td>
                     <td align="center">
                        <input style="width:120px;"  name="txt_item_sub_group" id="txt_item_sub_group" onDblClick="openmypage_itemSubgroup()" class="text_boxes" placeholder="Browse"/>
                        <input type="hidden" name="txt_item_sub_group_id" id="txt_item_sub_group_id"/>
                        <input type="hidden" name="txt_item_sub_group_no" id="txt_item_sub_group_no"/>
                    </td>
                    <td align="center">
                        <input style="width:130px;"  name="txt_product" id="txt_product"  ondblclick="openmypage_item()"  class="text_boxes" placeholder="Dubble Click For Item"  readonly />
                        <input type="hidden" name="txt_product_id" id="txt_product_id"/>   <input type="hidden" name="txt_product_no" id="txt_product_no"/>
                    </td>


                    <td>
                        <input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:80px" class="formbutton" />
                    </td>
                </tr>
                <!--<tr>
                    <td colspan="6"><? echo load_month_buttons(1);  ?></td>
                </tr>-->
            </table>
        </fieldset>
    </div>
    <br />

        <!-- Result Contain Start-------------------------------------------------------------------->

        	<div id="report_container" align="center"></div>
            <div id="report_container2"></div>

        <!-- Result Contain END-------------------------------------------------------------------->


    </form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
