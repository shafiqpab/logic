<? 
/*------------------------------------------------------------ Comments
Purpose			: 	This form will create for Yarn Test
Functionality	:	
JS Functions	:
Created by		:	Md. Reaz Uddin
Creation date 	: 	07-02-2018
Updated by 		: 	
Update date		: 
QC Performed BY	:		
QC Date			:	
Comments		:
*/
//----------------------------------------------------------------------------------

session_start();
if($_SESSION['logic_erp']['user_id']=="") header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
echo load_html_head_contents("Yarn Bag Receive", "../../", 1, 1,'','',''); 
?>

<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission='<? echo $permission; ?>';
	var scanned_barcode=new Array();
	
	function fnc_yarn_test()
	{ 
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return; 
		}
	
		var company_id=$('#cbo_company_name').val();
		var cbo_supplier = $("#cbo_supplier").val();
		var txt_lot = $('#txt_lot').val();
		var cbo_yarn_count = $('#cbo_yarn_count').val();
		var cbo_yarn_type = $('#cbo_yarn_type').val();
		
		var dataString = "&cbo_company_name="+company_id+"&cbo_supplier="+cbo_supplier+"&txt_lot="+txt_lot+"&cbo_yarn_count="+cbo_yarn_count+"&cbo_yarn_type="+cbo_yarn_type;
		
		var data="action=generate_report"+dataString;
		freeze_window(3);
		http.open("POST","requires/yarn_test_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_yarn_test_reponse;
	}

	function fnc_yarn_test_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
			$("#yarn_test_details_container").html(reponse[0]);  
			show_msg('3');
			release_freezing();
		}
	}
	 
	function openmypage_stock(prod_id,action)
	{
		var popup_width='745px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/yarn_test_controller.php?prod_id='+prod_id+'&action='+action+'&cbo_company_id='+$("#cbo_company_name").val(),'Yarn Lot Test Information','width='+popup_width+', height=490px,center=1,resize=0,scrolling=0','../');
	}
	
</script>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
    <div style="width:1000px;">
    <? echo load_freeze_divs ("../../",$permission); ?>
        <form name="yarnTestform_1" id="yarnTestform_1" autocomplete="off">
            <fieldset style="margin-top:10px;">
            <legend>Yarn Test</legend>
            <table width="1000" border="0" cellpadding="0" cellspacing="3">
                <tr>
                    <td width="80" align="left">Company Name </td>
                    <td align="left">
						<? 
                        	echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name",1, "-- Select Company --",$selected,"load_drop_down( 'requires/yarn_test_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );",'' );
                        ?>
                    </td>
                    
                    <td width="80">Supplier Name</td>
					<td id="supplier_td">
                    	<?
                       	echo create_drop_down("cbo_supplier", 120, "select c.supplier_name,c.id from lib_supplier c where c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
						
						//echo create_drop_down( "cbo_supplier", 120, $blank_array,"",0, "--- Select Supplier ---", $selected, "",0);
                        ?> 
                    </td> 
                    
                    <td>Lot</td>
                    <td><input type="text" style="width:50px" class="text_boxes" name="txt_lot" id="txt_lot"/></td>
                    
                    <td>Yarn Count</td>
                    <td>
						<?
                        echo create_drop_down("cbo_yarn_count", 100, "select id,yarn_count from lib_yarn_count where is_deleted = 0 AND status_active = 1 ORDER BY yarn_count ASC", "id,yarn_count", 1, "--Select--", 0, "", 0);
                        ?>
                    </td>
                    
                    <td>Yarn Type</td>
                    
                    <td>
						<?
                        asort($yarn_type);
                        echo create_drop_down("cbo_yarn_type", 100, $yarn_type, "", 1, "--Select--", 0, "", 0);
                        ?>
                    </td>
                                 
                    <td>
                      <input type="button" name="btn_show" id="btn_show"  class="formbutton" value="Show" onClick="fnc_yarn_test();" style="width:80px" />
                    </td>
                </tr>
            </table>
            </fieldset>
        </form>
    </div>
    <fieldset style="width:800px; margin-top:10px;">
    <legend>Yarn Test Details</legend>
        <div id="yarn_test_details_container"></div> 
    </fieldset> 
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>