<?
/*-------------------------------------------- Comments 
Version          : 
Purpose			 : 
Functionality	 :	
JS Functions	 :
Created by		 : Monir Hossain
Creation date 	 : 17/02/2016
Requirment Client: 
Requirment By    : 
Requirment type  : 
Requirment       : 
Affected page    : 
Affected Code    :              
DB Script        : 
Updated by 		 : 
Update date		 : 
QC Performed BY	 :		
QC Date			 :	
Comments		 : 
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
//function load_html_head_contents($title, $path, $filter, $popup, $unicode, $multi_select, $am_chart)

echo load_html_head_contents("Sample Issue", "../", 1, 1,$unicode,'','');

?>	
<script>
<?
	$color = return_library_array("select id,color_name from lib_color", "id", "color_name");
	$size = return_library_array("select id,size_name from lib_size", "id", "size_name");
	$data_array = sql_select("SELECT a.id,a.item_id,a.category_id,a.construction,a.composition,a.produced_by_id,a.designer,b.balance,b.barcode,b.color_id,b.size_id,b.expected_price from sample_receive_mst a, sample_receive_dtls b where a.id=b.mst_id ");//and b.balance>0
	$operation_barcode = array();
foreach($data_array as $row)
{

	$operation_barcode[$row[csf("barcode")]]['balance'] = $row[csf("balance")];
	$operation_barcode[$row[csf("barcode")]]['id'] = $row[csf("id")];
	$operation_barcode[$row[csf("barcode")]]['item_id'] = $row[csf("item_id")];
	$operation_barcode[$row[csf("barcode")]]['color_id'] = $color[$row[csf("color_id")]];
	$operation_barcode[$row[csf("barcode")]]['size_id'] = $size[$row[csf("size_id")]];
	$operation_barcode[$row[csf("barcode")]]['expected_price'] = $row[csf("expected_price")];
	$operation_barcode[$row[csf("barcode")]]['category_id'] = $row[csf("category_id")];
	$operation_barcode[$row[csf("barcode")]]['construction'] = $row[csf("construction")];
	$operation_barcode[$row[csf("barcode")]]['composition'] = $row[csf("composition")];
	$operation_barcode[$row[csf("barcode")]]['produced_by_id'] = $row[csf("produced_by_id")];
	$operation_barcode[$row[csf("barcode")]]['designer'] = $row[csf("designer")];

}
	$operation_barcode = json_encode($operation_barcode);
	echo "var operation_barcode = ".$operation_barcode.";\n";
	
	$receive_image_arr = array();
$sql_photo=sql_select("select b.sample_pic,b.barcode,a.id,a.item_barcode from  sample_issue_mst a,sample_receive_dtls b ");
	foreach($sql_photo as $val)
	{
		$receive_image_arr[$val[csf('barcode')]]=$val[csf('sample_pic')];	
	}
 	 $receive_image_arr_js = json_encode($receive_image_arr);
	echo "var receive_image_arr = ".$receive_image_arr_js.";\n";
	
?>
	if ($('#index_page', window.parent.document).val() != 1)
	window.location.href = "../../logout.php";
	
	var permission = '<? echo $permission; ?>';
	
function  fnc_sample_issue(operation)
{
	if (form_validation('item_barcode*issue_date*txtissued_qty', 'Scan*Date*Qty') == false)
	{
		return;
	}
		var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('item_barcode*issue_date*txtissued_qty*cbo_team_leader*cbo_dealing_merchant*cbo_Purpose*txt_gifted_to*returnable*pos_return_date*update_id', "../");
		freeze_window(operation);
		http.open("POST", "requires/sample_issue_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_sample_issue_response;
}

function fnc_sample_issue_response()
{
	if (http.readyState == 4)
	{
		var response = trim(http.responseText).split('**');
		show_msg(response[0]);
		release_freezing();
		if (response[0] == 0 || response[0] == 1 || response[0] == 2 )
		{
			$('#system_id').val(response[1]);
			$('#update_id').val(response[1]);
			set_button_status(1, permission, 'fnc_sample_issue', 1);
			show_list_view('','sample_list_view','search_list_view','requires/sample_issue_controller','setFilterGrid("list_view",-1)');
			reset_form('sampleissue_1','','');
		}


	}
	
}

function sample_issue_pop()
{
	  	reset_form('sampleissue_1','','');
	var page_link = 'requires/sample_issue_controller.php?action=barcode_list_view';
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, 'sample List View', 'width=720px, height=280px, center=1, resize=0, scrolling=0', '');
        emailwindow.onclose = function()
        {
			
			var bar_code = this.contentDoc.getElementById("update_id").value;
			$('#txtissued_qty').val(1);
			$('#item_barcode').val(bar_code);
			$('#system_id').val(operation_barcode[bar_code]["id"]);
			$('#issue_date').val('<? echo date("d-m-Y"); ?>');
			$('#txt_item_name').val(operation_barcode[bar_code]["item_id"]);
			$('#txt_item_color').val(operation_barcode[bar_code]["color_id"]);
			$('#txt_item_size').val(operation_barcode[bar_code]["size_id"]);
			$('#expected_price').val(operation_barcode[bar_code]["expected_price"]);
			$('#cbo_fabric_cat').val(operation_barcode[bar_code]["category_id"]);
			$('#txt_construction').val(operation_barcode[bar_code]["construction"]);
			$('#txt_composition').val(operation_barcode[bar_code]["composition"]);
			$('#cbo_supplier_source').val(operation_barcode[bar_code]["produced_by_id"]);
			$('#txt_designer').val(operation_barcode[bar_code]["designer"]);
			var image_link="requires/uploads/"+receive_image_arr[bar_code];
			$("#img_sample_issue").attr("src", image_link);
			sample_listview_data();
        }

}

var str_gifted_to = [ <? echo substr(return_library_autocomplete("select distinct(gifted_to) from sample_issue_mst", "gifted_to"), 0, - 1); ?> ];

function add_auto_complete(j)
{
		$("#txt_gifted_to").autocomplete({
			source: str_gifted_to
		});
}
		
function pos_return_dis()
{
	var i = $('#returnable').val();
	if (i == 2)
	{
		$('#pos_return_date').attr('disabled', true);
		$('#pos_return_date').val('');
	}
	else
	{
		$('#pos_return_date').attr('disabled', false);
		if (form_validation('pos_return_date', 'Return Date') == false)
		{
			return;
		}
	}
}


$('#item_barcode').live('keydown', function(e) 
{
	if (e.keyCode === 13) {
	e.preventDefault();
	
	var bar=populate_data($('#item_barcode').val());
}

});


function populate_data(bar_code)
{
	if(bar_code !='')
	{	
		$('#txtissued_qty').val(1);
		$('#txt_item_name').val(operation_barcode[bar_code]["item_id"]);
		$('#txt_item_color').val(operation_barcode[bar_code]["color_id"]);
		$('#txt_item_size').val(operation_barcode[bar_code]["size_id"]);
		$('#expected_price').val(operation_barcode[bar_code]["expected_price"]);
		$('#cbo_fabric_cat').val(operation_barcode[bar_code]["category_id"]);
		$('#txt_construction').val(operation_barcode[bar_code]["construction"]);
		$('#txt_composition').val(operation_barcode[bar_code]["composition"]);
		$('#cbo_supplier_source').val(operation_barcode[bar_code]["produced_by_id"]);
		$('#txt_designer').val(operation_barcode[bar_code]["designer"]);
		var image_link="requires/uploads/"+receive_image_arr[bar_code];
		$("#img_sample_issue").attr("src", "");
		$("#img_sample_issue").attr("src", image_link);
	}
}
function sample_listview_data(data)
{
	var data=data.split("_");
	var master_id=data[0];
	var barcode=data[1];
	get_php_form_data(master_id, 'load_php_data_to_form', 'requires/sample_issue_controller');
	populate_data(barcode)
}
</script>
</head>
<body onLoad="set_hotkey();">
<div align="center">
<? echo load_freeze_divs ("../",$permission); ?>
<fieldset style="width:850px;">
    <form name="sampleissue_1" id="sampleissue_1">	
        
        <table>
            <tr>
                <td>System Id</td>
                <td> 
                    <input type="text" name="system_id" id="system_id" class="text_boxes_numeric" style="width:200px" placeholder="System id" readonly />
                </td>
            </tr>
        </table>

        <table cellpadding="0" cellspacing="1" width="650" align="center">
            <tr>
                <td>
                    <table cellpadding="0" cellspacing="1" width="320" align="center" id="sample_re">
                        <tr>
                            <td align="right" width="100" class="must_entry_caption">Item Barcode:</td>
                            <td width='100'>
                                <input type="text" name="item_barcode" id="item_barcode" class="text_boxes_numeric" style="width:200px" placeholder="Scan/Browse/Write" onDblClick="sample_issue_pop()" maxlength="8" />	
                                <input type="hidden" name="update_id" id="update_id"  />					
                            </td>
                        </tr>	
                        <tr>
                            <td  align="right" class="must_entry_caption">Issue Date:</td>
                            <td valign="top">
                                <input type="text" name="issue_date" id="issue_date" class="datepicker" style="width:200px" placeholder="Date Picker" value="" />
                            </td>
                        </tr>
                        <tr>
                            <td  align="right" class="must_entry_caption">Issued Qty</td>
                            <td valign="top">
                                <input type="text"  name="txtissued_qty" id="txtissued_qty" class="text_boxes_numeric" style="width:200px" placeholder="">
                            </td>			
                        </tr>
                        <tr>
                             
                        	<td align="right" class="must_entry_caption">Team Leader</td>   
    						<td>
                             <?  
							  	echo create_drop_down( "cbo_team_leader", 210, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "load_drop_down( 'requires/sample_issue_controller', this.value, 'cbo_dealing_merchant', 'div_marchant' )" );
							?>		
                            </td>
                            </tr>
                            <tr>
							<td align="right" class="must_entry_caption">Dealing Merchant</td>   
    						<td id="div_marchant" > 
                            <? 
							  	echo create_drop_down( "cbo_dealing_merchant", 210,$blank_array,"",1,"-- Select Team Member --",$selected,"" );
							?>	
                           </td>	
                        </tr>	
                        
                        <tr>
                            <td  align="right" class="must_entry_caption">Purpose</td>
                            <td valign="top">
                                <? 
	                                $purpose=array(1=>"Presentation",2=>"Buyer Selected",3=>"Unknown and  Adjustment");
	                                echo create_drop_down( "cbo_Purpose", 212, $purpose,"", 1, "-- Select --", "","", "", "");
                                ?>
                            </td>			
                        </tr>
                        <tr>
                            <td  align="right" class="must_entry_caption">Gifted To</td>
                            <td valign="top">
                                <input type="text"  name="txt_gifted_to" id="txt_gifted_to" class="text_boxes" style="width:200px"  placeholder="" onFocus="add_auto_complete(1)" >
                            </td>			
                        </tr>
                        <tr>
                            <td  align="right" class="must_entry_caption">Returnable</td>
                            <td valign="top">
                                <? 
                               echo create_drop_down( "returnable", 212, $yes_no,"", 1, "-- Select --", 0,"pos_return_dis();","","");	
                                ?>
                            </td>			
                        </tr>
                        <tr>
                            <td  align="right" class="must_entry" style="width:150px">Posible Return Date:</td>
                            <td valign="top">
                                <input type="text" name="pos_return_date" id="pos_return_date" class="datepicker" style="width:200px" placeholder=	"Date Picker" />
                            </td>			
                        </tr>
                    </table>
                </td>
                <td>
                    <table cellpadding="0" cellspacing="1" width="300"  align="center">
                        <tr>
                            <td align="right" >Item Name</td>
                            <td colspan="3">
                                <input type="text" id="txt_item_name" name="txt_item_name" class="text_boxes" style="width:200px" onFocus="add_auto_complete(1)" disabled="disabled" />					
                            </td>
                        </tr>	
                        <tr>
                            <td  align="right">Item Color</td>
                            <td>
                                <input type="text" id="txt_item_color" name="txt_item_color" class="text_boxes" style="width:200px" onFocus="" disabled="disabled"/>
                            </td>
                        </tr>
                        <tr>
                            <td  align="right">Item Size</td>
                            <td >
                                <input type="text" id="txt_item_size" name="txt_item_size" class="text_boxes" style="width:200px" onFocus="" disabled="disabled" />
                            </td>			
                        </tr>
                         <tr>
                            <td  align="right">Expected Price</td>
                            <td >
                                <input type="text" id="expected_price" name="expected_price" class="text_boxes_numeric" style="width:200px" onFocus="" disabled="disabled" />
                            </td>			
                        </tr>
                        <tr>
                            <td  align="right">Category</td>
                            <td valign="top">
                                <? 
                                $sample_category=array(1=>"Basic",2=>"Casual Wear",3=>"Dress Up",4=>"Holiday",5=>"Occasion Wear",6=>
                                "Sport Wear",7=>"Work Wear");
                                echo create_drop_down( "cbo_fabric_cat", 212, $sample_category,"", 1, "-- Select --", 0,$onchange_func,
                                1, ""); 		
                                ?>
                            </td>			
                        </tr>
                        <tr>
                            <td  align="right">Construction</td>
                            <td valign="top">
                                <input type="text" id="txt_construction" name="txt_construction" class="text_boxes_numeric" style="width:200px"disabled="disabled">
                            </td>	
                        </tr>
                        <tr>
                            <td  align="right">Composition</td>
                            <td valign="top">
                                <input type="text" id="txt_composition" name="txt_composition" class="text_boxes_numeric" style="width:200px" disabled="disabled">
                            </td>	
                        </tr>
                        <tr>
                            <td  align="right">Supplier</td>
                            <td valign="top">
                                <? 
                                $supplier="select id,company_name from lib_company";
                                echo create_drop_down( "cbo_supplier_source", 212,$supplier,"id,company_name", 1, "-- Select --", "","", 1, "");		
                                ?>
                            </td>	
                        </tr>
                        <tr >
                            <td  align="right">Designer</td>
                            <td valign="top">
                               <input type="text" id="txt_designer" name="txt_designer" class="text_boxes_numeric" style="width:200px" disabled="disabled">                             </td>	
                        </tr>
                    </table>
                </td>
                <td>
                <div style=" width:188px; height:188px;box-shadow:5px 5px 5px gray;">
                <img id="img_sample_issue" src="requires/uploads/noimage.jpg"  height="188" width="188"/>
                </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="button_container" align="center">
                    <? 
					 echo load_submit_buttons( $permission, "fnc_sample_issue", 0,0 ,"reset_form('sampleissue_1','','',0)"); 
                    ?>
                </td>
            </tr>
    	</table>        
    </form>	
</fieldset>
<fieldset style="width:900px; margin-top:10px">
		<legend>List View</legend>
		<form>
			<div style="width:885px; margin-top:10px" id="search_list_view" align="left">
				<?
					$purpose=array(1=>"Presentation",2=>"Buyer Selected",3=>"Unknown and  Adjustment");
					$team_leader=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name"  );
					$dealing_merchant=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
					$arr=array (3=>$team_leader,4=>$dealing_merchant,5=>$purpose,7=>$yes_no);
					echo  create_list_view ( "list_view","Item Barcode,Item Name,Quantity,Team Leader,Dealing Merchant,Purpose,Gifted To,Returnable,Posiable Return date", "100,100,50,100,100,100,140","880","220",0,"select id,item_barcode,issue_date,issue_qty,team_leader,dealing_merchant,issue_purpose,gifted_to,issue_returnable,pos_re_date from sample_issue_mst where is_deleted=0", "sample_listview_data", "id,item_barcode", "", 1, "0,0,0,team_leader,dealing_merchant,issue_purpose,0,issue_returnable", $arr , "item_barcode,issue_date,issue_qty,team_leader,dealing_merchant,issue_purpose,gifted_to,issue_returnable,pos_re_date", "requires/sample_issue_controller", 'setFilterGrid("list_view",-1);','0,3,0,0,0,0,0,0,3');
				?>
            </div>
		</form>
</fieldset>	
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>