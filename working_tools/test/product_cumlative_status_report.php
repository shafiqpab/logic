<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con = connect();

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "")
{
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

$companyArr = return_library_array("select id,company_name from lib_company", "id", "company_name");
$companyArr[0] = "All Company";

// ==== Report Generate start === //
//$prod_id_cond = "and a.id in (20192,21236,25771,26394)";

$sql_cumlative = "select * from product_details_master a where a.status_active=1 and a.is_deleted=0 and a.allocated_qnty!=a.cumulative_balance $prod_id_cond"; 
$result_cumlative = sql_select($sql_cumlative);
?>
<div align="center">
	<table width="1020" border="1" cellpadding="2" cellspacing="0" class="rpt_table" style="font:'Arial Narrow'; font-size:14px"  rules="all" id="table_header_1">
		<thead> 
			<tr class="form_caption" style="border:none;">
				<td colspan="9" align="center" style="border:none;font-size:16px; font-weight:bold" > Product cumlative mismatch status report</td>
			</tr>
			<tr>
				<th width="50">SL</th>
				<th width="200">Company</th>
				<th width="100">Product ID</th>
				<th width="100">Lot</th>
				<th width="100">Current Stock</th>
				<th width="100">Allocated Qty</th>
				<th width="100">Available Qty</th>
				<th width="100">Cumulative Qty</th>
				<th >Differents</th>
			</tr>
		</thead>
	</table>
	
    <div style="width:1020px; overflow-y:scroll; max-height:250px" id="scroll_body">  
        <table width="1003" border="1" cellpadding="2" style="font:'Arial Narrow';"  cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<?
			$i = 1;
			if(!empty($result_cumlative))
			{
				foreach ($result_cumlative as $row) 
				{
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
					$different_qty = $row[csf("allocated_qnty")]-$row[csf("cumulative_balance")];

					if(number_format($different_qty, 2)>0.00)
					{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="50"  align="center"><? echo $i; ?></td>
							<td width="200" align="center"><p><? echo $companyArr[$row[csf("company_id")]]; ?></p></td>
							<td width="100" align="center" ><? echo $row[csf("id")]; ?></td>
							<td width="100" align="center" ><? echo $row[csf("lot")]; ?></td>
							<td width="100" align="right"><p><? echo number_format($row[csf("current_stock")],2); ?>&nbsp;</p></td>
							<td width="100" align="right"><p><? echo number_format($row[csf("allocated_qnty")],2); ?>&nbsp;</p></td>
							<td width="100" align="right"><p><? echo number_format($row[csf("available_qnty")],2); ?>&nbsp;</p></td>					
							<td width="100" align="right"><p><? echo number_format($row[csf("cumulative_balance")], 2); ?>&nbsp;</p></td>
							<td align="right"><p><? echo number_format($different_qty, 2); ?>&nbsp;</p></td>
						</tr>
						<?	
						$i++;
					}								
				}
			}
			else
			{
				echo "<tr colspan='9'><th style='text-align:center;'>No Data Found</th></tr>";
			}
			?>
		</table>
    </div>

	<table width="1020" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_footer">			
        <tr class="tbl_bottom"> 
			<td width="50">&nbsp;</td>
			<td width="200">&nbsp;</td>  
			<td width="100">&nbsp;</td>  
		    <td width="100"align="right">&nbsp;</td>
		    <td width="100" style="word-break: break-all; text-align:right;" id="value_total_allocation_qty" >&nbsp;</td> 
		    <td width="100" style="word-break: break-all;text-align:right;" id="value_total_issue_qty" >&nbsp;</td>
		    <td width="100" style="word-break: break-all;text-align:right;" id="value_total_issue_return_qty">&nbsp;</td>
		    <td width="100" align="right" style="word-break: break-all;" id="value_total_balance">&nbsp;</td>
		    <td>&nbsp;</td>  
        </tr>
	</table>			
</div>

<?
	/*
	//===========
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename) {
		@unlink($filename);
	}
	//---------end------------//
	$name = time();
	$filename = $user_id . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename";
	*/

?>
<style>

	a {
		color: #0254EB
	}
	a:visited {
		color: #0254EB
	}
	a.morelink {
		text-decoration:none;
		outline: none;
	}
	.morecontent span {
		display: none;
	}
	.comment {
		width: 400px;
		background-color: #f0f0f0;
		margin: 10px;
	}

	table tr th, table tr td{word-wrap: break-word;word-break: break-all;}

</style>
