<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="item_group_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data); 
?>	
    <script>
	var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	 
	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
	function js_set_value(id)
	{
		var str=id.split("_");
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		var strdt=str[2];
		str=str[1];
	
		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );
			selected_name.push( strdt );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i,1 );
		}
		var id = '';
		var ddd='';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			ddd += selected_name[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		ddd = ddd.substr( 0, ddd.length - 1 );
		$('#item_group_id').val( id );
		$('#item_group_val').val( ddd );
	} 
		  
	</script>
     <input type="hidden" id="item_group_id" />
     <input type="hidden" id="item_group_val" />
 <?
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	if ($data[1]==0) $category=""; else $category=" and a.item_category_id in ($data[1])";
	
	$sql="SELECT a.id, a.item_account, a.item_category_id, a.item_group_id, a.item_description, a.supplier_id, b.item_name from  product_details_master a,lib_item_group b where a.item_group_id=b.id and a.company_id=$data[0] and a.status_active=1 and a.is_deleted=0 $category group by b.item_name, a.item_group_id order by a.id ";
	//echo $sql; 
	$arr=array(2=>$item_category,5=>$supplierArr);
	echo  create_list_view("list_view", "Product ID,Item Account,Item Category,Item Group,Item Description,Supplier", "50,70,130,170,170,100","780","360",0, $sql , "js_set_value", "id,item_name", "", 0, "0,0,item_category_id,0,0,supplier_id", $arr , "id,item_account,item_category_id,item_name,item_description,supplier_id", "",'setFilterGrid("list_view",-1);','0,0,0,0,0,0','',1) ;
	exit();
}

if ($action=="item_subgroup_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data); 
?>	
    <script>
	var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	 
	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
	function js_set_value(id)
	{
		var str=id.split("_");
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		var strdt=str[2];
		str=str[1];
	
		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );
			selected_name.push( strdt );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i,1 );
		}
		var id = '';
		var ddd='';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			ddd += selected_name[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		ddd = ddd.substr( 0, ddd.length - 1 );
		$('#item_subgroup_id').val( id );
		$('#item_subgroup_val').val( ddd );
	} 
		  
	</script>
     <input type="hidden" id="item_subgroup_id" />
     <input type="hidden" id="item_subgroup_val" />
 <?
 	$item_category_id=str_replace("'","",$data[1]);
	$item_group_id=str_replace("'","",$data[2]);
	
	if ($item_category_id==0) $item_category_list =""; else $item_category_list =" and a.item_category_id in ( $item_category_id )";
	if ($item_group_id==0) $group_id_list =""; else $group_id_list =" and a.item_group_id in ( $item_group_id )";
	
	$sql="SELECT a.id, a.item_account, a.item_category_id, a.item_group_id, a.item_description, a.sub_group_name, b.item_name from  product_details_master a,l
	ib_item_group b where a.item_group_id=b.id and a.company_id=$data[0] and a.status_active=1 and a.is_deleted=0 $item_category_list $group_id_list order by a.id "; 
	//echo $sql;
	$arr=array(2=>$item_category);
	echo  create_list_view("list_view", "Product ID,Item Account,Item Category,Item Group,Sub Group,Item Description", "70,70,110,150,150,100","780","360",0, $sql , "js_set_value", "id,sub_group_name", "", 0, "0,0,item_category_id,0,0,0", $arr , "id,item_account,item_category_id,item_name,sub_group_name,item_description", "",'setFilterGrid("list_view",-1);','0,0,0,0,0,0','',1) ;
	exit();
}

if ($action=="report_generate")
{
	extract($_REQUEST);
	$item_category_id=str_replace("'","",$cbo_item_category);
	$item_group_id=str_replace("'","",$txt_group_id);
	$item_subgroup_id=str_replace("'","",$txt_item_subgroup_id);
	//print_r ($item_subgroup_id);
	
	if ($item_category_id==0) $item_category_list =""; else $item_category_list =" and a.item_category_id in ( $item_category_id )";
	if ($item_group_id==0) $group_id_list =""; else $group_id_list =" and a.item_group_id in ( $item_group_id )";
	if ($item_subgroup_id==0) $subgroup_id_list =""; else $subgroup_id_list =" and a.id in ( $item_subgroup_id )";
	
	?>
	<div id="scroll_body" align="center" style="height:auto; width:1110px; margin:0 auto; padding:0;">
	<?
	$company_library=sql_select("select id, company_name, plot_no, level_no,road_no,city from lib_company where id=".$cbo_company_id."");
	//$groupArr = return_library_array("select id,item_name from lib_group ","id","item_name");
	
	foreach( $company_library as $row)
	{
?>
		<span style="font-size:20px"><center><b><? echo $row[csf('company_name')];?></b></center></span>
<?
	}
?>
    <table width="1100px" align="center">
        <tr>
            <td colspan="6" align="center" style="font-size:16px"><center><strong><u><? echo $report_title; ?> Report</u></strong></center></td>
        </tr>
    </table>
    <?
		$sql_con="select a.id, a.item_group_id, a.item_category_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.re_order_label, a.minimum_label, a.maximum_label, a.status_active, b.item_name, b.order_uom from product_details_master a, lib_item_group b where a.company_id=$cbo_company_id and a.is_deleted=0 and b.is_deleted=0 and a.item_group_id=b.id $item_category_list $group_id_list $subgroup_id_list order by a.item_category_id ";
		//echo $sql_con;			
		$sql_data=sql_select($sql_con);
		
		$item_category_array=array();
	?>
        <div style="width:1110px; height:auto">
        <table align="right" cellspacing="0" width="1100px"  border="1" rules="all" class="rpt_table" id="tbl_suppler_list" >
            <?
		foreach( $sql_data as $row)
		{
			if (!in_array($row[csf("item_category_id")],$item_category_array) )
			{
				$item_category_array[]=$row[csf('item_category_id')];
			?>
            <thead bgcolor="#dddddd" align="center">
                <tr>
                    <td colspan="11" align="left"><b>Category : <? echo $item_category[$row[csf("item_category_id")]]; ?></b></td>
                </tr>
                <tr>
                    <th width="40">SL</th>
                    <th width="150" align="center">Item Group</th>
                    <th width="150" align="center">Sub-group</th>
                    <th width="150" align="center">Description</th>
                    <th width="100" align="center">Item Size</th>
                    <th width="80" align="center">Order UoM</th>
                    <th width="80" align="center">Cons. UoM</th>
                    <th width="80" align="center">Re-Order Level</th>
                    <th width="80" align="center">Minimum Level</th>
                    <th width="80" align="center">Maximum Level</th>
                    <th width="80" align="center">Status</th>
                </tr>         
            </thead>
            <tbody>
            <?
			$i=1;
			}
				if ($i%2==0)  
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
			?>
            <tr bgcolor="<? echo $bgcolor ; ?>">
            	<td><? echo $i; ?></td>
                <td><? echo $row[csf("item_name")]; ?></td>
                <td><? echo $row[csf("sub_group_name")]; ?></td>
                <td><? echo $row[csf("item_description")]; ?></td>
                <td><? echo $row[csf("item_size")]; ?></td>
                <td align="center"><? echo $unit_of_measurement[$row[csf("order_uom")]]; ?></td>
                <td align="center"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
                <td align="center"><? echo $row[csf("re_order_label")]; ?></td>
                <td align="center"><? echo $row[csf("minimum_label")]; ?></td>
                <td align="center"><? echo $row[csf("maximum_label")]; ?></td>
                <td><? echo $row_status[$row[csf("status_active")]]; ?></td>
            </tr>
          </tbody>
		<?	
        $i++;
        }
?>
        </table>
	</div>
	</div>
<?
}
?>