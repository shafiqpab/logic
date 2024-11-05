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
//mrr search------------------------------//
if($action=="mrr_search")
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
			show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $company; ?>, 'create_lot_search_list_view', 'search_div', 'count_wise_monthly_yarn_status_report_controller', 'setFilterGrid("list_view",-1)');
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

if ($action=="load_drop_down_store")
{	  
	echo create_drop_down( "cbo_store", 120, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.status_active=1 and a.is_deleted=0 and company_id in(".$data.") and b.category_type in(1) group by a.id, a.store_name order by a.store_name","id,store_name",1, "-- Select --", 0, "",0 );  	 
	exit();
}

//report generated here--------------------//
if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_store=str_replace("'","",$cbo_store);
	$cbo_yarn_count=str_replace("'","",$cbo_yarn_count);
	$to_date=str_replace("'","",$to_date);
	// echo $cbo_company_name."##".$cbo_store."##".$cbo_yarn_count."##".$to_date;die;
	$to_date=date('Y-m-d', strtotime($to_date));
	$year=date('Y', strtotime($to_date));
	$month_to=date('m', strtotime($to_date));
	// echo $month_to.'='.$year;die;
	$from_date=$year."-".$month_to."-01";
    $from_date = date('Y-m-d', strtotime($from_date.'-11 month'));
    // echo $start_date = date('Y-m-d', strtotime($from_date.'-12 month')).'='.date('Y-m-d', strtotime($to_date));die;

	$search_cond="";
	if($db_type==0)
	{
 		if( $from_date!="" && $to_date!="" ) $search_cond .= " and a.receive_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
	}
	else
	{
		if($from_date!="" && $to_date!="") $search_cond .= " and a.receive_date  between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
	}
	// echo $search_cond;die;

	if($cbo_store!=0) $store_id_cond=" and a.store_id='$cbo_store'";
	if($cbo_yarn_count!=0) $yCound_cond=" and c.yarn_count_id='$cbo_yarn_count'";

 	//library array-------------------
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');

	$con = connect();
    $r_id=execute_query("delete from tmp_prod_id where userid=$user_id");
    oci_commit($con);

	// Receive MRR array------------------------------------------------
	$sql="SELECT a.id as mrr_id, a.recv_number, to_char(a.receive_date,'MON-YYYY') as month_year, a.source, b.cons_quantity as recv_qnty, c.id as prod_id, c.product_name_details, c.lot, c.yarn_count_id, c.current_stock
	from inv_receive_master a, inv_transaction b, product_details_master c
	where a.id=b.mst_id and b.prod_id=c.id and a.item_category=1 and b.item_category=1 and c.item_category_id=1 and b.transaction_type=1 and b.balance_qnty>0 and a.status_active=1 and b.status_active=1 and a.company_id=$cbo_company_name $search_cond $store_id_cond $yCound_cond order by a.receive_date desc";
	// echo $sql;die; 
	$recv_sql_data=sql_select($sql);
	$data_arr=array();
	foreach ($recv_sql_data as $key => $row) 
	{
		$data_arr[$row[csf("month_year")]][$row[csf("prod_id")]]['yarn_count_id'] = $row[csf("yarn_count_id")];
		$data_arr[$row[csf("month_year")]][$row[csf("prod_id")]]['lot'] = $row[csf("lot")];
		$data_arr[$row[csf("month_year")]][$row[csf("prod_id")]]['receive_date'] = $row[csf("receive_date")];
		if ($row[csf("source")]==1) 
		{
			$data_arr[$row[csf("month_year")]][$row[csf("prod_id")]]['import_qty'] += $row[csf("recv_qnty")];
		}
		elseif ($row[csf("source")]==3) 
		{
			$data_arr[$row[csf("month_year")]][$row[csf("prod_id")]]['local_qty'] += $row[csf("recv_qnty")];
		}
		$data_arr[$row[csf("month_year")]][$row[csf("prod_id")]]['recv_qty'] += $row[csf("recv_qnty")];

		if( $prod_id_check[$row[csf('prod_id')]] == "" )
        {
            $prod_id_check[$row[csf('prod_id')]]=$row[csf('prod_id')];
            $prod_id = $row[csf('prod_id')];
            // echo "insert into tmp_prod_id (userid, prod_id) values ($user_id,$prod_id)";
            $r_id=execute_query("insert into tmp_prod_id (userid, prod_id) values ($user_id,$prod_id)");
        }
		$prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];
	}
	oci_commit($con);
	// echo '<pre>';print_r($data_arr);

 	// issue MRR array------------------------------------------------
	
	$sql_issue = "SELECT a.prod_id, a.mst_id as mrr_id, b.issue_qnty
	from tmp_prod_id t, inv_transaction a, inv_mrr_wise_issue_details b
	where t.prod_id=a.prod_id and a.id=b.recv_trans_id and a.item_category=1 and a.company_id=$cbo_company_name and t.userid=$user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.balance_qnty>0 $store_id_cond";
	// echo $sql_issue;
	$result_issue_data = sql_select($sql_issue);
	$issue_arr=array();
	foreach($result_issue_data as $row)
	{
		$issue_arr[$row[csf("prod_id")]] += $row[csf("issue_qnty")];
	}

	$month_count=1; $first_month_name=''; $second_month_name=''; $last_month_name='';
	foreach ($data_arr as $year_month => $year_month_arr)
	{
		if ($month_count<=3) // 1 to 3 month
		{
			$first_month_name.=date('M-Y',strtotime($year_month)).', ';
		}
		if ($month_count>3 && $month_count<7) // 3 to 6 month
		{
			$second_month_name.=date('M-Y',strtotime($year_month)).', ';
		}
		if ($month_count>6) // 6 to 12 month
		{
			$last_month_name.=date('M-Y',strtotime($year_month)).', ';
		}
		$month_count++;
	}

	$con = connect();
    $r_id=execute_query("delete from tmp_prod_id where userid=$user_id");
    oci_commit($con);

	ob_start();	
	?>
	<div style="width:820px;" align="left"> 
		<table style="width:800px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1"> 
			<thead>
				<tr class="form_caption" style="border:none;">
					<td colspan="8" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $title; ?></td> 
				</tr>
				<tr>
					<th width="50">SL</th>
					<th width="80">Count</th>
					<th width="150">Lot</th>
					<th width="100">Received qty</th>
					<th width="100">Import Qty</th>
					<th width="100">Local Qty</th>
					<th width="100">Issue QTY</th>
					<th width="">Balance</th>                 
				</tr>
			</thead>
		</table>  
		<div style="width:820px; overflow-y:scroll; max-height:300px" id="scroll_body" > 
			<table style="width:800px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">   
				<?			
				$k=1; $j=1;
				$total_recv_qty=$total_import_qty=$total_local_qty=$total_issue_qty=$total_balance_qnty=0;
				foreach ($data_arr as $year_month => $year_month_arr)
				{
					if ($j==1) // 1 to 3 month
					{
						// echo $j.'<br>';
						?>
                        <tr>
                            <th colspan="8" style="background: #C2DCFF; text-align: left; padding-left: 15px; text-align: center; ">
								<? echo '1 To 3 Month - '.chop($first_month_name,', ');  ?>
                            </th>
                        </tr>
						<?
					}

					if ($j==4 && $j<7) // 3 to 6 month
					{
						?>
                        <tr>
                            <th colspan="8" style="background: #C2DCFF; text-align: left; padding-left: 15px; text-align: center;">
								<? echo '3 to 6 month - '.chop($second_month_name,', ');  ?>
                            </th>
                        </tr>
						<?
					}

					if ($j==7) // 6 to 12 month
					{
						?>
                        <tr>
                            <th colspan="8" style="background: #C2DCFF; text-align: left; padding-left: 15px; text-align: center;">
								<? echo '6 to 12 month - '.chop($last_month_name,', ');  ?>
                            </th>
                        </tr>
						<?
					}

					foreach ($year_month_arr as $prod_id => $row) 
					{
						if ($k%2==0)
						$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$balance_qnty=$row["recv_qty"]-$issue_arr[$prod_id];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
		                	<td width="50"><p><? echo $k; ?></p></td>
		                    <td width="80"><p><? echo $yarn_count_arr[$row["yarn_count_id"]];?>&nbsp;</p></td>
		                    <td width="150" title="Prod ID: <? echo $prod_id; ?>"><p><? echo $row["lot"]; //echo $year_month;?>&nbsp;</p></td>
		                    <td width="100" align="right"><p><? echo number_format($row["recv_qty"],2,".","");?></p></td>
		                    <td width="100" align="right"><p><? echo number_format($row["import_qty"],2,".",""); ?></p></td>
		                    <td width="100" align="right"><p><? echo number_format($row["local_qty"],2,".","");?></p></td>
		                    <td width="100" align="right"><p><? echo number_format($issue_arr[$prod_id],2,".",""); ?></p></td>
		                    <td width="" align="right"><p><? echo number_format($balance_qnty,2,".","");?></p></td>
		                </tr>
		                <?
						$k++;
						$total_recv_qty += $row["recv_qty"];
						$total_import_qty += $row["import_qty"];
						$total_local_qty += $row["local_qty"];
						$total_issue_qty += $issue_arr[$prod_id];
						$total_balance_qnty += $balance_qnty;
					}

					if ($j==3) // 1 to 3 month total
					{
						$first_month_name='';
						?>
                        <tr>
		                    <td width="50"></td>
							<td width="80"></td>
							<td width="150" align="right"><strong>Month Total:</strong></td>
							<td width="100" align="right"><? echo number_format($total_recv_qty,2,".",""); ?></td>
							<td width="100" align="right"><? echo number_format($total_import_qty,2,".",""); ?></td>
							<td width="100" align="right"><? echo number_format($total_local_qty,2,".",""); ?></td>
							<td width="100" align="right"><? echo number_format($total_issue_qty,2,".",""); ?></td>
							<td width="" align="right"><? echo number_format($total_balance_qnty,2,".",""); ?></td>
		                </tr>
						<?
						unset($total_recv_qty);
						unset($total_import_qty);
						unset($total_local_qty);
						unset($total_issue_qty);
						unset($total_balance_qnty);
					}
					if ($j==6 && $j<7) // 3 to 6 month total
					{
						$second_month_name='';
						?>
                        <tr>
		                    <td width="50"></td>
							<td width="80"></td>
							<td width="150" align="right"><strong>Month Total:</strong></td>
							<td width="100" align="right"><? echo number_format($total_recv_qty,2,".",""); ?></td>
							<td width="100" align="right"><? echo number_format($total_import_qty,2,".",""); ?></td>
							<td width="100" align="right"><? echo number_format($total_local_qty,2,".",""); ?></td>
							<td width="100" align="right"><? echo number_format($total_issue_qty,2,".",""); ?></td>
							<td width="" align="right"><? echo number_format($total_balance_qnty,2,".",""); ?></td>
		                </tr>
						<?
						unset($total_recv_qty);
						unset($total_import_qty);
						unset($total_local_qty);
						unset($total_issue_qty);
						unset($total_balance_qnty);
					}
					if ($j==12) // 6 to 12 month total
					{
						$last_month_name='';
						?>
                        <tr>
		                    <td width="50"></td>
							<td width="80"></td>
							<td width="150" align="right"><strong>Month Total:</strong></td>
							<td width="100" align="right"><? echo number_format($total_recv_qty,2,".",""); ?></td>
							<td width="100" align="right"><? echo number_format($total_import_qty,2,".",""); ?></td>
							<td width="100" align="right"><? echo number_format($total_local_qty,2,".",""); ?></td>
							<td width="100" align="right"><? echo number_format($total_issue_qty,2,".",""); ?></td>
							<td width="" align="right"><? echo number_format($total_balance_qnty,2,".",""); ?></td>
		                </tr>
						<?
						unset($total_recv_qty);
						unset($total_import_qty);
						unset($total_local_qty);
						unset($total_issue_qty);
						unset($total_balance_qnty);
					}
					$j++;
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

