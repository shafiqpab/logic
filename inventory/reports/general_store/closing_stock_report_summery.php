<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Closing Stock summery Report
				
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	13-05-2023
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
echo load_html_head_contents("Closing Stock Summery Report","../../../", 1, 1, $unicode,1,''); 
//var_dump($item_category);
$user_id=$_SESSION['logic_erp']['user_id'];
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	function generate_report(operation)
	{
		if( form_validation('cbo_company_name*cbo_item_category_id*txt_date_from*txt_date_to','Company Name*Item Category*Date From*Date TO')==false )
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html(); 
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_item_category_id = $("#cbo_item_category_id").val();
		var item_group_id = $("#txt_item_group_id").val();
		var cbo_store_name = $("#cbo_store_name").val();
		var from_date = $("#txt_date_from").val();
		var to_date = $("#txt_date_to").val();
		
		
		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_store_name="+cbo_store_name+"&cbo_item_category_id="+cbo_item_category_id+"&from_date="+from_date+"&to_date="+to_date+"&item_group_id="+item_group_id+"&report_title="+report_title+"&report_type="+operation;
		var data="action=generate_report"+dataString;
		//alert (data);return;
		freeze_window(operation);
		http.open("POST","requires/closing_stock_report_summery_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
			if(reponse[2] == 11) 
			{
				if(reponse[0]!='')
				{
					$('#aa1').removeAttr('href').attr('href','requires/'+reponse[1]);
					document.getElementById('aa1').click();
				}				
			}
			else	
			{
				$("#report_container2").html(reponse[0]); 
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			}
			//alert();
			if(reponse[2]!=8) setFilterGrid("table_body",-1);
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none"; 
		//$('#scroll_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		//document.getElementById('scroll_body').style.overflow="auto"; 
		//document.getElementById('scroll_body').style.maxHeight="350px";
        //$('#scroll_body tr:first').show();
	}
	
	
	
	function openmypage_itemgroup()
	{
		if( form_validation('cbo_company_name*cbo_item_category_id','Company Name*Item Category')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var cbo_item_category_id = $("#cbo_item_category_id").val();
		var txt_item_group = $("#txt_item_group").val();
		var txt_item_group_id = $("#txt_item_group_id").val();
		var txt_item_group_no = $("#txt_item_group_no").val();
		var page_link='requires/closing_stock_report_summery_controller.php?action=item_group_such_popup&company='+company+'&cbo_item_category_id='+cbo_item_category_id+'&txt_item_group='+txt_item_group+'&txt_item_group_id='+txt_item_group_id+'&txt_item_group_no='+txt_item_group_no;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=320px,height=370px,center=1,resize=0,scrolling=0','../../')
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
	
	function getStoreLoad() 
	{  
		var company_id = document.getElementById('cbo_company_name').value;
		load_drop_down( 'requires/closing_stock_report_summery_controller', company_id+'**'+$('#cbo_item_category_id').val() , 'load_drop_down_store', 'store_td' );
	}
	

</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?><br />    		 
        <form name="closingstock_1" id="closingstock_1" autocomplete="off" > 
         <h3 style="width:1060px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:1060px" >      
            <fieldset>  
                <table class="rpt_table" width="1060" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <th width="210" class="must_entry_caption">Company</th>
                        <th width="160">Store</th>
                        <th width="210" class="must_entry_caption">Item Category</th>
                        <th width="160">Item Group</th>
                        <th class="must_entry_caption">Date Range</th>
                        <th width="110"><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('closingstock_1','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td align="center" id="td_company">
                                <? 
                                    echo create_drop_down( "cbo_company_name", 200, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/closing_stock_report_summery_controller', this.value+'**'+$('#cbo_yes_no').val() , 'load_drop_down_store', 'store_td' );" );//load_drop_down( 'requires/closing_stock_report_summery_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );
									
                                ?> 
                                <input type="hidden" name="variable_string_inventory" id="variable_string_inventory" />                           
                            </td>
                            <td id="store_td" align="center">
							<?
								$userCredential = sql_select("SELECT store_location_id, item_cate_id FROM user_passwd where id=$user_id");
								$store_cond = ($userCredential[0][csf("store_location_id")]) ? " and a.id in (".$userCredential[0][csf("store_location_id")].")" : "" ; 
                                echo create_drop_down( "cbo_store_name", 150, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and b.CATEGORY_TYPE in(".implode(",",array_flip($general_item_category)).") $store_cond group by a.id,a.store_name","id,store_name", 1, "--Select Store--", "", "",0 );
                            ?>
                           	</td>
                            <td align="center">
                            <?
                            $userCredential = sql_select("SELECT store_location_id, item_cate_id FROM user_passwd where id=$user_id");
                           	$item_cat_cond = ($userCredential[0][csf("item_cate_id")]) ? $userCredential[0][csf("item_cate_id")] : "" ;
							$item_cat_cond = implode(",",array_diff(explode(",",$item_cat_cond), array("4")));
							//create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name ) 4
                            echo create_drop_down( "cbo_item_category_id", 200, $general_item_category,"", 0, "", 0, "", 0,"$item_cat_cond", "", "", "");
                            ?>
                            
                            </td>
                            <td align="center">
                            <input style="width:130px;"  name="txt_item_group" id="txt_item_group" onDblClick="openmypage_itemgroup()" class="text_boxes" placeholder="Browse"/>   
                            <input type="hidden" name="txt_item_group_id" id="txt_item_group_id"/>  
                              <input type="hidden" name="txt_item_group_no" id="txt_item_group_no"/>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:70px;" readonly />                    							
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:70px;" readonly />                        
                            </td>
                            <td align="center">
                                <input type="button" name="search" id="show" value="Show" onClick="generate_report(3)" style="width:100px;" class="formbutton"/>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" align="center"><? echo load_month_buttons(1);  ?></td>
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
	set_multiselect('cbo_company_name*cbo_item_category_id','0*0','0*0','0*0','0*0');
	setTimeout[($("#td_company a").attr("onclick","getStoreLoad();") ,3000)];  
</script> 
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
