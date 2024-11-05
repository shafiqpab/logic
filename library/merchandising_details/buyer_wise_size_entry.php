<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Buyer wise Size					
Functionality	:	
JS Functions	:
Created by		:	Zakaria joy 
Creation date 	: 	17-02-2024	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Buyer Wise Size Information", "../../", 1, 1,$unicode,1,'');
?>	
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';

function fnc_size_info( operation )
{
	
	if (form_validation('cbo_buyer_name*cbo_brand_id*cbo_product_department*txt_size_name','Buyer Name*Brand Name*Product Department*Size Name')==false)
	{
		return;
	}
	else
	{
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_buyer_name*cbo_brand_id*cbo_product_department*cbo_sub_dept*txt_size_name*txt_sequence*cbo_status*update_id',"../../");
		freeze_window(operation);
		http.open("POST","requires/buyer_wise_size_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_size_info_reponse;
	}
}

function fnc_size_info_reponse()
{
	if(http.readyState == 4) 
	{  	
		var reponse=trim(http.responseText).split('**');
		show_msg(reponse[0]);
		show_list_view('','color_list_view','color_list_view','../merchandising_details/requires/buyer_wise_size_entry_controller','setFilterGrid("list_view",-1)');
		reset_form('colorinfo_1','','');
		set_button_status(0, permission, 'fnc_size_info',1);
		release_freezing();
	}
}
function sub_dept_load(cbo_buyer_name,cbo_product_department)
{
    if(cbo_buyer_name ==0 || cbo_product_department==0 )
    {
        return;
    }
    else
    {
        load_drop_down( 'requires/buyer_wise_size_entry_controller',cbo_buyer_name+'_'+cbo_product_department, 'load_drop_down_sub_dep', 'sub_td' );
    }
}

</script>
</head>
<body  onload="set_hotkey()">
<? echo load_freeze_divs ("../../",$permission);  ?>
<div align="center" style="width:100%;">	
    
	<fieldset style="width:500px;">
		<legend>Size Information</legend>
		<form name="colorinfo_1" id="colorinfo_1"  autocomplete="off">	
			<table cellpadding="0" cellspacing="2" width="950px">
                <tr>
					<td class="must_entry_caption">Buyer Name</td>
					<td><? echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name ","id,buyer_name", 1, "-- Select Buyer --", $selected, "sub_dept_load(this.value,document.getElementById('cbo_product_department').value); load_drop_down( 'requires/buyer_wise_size_entry_controller', this.value, 'load_drop_down_brand', 'brand_td');" ); ?></td>
                    <td class="must_entry_caption">Brand Name</td>
					<td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 150, $blank_array,'', 1, "--Brand--",$selected, "" ); ?></td>
                    <td class="must_entry_caption">Product Dept.</td>
					<td><? echo create_drop_down( "cbo_product_department", 150, $product_dept, "", 1, "-Select-", $selected, "sub_dept_load(document.getElementById('cbo_buyer_name').value,this.value)", "", "" ); ?></td>
                    <td>Sub Dept.</td>
					<td id="sub_td"><? echo create_drop_down( "cbo_sub_dept", 150, $blank_array,"", 1, "-- Select Sub Dep --", $selected, "" ); ?></td>
                </tr>
			 	<tr>
					<td class="must_entry_caption">Size Name</td>
					<td ><input type="text" name="txt_size_name" id="txt_size_name" class="text_boxes" style="width:140px" /></td>
                    <td>Sequence</td>
					<td ><input type="text" name="txt_sequence" id="txt_sequence" class="text_boxes" style="width:140px" /></td>
                    <td>Status</td>
                    <td ><? echo create_drop_down( "cbo_status", 150, $row_status,'', $is_select, $select_text, 1, $onchange_func, '','','','',3 ); ?></td>
                    <td colspan="2"></td>
                </tr>
                  <tr>
                                    
                  </tr>
                  <tr>
					<td colspan="8" align="center" class="button_container">
						<? 
					     echo load_submit_buttons( $permission, "fnc_size_info", 0,0 ,"reset_form('colorinfo_1','','')",1);
				        ?> 
                        <input type="hidden" name="update_id" id="update_id" >
					</td>				
				</tr>
                <tr>
						<td colspan="8" align="center" id="color_list_view">
							<?
                            $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
                            $brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
                            $pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
							$arr=array (0=>$buyer_arr,1=>$brand_arr,2=>$product_dept,3=>$pro_sub_dept_array,6=>$row_status);
							echo  create_list_view ( "list_view", "Buyer,Brand,Product Dept.,Sub Dept.,Size Name,Sequence,Status", "150,150,150,150,150,100,100","950","220",0, "select  buyer_id, brand_id, product_dept, pro_sub_dep,size_name,sequence,status_active,id from   buyer_wise_size where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "buyer_id,brand_id,product_dept,pro_sub_dep,0,0,status_active", $arr , "buyer_id,brand_id,product_dept,pro_sub_dep,size_name,sequence,status_active", "requires/buyer_wise_size_entry_controller", 'setFilterGrid("list_view",-1);' ) ;
							 ?>
						</td>
					</tr>
		   </table>
			</form> 
		</fieldset>	
	</div>
</div>
</body>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>
