<? 
session_start(); 
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
echo load_html_head_contents("Yarn Stock", "", "", 1, $unicode, '', '');
$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$supplier_library=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	
$sql="select a.id, d.yarn_count, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.current_stock 
	from product_details_master a, lib_yarn_count d
	where a.yarn_count_id=d.id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0
	order by a.company_id"; 	
 
//die;//echo count($result);
$result = sql_select($sql);		
?>
<table width="1150" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
	<thead>
    	<tr>
        	<th width="80">Product Id</th>
            <th width="150">Company Name</th>
            <th width="220">Supplier Name</th>
            <th width="100">Lot</th>
            <th width="70">Count</th>
            <th width="150">Composition</th>
            <th width="100">Yarn Type</th>
            <th width="150">Color</th>
            <th>Stock</th>
        </tr>
    </thead>
	<tbody>
    <?
	$i=1;
	foreach($result as $row)
	{
		if ($i%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
		?>
    	<tr bgcolor="<? echo $bgcolor; ?>">
        	<td align="center"><p><? echo $row[csf("id")]; ?>&nbsp;</p></td>
            <td><p><? echo $company_library[$row[csf("company_id")]]; ?>&nbsp;</p></td>
            <td><p><? echo $supplier_library[$row[csf("supplier_id")]]; ?>&nbsp;</p></td>
            <td align="center"><p><? echo $row[csf("lot")]; ?>&nbsp;</p></td>
            <td align="center"><p><? echo $row[csf("yarn_count")]; ?>&nbsp;</p></td>
            <td><p><? echo $composition[$row[csf("yarn_comp_type1st")]]; ?>&nbsp;</p></td>
            <td><p><? echo $yarn_type[$row[csf("yarn_type")]]; ?>&nbsp;</p></td>
            <td><p><? echo $color_library[$row[csf("color")]]; ?>&nbsp;</p></td>
            <td align="right"><p><? echo $row[csf("current_stock")]; ?></p></td>
        </tr>
        <?
		$i++;
	}
	?>
    </tbody>
</table>
