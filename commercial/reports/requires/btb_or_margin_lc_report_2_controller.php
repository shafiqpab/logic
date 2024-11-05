<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == '' ) header('location:login.php');
require_once('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if($action==='report_generate')
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_id       = str_replace("'","",$cbo_company_id);
	$item_category_id = str_replace("'","",$cbo_item_category_id);
	$cbo_lc_type_id       = trim(str_replace("'","",$cbo_lc_type_id));
	$txt_btb_lc           = trim(str_replace("'","",$txt_btb_lc));
	$cbo_supplier_id      = str_replace("'","",$cbo_supplier_id);
	$txt_date_from        = str_replace("'","",$txt_date_from);
	$txt_date_to          = str_replace("'","",$txt_date_to);

	$company_cond=$supplier_cond=$item_category_cond=$issue_banking_cond=''; 
    $sql_cond="";
	if ($cbo_company_id != 0) $company_cond="  and a.importer_id in($cbo_company_id)";
    if ($cbo_lc_type_id==0) $lc_tpe_id =""; else $lc_tpe_id =" and a.lc_type_id=$cbo_lc_type_id ";
	if ($cbo_supplier_id != 0) $supplier_cond=" and a.supplier_id=$cbo_supplier_id";
	if ($item_category_id != 0) $item_category_cond=" and d.item_category_id=$item_category_id";
	// if ($item_category_id>0) $item_category_cond =" and a.pi_entry_form=".$category_wise_entry_form[$item_category_id];
	if ($txt_btb_lc != "") $bc_lc_no =" and a.lc_number like '%$txt_btb_lc%'";

    if($txt_date_from=='' && $txt_date_to=='') $lc_date=""; else $lc_date= " and a.lc_date between '".$txt_date_from."' and '".$txt_date_to."'";

	$supplier_arr= return_library_array("select id,supplier_name from lib_supplier where is_deleted=0", 'id','supplier_name');
	$payment_data_arr=return_library_array("select invoice_id, sum(accepted_ammount) as paid_amount from  com_import_payment where status_active=1 and is_deleted=0 group by invoice_id","invoice_id","paid_amount");
	$companyArr = return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0","id","company_short_name"); 
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$issueBankrArr = return_library_array("select id,bank_name from lib_bank ","id","bank_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name");

     $sql="SELECT a.id, a.importer_id, a.lc_number, a.lc_date, a.lc_category, a.supplier_id, a.currency_id, a.lc_value, a.pi_id, a.issuing_bank_id, d.item_category_id, a.payterm_id, a.ud_no, a.ud_date, a.upas_rate, a.tenor, d.import_pi,
	sum(CASE WHEN c.uom=27 and c.item_category_id in(1,2,5,6,23)  THEN c.QUANTITY ELSE 0 END) as Yds_qty,
	sum(case WHEN c.uom=23 and c.item_category_id in(1,2,5,6,23)  THEN c.QUANTITY ELSE 0 END) as Mtr_qty,
	sum(case WHEN c.uom=12 and c.item_category_id in(1,2,5,6,23) THEN c.QUANTITY ELSE 0 END) as kg_qty, 
	EXTRACT(MONTH FROM TO_DATE(a.INSERT_DATE, 'DD/MM/YYYY HH:MI:SS AM')) AS month,
	LISTAGG(DISTINCT d.buyer_id, ',') WITHIN GROUP (ORDER BY d.buyer_id) AS buyer_ids
    from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c, com_pi_master_details d
    where a.id=b.com_btb_lc_master_details_id and b.pi_id=d.id and d.id=c.pi_id and  a.status_active=1 and a.is_deleted=0  $company_cond $item_category_cond $supplier_cond $lc_tpe_id  $lc_date $bc_lc_no group by a.id, a.importer_id, a.lc_number, a.lc_date, a.lc_category, a.supplier_id, a.currency_id, a.lc_value, a.pi_id, a.issuing_bank_id, d.item_category_id, a.payterm_id, a.ud_no, a.ud_date, a.upas_rate, a.tenor, a.INSERT_DATE, d.import_pi";
	
	$sql_res=sql_select($sql);
	$btb_lc_id_arr=array();
	foreach($sql_res as $row){
		$btb_lc_id_arr[$row[csf("id")]]=$row[csf("id")];
	}

	$con = connect();
	$rid=execute_query("delete from GBL_TEMP_ENGINE where entry_form=143 and user_id=$user_id");
	if($rid) oci_commit($con);

	if(!empty($btb_lc_id_arr)){
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 143, 1, $btb_lc_id_arr,$empty_arr); 
		$sql_amidment=sql_select("SELECT a.id,
		CASE WHEN a.value_change_by = 1 THEN a.amendment_value ELSE NULL END as increase_qty,
		CASE WHEN a.value_change_by = 2 THEN a.amendment_value ELSE NULL END as decrise_qty, a.btb_id, a.amendment_no as amendment_no FROM com_btb_lc_amendment a INNER JOIN gbl_temp_engine b ON a.btb_id = b.REF_VAL WHERE b.entry_form = 143 AND b.ref_from = 1 AND b.user_id = $user_id AND a.amendment_no <> 0 GROUP BY a.btb_id, a.value_change_by, a.amendment_value, a.id, a.amendment_no order by  a.id desc");
		// AND a.id = (SELECT MAX (a.id) FROM com_btb_lc_amendment a,gbl_temp_engine b where a.btb_id = b.REF_VAL) 

		$amidment_arr=$amidment_arr_new=array();
		foreach($sql_amidment as $row){
			$amidment_arr[$row[csf("btb_id")]][$row[csf("id")]]["increase_qty"].=$row[csf("increase_qty")];
			$amidment_arr[$row[csf("btb_id")]][$row[csf("id")]]["decrise_qty"].=$row[csf("decrise_qty")];
			$amidment_arr[$row[csf("btb_id")]]["amendment_no"].=$row[csf("amendment_no")].",";	
			$amidment_arr_new[$row[csf("btb_id")]]["id"].=$row[csf("id")].",";				
		}
	}  
	// echo "<pre>";
	// print_r($amidment_arr_new);

	$width=1970;
	ob_start();
	?>
	<div width="<?= $width; ?>">
		<table width="<?= $width; ?>" cellspacing="0" cellpadding="0">
                <?
                    $company_library=sql_select("select id, company_name from lib_company where id in(".$cbo_company_id.")");
                    foreach( $company_library as $row)
                    {
                        $company_name.=$row[csf('company_name')].", ";
                    }
                ?>     
          	 <tr>
					<td colspan="21" align="center" style="font-size:22px"><center><strong><? echo rtrim($company_name,", ");?></strong></center></td>
			</tr>
			<tr>
				<td colspan="21" align="center" width="<?= $width; ?>"><p style="font-size:20px">BTB or Margin LC Repot</p>
				</td>
			</tr>				
		</table>
		<table class="rpt_table" border="1" rules="all" width="<?= $width; ?>" cellpadding="0" cellspacing="0" style="margin-left: 2px;">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="120">Company</th>
					<th width="120"> Item Category</th>
	                <th width="150">Supplier Name</th>
					<th width="70">LC No</th>
	                <th width="80">LC DATE</th>
	                <th width="100">FILE NO</th>
	                <th width="100">BANK</th>
	                <th width="120">BUYER</th>
	                <th width="110">KGS</th>
	                <th width="70">YARD</th>
	                <th width="80">MTR</th>
	                <th width="80">CURRENCY</th>
	                <th width="80">LC VALUE</th>
	                <th width="80">Amendment</th>
	                <th width="70">INCREASE</th>
	                <th width="70">  DECREASE</th>
	                <th width="120">TENOR</th>
	                <th width="150">MODE OF PAYMENT</th>
	                <th width="80">EDF/UPAS</th>
	                <th width="80">UD No</th>
				</tr>
			</thead>
		</table>
		<div style="width:<?= $width ?>px; overflow-y:scroll; max-height:350px" id="scroll_body">			
		    <table class="rpt_table" border="1" rules="all" width="<?= $width; ?>" cellpadding="0" cellspacing="0" id="table_body">
		        <tbody>
		        	<?
		        	$i=1;
		        	$pi_id_arr=array();
		        	$tot_pi_value=$tot_lc_value=0;$tot_acceptance_value=$tot_payment_value=0;$tot_payment_values=0;		        			        	
		        	foreach ($sql_res as $row)
		        	{                    			
						$amendment_count="";		
						$amendment_no = trim($amidment_arr[$row[csf("id")]]["amendment_no"], ',');
						if($amendment_no){
							$amendment_count = count(explode(",", $amendment_no));		
						}
													
						$amendment_id = trim($amidment_arr_new[$row[csf("id")]]["id"], ',');
						$unique_amendment_ids = array_unique(explode(',', $amendment_id));
						$max_amendment_id = max($unique_amendment_ids);

						$buyer_ids = explode(",", $row["BUYER_IDS"]);
						$buyer_names = '';
						foreach ($buyer_ids as $val) {
							if (isset($buyerArr[$val])) {
								$buyer_names .= $buyerArr[$val] . ",";
							}
						}
						if($row['IMPORT_PI']==1){
							$suppyler=$comp[$row['SUPPLIER_ID']];
						}else{
							$suppyler=$supplier_arr[$row['SUPPLIER_ID']];
						}		

                        if ($i%2==0) $bgcolor="#E9F3FF";
                        else $bgcolor="#FFFFFF";
                        $tolerance=($row['TOLERANCE']*$row['LC_VALUE'])/100;
                        $payment_values=$payment_values_arr[$row['INVOICE_ID']]
                        ?>
                        <tr bgcolor="<?= $bgcolor; ?>"  onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor;?>')" id="tr_<?= $i;?>" style="text-decoration:none; cursor:pointer">
                            <td width="30" align="center"><p><?= $i; ?></p></td>			        			        	
                            <td width="120" align="center"><p><?= $companyArr[$row['IMPORTER_ID']]; ?></p></td>
                            <td width="120" align="center"><p><?= $item_category[$row['ITEM_CATEGORY_ID']]; ?></p></td>
                            <td width="150" align="center"><p><?= $suppyler; ?></p></td>
                            <td width="70" style="word-break: break-all;" align="center"><p><?= $row["LC_NUMBER"] ?></p></td>
                            <td width="80" align="right"><p><?= change_date_format($row["LC_DATE"]); ?></p></td>
                            <td width="100" align="center"><p>&nbsp;<?//=  $row["INTERNAL_FILE_NO"];?></p></td>
                            <td width="100" align="center"><p>&nbsp;<?= $issueBankrArr[$row["ISSUING_BANK_ID"]]; ?></p></td>
                            <td width="120" style="word-break: break-all;" align="center"><?= rtrim($buyer_names, ",");?></td>
                            <td width="110" align="right"><p><?= number_format($row["KG_QTY"],2); ?></p></td>
                            <td width="70" align="right"><p><?= number_format($row["YDS_QTY"],2); ?></p></td>
                            <td width="80" align="right"><p><?= number_format($row["MTR_QTY"],2); ?></p></td>
                            <td width="80" align="center"><p>&nbsp;<?= $currency[$row["CURRENCY_ID"]];; ?></p></td>
                            <td width="80" align="right"><p><?= number_format($row["LC_VALUE"]); ?></p></td>
                            <td width="80" align="center"><p><?= $amendment_count; ?></p></td>
                            <td width="70" align="right"><p><?= number_format($amidment_arr[$row[csf("id")]][$max_amendment_id]["increase_qty"],2); ?></p></td>
                            <td width="70" align="right"><p><?= number_format($amidment_arr[$row[csf("id")]][$max_amendment_id]["decrise_qty"],2); ?></p></td>
                            <td width="120" align="center"><p><?= $row["TENOR"]; ?></p></td>
                            <td width="150" align="center"><p><?= $pay_term[$row['PAYTERM_ID']]; ?></p></td>
                            <td width="80" align="right" ><p>&nbsp;<?= $row["UPAS_RATE"] ?></p></td>
                            <td width="80" align="ceneter"><p><?= $row['UD_NO']; ?></p></td>
                        </tr>
                        <?
                        $i++;
                        $tot_kg_qty_value+=$row['KG_QTY'];
                        $tot_yds_qty_value+=$row['YDS_QTY'];
                        $tot_mtr_qty_value+=$row['MTR_QTY'];
                        $tot_lc_value_value+=$row['LC_VALUE'];
                        $tot_increase_qty_value+=$amidment_arr[$row[csf("id")]]["increase_qty"];
                        $tot_decrease_qty_value+=$amidment_arr[$row[csf("id")]]["decrise_qty"];  
						$lc_calu_array[$row['MONTH']][$row['ITEM_CATEGORY_ID']]['LC_VALUE']+=$row['LC_VALUE'] ;               
                    }
			         ?>
			        <tr class="tbl_bottom">
		                <td colspan="9" align="right"><b>Total:</b></td>
		                <td width="110" align="right"><?= number_format($tot_kg_qty_value,2); ?></td>
		                <td width="70" align="right"><?= number_format($tot_yds_qty_value,2); ?></td>
		                <td width="80" align="right"><?= number_format($tot_mtr_qty_value,2); ?></td>
		                <td width="80">&nbsp;</td>
		                <td width="80" align="right"><?= number_format($tot_lc_value_value); ?></td>
		                <td width="80">&nbsp;</td>
		                <td width="70" align="right"><?= number_format($tot_increase_qty_value,2); ?></td>
		                <td width="70" align="right"><?= number_format($tot_decrease_qty_value,2); ?></td>
		                <td width="120">&nbsp;</td>
		                <td width="150">&nbsp;</td>
		                <td width="80">&nbsp;</td>
		                <td width="80">&nbsp;</td>		               
		            </tr>
		        </tbody>
		    </table>
		</div>

		<?
	     $sql="SELECT a.lc_value, d.item_category_id, EXTRACT(MONTH FROM TO_DATE(a.INSERT_DATE, 'DD/MM/YYYY HH:MI:SS AM')) AS month from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details d
		where a.id=b.com_btb_lc_master_details_id and b.pi_id=d.id and  a.status_active=1 and a.is_deleted=0  $company_cond $lc_date $item_category_cond $supplier_cond $lc_tpe_id  $bc_lc_no";
		$sumarry=sql_select($sql);	
		$summary_arr=array();
		foreach($sumarry as $row){
			$summary_arr[$row["MONTH"]][$row["ITEM_CATEGORY_ID"]]["ITEM_CATEGORY_ID"]=$row["ITEM_CATEGORY_ID"];
			$summary_arr[$row["MONTH"]][$row["ITEM_CATEGORY_ID"]]["MONTH"]=$row["MONTH"];
			$summary_arr[$row["MONTH"]][$row["ITEM_CATEGORY_ID"]]["LC_VALUE"]+=$row["LC_VALUE"];
		}
		// echo "<pre>";
		// print_r($summary_arr);
		?>
       <br><br>
		<div  style="width:400px; overflow-y:scroll; max-height:350px">
			<table class="rpt_table" border="1" rules="all" width="400" cellpadding="0">
				<thead>
					<tr>
						<th colspan="4">Summary with BTB LC values.</th>
					</tr>
					<tr>
						<th width="40">SL</th>
						<th width="100">MONTH</th>
						<th width="130">Item Category</th>
						<th width="100">VALUE</th>
					</tr>
				</thead>			
				<tbody>
					<? $i=1;
					foreach($summary_arr as $month_arr){
						foreach($month_arr as $row){
							//$lc_calu_array[$row['ITEM_CATEGORY_ID']]['LC_VALUE'] 
							
							?>					
							<tr>			
								<td><?=$i;?></td>
								<td align="ceneter"><?=$months[$row["MONTH"]]?></td>
								<td align="ceneter"><?=$item_category[$row["ITEM_CATEGORY_ID"]]?></td>
								<td align="right"><?= number_format($lc_calu_array[$row['MONTH']][$row['ITEM_CATEGORY_ID']]['LC_VALUE'])?></td>
							</tr>
							<?
							$i++;
							$lc_total+=$lc_calu_array[$row['MONTH']][$row['ITEM_CATEGORY_ID']]['LC_VALUE'];
						}
					}?>				
				</tbody>
				<tfoot>
					<tr>
						<td colspan="3" align="right"><b>Total Value =</b></td>
						<td align="right"><?=number_format($lc_total)?></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	<?
	$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form=143");
	oci_commit($con);
	disconnect($con);
	foreach (glob("$user_id*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename";
	exit();
}

if($action==='supplier_list_popup')
{
	echo load_html_head_contents("Supplier List", "../../../", 1, 1, '','','');
	extract($_REQUEST);
	?>
	<script>

		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});

		function js_set_value( str )
		{
			var id = $('#txt_individual_id' + str).val()
			var name= $('#txt_individual_name' + str).val()
			$('#hidden_supplier_id').val(id);
			$('#hidden_supplier_name').val(name);
			parent.emailwindow.hide();
		}
    </script>

	</head>
	<?
	

	$result = sql_select("SELECT a.id as ID, a.supplier_name as SUPPLIER_NAME from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id  and c.tag_company in($company)  and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name");

	?>
	<body>
	<div align="center">
		<fieldset style="width:370px;margin-left:10px">
	    	<input type="hidden" name="hidden_supplier_id" id="hidden_supplier_id">
	    	<input type="hidden" name="hidden_supplier_name" id="hidden_supplier_name">
	        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
	            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
	                <thead>
	                    <th width="50">SL</th>
	                    <th>Supplier Name</th>
	                </thead>
	            </table>
	            <div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
	                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
	                <?
	                    $i=1;
	                    foreach($result as $row)
	                    {
	                        if ($i%2==0) $bgcolor='#E9F3FF'; 
	                        else $bgcolor='#FFFFFF';
	                        ?>
	                        <tr bgcolor="<?= $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?= $i;?>" onClick="js_set_value(<?= $i; ?>)">
                                <td width="50" align="center"><?php echo "$i"; ?>
                                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?= $i ?>" value="<?= $row['ID']; ?>"/>
                                    <input type="hidden" name="txt_individual_name" id="txt_individual_name<?= $i ?>" value="<?= $row['SUPPLIER_NAME']; ?>"/>
                                </td>
                                <td><p><?= $row['SUPPLIER_NAME']; ?></p></td>
	                        </tr>
	                        <?
	                        $i++;
	                    }
	                ?>
	                </table>
	            </div>
	        </form>
	    </fieldset>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

