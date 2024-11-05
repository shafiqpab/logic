<?
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
//----------------------------------------------------------------------------------------------------------------
//echo load_html_head_contents("Supplier Info","../../",1 ,1 ,'',1 );
echo load_html_head_contents("Category Create", "../", 'filter', '', $unicode, '', '');
?>


<script>
    if ($('#index_page', window.parent.document).val() != 1)
        window.location.href = "../../logout.php";

    var permission = '<? echo $permission; ?>';
	
	function fncCategoryInfo() {
		
		if (form_validation('category_name*cbo_status','Category Name*cbo status')==false)
		{			
			return false;
		}
		else
		{
			var data="action=category_create"+get_submitted_data_string('category_name*cbo_status',"../");
			http.open("POST","requires/category_create_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_category_create_reponse;
		}
	}

	function fnc_category_create_reponse()
	{
		if(http.readyState == 4) 
		{
			document.getElementById('category_list_view').innerHTML=http.responseText;
		}
	}
</script>


</head>	
<body onLoad="set_hotkey()">

    <div align="center" style="width:100%;">
        <? echo load_freeze_divs("../../", $permission); ?>
        <fieldset style="width:500px;">
            <legend>Category Create</legend>

            <form name="category_create" id="category_create" autocomplete="off">	
                <table cellpadding="0" cellspacing="2" border="0" width="100%">
                    
					<tr>
                        <td width="130" class="">Category Name</td>
                        <td width="180">
                            <input type="text" name="category_name" id="category_name" class="text_boxes" style="width:180px" maxlength="100" title="Maximum 100 Character" />
                        </td>
                    </tr>			

                    <tr>
                        <td>Status</td>
                        <td>
                            <?
                            echo create_drop_down("cbo_status", 190, $row_status, '', $is_select, $select_text, 1, $onchange_func, "", '', '');
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="6" align="center" height="20" valign="middle" > 
                            <input type="hidden" name="update_id" id="update_id" />  
                        </td>					
                    </tr>	 

                    <tr>
                        <td colspan="6" align="center" height="40" valign="middle" class="button_container"> 
                            <?
                            echo load_submit_buttons($permission, "fncCategoryInfo", 0, 0, "reset_form('supplierinfo_1','','')", 1);							
                            ?> 
                        </td>					
                    </tr>
					
                </table>
            </form>
        </fieldset>	

        <div style="width:100%; float:left; margin:auto" align="center" id="search_container">
            <fieldset style="width:600px; margin-top:10px">
                <table width="720" cellspacing="2" cellpadding="0" border="0">

                    <tr>
                        <td colspan="3">
                            <div id="category_list_view">
                                <?
                                //echo  create_list_view ( "list_view", "Test Name,Country,Party Type,Status", "200,100,200,","700","220",0, "select supplier_name,party_type,status_active from lib_supplier where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,0,status_active", $arr , "supplier_name,country,party_type,status_active", "../contact_details/requires/supplier_info_controller", 'setFilterGrid("list_view",-1);') ;
                                ?>
                            </div>
                        </td>
                    </tr>
                </table>
            </fieldset>	
        </div>
    </div>
</body>

<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>