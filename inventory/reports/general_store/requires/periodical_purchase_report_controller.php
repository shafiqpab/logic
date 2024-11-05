<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_store")
{
	$data=explode('_',$data);
	//print_r ($data);  
	echo create_drop_down( "cbo_store_name", 120, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b  where a.id=b.store_location_id  and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0] and b.category_type in($data[1]) group by  a.id,a.store_name order by a.store_name","id,store_name", 0, "", 1, "",0 );
	exit();
}

if ($action=="load_drop_down_supplier")
{	  
	echo create_drop_down( "cbo_supplier_name", 120, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where c.tag_company=$data and a.id=b.supplier_id and  a.id=c.supplier_id and b.party_type in (1,6,7,8,90,92) and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", "", "" );
	exit();
	//select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0] and b.category_type=$data[1] order by a.store_name
	//select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where  a.id=b.supplier_id and a.id=c.supplier_id and  c.tag_company=$data and b.party_type in (1,6,7,8,90) and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name
}

if ($action=="item_account_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);  
?>	
    <script>
	 var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	 
	 function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			alert (tbl_row_count);
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				 eval($('#tr_'+i).attr("onclick"));  
			}
		}
		
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
		$('#item_account_id').val( id );
		$('#item_account_val').val( ddd );
	} 
		  
		  
	</script>eth
     <input type="hidden" id="item_account_id" />
     <input type="hidden" id="item_account_val" />
 <?
		$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
		$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
		
		$sql="SELECT id,item_account,item_category_id,item_group_id,item_description,supplier_id from  product_details_master where item_category_id in ($data[1]) and status_active=1 and is_deleted=0"; 
		$arr=array(1=>$item_category,2=>$itemgroupArr,4=>$supplierArr);
		echo  create_list_view("list_view", "Item Account,Item Category,Item Group,Item Description,Supplier,Product ID", "70,110,150,150,100,70","780","400",0, $sql , "js_set_value", "id,item_description", "", 0, "0,item_category_id,item_group_id,0,supplier_id,0", $arr , "item_account,item_category_id,item_group_id,item_description,supplier_id,id", "",'setFilterGrid("list_view",-1);','0,0,0,0,0,0','',1) ;
		exit();
}

if ($action=="item_group_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);  
?>	
    <script>
		  function js_set_value(id)
		  { 
			  document.getElementById('item_name_id').value=id;
			  parent.emailwindow.hide();
		  }
	</script>
        <input type="hidden" id="item_name_id" />
    <?
	$sql="SELECT id,item_name from  lib_item_group where item_category in ($data[1]) and status_active=1 and is_deleted=0"; //id=$data[1] and
	
	echo  create_list_view("list_view", "Item Name", "350","430","300",0, $sql , "js_set_value", "id,item_name", "", 1, "0", $arr , "item_name", "periodical_purchase_report_controller",'setFilterGrid("list_view",-1);','0') ;
	exit();
}

if ($action=="supplier_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST); 
?>	
    <script>
		  function js_set_value(id)
		  { 
			  document.getElementById('supplier_data').value=id;
			  parent.emailwindow.hide();
		  }
	</script>
        <input type="hidden" id="supplier_data" />
    <?
	//$sql="SELECT id,item_name from  lib_item_group where item_category in ($data[1]) and status_active=1 and is_deleted=0"; //id=$data[1] and
	$sql ="select a.id,a.supplier_name from lib_supplier a,lib_supplier_party_type b,lib_supplier_tag_company c where c.tag_company=$data and a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in (1,6,7,8,90,92) and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name";
	
	echo  create_list_view("list_view", "Item Name", "350","385","300",0, $sql , "js_set_value", "id,supplier_name", "", 1, "0", $arr , "supplier_name", "periodical_purchase_report_controller",'setFilterGrid("list_view",-1);','0') ;
	exit();
}

if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if ($cbo_company_name==0) $company_id =""; else $company_id =" and a.company_id='$cbo_company_name'";
	//if ($cbo_item_category_id==0) $item_category_id=""; else $item_category_id=" and b.item_category='$cbo_item_category_id'";
	$cbo_item_category_id =str_replace("'", "", $cbo_item_category_id);
	if ($cbo_item_category_id) $item_category_id=" and b.item_category in ($cbo_item_category_id)";else $item_category_id="";
	if ($item_account_id==0) $item_code=""; else $item_code=" and b.prod_id in ($item_account_id)";
	if ($item_group_id==0) $group_id=""; else $group_id=" and c.item_group_id='$item_group_id'";
	if ($cbo_supplier_name==0) $supplier_id=""; else $supplier_id=" and a.supplier_id='$cbo_supplier_name'";
	if ($cbo_store_name==0){ $store_id="";}else{$store_id=" and a.store_id in ($cbo_store_name)";}
	if ($item_group_id==0){ $group_id="";}else{$group_id=" and c.item_group_id='$item_group_id'";}
	if($db_type==0)
	{
		if( $from_date==0 && $to_date==0 ) $transaction_date=""; else $transaction_date= " and b.transaction_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";	
 	}
	if($db_type==2)
	{
		if( $from_date==0 && $to_date==0 ) $transaction_date=""; else $transaction_date= " and b.transaction_date  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";	
 	
	}
 	
 	
	
 	//library array-------------------
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name"); 
	$storeArr = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0","id","store_name"); 
	$itemnameArr = return_library_array("select id,item_name from lib_item_creation where status_active=1 and is_deleted=0","id","item_name");
	$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");

	$sql = "select a.receive_date,a.supplier_id,(b.cons_quantity) as cons_quantity,b.cons_uom,(b.cons_amount) as cons_amount,c.id as pro_id,c.item_description,c.item_category_id,c.item_group_id,a.store_id,c.item_size 
	from inv_receive_master a,inv_transaction b,product_details_master c 	
	where a.id=b.mst_id and a.entry_form=20 and b.transaction_type=1 and c.id=b.prod_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $item_category_id $item_code  $supplier_id $store_id $transaction_date $group_id 	order by a.store_id,a.item_category,c.item_group_id";
	//echo $sql; 
	$result = sql_select($sql);	
	//$r=1;
	ob_start();	
	?>
	<div style="width:100%;"> 
     <fieldset style="width:750px;">
        <table style="width:720px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" > 
            <tr class="form_caption" style="border:none;">
                <td colspan="6" align="center" style="border:none;font-size:20px; font-weight:bold" ><strong>Periodical Purchase Report- Detail</strong></td> 
            </tr>
            <tr style="border:none;">
                <td colspan="6" align="center" style="border:none; font-size:17px;"><strong>
                    Company Name : <? echo $companyArr[str_replace("'","",$cbo_company_name)]; ?>  </strong>                              
                </td>
            </tr>
            <tr style="border:none;">
                <td colspan="6" align="center" style="border:none;font-size:12px; font-weight:bold">
                    <? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date)."   To : ".change_date_format($to_date)."" ;?>
                </td>
            </tr>
            <tr style="border:none;">
                <td colspan="6" align="center" style="border:none;font-size:12px; font-weight:bold">
                    <? if($cbo_supplier_name!=0)echo "Supplier : ".$supplierArr[$cbo_supplier_name]."" ;?>
                </td>
            </tr>
            <?
/*			$item_category_array=array();
			$item_group_array=array();
			$store_loc_array=array();
*/		$all_data=array();
		foreach($result as $row)
		{
			if( $all_data[$row[csf('store_id')]][$row[csf('item_category_id')]][$row[csf('item_group_id')]][$row[csf('pro_id')]]['item_description']=="")
			{
				$all_data[$row[csf('store_id')]][$row[csf('item_category_id')]][$row[csf('item_group_id')]][$row[csf('pro_id')]]['store_id']=$row[csf('store_id')];
				$all_data[$row[csf('store_id')]][$row[csf('item_category_id')]][$row[csf('item_group_id')]][$row[csf('pro_id')]]['item_group_id']=$row[csf('item_group_id')];
				$all_data[$row[csf('store_id')]][$row[csf('item_category_id')]][$row[csf('item_group_id')]][$row[csf('pro_id')]]['pro_id']=$row[csf('pro_id')];
				$all_data[$row[csf('store_id')]][$row[csf('item_category_id')]][$row[csf('item_group_id')]][$row[csf('pro_id')]]['item_category_id']=$row[csf('item_category_id')];
				$all_data[$row[csf('store_id')]][$row[csf('item_category_id')]][$row[csf('item_group_id')]][$row[csf('pro_id')]][csf('cons_amount')]=$row[csf('cons_amount')];
				$all_data[$row[csf('store_id')]][$row[csf('item_category_id')]][$row[csf('item_group_id')]][$row[csf('pro_id')]][csf('cons_quantity')]=$row[csf('cons_quantity')];
				$all_data[$row[csf('store_id')]][$row[csf('item_category_id')]][$row[csf('item_group_id')]][$row[csf('pro_id')]]['cons_uom']=$row[csf('cons_uom')];
				$all_data[$row[csf('store_id')]][$row[csf('item_category_id')]][$row[csf('item_group_id')]][$row[csf('pro_id')]]['item_size']=$row[csf('item_size')];
				$all_data[$row[csf('store_id')]][$row[csf('item_category_id')]][$row[csf('item_group_id')]][$row[csf('pro_id')]]['item_description']=$row[csf('item_description')];
				$all_data[$row[csf('store_id')]][$row[csf('item_category_id')]][$row[csf('item_group_id')]][$row[csf('pro_id')]]['store_id']=$row[csf('store_id')];
				$all_data[$row[csf('store_id')]][$row[csf('item_category_id')]][$row[csf('item_group_id')]][$row[csf('pro_id')]]['store_id']=$row[csf('store_id')];
			}
			else
			{
				$all_data[$row[csf('store_id')]][$row[csf('item_category_id')]][$row[csf('item_group_id')]][$row[csf('pro_id')]][csf('cons_amount')]+=$row[csf('cons_amount')];
				$all_data[$row[csf('store_id')]][$row[csf('item_category_id')]][$row[csf('item_group_id')]][$row[csf('pro_id')]][csf('cons_quantity')]+=$row[csf('cons_quantity')]; 
			}
		  
		}
		$comp_total_qnty=0;
		$comp_total_amount=0;
		$r=1;

		foreach($all_data as $storeid=>$store_data)
		{
			$store_total_qnty=0;
			$store_total_amount=0;
			?>
               	<tr>
                	<td colspan="6" style="font-size:20px"><b>Store : <? echo $storeArr[$storeid]; ?></b></td>
            	</tr> 
			<? 
			foreach($store_data as $catid=>$catdata)
			{
				$cate_total_qnty=0;
				$cate_total_amount=0;
				foreach($catdata as $gorupid=>$groupdata)
				{
					?>
						<tr>
							<td colspan="4"><b>Item Category :  <? echo $item_category[$catid];// print_r($groupdata); ?></b></td>
							 <td colspan="2"><b>Item Group : <? echo $itemgroupArr[$gorupid];//$itemgroupArr[$item_group_id]; ?></b></td>
						</tr>
                     </table>
                    <div style="width:750px;" id="scroll_body" > 
                    <table style="width:720px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_2" >
                        <thead>
                            <th width="50" >SL</th>
                            <th width="200" >Item Name</th>
                            <th width="70" >UoM</th>
                            <th width="100" >Quantity</th>
                            <th width="100" >Amount</th>
                            <th width="100" >Avg. Rate</th>
                        </thead>
                        <tbody>
					<?
					
					$total_qnty=0;
					$total_amount=0;
					
					foreach($groupdata as $rowid=>$rowdata)
					{
						if ($r%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						if($rowdata[csf("transaction_type")]==1) 
							$stylecolor='style="color:#A61000"';
						else
							$stylecolor='style="color:#000000"'; 								
						
						$cons_amount=$rowdata[csf('cons_amount')];
						$cons_amount_sum += $cons_amount;
						
						$cons_quantity=$rowdata[csf('cons_quantity')];
						$cons_quantity_sum += $cons_quantity;
						$avg_rate=$cons_amount/$cons_quantity;
						?>
                            <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $r; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $r; ?>">
                            	<td align="center"><? echo $r;?></td>
                            	<td><? echo $rowdata['item_description'].','.$rowdata['item_size']; ?></td>
                                <td align="center"><? echo $unit_of_measurement[$rowdata['cons_uom']]; ?></td>
                                <td align="right"><? echo number_format($rowdata[csf('cons_quantity')]); ?></td>
                                <td align="right"><? echo number_format($rowdata[csf('cons_amount')],2,'.',''); ?></td>
                                <td align="right"><? echo number_format($avg_rate,2,'.','');//number_format($avg_rate); ?></td>
                           </tr>
                        
						<?
						$total_qnty+=$rowdata[csf('cons_quantity')];
						$total_amount+=$rowdata[csf('cons_amount')];
						$r++;
					}
					?>
                    	<tr>
                        	<td colspan="3" align="right"><b>Group Total: </b></td>
							<td align="right"><b><? echo number_format($total_qnty,0,'',','); ?></b></td>
                            <td align="right"><b><? echo number_format($total_amount,2,'.',''); ?></b></td>
                            <td>&nbsp;</td>
						</tr>
                    <?
					$cate_total_qnty+=$total_qnty;
					$cate_total_amount+=$total_amount;
				}
				?>
					<tr>
						<td colspan="3" align="right"><b>Category Total: </b></td>
						<td align="right"><b><? echo number_format($cate_total_qnty,0,'',','); ?></b></td>
						<td align="right"><b><? echo number_format($cate_total_amount,2,'.',''); ?></b></td>
                        <td>&nbsp;</td>
					</tr>
				<?
				$store_total_qnty+=$cate_total_qnty;
				$store_total_amount+=$cate_total_amount;
			}
			?>
                <tr>
                    <td colspan="3" align="right"><b>Store Total: </b></td>
                    <td align="right"><b><? echo number_format($store_total_qnty,0,'',','); ?></b></td>
                    <td align="right"><b><? echo number_format($store_total_amount,2,'.',''); ?></b></td>
                    <td>&nbsp;</td>
                </tr>
            </tbody> 
			<?
		 	$comp_total_qnty+=$store_total_qnty;
			$comp_total_amount+=$store_total_amount;
		}
		?>
            <tfoot>
            	<tr>
                    <td align="right" colspan="3" ><strong>Grand Total : </strong></td>
                    <td align="right"><strong><? echo number_format($comp_total_qnty,0,'',','); ?></strong></td>
                    <td align="right" ><strong><? echo number_format($comp_total_amount,2,'.',''); ?></strong></td>
                    <td>&nbsp;</td>
                </tr>
            </tfoot>
       </table> 
     </div>
    </fieldset>
   </div>
     <?
    //die;
	$html = ob_get_contents();
	ob_clean();
	//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename"; 
	exit();	
}
?>