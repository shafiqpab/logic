<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------



//item search------------------------------//
if($action=="item_description_search")
{		  
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
    <script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
		
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
				
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) { 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{
				var splitSTR = strCon.split("_");
				var str = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				
				toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
				
				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );
					selected_no.push(str);					
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 ); 
					selected_no.splice( i, 1 );
				}
				var id = ''; var name = ''; var job = ''; var num='';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
					num += selected_no[i] + ','; 
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 );
				num 	= num.substr( 0, num.length - 1 ); 
				
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name ); 
				$('#txt_selected_no').val( num );
		}
		
		function fn_check_lot()
		{ 
			show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $company; ?>, 'create_lot_search_list_view', 'search_div', 'mrr_wise_yarn_stock_controller', 'setFilterGrid("list_view",-1)');
		}
    </script>
    <body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="500" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>                	 
						<th>Search By</th>
						<th align="center" width="200" id="search_by_td_up">Enter Lot Number</th>
 						<th>
                       		<input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  />
                            <input type='hidden' id='txt_selected_id' />
							<input type='hidden' id='txt_selected' />
							<input type='hidden' id='txt_selected_no' />
                        </th>
					</tr>
				</thead>
				<tbody>
					<tr align="center">
						<td align="center">
							<?  
								$search_by = array(1=>'Lot No', 2=>'Item Description');
								$dd="change_search_event(this.value, '0*0', '0*0', '../../')";
								echo create_drop_down( "cbo_search_by", 150, $search_by, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td width="180" align="center" id="search_by_td">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
						</td> 
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="fn_check_lot()" style="width:100px;" />				
						</td>
					</tr>
 				</tbody>
			 </tr>         
			</table>    
			<div align="center" valign="top" style="margin-top:5px" id="search_div"> </div> 
			</form>
	   </div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script> 
	</html>
    <?
	exit();
}

if($action=="create_lot_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$company = $ex_data[2];
	
	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==1) // for LOT NO
		{
			$sql_cond= " and lot LIKE '%$txt_search_common%'";	
 		}
		else if(trim($txt_search_by)==2) // for Yarn Count
		{
			$sql_cond= " and product_name_details LIKE '%$txt_search_common%'";	 	
 		} 
 	} 
	
 	$sql = "select id,supplier_id,lot,product_name_details from product_details_master where company_id=$company and item_category_id=1 $sql_cond"; 
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$arr=array(1=>$supplier_arr);
	echo create_list_view("list_view", "Product Id, Supplier, Lot, Item Description","70,160,70","600","250",0, $sql , "js_set_value", "id,product_name_details", "", 1, "0,supplier_id,0,0", $arr, "id,supplier_id,lot,product_name_details", "","","0","",1) ;	
	
	exit();	
}

if ($action=="load_drop_down_supplier")
{	  
	echo create_drop_down( "cbo_supplier", 120, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name","id,supplier_name",1, "-- Select --", 0, "",0 );  	 
	exit();
}


//report generated here--------------------//
if($action=="generate_report")
{ 
	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_product_id=str_replace("'","",$txt_product_id);
	$cbo_supplier=str_replace("'","",$cbo_supplier);
	$from_date=str_replace("'","",$from_date);
	$to_date=str_replace("'","",$to_date);
	$txt_lot_no=str_replace("'","",$txt_lot_no);
	//echo $cbo_company_name."##".$txt_product_id."##".$cbo_supplier."##".$from_date."##".$to_date."##".$txt_lot_no;die;
	$search_cond="";
	if($db_type==0)
	{
 		if( $from_date!="" && $to_date!="" ) $search_cond .= " and a.receive_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
	}
	else
	{
		if($from_date!="" && $to_date!="") $search_cond .= " and a.receive_date  between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
	}
	if($cbo_supplier!=0) $search_cond.=" and a.supplier_id='$cbo_supplier'";
	if($txt_product_id!="") $search_cond.=" and c.id in($txt_product_id)";
	if($txt_lot_no!="") $search_cond.=" and c.lot='$txt_lot_no'";

 	//library array-------------------
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
	//$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name"); 
 	 
 	// issue MRR array------------------------------------------------
	$sql_cond_mrr="";
	if($txt_product_id!="") $sql_cond_mrr.=" and a.prod_id in($txt_product_id)";
	if($cbo_supplier!=0) $sql_cond_mrr.=" and a.supplier_id='$cbo_supplier'";
	$sql_mrr_wise_issue = "select a.prod_id, a.mst_id as mrr_id, sum(b.issue_qnty) as issue_qnty
			from inv_transaction a,  inv_mrr_wise_issue_details b
			where a.company_id=$cbo_company_name and a.id=b.recv_trans_id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.balance_qnty>0 $sql_cond_mrr
			group by a.prod_id, a.mst_id";
	$result_rcv = sql_select($sql_mrr_wise_issue);
	$issueMRR=array();
	foreach($result_rcv as $row)
	{
		$issueMRR[$row[csf("prod_id")]][$row[csf("mrr_id")]] = $row[csf("issue_qnty")];
	}
	
	$sql="select a.id as mrr_id, a.recv_number, a.receive_date, sum(b.cons_quantity) as mrr_qnty, max(b.cons_rate) as mrr_rate, c.id as prod_id, c.product_name_details, c.lot, c.current_stock  from inv_receive_master a, inv_transaction b,  product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.item_category=1 and b.item_category=1 and c.item_category_id=1 and b.transaction_type=1 and b.balance_qnty>0 and a.status_active=1 and b.status_active=1 and a.company_id=$cbo_company_name $search_cond group by c.id, c.product_name_details, c.lot, c.current_stock, a.id,a.recv_number, a.receive_date order by c.id,a.id";
	
	ob_start();	
	?>
	<div style="width:920px;" align="left"> 
		<table style="width:900px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1"> 
			<thead>
				<tr class="form_caption" style="border:none;">
					<td colspan="9" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $title; ?></td> 
				</tr>
				<tr>
					<th width="50">SL</th>
					<th width="150">MRR No</th>
					<th width="80">MRR Date</th>
					<th width="100">Rcv. Qnty</th>
					<th width="100">Issue Qnty</th>
					<th width="100">Stock Qnty</th>
					<th width="100">Rate</th>
					<th width="100">Value</th>
					<th >Age</th>                    
				</tr>
			</thead>
		</table>  
		<div style="width:920px; overflow-y:scroll; max-height:300px" id="scroll_body" > 
		<table style="width:900px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">   
			<?
			$result=sql_select($sql);
			$k=1;$temp_array=array();
            foreach($result as $row)
            {
				if(!in_array($row[csf("prod_id")],$temp_array))
				{
					$temp_array[]=$row[csf("prod_id")];
					?>
                    <tr bgcolor="#FFFFCC">
                        <td colspan="9">Product Id :&nbsp;<? echo $row[csf("prod_id")]?> ,&nbsp;&nbsp; Item Description :&nbsp;<? echo $row[csf("product_name_details")]?> ,&nbsp;&nbsp; Lot :&nbsp;<? echo $row[csf("lot")]?> ,&nbsp;&nbsp; Stock Qnty :&nbsp;<? echo number_format($row[csf("current_stock")],2)?></td>
                    </tr>
                    <?
				}
				if ($k%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";
				$mrr_stock=$row[csf("mrr_qnty")]-$issueMRR[$row[csf("prod_id")]][$row[csf("mrr_id")]];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                	<td width="50" align="center"><? echo $k; ?></td>
                    <td width="150"><p><? echo $row[csf("recv_number")];?>&nbsp;</p></td>
                    <td width="80" align="center"><p><? echo change_date_format($row[csf("receive_date")]);?>&nbsp;</p></td>
                    <td width="100" align="right"><p><? echo number_format($row[csf("mrr_qnty")],2);?></p></td>
                    <td width="100" align="right"><p><? if($issueMRR[$row[csf("prod_id")]][$row[csf("mrr_id")]]!="") echo $issueMRR[$row[csf("prod_id")]][$row[csf("mrr_id")]]; else echo "&nbsp;";?></p></td>
                    <td width="100" align="right"><p><? echo number_format($mrr_stock,2);?></p></td>
                    <td width="100" align="right"><p><? echo number_format($row[csf("mrr_rate")],2);?></p></td>
                    <td width="100" align="right"><p><? $mrr_value=$mrr_stock*$row[csf("mrr_rate")]; echo number_format($mrr_value,2);?></p></td>
                    <td style="padding-left:3px;" align="center"><? $age_date=datediff('d',$row[csf("receive_date")],$pc_date); if($age_date>0) echo $age_date." Days";?></td>
                </tr>
                <?
				$k++;
			} 
            ?>
              
        </table> 
		</div>  
	</div> 
	<?	 
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename) 
	{
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

