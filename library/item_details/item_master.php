<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Item Master Info
Functionality	:	
JS Functions	:
Created by		:	CTO/sohel
Creation date 	: 	24.03.2013
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
echo load_html_head_contents("Item Group Info", "../../", 1, 1,$unicode,'','');
?>

<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission='<? echo $permission; ?>';


</script>
</head>

<body onLoad="set_hotkey()">
<? echo load_freeze_divs ("../../",$permission);  ?>
<div align="center">
	<fieldset style="width:900px;">
		<legend>Item Master Info</legend>
        
        <table  cellpadding="1" cellspacing="0" width="100%" border="0">
                <tr>
                    <td>
						Company
					</td>
                    <td> 
					 <select name="cbo_company_name" id="cbo_company_name" class="combo_boxes" style="width:155px" >
                     <?
					 	if($company_cond=="")
						{
					 ?>
                     	<option value='0'>-- Select Company --</option>
                     <?php
					 	}
						 $sql = "SELECT * FROM lib_company WHERE status_active=1 and is_deleted = 0 $company_cond ORDER BY company_name ASC";
						 $result = mysql_query( $sql ) or die( $sql . "<br />" . mysql_error() );
						 while($row=mysql_fetch_assoc($result))
						 {?>
						  <option value="<?php echo $row['id']; ?>"><?php echo ($row['company_name']); ?></option>
						 <?php } ?>
                     </select>
					</td>
                    <td>Item Category</td>
                    <td> 
                        <select name="cbo_item_category" id="cbo_item_category" class="combo_boxes" style="width:155px " onChange="clear_fld()">
                            <?php
                            foreach($gen_item_category as $key=>$value):
                            ?>
                            <option value=<? echo "$key";
                            if ($cbo_item_category1==$key){?> selected <? } ?>> <? echo "$value" ; ?> </option>
                            <?		
                            endforeach;
                            ?>
                       </select>
                    </td>
                    <td>Item Group</td>
                    <td> 
                        <Input name="txt_group" ID="txt_group"   style="width:145px" value="" class="text_boxes" autocomplete="off" placeholder="Double Click For Search" onDblClick="popuppage_group('search_group.php','Item Group Info'); return false" readonly >
                    </td>
                </tr>	 
                <tr>
                    <td>Item Name Code</td>
                    <td> 
                        <Input name="txt_name_code" ID="txt_name_code"  style="width:145px" value="" class="text_boxes" autocomplete="off">
                    </td>
                    <td>Item Name</td>
                    <td> 
                        <Input name="txt_name" ID="txt_name"  style="width:145px" class="text_boxes" autocomplete="off">
                    </td>
                    <td>Item Description</td>
                    <td> 
                        <Input name="txt_description" ID="txt_description"  style="width:145px"  class="text_boxes" autocomplete="off">
                    </td>
                </tr> 
                <tr>
                    <td>Order UOM</td>
                    <td> 
                         <select name="cbo_order_uom" id="cbo_order_uom" style="width:155px" class="combo_boxes"> 
                            <option value="0">--- Select Order UOM ---</option>
                            <?
                            $unit_name_sql= mysql_db_query($DB, "select unit_name,id from lib_unit where is_deleted=0  order by unit_name");
                            while ($r_unit_name=mysql_fetch_array($unit_name_sql))
                            {
                            ?>
                            <option value=<? echo $r_unit_name["id"];
                            if ($cbo_unit_name==$r_unit_name["id"]){?> selected <?php }?>><? echo "$r_unit_name[unit_name]" ?> </option>
                            <?
                            }
                            ?>
                        </select>
                    </td>
                    <td>Cons UOM</td>
                    <td> 
                         <select name="cbo_cons_uom" id="cbo_cons_uom" style="width:155px" class="combo_boxes"> 
                            <option value="0">--- Select Order UOM ---</option>
                            <?
                            $unit_name_sql= mysql_db_query($DB, "select unit_name,id from lib_unit where is_deleted=0  order by unit_name");
                            while ($r_unit_name=mysql_fetch_array($unit_name_sql))
                            {
                            ?>
                            <option value=<? echo $r_unit_name["id"];
                            if ($cbo_unit_name==$r_unit_name["id"]){?> selected <?php }?>><? echo "$r_unit_name[unit_name]" ?> </option>
                            <?
                            }
                            ?>
                        </select>
                    </td>
                    <td>Conv. Factor </td>
                    <td>
                        <Input name="txt_conversion_factor" ID="txt_conversion_factor"  style="width:145px" class="text_boxes_numeric" onKeyPress="return numbersonly(this,event)" autocomplete="off">					 
                    </td>
                </tr>
                <tr>
                    <td>Item Code</td>
                    <td>
                        <Input name="txt_item_code" ID="txt_item_code" style="width:145px" class="text_boxes" readonly >					 
                    </td>
                    <td>Status </td>
                    <td>
                        <select name="cbo_status" id="cbo_status"  class="combo_boxes" style="width:155px" > 
                            <?php
                            foreach($status_active as $key=>$value):
                            ?>
                            <option value=<? echo "$key";
                            if ($key==1){?> selected <? } ?>> <? echo "$value" ; ?> </option>
                            <?		
                            endforeach;
                        ?>	
                        </select>
                    </td>		
                </tr>
                <tr>
                    <td colspan="6" align="center">&nbsp;						
                        <input type="hidden" name="save_up" id="save_up" >
                        <input type="hidden" name="item_group_id" id="item_group_id" >
                        <input type="hidden" name="item_group_code" id="item_group_code" >
                    </td>	
                </tr>
				<tr>
				  <td colspan="6" align="center" class="button_container">
						<input type="submit" value="Save" name="save" style="width:100px" id="save" class="formbutton"/>&nbsp;&nbsp;
						<input type="reset" value="Refresh" style="width:100px" name="reset" id="reset" class="formbutton" onClick="fn_reset_form()" />	
					</td>				
				</tr>
            </table>
		</form>	
	</fieldset>	
		<fieldset style="width:900px; margin-top:20px">
			<legend>Search Item Master Info</legend>
            	<table width="300" cellpadding="0" cellspacing="0" class="rpt_table">
                	<thead>
                    	<th>Search By</th>
                        <th>Search</th>
                        <th>&nbsp;</th>
                    </thead>
                    <tr class="general">
                    	<td>
                        	<select name="cbo_search_by" id="cbo_search_by"  class="combo_boxes" style="width:160px" onchange="search_populate(this.value)">
                            	<option value="item_code">Item Code</option>
                                <option value="name">Name</option>
                                <option value="description">Description</option>
                                <option value="status_active">Status</option>
                            </select>
                        </td>
                        <td id="search_by_td"><input type="text" name="txt_search" id="txt_search_common" class="text_boxes" style="width:160px"></td>
                        <td><input type="button" value="Show" name="btn" style="width:100px" id="btn" class="formbutton" onclick="showResult(document.getElementById('txt_search_common').value+'*'+document.getElementById('cbo_search_by').value,'item_master_search','item_master_list_view')"/></td>
                    </tr>
                </table>
                <table width="100%">
                	<tr>
                    	<td><div style="width:750px; margin-top:10px" id="item_master_list_view" align="left"></div></td>
                    </tr>
                </table>
        
			
	</fieldset>	
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
