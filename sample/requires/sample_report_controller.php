<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
//...............................................
if ($action=="barcode_list_view")
{
	echo load_html_head_contents("Sample List View","../../", 1, 1, $unicode);
?>	
	<script> 
	function js_set_value(data)
	{
		document.getElementById('update_id').value=data;
		parent.emailwindow.hide();
	}
	
	</script> 
	<input type="hidden" id="update_id"	 value="">
<?	
		echo load_html_head_contents("Sample List View","../../", 1, 1, $unicode);
?>	
	<script> 
	function js_set_value(data)
	{
		document.getElementById('update_id').value=data;
		parent.emailwindow.hide();
	}
	</script> 
	<input type="hidden" id="update_id"	 value="">
<?	
	echo load_html_head_contents("Sample reports", "../../", 1, 1,$unicode,'','');
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
  	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$arr=array(0=>$color_library,1=>$size_library);
	
	echo  create_list_view ( "list_view", "Color,Size,Quantity,Expected Price,Amount,Barcode", "100,100,100,100,100","700","220",0,"select id,mst_id,color_id,size_id,quantity,expected_price,amount,barcode from sample_receive_dtls order by barcode desc", "js_set_value", "barcode", "'load_php_data'", 1, "color_id,size_id,0,0,0,0", $arr , "color_id,size_id,quantity,expected_price,amount,barcode", "requires/sample_issue_controller", 'setFilterGrid("list_view",-1)','0,0,0,0') ;  
}


if ($action=="sample_list_view")
{
	?>
<table border="1" cellspacing="0" class="rpt_table" rules="all" width="850" >
		<tr style="height:20px; background-color:#B2CEF4; ">
			<th>Sl</th>
			<th>Item Name</th>
			<th>Category</th>
			<th>Style Ref.</th>
			<th>Opening</th>
			<th>Receive</th>
			<th>Total Receive</th>
			<th>Issued</th>
			<th>Adjustment</th>
			<th>Total Issued</th>
			<th>Stock</th>
		</tr>
		
        <?
			$sample_category=array(1=>"Basic",2=>"Casual Wear",3=>"Dress Up",4=>"Holiday",5=>"Occasion Wear",6=>"Sport Wear",7=>"Work Wear");
			$reports=sql_select("SELECT a.id,b.mst_id,a.item_id,a.category_id,a.style_ref,b.quantity,b.barcode,c.item_barcode,SUM(c.issue_qty ) as itemqty from sample_receive_mst a,sample_receive_dtls b,sample_issue_mst c where b.barcode=c.item_barcode and  a.id=b.mst_id and b.barcode='$data'  group by a.id,b.mst_id,a.item_id,a.category_id,a.style_ref,b.quantity,b.barcode,c.item_barcode");
						
			$i=0;
			$qty='';
			foreach($reports as $info){
			$i++;
		 ?>
         <tr>
			<td><? echo $i; ?></td>
			<td><? echo $info[csf("item_id")];?></td>
			<td><? echo $sample_category[$info[csf("category_id")]];?></td>
			<td><? echo $info[csf("style_ref")];?></td>
			<td><? echo $info[csf("quantity")];?></td>
			<td><? echo $info[csf("quantity")];?></td>
			<td><? $open=$info[csf("quantity")]; $receive=$info[csf("quantity")]; $total_receive=$open+$receive ;  echo $total_receive;?></td>
			<td><? echo $info[csf("itemqty")]; ?></td>
			<td></td>
			<td><? echo $info[csf("itemqty")]; ?></td>
			<td><? echo $total_receive-$info[csf("issue_qty")]-$info[csf("itemqty")]; ?></td>
		</tr>
        <? 
		 
			}
		 ?>
	</table>
    <?
}
	
