<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

$seource_des_array=array(3=>"Non EPZ",4=>"Non EPZ",5=>"Abroad",6=>"Abroad",11=>"EPZ",12=>"EPZ");

if($action=="source_surch")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", '', '', $unicode);
	?>
    <script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() 
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			var onclickString=paramArr=functionParam="";
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
				onclickString = $('#tr_' + i).attr('onclick');
				paramArr = onclickString.split("'");
				functionParam = paramArr[1];
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
			//alert(strCon);
				var splitSTR = strCon.split("_");
				var str_or = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
				
				toggle( document.getElementById( 'tr_' + str_or ), '#FFFFCC' );
				
				if( jQuery.inArray( str_or, selected_no ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );
					selected_no.push( str_or );				
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
				//alert(num);
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name ); 
				$('#txt_selected_no').val( num );
		}
		
		function frm_close()
		{
			parent.emailwindow.hide();
		}
    </script>
    <?
	$company_id=str_replace("'","",$company_id);
	//$sql="select max(id) as id, lc_category from com_btb_lc_master_details where importer_id=$company_id and lc_category in('01','1','02','2','03','3','04','4','05','5','06','6','11','12','99') group by lc_category";
	$sql="select max(id) as id, lc_category from com_btb_lc_master_details group by lc_category";
	$sql_result=sql_select($sql);
	//echo $sql;die;
	?>
    
    <div style="width:100%">
    <table width="480" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="" align="left">
    	<thead>
        	<tr>
                <th width="50">Sl</th>
                <th>Import Source</th>
            </tr>
        </thead>
    </table>
    <div style="width:500px; max-height:300px; overflow-y:scroll;">
    <table width="480" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="list_view" align="left">
        <tbody>
			<?
            $i=1;
            foreach($sql_result as $row)
            {
                if ($i%2==0)
                $bgcolor="#E9F3FF";
                else
                $bgcolor="#FFFFFF";
                $seource_des=$seource_des=$supply_source[$row[csf("lc_category")]*1];
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="js_set_value('<? echo $i."_".$row[csf("lc_category")]."_".$seource_des; ?>')" style="cursor:pointer">
                    <td width="50"  align="center"><? echo $i; ?></td>
                    <td><? echo $seource_des; ?></td>
                </tr>
                <?
                $i++;
            }
            ?>
            <tr>
                <td  style="vertical-align:middle; padding-left:20px;" colspan="2"><input type="checkbox" id="all_check" onClick="check_all_data('all_check')" />&nbsp;Check All
                <input type='hidden' id='txt_selected_id' />
                <input type='hidden' id='txt_selected' />
                <input type='hidden' id='txt_selected_no' />
                </td>
            </tr>
        </tbody>
    </table>
    </div>
    <br>
    <div style="width:100%"><p align="center"><input type="button" id="btn_close" class="formbutton" style="width:100px;" value="Close" onClick="frm_close();" ></p></div>
    </div>
	
    <script language="javascript" type="text/javascript">
	var category_no='<? echo $txt_serial_no;?>';
	var category_id='<? echo $txt_lc_category;?>';
	var category_des='<? echo $import_source;?>';
	var cate_ref="";
	if(category_no!="")
	{
		category_no_arr=category_no.split(",");
		category_id_arr=category_id.split(",");
		category_des_arr=category_des.split(",");
		var str_ref="";
		for(var k=0;k<category_no_arr.length; k++)
		{
			cate_ref=category_no_arr[k]+'_'+category_id_arr[k]+'_'+category_des_arr[k];
			js_set_value(cate_ref);
		}
	}
	</script>
    <?
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_lein_bank=str_replace("'","",$cbo_lein_bank);
	$cbo_item_category=str_replace("'","",$cbo_item_category);
	$txt_lc_category=str_replace("'","",$txt_lc_category);  
	$hide_year=str_replace("'","",$hide_year);
	//echo $hide_year;die;
	$company_arr=return_library_array( "select company_name,id from  lib_company",'id','company_name');
	$bank_arr=return_library_array( "select bank_name,id from lib_bank",'id','bank_name');
	$payment_data_array=return_library_array("select invoice_id, sum(accepted_ammount) as paid_amt from com_import_payment where status_active=1 and is_deleted=0 group by invoice_id","invoice_id","paid_amt");
	$sql_cond="";
	if($cbo_company_name>0) $sql_cond.=" and a.importer_id=$cbo_company_name";
	if($cbo_lein_bank>0) $sql_cond.=" and a.issuing_bank_id=$cbo_lein_bank";
	if($cbo_item_category>0) $sql_cond.=" and p.item_category_id=$cbo_item_category";
	if($txt_lc_category!="") $sql_cond.=" and a.lc_category in($txt_lc_category)";
	
	$btb_sql="select p.item_category_id, a.id as btb_lc_id, a.importer_id, a.issuing_bank_id, a.lc_date, a.tenor, a.payterm_id, a.lc_category, a.lc_value, b.id as import_inv_id, b.maturity_date, b.edf_paid_date, c.id as inv_dtls_id, c.current_acceptance_value as edf_loan_value
	from com_pi_item_details p, com_btb_lc_pi q, com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
	where p.pi_id=q.pi_id and p.pi_id=c.pi_id and q.pi_id=c.pi_id and q.com_btb_lc_master_details_id=a.id and a.id=c.btb_lc_id and c.import_invoice_id=b.id and a.lc_category>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.ref_closing_status<>1 $sql_cond
	order by b.maturity_date";
	//echo $btb_sql;die;
	$btb_result=sql_select($btb_sql);
	$total_data=array();
	foreach($btb_result as $row)
	{
		if($inv_dtls_check[$row[csf("inv_dtls_id")]]=="" && $row[csf("maturity_date")]!="" && $row[csf("maturity_date")]!="0000-00-00")
		{
			$inv_dtls_check[$row[csf("inv_dtls_id")]]=$row[csf("inv_dtls_id")];
			$total_data[$row[csf("import_inv_id")]]["item_category_id"]=$row[csf("item_category_id")];
			$total_data[$row[csf("import_inv_id")]]["btb_lc_id"]=$row[csf("btb_lc_id")];
			$total_data[$row[csf("import_inv_id")]]["importer_id"]=$row[csf("importer_id")];
			$total_data[$row[csf("import_inv_id")]]["issuing_bank_id"]=$row[csf("issuing_bank_id")];
			$total_data[$row[csf("import_inv_id")]]["lc_date"]=$row[csf("lc_date")];
			$total_data[$row[csf("import_inv_id")]]["tenor"]=$row[csf("tenor")];
			$total_data[$row[csf("import_inv_id")]]["payterm_id"]=$row[csf("payterm_id")];
			$total_data[$row[csf("import_inv_id")]]["lc_category"]=$row[csf("lc_category")];
			$total_data[$row[csf("import_inv_id")]]["lc_value"]=$row[csf("lc_value")];
			$total_data[$row[csf("import_inv_id")]]["import_inv_id"]=$row[csf("import_inv_id")];
			$total_data[$row[csf("import_inv_id")]]["maturity_date"]=$row[csf("maturity_date")];
			$total_data[$row[csf("import_inv_id")]]["edf_paid_date"]=$row[csf("edf_paid_date")];
			$total_data[$row[csf("import_inv_id")]]["edf_loan_value"]+=$row[csf("edf_loan_value")];
		}
	}
	unset($btb_result);
	$all_data=$all_data_month_wise=$company_data=$company_data_month_wise=$bank_data=$bank_data_month_wise=array();
	$current_month_arr=array();
	//echo "<pre>";print_r($total_data);die;
	$i=1;
	foreach($total_data as $inv_id=>$val)
	{
		$maturity_year=date('Y',strtotime($val["maturity_date"]));
		$cu_amt=($val["edf_loan_value"]-$payment_data_array[$inv_id])*1;
		if($cu_amt>0) $test_data.=$maturity_year*1 ."=". $hide_year*1 ."=". $val["edf_loan_value"] ."=".$payment_data_array[$inv_id] ."=".$cu_amt.",";
		if($cu_amt>0)
		{
			if(($maturity_year*1) < ($hide_year*1))
			{
				$test2 .=$i.",";
				$all_data[$val["item_category_id"]][$val["lc_category"]]["previous"]+=$val["edf_loan_value"]-$payment_data_array[$inv_id];
				$company_data[$val["importer_id"]][$val["item_category_id"]][$val["lc_category"]]["previous"]+=$val["edf_loan_value"]-$payment_data_array[$inv_id];
				$bank_data[$val["issuing_bank_id"]][$val["item_category_id"]][$val["lc_category"]]["previous"]+=$val["edf_loan_value"]-$payment_data_array[$inv_id];
			}
			else
			{
				$test2 .=$i."=";
				$all_data[$val["item_category_id"]][$val["lc_category"]]["current"]+=$val["edf_loan_value"]-$payment_data_array[$inv_id];
				$company_data[$val["importer_id"]][$val["item_category_id"]][$val["lc_category"]]["current"]+=$val["edf_loan_value"]-$payment_data_array[$inv_id];
				$bank_data[$val["issuing_bank_id"]][$val["item_category_id"]][$val["lc_category"]]["current"]+=$val["edf_loan_value"]-$payment_data_array[$inv_id];
				if($cu_amt>0)
				{
					$test_data.=date('n',strtotime($val["maturity_date"])). "=" .$maturity_year*1 ."=". $hide_year*1 ."=". $val["edf_loan_value"] ."=".$payment_data_array[$inv_id] ."=".$cu_amt.","; 
					$current_month_arr[date('n',strtotime($val["maturity_date"]))]=date('n',strtotime($val["maturity_date"]));
					$all_data_month_wise[$val["item_category_id"]][$val["lc_category"]][date('n',strtotime($val["maturity_date"]))]+=$val["edf_loan_value"]-$payment_data_array[$inv_id];
					$company_data_month_wise[$val["importer_id"]][$val["item_category_id"]][$val["lc_category"]][date('n',strtotime($val["maturity_date"]))]+=$val["edf_loan_value"]-$payment_data_array[$inv_id];
					$bank_data_month_wise[$val["issuing_bank_id"]][$val["item_category_id"]][$val["lc_category"]][date('n',strtotime($val["maturity_date"]))]+=$val["edf_loan_value"]-$payment_data_array[$inv_id];
				}
				
			}
		}
		$i++;
	}
	//echo $test_data ."<br>".$test2;die;
	//echo "<pre>";print_r($current_month_arr);
	//echo "all <pre>";print_r($all_data);echo "all month <pre>";print_r($all_data_month_wise);
	//echo "company <pre>";print_r($company_data);echo "company month <pre>";print_r($company_data_month_wise);die;
	//echo "bank <pre>";print_r($bank_data);echo "bank month <pre>";print_r($bank_data_month_wise);
	//die;
	ksort($all_data);
	ksort($company_data);
	ksort($bank_data);
	$div_width=670+count($current_month_arr)*100;
	$table_width=650+count($current_month_arr)*100;
	ob_start();
	?>
	<div style="width:<? echo $div_width;?>px;" id="scroll_body">
		<p style="font-size:20px; font-weight:bold">Total Liability Possition</p>
		<table width="<? echo $div_width;?>" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="" align="left">
			<thead>
				<tr>
					<th width="40">SL</th>
                    <th width="120">Item Name</th>
					<th width="200">Import Source</th>
					<th width="100">Previous Year</th>
                    <?
					foreach($current_month_arr as $month_id=>$month_val)
					{
						?>
                        <th width="100"><? echo $months[$month_id]."-".$hide_year; ?></th>
                        <?
					}
					?>
					
					<th width="100">Total (USD)</th>
					<th>Total (BDT)</th>
				</tr>
			</thead>
			<tbody>
				<?
				$i=1;$k=1;
                foreach($all_data as $item_cat=>$cat_val)
				{
					$cat_prev_total=0;
					foreach($cat_val as $source_id=>$source_val)
					{
						if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td align="center"><? echo $k; ?></td>
                            <td><? echo $item_category[$item_cat]; ?></td>
                            <td title="<? echo $source_id;?>"><? echo $supply_source[$source_id*1]; ?></td>
                            <td align="right"><? echo number_format($source_val["previous"],2);$cat_prev_total+=$source_val["previous"]; $gr_all_total+=$source_val["previous"]; ?></td>
                            <?
							$row_total=0;
							foreach($current_month_arr as $month_id=>$month_val)
							{
								?>
                                <td align="right"><? echo number_format($all_data_month_wise[$item_cat][$source_id][$month_id],2); ?></td>
                                <?
								$row_total+=$all_data_month_wise[$item_cat][$source_id][$month_id];
								$all_month_total[$month_id]+=$all_data_month_wise[$item_cat][$source_id][$month_id];
								$cat_all_total[$item_cat][$month_id]+=$all_data_month_wise[$item_cat][$source_id][$month_id];
								
							}
							?>
                            <td align="right"><? echo number_format($row_total,2); ?></td>
                            <td align="right"><? echo number_format(($row_total*80),2); ?></td>
                        </tr>
                        <?
						$i++;$k++;
					}
					
					?>
                    <tr bgcolor="#CCCCCC">
                        <td colspan="3" align="right"> Sub Total:</td>
                        <td align="right"><? echo number_format($cat_prev_total,2);?></td>
                        <?
                        $row_sub_total=0;
                        foreach($current_month_arr as $month_id=>$month_val)
                        {
                            ?>
                            <td align="right"><? echo number_format($cat_all_total[$item_cat][$month_id],2); ?></td>
                            <?
                            $row_sub_total+=$cat_all_total[$item_cat][$month_id];
                            
                        }
                        ?>
                        <td align="right"><? echo number_format($row_sub_total,2); ?></td>
                        <td align="right"><? echo number_format(($row_sub_total*80),2); ?></td>
                    </tr>
                    <?
				}
                ?>
                <tr bgcolor="#CCCCCC">
                    <td colspan="3" align="right"> Grand Total:</td>
                    <td align="right"><? echo number_format($gr_all_total,2);?></td>
                    <?
                    $gt_all_total=0;
                    foreach($current_month_arr as $month_id=>$month_val)
                    {
                        ?>
                        <td align="right"><? echo number_format($all_month_total[$month_id],2); ?></td>
                        <?
                        $gt_all_total+=$all_month_total[$month_id];
                        
                    }
                    ?>
                    <td align="right"><? echo number_format($gt_all_total,2); ?></td>
                    <td align="right"><? echo number_format(($gt_all_total*80),2); ?></td>
                </tr>
			</tbody>
		</table>
        <br />&nbsp;<br />
        <p style="font-size:20px; font-weight:bold;">Company Wise Liability Possition</p>
		<?
        foreach($company_data as $com_id=>$com_val)
        {
            $gr_com_total=0;
            echo '<p style="font-size:16px; font-weight:bold;  text-align:left">'.$company_arr[$com_id].'</p>';
            ?>
            <table width="<? echo $div_width;?>" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="" align="left">
                <thead>
                    <tr>
                        <th width="40">SL</th>
                        <th width="120">Item Name</th>
                        <th width="200">Import Source</th>
                        <th width="100">Previous Year</th>
                        <?
                        foreach($current_month_arr as $month_id=>$month_val)
                        {
                            ?>
                            <th width="100"><? echo $months[$month_id]."-".$hide_year; ?></th>
                            <?
                        }
                        ?>
                        
                        <th width="100">Total (USD)</th>
                        <th>Total (BDT)</th>
                    </tr>
                </thead>
                <tbody>
                    <?
                    $k=1;
                    foreach($com_val as $item_cat=>$cat_val)
                    {
                        $cat_prev_total=0;
                        foreach($cat_val as $source_id=>$source_val)
                        {
                            if ($i%2==0)
                            $bgcolor="#E9F3FF";
                            else
                            $bgcolor="#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td align="center"><? echo $k; ?></td>
                                <td><? echo $item_category[$item_cat]; ?></td>
                                <td title="<? echo $source_id;?>"><? echo $supply_source[$source_id*1]; ?></td>
                                <td align="right"><? echo number_format($source_val["previous"],2);$cat_prev_total+=$source_val["previous"]; $gr_com_total+=$source_val["previous"]; ?></td>
                                <?
                                $com_row_total=0;
                                foreach($current_month_arr as $month_id=>$month_val)
                                {
                                    ?>
                                    <td align="right"><? echo number_format($company_data_month_wise[$com_id][$item_cat][$source_id][$month_id],2); ?></td>
                                    <?
                                    $com_row_total += $company_data_month_wise[$com_id][$item_cat][$source_id][$month_id]*1;
                                    $com_month_total[$com_id][$month_id]+=$company_data_month_wise[$com_id][$item_cat][$source_id][$month_id];
                                    $cat_com_total[$com_id][$item_cat][$month_id]+=$company_data_month_wise[$com_id][$item_cat][$source_id][$month_id];
                                    
                                }
                                ?>
                                <td align="right"><? echo number_format($com_row_total,2); ?></td>
                                <td align="right"><? echo number_format(($com_row_total*80),2); ?></td>
                            </tr>
                            <?
                            $i++;$k++;
                        }
                        ?>
                        <tr bgcolor="#CCCCCC">
                            <td colspan="3" align="right"> Sub Total:</td>
                            <td align="right"><? echo number_format($cat_prev_total,2);?></td>
                            <?
                            $row_sub_total=0;
                            foreach($current_month_arr as $month_id=>$month_val)
                            {
                                ?>
                                <td align="right"><? echo number_format($cat_com_total[$com_id][$item_cat][$month_id],2); ?></td>
                                <?
                                $row_sub_total+=$cat_com_total[$com_id][$item_cat][$month_id];
                                
                            }
                            ?>
                            <td align="right"><? echo number_format($row_sub_total,2); ?></td>
                            <td align="right"><? echo number_format(($row_sub_total*80),2); ?></td>
                        </tr>
                        <?
                    }
                    ?>
                    <tr bgcolor="#CCCCCC">
                        <td colspan="3" align="right"> Company Total:</td>
                        <td align="right"><? echo number_format($gr_com_total,2);?></td>
                        <?
                        $gt_all_total=0;
                        foreach($current_month_arr as $month_id=>$month_val)
                        {
                            ?>
                            <td align="right"><? echo number_format($com_month_total[$com_id][$month_id],2); ?></td>
                            <?
                            $gt_all_total+=$com_month_total[$com_id][$month_id];
                            
                        }
                        ?>
                        <td align="right"><? echo number_format($gt_all_total,2); ?></td>
                        <td align="right"><? echo number_format(($gt_all_total*80),2); ?></td>
                    </tr>
                </tbody>
            </table>
            <?
        }
		?>
		<br />&nbsp;<br />
        <p style="font-size:20px; font-weight:bold;">Bank Wise Liability Possition</p>
		<?
        foreach($bank_data as $bank_id=>$bank_val)
        {
            $gr_bank_total=0;
            echo '<p style="font-size:16px; font-weight:bold;  text-align:left">'.$bank_arr[$bank_id].'</p>';
            ?>
            <table width="<? echo $div_width;?>" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="" align="left">
                <thead>
                    <tr>
                        <th width="40">SL</th>
                        <th width="120">Item Name</th>
                        <th width="200">Import Source</th>
                        <th width="100">Previous Year</th>
                        <?
                        foreach($current_month_arr as $month_id=>$month_val)
                        {
                            ?>
                            <th width="100"><? echo $months[$month_id]."-".$hide_year; ?></th>
                            <?
                        }
                        ?>
                        
                        <th width="100">Total (USD)</th>
                        <th>Total (BDT)</th>
                    </tr>
                </thead>
                <tbody>
                    <?
                    $k=1;
                    foreach($bank_val as $item_cat=>$cat_val)
                    {
                        $cat_prev_total=0;
                        foreach($cat_val as $source_id=>$source_val)
                        {
                            if ($i%2==0)
                            $bgcolor="#E9F3FF";
                            else
                            $bgcolor="#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td align="center"><? echo $k; ?></td>
                                <td><? echo $item_category[$item_cat]; ?></td>
                                <td title="<? echo $source_id;?>"><? echo $supply_source[$source_id*1]; ?></td>
                                <td align="right"><? echo number_format($source_val["previous"],2);$cat_prev_total+=$source_val["previous"]; $gr_bank_total+=$source_val["previous"]; ?></td>
                                <?
                                $bank_row_total=0;
                                foreach($current_month_arr as $month_id=>$month_val)
                                {
                                    ?>
                                    <td align="right"><? echo number_format($bank_data_month_wise[$bank_id][$item_cat][$source_id][$month_id],2); ?></td>
                                    <?
                                    $bank_row_total += $bank_data_month_wise[$bank_id][$item_cat][$source_id][$month_id]*1;
                                   	// $bank_month_total[$com_id][$month_id]+=$bank_data_month_wise[$bank_id][$item_cat][$source_id][$month_id];
                                    $bank_month_total[$bank_id][$month_id]+=$bank_data_month_wise[$bank_id][$item_cat][$source_id][$month_id];
                                    //$cat_bank_total[$com_id][$item_cat][$month_id]+=$bank_data_month_wise[$bank_id][$item_cat][$source_id][$month_id];
                                    $cat_bank_total[$bank_id][$item_cat][$month_id]+=$bank_data_month_wise[$bank_id][$item_cat][$source_id][$month_id];
                                    
                                }
                                ?>
                                <td align="right"><? echo number_format($bank_row_total,2); ?></td>
                                <td align="right"><? echo number_format(($bank_row_total*80),2); ?></td>
                            </tr>
                            <?
                            $i++;$k++;
                        }
                        ?>
                        <tr bgcolor="#CCCCCC">
                            <td colspan="3" align="right"> Sub Total:</td>
                            <td align="right"><? echo number_format($cat_prev_total,2);?></td>
                            <?
                            $row_sub_total=0;
                            foreach($current_month_arr as $month_id=>$month_val)
                            {
                                ?>
                                <td align="right"><? echo number_format($cat_bank_total[$bank_id][$item_cat][$month_id],2); ?></td>
                                <?
                                $row_sub_total+=$cat_bank_total[$bank_id][$item_cat][$month_id];
                                
                            }
                            ?>
                            <td align="right"><? echo number_format($row_sub_total,2); ?></td>
                            <td align="right"><? echo number_format(($row_sub_total*80),2); ?></td>
                        </tr>
                        <?
                    }
                    ?>
                    <tr bgcolor="#CCCCCC">
                        <td colspan="3" align="right"> Bank Total:</td>
                        <td align="right"><? echo number_format($gr_bank_total,2);?></td>
                        <?
                        $gt_all_total=0;
                        foreach($current_month_arr as $month_id=>$month_val)
                        {
                            ?>
                            <td align="right"><? echo number_format($bank_month_total[$bank_id][$month_id],2); ?></td>
                            <?
                            $gt_all_total+=$bank_month_total[$bank_id][$month_id];
                            
                        }
                        ?>
                        <td align="right"><? echo number_format($gt_all_total,2); ?></td>
                        <td align="right"><? echo number_format(($gt_all_total*80),2); ?></td>
                    </tr>
                </tbody>
            </table>
            <?
        }
        ?>
	</div>
	<?
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$total_data=ob_get_contents();
	ob_clean();
	$is_created = fwrite($create_new_doc,$total_data);
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}


if($action=="btb_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	if($btb_id=="") die;
	$day_of_month=cal_days_in_month(CAL_GREGORIAN, $month_val, $year_val);
	$maturity_start_date=$year_val."-".$month_val."-01";
	$maturity_end_date=$year_val."-".$month_val."-".$day_of_month;
	if($db_type==2)
	{
		$maturity_start_date=change_date_format($maturity_start_date,"","",1);
		$maturity_end_date=change_date_format($maturity_end_date,"","",1);
	}
	//echo $maturity_start_date."**".$maturity_end_date;die;
	//print_r($po_id);die;
?> 

<script>

	function print_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>
<div style="width:660px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
<div style="width:660px" id="report_container">
<fieldset style="width:660px">
    <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="660">
        <thead>
        	<tr>
                <th width="40">SL NO</th>
                <th width="120">BTB LC NO</th>
                <th width="70">BTB LC Date</th>
                <th width="100">BTB LC Value</th>
                <th width="120">Invoice No</th>
                <th width="100">Invoice Value</th>
                <th>Maturity Date</th>
            </tr>
        </thead>
        <tbody>
		<?
		//for show file year
		/*
		$lc_year_sql=sql_select("select id as lc_sc_id, lc_year as lc_sc_year, 0 as type from com_export_lc union all select id as lc_sc_id, sc_year as lc_sc_year, 1 as type from com_sales_contract");
		$lc_sc_year=array();
		foreach($lc_year_sql as $row)
		{
			$lc_sc_year[$row[csf("lc_sc_id")]][$row[csf("type")]]=$row[csf("lc_sc_year")];
		}*/
		
		if($btb_id!="")
		{
			//previous query with file year and export lc/sc
			/*$btb_sql="select b.lc_sc_id, b.is_lc_sc, c.id as btb_lc_sc_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.lc_value, d.id as invoice_id, d.invoice_no, d.invoice_date, d.maturity_date, sum(e.current_acceptance_value) as inv_value
			from 
					com_btb_export_lc_attachment b,  com_btb_lc_master_details c, com_import_invoice_mst d, com_import_invoice_dtls e
			where 
					b.import_mst_id=c.id and c.id=e.btb_lc_id and e.import_invoice_id=d.id and c.id in($btb_id) and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1  and e.is_deleted=0 and e.status_active=1 and d.maturity_date between '$maturity_start_date' and '$maturity_end_date' 
			group by b.lc_sc_id, b.is_lc_sc, c.id, c.lc_number, c.lc_date, c.lc_value, d.id, d.invoice_no, d.invoice_date, d.maturity_date";*/
			
			if($type==3)
			{
				$btb_sql="select c.id as btb_lc_sc_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.lc_value, d.id as invoice_id, d.invoice_no, d.invoice_date, d.maturity_date, sum(e.current_acceptance_value) as inv_value
				from 
						com_btb_lc_master_details c, com_import_invoice_mst d, com_import_invoice_dtls e
				where 
						c.id=e.btb_lc_id and e.import_invoice_id=d.id and c.id in($btb_id) and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1  and e.is_deleted=0 and e.status_active=1 and c.lc_date between '$maturity_start_date' and '$maturity_end_date' 
				group by c.id, c.lc_number, c.lc_date, c.lc_value, d.id, d.invoice_no, d.invoice_date, d.maturity_date";
				
				
			}
			else
			{
				$btb_sql="select c.id as btb_lc_sc_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.lc_value, d.id as invoice_id, d.invoice_no, d.invoice_date, d.maturity_date, sum(e.current_acceptance_value) as inv_value
				from 
						com_btb_lc_master_details c, com_import_invoice_mst d, com_import_invoice_dtls e
				where 
						c.id=e.btb_lc_id and e.import_invoice_id=d.id and c.id in($btb_id) and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1  and e.is_deleted=0 and e.status_active=1 and d.maturity_date between '$maturity_start_date' and '$maturity_end_date' 
				group by c.id, c.lc_number, c.lc_date, c.lc_value, d.id, d.invoice_no, d.invoice_date, d.maturity_date";
			}
			
		}
		
		//echo $btb_sql;
		
		
		$payment_data_array=return_library_array("select invoice_id, sum(accepted_ammount) as paid_amt from com_import_payment where status_active=1 and is_deleted=0 group by invoice_id","invoice_id","paid_amt");
		
		
		$i=1;
        $sql_re=sql_select($btb_sql);
        $total_invoice_qty=0;  $total_order_qty=0;  $total_attach_qty=0;$result=0;
        
        foreach($sql_re as $row)
        {
			if($type==2)
			{
				$invoice_value=$row[csf("inv_value")]-$payment_data_array[$row[csf("invoice_id")]];
				if($invoice_value>0)
				{
					$lc_value=$payment_data_array[$row[csf('btb_lc_date')]];
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";  
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td align="center"><? echo $i; ?></td>
						<td><p><?  echo $row[csf('btb_lc_number')]; ?>&nbsp;</p></td>
						<td align="center"><? if($row[csf('btb_lc_date')]!="" && $row[csf('btb_lc_date')]!="0000-00-00") echo change_date_format($row[csf('btb_lc_date')]); ?></td>
						<!--<td align="center"><?//  echo $lc_sc_year[$row[csf("lc_sc_id")]][$row[csf("is_lc_sc")]];?></td>-->
						<td align="right"><?  echo number_format($row[csf('lc_value')],2); $total_lc_value+=$row[csf('lc_value')]; ?></td>
						<td><p><?  echo $row[csf("invoice_no")]; ?>&nbsp;</p></td>
						<td align="right"><?  echo number_format($invoice_value,2); $total_invoice_value+=$invoice_value;  ?></td>
						<td align="center"><? if($row[csf('maturity_date')]!="" && $row[csf('maturity_date')]!="0000-00-00") echo change_date_format($row[csf('maturity_date')]); ?></td>
					</tr>
					<?
					$i++;
				}
			}
			else
			{
				$lc_value=$invoice_value=0;
				$invoice_value=$row[csf("inv_value")];
				$lc_value=$row[csf('lc_value')]-$payment_data_array[$row[csf('invoice_id')]];
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";  
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
					<td align="center"><? echo $i; ?></td>
					<td><p><?  echo $row[csf('btb_lc_number')]; ?>&nbsp;</p></td>
					<td align="center"><? if($row[csf('btb_lc_date')]!="" && $row[csf('btb_lc_date')]!="0000-00-00") echo change_date_format($row[csf('btb_lc_date')]); ?></td>
					<!--<td align="center"><?//  echo $lc_sc_year[$row[csf("lc_sc_id")]][$row[csf("is_lc_sc")]];?></td>-->
					<td align="right"><?  echo number_format($lc_value,2); $total_lc_value+=$lc_value; ?></td>
					<td><p><?  echo $row[csf("invoice_no")]; ?>&nbsp;</p></td>
					<td align="right"><?  echo number_format($invoice_value,2); $total_invoice_value+=$invoice_value;  ?></td>
					<td align="center"><? if($row[csf('maturity_date')]!="" && $row[csf('maturity_date')]!="0000-00-00") echo change_date_format($row[csf('maturity_date')]); ?></td>
				</tr>
				<?
				$i++;
			}
			
        }
        ?>
        </tbody>
        <tfoot>
            <tr >
                <th align="right">&nbsp;</th>
                <th align="right" >&nbsp;</th>
                <!--<th align="right">&nbsp;</th>-->
                <th align="right">Total:</th>
                <th align="right"><? echo number_format($total_lc_value,2); ?></th>
                <th align="right">&nbsp;</th>
                <th align="right"><? echo number_format($total_invoice_value,2); ?></th>
                <th align="right">&nbsp;</th>
            </tr>
        </tfoot>
    </table>
</fieldset>
</div>
<?
exit();
}


if($action=="btb_open_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $btb_id;die;
	if($btb_id=="") die;
	$day_of_month=cal_days_in_month(CAL_GREGORIAN, $month_val, $year_val);
	$maturity_start_date=$year_val."-".$month_val."-01";
	$maturity_end_date=$year_val."-".$month_val."-".$day_of_month;
	if($db_type==2)
	{
		$maturity_start_date=change_date_format($maturity_start_date,"","",1);
		$maturity_end_date=change_date_format($maturity_end_date,"","",1);
	}
	//echo $maturity_start_date."**".$maturity_end_date;die;
	//print_r($po_id);die;
?> 

<script>

	function print_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>
<div style="width:450px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
<div style="width:450px" id="report_container">
<fieldset style="width:450px">
    <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="450">
        <thead>
        	<tr>
                <th width="50">SL NO</th>
                <th width="150">BTB LC NO</th>
                <th width="100">BTB LC Date</th>
                <th>BTB LC Value</th>
            </tr>
        </thead>
        <tbody>
		<?
		
		if($btb_id!="")
		{
			
			$btb_sql="select c.id as btb_lc_sc_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.lc_value
			from 
					com_btb_lc_master_details c 
			where 
					 c.id in($btb_id) and c.is_deleted=0 and c.status_active=1";
			
			if($type==5)
			{
				//$payment_data_array=return_library_array("select lc_id, sum(accepted_ammount) as paid_amt from  com_import_payment where status_active=1 and is_deleted=0 group by invoice_id","lc_id","paid_amt");
				$payment_data_array=return_library_array("select invoice_id, sum(accepted_ammount) as paid_amt from  com_import_payment where status_active=1 and is_deleted=0 group by invoice_id","invoice_id","paid_amt");
				$btb_id_sql=sql_select("select c.import_invoice_id, c.btb_lc_id from com_import_invoice_dtls c where c.is_deleted=0 and c.status_active=1 and c.btb_lc_id in($btb_id)");
				$btb_inv_id_arr=array();
				foreach($btb_id_sql as $row)
				{
					$btb_inv_id_arr[$row[csf("btb_lc_id")]].=$row[csf("import_invoice_id")].",";
				}
				
				
			}
			else
			{
				
				if($db_type==0)
				{
					$payment_data_array=return_library_array("select c.btb_lc_id, sum(c.current_acceptance_value) as edf_loan_value 
				from com_import_invoice_mst b, com_import_invoice_dtls c 
				where c.btb_lc_id in($btb_id) and c.import_invoice_id=b.id and b.edf_paid_date!='0000-00-00' and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 group by c.btb_lc_id","btb_lc_id","edf_loan_value");
				}
				else
				{
					$payment_data_array=return_library_array("select c.btb_lc_id, sum(c.current_acceptance_value) as edf_loan_value 
				from com_import_invoice_mst b, com_import_invoice_dtls c 
				where c.btb_lc_id in($btb_id) and c.import_invoice_id=b.id and b.edf_paid_date is not null and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 group by c.btb_lc_id","btb_lc_id","edf_loan_value");
				}
			}
			
			
			
			
			
		}
		
		//echo $btb_sql;//die;
		
		
		//$payment_data_array=return_library_array("select invoice_id, sum(accepted_ammount) as paid_amt from com_import_payment where status_active=1 and is_deleted=0 group by invoice_id","invoice_id","paid_amt");
		
		
		
		
		$i=1;
        $sql_re=sql_select($btb_sql);
        $total_invoice_qty=0;  $total_order_qty=0;  $total_attach_qty=0;$result=0;
        
        foreach($sql_re as $row)
        {
			if($type==5)
			{
				$all_inv_id_arr=array_unique(explode(",",chop($btb_inv_id_arr[$row[csf("btb_lc_sc_id")]],",")));
				foreach($all_inv_id_arr as $invoice_id)
				{
					$lc_payment+=$payment_data_array[$invoice_id];
				}
				$lc_value=$row[csf('lc_value')]-$lc_payment;
				$lc_payment=0;
			}
			else
			{
				$lc_value=$row[csf('lc_value')]-$payment_data_array[$row[csf("btb_lc_sc_id")]];
			}
			
			
			if(number_format($lc_value,2)>0)
			{
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";  
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
					<td align="center"><? echo $i; ?></td>
					<td><p><?  echo $row[csf('btb_lc_number')]; ?>&nbsp;</p></td>
					<td align="center"><? if($row[csf('btb_lc_date')]!="" && $row[csf('btb_lc_date')]!="0000-00-00") echo change_date_format($row[csf('btb_lc_date')]); ?></td>
					<td align="right"><?  echo number_format($lc_value,2); $total_lc_value+=$lc_value; ?></td>
				</tr>
				<?
				$lc_value=0;
				$i++;
			}
			
        }
        ?>
        </tbody>
        <tfoot>
            <tr >
                <th align="right">&nbsp;</th>
                <th align="right" >&nbsp;</th>
                <th align="right">Total:</th>
                <th align="right"><? echo number_format($total_lc_value,2); ?></th>
            </tr>
        </tfoot>
    </table>
</fieldset>
</div>
<?
exit();
}


if($action=="btb_paid_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	if($inv_id=="") die;
	//echo $type;die;
	$day_of_month=cal_days_in_month(CAL_GREGORIAN, $month_val, $year_val);
	$btb_start_date=$year_val."-".$month_val."-01";
	$btb_end_date=$year_val."-".$month_val."-".$day_of_month;
	if($db_type==2)
	{
		$btb_start_date=change_date_format($btb_start_date,"","",1);
		$btb_end_date=change_date_format($btb_end_date,"","",1);
	}
	
	if($type==3)
	{
		?>
        <div style="width:760px" id="report_container">
        <fieldset style="width:760px">
            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="760">
                <thead>
                    <tr>
                        <th width="50">SL NO</th>
                        <th width="140">BTB LC NO</th>
                        <th width="80">BTB LC Date</th>
                        <th width="100">BTB LC Value</th>
                        <th width="100">Paid Value</th>
                        <th width="100">Balance Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$payment_data_array=return_library_array("select invoice_id, sum(accepted_ammount) as paid_amt from  com_import_payment where status_active=1 and is_deleted=0 group by invoice_id","invoice_id","paid_amt");
                $btb_sql="select c.id as btb_lc_sc_id, c.lc_category, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.lc_value, d.id as inv_id, d.edf_paid_date, e.current_acceptance_value as inv_value
                    from 
                            com_btb_lc_master_details c left join com_import_invoice_dtls e on c.id=e.btb_lc_id and e.is_deleted=0 and e.status_active=1 left join  com_import_invoice_mst d on e.import_invoice_id=d.id and d.is_deleted=0 and d.status_active=1  
                    where 
                             c.id in($inv_id) and c.is_deleted=0 and c.status_active=1 and c.lc_date between '$btb_start_date' and '$btb_end_date'";
                //echo $btb_sql;
                
                $i=1;
                $sql_re=sql_select($btb_sql);
				$details_data=array();
				foreach($sql_re as $row)
                {
					$details_data[$row[csf('btb_lc_sc_id')]]["btb_lc_sc_id"]=$row[csf('btb_lc_sc_id')];
					$details_data[$row[csf('btb_lc_sc_id')]]["btb_lc_number"]=$row[csf('btb_lc_number')];
					$details_data[$row[csf('btb_lc_sc_id')]]["btb_lc_date"]=$row[csf('btb_lc_date')];
					$details_data[$row[csf('btb_lc_sc_id')]]["lc_value"]=$row[csf('lc_value')];
					if(($row[csf('lc_category')]*1)==3 || ($row[csf('lc_category')]*1)==5 || ($row[csf('lc_category')]*1)==11)
					{
						if($row[csf('edf_paid_date')] !="" && $row[csf('edf_paid_date')]!="0000-00-00")
						{
							$details_data[$row[csf('btb_lc_sc_id')]]["paid_value"]+=$row[csf('inv_value')];
						}
					}
					else
					{
						if($inv_check_arr[$row[csf('inv_id')]]=="")
						{
							$inv_check_arr[$row[csf('inv_id')]]=$row[csf('inv_id')];
							$details_data[$row[csf('btb_lc_sc_id')]]["paid_value"]+=$payment_data_array[$row[csf('inv_id')]];
						}
					}
				}
				
                foreach($details_data as $row)
                {
                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					$balance_value=  $row[('lc_value')]-$row[("paid_value")];
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td><p><?  echo $row[('btb_lc_number')]; ?>&nbsp;</p></td>
                        <td align="center"><? if($row[('btb_lc_date')]!="" && $row[('btb_lc_date')]!="0000-00-00") echo change_date_format($row[('btb_lc_date')]); ?></td>
                        <td align="right"><?  echo number_format($row[('lc_value')],2); $total_lc_value+=$row[('lc_value')]; ?></td>
                        <td align="right"><?  echo number_format($row[("paid_value")],2); $total_paid_value+=$row[("paid_value")];  ?></td>
                        <td align="right"><?  echo number_format($balance_value,2); $total_balance_value+=$balance_value;  ?></td>
                        <td>&nbsp;</td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
                </tbody>
                <tfoot>
                    <tr >
                        <th align="right">&nbsp;</th>
                        <th align="right" >&nbsp;</th>
                        <th align="right">Total:</th>
                        <th align="right"><? echo number_format($total_lc_value,2); ?></th>
                        <th align="right"><? echo number_format($total_paid_value,2); ?></th>
                        <th align="right"><? echo number_format($total_balance_value,2); ?></th>
                        <th align="right">&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
        </div>
        <?
	}
	else
	{
		?>
        <script>
        function print_window()
        {
            //document.getElementById('scroll_body').style.overflow="auto";
            //document.getElementById('scroll_body').style.maxHeight="none";
            
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
        '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
        
            d.close();
            //document.getElementById('scroll_body').style.overflowY="scroll";
            //document.getElementById('scroll_body').style.maxHeight="230px";
        }	
        </script>
        <div style="width:760px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
        <div style="width:760px" id="report_container">
        <fieldset style="width:760px">
            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="760">
                <thead>
                    <tr>
                        <th width="40">SL NO</th>
                        <th width="120">BTB LC NO</th>
                        <th width="70">BTB LC Date</th>
                        <th width="100">BTB LC Value</th>
                        <th width="120">Invoice No</th>
                        <th width="100">Invoice Value</th>
                        <th width="100">Paid Value</th>
                        <th>Paid Date</th>
                    </tr>
                </thead>
                <tbody>
                <?
                
                if($inv_id!="")
                {
                    
                    
                    $btb_sql="select c.id as btb_lc_sc_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.lc_value, d.id as invoice_id, d.invoice_no, d.invoice_date, d.maturity_date, sum(e.current_acceptance_value) as inv_value, f.accepted_ammount, f.payment_date
                    from 
                            com_btb_lc_master_details c, com_import_invoice_mst d, com_import_invoice_dtls e, com_import_payment f
                    where 
                            c.id=e.btb_lc_id and e.import_invoice_id=d.id and d.id=f.invoice_id  and d.id in($inv_id) and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1  and e.is_deleted=0 and e.status_active=1 and c.lc_date between '$btb_start_date' and '$btb_end_date' 
                    group by c.id, c.lc_number, c.lc_date, c.lc_value, d.id, d.invoice_no, d.invoice_date, d.maturity_date, f.accepted_ammount, f.payment_date";
                }
                
                //echo $btb_sql;
                
                $i=1;
                $sql_re=sql_select($btb_sql);
                $total_invoice_qty=0;  $total_order_qty=0;  $total_attach_qty=0;$result=0;
                
                foreach($sql_re as $row)
                {
                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";  
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td><p><?  echo $row[csf('btb_lc_number')]; ?>&nbsp;</p></td>
                        <td align="center"><? if($row[csf('btb_lc_date')]!="" && $row[csf('btb_lc_date')]!="0000-00-00") echo change_date_format($row[csf('btb_lc_date')]); ?></td>
                        <td align="right"><?  echo number_format($row[csf('lc_value')],2); $total_lc_value+=$row[csf('lc_value')]; ?></td>
                        <td><p><?  echo $row[csf("invoice_no")]; ?>&nbsp;</p></td>
                        <td align="right"><?  echo number_format($row[csf("inv_value")],2); $total_invoice_value+=$row[csf("inv_value")];  ?></td>
                        <td align="right"><?  echo number_format($row[csf("accepted_ammount")],2); $total_paid_value+=$row[csf("accepted_ammount")];  ?></td>
                        <td align="center"><? if($row[csf('payment_date')]!="" && $row[csf('payment_date')]!="0000-00-00") echo change_date_format($row[csf('payment_date')]); ?></td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
                </tbody>
                <tfoot>
                    <tr >
                        <th align="right">&nbsp;</th>
                        <th align="right" >&nbsp;</th>
                        <th align="right">Total:</th>
                        <th align="right"><? echo number_format($total_lc_value,2); ?></th>
                        <th align="right">&nbsp;</th>
                        <th align="right"><? echo number_format($total_invoice_value,2); ?></th>
                        <th align="right"><? echo number_format($total_paid_value,2); ?></th>
                        <th align="right">&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
        </div>
        <?
	}
	
	//echo $maturity_start_date."**".$maturity_end_date;die;
	//print_r($po_id);die;
?> 

		
<?
exit();
}

if($action=="btb_open_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	if($btb_id=="") die;
	$day_of_month=cal_days_in_month(CAL_GREGORIAN, $month_val, $year_val);
	$btb_start_date=$year_val."-".$month_val."-01";
	$btb_end_date=$year_val."-".$month_val."-".$day_of_month;
	if($db_type==2)
	{
		$btb_start_date=change_date_format($btb_start_date,"","",1);
		$btb_end_date=change_date_format($btb_end_date,"","",1);
	}
	//echo $maturity_start_date."**".$maturity_end_date;die;
	//print_r($po_id);die;
?> 

<script>

	function print_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>
<div style="width:500px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
<div style="width:500px" id="report_container">
<fieldset style="width:500px">
    <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="480">
        <thead>
        	<tr>
                <th width="50">SL NO</th>
                <th width="150">BTB LC NO</th>
                <th width="100">BTB LC Date</th>
                <th>BTB LC Value</th>
            </tr>
        </thead>
        <tbody>
		<?
		
		if($btb_id!="")
		{
			
			$btb_sql="select c.id as btb_lc_sc_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.lc_value
			from 
					com_btb_lc_master_details c
			where 
					c.id in($btb_id) and c.is_deleted=0 and c.status_active=1  and c.lc_date between '$btb_start_date' and '$btb_end_date'";
		}
		
		//echo $btb_sql;
		
		$i=1;
        $sql_re=sql_select($btb_sql);
        $total_invoice_qty=0;  $total_order_qty=0;  $total_attach_qty=0;$result=0;
        
        foreach($sql_re as $row)
        {
			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";  
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
				<td align="center"><? echo $i; ?></td>
				<td><p><?  echo $row[csf('btb_lc_number')]; ?>&nbsp;</p></td>
				<td align="center"><? if($row[csf('btb_lc_date')]!="" && $row[csf('btb_lc_date')]!="0000-00-00") echo change_date_format($row[csf('btb_lc_date')]); ?></td>
				<td align="right"><?  echo number_format($row[csf('lc_value')],2); $total_lc_value+=$row[csf('lc_value')]; ?></td>
			</tr>
			<?
			$i++;
			
        }
        ?>
        </tbody>
        <tfoot>
            <tr >
                <th align="right">&nbsp;</th>
                <th align="right" >&nbsp;</th>
                <th align="right">Total:</th>
                <th align="right"><? echo number_format($total_lc_value,2); ?></th>
            </tr>
        </tfoot>
    </table>
</fieldset>
</div>
<?
exit();
}


disconnect($con);
?>
