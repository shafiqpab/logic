<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Item Category List Report
				
Functionality	:	
JS Functions	:
Created by		:	Md: Didarul Alam 
Creation date 	: 	21/06/2016
Updated by 		: 	AL-Hassan	
Update date		: 	22/08/2023	   
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
echo load_html_head_contents("Item Category List", "../../", 1, 1, $unicode,1,'');
?>	
<script>
    var permission='<? echo $permission; ?>';
       
    function openmypage()
	{
		if ( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		else
		{
			var category=document.getElementById('cbo_item_category').value;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/item_category_list_controller.php?category='+category+'&action=order_popup','Search Group Code', 'width=950px,height=400px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemail=this.contentDoc.getElementById("item_id");
				document.getElementById('item_group_id').value = theemail.value;
				get_php_form_data(theemail.value, "load_php_popup_to_form", "requires/item_category_list_controller" );
				set_button_status(0, permission, 'fnc_item_creation',1);
				release_freezing();
			}
		}
	}

	function openmypagedescriction()
	{
		if ( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		else
		{
			var cbo_company_name=document.getElementById('cbo_company_name').value;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/item_category_list_controller.php?cbo_company_name='+cbo_company_name+'&action=item_popup','Search Item Pop-up', 'width=850px,height=400px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemail=this.contentDoc.getElementById("hidden_item");
				//alert(theemail.value);
				document.getElementById('txt_item_description').value = theemail.value;
				release_freezing();
			}
		}
	}
    
	function fn_report_generated(type)
	{       
		
        if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else
		{	
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_item_category*item_group_id*txt_item_description',"../../")+'&report_title='+report_title+'&type='+type;
			freeze_window(3);
			http.open("POST","requires/item_category_list_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}

	
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####");
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';		
			
			//setFilterGrid("table_body",-1,'');
			setFilterGrid("tbl_suppler_list2",-1,'');
	 		show_msg('3');
			release_freezing();
		}
	}

 	function print_report_button_setting(report_ids)
	{
		$("#show").hide();
		$("#show2").hide();
		
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==108) $("#show").show();
			if(report_id[k]==195) $("#show2").show();
		}
	}
	function fnc_load_print_report_setting(data)
	{
		get_php_form_data(data,'report_formate_setting','requires/item_category_list_controller');
	}
</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../");  ?>
        <form id="category_list">
        <h3 style="width:1100px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
        <div id="content_search_panel" style="width:1100px" >      
            <fieldset>  
                <table cellpadding="1" cellspacing="1" width="100%" >
                    <thead>
                    <tr>
                        <td width="80" class="must_entry_caption">Company</td>
                        <td width="170"> <input type="hidden" name="txt_hidden_item_group" id="txt_hidden_item_group" value="" ><input type="hidden" name="update_id" id="update_id" > <input type="hidden" name="update_status_active" id="update_status_active" > 
                            <? 
                                echo create_drop_down( "cbo_company_name", 150, "select company_name,id from lib_company comp where is_deleted=0  and status_active=1 $company_cond  order by company_name",'id,company_name', 1, '--- Select Company ---', 0, "fnc_load_print_report_setting(this.value)" );
                            ?> 
                        </td>
                        <td width="80" class="">Item Category</td>
                        <td width="170"><input type="hidden" name="set_id" id="set_id" >                                 
                            <?
                                echo create_drop_down( "cbo_item_category", 150, $item_category,"", "1", "--- Select---", 0, "","","","","","1,2,3,12,13,14,24,25" );
                            ?>
                        </td>
                        <td width="80" class="">Item Group</td>
                        <td width="190"><input type="hidden" id="item_group_id" />
                            <input name="txt_item_group" ID="txt_item_group"   style="width:140px" value="" class="text_boxes" autocomplete="off" maxlength="50" title="Maximum 50" placeholder="Double Click to Search" onKeyUp="if (this.value!='') get_php_form_data(this.value+'_blur'+document.getElementById('cbo_item_category').value, 'load_php_popup_to_form', 'requires/item_category_list_controller')" onDblClick="openmypage()"  readonly />
                        </td>

                        <th width="100" class="">Item Description</th>
                        <td width="150">
                            <input type="text" name="txt_item_description" id="txt_item_description" class="text_boxes" value="" style="width:140px;" placeholder="Browse or Write" onDblClick="openmypagedescriction()"/>
                        </td>
                        
                        <td>
                            <input type="button" name="search" id="show" value="Show" onClick="fn_report_generated(1)" style="width:70px" class="formbutton" />
                        </td>
                        <td>
                            <input type="button" name="search" id="show2" value="Show2" onClick="fn_report_generated(2)" style="width:70px" class="formbutton" />
                        </td>
                    </tr>
                    </thead>
                </table>  
            </fieldset>
        </div>
        </form>
        <div id="report_container" align="center"></div>
        <div id="report_container2"></div>
</body>
<script>set_multiselect('cbo_item_category','0*0','0','','0*0');</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>