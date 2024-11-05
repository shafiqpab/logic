<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Item Wise Purchase Report
				
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	30-10-2013
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
echo load_html_head_contents("Periodical Purchase Report","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	function openmypage_item_account()
	{
		 var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_item_category_id').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/periodical_purchase_report_controller.php?action=item_account_popup&data='+data,'Item Account Popup', 'width=900px,height=420px,center=1,resize=0','../../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("item_account_id");
			var theemailv=this.contentDoc.getElementById("item_account_val");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_item_account_id").value=response[0];
			    document.getElementById("txt_item_acc").value=theemailv.value;
				//reset_form();
				get_php_form_data( response[0], "item_account_dtls_popup", "requires/periodical_purchase_report_controller" );
				release_freezing();
			}
		}
	}
	
	function openmypage_item_group()
	{
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_item_category_id').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/periodical_purchase_report_controller.php?action=item_group_popup&data='+data,'Item Group Popup', 'width=450px,height=350px,center=1,resize=0,scrolling=0','../../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("item_name_id");
			var response=theemail.value.split('_');
			//alert (response[1]);
			if (theemail.value!="")
			{
				//freeze_window(5);
				document.getElementById("txt_item_group_id").value=response[0];
				document.getElementById("txt_item_group").value=response[1];
				release_freezing();
			}
		}
	}
	
	function generate_report(operation)
	{
		if( form_validation('cbo_company_name*cbo_store_name*txt_date_from*txt_date_to','Company Name*Store Name*From Date*To Date')==false )
		{
			return;
		} 
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_item_category_id = $("#cbo_item_category_id").val();
		var txt_product_id = $("#txt_product_id").val();
		var item_group_id = $("#txt_item_group_id").val();
		var item_account_id = $("#txt_item_account_id").val();
		var txt_item_code = $("#txt_item_code").val();
		var cbo_supplier_name = $("#cbo_supplier_name").val();
		var cbo_store_name = $("#cbo_store_name").val();
		var from_date = $("#txt_date_from").val();
		var to_date = $("#txt_date_to").val();
		
		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_store_name="+cbo_store_name+"&cbo_item_category_id="+cbo_item_category_id+"&txt_item_code="+txt_item_code+"&txt_product_id="+txt_product_id+"&from_date="+from_date+"&to_date="+to_date+"&item_account_id="+item_account_id+"&item_group_id="+item_group_id+"&cbo_supplier_name="+cbo_supplier_name;
		var data="action=generate_report"+dataString;
		//alert (data);
		freeze_window(operation);
		http.open("POST","requires/periodical_purchase_report_controller.php",true);
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
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="250px";
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
	function loadnewstore( value )
	{
		load_drop_down( 'requires/periodical_purchase_report_controller', value, 'load_drop_down_store', 'store_td' );
		set_multiselect('cbo_store_name','0','0','','');
	}

	function set_multiselect_local( fld_id, max_selection, is_update, update_values, on_close_fnc_param )
	{
		
		if (!on_close_fnc_param) var on_close_fnc_param="";
		else
		{
			on_close_fnc_param=on_close_fnc_param.split("*");
		}
		
		fld_id=fld_id.split("*");
		max_selection=max_selection.split("*");
		update_values=update_values.split("*");
		
		for ( var i=0; i<fld_id.length; i++ )  
		{
			var html_list="";
			var elm_width=document.getElementById(fld_id[i]).offsetWidth-12;  
			var elm_height=document.getElementById(fld_id[i]).offsetHeight;
			var onc="'"+on_close_fnc_param[i]+"'";
			var j=0;
			var opts = $('#'+fld_id[i])[0].options;
		 	if (max_selection[i]==0) max_selection[i]=opts.length;
			var max_select=max_selection[i];
			var closed='<span style="position:absolute; right:5px; width:15px;"><a href="##" style="text-decoration:none" onclick="disappear_list_local('+fld_id[i]+','+onc+')"> X </a></span>';
			html_list ='<div class="multiselect_dropdown_table" id="multi_select_'+fld_id[i]+'" style="display:none; width:'+((elm_width*1)+12)+'px; max-height:'+170+'px; min-height:'+50+'px; position:absolute;"><table border="1" width="100%" class="multiselect_dropdown_table_top" >';
			html_list=html_list+'<thead><tr><th colspan="2" height="20" id="multiselect_dropdown_table_header'+fld_id[i]+'" align="center"><b>Select Max '+max_select+' Item</b>'+closed+'</th></tr></thead></table><div class="mylistview" style="overflow-y:scroll;max-height:'+140+'px; min-height:'+20+'px;"><table border="1" width="100%" id="table_body'+fld_id[i]+'" class="multiselect_dropdown_table_bottom" >';
			var array = $.map(opts, function( elem ) {
				j++;	
				html_list=html_list+'<tr id="tr'+fld_id[i]+elem.value+'" class="multiselect_mouse_out" onMouseOver="make_selection('+fld_id[i]+','+elem.value+')" onMouseOut="make_selection_remove('+fld_id[i]+','+elem.value+')" ><td width="15"><input type="checkbox" onclick="add_multiselect_listitems('+fld_id[i]+', '+elem.value+','+opts.length+','+max_selection[i]+')" id="'+fld_id[i]+elem.value+'"></td><td onclick="add_multiselect_listitems('+fld_id[i]+', '+elem.value+','+opts.length+','+max_selection[i]+')">'+elem.text+'</td></tr>';				 
			});
			html_list=html_list+'</table></div></div>'; 
			$('#'+fld_id[i]).replaceWith('<input id="show_text'+fld_id[i]+'" placeholder="Select Multiple Item" readonly type="text" class="text_boxes" style="text-align:center; width:'+elm_width+'px; height:'+elm_height+'px" onclick="append_list('+fld_id[i]+')"/>'+html_list +'<input type="hidden" id="'+fld_id[i]+'" class="text_boxes" />');
			
		}  
		 
	}
	function getStoreId() 
	{
	    var company_id = document.getElementById('cbo_company_name').value;
	    var item_category_id = document.getElementById('cbo_item_category_id').value;
	    if(company_id !='') {
		  var data="action=load_drop_down_store&data="+company_id+'_'+item_category_id;
		  //alert(data);die;
		  http.open("POST","requires/periodical_purchase_report_controller.php",true);
		  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		  http.send(data); 
		  http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText);
	              $('#store_td').html(response);
	              set_multiselect('cbo_store_name','0','0','','0'); 
				  /*setTimeout[($("#floor_td a").attr("onclick","disappear_list(cbo_floor_id,'0');getFloorId();") ,3000)]; */
	          }			 
	      };
	    }         
	}

	function disappear_list_local(fld,close_fnc)
	{
		var company= document.getElementById("cbo_company_name").value;
	  	loadnewstore( company + '_' + fld.value );
		$('#multi_select_'+fld.id).hide('slow');
	}

	function openmypage_supplier()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}

		var data=document.getElementById('cbo_company_name').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/periodical_purchase_report_controller.php?action=supplier_popup&data='+data,'Supplier Popup', 'width=400px,height=350px,center=1,resize=0,scrolling=0','../../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("supplier_data");
			var response=theemail.value.split('_');
			//alert (response[1]);
			if (theemail.value!="")
			{
				//freeze_window(5);
				document.getElementById("cbo_supplier_name").value=response[0];
				document.getElementById("cbo_supplier").value=response[1];
				release_freezing();
			}
		}

		//var company= document.getElementById("cbo_company_name").value;
		//load_drop_down( 'requires/periodical_purchase_report_controller', company, 'load_drop_down_supplier', 'supplier_td' );
	}

</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?><br />    		 
        <form name="itemwisepurchase_1" id="itemwisepurchase_1" autocomplete="off" > 
         <h3 style="width:950px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:950px" >      
            <fieldset>  
                <table class="rpt_table" width="950" cellpadding="0" cellspacing="0">
                    <thead>
                        <th width="130" class="must_entry_caption">Company</th>
                        <th width="120" >Item Category</th>
                        <th width="120" class="must_entry_caption">Store</th>
                        <th width="90">Item Group</th>
                        <th width="90">Item Account</th>
                        <th width="120">Supplier</th>                            
                        <th class="must_entry_caption">Purchase Date</th>
                        <th width="60"><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('itemwisepurchase_1','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <? 
                                    echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "" );
                                ?>                            
                            </td>
                           <td id="category_td">
								<?php 
									echo create_drop_down( "cbo_item_category_id", 120,$item_category,"", 0, "", $selected, "","","","","","1,2,3,4,5,6,7,12,13,14");
                                ?> 
                                <input type="hidden" name="txt_product_id" id="txt_product_id" style="width:90px;"/>               
                          </td>
                           <td width="120" id="store_td">
                                <? 
                                    echo create_drop_down( "cbo_store_name", 120, $blank_array,"", 0, "", 1, "" );
                                ?>
                           </td>
                            <td>
                            	<input style="width:90px;" name="txt_item_group" id="txt_item_group" class="text_boxes" onDblClick="openmypage_item_group()" placeholder="Browse" readonly />
                                <input type="hidden" name="txt_item_group_id" id="txt_item_group_id" style="width:90px;"/>  
                            </td>
                            <td>
                            	<input style="width:90px;" name="txt_item_acc" id="txt_item_acc" class="text_boxes" onDblClick="openmypage_item_account()" placeholder="Browse" readonly />
                                <input type="hidden" name="txt_item_account_id" id="txt_item_account_id" style="width:90px;"/>
                            </td>
                           <td width="120" id="supplier_td">
                                <? 
                                    //echo create_drop_down( "cbo_supplier_name", 120, $blank_array,"", 1, "--Select Supplier--", "", "" );
                                ?>
                                <input style="width:100px;" name="cbo_supplier" id="cbo_supplier" class="text_boxes" onDblClick="openmypage_supplier()" placeholder="Browse" readonly />
                                <input name="cbo_supplier_name" id="cbo_supplier_name" type="hidden" value="" />
                           </td>
                            <td>
                                <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:63px;"/>                    							
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:63px;"/>                        
                            </td>
                            <td>
                                <input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:60px" class="formbutton" />
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
            <br /> 
                <div id="report_container" align="center"></div>
                <div id="report_container2"></div> 
        </form>    
    </div>
</body>  
<script>
set_multiselect('cbo_item_category_id','0','0','','');
set_multiselect('cbo_store_name','0','0','','');
/*set_multiselect_local('cbo_item_category_id','0','0','','');*/

setTimeout[($("#category_td a").attr("onclick","disappear_list(cbo_item_category_id,'0');getStoreId();") ,3000)]; 
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
