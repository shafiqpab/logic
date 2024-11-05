<?
/*
Purpose			: 	This form will create for Comparative Statement Report
Functionality	:
JS Functions	:
Created by		:	Feroz Mhamud
Creation date 	: 	27-10-2019
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
echo load_html_head_contents("Supplier List", "../../", 1, 1, $unicode,1,'');
?>
<script>
	var permission='<? echo $permission; ?>';
	function getItemCode(){
		if(form_validation('cbo_item_category','Item Category') == false){
			return;
		}
		else{
			var item_category=document.getElementById('cbo_item_category').value;
			var item_group =document.getElementById('cbo_item_group').value
			var page_link='requires/comparative_statement_controller.php?action=openpopup_item_code&item_category='+item_category;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Item Code','width=450px,height=400px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var itemdescription=this.contentDoc.getElementById("itemdescription").value;
				var itemdescription=itemdescription.split("__");
				document.getElementById('item_code').value=itemdescription[1];
				//document.getElementById('search_item_description').value=itemdescription[1];
			}
		}
	}

	function getItemDescription()
	{
		if(form_validation('cbo_item_category','Item Category') == false){
			return;
		}
		else
		{
			var item_category=document.getElementById('cbo_item_category').value;
			var item_group=document.getElementById('cbo_item_group').value;
			var page_link='requires/comparative_statement_controller.php?action=openpopup_item_description&item_category='+item_category+'&item_group='+item_group;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Item Description','width=450px,height=400px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var item_id=this.contentDoc.getElementById("txt_selected_id").value;
				var itemdesc=this.contentDoc.getElementById("txt_selected").value;

				document.getElementById('hidden_item_description').value=item_id;
				document.getElementById('search_item_description').value=itemdesc;
			}
		}
	}

	function fn_report_generated(action_type)
	{
		var report_title=$( "div.form_caption" ).html();
		var data="action="+action_type+get_submitted_data_string('cbo_item_category*cbo_item_group*search_item_description*insert_date*effective_from',"../../")+'&report_title='+report_title;
		//alert(data);
		freeze_window(3);
		http.open("POST","requires/comparative_statement_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function(){
			if(http.readyState == 4)
			{
				var reponse=trim(http.responseText).split("****");
				//var tot_rows=reponse[2];
				$('#report_container2').html(reponse[0]);
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
				//document.getElementById('report_container').innerHTML=report_convert_button('../../');
				show_msg('3');
				release_freezing();
				if(action_type=='report_generate')setFilterGrid('table_body',-1);
			}

		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		$(".flt").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
		$(".flt").show();
	}
</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../");  ?>
    <form name="supplierWiseRate_1" id="supplierWiseRate_1" autocomplete="off" >
        <h3 style="width:800px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" >
            <fieldset style="width:800px;">
            <table class="rpt_table" width="800" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="130">Item Category</th>
                        <th width="130">Item Group</th>
                        <th with="130">Item Description</th>                        
						<th width="130">Insert Date</th>
                        <th width="130">Effective From</th>
                        <th width="130"><input type="reset" name="reset" id="reset" value="Reset" style="width:60px" class="formbutton" /></th>
                    </tr>
                </thead>
                <tr class="general">
                    <td><? echo create_drop_down( "cbo_item_category", 130,$item_category,"", '1', '---- Select ----', 0, "load_drop_down('requires/comparative_statement_controller',this.value, 'load_drop_down_item_group', 'td_item_group');load_drop_down('requires/comparative_statement_controller',this.value, 'load_drop_down_supplier', 'td_supplier')","","","","","1,2,3,12,13,14,24,25" ); ?></td>
                    <td id="td_item_group">
                    	<? echo create_drop_down( "cbo_item_group", 130,$blank_array, '', 1, '---- Select ----'  ); ?>
                    </td>
                    <td width="130">
                    	<input type="text" placeholder="Browse/Write"  id="search_item_description"  name="search_item_description" class="text_boxes" onDblClick="getItemDescription()"/>
                        <input type="hidden" id="hidden_item_description" name="hidden_item_description" class="text_boxes"/>
                    </td>
                    <td>
                    	<input type="text" name="insert_date" id="insert_date" class="datepicker" style="width:120px" placeholder="Date" />
                    </td>
					<td>
						<input type="text" name="effective_from" id="effective_from" class="datepicker" style="width:120px" placeholder="Date" />
					</td>
                    <td>
                    	<input type="button" name="search" id="search" value="Show" onClick="fn_report_generated('report_generate')" style="width:60px" class="formbutton" />
                    </td>
                </tr>
            </table>
        </fieldset>
        </div>
        </form>
           <div id="report_container" align="center"></div>
           <div id="report_container2">
       </div>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>