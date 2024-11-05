<?

use PhpOffice\PhpSpreadsheet\Worksheet\Row;

header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="load_drop_down_supplier")
{
    echo create_drop_down( "cbo_supplier", 120, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company b where a.id=b.supplier_id and a.status_active=1 and a.is_deleted=0 and b.tag_company in ($data) group by a.id,a.supplier_name order by supplier_name","id,supplier_name", 1, "--Select Supplier--", $selected, "" );
    exit();
}

if($action=="load_report_format")
{
	//echo $sql="select format_id from lib_report_template where template_name in(".$data.") and module_id=11 and report_id in(44) and is_deleted=0 and status_active=1"; die;

	$print_report_format=return_field_value("format_id","lib_report_template","template_name in($data) and module_id=5 and report_id =297 and status_active=1 and is_deleted=0");
 	echo trim($print_report_format);
	exit();
}

if($action=="pi_details")
{
    echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    //print_r ($pi_id);
    $supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
    $companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
    $itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
	//if ($pi_id==0) $piId =""; else $piId =" and a.id in ($pi_id)";
	$yarncountArr = return_library_array("SELECT id,yarn_count FROM lib_yarn_count WHERE status_active = 1 AND is_deleted = 0","id","yarn_count"); 
            
	$sql="Select a.item_category_id,a.id, a.pi_number, b.item_prod_id, b.determination_id, b.item_group, b.item_description, b.size_id, b.quantity, b.rate, b.amount,b.count_name ,b.yarn_composition_item1 ,b.yarn_type,b.yarn_composition_percentage1, a.upcharge,a.discount, a.net_total_amount from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.id in ($pi_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.pi_number";
	//echo $sql;
	$result=sql_select($sql);
	foreach($result as $row)
	{
		$pi_wise_count[$row[csf("pi_number")]]++;
	}
	//print_r($pi_wise_count);die;
    ?>  
    <script>
    function print_window()
    {
        document.getElementById('scroll_body').style.overflow="auto";
        document.getElementById('scroll_body').style.maxHeight="none";
        
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
    
        d.close();
        document.getElementById('scroll_body').style.overflowY="auto";
        document.getElementById('scroll_body').style.maxHeight="none";
    }
    
    function window_close()
    {
        parent.emailwindow.hide();
    }
    <? ob_start();?>
    </script>   
    <div style="width:800px" align="center" id="scroll_body" >
    <fieldset style="width:100%; margin-left:10px" >
         <div id="report_container" align="center" style="width:100%" > 
             <div style="width:780px">
                <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
                    <thead>
                        <th colspan="4" align="center"><? echo $companyArr[$company_name]; ?></th>
                    </thead>
                        <tr>
                            <td width="150"><strong>LC Number : </strong></td> <td width="150"><strong><?  echo $lc_number; ?></strong></td>
                            <td><strong>Last Ship Date :</strong></td><td><strong>&nbsp;<?  echo change_date_format($ship_date); ?></strong></td>
                        </tr>
                        <tr>
                            <td width="150"><strong>Supplier : </strong></td> <td width="150"><strong><?  echo $supplierArr[$supplier_id]; ?></strong></td>
                            <td><strong>Expiry Date :</strong></td><td><strong>&nbsp;<?  echo change_date_format($exp_date); ?></strong></td>
                        </tr>
                        <tr>
                            <td width="150"><strong>LC Date : </strong></td> <td width="150"><strong>&nbsp;<?  echo change_date_format($lc_date); ?></strong></td>
                            <td><strong>Pay Term :</strong></td><td><strong><?  echo $pay_term[$payterm]; ?></strong></td>
                        </tr>
                </table>
                <br />
                <table class="rpt_table" border="1" rules="all" width="780" cellpadding="0" cellspacing="0">
                     <thead bgcolor="#dddddd">
                        <tr>
                            <th width="50">SL</th>
                            <th width="130">PI NO.</th>
                            <th width="120">Item Group</th>
                            <th width="150">Item Description</th>
                            <th width="100">Qnty</th>
                            <th width="100">Rate Total </th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $i=1;
                    $pi_arr=array();$k=1;$sub_total_qnt=0; $discount=0; $upcharge=0;
					$total_qty=0; $net_total=0; $pi_arr_check=array();
					$total_ammount=0;
					$sub_total_amount=0;
                    foreach( $result as $row)
                    {                        
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor ; ?>">
                            
                            <?
                            if($pi_arr_check[$row[csf('pi_number')]]=="")
                            {
								if($i!=1)
								{
									?>
									<tr>
										<td colspan="4" align="right" > <b> Subtotal : </b></td>
										<td align="right"><? echo number_format($sub_total_qnt,0); ?></td>
										<td>&nbsp;</td>
										<td align="right"><? echo number_format($sub_total_amount,2); ?></td>
									</tr>
								    <tr>
										<td colspan="6" align="right"><b>Upcharge :</b> </td>
										<td align="right"><? echo number_format($upcharge=$row[csf("upcharge")],2); ?></td>
									</tr>
									<tr>
										<td colspan="6" align="right"><b>Discount : </b></td>
										<td align="right"><? echo number_format($discount=$row[csf("discount")],2); ?></td>
									</tr>
									<tr>
										<td colspan="6" align="right"><b>Net Total : </b></td>
										<td align="right"><? echo number_format($row[csf("net_total_amount")],2); ?></td>
									</tr>
									<?
									$sub_total_qnt=0;
									$sub_total_amount=0;
										
									}
									$pi_arr_check[$row[csf('pi_number')]]=$row[csf('pi_number')];
									?>
									<td rowspan="<?= $pi_wise_count[$row[csf("pi_number")]];?>" valign="middle" align="center"><? echo $k; ?></td>
									<td rowspan="<?= $pi_wise_count[$row[csf("pi_number")]];?>" valign="middle"><? echo $row[csf("pi_number")]; ?></td>
									<?
									$k++;
									
								}
                            ?>
                            <!--<td><?// echo $row[csf("pi_number")]; ?></td>-->
                            <td><? echo $itemgroupArr[$row[csf("item_group")]]; ?></td>
                            <td>
                                <? 
                                if($row[csf("item_category_id")]==1)
                                {
                                    echo $yarncountArr[$row[csf("count_name")]].' '.$composition[$row[csf("yarn_composition_item1")]].' '.$row[csf("yarn_composition_percentage1")].'% '.$yarn_type[$row[csf("yarn_type")]]; 
                                }
                                else
                                { 
                                    echo $row[csf("item_description")]; 
                                }
                                ?>
                            </td>
                            <td align="right"><? echo $row[csf("quantity")]; ?></td>
                            <td align="right"><? echo $row[csf("rate")]; ?></td>
                            <td align="right"><? echo number_format($row[csf("amount")],2); ?></td>
                        </tr>
                        <?
						$sub_total_qnt+=$row[csf("quantity")];
						$sub_total_amount+=$row[csf("amount")];
						$total_qty+=$row[csf("quantity")];
						$total_ammount+=$row[csf("amount")];
						$upcharge=$row[csf("upcharge")];
						$discount=$row[csf("discount")];
						$net_total=$row[csf("net_total_amount")];

                        $i++;
						
                    } 
					
                    ?>
						<tr>
							<td colspan="4" align="right"><b> Subtotal : </b></th>
							<td align="right"><? echo number_format($sub_total_qnt,0); ?></td>
							<th>&nbsp;</td>
							<td align="right"><? echo number_format($sub_total_amount,2); ?></td>
						</tr>
						<tr>
							<td colspan="6" align="right"><b>Upcharge : </b></td>
							<td align="right"><? echo number_format($upcharge,2); ?></td>
						</tr>
						<tr>
							<td colspan="6" align="right"><b>Discount : </b></td>
							<td align="right"><? echo number_format($discount,2); ?></td>
						</tr>
						<tr>
							<td colspan="6" align="right"><b>Net Total : </b></td>
							<td align="right"><? echo number_format($net_total,2); ?></td>
						</tr>
                    </tbody>

                    <tfoot>
						<tr>
						<th colspan="4" align="right"><b>Total :</b> </th>
							<td align="right"><? echo number_format($total_qty,0); ?></td>
							<th>&nbsp;</td>
							<td align="right"><? echo number_format($total_ammount,2); ?></td>
						</tr>
						
                    </tfoot>
            </table>
            </div>
        </div>
    </fieldset>
    </div>
    <?
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w') or die('canot open');
	$is_created = fwrite($create_new_doc,ob_get_contents()) or die('canot write');
	?>
	<div style="width:800px" align="center" id="scroll_body" >
		<a href="<?=$filename;?>" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>
	    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="button" value="Close" onClick="window_close()" style="width:100px"  class="formbutton"/>

	</div>
	<?
    exit();
}
if($action=="wo_details")
{
    echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    //print_r ($pi_id);
    $supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
    $companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
    $itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
	//if ($pi_id==0) $piId =""; else $piId =" and a.id in ($pi_id)";
	$yarncountArr = return_library_array("SELECT id,yarn_count FROM lib_yarn_count WHERE status_active = 1 AND is_deleted = 0","id","yarn_count"); 
	$general_item_category = return_library_array("select category_id, short_name from  lib_item_category_list where status_active=1 and is_deleted=0 ", "category_id", "short_name");
            
	$sql="Select a.item_category_id,a.id, a.pi_number, b.item_prod_id, b.determination_id, b.item_group, b.item_description, b.size_id, b.quantity, b.rate, b.amount,b.count_name ,b.yarn_composition_item1 ,b.yarn_type,b.yarn_composition_percentage1, a.upcharge,a.discount, a.net_total_amount,b.WORK_ORDER_NO from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.id in ($pi_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.pi_number";
	//echo $sql;
	$result=sql_select($sql);
	foreach($result as $row)
	{
		$pi_wise_count[$row[csf("pi_number")]]++;
		$pi_wise_count[$row[csf("item_category_id")]]++;
	}
	//print_r($pi_wise_count);die;
    ?>  
    <script>
    function print_window()
    {
        document.getElementById('scroll_body').style.overflow="auto";
        document.getElementById('scroll_body').style.maxHeight="none";
        
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
    
        d.close();
        document.getElementById('scroll_body').style.overflowY="auto";
        document.getElementById('scroll_body').style.maxHeight="none";
    }
    
    function window_close()
    {
        parent.emailwindow.hide();
    }
    <? ob_start();?>
    </script>   
    <div style="width:800px" align="center" id="scroll_body" >
    <fieldset style="width:100%; margin-left:10px" >
         <div id="report_container" align="center" style="width:100%" > 
             <div style="width:780px">
                <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
                    <thead>
                        <th colspan="4" align="center"><? echo $companyArr[$company_name]; ?></th>
                    </thead>
                        <tr>
                            <td width="150"><strong>LC Number : </strong></td> <td width="150"><strong><?  echo $lc_number; ?></strong></td>
                            <td><strong>Last Ship Date :</strong></td><td><strong>&nbsp;<?  echo change_date_format($ship_date); ?></strong></td>
                        </tr>
                        <tr>
                            <td width="150"><strong>Supplier : </strong></td> <td width="150"><strong><?  echo $supplierArr[$supplier_id]; ?></strong></td>
                            <td><strong>Expiry Date :</strong></td><td><strong>&nbsp;<?  echo change_date_format($exp_date); ?></strong></td>
                        </tr>
                        <tr>
                            <td width="150"><strong>LC Date : </strong></td> <td width="150"><strong>&nbsp;<?  echo change_date_format($lc_date); ?></strong></td>
                            <td><strong>Pay Term :</strong></td><td><strong><?  echo $pay_term[$payterm]; ?></strong></td>
                        </tr>
                </table>
                <br />
                <table class="rpt_table" border="1" rules="all" width="780" cellpadding="0" cellspacing="0">
                     <thead bgcolor="#dddddd">
                        <tr>
                            <th width="50">SL</th>
                            <th width="130">PI NO.</th>
							<th width="120">Item Category</th>
                            <th width="150">WO No.</th>
                            
                   
                        </tr>
                    </thead>
                    <tbody>
					<?
                    $i=1;
                    $pi_arr=array();$k=1;$sub_total_qnt=0; $discount=0; $upcharge=0;
				 $pi_arr_check=array();
					$total_ammount=0;
					$sub_total_amount=0;
                    foreach( $result as $row)
                    {                        
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor ; ?>">
                            
                            <?
                            if($pi_arr_check[$row[csf('pi_number')]]=="")
                            {
								
									$pi_arr_check[$row[csf('pi_number')]]=$row[csf('pi_number')];
									?>
									<td rowspan="<?= $pi_wise_count[$row[csf("pi_number")]];?>" valign="middle" align="center"><? echo $k; ?></td>
									<td rowspan="<?= $pi_wise_count[$row[csf("pi_number")]];?>" valign="middle"><? echo $row[csf("pi_number")]; ?></td>
									<?
									$k++;
									
								}
                            ?>
                            <!--<td><?// echo $row[csf("pi_number")]; ?></td>-->

							<?
                            if($pi_arr_check[$row[csf('item_category_id')]]=="")
                            {
								
									$pi_arr_check[$row[csf('item_category_id')]]=$row[csf('item_category_id')];
									?>
									
									<td rowspan="<?= $pi_wise_count[$row[csf("item_category_id")]];?>" valign="middle"><? echo $general_item_category[$row[csf("item_category_id")]]; ?></td>
									<?
									$k++;
									
								}
                            ?>
                         
                            <td>
                              <? echo $row["WORK_ORDER_NO"]?>
                            </td>
                            
                        </tr>
                        <?
						

                        $i++;
						
                    } 
					
                    ?>
                    </tbody>

                    <tfoot>
						
						
						
                    </tfoot>
            </table>
            </div>
        </div>
    </fieldset>
    </div>
    <?
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w') or die('canot open');
	$is_created = fwrite($create_new_doc,ob_get_contents()) or die('canot write');
	?>
	<div style="width:800px" align="center" id="scroll_body" >
		<a href="<?=$filename;?>" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>
	    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="button" value="Close" onClick="window_close()" style="width:100px"  class="formbutton"/>

	</div>
	<?
    exit();
}

if ($action=="report_generate")
{
    //extract($_REQUEST);
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
	//echo $report_type;die;
    //echo $cbo_company_id;
	$exportPiSupp = sql_select("select c.import_pi, a.id from com_btb_lc_master_details a , com_btb_lc_pi b , com_pi_master_details c where a.id = b.com_btb_lc_master_details_id and b.pi_id = c.id");
	foreach ($exportPiSupp as $value)
	{
		$exportPiSuppArr[$value[csf("id")]] = $value[csf("import_pi")];
	}
	if($report_type==3) //Show
	{
		$item_category_id=str_replace("'","",$cbo_item_category_id);
		$item_category_arr = array(4,8,10,11,13,15,21);
		$companyArr = return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0","id","company_short_name"); 
		$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
		$issueBankrArr = return_library_array("select id,bank_name from lib_bank ","id","bank_name");
		$user_name = return_library_array("select id,user_name from user_passwd ","id","user_name");
		$hscodeArr = return_library_array("select id, hs_code from com_pi_master_details ","id","hs_code"); 
		//$lc_num_arr = return_library_array("select id,export_lc_no from com_export_lc ","id","export_lc_no"); 
		//$sc_num_arr = return_library_array("select id,contract_no from com_sales_contract ","id","contract_no"); 
		ob_start();
		if(!in_array($item_category_id,$item_category_arr))
		{
			//$div_width="3550";
			//$tbl_width="3550";
			
			$div_width="3870";
			$tbl_width="3870";
		}
		else
		{
			//$div_width="3240";
			//$tbl_width="3240";
			
			 
			//$div_width="3340";
			//$tbl_width="3340";
			
			$div_width="3550";
			$tbl_width="3550";
		}
		$cbo_company=str_replace("'","",$cbo_company_id);
		?>
		<div id="scroll_body" align="center" style="height:auto; width:<? echo $div_width; ?>px; margin:0 auto; padding:0;">
			<table width="2820px" >
				<?
				$company_library=sql_select("select id, company_name, plot_no, level_no,road_no,city from lib_company where id in(".$cbo_company.")");
				foreach( $company_library as $row)
				{
					$company_name.=$row[csf('company_name')].", ";
				}
				?>
				<tr>
					<td colspan="32" align="center" style="font-size:22px"><center><strong><? echo rtrim($company_name,", ");?></strong></center></td>
				</tr>
				<tr>
					<td colspan="32" align="center" style="font-size:18px"><center><strong><u><? echo $report_title; ?></u></strong></center></td>
				</tr>
			</table>
			<!-- ---------------------------- -->

		
				<?
				//var_dump($assocArray);die;
				
				$cbo_issue=str_replace("'","",$cbo_issue_banking);
				$lc_type_id=str_replace("'","",$cbo_lc_type_id);
				$txt_lc_no=str_replace("'","",$txt_lc_no);
				$cbo_payterm_id=str_replace("'","",$cbo_payterm_id);
				
				$cbo_bonded=str_replace("'","",$cbo_bonded_warehouse);
				$from_date=str_replace("'","",$txt_date_from);
				$to_date=str_replace("'","",$txt_date_to);
				$cbo_supplier=str_replace("'","",$cbo_supplier);
	
				$cbo_status=str_replace("'","",$cbo_status);
				$cbo_reference_close=str_replace("'","",$cbo_reference_close);
	
				$reference_close_cond='';
				if ($cbo_reference_close == 1)
					$reference_close_cond = " and a.ref_closing_status=1";
				else if ($cbo_reference_close == 2) 
					$reference_close_cond = " and a.ref_closing_status=0";
				else $reference_close_cond = " and a.ref_closing_status in(0,1)";
				$reference_close=array(1=>"Yes",2=>"No");
				//echo $cbo_status.'system'.$cbo_reference_close;die;REF_CLOSING_STATUS STATUS_ACTIVE
				//echo $from_date."**".$to_date."jahid";die;
				//$issue_banking $category_id $lc_tpe_id $bonded_warehouse $lc_date  $supply_source_cond $lc_no_cond
				$supply_so=str_pad(str_replace("'","",$cbo_supply_source), 2, "0", STR_PAD_LEFT);
				if (str_replace("'","",$cbo_supply_source)==0) $supply_source_cond =""; else $supply_source_cond ="and a.lc_category='".$supply_so."'";
				if ($cbo_company=='') $company_id =""; else $company_id =" and a.importer_id in($cbo_company) ";
				if ($cbo_issue==0) $issue_banking =""; else $issue_banking =" and a.issuing_bank_id=$cbo_issue ";
				$category_entry_form_cond ="";
				if ($item_category_id>0)
				{
					$category_entry_form_cond =" and a.pi_entry_form=".$category_wise_entry_form[$item_category_id];
				}
				//echo $category_entry_form_cond."==".$item_category_id;die;
				
				if ($item_category_id>0 && ($item_category_id !=2 || $item_category_id !=3 || $item_category_id!=13 || $item_category_id !=14 || $item_category_id!=12 || $item_category_id!=24 || $item_category_id!=25 || $item_category_id!=30 || $item_category_id!=31))
				{
					if($item_category_id==1)
					{
						$category_cond_wo =" and d.entry_form=144 ";
						//144
					}
					else if($item_category_id==5 || $item_category_id==6 || $item_category_id==7 || $item_category_id==23 )
					{
						$category_cond_wo =" and d.entry_form=145 ";
						//145
					}
					else if($item_category_id==4 || $item_category_id==11)
					{
						
						$category_cond_wo =" and d.entry_form=146 ";//146
					}
					else
					{
						$category_cond_wo =" and d.entry_form=147 ";
						//146
					}
					
				}
				//if ($item_category_id==0) $category_cond_wo =""; else $category_cond_wo =" and d.item_category=$item_category_id ";
				if ($lc_type_id==0) $lc_tpe_id =""; else $lc_tpe_id =" and a.lc_type_id=$lc_type_id ";
				if ($cbo_bonded==0) $bonded_warehouse =""; else $bonded_warehouse =" and a.bonded_warehouse=$cbo_bonded ";
				$payterm_id =""; 
				if ($cbo_payterm_id>0) $payterm_id =" and a.payterm_id=$cbo_payterm_id ";
				if ($cbo_supplier>0) $payterm_id .=" and a.supplier_id=$cbo_supplier ";
				
				
				//print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));
				$cbo_based_on=str_replace("'","",$cbo_based_on);
				if($db_type==2)
				{
					if($cbo_based_on==1)
					{
						if( $from_date=="" && $to_date=="" ) $lc_date=""; else $lc_date= " and a.lc_date between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
					}
					else
					{
						if( $from_date=="" && $to_date=="" ) $lc_date=""; else $lc_date= " and a.insert_date between '".date("j-M-Y",strtotime($from_date)) ." 12:00:01 AM"."' and '".date("j-M-Y",strtotime($to_date)). " 11:59:59 PM"."'";
					}
					$select_insert_date=" to_char(a.insert_date,'DD-MM-YYYY') as  insert_date";
					
				}
				else if($db_type==0)
				{
					if($cbo_based_on==1)
					{
						if( $from_date=='' && $to_date=='' ) $lc_date=""; else $lc_date= " and a.lc_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
					}
					else
					{
						if( $from_date=='' && $to_date=='' ) $lc_date=""; else $lc_date= " and a.insert_date between '".change_date_format($from_date,'yyyy-mm-dd'). " 00:00:01"."' and '".change_date_format($to_date,'yyyy-mm-dd'). " 23:59:59"."'";
					}
					
					$select_insert_date=" DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date";
				}
				
				$lc_no_cond="";
				if($txt_lc_no!="") $lc_no_cond=" and a.lc_number like '%$txt_lc_no'";
	
				$cbo_search_by=str_replace("'","",$cbo_search_by);
				$txt_search_common=str_replace("'","",$txt_search_common);
				if($cbo_search_by==1)
				{
					if($txt_search_common!="") $txt_lc_no_cond=" and c.export_lc_no = '$txt_search_common'";
				}
				else
				{
					if($txt_search_common!="") $txt_sc_no_cond=" and c.contract_no = '$txt_search_common'";
				}
				//if($txt_search_common!="") $txt_lc_no_cond=" and c.export_lc_no = '$txt_search_common'";
				//if($txt_search_common!="") $txt_sc_no_cond=" and c.contract_no = '$txt_search_common'";
	
				//echo $txt_lc_no_cond.$txt_sc_no_cond;die;
				
				$lc_pi_sql="Select c.item_category_id, a.id, c.id as pi_dtls_id, c.quantity, d.goods_rcv_status, d.id as pi_id, c.work_order_id, c.booking_without_order, c.item_group
				from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c, com_pi_master_details d 
				where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and c.pi_id=d.id and b.pi_id=d.id and a.status_active=1 and b.status_active=1 and c.status_active=1 $company_id  $issue_banking  $category_entry_form_cond $lc_tpe_id $bonded_warehouse $lc_date $lc_no_cond";
				$lc_item_category_sql=sql_select($lc_pi_sql);
				$pi_lc_data=array();
				$itemCategoryID="";
				foreach($lc_item_category_sql as $row)
				{
					if($row[csf("item_category_id")]==1 && $row[csf("item_group")]=="") $row[csf("item_group")]=0;
					$tot_lc_qty_arr[$row[csf("id")]]+=$row[csf("quantity")];
					if($row[csf("goods_rcv_status")]==1)
					{
						if($wo_id_check[$row[csf("id")]][$row[csf("work_order_id")]]=="")
						{
							$wo_id_check[$row[csf("id")]][$row[csf("work_order_id")]]=$row[csf("work_order_id")];
							$pi_lc_data[$row[csf("id")]]['pi_wo_id'].=$row[csf("work_order_id")]."_".$row[csf("goods_rcv_status")]."_".$row[csf("booking_without_order")]."_".$row[csf("item_category_id")]."_".$row[csf("item_group")].",";
						}
					}
					else
					{
						if($pi_id_check[$row[csf("id")]][$row[csf("pi_id")]]=="")
						{
							$pi_id_check[$row[csf("id")]][$row[csf("pi_id")]]=$row[csf("pi_id")];
							$pi_lc_data[$row[csf("id")]]['pi_wo_id'].=$row[csf("pi_id")]."_".$row[csf("goods_rcv_status")]."_".$row[csf("booking_without_order")]."_".$row[csf("item_category_id")]."_".$row[csf("item_group")].",";
						}
					}
					
					$pi_lc_data[$row[csf("id")]]['item_category_id'].=$row[csf("item_category_id")].",";
					if($item_group_check[$row[csf("id")]][$row[csf("item_group")]]=="")
					{
						$item_group_check[$row[csf("id")]][$row[csf("item_group")]]=$row[csf("item_group")];
						$pi_lc_data[$row[csf("id")]]['item_group'].=$row[csf("item_group")].",";
					}
					
					
					if($item_cat_check[$row[csf("item_category_id")]]=="")
					{
						$item_cat_check[$row[csf("item_category_id")]]=$row[csf("item_category_id")];
						$itemCategoryID.=$row[csf("item_category_id")].",";
					}
				}
				$itemCategoryID=chop($itemCategoryID,",");
				
				$rcv_sql="select a.mst_id, a.receive_basis, a.pi_wo_batch_no, a.cons_quantity as cons_quantity, a.cons_amount as cons_amount, a.order_amount as order_amount,a.booking_without_order,a.item_category, b.receive_purpose, b.EXCHANGE_RATE, c.item_group_id, c.entry_form, a.order_rate, a.prod_id, b.booking_id
				from inv_transaction a, inv_receive_master b, product_details_master c
				where a.mst_id=b.id and a.prod_id=c.id and a.transaction_type = 1 and b.receive_purpose<>2 and a.status_active = 1 and a.company_id in( $cbo_company) and a.item_category in ($itemCategoryID) $recBasis_cond";
				//echo $rcv_sql;die;  and b.receive_purpose not in(2,43)
				//echo $rcv_sql;
				$rcv_result=sql_select($rcv_sql);
				$tot_rec_qty_arr=array();$rcv_wise_pi_wo=$rcv_rate_arrr=array();
				foreach($rcv_result as $row)
				{
					if($row[csf("item_category")]==2 || $row[csf("item_category")]==3 || $row[csf("item_category")]==13 || $row[csf("item_category")]==14) $row[csf("pi_wo_batch_no")]=$row[csf("booking_id")];

					if($row[csf("item_category")]==4 && $row[csf("entry_form")]==20) $row[csf("item_category")]=11;

					if($row[csf("item_group_id")]=="" && $row[csf("item_category")]==1) $row[csf("item_group_id")]=0;

					$tot_rec_qty_arr[$row[csf("pi_wo_batch_no")]][$row[csf("receive_basis")]][$row[csf("booking_without_order")]][$row[csf("item_category")]][$row[csf("item_group_id")]]["cons_quantity"] +=$row[csf("cons_quantity")];

					// $tot_rec_qty_arr[$row[csf("pi_wo_batch_no")]][$row[csf("receive_basis")]][$row[csf("booking_without_order")]]["cons_amount"] +=$row[csf("cons_amount")];

					$tot_rec_qty_arr[$row[csf("pi_wo_batch_no")]][$row[csf("receive_basis")]][$row[csf("booking_without_order")]][$row[csf("item_category")]][$row[csf("item_group_id")]]["cons_amount"] +=$row[csf("cons_amount")]/$row["EXCHANGE_RATE"];

					$tot_rec_qty_arr[$row[csf("pi_wo_batch_no")]][$row[csf("receive_basis")]][$row[csf("booking_without_order")]][$row[csf("item_category")]][$row[csf("item_group_id")]]["order_amount"] +=$row[csf("order_amount")];

					$rcv_wise_pi_wo[$row[csf("mst_id")]]=$row[csf("pi_wo_batch_no")]."_".$row[csf("receive_basis")]."_".$row[csf("booking_without_order")];
					$currency_rcv_rate[$row[csf("mst_id")]]=$row["EXCHANGE_RATE"];

					$rcv_rate_arrr[$row[csf("pi_wo_batch_no")]][$row[csf("prod_id")]]["order_rate"]=$row[csf("order_rate")];
					
					$recv_wo_prod_id[$row[csf("pi_wo_batch_no")]][$row[csf("receive_basis")]].=$row[csf("prod_id")].",";
				}
				//echo "<pre>"; print_r($tot_rec_qty_arr[15994]);
				//  echo "</pre>";
				 $rcv_rtn_sql="select b.received_id, a.cons_quantity as cons_quantity, a.cons_amount as cons_amount, a.item_category, c.entry_form, a.pi_wo_batch_no, a.prod_id
				from inv_transaction a, inv_issue_master b, product_details_master c 
				where a.mst_id = b.id and a.prod_id=c.id and b.status_active = 1 and a.status_active = 1 and a.transaction_type = 3 and a.company_id in($cbo_company) and a.item_category in($itemCategoryID)";
				//echo $rcv_rtn_sql;
				$rcv_rtn_result=sql_select($rcv_rtn_sql);
				$tot_return_qty_arr=array();
				foreach($rcv_rtn_result as $row)
				{
					if($row[csf("item_category")]==4 && $row[csf("entry_form")]==20) $row[csf("item_category")]=11;
					$pi_ref=explode("_",$rcv_wise_pi_wo[$row[csf("received_id")]]);
					$tot_return_qty_arr[$pi_ref[0]][$pi_ref[1]][$pi_ref[2]][$row[csf("item_category")]]["cons_quantity"]+=$row[csf("cons_quantity")];
					// $tot_return_qty_arr[$pi_ref[0]][$pi_ref[1]]["cons_amount"]+=$row[csf("cons_amount")];
					$tot_return_qty_arr[$pi_ref[0]][$pi_ref[1]][$pi_ref[2]][$row[csf("item_category")]]["cons_amount"]+=$row[csf("cons_amount")]/$currency_rcv_rate[$row[csf("received_id")]];

					$rtn_qty_arr[$row[csf("pi_wo_batch_no")]][$row[csf("prod_id")]]["cons_quantity"]+=$row[csf("cons_quantity")];
					//$rtn_wo_wise_prod_id[$row[csf("pi_wo_batch_no")]].=$row[csf("prod_id")].",";
				}
				//print_r($recv_wo_prod_id);die;
	
				if($txt_search_common!="") 
				{
					if ($cbo_search_by==1) 
					{
						$sql = "SELECT a.id, b.is_lc_sc, b.lc_sc_id  
						from com_btb_lc_master_details a, com_btb_export_lc_attachment b, com_export_lc c
						where a.id=b.import_mst_id and b.lc_sc_id=c.id and b.is_lc_sc=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $issue_banking $category_entry_form_cond $lc_tpe_id $bonded_warehouse $lc_date  $supply_source_cond $lc_no_cond $txt_lc_no_cond order by a.lc_date ";
					}
					else
					{
						$sql = "SELECT a.id, b.is_lc_sc, b.lc_sc_id
						from com_btb_lc_master_details a, com_btb_export_lc_attachment b, com_sales_contract c
						where a.id=b.import_mst_id and b.lc_sc_id=c.id and b.is_lc_sc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $issue_banking $category_entry_form_cond $lc_tpe_id $bonded_warehouse $lc_date  $supply_source_cond $lc_no_cond $txt_sc_no_cond  order by a.lc_date ";
					}
					$lc_sc_sql=sql_select($sql);
					foreach($lc_sc_sql as $row)
					{
						$ls_sc_id.=$row[csf('id')].',';
					}
						
			
					$ls_sc_id_cond = implode(",",array_unique(explode(",",chop($ls_sc_id,','))));
					if($ls_sc_id_cond!="")
					{
						$btb_lc_sc_no_cond=" and a.id in($ls_sc_id_cond)";
					}
					else
					{
						echo "<br><strong><span style='color:red;'>Data Not Found</span></strong>";die;
					}
					/*echo "<pre>";
					print_r($ls_sc_id);die;*/
	
				}
				
				//echo $btb_lc_sc_no_cond;die;
				
				
				if($item_category_id==4) // Accessories
				{
					$sql="SELECT a.id, a.importer_id, a.lc_number, a.lc_date, a.lc_category, a.supplier_id, a.currency_id, a.lc_value, a.pi_id, a.inco_term_id, a.inco_term_place, a.issuing_bank_id, a.item_category_id, a.lc_type_id, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.hs_code, a.doc_presentation_days, a.delivery_mode_id, a.psi_company, a.insurance_company_name, a.cover_note_no, a.maturity_from_id, a.margin, a.bonded_warehouse, $select_insert_date, a.status_active, 1 as type, a.btb_system_id, a.insert_date as insert_date_time, a.inserted_by, a.payterm_id, a.ref_closing_status, a.ud_no, a.ud_date, c.work_order_id
					from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details d, com_pi_item_details c
					where a.id=b.com_btb_lc_master_details_id and b.pi_id=d.id and d.id=c.pi_id and a.is_deleted=0 and a.status_active=$cbo_status $reference_close_cond $company_id $issue_banking $category_entry_form_cond $lc_tpe_id $bonded_warehouse $lc_date $supply_source_cond $lc_no_cond $btb_lc_sc_no_cond and c.item_category_id=4
					group by a.id, a.importer_id, a.lc_number, a.lc_date, a.lc_category, a.supplier_id, a.currency_id, a.lc_value, a.pi_id, a.inco_term_id, a.inco_term_place, a.issuing_bank_id, a.item_category_id, a.lc_type_id, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.hs_code, a.doc_presentation_days, a.delivery_mode_id, a.psi_company, a.insurance_company_name, a.cover_note_no, a.maturity_from_id, a.margin, a.bonded_warehouse, a.insert_date, a.status_active, a.btb_system_id, a.inserted_by, a.payterm_id, a.ref_closing_status, a.ud_no, a.ud_date, c.work_order_id
					union all
					Select a.id, a.importer_id, a.lc_number, a.lc_date, a.lc_category, a.supplier_id, a.currency_id, a.lc_value, a.pi_id, a.inco_term_id, a.inco_term_place, a.issuing_bank_id, d.item_category as item_category_id, a.lc_type_id, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.hs_code, a.doc_presentation_days, a.delivery_mode_id, a.psi_company, a.insurance_company_name, a.cover_note_no, a.maturity_from_id, a.margin, a.bonded_warehouse, $select_insert_date, a.status_active, 2 as type, a.btb_system_id, a.insert_date as insert_date_time, a.inserted_by, a.payterm_id, a.ref_closing_status, a.ud_no, a.ud_date, c.work_order_id
					from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details e, com_pi_item_details c, wo_non_order_info_mst d
					where a.id=b.com_btb_lc_master_details_id and b.pi_id=e.id and e.id=c.pi_id and c.work_order_id=d.id and a.is_deleted=0 and a.status_active=$cbo_status $reference_close_cond $company_id  $issue_banking $category_cond_wo $lc_tpe_id $bonded_warehouse $lc_date $supply_source_cond $lc_no_cond $payterm_id $btb_lc_sc_no_cond and c.item_category_id=4
					group by a.id, a.importer_id, a.lc_number, a.lc_date, a.lc_category, a.supplier_id, a.currency_id, a.lc_value, a.pi_id, a.inco_term_id, a.inco_term_place, a.issuing_bank_id, d.item_category, a.lc_type_id, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.hs_code, a.doc_presentation_days, a.delivery_mode_id, a.psi_company, a.insurance_company_name, a.cover_note_no, a.maturity_from_id, a.margin, a.bonded_warehouse, a.insert_date, a.status_active, a.btb_system_id, a.insert_date, a.inserted_by, a.payterm_id, a.ref_closing_status, a.ud_no, a.ud_date, c.work_order_id order by lc_date";
				}
				else if($item_category_id==11) //Stationeries
				{
					if($db_type==0)
					{
						$sql="SELECT a.id, a.importer_id, a.lc_number, a.lc_date, a.lc_category, a.supplier_id, a.currency_id, a.lc_value, a.pi_id, a.inco_term_id, a.inco_term_place, a.issuing_bank_id, a.item_category_id, a.lc_type_id, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.hs_code, a.doc_presentation_days, a.delivery_mode_id, a.psi_company, a.insurance_company_name, a.cover_note_no, a.maturity_from_id, a.margin, a.bonded_warehouse, a.status_active, $select_insert_date, a.btb_system_id, a.insert_date as insert_date_time, a.inserted_by, a.payterm_id, a.ref_closing_status, a.ud_no, a.ud_date, d.work_order_id
						from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c, com_pi_item_details d
						where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.id and c.id=d.pi_id and a.is_deleted=0 and a.status_active=$cbo_status $reference_close_cond $company_id $issue_banking $category_entry_form_cond $lc_tpe_id $bonded_warehouse $lc_date  $supply_source_cond $btb_lc_sc_no_cond and a.id not in(Select a.id from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c, wo_non_order_info_mst d  where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and c.work_order_id=d.id and a.status_active=1 and a.is_deleted=0  $category_entry_form_cond $payterm_id  $lc_no_cond and d.item_category=4 group by a.id)
						group by a.id, a.importer_id, a.lc_number, a.lc_date, a.lc_category, a.supplier_id, a.currency_id, a.lc_value, a.pi_id, a.inco_term_id, a.inco_term_place, a.issuing_bank_id, a.item_category_id, a.lc_type_id, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.hs_code, a.doc_presentation_days, a.delivery_mode_id, a.psi_company, a.insurance_company_name, a.cover_note_no, a.maturity_from_id, a.margin, a.bonded_warehouse, a.status_active, a.insert_date, a.btb_system_id, a.inserted_by, a.payterm_id, a.ref_closing_status, a.ud_no, a.ud_date, d.work_order_id  order by a.lc_date ";
					}
					else
					{
						$sql="SELECT a.id, a.importer_id, a.lc_number, a.lc_date, a.lc_category, a.supplier_id, a.currency_id, a.lc_value, a.pi_id, a.inco_term_id, a.inco_term_place, a.issuing_bank_id, a.item_category_id, a.lc_type_id, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.hs_code, a.doc_presentation_days, a.delivery_mode_id, a.psi_company, a.insurance_company_name, a.cover_note_no, a.maturity_from_id, a.margin, a.bonded_warehouse, a.status_active, $select_insert_date, a.btb_system_id, a.insert_date as insert_date_time, a.inserted_by, a.payterm_id, a.ref_closing_status, a.ud_no, a.ud_date, d.work_order_id
						from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c, com_pi_item_details d
						where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.id and c.id=d.pi_id and a.is_deleted=0 and a.status_active=$cbo_status $reference_close_cond $company_id $issue_banking $category_entry_form_cond $lc_tpe_id $bonded_warehouse $lc_date  $supply_source_cond $btb_lc_sc_no_cond and a.id not in(Select a.id from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c, wo_non_order_info_mst d  where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and c.work_order_id=d.id and a.status_active=1 and a.is_deleted=0  $category_entry_form_cond $payterm_id  $lc_no_cond and  d.item_category=4 group by a.id)
						group by a.id, a.importer_id, a.lc_number, a.lc_date, a.lc_category, a.supplier_id, a.currency_id, a.lc_value, a.pi_id, a.inco_term_id, a.inco_term_place, a.issuing_bank_id, a.item_category_id, a.lc_type_id, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.hs_code, a.doc_presentation_days, a.delivery_mode_id, a.psi_company, a.insurance_company_name, a.cover_note_no, a.maturity_from_id, a.margin, a.bonded_warehouse, a.status_active, a.insert_date, a.btb_system_id, a.inserted_by, a.payterm_id, a.ref_closing_status, a.ud_no, a.ud_date, d.work_order_id order by a.lc_date ";
					}                
				}
				else // All item_category
				{
					if($db_type==0)
					{
						$sql="SELECT a.id, a.importer_id, a.lc_number, a.lc_date, a.lc_category, a.supplier_id, a.currency_id, a.lc_value, a.pi_id, a.inco_term_id, a.inco_term_place, a.issuing_bank_id, a.item_category_id, a.lc_type_id, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.hs_code, a.doc_presentation_days, a.delivery_mode_id, a.psi_company, a.insurance_company_name, a.cover_note_no, a.maturity_from_id, a.margin, a.bonded_warehouse, a.status_active, $select_insert_date, a.btb_system_id, a.insert_date as insert_date_time, a.inserted_by, a.payterm_id, a.ref_closing_status, a.ud_no, a.ud_date, d.work_order_id
						from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c, com_pi_item_details d
						where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.id and c.id=d.pi_id and a.is_deleted=0 and a.status_active=$cbo_status $reference_close_cond $company_id  $issue_banking $category_entry_form_cond $lc_tpe_id $payterm_id $bonded_warehouse $lc_date  $supply_source_cond $lc_no_cond $btb_lc_sc_no_cond
						group by a.id, a.importer_id, a.lc_number, a.lc_date, a.lc_category, a.supplier_id, a.currency_id, a.lc_value, a.pi_id, a.inco_term_id, a.inco_term_place, a.issuing_bank_id, a.item_category_id, a.lc_type_id, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.hs_code, a.doc_presentation_days, a.delivery_mode_id, a.psi_company, a.insurance_company_name, a.cover_note_no, a.maturity_from_id, a.margin, a.bonded_warehouse, a.status_active, a.insert_date, a.btb_system_id, a.inserted_by, a.payterm_id, a.ref_closing_status, a.ud_no, a.ud_date, d.work_order_id order by a.lc_date ";
					}
					else
					{                   
						$sql="SELECT a.id, a.importer_id, a.lc_number, a.lc_date, a.lc_category, a.supplier_id, a.currency_id, a.lc_value, a.pi_id, a.inco_term_id, a.inco_term_place, a.issuing_bank_id, a.item_category_id, a.lc_type_id, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.hs_code, a.doc_presentation_days, a.delivery_mode_id, a.psi_company, a.insurance_company_name, a.cover_note_no, a.maturity_from_id, a.margin, a.bonded_warehouse, a.status_active, $select_insert_date, a.btb_system_id, a.insert_date as insert_date_time, a.inserted_by, a.payterm_id, a.ref_closing_status, a.ud_no, a.ud_date, d.work_order_id,a.REMARKS
						from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c, com_pi_item_details d
						where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.id and c.id=d.pi_id and a.is_deleted=0 and a.status_active=$cbo_status $reference_close_cond $company_id  $issue_banking $category_entry_form_cond $lc_tpe_id $payterm_id $bonded_warehouse $lc_date  $supply_source_cond $lc_no_cond $btb_lc_sc_no_cond
						group by a.id, a.importer_id, a.lc_number, a.lc_date, a.lc_category, a.supplier_id, a.currency_id, a.lc_value, a.pi_id, a.inco_term_id, a.inco_term_place, a.issuing_bank_id, a.item_category_id, a.lc_type_id, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.hs_code, a.doc_presentation_days, a.delivery_mode_id, a.psi_company, a.insurance_company_name, a.cover_note_no, a.maturity_from_id, a.margin, a.bonded_warehouse, a.status_active, a.insert_date, a.btb_system_id, a.inserted_by, a.payterm_id, a.ref_closing_status, a.ud_no, a.ud_date, d.work_order_id,a.REMARKS order by a.lc_date";
					}
				}
				//echo $sql;die;
				$sql_data = sql_select($sql);
				
				$lc_sc_sql=sql_select("SELECT a.id, b.is_lc_sc, b.lc_sc_id as lc_sc_id, c.internal_file_no, c.export_lc_no as lc_sc_no 
				from com_btb_lc_master_details a, com_btb_export_lc_attachment b, com_export_lc c
				where a.id=b.import_mst_id and b.lc_sc_id=c.id and b.is_lc_sc=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $issue_banking $category_entry_form_cond $lc_tpe_id $bonded_warehouse $lc_date  $supply_source_cond $lc_no_cond
				union all
				Select a.id, b.is_lc_sc, b.lc_sc_id as lc_sc_id, c.internal_file_no, c.contract_no as lc_sc_no
				from com_btb_lc_master_details a, com_btb_export_lc_attachment b, com_sales_contract c
				where a.id=b.import_mst_id and b.lc_sc_id=c.id and b.is_lc_sc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $issue_banking $category_entry_form_cond $lc_tpe_id $bonded_warehouse $lc_date  $supply_source_cond $lc_no_cond");
				
				foreach($lc_sc_sql as $row)
				{
					if($row[csf("is_lc_sc")]==0)
						$lc_num_arr[$row[csf("lc_sc_id")]]=$row[csf("lc_sc_no")];
					else
						$sc_num_arr[$row[csf("lc_sc_id")]]=$row[csf("lc_sc_no")];
					
					$ls_sc_data[$row[csf("id")]]['is_lc_sc']=$row[csf("is_lc_sc")];
					$ls_sc_data[$row[csf("id")]]['lc_sc_id']=$row[csf("lc_sc_id")];
					$file_no_arr[$row[csf("id")]]=$row[csf("internal_file_no")];
				}
			
				if($db_type==0)
				{
					$lib_currency_data=sql_select("select conversion_rate from currency_conversion_rate where currency=2 order by id desc limit 1");
				}
				else
				{
					$lib_currency_data=sql_select("select conversion_rate from currency_conversion_rate where currency=2 and rownum<2 order by id desc");
				}
				$currency_conversion_rate=$lib_currency_data[0][csf("conversion_rate")];
				//echo $sql;die;
				
				$bal=0;
				$rec=0;
				$tlc_qty=0;
	
				$sql_accep = sql_select("SELECT a.company_acc_date as acceptance_date, b.btb_lc_id, b.current_acceptance_value, c.id as pay_id, b.import_invoice_id, c.accepted_ammount 
				from com_import_invoice_mst a,com_import_invoice_dtls b 
				left join com_import_payment c on b.import_invoice_id=c.invoice_id and c.status_active=1  
				where a.id=b.import_invoice_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.current_acceptance_value>0");
				$accep_data=array(); 
				$accep_data_file=array();
				$accep_data_date=array();
				$paid_ammount=array();
				foreach( $sql_accep as $row)
				{
					$accep_data[$row[csf("btb_lc_id")]]+=$row[csf("current_acceptance_value")];
					$accep_data_date[$row[csf("btb_lc_id")]].=change_date_format($row[csf("acceptance_date")]).',';
					$accep_data_file[$row[csf("btb_lc_id")]]['file']=$row[csf("import_invoice_id")];
				}
				unset($sql_accep);
				
				$pay_sql="select a.ID, c.ID as PAY_ID, c.ACCEPTED_AMMOUNT 
				from com_btb_lc_master_details a, com_import_invoice_dtls b, COM_IMPORT_PAYMENT_com c 
				where a.id=b.BTB_LC_ID and b.IMPORT_INVOICE_ID=c.INVOICE_ID and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.PAYTERM_ID=1
				union all 
				select a.ID, c.ID as PAY_ID, c.ACCEPTED_AMMOUNT 
				from com_btb_lc_master_details a, com_import_invoice_dtls b, COM_IMPORT_PAYMENT_com c 
				where a.id=b.BTB_LC_ID and b.IMPORT_INVOICE_ID=c.INVOICE_ID and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.PAYTERM_ID=2";
				$pay_sql_result = sql_select($pay_sql);
				foreach( $pay_sql_result as $row)
				{
					if($pay_check[$row["PAY_ID"]]=="")
					{
						$pay_check[$row["PAY_ID"]]=$row["PAY_ID"];
						$paid_ammount[$row["ID"]]+=$row["ACCEPTED_AMMOUNT"];
					}
				}
				unset($pay_sql_result);
	
				/*foreach( $sql_data as $row)
				{
					$pi_ID=$row[csf("pi_id")].',';
					$btb_system_ID="'".$row[csf("btb_system_id")]."'".',';
				}
				//echo $btb_ID.'system';
				$pi_IDs = implode(',',array_flip(array_flip(explode(',', rtrim($pi_ID,',')))));
				$btb_system_IDs = implode(',',array_flip(array_flip(explode(',', rtrim($btb_system_ID,',')))));*/
	
				$data_file=sql_select("select image_location, master_tble_id from common_photo_library where form_name='proforma_invoice' and is_deleted=0 and file_type=2 union all select image_location, master_tble_id from common_photo_library where form_name='BTBMargin LC' and is_deleted=0 and file_type=2 union all select image_location, master_tble_id from common_photo_library where form_name='importdocumentacceptance_1' and is_deleted=0 and file_type=2");
				$file_arr=array();
				foreach($data_file as $row)
				{
					$file_arr[$row[csf('master_tble_id')]]['file']=$row[csf('image_location')];
				}
				unset($data_file);
				// ----------------------------

				$all_data_arr=array();
				foreach($sql_data as $row)
				{
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["id"]=$row[csf("id")];
                    $all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["importer_id"]=$row[csf("importer_id")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["lc_number"]=$row[csf("lc_number")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["lc_date"]=$row[csf("lc_date")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["lc_category"]=$row[csf("lc_category")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["supplier_id"]=$row[csf("supplier_id")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["currency_id"]=$row[csf("currency_id")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["lc_value"]=$row[csf("lc_value")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["pi_id"]=$row[csf("pi_id")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["inco_term_id"]=$row[csf("inco_term_id")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["inco_term_place"]=$row[csf("inco_term_place")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["issuing_bank_id"]=$row[csf("issuing_bank_id")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["item_category_id"]=$row[csf("item_category_id")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["lc_type_id"]=$row[csf("lc_type_id")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["tenor"]=$row[csf("tenor")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["lc_expiry_date"]=$row[csf("lc_expiry_date")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["hs_code"]=$row[csf("hs_code")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["doc_presentation_days"]=$row[csf("doc_presentation_days")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["delivery_mode_id"]=$row[csf("delivery_mode_id")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["psi_company"]=$row[csf("psi_company")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["insurance_company_name"]=$row[csf("insurance_company_name")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["cover_note_no"]=$row[csf("cover_note_no")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["maturity_from_id"]=$row[csf("maturity_from_id")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["margin"]=$row[csf("margin")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["bonded_warehouse"]=$row[csf("bonded_warehouse")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["status_active"]=$row[csf("status_active")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["btb_system_id"]=$row[csf("btb_system_id")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["insert_date_time"]=$row[csf("insert_date_time")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["inserted_by"]=$row[csf("inserted_by")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["payterm_id"]=$row[csf("payterm_id")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["ref_closing_status"]=$row[csf("ref_closing_status")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["ud_date"]=$row[csf("ud_date")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["ud_no"]=$row[csf("ud_no")];
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["work_order_id"].=$row[csf("work_order_id")].",";
					$all_data_arr[$row[csf("currency_id")]][$row[csf("id")]]["REMARKS"]=$row[csf("REMARKS")];
				}
				$k=1;
				foreach($all_data_arr as $currency_key=>$currency_val)
				{
					
					$i=1;
					$currencyId.=$currency_key.',';
					?>
					<h1 style="float:left;  padding: 5px; font-size:20px;"> <? echo "For ".$currency[$currency_key]; ?> </h1>
                    <table cellspacing="0" width="<? echo $div_width;?>"  border="1" rules="all" class="rpt_table">
                    	<thead>
                        	<th width="30">SL</th>
                            <th width="70">Company</th>
                            <th width="120" align="center">BTB LC Number</th>
                            <th width="80" align="center">UD No</th>
                            <th width="80" align="center">UD Date</th>
                            <th width="80" align="center">Attached File</th>
                            <th width="70" align="center">Internal File No</th>
                            <th width="80" align="center">Supply Source</th>
                            <th width="110" align="center">Export LC/SC No</th>
                            <th width="70" align="center">LC Date</th>
                            <th width="110" align="center">Supplier</th>
                            <th width="50" align="center">Curr.</th>
                            <th width="90" align="center">LC Value</th>
                            <?
                            if(!in_array($item_category_id,$item_category_arr))
                            {
                                ?>
                                <th width="80" align="center">Rec. Amt</th>
                                <th width="80" align="center">Balance</th>
                                <?
                            }
                            ?>
                            <th width="80" align="center">Total LC Qty</th>
                            <?
                            if(!in_array($item_category_id,$item_category_arr))
                            {
                                ?>
                                <th width="80" align="center">Total Rec. Qty</th>
                                <th width="80" align="center">Balance</th>
                                <?
                            }
                            ?>
                            <th width="50" align="center">PI Dtls</th>
							<th width="50" align="center">WO No</th>
                            <th width="70" align="center">Acceptance</th>
                            <th width="70" align="center">Acceptance Date</th>
                            <th width="70" align="center">Paid Ammount</th>
                            <th width="50" align="center">Incoterm</th>
                            <th width="80" align="center">Inco T. Place</th>
                            <th width="120" align="center">Issuing Bank</th>
                            <th width="80" align="center">Item Category</th>
                            <th width="70" align="center">LC Type</th>
                            <th width="40" align="center">Tenor</th>
                            <th width="70" align="center">Ship Date</th>
                            <th width="70" align="center">LC Expiry Date</th>
                            <th width="60" align="center">HS Code</th>
                            <th width="40" align="center">Pres. Days</th>
                            <th width="60" align="center">Delivery Mode</th>
                            <th width="110" align="center">PSI Company</th>
                            <th width="110" align="center">Insurance Company</th>
                            <th width="80" align="center">Cover Note No</th>
                            <th width="100" align="center">Maturity From</th>
                            <th width="50" align="center">Margin %</th>
                            <th width="80" align="center">Reference Close</th>
                            <th width="80" align="center">Status</th>
                            <th width="110" align="center">System ID</th>
                            <th width="120" align="center">Insert Date and Time</th>
                            <th width="100" align="center">Insert User Name</th>
                            <th width="100" align="center">Pay Term</th>
                            <th  width="100"align="center" >Bonded</th>
							<th align="center" >Remarks</th>
                    </thead>
                    </table>
                    <div style="width:<? echo $div_width;?>px; overflow-y: scroll; overflow-x:hidden; max-height:300px;" id="scroll_body_<?=$currency_key;?>"> 
                    <table cellspacing="0" width="<? echo $tbl_width;?>"  border="1" rules="all" class="rpt_table" id="tbl_marginlc_list_<?=$currency_key;?>">
					<?php
					//print_r($rtn_qty_arr[45369]);echo "<br>";print_r($rtn_qty_arr[45500]);echo "<br>";print_r($rtn_qty_arr[45643]);echo "<br>";
					//echo $rtn_wo_wise_prod_id[45369]."=".$rtn_wo_wise_prod_id[45500]."=".$rtn_wo_wise_prod_id[45643]."<br>";
					//echo $rtn_qty_arr[45369]["cons_quantity"]."=".$rtn_qty_arr[45500]["cons_quantity"]."=".$rtn_qty_arr[45643]["cons_quantity"]."<br>";
					foreach( $currency_val as $row)
					{            
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
							
						$tot_lc_value+=$row["lc_value"];
						//$tol_lc_qnty+=$ttlc_qty;
						//$tol_rec_qnty+=$ttrec_qty;
						$tot_balance+=$balance;
						$ttl_acceptance_vlu += $accep_data[$row["id"]];
						$ttl_paid_amount += $paid_ammount[$row["id"]];
						
						$company_name=$row["importer_id"];
						$lc_number=$row["lc_number"];
						$ship_date=$row["last_shipment_date"];
						$supplier_id=$row["supplier_id"];
						$lc_date=$row["lc_date"];
						$exp_date=$row["lc_expiry_date"];
						$payterm=$row["payterm_id"];
						//$pi_id_arr=array_unique(explode(",",$row[csf("pi_id")]));
						//goods_rcv_status work_order_id
						//print_r($pi_id_arr); 
						$tot_rec_qty=0; $rtn_qty=0;$order_amount=0;
						$tot_rec_amt=0;
						$tot_lc_qty =$tot_lc_qty_arr[$row["id"]];
						$all_po_wo_ids=$all_wo_ids=$all_pi_ids="";
						$lc_pi_wo_ids=array_unique(explode(",",chop($pi_lc_data[$row["id"]]['pi_wo_id'],",")));
						$lc_pi_wo_cagtegory=array_unique(explode(",",chop($pi_lc_data[$row["id"]]['item_category_id'],",")));
						$lc_pi_wo_group=array_unique(explode(",",chop($pi_lc_data[$row["id"]]['item_group'],",")));
						$all_rec_wo_id_arr=array();$all_rec_pi_id_arr=array();
						foreach($lc_pi_wo_ids as $pi_wo_ids)
						{
							$pi_wo_ids_ref=explode("_",$pi_wo_ids);
							//echo $pi_wo_ids_ref[1]."<br>";
							if($pi_wo_ids_ref[1]==1)
							{  //echo $pi_wo_ids_ref[0].'=';
								$reaceive_basis=2;
								foreach($lc_pi_wo_cagtegory as $pi_cat)
								{
									foreach($lc_pi_wo_group as $group_id)
									{
										$ttrec_qty=$tot_rec_qty +=$tot_rec_qty_arr[$pi_wo_ids_ref[0]][2][$pi_wo_ids_ref[2]][$pi_cat][$group_id]["cons_quantity"] - $tot_return_qty_arr[$pi_wo_ids_ref[0]][2][$pi_wo_ids_ref[2]][$pi_cat]["cons_quantity"];
										$ttrec_amt=$tot_rec_amt +=$tot_rec_qty_arr[$pi_wo_ids_ref[0]][2][$pi_wo_ids_ref[2]][$pi_cat][$group_id]["order_amount"] -($tot_return_qty_arr[$pi_wo_ids_ref[0]][2][$pi_wo_ids_ref[2]][$pi_cat]["cons_amount"]);
									}
								}
								$all_wo_ids.=$pi_wo_ids_ref[0].'*'.$pi_wo_ids_ref[2].'*'.$pi_wo_ids_ref[3].",";
								$all_rec_wo_id_arr[$pi_wo_ids_ref[0]]=$pi_wo_ids_ref[0];
							}
							else
							{
								
								$reaceive_basis=1;
								foreach($lc_pi_wo_cagtegory as $pi_cat)
								{
									foreach($lc_pi_wo_group as $group_id)
									{
										//echo $pi_wo_ids_ref[0]."=1=".$pi_wo_ids_ref[2]."=".$pi_cat."=".$group_id.'<br>';
										if($group_id=="") $group_id=0;
										$ttrec_qty=$tot_rec_qty +=$tot_rec_qty_arr[$pi_wo_ids_ref[0]][1][$pi_wo_ids_ref[2]][$pi_cat][$group_id]["cons_quantity"] - $tot_return_qty_arr[$pi_wo_ids_ref[0]][1][$pi_wo_ids_ref[2]][$pi_cat]["cons_quantity"];
										$ttrec_amt=$tot_rec_amt +=$tot_rec_qty_arr[$pi_wo_ids_ref[0]][1][$pi_wo_ids_ref[2]][$pi_cat][$group_id]["order_amount"] -( $tot_return_qty_arr[$pi_wo_ids_ref[0]][1][$pi_wo_ids_ref[2]][$pi_cat]["cons_amount"]);
									}
								}
								$all_pi_ids.=$pi_wo_ids_ref[0].",";
								$all_rec_pi_id_arr[$pi_wo_ids_ref[0]]=$pi_wo_ids_ref[0];
							}
						}

						//echo $all_wo_ids."=".$all_pi_ids."<br>";
						if($row["item_category_id"]==4)
						{							 
							$rtn_qty=0;$return_amt=0;
							if(count($all_rec_wo_id_arr)>0)
							{
								foreach($all_rec_wo_id_arr as $wo_ids)
								{
									$prod_ids_arr=array_unique(explode(",",chop($recv_wo_prod_id[$wo_ids][2],",")));
									foreach($prod_ids_arr as $prod_val)
									{
										$rtn_qty+=$rtn_qty_arr[$wo_ids][$prod_val]["cons_quantity"];
										$return_amt+=$rtn_qty_arr[$wo_ids][$prod_val]["cons_quantity"]*$rcv_rate_arrr[$wo_ids][$prod_val]["order_rate"];
									}
								}
							}
							elseif(count($all_rec_pi_id_arr)>0)
							{
								foreach($all_rec_pi_id_arr as $wo_ids)
								{
									$prod_ids_arr=array_unique(explode(",",chop($recv_wo_prod_id[$wo_ids][1],",")));
									foreach($prod_ids_arr as $prod_val)
									{
										$rtn_qty+=$rtn_qty_arr[$wo_ids][$prod_val]["cons_quantity"];
										$return_amt+=$rtn_qty_arr[$wo_ids][$prod_val]["cons_quantity"]*$rcv_rate_arrr[$wo_ids][$prod_val]["order_rate"];
									}
								}
							}
							
							$tot_rec_qty=$tot_rec_qty-$rtn_qty;
							$tot_rec_amt=$tot_rec_amt-$return_amt;
						}
						
						$all_po_wo_ids=chop($all_pi_ids,",")."__".chop($all_wo_ids,",");
						//echo $rtn_qty."=".$return_amt."<br>";
						?>
						<tr bgcolor="<? echo $bgcolor ; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
							<td align="center" width="30"><? echo $i; ?></td>
							<td align="center" width="70" style="word-break:break-all;"><p><? echo $companyArr[$row["importer_id"]]; ?></p></td>
							<td width="120" style="word-break: break-all;"><p><a href="#report_details" onclick="lc_details_popup('<? echo $row['id'];?>','lc_details','LC Details','<? echo $file_no_arr[$row["id"]];?>');"><? echo $row["lc_number"]; ?></a>&nbsp;</p></td>
							<td width="80" align="center" style="word-break:break-all;"><p><? echo $row["ud_no"]; ?></p></td>
							<td width="80" align="center" style="word-break:break-all;"><p>&nbsp;<? echo change_date_format( $row["ud_date"]); ?></p></td>

							<td width="80" style="word-break: break-all;"><p>
								<? 
									  $file_name_pi=$file_arr[$row['pi_id']]['file'];
									  $file_name_btb=$file_arr[$row['btb_system_id']]['file'];
									  $file_name_accep=$accep_data_file[$row["id"]]['file'];
									  if( $file_name_pi != '' || $file_name_btb != '' || $file_name_accep != '')
									  {
										?>
									   <input type="button" class="image_uploader" id="fileno_<? echo $i;?>" style="width:60px" value="File" onClick="openmypage_file('<? echo $row['pi_id']; ?>','<? echo $row['btb_system_id']; ?>','<? echo  $accep_data_file[$row["id"]]['file']; ?>','<? echo $row['id']; ?>')"/>
										<?  
									  }
								?></p></td>
							<td width="70" align="center" style="word-break:break-all;"><p><? echo $file_no_arr[$row["id"]]; ?></p></td>
							<td width="80" style="word-break:break-all;"><p><? echo $supply_source[(int)$row["lc_category"]]; ?></p></td>
							<td width="110" style="word-break:break-all;"><p>
								<?
								$lc_sc_num="";
								$p=1; //lc_sc_no_cond
								$lc_sc_id_arr=array_unique(explode(",",$ls_sc_data[$row["id"]]['lc_sc_id']));
								foreach($lc_sc_id_arr as $lc_sc_id)
								{
									if($p!=1) $lc_sc_num .=", ";
									if($ls_sc_data[$row["id"]]['is_lc_sc']==0)
									{
										$lc_sc_num .=$lc_num_arr[$lc_sc_id];
									}
									else
									{
										$lc_sc_num .=$sc_num_arr[$lc_sc_id];
									}
									$p++;
								}
								echo $lc_sc_num;
								?>&nbsp;
								</p>
							</td>
							<td width="70" style="word-break: break-all;"><p>&nbsp;<? if($row["lc_date"]!="" && $row["lc_date"]!="0000-00-00") echo change_date_format($row["lc_date"]); ?></p></td>
							<td width="110" style="word-break: break-all;"><p><? if($exportPiSuppArr[$row["id"]]==1) echo $companyArr[$row["supplier_id"]]; else echo $supplierArr[$row["supplier_id"]]; ?></p></td>
							<td width="50" align="center" style="word-break:break-all;"><p><? echo $currency[$row["currency_id"]]; ?></p></td>
							<td width="90" align="right"><p><? echo number_format($row["lc_value"],2); ?></p></td>
							<?
							if(!in_array($item_category_id,$item_category_arr))
							{
							?>
								<td width="80" align="right" style="word-break:break-all;"><? echo number_format($tot_rec_amt,2); ?></td>
								<td width="80" align="right" style="word-break:break-all;"><p><? $balance_amt=$row["lc_value"]-$tot_rec_amt; echo number_format($balance_amt,2); ?></p></td>
							<?
							}
							?>
							<td width="80" align="right" style="word-break:break-all;"><? echo number_format($tot_lc_qty,2); ?></td>
							<?
							
							
							if(!in_array($item_category_id, $item_category_arr))
							{
								$item_cate_array="";
								$l=1;
								foreach($lc_pi_wo_cagtegory as $cat_id)
								{
									if($l!=1) $item_cate_array .=", ";
									$item_cate_array .=$cat_id;
									$l++;
								}   
								
								?>
								<td width="80" align="right" style="word-break:break-all;"><a href="##" onclick="openmypagRcvDetails('<? echo $all_po_wo_ids;?>', '<? echo $item_cate_array;?>', '<? echo $reaceive_basis;?>', '<? echo $company_name;?>', '<? echo chop($pi_lc_data[$row['id']]['item_group'],',');?>')"><? echo number_format($tot_rec_qty,2);?></a></td>
								
								<td width="80" align="right" style="word-break:break-all;"><? $balance=$tot_lc_qty-$tot_rec_qty; echo number_format($balance,2); ?></td>
								<?
							}
							?>
							
							<td width="50" align="center" style="word-break:break-all;"><p>
							<?
							$pi_id=$row["pi_id"]; 
							echo "<a href='#report_details' style='color:#000' onclick= \"openmypage('$company_name','$lc_number','$ship_date','$supplier_id','$lc_date','$exp_date','$payterm','$pi_id','pi_details','PI Details');\">"."View"."</a>"; //$row["pi_id"]; 
							?>
							</p></td>
							<td width="50" align="center" style="word-break:break-all;"><p>
							<?
							$pi_id=$row["pi_id"]; 
							echo "<a href='#report_details' style='color:#000' onclick= \"openmypage('$company_name','$lc_number','$ship_date','$supplier_id','$lc_date','$exp_date','$payterm','$pi_id','wo_details','WO Details');\">"."View"."</a>"; //$row["pi_id"]; 
							?>
							</p></td>
							<td width="70" align="right" style="word-break:break-all;">&nbsp;<? echo number_format($accep_data[$row["id"]],2); ?></td>
							<td width="70" align="right" style="word-break:break-all;">&nbsp;<? echo implode(", ",array_unique(explode(",",chop($accep_data_date[$row["id"]],',')))); ?></td>
							<td width="70" align="right" style="word-break:break-all;"><? echo number_format($paid_ammount[$row["id"]],2); ?></td>
							<td width="50" align="center" style="word-break:break-all;"><p><? echo $incoterm[$row["inco_term_id"]]; ?></p></td>
							<td width="80" align="center" style="word-break:break-all;"><p><? echo $row["inco_term_place"]; ?></p></td>
							<td width="120" style="word-break:break-all;"><p><? echo $issueBankrArr[$row["issuing_bank_id"]]; ?></p></td>
							<td width="80" style="word-break: break-all;"><p><? 
							$itemCategory="";
							$l=1;
							foreach($lc_pi_wo_cagtegory as $cat_id)
							{
								if($l!=1) $itemCategory .=", ";
								$itemCategory .=$item_category[$cat_id];
								$l++;
							}                       
							echo $itemCategory;
		
							//echo $item_category[$row["item_category_id"]]; ?></p></td>
							<td width="70" style="word-break: break-all;"><p><? echo $lc_type[$row["lc_type_id"]]; ?></p></td>
							<td  width="40" style="word-break: break-all;" align="center"><p><? echo $row["tenor"]; ?></p></td>
							<td width="70" style="word-break: break-all;"><p>&nbsp;<? echo change_date_format($row["last_shipment_date"]); ?></p></td>
							<td width="70" style="word-break: break-all;" align="center"><p>&nbsp;<? echo change_date_format($row["lc_expiry_date"]); ?></p></td>
							<td width="60" align="center" style="word-break:break-all;"><p><? echo $hscodeArr[$row["pi_id"]]; ?></p></td>
							<td width="40" align="center" style="word-break:break-all;"><p><? echo $row["doc_presentation_days"]; ?></p></td>
							<td width="60" align="center" style="word-break:break-all;"><p><? echo $shipment_mode[$row["delivery_mode_id"]]; ?></p></td>
							<td width="110" align="center" style="word-break:break-all;"><p><? echo $row["psi_company"]; ?></p></td>
							<td width="110" style="word-break:break-all;"><p><? echo $row["insurance_company_name"]; ?></p></td>
							<td width="80" style="word-break:break-all;"><p><? echo $row["cover_note_no"]; ?></p></td>
							<td width="100" align="center" style="word-break:break-all;"><p><? echo $maturity_from[$row["maturity_from_id"]]; ?></p></td>
							<td width="50" align="center" style="word-break:break-all;"><p><? echo $row["margin"]; ?></p></td>
		
							<td width="80" align="center" style="word-break:break-all;"><p><? if ($row["ref_closing_status"] != 1) echo 'No'; else echo $yes_no[$row["ref_closing_status"]]; ?></p></td>
		
							<td width="80" align="center" style="word-break:break-all;"><p><? echo $row_status[$row["status_active"]]; ?></p></td>
							<td width="110" align="center" style="word-break:break-all;"><p><? echo $row["btb_system_id"]; ?></p></td>
							<td width="120" align="center" style="word-break:break-all;"><p><? echo date('d-m-Y', strtotime($row["insert_date_time"])).'&nbsp;&nbsp;'.date('h:i:s a', strtotime($row["insert_date_time"]));  ?></p></td>
							<td width="100" align="center" style="word-break:break-all;"><p><? echo $user_name[$row["inserted_by"]]; ?></p></td>
							<td width="100" align="center" style="word-break:break-all;"><p><? echo $pay_term[$row["payterm_id"]]; ?></p></td>
							<td width="100"align="center" style="word-break:break-all;"><p><? echo $yes_no[$row["bonded_warehouse"]]; ?></p></td>
							<td align="center" style="word-break:break-all;"><p><? echo $row[csf("REMARKS")]; ?></p></td>
							
						</tr>
						<?
						
						$bal=$bal+$balance;
						$bal_amt=$bal_amt+$balance_amt;
						$rec=$rec+$ttrec_qty-$rtn_qty;
						$rec_amt=$rec_amt+$ttrec_amt-$order_amount;
						$tlc_qty=$tlc_qty+$tot_lc_qty;
						$tot_lc_qty=$tot_rec_qty=0; 
		
						$i++;
						$k++;
					}
               ?>
                    </table>
				<table cellspacing="0" width="<? echo $div_width;?>"  border="1" rules="all" class="rpt_table" >
					<tfoot>
						<tr>
								<th width="30" >&nbsp;</th>
								<th width="70">&nbsp;</th>
								<th width="120">&nbsp;</th>
								<th width="80">&nbsp;</th>
								<th width="80">&nbsp;</th>
								<th width="80">&nbsp;</th>
								<th width="70">&nbsp;</th>
								<th width="80">&nbsp;</th>
								<th width="110">&nbsp;</th>
								<th width="70">&nbsp;</th>
								<th width="110">&nbsp;</th>
								<th width="50"><? echo $currency[$currency_key]; ?> Total : </th>
 								<th align="right" width="90" id="value_tot_lc_value_<?=$currency_key;?>" style="word-break:break-all;"><? echo number_format($tot_lc_value,2); ?></th>
								<?
								if(!in_array($item_category_id,$item_category_arr))
								{
									?>
									<th width="80" align="center" id="value_tot_rec_amt_<?=$currency_key;?>" style="word-break:break-all;"><? echo number_format($rec_amt,2);?></th>
								<th width="80" align="center" id="value_tot_bal_amt_<?=$currency_key;?>"  style="word-break:break-all;"><? echo number_format($bal_amt,2);?></th>
									<?
								}
								?>
								<th width="80" align="center"  id="value_tot_tlc_qty_<?=$currency_key;?>" style="word-break:break-all;"><? echo number_format($tlc_qty,2);?></th>
								<?
								if(!in_array($item_category_id,$item_category_arr))
								{
									?>
									<th width="80" align="center"  id="value_tot_rec_<?=$currency_key;?>" style="word-break:break-all;"><? echo number_format($rec,2);?></th>
								    <th width="80" align="center"  id="value_tot_bal_<?=$currency_key;?>" style="word-break:break-all;"><? echo number_format($bal,2);?></th>
									<?
								}
								?>
								<th width="50" align="center">&nbsp;</th>
								<th width="50" align="center">&nbsp;</th>
								<th width="70" align="center"  id="value_tot_ttl_acceptance_vlu_<?=$currency_key;?>" style="word-break:break-all;"><? echo $ttl_acceptance_vlu;?></th>
								<th width="70">&nbsp;</th>    
								<th width="70" align="center"  id="value_tot_ttl_paid_amount_<?=$currency_key;?>" style="word-break:break-all;"><? echo $ttl_paid_amount;?></th>      
                                <th width="50" align="center">&nbsp;</th>
                                <th width="80" align="center">&nbsp;</th>
                                <th width="120">&nbsp;</th>
                                <th width="80">&nbsp;</th>
                                <th width="70">&nbsp;</th>
                                <th  width="40">&nbsp;</th>
                                <th width="70">&nbsp;</th>
                                <th width="70">&nbsp;</th>
                                <th width="60">&nbsp;</th>
                                <th width="40">&nbsp;</th>
                                <th width="60">&nbsp;</th>
                                <th width="110">&nbsp;</th>
                                <th width="110">&nbsp;</th>
                                <th width="80">&nbsp;</th>
                                <th width="100">&nbsp;</th>
                                <th width="50">&nbsp;</th>
                                <th width="80">&nbsp;</th>
                                <th width="80">&nbsp;</th>
                                <th width="110">&nbsp;</th>
                                <th width="120">&nbsp;</th>
                                <th width="100">&nbsp;</th>
                                <th width="100">&nbsp;</th>
								<th width="100">&nbsp;</th>
                                <th >&nbsp;</th>
						</tr>
					</tfoot>         
				</table>
			</div>
                <?php
				$tot_lc_value=0;
				$rec_amt=0;
				$bal_amt=0;
				$tlc_qty=0;
				$rec=0;
				$bal=0;
				$ttl_acceptance_vlu=0;
				$ttl_paid_amount=0;
				}
				$currencyId=rtrim($currencyId,',');
				
				
				?>
				
		</div>
		<?
	}

	if($report_type==4) //Details
	{
		$item_category_id=str_replace("'","",$cbo_item_category_id);
		//$item_category_arr = array(4,8,10,11,13,15,21);
		$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
		$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
		$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name");
		$issueBankrArr = return_library_array("select id,bank_name from lib_bank ","id","bank_name");
		$user_name = return_library_array("select id,user_name from user_passwd ","id","user_name");
		//$hscodeArr = return_library_array("select id, hs_code from com_pi_master_details ","id","hs_code"); 
		//$lc_num_arr = return_library_array("select id,export_lc_no from com_export_lc ","id","export_lc_no"); 
		//$sc_num_arr = return_library_array("select id,contract_no from com_sales_contract ","id","contract_no"); 
		
		//var_dump($assocArray);die;
		$cbo_company=str_replace("'","",$cbo_company_id);
		$cbo_issue=str_replace("'","",$cbo_issue_banking);
		$lc_type_id=str_replace("'","",$cbo_lc_type_id);
		$txt_lc_no=str_replace("'","",$txt_lc_no);
		$cbo_payterm_id=str_replace("'","",$cbo_payterm_id);
		
		$cbo_bonded=str_replace("'","",$cbo_bonded_warehouse);
		$from_date=str_replace("'","",$txt_date_from);
		$to_date=str_replace("'","",$txt_date_to);
		$cbo_supplier=str_replace("'","",$cbo_supplier);
		$txt_search_common=str_replace("'","",$txt_search_common);
		$cbo_status=str_replace("'","",$cbo_status);
		$cbo_reference_close=str_replace("'","",$cbo_reference_close);

		$reference_close_cond='';
		if ($cbo_reference_close == 1)
			$reference_close_cond = " and a.ref_closing_status=1";
		else if ($cbo_reference_close == 2) 
			$reference_close_cond = " and a.ref_closing_status=0";
		else $reference_close_cond = " and a.ref_closing_status in(0,1)";
		$reference_close=array(1=>"Yes",2=>"No");
		$sql_cond="";
		$supply_so=str_pad(str_replace("'","",$cbo_supply_source), 2, "0", STR_PAD_LEFT);
		if (str_replace("'","",$cbo_supply_source)>0) $sql_cond .=" and a.lc_category='".$supply_so."'";
		if ($cbo_company!="") $sql_cond .=" and a.importer_id in($cbo_company) ";
		if ($cbo_issue>0) $sql_cond .=" and a.issuing_bank_id=$cbo_issue ";
		if ($item_category_id>0) $sql_cond .=" and a.pi_entry_form=".$category_wise_entry_form[$item_category_id];
		
		if ($lc_type_id>0) $sql_cond .=" and a.lc_type_id=$lc_type_id ";
		if ($cbo_bonded>0) $sql_cond .=" and a.bonded_warehouse=$cbo_bonded ";
		if ($cbo_payterm_id>0) $sql_cond .=" and a.payterm_id=$cbo_payterm_id ";
		if ($cbo_supplier>0) $sql_cond .=" and a.supplier_id=$cbo_supplier ";
		if($txt_lc_no!="") $sql_cond .=" and a.lc_number = '$txt_lc_no'";
		
		//print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));
		$cbo_based_on=str_replace("'","",$cbo_based_on);
		if($db_type==2)
		{
			if($cbo_based_on==1)
			{
				if( $from_date=="" && $to_date=="" ) $lc_date=""; else $lc_date= " and a.lc_date between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
			}
			else
			{
				if( $from_date=="" && $to_date=="" ) $lc_date=""; else $lc_date= " and a.insert_date between '".date("j-M-Y",strtotime($from_date)) ." 12:00:01 AM"."' and '".date("j-M-Y",strtotime($to_date)). " 11:59:59 PM"."'";
			}
			$select_insert_date=" to_char(a.insert_date,'DD-MM-YYYY') as  insert_date";
			
		}
		else if($db_type==0)
		{
			if($cbo_based_on==1)
			{
				if( $from_date=='' && $to_date=='' ) $lc_date=""; else $lc_date= " and a.lc_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
			}
			else
			{
				if( $from_date=='' && $to_date=='' ) $lc_date=""; else $lc_date= " and a.insert_date between '".change_date_format($from_date,'yyyy-mm-dd'). " 00:00:01"."' and '".change_date_format($to_date,'yyyy-mm-dd'). " 23:59:59"."'";
			}
			
			$select_insert_date=" DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date";
		}
		
		
		if($db_type==0)
		{
			$select_pi_id=" group_concat(b.pi_id) as PI_ID";
		}
		else
		{
			$select_pi_id=" listagg(cast(b.pi_id as varchar(4000)),',') within group(order by b.pi_id) as PI_ID";
		}
		if($txt_search_common!="") 
		{
			if ($cbo_search_by==1) 
			{
				$sql = "SELECT a.id, b.is_lc_sc, b.lc_sc_id  
				from com_btb_lc_master_details a, com_btb_export_lc_attachment b, com_export_lc c
				where a.id=b.import_mst_id and b.lc_sc_id=c.id and b.is_lc_sc=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $issue_banking $category_entry_form_cond $lc_tpe_id $bonded_warehouse $lc_date  $supply_source_cond $lc_no_cond $txt_lc_no_cond order by a.lc_date ";
			}
			else
			{
				$sql = "SELECT a.id, b.is_lc_sc, b.lc_sc_id
				from com_btb_lc_master_details a, com_btb_export_lc_attachment b, com_sales_contract c
				where a.id=b.import_mst_id and b.lc_sc_id=c.id and b.is_lc_sc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $issue_banking $category_entry_form_cond $lc_tpe_id $bonded_warehouse $lc_date  $supply_source_cond $lc_no_cond $txt_sc_no_cond  order by a.lc_date ";
			}
			$lc_sc_sql=sql_select($sql);
			foreach($lc_sc_sql as $row)
			{
				$ls_sc_id.=$row[csf('id')].',';
			}
				
	
			$ls_sc_id_cond = implode(",",array_unique(explode(",",chop($ls_sc_id,','))));
			if($ls_sc_id_cond!="")
			{
				$btb_lc_sc_no_cond=" and a.id in($ls_sc_id_cond)";
			}
			else
			{
				echo "<br><strong><span style='color:red;'>Data Not Found</span></strong>";die;
			}
			
			
			$main_sql="SELECT a.id as BTB_ID, a.lc_number as LC_NUMBER, a.importer_id as IMPORTER_ID, a.lc_type_id as LC_TYPE_ID, a.lc_date as LC_DATE, a.maturity_from_id as MATURITY_FROM_ID, a.supplier_id as SUPPLIER_ID, a.payterm_id as PAYTERM_ID, a.tenor as TENOR, a.issuing_bank_id as ISSUING_BANK_ID, a.lc_value as LC_VALUE, a.inserted_by as INSERTED_BY, a.insert_date as INSERT_DATE, p.is_lc_sc as IS_LC_SC, p.lc_sc_id as LC_SC_ID, c.id as INV_ID, c.invoice_no as INVOICE_NO, c.company_acc_date as COMPANY_ACC_DATE, c.bank_acc_date as BANK_ACC_DATE, c.bank_ref as BANK_REF, c.retire_source as RETIRE_SOURCE, c.shipment_date as SHIPMENT_DATE, c.edf_paid_date as EDF_PAID_DATE, c.maturity_date as MATURITY_DATE, sum(b.current_acceptance_value) as ACCEPTANCE_VALUE, $select_pi_id
			from com_btb_export_lc_attachment p, com_btb_lc_master_details a
			left join com_import_invoice_dtls b on a.id=b.btb_lc_id and b.status_active=1 and b.is_deleted=0
			left join com_import_invoice_mst c on b.import_invoice_id=c.id and c.status_active=1 and c.is_deleted=0
			where p.import_mst_id=a.id  and p.status_active=1 and p.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $lc_date $sql_cond $btb_lc_sc_no_cond
			group by a.id, a.lc_number, a.importer_id, a.lc_type_id, a.lc_date, a.maturity_from_id, a.supplier_id, a.payterm_id, a.tenor, a.issuing_bank_id, a.lc_value, a.inserted_by, a.insert_date, p.is_lc_sc, p.lc_sc_id, c.id, c.invoice_no, c.company_acc_date, c.bank_acc_date, c.bank_ref, c.retire_source, c.shipment_date, c.edf_paid_date, c.maturity_date
			order by a.lc_date";

		}
		else
		{
			$main_sql="SELECT a.id as BTB_ID, a.lc_number as LC_NUMBER, a.importer_id as IMPORTER_ID, a.lc_type_id as LC_TYPE_ID, a.lc_date as LC_DATE, a.maturity_from_id as MATURITY_FROM_ID, a.supplier_id as SUPPLIER_ID, a.payterm_id as PAYTERM_ID, a.tenor as TENOR, a.issuing_bank_id as ISSUING_BANK_ID, a.lc_value as LC_VALUE, a.inserted_by as INSERTED_BY, a.insert_date as INSERT_DATE, p.is_lc_sc as IS_LC_SC, p.lc_sc_id as LC_SC_ID, c.id as INV_ID, c.invoice_no as INVOICE_NO, c.company_acc_date as COMPANY_ACC_DATE, c.bank_acc_date as BANK_ACC_DATE, c.bank_ref as BANK_REF, c.retire_source as RETIRE_SOURCE, c.shipment_date as SHIPMENT_DATE, c.edf_paid_date as EDF_PAID_DATE, c.maturity_date as MATURITY_DATE, sum(b.current_acceptance_value) as ACCEPTANCE_VALUE, $select_pi_id 
			from com_btb_export_lc_attachment p, com_btb_lc_master_details a
			left join com_import_invoice_dtls b on a.id=b.btb_lc_id and b.status_active=1 and b.is_deleted=0
			left join com_import_invoice_mst c on b.import_invoice_id=c.id and c.status_active=1 and c.is_deleted=0
			where p.import_mst_id=a.id  and p.status_active=1 and p.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $lc_date $sql_cond
			group by a.id, a.lc_number, a.importer_id, a.lc_type_id, a.lc_date, a.maturity_from_id, a.supplier_id, a.payterm_id, a.tenor, a.issuing_bank_id, a.lc_value, a.inserted_by, a.insert_date, p.is_lc_sc, p.lc_sc_id, c.id, c.invoice_no, c.company_acc_date, c.bank_acc_date, c.bank_ref, c.retire_source, c.shipment_date, c.edf_paid_date, c.maturity_date
			order by a.lc_date";
		}
		//echo $main_sql;die;
		$main_sql_result=sql_select($main_sql);
		
		$export_lc_sc="select id as LC_SC_ID, export_lc_no as LC_SC_NO, lc_date as LC_SC_DATE, buyer_name as BUYER_NAME, lien_bank as LIEN_BANK, tenor as TENOR, 0 as TYPE 
		from com_export_lc where beneficiary_name in($cbo_company)
		union all 
		select id as LC_SC_ID, contract_no as LC_SC_NO, contract_date as LC_SC_DATE, buyer_name as BUYER_NAME, lien_bank as LIEN_BANK, tenor as TENOR, 1 as TYPE 
		from com_sales_contract where beneficiary_name in($cbo_company)";
		//echo $export_lc_sc;die;
		$export_lc_sc_result=sql_select($export_lc_sc);
		$export_lc_sc_data=array();
		foreach($export_lc_sc_result as $row)
		{
			$ref_data=$row["LC_SC_ID"]."__".$row["TYPE"];
			$export_lc_sc_data[$ref_data]["LC_SC_ID"]=$row["LC_SC_ID"];
			$export_lc_sc_data[$ref_data]["LC_SC_NO"]=$row["LC_SC_NO"];
			$export_lc_sc_data[$ref_data]["LC_SC_DATE"]=$row["LC_SC_DATE"];
			$export_lc_sc_data[$ref_data]["BUYER_NAME"]=$row["BUYER_NAME"];
			$export_lc_sc_data[$ref_data]["LIEN_BANK"]=$row["LIEN_BANK"];
			$export_lc_sc_data[$ref_data]["TENOR"]=$row["TENOR"];
		}
		//echo "<pre>";print_r($export_lc_sc_data);die;
		unset($export_lc_sc_result);
		
		$sql_pi="select id as PI_ID, supplier_id as SUPPLIER_ID, pi_number as PI_NUMBER, pi_date as PI_DATE, item_category_id as ITEM_CATEGORY_ID from com_pi_master_details where status_active=1 and is_deleted=0 and importer_id in($cbo_company)";		
		//echo $sql_pi;die;
		$sql_pi_result=sql_select($sql_pi);
		$pi_data=array();
		foreach($sql_pi_result as $row)
		{
			$ref_data=$row["PI_ID"];
			$pi_data[$ref_data]["PI_ID"]=$row["PI_ID"];
			$pi_data[$ref_data]["SUPPLIER_ID"]=$row["SUPPLIER_ID"];
			$pi_data[$ref_data]["ITEM_CATEGORY_ID"]=$row["ITEM_CATEGORY_ID"];
			$pi_data[$ref_data]["PI_NUMBER"]=$row["PI_NUMBER"];
			$pi_data[$ref_data]["PI_DATE"]=$row["PI_DATE"];
		}
		unset($sql_pi_result);
		
		$payment_sql="select invoice_id as INVOICE_ID, payment_date as PAYMENT_DATE, accepted_ammount as ACCEPTED_AMMOUNT, domistic_currency as DOMISTIC_CURRENCY from com_import_payment where status_active=1 and is_deleted=0";
		$payment_sql_result=sql_select($payment_sql);
		$payment_data=array();
		foreach($payment_sql_result as $row)
		{
			$ref_data=$row["INVOICE_ID"];
			$payment_data[$ref_data]["INVOICE_ID"]=$row["INVOICE_ID"];
			$payment_data[$ref_data]["PAYMENT_DATE"]=$row["PAYMENT_DATE"];
			$payment_data[$ref_data]["ACCEPTED_AMMOUNT"]+=$row["ACCEPTED_AMMOUNT"];
			$payment_data[$ref_data]["DOMISTIC_CURRENCY"]+=$row["DOMISTIC_CURRENCY"];
		}
		unset($payment_sql_result);
		
		$atsite_payment_sql="select invoice_id as INVOICE_ID, payment_date as PAYMENT_DATE, accepted_ammount as ACCEPTED_AMMOUNT, domistic_currency as DOMISTIC_CURRENCY from com_import_payment_com where status_active=1 and is_deleted=0";
		$atsite_payment_sql_result=sql_select($atsite_payment_sql);
		$atsite_payment_data=array();
		foreach($atsite_payment_sql_result as $row)
		{
			$ref_data=$row["INVOICE_ID"];
			$atsite_payment_data[$ref_data]["INVOICE_ID"]=$row["INVOICE_ID"];
			$atsite_payment_data[$ref_data]["PAYMENT_DATE"]=$row["PAYMENT_DATE"];
			$atsite_payment_data[$ref_data]["ACCEPTED_AMMOUNT"]+=$row["ACCEPTED_AMMOUNT"];
			$atsite_payment_data[$ref_data]["DOMISTIC_CURRENCY"]+=$row["DOMISTIC_CURRENCY"];
		}
		unset($atsite_payment_sql_result);
		$invoice_payment_atsite=return_library_array( "select invoice_id, sum(accepted_ammount) as accepted_ammount from com_import_payment_com where status_active=1 and is_deleted=0 group by invoice_id",'invoice_id','accepted_ammount');
		ob_start();
		$div_width="3120";
		$tbl_width="3100";
		
		?>
		<div id="scroll_body" align="center" style="height:auto; width:<? echo $div_width; ?>px; margin:0 auto; padding:0;">
			<table width="<? echo $div_width;?>">
				<?
				$company_library=sql_select("select id, company_name, plot_no, level_no,road_no,city from lib_company where id in(".$cbo_company.")");
				foreach( $company_library as $row)
				{
					$company_name.=$row[csf('company_name')].", ";
				}
				?>
				<tr>
					<td colspan="29" align="center" style="font-size:22px"><center><strong><? echo rtrim($company_name,", ");?></strong></center></td>
				</tr>
				<tr>
					<td colspan="29" align="center" style="font-size:18px"><center><strong><u><? echo $report_title; ?></u></strong></center></td>
				</tr>
			</table>
			<table cellspacing="0" width="<? echo $tbl_width;?>"  border="1" rules="all" class="rpt_table" align="left">
			   <thead>
               		<tr>
                    	<th width="30">SL</th>
                        <th width="120">Company</th>
                        <th width="70">Lc Type</th>
                        <th width="120">LC Number</th>
                        <th width="70">LC Issue Date</th>
                        <th width="90">LC Value</th>
						<th width="100">Insert By</th>
                        <th width="120">Insert Date and Time</th>
                        <th width="120">Inv. No</th>
                        <th width="90">Accep. Value</th>
                        <th width="90">Yet to Acceptance</th>
                        <th width="70">Com. Acc. Date</th>
                        <th width="70">Bank Acc. Date</th>
                        <th width="100">Bank Ref.no</th>
                        <th width="100">Reture Source</th>
                        <th width="70">Supplier Paid Date</th>
                        <th width="90">Supp. Paid Amount</th>
                        <th width="90">Deferred Liability</th>
                        <th width="90">Bank Liability Amount/EDF</th>
                        <th width="70">BTB/EDF Maturity Date</th>
                        <th width="70">EDF Paid Date</th>
                        <th width="90">EDF/Upas Paid Amount</th>
                        <th width="90">Balance BTB L/C Value</th>
                        <th width="130">Supplier Name</th>
                        <th width="120">PI Number</th>
                        <th width="100">PI Date</th>
                        <th width="120">Contract/LC no.</th>
                        <th width="70">SC/LC Date</th>
                        <th width="120">Buyer Name</th>
                        <th width="120">Issuing Bank</th>
                        <th width="120">Item Category</th>
                        <th width="60">Tenor</th>
                        <th width="100">Remarks</th>
                    </tr>
			   </thead>
			</table>
			<div style="width:<? echo $div_width;?>px; overflow-y: scroll; overflow-x:hidden; max-height:300px;" id="scroll_body2"> 
				<table cellspacing="0" width="<? echo $tbl_width;?>"  border="1" rules="all" class="rpt_table" id="tbl_marginlc_list" style="float: left;" >
				<?
				$i=1;
				//print_r($main_sql_result);die;
				foreach($main_sql_result as $row)
				{            
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					$deferd_liability=0;
					if($payment_data[$row["INV_ID"]]["ACCEPTED_AMMOUNT"]=="") $deferd_liability=$row["ACCEPTANCE_VALUE"];
					?>
					<tr bgcolor="<? echo $bgcolor ; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td align="center" width="30"><? echo $i; ?></td>
                        <?
						
						if($lc_id_check[$row["BTB_ID"]]=="")
						{
							$lc_id_check[$row["BTB_ID"]]=$row["BTB_ID"];
							$total_lc_value+=$row["LC_VALUE"];
							$lc_wise_inv_value=0;
							$lc_wise_inv_value+=$row["ACCEPTANCE_VALUE"];
							?>
                            <td align="center" width="120"><p><? echo $companyArr[$row["IMPORTER_ID"]]; ?></p></td>
                            <td align="center" width="70"><p><? echo $lc_type[$row["LC_TYPE_ID"]]; ?></p></td>
                            <td align="center" width="120"><p><? echo $row["LC_NUMBER"]; ?>&nbsp;</p></td>
                            <td align="center" width="70"><p>&nbsp;<? if($row["LC_DATE"]!="" && $row["LC_DATE"]!="0000-00-00") echo change_date_format($row["LC_DATE"]); ?></p></td>
                            <td align="right" width="90"><? echo number_format($row["LC_VALUE"],2); ?></td>
                            <td width="100"><? echo $user_name[$row["INSERTED_BY"]]; ?></td>
                            <td align="center" width="120"><? echo $row["INSERT_DATE"]; ?></td>
                            <?
						}
						else
						{
							$lc_wise_inv_value+=$row["ACCEPTANCE_VALUE"];
							?>
                            <td align="center" width="120"><p></p></td>
                            <td align="center" width="70"><p></p></td>
                            <td align="center" width="120"><p></p></td>
                            <td align="center" width="70"><p></p></td>
                            <td align="right" width="90"></td>
                            <td align="right" width="100"></td>
                            <td align="right" width="120"></td>
                            <?
						}
						$yet_to_accep=$row["LC_VALUE"]-$lc_wise_inv_value;
						?>
						<td width="120" align="center"><p><? echo $row["INVOICE_NO"]; ?></p></td>
						<td align="right" width="90"><? echo number_format($row["ACCEPTANCE_VALUE"],2); ?></td>
                        <td align="right" width="90"><?  echo number_format($yet_to_accep,2);  ?></td>
                        <td align="center" width="70"><p>&nbsp;<? if($row["COMPANY_ACC_DATE"]!="" && $row["COMPANY_ACC_DATE"]!="0000-00-00") echo change_date_format($row["COMPANY_ACC_DATE"]); ?></p></td>
                        <td align="center" width="70"><p>&nbsp;<? if($row["BANK_ACC_DATE"]!="" && $row["BANK_ACC_DATE"]!="0000-00-00") echo change_date_format($row["BANK_ACC_DATE"]); ?></p></td>
						<td width="100"><p><? echo $row["BANK_REF"]; ?>&nbsp;</p></td>
						<td width="100" align="center"><p><? echo $commercial_head[$row["RETIRE_SOURCE"]]; ?></p></td>
						<td align="center" width="70"><p>&nbsp;<? if($row["BANK_ACC_DATE"]!="" && $row["BANK_ACC_DATE"]!="0000-00-00") echo change_date_format($row["BANK_ACC_DATE"]); ?></p></td>
                        <td align="right" width="90"><? echo number_format($payment_data[$row["INV_ID"]]["ACCEPTED_AMMOUNT"],2); ?></td>
                        <td align="right" width="90" title="<?= $payment_data[$row["INV_ID"]]["ACCEPTED_AMMOUNT"];?>"><? echo number_format($deferd_liability,2); $total_deferd_liability+=$deferd_liability; ?></td>
                        <td align="right" width="90" title="<?= $row["PAYTERM_ID"];?>">
						<? 
						$edf_liability=0;
						if($row["RETIRE_SOURCE"]==30 ||$row["RETIRE_SOURCE"]==31 || $row["RETIRE_SOURCE"]==142) 
						{
							if($row["PAYTERM_ID"]==3) 
							{
								$edf_liability=$row["ACCEPTANCE_VALUE"];
							}
							else
							{
								if($row["EDF_PAID_DATE"] != "" && $row["EDF_PAID_DATE"] != "0000-00-00" && strtotime($row["EDF_PAID_DATE"])<strtotime("25-10-2020"))
								{
									$edf_liability=$row["ACCEPTANCE_VALUE"];
								}
								else
								{
									$edf_liability=$invoice_payment_atsite[$row["INV_ID"]];
								}
							}
							echo number_format($edf_liability,2); 
							$total_edf_liabilty+=$edf_liability;
						}
						?></td>
                        <td align="center" width="70"><p>&nbsp;<? if($row["MATURITY_DATE"]!="" && $row["MATURITY_DATE"]!="0000-00-00") echo change_date_format($row["MATURITY_DATE"]); ?></p></td>
                        <td align="center" width="70"><p>&nbsp;<? if($atsite_payment_data[$row["INV_ID"]]["PAYMENT_DATE"]!="" && $atsite_payment_data[$row["INV_ID"]]["PAYMENT_DATE"]!="0000-00-00") echo change_date_format( $atsite_payment_data[$row["INV_ID"]]["PAYMENT_DATE"]); ?></p></td>
                        <td align="right" width="90"><? echo number_format($atsite_payment_data[$row["INV_ID"]]["ACCEPTED_AMMOUNT"],2); ?></td>
                        <td align="right" width="90">
						<?
						$btb_balance_amt=0;
						if($row["RETIRE_SOURCE"]==30 ||$row["RETIRE_SOURCE"]==31 || $row["RETIRE_SOURCE"]==142)
						{
							$btb_balance_amt=$row["ACCEPTANCE_VALUE"]-$atsite_payment_data[$row["INV_ID"]]["ACCEPTED_AMMOUNT"];
						}
						else
						{
							$btb_balance_amt=$row["ACCEPTANCE_VALUE"]-$payment_data[$row["INV_ID"]]["ACCEPTED_AMMOUNT"];
						}
						echo number_format($btb_balance_amt,2); 
						$pi_id_arr=array_unique(explode(",",$row["PI_ID"]));
						$pi_nums="";$item_cat="";$pi_date="";
						foreach($pi_id_arr as $pi_id)
						{
							$pi_nums.=$pi_data[$pi_id]["PI_NUMBER"].",";
							$pi_date.=change_date_format($pi_data[$pi_id]["PI_DATE"]).", ";
							if($cat_check[$row["INV_ID"]][$pi_data[$pi_id]["ITEM_CATEGORY_ID"]]=="")
							{
								$cat_check[$row["INV_ID"]][$pi_data[$pi_id]["ITEM_CATEGORY_ID"]]=$pi_data[$pi_id]["ITEM_CATEGORY_ID"];
								$item_cat.=$item_category[$pi_data[$pi_id]["ITEM_CATEGORY_ID"]].",";
							}
						}
						$pi_nums=chop($pi_nums,",");
						$item_cat=chop($item_cat,",");
						$pi_date=chop($pi_date,", ");
						?></td>
						<td width="130"><p><? if($exportPiSuppArr[$row["BTB_ID"]]==1) echo $companyArr[$row["SUPPLIER_ID"]]; else echo $supplierArr[$row["SUPPLIER_ID"]]; echo $supplierArr[$row["SUPPLIER_ID"]]; ?></p></td>
						<td width="120"><p><? echo $pi_nums; ?></p></td>
						<td width="100"><p>&nbsp;<? echo change_date_format($pi_date); ?></p></td>
                        <td width="120"><p><? echo $export_lc_sc_data[$row["LC_SC_ID"]."__".$row["IS_LC_SC"]]["LC_SC_NO"]; ?></p></td>
                        <td width="70" align="center"><p>&nbsp;<? if($export_lc_sc_data[$row["LC_SC_ID"]."__".$row["IS_LC_SC"]]["LC_SC_DATE"]!="" && $export_lc_sc_data[$row["LC_SC_ID"]."__".$row["IS_LC_SC"]]["LC_SC_DATE"]!="0000-00-00") echo change_date_format($export_lc_sc_data[$row["LC_SC_ID"]."__".$row["IS_LC_SC"]]["LC_SC_DATE"]); ?></p></td>
                        <td width="120"><p><? echo $buyerArr[$export_lc_sc_data[$row["LC_SC_ID"]."__".$row["IS_LC_SC"]]["BUYER_NAME"]]; ?></p></td>
						<td width="120"><p><? echo $issueBankrArr[$row["ISSUING_BANK_ID"]]; ?></p></td>
						<td width="120"><p><? echo $item_cat; ?></p></td>
						<td width="60" align="center"><p><? echo $row["TENOR"]; ?></p></td>
                        <td width="100"><p></p></td>
					</tr>
					<?
					$total_accep_value+=$row["ACCEPTANCE_VALUE"];
					$total_upass_amt+=$atsite_payment_data[$row["INV_ID"]]["ACCEPTED_AMMOUNT"];
					$total_balance_value+=$btb_balance_amt;
					//$total_yet_to_accep+=$yet_to_accep;
					$i++;
				}
				?>
				</table>
				<table cellspacing="0" width="<? echo $tbl_width;?>"  border="1" rules="all" class="rpt_table" >
					<tfoot>
						<tr>
                            <th width="30">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="70" align="right">Total:</th>
                            <th width="90" align="right"><? echo number_format($total_lc_value,2); ?></th>
                            <th width="100">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="90" align="right"><? echo number_format($total_accep_value,2); ?></th>
                            <th width="90" align="right"><? $total_yet_to_accep=$total_lc_value-$total_accep_value; echo number_format($total_yet_to_accep,2); ?></th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="90">&nbsp;</th>
                            <th width="90" align="right"><? echo number_format($total_deferd_liability,2); ?></th>
                            <th width="90" align="right"><? echo number_format($total_edf_liabilty,2); ?></th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="90" align="right"><? echo number_format($total_upass_amt,2); ?></th>
                            <th width="90" align="right"><? echo number_format($total_balance_value,2); ?></th>
                            <th width="130">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="60">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                        </tr>
						</tr>
					</tfoot>         
				</table>
			</div>
		</div>
		<?
	}

	if($report_type==5) //LC Status
	{
		$item_category_id=str_replace("'","",$cbo_item_category_id);
		//$item_category_arr = array(4,8,10,11,13,15,21);
		$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
		$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
		$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name");
		$issueBankrArr = return_library_array("select id,bank_name from lib_bank ","id","bank_name");
		//$user_name = return_library_array("select id,user_name from user_passwd ","id","user_name");
		//$hscodeArr = return_library_array("select id, hs_code from com_pi_master_details ","id","hs_code"); 
		//$lc_num_arr = return_library_array("select id,export_lc_no from com_export_lc ","id","export_lc_no"); 
		//$sc_num_arr = return_library_array("select id,contract_no from com_sales_contract ","id","contract_no"); 
		
		//var_dump($assocArray);die;
		$cbo_company=str_replace("'","",$cbo_company_id);
		$cbo_issue=str_replace("'","",$cbo_issue_banking);
		$lc_type_id=str_replace("'","",$cbo_lc_type_id);
		$txt_lc_no=str_replace("'","",$txt_lc_no);
		$cbo_payterm_id=str_replace("'","",$cbo_payterm_id);
		
		$cbo_bonded=str_replace("'","",$cbo_bonded_warehouse);
		$from_date=str_replace("'","",$txt_date_from);
		$to_date=str_replace("'","",$txt_date_to);
		$cbo_supplier=str_replace("'","",$cbo_supplier);
		$txt_search_common=str_replace("'","",$txt_search_common);
		$cbo_status=str_replace("'","",$cbo_status);
		$cbo_reference_close=str_replace("'","",$cbo_reference_close);

		$reference_close_cond='';
		if ($cbo_reference_close == 1)
			$reference_close_cond = " and a.ref_closing_status=1";
		else if ($cbo_reference_close == 2) 
			$reference_close_cond = " and a.ref_closing_status=0";
		else $reference_close_cond = " and a.ref_closing_status in(0,1)";
		$reference_close=array(1=>"Yes",2=>"No");
		$sql_cond="";
		$supply_so=str_pad(str_replace("'","",$cbo_supply_source), 2, "0", STR_PAD_LEFT);
		if (str_replace("'","",$cbo_supply_source)>0) $sql_cond .=" and a.lc_category='".$supply_so."'";
		if ($cbo_company!="") $sql_cond .=" and a.importer_id in($cbo_company) ";
		if ($cbo_issue>0) $sql_cond .=" and a.issuing_bank_id=$cbo_issue ";
		if ($item_category_id>0) $sql_cond .=" and a.pi_entry_form=".$category_wise_entry_form[$item_category_id];
		
		if ($lc_type_id>0) $sql_cond .=" and a.lc_type_id=$lc_type_id ";
		if ($cbo_bonded>0) $sql_cond .=" and a.bonded_warehouse=$cbo_bonded ";
		if ($cbo_payterm_id>0) $sql_cond .=" and a.payterm_id=$cbo_payterm_id ";
		if ($cbo_supplier>0) $sql_cond .=" and a.supplier_id=$cbo_supplier ";
		if($txt_lc_no!="") $sql_cond .=" and a.lc_number = '$txt_lc_no'";
		
		//print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));
		$cbo_based_on=str_replace("'","",$cbo_based_on);
		if($db_type==2)
		{
			if($cbo_based_on==1)
			{
				if( $from_date=="" && $to_date=="" ) $lc_date=""; else $lc_date= " and a.lc_date between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
			}
			else
			{
				if( $from_date=="" && $to_date=="" ) $lc_date=""; else $lc_date= " and a.insert_date between '".date("j-M-Y",strtotime($from_date)) ." 12:00:01 AM"."' and '".date("j-M-Y",strtotime($to_date)). " 11:59:59 PM"."'";
			}
			$select_insert_date=" to_char(a.insert_date,'DD-MM-YYYY') as  insert_date";
			
		}
		else if($db_type==0)
		{
			if($cbo_based_on==1)
			{
				if( $from_date=='' && $to_date=='' ) $lc_date=""; else $lc_date= " and a.lc_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
			}
			else
			{
				if( $from_date=='' && $to_date=='' ) $lc_date=""; else $lc_date= " and a.insert_date between '".change_date_format($from_date,'yyyy-mm-dd'). " 00:00:01"."' and '".change_date_format($to_date,'yyyy-mm-dd'). " 23:59:59"."'";
			}
			
			$select_insert_date=" DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date";
		}
		
		
		if($db_type==0)
		{
			$select_pi_id=" group_concat(b.pi_id) as PI_ID";
			$select_lc_sc_id=" group_concat(p.lc_sc_id) as LC_SC_ID";
		}
		else
		{
			$select_pi_id=" listagg(cast(a.pi_id as varchar(4000)),',') within group(order by a.pi_id) as PI_ID";
			$select_lc_sc_id=" listagg(cast(p.lc_sc_id as varchar(4000)),',') within group(order by p.lc_sc_id) as LC_SC_ID";
		}
		if($txt_search_common!="") 
		{
			if ($cbo_search_by==1) 
			{
				$sql = "SELECT a.id, b.is_lc_sc, b.lc_sc_id  
				from com_btb_lc_master_details a, com_btb_export_lc_attachment b, com_export_lc c
				where a.id=b.import_mst_id and b.lc_sc_id=c.id and b.is_lc_sc=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $issue_banking $category_entry_form_cond $lc_tpe_id $bonded_warehouse $lc_date  $supply_source_cond $lc_no_cond $txt_lc_no_cond order by a.lc_date ";
			}
			else
			{
				$sql = "SELECT a.id, b.is_lc_sc, b.lc_sc_id
				from com_btb_lc_master_details a, com_btb_export_lc_attachment b, com_sales_contract c
				where a.id=b.import_mst_id and b.lc_sc_id=c.id and b.is_lc_sc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $issue_banking $category_entry_form_cond $lc_tpe_id $bonded_warehouse $lc_date  $supply_source_cond $lc_no_cond $txt_sc_no_cond  order by a.lc_date ";
			}
			$lc_sc_sql=sql_select($sql);
			foreach($lc_sc_sql as $row)
			{
				$ls_sc_id.=$row[csf('id')].',';
			}
				
	
			$ls_sc_id_cond = implode(",",array_unique(explode(",",chop($ls_sc_id,','))));
			if($ls_sc_id_cond!="")
			{
				$btb_lc_sc_no_cond=" and a.id in($ls_sc_id_cond)";
			}
			else
			{
				echo "<br><strong><span style='color:red;'>Data Not Found</span></strong>";die;
			}
			
			// 
			$main_sql="SELECT a.id as BTB_ID, a.lc_number as LC_NUMBER, a.application_date as APPLICATION_DATE, a.importer_id as IMPORTER_ID, a.lc_type_id as LC_TYPE_ID, a.lc_date as LC_DATE, a.maturity_from_id as MATURITY_FROM_ID, a.supplier_id as SUPPLIER_ID, a.payterm_id as PAYTERM_ID, a.tenor as TENOR, a.issuing_bank_id as ISSUING_BANK_ID, a.lc_value as LC_VALUE, a.uom_id as UOM_ID, a.lc_category as LC_CATEGORY, c.id as INV_ID, c.invoice_no as INVOICE_NO, c.invoice_date as INVOICE_DATE, a.remarks as REMARKS, c.company_acc_date as COMPANY_ACC_DATE, c.bank_acc_date as BANK_ACC_DATE, c.bank_ref as BANK_REF, c.retire_source as RETIRE_SOURCE, c.shipment_date as SHIPMENT_DATE, c.edf_paid_date as EDF_PAID_DATE, c.maturity_date as MATURITY_DATE, c.doc_rcv_date as DOC_RCV_DATE,c.nagotiate_date as NAGOTIATE_DATE, sum(b.current_acceptance_value) as ACCEPTANCE_VALUE, c.bill_entry_value as BILL_ENTRY_VALUE, c.bill_no as BILL_NO, c.bill_date as BILL_DATE, c.bill_of_entry_no as BILL_OF_ENTRY_NO, c.bill_of_entry_date as BILL_OF_ENTRY_DATE, $select_pi_id
			from  com_btb_lc_master_details a
			left join com_import_invoice_dtls b on a.id=b.btb_lc_id and b.status_active=1 and b.is_deleted=0
			left join com_import_invoice_mst c on b.import_invoice_id=c.id and c.status_active=1 and c.is_deleted=0
			where a.status_active=1 and a.is_deleted=0 $lc_date $sql_cond $btb_lc_sc_no_cond
			group by a.id, a.lc_number, a.application_date, a.importer_id, a.lc_type_id, a.lc_date, a.maturity_from_id, a.supplier_id, a.payterm_id, a.tenor, a.issuing_bank_id, a.lc_value, a.uom_id, a.lc_category, a.remarks, c.id, c.invoice_no, c.invoice_date, c.company_acc_date, c.bank_acc_date, c.bank_ref, c.retire_source, c.shipment_date, c.edf_paid_date, c.maturity_date, c.doc_rcv_date,c.nagotiate_date, c.bill_entry_value, c.bill_no, c.bill_date, c.bill_of_entry_no, c.bill_of_entry_date 
			order by a.id";

		}
		else
		{
			$main_sql="SELECT a.id as BTB_ID, a.lc_number as LC_NUMBER, a.application_date as APPLICATION_DATE, a.importer_id as IMPORTER_ID, a.lc_type_id as LC_TYPE_ID, a.lc_date as LC_DATE, a.maturity_from_id as MATURITY_FROM_ID, a.supplier_id as SUPPLIER_ID, a.payterm_id as PAYTERM_ID, a.tenor as TENOR, a.issuing_bank_id as ISSUING_BANK_ID, a.lc_value as LC_VALUE, a.uom_id as UOM_ID, a.lc_category as LC_CATEGORY, c.id as INV_ID, c.invoice_no as INVOICE_NO, c.invoice_date as INVOICE_DATE, a.remarks as REMARKS, c.company_acc_date as COMPANY_ACC_DATE, c.bank_acc_date as BANK_ACC_DATE, c.bank_ref as BANK_REF, c.retire_source as RETIRE_SOURCE, c.shipment_date as SHIPMENT_DATE, c.edf_paid_date as EDF_PAID_DATE, c.maturity_date as MATURITY_DATE, c.doc_rcv_date as DOC_RCV_DATE,c.nagotiate_date as NAGOTIATE_DATE, sum(b.current_acceptance_value) as ACCEPTANCE_VALUE, c.bill_entry_value as BILL_ENTRY_VALUE, c.bill_no as BILL_NO, c.bill_date as BILL_DATE, c.bill_of_entry_no as BILL_OF_ENTRY_NO, c.bill_of_entry_date as BILL_OF_ENTRY_DATE, $select_pi_id
			from com_btb_lc_master_details a
			left join com_import_invoice_dtls b on a.id=b.btb_lc_id and b.status_active=1 and b.is_deleted=0
			left join com_import_invoice_mst c on b.import_invoice_id=c.id and c.status_active=1 and c.is_deleted=0
			where a.status_active=1 and a.is_deleted=0 $lc_date $sql_cond
			group by a.id, a.lc_number, a.application_date, a.importer_id, a.lc_type_id, a.lc_date, a.maturity_from_id, a.supplier_id, a.payterm_id, a.tenor, a.issuing_bank_id, a.lc_value, a.uom_id, a.lc_category, a.remarks, c.id, c.invoice_no, c.invoice_date, c.company_acc_date, c.bank_acc_date, c.bank_ref, c.retire_source, c.shipment_date, c.edf_paid_date, c.maturity_date, c.doc_rcv_date,c.nagotiate_date, c.bill_entry_value, c.bill_no, c.bill_date, c.bill_of_entry_no, c.bill_of_entry_date 
			order by a.id";

		}
		//echo $main_sql;// die;
		$main_sql_result=sql_select($main_sql);

		$row_count=array();
		$total_inv_val=array();
		foreach($main_sql_result as $val)
		{
			$row_count[$val['BTB_ID']]++;
			$total_inv_val[$val['BTB_ID']]+=$val['ACCEPTANCE_VALUE'];
		}
		
		$export_lc_sc="SELECT a.import_mst_id as IMPORT_MST_ID, a.lc_sc_id as LC_SC_ID, a.is_lc_sc as TYPE, b.id as LC_SC_ID, b.export_lc_no as LC_SC_NO, b.lc_year as FILE_YEAR, b.internal_file_no as INTERNAL_FILE_NO
		from com_btb_export_lc_attachment a, com_export_lc b where b.beneficiary_name in($cbo_company) and a.lc_sc_id=b.id and a.is_lc_sc=0
		union all 
		select a.import_mst_id as IMPORT_MST_ID, a.lc_sc_id as LC_SC_ID, a.is_lc_sc as TYPE, b.id as LC_SC_ID, b.contract_no as LC_SC_NO, b.sc_year as FILE_YEAR, b.internal_file_no as INTERNAL_FILE_NO
		from com_btb_export_lc_attachment a, com_sales_contract b where b.beneficiary_name in($cbo_company) and a.lc_sc_id=b.id  and a.is_lc_sc=1";
		// echo $export_lc_sc;die;
		$export_lc_sc_result=sql_select($export_lc_sc);
		$export_lc_sc_data=array();
		foreach($export_lc_sc_result as $row)
		{
			$ref_data=$row["IMPORT_MST_ID"];
			$export_lc_sc_data[$ref_data]["LC_SC_NO"].=$row["LC_SC_NO"].', ';
			$export_lc_sc_data[$ref_data]["FILE_YEAR"].=$row["FILE_YEAR"].', ';
			$export_lc_sc_data[$ref_data]["INTERNAL_FILE_NO"].=$row["INTERNAL_FILE_NO"].', ';
		}
		//echo "<pre>";print_r($export_lc_sc_data);die;
		unset($export_lc_sc_result);
		
		$sql_pi="SELECT id as PI_ID, supplier_id as SUPPLIER_ID, pi_number as PI_NUMBER, pi_date as PI_DATE, item_category_id as ITEM_CATEGORY_ID from com_pi_master_details where status_active=1 and is_deleted=0 and importer_id in($cbo_company)";		
		//echo $sql_pi;die;
		$sql_pi_result=sql_select($sql_pi);
		$pi_data=array();
		foreach($sql_pi_result as $row)
		{
			$ref_data=$row["PI_ID"];
			$pi_data[$ref_data]["PI_ID"]=$row["PI_ID"];
			$pi_data[$ref_data]["SUPPLIER_ID"]=$row["SUPPLIER_ID"];
			$pi_data[$ref_data]["ITEM_CATEGORY_ID"]=$row["ITEM_CATEGORY_ID"];
			$pi_data[$ref_data]["PI_NUMBER"]=$row["PI_NUMBER"];
			$pi_data[$ref_data]["PI_DATE"]=$row["PI_DATE"];
		}
		unset($sql_pi_result);
		
		//$sql_paid = "select a.id, b.accepted_ammount, b.payment_date 
//		from com_import_invoice_mst a, com_import_payment b 
//		where a.id=b.invoice_id and b.status_active=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.retire_source<>30
//		union all
//		select a.id, b.accepted_ammount, b.payment_date 
//		from com_import_invoice_mst a, COM_IMPORT_PAYMENT_COM b 
//		where a.id=b.invoice_id and b.status_active=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.retire_source=30";
		
		$sql_paid="select a.ID, c.ID as PAY_ID, b.IMPORT_INVOICE_ID, c.ACCEPTED_AMMOUNT 
		from com_btb_lc_master_details a, com_import_invoice_dtls b, COM_IMPORT_PAYMENT_com c 
		where a.id=b.BTB_LC_ID and b.IMPORT_INVOICE_ID=c.INVOICE_ID and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.PAYTERM_ID=1
		union all 
		select a.ID, c.ID as PAY_ID, b.IMPORT_INVOICE_ID, c.ACCEPTED_AMMOUNT 
		from com_btb_lc_master_details a, com_import_invoice_dtls b, COM_IMPORT_PAYMENT_com c 
		where a.id=b.BTB_LC_ID and b.IMPORT_INVOICE_ID=c.INVOICE_ID and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.PAYTERM_ID=2";
		//echo $sql_paid;
		$sql_paid_result = sql_select($sql_paid);
		$paid_ammount=array();
		$paid_date_info=array();
		foreach( $sql_paid_result as $row)
		{
			if($pay_check[$row[csf("IMPORT_INVOICE_ID")]]=="")
			{
				$pay_check[$row[csf("IMPORT_INVOICE_ID")]]=$row[csf("IMPORT_INVOICE_ID")];
				$paid_ammount[$row[csf("IMPORT_INVOICE_ID")]]+=$row[csf("accepted_ammount")];
				$paid_date_info[$row[csf("IMPORT_INVOICE_ID")]]=$row[csf("payment_date")];
			}
		}
		
		//echo "<pre>";print_r($paid_ammount);die;
		
		if ($cbo_company=="") $company_id =""; else $company_id =" and a.importer_id in($cbo_company) ";
		if ($cbo_issue==0) $issue_banking =""; else $issue_banking =" and a.issuing_bank_id=$cbo_issue ";
		$category_entry_form_cond ="";
		if ($item_category_id>0)
		{
			$category_entry_form_cond =" and a.pi_entry_form=".$category_wise_entry_form[$item_category_id];
		}
		if ($lc_type_id==0) $lc_tpe_id =""; else $lc_tpe_id =" and a.lc_type_id=$lc_type_id ";
		if ($cbo_bonded==0) $bonded_warehouse =""; else $bonded_warehouse =" and a.bonded_warehouse=$cbo_bonded ";

		$lc_no_cond="";
		if($txt_lc_no!="") $lc_no_cond=" and a.lc_number = '$txt_lc_no'";

			
		$lc_item_category_sql=sql_select("SELECT c.item_category_id, a.id, c.id as pi_dtls_id, c.quantity, d.goods_rcv_status, d.id as pi_id, c.work_order_id
		from  com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c, com_pi_master_details d 
		where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and c.pi_id=d.id and b.pi_id=d.id and a.status_active=1 and b.status_active=1 and c.status_active=1 $company_id  $issue_banking  $category_entry_form_cond $lc_tpe_id $bonded_warehouse $lc_date $lc_no_cond");

		$pi_lc_data=array();
		$itemCategoryID="";
		foreach($lc_item_category_sql as $row)
		{
			$tot_lc_qty_arr[$row[csf("id")]]+=$row[csf("quantity")];
			if($row[csf("goods_rcv_status")]==1)
			{
				if($wo_id_check[$row[csf("id")]][$row[csf("work_order_id")]]=="")
				{
					$wo_id_check[$row[csf("id")]][$row[csf("work_order_id")]]=$row[csf("work_order_id")];
					$pi_lc_data[$row[csf("id")]]['pi_wo_id'].=$row[csf("work_order_id")]."_".$row[csf("goods_rcv_status")].",";
				}
			}
			else
			{
				if($pi_id_check[$row[csf("id")]][$row[csf("pi_id")]]=="")
				{
					$pi_id_check[$row[csf("id")]][$row[csf("pi_id")]]=$row[csf("pi_id")];
					$pi_lc_data[$row[csf("id")]]['pi_wo_id'].=$row[csf("pi_id")]."_".$row[csf("goods_rcv_status")].",";
				}
				
			}
			$pi_lc_data[$row[csf("id")]]['item_category_id']=$row[csf("item_category_id")];
			if($item_cat_check[$row[csf("item_category_id")]]=="")
			{
				$item_cat_check[$row[csf("item_category_id")]]=$row[csf("item_category_id")];
				$itemCategoryID.=$row[csf("item_category_id")].",";
			}
		}
		$rcv_sql="select a.mst_id, a.receive_basis, a.pi_wo_batch_no, a.cons_quantity as cons_quantity, a.cons_amount as cons_amount, a.order_amount as order_amount, b.receive_purpose 
		from inv_transaction a, inv_receive_master b
		where a.mst_id=b.id and a.transaction_type = 1 and b.receive_purpose<>2 and a.status_active = 1 and a.company_id = $cbo_company_id and a.item_category in (".chop($itemCategoryID,',').")";
		//echo $rcv_sql;die;  and b.receive_purpose not in(2,43) and a.item_category not in (8, 10, 11, 15, 21) 
		//
		$rcv_result=sql_select($rcv_sql);
		$tot_rec_qty_arr=array();$rcv_wise_pi_wo=array();
		foreach($rcv_result as $row)
		{
			$tot_rec_qty_arr[$row[csf("pi_wo_batch_no")]][$row[csf("receive_basis")]]["cons_quantity"] +=$row[csf("cons_quantity")];
			$tot_rec_qty_arr[$row[csf("pi_wo_batch_no")]][$row[csf("receive_basis")]]["cons_amount"] +=$row[csf("cons_amount")];
			$tot_rec_qty_arr[$row[csf("pi_wo_batch_no")]][$row[csf("receive_basis")]]["order_amount"] +=$row[csf("order_amount")];
			$rcv_wise_pi_wo[$row[csf("mst_id")]]=$row[csf("pi_wo_batch_no")]."_".$row[csf("receive_basis")];
		}
		$invoice_payment_atsite=return_library_array( "SELECT invoice_id, sum(accepted_ammount) as accepted_ammount from com_import_payment_com where status_active=1 and is_deleted=0 group by invoice_id",'invoice_id','accepted_ammount');
		ob_start();
		$div_width="2800";
		$tbl_width="2780";
		
		?>
		<div id="scroll_body" align="center" style="height:auto; width:<? echo $div_width; ?>px; margin:0 auto; padding:0;">
			<table width="<? echo $div_width;?>">
				<?
				$company_library=sql_select("select id, company_name, plot_no, level_no,road_no,city from lib_company where id=".$cbo_company_id."");
				foreach( $company_library as $row)
				{
					$company_name.=$row[csf('company_name')].", ";
				}
				?>
				<tr>
					<td colspan="28" align="center" style="font-size:22px"><center><strong><? echo rtrim($company_name,", ");?></strong></center></td>
				</tr>
				<tr>
					<td colspan="28" align="center" style="font-size:18px"><center><strong><u><? echo $report_title; ?></u></strong></center></td>
				</tr>
			</table>
			<table cellspacing="0" width="<? echo $tbl_width;?>"  border="1" rules="all" class="rpt_table" align="left">
			   <thead>
               		<tr>
                    	<th width="30">SL</th>
                        <th width="60">File Year</th>
                        <th width="60">File No</th>
                        <th width="120">SC/LC No</th>
                        <th width="120">LC Number</th>
                        <th width="70">LC Date</th>
                        <th width="90">LC Value</th>
                        <th width="90">LC Quantity</th>
                        <th width="90">Received Quantity</th>
                        <th width="90">Total LC Balance quantity</th>
                        <th width="70">Item</th>
                        <th width="100">Bank Name</th>
                        <th width="100">Supplier Name</th>
                        <th width="70">SUPPLY SOURCE</th>
                        <th width="60">PI Details</th>
                        <th width="60">Tenor</th>
                        <th width="80">Documets Recievedate From Supplier</th>
                        <th width="100">INVOICE NO.</th>
                        <th width="60">INVOICE DATE</th>
                        <th width="90">BILL VALUE (Invoice)</th>
                        <th width="70">Company Acceptance Date</th>
                        <th width="70">Bank Acceptance Date</th>
                        <th width="80">Bank Referance No.</th>
                        <th width="70">Negotiation Date</th>
                        <th width="60">Maturity Date</th>
                        <th width="60">Paid Date</th>
                        <th width="90">Bill Paid Value</th>
                        <th width="90">Bill Outstanding Value</th>
                        <th width="90">LC Balance</th>
                        <th width="60">Actual LC Tenor</th>
                        <th width="100">BL NO</th>
                        <th width="60">BL Date</th>
                        <th width="100">Bill of Entry No.</th>
                        <th width="60">Bill of Entry Date</th>
                        <th >Remarks</th>
                    </tr>
			   </thead>
			</table>
			<div style="width:<? echo $div_width;?>px; overflow-y: scroll; overflow-x:hidden; max-height:300px;" id="scroll_body2"> 
				<table cellspacing="0" width="<? echo $tbl_width;?>"  border="1" rules="all" class="rpt_table" id="tbl_marginlc_list" style="float: left;" >
				<?
				$i=1;
				// print_r($main_sql_result);die;
				
				$total_lc_value=$total_lc_qnty=$total_lc_balance_qnty=$total_invoice_value=$total_bill_paid_value=$total_bill_outstanding_value=$total_lc_balance_value=0;
				$row_chk=$row_chk2=array();
				foreach($main_sql_result as $row)
				{           
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					$deferd_liability=0;
					$tot_rec_qty=0;
					// if($payment_data[$row["INV_ID"]]["ACCEPTED_AMMOUNT"]=="") $deferd_liability=$row["ACCEPTANCE_VALUE"];

					$lc_pi_wo_ids=explode(",",chop($pi_lc_data[$row['BTB_ID']]['pi_wo_id'],","));
					foreach($lc_pi_wo_ids as $pi_wo_ids)
					{
						$pi_wo_ids_ref=explode("_",$pi_wo_ids);
						if($pi_wo_ids_ref[1]==1)
						{  
							$tot_rec_qty +=$tot_rec_qty_arr[$pi_wo_ids_ref[0]][2]["cons_quantity"] - $tot_return_qty_arr[$pi_wo_ids_ref[0]][2]["cons_quantity"];
						}
						else
						{
							$tot_rec_qty +=$tot_rec_qty_arr[$pi_wo_ids_ref[0]][1]["cons_quantity"] - $tot_return_qty_arr[$pi_wo_ids_ref[0]][1]["cons_quantity"];
						}
					}
					
					$all_po_wo_ids=chop($all_pi_ids,",")."__".chop($all_wo_ids,",");

					$pi_id_arr=array_unique(explode(",",$row["PI_ID"]));
					$item_cat="";
					foreach($pi_id_arr as $pi_id)
					{
						if($cat_check[$row["INV_ID"]][$pi_data[$pi_id]["ITEM_CATEGORY_ID"]]=="")
						{
							$cat_check[$row["INV_ID"]][$pi_data[$pi_id]["ITEM_CATEGORY_ID"]]=$pi_data[$pi_id]["ITEM_CATEGORY_ID"];
							$item_cat.=$item_category[$pi_data[$pi_id]["ITEM_CATEGORY_ID"]].",";
						}
					}
					$item_cat=chop($item_cat,",");
					$paid_date='';$paid_value_amount=0;
					$paid_date=change_date_format($paid_date_info[$row["INV_ID"]]);
					$paid_value_amount=$paid_ammount[$row["INV_ID"]];
					$bill_discount_value=$row["ACCEPTANCE_VALUE"]-$paid_ammount[$row["INV_ID"]];
					?>
					<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td align="center" width="30"><? echo $i; ?></td>
						<?
							if(!in_array($row['BTB_ID'],$row_chk))
							{ $row_chk[$row['BTB_ID']]=$row['BTB_ID'];
						?>
						<td rowspan="<?=$row_count[$row['BTB_ID']];?>" valign="middle" width="60"><p><? echo implode(", ",array_unique(explode(", ",chop($export_lc_sc_data[$row['BTB_ID']]["FILE_YEAR"],', ')))); ?></p></td>
						<td rowspan="<?=$row_count[$row['BTB_ID']];?>" valign="middle" width="60"><p><? echo implode(", ",array_unique(explode(", ",chop($export_lc_sc_data[$row['BTB_ID']]["INTERNAL_FILE_NO"],', ')))); ?></p></td>
						<td rowspan="<?=$row_count[$row['BTB_ID']];?>" valign="middle" width="120"><p><? echo chop($export_lc_sc_data[$row['BTB_ID']]["LC_SC_NO"],', '); ?>&nbsp;</p></td>
						<td rowspan="<?=$row_count[$row['BTB_ID']];?>" valign="middle" width="120"><p><? echo $row["LC_NUMBER"]; ?>&nbsp;</p></td>
						<td rowspan="<?=$row_count[$row['BTB_ID']];?>" valign="middle" align="center" width="70"><p>&nbsp;<? if($row["LC_DATE"]!="" && $row["LC_DATE"]!="0000-00-00") echo change_date_format($row["LC_DATE"]); ?></p></td>
						<td rowspan="<?=$row_count[$row['BTB_ID']];?>" valign="middle" align="right" width="90"><? echo number_format($row["LC_VALUE"],2); ?></td>
						<td rowspan="<?=$row_count[$row['BTB_ID']];?>" valign="middle" align="right" width="90"><? echo number_format($tot_lc_qty_arr[$row["BTB_ID"]],2); ?></td>
						<td rowspan="<?=$row_count[$row['BTB_ID']];?>" valign="middle" align="right" width="90"><? echo number_format($tot_rec_qty,2); ?></td>
						<td rowspan="<?=$row_count[$row['BTB_ID']];?>" valign="middle" align="right" width="90"><? echo number_format($tot_lc_qty_arr[$row["BTB_ID"]]-$tot_rec_qty,2); ?></td>
						<td rowspan="<?=$row_count[$row['BTB_ID']];?>" valign="middle" width="70"><p><? echo $item_cat; ?></p></td>
						<td rowspan="<?=$row_count[$row['BTB_ID']];?>" valign="middle" width="100"><p><? echo $issueBankrArr[$row["ISSUING_BANK_ID"]]; ?></p></td>
						<td rowspan="<?=$row_count[$row['BTB_ID']];?>" valign="middle" width="100"><p><? if($exportPiSuppArr[$row["BTB_ID"]]==1) echo $companyArr[$row["SUPPLIER_ID"]]; else echo $supplierArr[$row["SUPPLIER_ID"]];?></p></td>
						<td rowspan="<?=$row_count[$row['BTB_ID']];?>" valign="middle" align="center" width="70"><p><? echo $supply_source[(int)$row['LC_CATEGORY']]; ?></p></td>
						<td rowspan="<?=$row_count[$row['BTB_ID']];?>" valign="middle" width="60">
							<?
							$pi_id=$row['PI_ID']; 
							$company_name=$row['IMPORTER_ID'];
							$lc_number=$row['LC_NUMBER'];
							$ship_date=$row['LAST_SHIPMENT_DATE'];
							$supplier_id=$row['SUPPLIER_ID'];
							$lc_date=$row['LC_DATE'];
							$exp_date=$row['LC_EXPIRY_DATE'];
							$payterm=$row['PAYTERM_ID'];
							echo "<a href='#report_details' style='color:#000' onclick= \"openmypage('$company_name','$lc_number','$ship_date','$supplier_id','$lc_date','$exp_date','$payterm','$pi_id','pi_details','PI Details');\">"."View"."</a>"; 
							?>	
						</td>
						<td rowspan="<?=$row_count[$row['BTB_ID']];?>" valign="middle" width="60" align="center"><p><? echo $row["TENOR"]; ?></p></td>
						<?
							$total_lc_value+=$row["LC_VALUE"];
							$total_lc_qnty+=$tot_lc_qty_arr[$row["BTB_ID"]];
							$total_lc_balance_qnty+=$tot_lc_qty_arr[$row["BTB_ID"]]-$tot_rec_qty;
							}
						?>
						<td width="80" align="center"><p>&nbsp;<? if($row["DOC_RCV_DATE"]!="" && $row["DOC_RCV_DATE"]!="0000-00-00") echo change_date_format($row["DOC_RCV_DATE"]); ?></p></td>
						<td width="100" ><p><? echo $row["INVOICE_NO"]; ?>&nbsp;</p></td>
						<td width="60" align="center"><p>&nbsp;<? echo change_date_format($row["INVOICE_DATE"]); ?></p></td>
						<td align="right" width="90"><? echo number_format($row["ACCEPTANCE_VALUE"],2);$total_invoice_value+=$row["ACCEPTANCE_VALUE"]; ?></td>
						<td align="center" width="70"><p>&nbsp;<? if($row["COMPANY_ACC_DATE"]!="" && $row["COMPANY_ACC_DATE"]!="0000-00-00") echo change_date_format($row["COMPANY_ACC_DATE"]); ?></p></td>
						<td align="center" width="70"><p>&nbsp;<? if($row["BANK_ACC_DATE"]!="" && $row["BANK_ACC_DATE"]!="0000-00-00") echo change_date_format($row["BANK_ACC_DATE"]); ?></p></td>
						<td width="80"><p><? echo $row["BANK_REF"]; ?>&nbsp;</p></td>
						<td align="center" width="70"><p>&nbsp;<? if($row["NAGOTIATE_DATE"]!="" && $row["NAGOTIATE_DATE"]!="0000-00-00") echo change_date_format($row["NAGOTIATE_DATE"]); ?></p></td>
						<td align="center" width="60"><p>&nbsp;<? if($row["MATURITY_DATE"]!="" && $row["MATURITY_DATE"]!="0000-00-00") echo change_date_format($row["MATURITY_DATE"]); ?></p></td>					
						<td align="center" width="60"><p>&nbsp;<? echo change_date_format($paid_date); ?></p></td>
						<td align="right" width="90"><p><? echo number_format($paid_value_amount,2);$total_bill_paid_value+=$paid_value_amount; ?></p></td>
						<td align="right" width="90"><p><? echo number_format($bill_discount_value,2);$total_bill_outstanding_value+=$bill_discount_value; ?></p></td>
						<?
							if(!in_array($row['BTB_ID'],$row_chk2))
							{ $row_chk2[$row['BTB_ID']]=$row['BTB_ID'];
						?>
						<td rowspan="<?=$row_count[$row['BTB_ID']];?>" valign="middle" align="right" width="90"><? echo number_format($row["LC_VALUE"]-$total_inv_val[$row['BTB_ID']],2); ?></td>
						<?
							$total_lc_balance_value+=$row["LC_VALUE"]-$total_inv_val[$row['BTB_ID']];
							}
						?>
						<td align="right" width="60"><?if($row["NAGOTIATE_DATE"]=='') echo 0; else echo datediff("d", date('Y-m-d',strtotime($row["NAGOTIATE_DATE"])), date('Y-m-d',strtotime($row["MATURITY_DATE"])))-1; ?></td>
                        <td align="right" width="100" style="word-break:break-all"><?  echo $row["BILL_NO"];  ?></td>						
                        <td align="right" width="60">&nbsp;<?  echo change_date_format($row["BILL_DATE"]);  ?></td>						
                        <td align="right" width="100"><?  echo $row["BILL_OF_ENTRY_NO"];  ?></td>						
                        <td align="right" width="60">&nbsp;<?  echo change_date_format($row["BILL_OF_ENTRY_DATE"]);  ?></td>						
                        <td ><p></p></td>
					</tr>
					<?
					$i++;
				}
				?>
				</table>
				<table cellspacing="0" width="<? echo $tbl_width;?>"  border="1" rules="all" class="rpt_table" align="left" >
					<tfoot>
						<tr>
							<th width="30">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>

							<th width="120">&nbsp;</th>
							<th width="120">&nbsp;</th>
							<th width="70"><strong>TOTAL</strong> &nbsp;</th>
							<th width="90"><?=number_format($total_lc_value,2);?>&nbsp;</th>
							<th width="90"><?=number_format($total_lc_qnty,2);?>&nbsp;</th>
							<th width="90">&nbsp;</th>
							<th width="90"><?=number_format($total_lc_balance_qnty,2);?>&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="90"><?=number_format($total_invoice_value,2);?>&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="90"><?=number_format($total_bill_paid_value,2);?>&nbsp;</th>
							<th width="90"><?=number_format($total_bill_outstanding_value,2);?>&nbsp;</th>
							<th width="90"><?=number_format($total_lc_balance_value,2);?>&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th >&nbsp;</th>
						</tr>
					</tfoot>         
				</table>
			</div>
		</div>
		<?
	}

	if($report_type==6) //WVN
	{
		$item_category_id=str_replace("'","",$cbo_item_category_id);
		$companyArr = return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0","id","company_short_name"); 
		$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
		$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name");
		$CountryArr = return_library_array("select id,country_name from lib_country where status_active=1 and is_deleted=0","id","country_name");
		$issueBankrArr = return_library_array("select id,bank_name from lib_bank ","id","bank_name");
		$user_name = return_library_array("select id,user_name from user_passwd ","id","user_name");
		//$hscodeArr = return_library_array("select id, hs_code from com_pi_master_details ","id","hs_code"); 
		//$lc_num_arr = return_library_array("select id,export_lc_no from com_export_lc ","id","export_lc_no"); 
		//$sc_num_arr = return_library_array("select id,contract_no from com_sales_contract ","id","contract_no"); 
		
		$cbo_company=str_replace("'","",$cbo_company_id);
		$cbo_issue=str_replace("'","",$cbo_issue_banking);
		$lc_type_id=str_replace("'","",$cbo_lc_type_id);
		$txt_lc_no=str_replace("'","",$txt_lc_no);
		$cbo_payterm_id=str_replace("'","",$cbo_payterm_id);
		
		$cbo_bonded=str_replace("'","",$cbo_bonded_warehouse);
		$from_date=str_replace("'","",$txt_date_from);
		$to_date=str_replace("'","",$txt_date_to);
		$cbo_supplier=str_replace("'","",$cbo_supplier);
		$txt_search_common=str_replace("'","",$txt_search_common);
		$cbo_status=str_replace("'","",$cbo_status);
		$cbo_reference_close=str_replace("'","",$cbo_reference_close);

		$reference_close_cond='';
		if ($cbo_reference_close == 1)
			$reference_close_cond = " and a.ref_closing_status=1";
		else if ($cbo_reference_close == 2) 
			$reference_close_cond = " and a.ref_closing_status=0";
		else $reference_close_cond = " and a.ref_closing_status in(0,1)";
		$reference_close=array(1=>"Yes",2=>"No");
		$sql_cond="";
		$supply_so=str_pad(str_replace("'","",$cbo_supply_source), 2, "0", STR_PAD_LEFT);
		if (str_replace("'","",$cbo_supply_source)>0) $sql_cond .=" and a.lc_category='".$supply_so."'";
		if ($cbo_company!="") $sql_cond .=" and a.importer_id in($cbo_company) ";
		if ($cbo_issue>0) $sql_cond .=" and a.issuing_bank_id=$cbo_issue ";
		if ($item_category_id>0) $sql_cond .=" and a.pi_entry_form=".$category_wise_entry_form[$item_category_id];
		
		if ($lc_type_id>0) $sql_cond .=" and a.lc_type_id=$lc_type_id ";
		if ($cbo_bonded>0) $sql_cond .=" and a.bonded_warehouse=$cbo_bonded ";
		if ($cbo_payterm_id>0) $sql_cond .=" and a.payterm_id=$cbo_payterm_id ";
		if ($cbo_supplier>0) $sql_cond .=" and a.supplier_id=$cbo_supplier ";
		if($txt_lc_no!="") $sql_cond .=" and a.lc_number = '$txt_lc_no'";
		
		//print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));
		$cbo_based_on=str_replace("'","",$cbo_based_on);
		if($db_type==2)
		{
			if($cbo_based_on==1)
			{
				if( $from_date=="" && $to_date=="" ) $lc_date=""; else $lc_date= " and a.lc_date between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
			}
			else
			{
				if( $from_date=="" && $to_date=="" ) $lc_date=""; else $lc_date= " and a.insert_date between '".date("j-M-Y",strtotime($from_date)) ." 12:00:01 AM"."' and '".date("j-M-Y",strtotime($to_date)). " 11:59:59 PM"."'";
			}
			$select_insert_date=" to_char(a.insert_date,'DD-MM-YYYY') as  insert_date";
			
		}
		else if($db_type==0)
		{
			if($cbo_based_on==1)
			{
				if( $from_date=='' && $to_date=='' ) $lc_date=""; else $lc_date= " and a.lc_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
			}
			else
			{
				if( $from_date=='' && $to_date=='' ) $lc_date=""; else $lc_date= " and a.insert_date between '".change_date_format($from_date,'yyyy-mm-dd'). " 00:00:01"."' and '".change_date_format($to_date,'yyyy-mm-dd'). " 23:59:59"."'";
			}
			
			$select_insert_date=" DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date";
		}
		
		
		if($db_type==0)
		{
			$select_lc_sc_id=" group_concat(b.lc_sc_id) as LC_SC_ID";
		}
		else
		{
			$select_lc_sc_id=" listagg(cast(b.lc_sc_id as varchar(4000)),',') within group(order by b.lc_sc_id) as LC_SC_ID";
		}
		if($txt_search_common!="") 
		{
			if ($cbo_search_by==1) 
			{
				$sql = "SELECT a.id, b.is_lc_sc, b.lc_sc_id  
				from com_btb_lc_master_details a, com_btb_export_lc_attachment b, com_export_lc c
				where a.id=b.import_mst_id and b.lc_sc_id=c.id and b.is_lc_sc=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $issue_banking $category_entry_form_cond $lc_tpe_id $bonded_warehouse $lc_date  $supply_source_cond $lc_no_cond $txt_lc_no_cond order by a.lc_date ";
			}
			else
			{
				$sql = "SELECT a.id, b.is_lc_sc, b.lc_sc_id
				from com_btb_lc_master_details a, com_btb_export_lc_attachment b, com_sales_contract c
				where a.id=b.import_mst_id and b.lc_sc_id=c.id and b.is_lc_sc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $issue_banking $category_entry_form_cond $lc_tpe_id $bonded_warehouse $lc_date  $supply_source_cond $lc_no_cond $txt_sc_no_cond  order by a.lc_date ";
			}
			$lc_sc_sql=sql_select($sql);
			foreach($lc_sc_sql as $row)
			{
				$ls_sc_id.=$row[csf('id')].',';
			}
				
	
			$ls_sc_id_cond = implode(",",array_unique(explode(",",chop($ls_sc_id,','))));
			if($ls_sc_id_cond!="")
			{
				$btb_lc_sc_no_cond=" and a.id in($ls_sc_id_cond)";
			}
			else
			{
				echo "<br><strong><span style='color:red;'>Data Not Found</span></strong>";die;
			}
			
			$main_sql="SELECT a.id as BTB_ID, a.btb_system_id as BTB_SYSTEM_ID, a.lc_number as LC_NUMBER, a.application_date as APPLICATION_DATE, a.importer_id as IMPORTER_ID, a.pi_id as PI_ID, a.lc_type_id as LC_TYPE_ID, a.lc_date as LC_DATE, a.maturity_from_id as MATURITY_FROM_ID, a.supplier_id as SUPPLIER_ID, a.last_shipment_date as LAST_SHIPMENT_DATE, a.lc_expiry_date as LC_EXPIRY_DATE, a.tolerance as TOLERANCE, a.payterm_id as PAYTERM_ID, a.tenor as TENOR, a.issuing_bank_id as ISSUING_BANK_ID, a.lc_value as LC_VALUE, a.uom_id as UOM_ID, a.lc_category as LC_CATEGORY, a.cover_note_no as COVER_NOTE_NO, a.cover_note_date as COVER_NOTE_DATE, a.advising_bank as ADVISING_BANK, a.origin as ORIGIN, a.inco_term_id as INCO_TERM_ID, a.port_of_loading as PORT_OF_LOADING, a.port_of_discharge as PORT_OF_DISCHARGE, a.inserted_by as INSERTED_BY, a.insert_date as INSERT_DATE, b.is_lc_sc as IS_LC_SC, c.id as AMENDMENT_ID, c.pi_id as PI_ID_AMENDMENT, c.amendment_no as AMENDMENT_NO, c.amendment_date as AMENDMENT_DATE, c.value_change_by as VALUE_CHANGE_BY, c.amendment_value as AMENDMENT_VALUE, c.addendum_no as ADDENDUM_NO, c.addendum_date as ADDENDUM_DATE, c.inserted_by as PI_INSERTED_BY, c.is_original as IS_ORIGINAL, $select_lc_sc_id,c.btb_lc_value
			from com_btb_lc_master_details a
			left join com_btb_export_lc_attachment b on a.id=b.import_mst_id and b.status_active=1 and b.is_deleted=0
			left join com_btb_lc_amendment c on a.id=c.btb_id and c.status_active=1 and c.is_deleted=0 
			where a.status_active=1 and a.is_deleted=0 $lc_date $sql_cond $btb_lc_sc_no_cond
			group by a.id, a.btb_system_id, a.lc_number, a.application_date, a.importer_id, a.pi_id, a.lc_type_id, a.lc_date, a.maturity_from_id, a.last_shipment_date, a.lc_expiry_date, a.tolerance, a.supplier_id, a.payterm_id, a.tenor, a.issuing_bank_id, a.lc_value, a.uom_id, a.lc_category, a.cover_note_no, a.cover_note_date, a.advising_bank, a.origin, a.inco_term_id, a.port_of_loading, a.port_of_discharge, a.inserted_by , a.insert_date, b.is_lc_sc, c.id , c.pi_id, c.amendment_no, c.amendment_date, c.value_change_by, c.amendment_value, c.addendum_no, c.addendum_date, c.inserted_by, c.is_original,c.btb_lc_value
			order by a.id,c.id";
		}
		else
		{
			$main_sql="SELECT a.id as BTB_ID, a.btb_system_id as BTB_SYSTEM_ID, a.lc_number as LC_NUMBER, a.application_date as APPLICATION_DATE, a.importer_id as IMPORTER_ID, a.pi_id as PI_ID, a.lc_type_id as LC_TYPE_ID, a.lc_date as LC_DATE, a.maturity_from_id as MATURITY_FROM_ID, a.supplier_id as SUPPLIER_ID, a.last_shipment_date as LAST_SHIPMENT_DATE, a.lc_expiry_date as LC_EXPIRY_DATE, a.tolerance as TOLERANCE, a.payterm_id as PAYTERM_ID, a.tenor as TENOR, a.issuing_bank_id as ISSUING_BANK_ID, a.lc_value as LC_VALUE, a.uom_id as UOM_ID, a.lc_category as LC_CATEGORY, a.cover_note_no as COVER_NOTE_NO, a.cover_note_date as COVER_NOTE_DATE, a.advising_bank as ADVISING_BANK, a.origin as ORIGIN, a.inco_term_id as INCO_TERM_ID, a.port_of_loading as PORT_OF_LOADING, a.port_of_discharge as PORT_OF_DISCHARGE, a.inserted_by as INSERTED_BY, a.insert_date as INSERT_DATE, b.is_lc_sc as IS_LC_SC, c.id as AMENDMENT_ID, c.pi_id as PI_ID_AMENDMENT, c.amendment_no as AMENDMENT_NO, c.amendment_date as AMENDMENT_DATE, c.value_change_by as VALUE_CHANGE_BY, c.amendment_value as AMENDMENT_VALUE, c.addendum_no as ADDENDUM_NO, c.addendum_date as ADDENDUM_DATE, c.inserted_by as PI_INSERTED_BY, c.is_original as IS_ORIGINAL, $select_lc_sc_id,c.btb_lc_value
			from com_btb_lc_master_details a
			left join com_btb_export_lc_attachment b on a.id=b.import_mst_id and b.status_active=1 and b.is_deleted=0
			left join com_btb_lc_amendment c on a.id=c.btb_id and c.status_active=1 and c.is_deleted=0 
			where a.status_active=1 and a.is_deleted=0 $lc_date $sql_cond
			group by a.id, a.btb_system_id, a.lc_number, a.application_date, a.importer_id, a.pi_id, a.lc_type_id, a.lc_date, a.maturity_from_id, a.last_shipment_date, a.lc_expiry_date, a.tolerance, a.supplier_id, a.payterm_id, a.tenor, a.issuing_bank_id, a.lc_value, a.uom_id, a.lc_category, a.cover_note_no, a.cover_note_date, a.advising_bank, a.origin, a.inco_term_id, a.port_of_loading, a.port_of_discharge, a.inserted_by , a.insert_date, b.is_lc_sc, c.id , c.pi_id, c.amendment_no, c.amendment_date, c.value_change_by, c.amendment_value, c.addendum_no, c.addendum_date, c.inserted_by, c.is_original,c.btb_lc_value
			order by a.id,c.id";
		}
		// echo $main_sql;die;
		$main_sql_result=sql_select($main_sql);
		$lc_value_data=array();
		foreach($main_sql_result as $row)
		{
			$lc_value_data[$row['BTB_ID']]=$row['LC_VALUE'];
		}
		foreach($main_sql_result as $row)
		{
			if($row['VALUE_CHANGE_BY']==1)
			{
				$lc_value_data[$row['BTB_ID']]-=$row['AMENDMENT_VALUE'];
			}
			if($row['VALUE_CHANGE_BY']==2)
			{
				$lc_value_data[$row['BTB_ID']]+=$row['AMENDMENT_VALUE'];
			}
		}
	
		$export_lc_sc="SELECT id as LC_SC_ID, export_lc_no as LC_SC_NO, lc_date as LC_SC_DATE, buyer_name as BUYER_NAME, 0 as TYPE 
		from com_export_lc where beneficiary_name in($cbo_company)
		union all 
		select id as LC_SC_ID, contract_no as LC_SC_NO, contract_date as LC_SC_DATE, buyer_name as BUYER_NAME, 1 as TYPE 
		from com_sales_contract where beneficiary_name in($cbo_company)";
		//echo $export_lc_sc;die;
		$export_lc_sc_result=sql_select($export_lc_sc);
		$export_lc_sc_data=array();
		foreach($export_lc_sc_result as $row)
		{
			$ref_data=$row["LC_SC_ID"]."__".$row["TYPE"];
			$export_lc_sc_data[$ref_data]["LC_SC_ID"]=$row["LC_SC_ID"];
			$export_lc_sc_data[$ref_data]["LC_SC_NO"]=$row["LC_SC_NO"];
			$export_lc_sc_data[$ref_data]["LC_SC_DATE"]=$row["LC_SC_DATE"];
			$export_lc_sc_data[$ref_data]["BUYER_NAME"]=$row["BUYER_NAME"];
		}
		//echo "<pre>";print_r($export_lc_sc_data);die;
		unset($export_lc_sc_result);
		
		$sql_pi="SELECT id as PI_ID, supplier_id as SUPPLIER_ID, pi_number as PI_NUMBER, pi_date as PI_DATE, item_category_id as ITEM_CATEGORY_ID, hs_code as HS_CODE, inserted_by as INSERTED_BY  from com_pi_master_details where status_active=1 and is_deleted=0 and importer_id in($cbo_company)";		
		//echo $sql_pi;die;
		$sql_pi_result=sql_select($sql_pi);
		$pi_data=array();
		foreach($sql_pi_result as $row)
		{
			$ref_data=$row["PI_ID"];
			$pi_data[$ref_data]["PI_ID"]=$row["PI_ID"];
			$pi_data[$ref_data]["SUPPLIER_ID"]=$row["SUPPLIER_ID"];
			$pi_data[$ref_data]["ITEM_CATEGORY_ID"]=$row["ITEM_CATEGORY_ID"];
			$pi_data[$ref_data]["PI_NUMBER"]=$row["PI_NUMBER"];
			$pi_data[$ref_data]["PI_DATE"]=$row["PI_DATE"];
			$pi_data[$ref_data]["HS_CODE"]=$row["HS_CODE"];
			$pi_data[$ref_data]["INSERTED_BY"]=$row["INSERTED_BY"];
		}
		unset($sql_pi_result);

		$data_file=sql_select("select image_location, master_tble_id from common_photo_library where form_name='proforma_invoice' and is_deleted=0 and file_type=2 union all select image_location, master_tble_id from common_photo_library where form_name='BTBMargin LC' and is_deleted=0 and file_type=2 union all select image_location, master_tble_id from common_photo_library where form_name='importdocumentacceptance_1' and is_deleted=0 and file_type=2");
		$file_arr=array();
		foreach($data_file as $row)
		{
			$file_arr[$row[csf('master_tble_id')]]['file']=$row[csf('image_location')];
		}
		unset($data_file);
		ob_start();
		$div_width="3000";
		$tbl_width="2980";
		
		?>
		<style>
			#tbl_marginlc_list tr td{
				word-break:break-all;
			}
		</style>
		<div id="scroll_body" align="center" style="height:auto; width:<? echo $div_width; ?>px; margin:0 auto; padding:0;">
			<table width="<? echo $div_width;?>">
				<?
				$company_library=sql_select("select id, company_name, plot_no, level_no,road_no,city from lib_company where id=".$cbo_company_id."");
				foreach( $company_library as $row)
				{
					$company_name.=$row[csf('company_name')].", ";
				}
				?>
				<tr>
					<td colspan="28" align="center" style="font-size:22px"><center><strong><? echo rtrim($company_name,", ");?></strong></center></td>
				</tr>
				<tr>
					<td colspan="28" align="center" style="font-size:18px"><center><strong><u><? echo $report_title; ?></u></strong></center></td>
				</tr>
			</table>
			<table cellspacing="0" width="<? echo $tbl_width;?>"  border="1" rules="all" class="rpt_table" align="left">
			   <thead>
               		<tr>
                    	<th width="30">SL NO.</th>
                        <th width="60">Company</th>
                        <th width="100">BTB LC NO.</th>
                        <th width="80">BTB L/C Date</th>
                        <th width="80">Amendment No</th>
                        <th width="70">Amendment Date</th>
                        <th width="80">Value Changed By</th>
                        <th width="80">Addendum No</th>
                        <th width="70">Addendum Date</th>
                        <th width="90">DEF L/C Amount (US$)</th>
                        <th width="90">EDF L/C Amount (US$)</th>
                        <th width="90">TT/FDD</th>
                        <th width="80">VALUE (US$)</th>
                        <th width="100">SUPPLIER'S NAME</th>
                        <th width="80">MOU/S. CONT. NO.</th>
                        <th width="100">BUYER NAME</th>
                        <th width="80">L/C Applicaion Sub Date</th>
                        <th width="100">PI NO.</th>
                        <th width="80">SHIPMENT DATE</th>
                        <th width="80">EXPIRY DATE</th>
                        <th width="80">ITEMS</th>
                        <th width="80">H.S CODE</th>
                        <th width="60">Tolerance %</th>
                        <th width="100">PAYMENT TERMS</th>
                        <th width="60">Cover Note No</th>
                        <th width="60">Cover Note Date</th>
                        <th width="90">ADVISING BANK</th>
                        <th width="90">COUNTRY OF ORIGIN</th>
                        <th width="70">INCO TERM</th>
                        <th width="100">PORT OF LOADING</th>
                        <th width="100">PORT OF DISCHARGE</th>
                        <th width="100">MERCHANDISER NAME</th>
                        <th width="100">Stystem ID</th>
                        <th width="60">Insert Date</th>
                        <th width="100">Insert User</th>
                        <th >View</th>
                    </tr>
			   </thead>
			</table>
			<div style="width:<? echo $div_width;?>px; overflow-y: scroll; overflow-x:hidden; max-height:300px;" id="scroll_body2"> 
				<table cellspacing="0" width="<? echo $tbl_width;?>"  border="1" rules="all" class="rpt_table" id="tbl_marginlc_list" style="float: left;" >
				<?
				$i=1;
				// print_r($main_sql_result);die;
				$cat_check=$pi_user_check=array();
				foreach($main_sql_result as $row)
				{            
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
				
					$all_po_wo_ids=chop($all_pi_ids,",")."__".chop($all_wo_ids,",");
					if($row['IS_ORIGINAL']!='')
					{
						$pi_id_arr=array_unique(explode(",",$row["PI_ID_AMENDMENT"]));
					}else{
						$pi_id_arr=array_unique(explode(",",$row["PI_ID"]));
					}

					$item_cat=$pi_number=$hs_code=$pi_user_name=$pi_id="";
					foreach($pi_id_arr as $pi_id)
					{
						if($row['IS_ORIGINAL']=='')
						{
							if($cat_check[$row['BTB_ID']][$pi_data[$pi_id]["ITEM_CATEGORY_ID"]]=="")
							{
								$cat_check[$row['BTB_ID']][$pi_data[$pi_id]["ITEM_CATEGORY_ID"]]=$pi_data[$pi_id]["ITEM_CATEGORY_ID"];
								$item_cat.=$item_category[$pi_data[$pi_id]["ITEM_CATEGORY_ID"]].",";
							}
							if($pi_user_check[$row['BTB_ID']][$pi_data[$pi_id]["INSERTED_BY"]]=="")
							{
								$pi_user_check[$row['BTB_ID']][$pi_data[$pi_id]["INSERTED_BY"]]=$pi_data[$pi_id]["INSERTED_BY"];
								$pi_user_name.=$user_name[$pi_data[$pi_id]["INSERTED_BY"]].", ";
							}
						}else{
							if($cat_check[$row['AMENDMENT_ID']][$pi_data[$pi_id]["ITEM_CATEGORY_ID"]]=="")
							{
								$cat_check[$row['AMENDMENT_ID']][$pi_data[$pi_id]["ITEM_CATEGORY_ID"]]=$pi_data[$pi_id]["ITEM_CATEGORY_ID"];
								$item_cat.=$item_category[$pi_data[$pi_id]["ITEM_CATEGORY_ID"]].",";
							}
							if($pi_user_check[$row['AMENDMENT_ID']][$pi_data[$pi_id]["INSERTED_BY"]]=="")
							{
								$pi_user_check[$row['AMENDMENT_ID']][$pi_data[$pi_id]["INSERTED_BY"]]=$pi_data[$pi_id]["INSERTED_BY"];
								$pi_user_name.=$user_name[$pi_data[$pi_id]["INSERTED_BY"]].", ";
							}
						}

						$pi_number.=$pi_data[$pi_id]["PI_NUMBER"].", ";
						$pi_id.=$pi_data[$pi_id]["PI_ID"].", ";
						$hs_code.=$pi_data[$pi_id]["HS_CODE"].", ";;
					}
					$item_cat=$item_category[$pi_data[$pi_id]["ITEM_CATEGORY_ID"]];
					$item_cat=chop($item_cat,",");
					$pi_id=chop($pi_id,",");

					$lc_sc_arr=explode(',',$row["LC_SC_ID"]);
					$lc_sc_no=$buyer_id=$buyer_name='';
					foreach($lc_sc_arr as $val)
					{
						$buyer_id.=$export_lc_sc_data[$val."__".$row["IS_LC_SC"]]["BUYER_NAME"].',';
						$lc_sc_no.=$export_lc_sc_data[$val."__".$row["IS_LC_SC"]]["LC_SC_NO"].', ';
					}
					$buyer_id=array_unique(explode(",",chop($buyer_id,',')));
					
					foreach($buyer_id as $val)
					{
						$buyer_name.=$buyerArr[$val].', ';
					}
					$lc_value='';
					if($row['IS_ORIGINAL']==1 || $row['IS_ORIGINAL']=='')
					{
						$lc_value=number_format($lc_value_data[$row["BTB_ID"]],2);
					}else{
						$lc_value=number_format($row["AMENDMENT_VALUE"],2);
					}
					?>
					<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td align="center" width="30"><? echo $i; ?></td>		
						<td width="60"><p><? echo $companyArr[$row["IMPORTER_ID"]]; ?></p></td>
						<td width="100"><p><? echo $row["LC_NUMBER"]; ?>&nbsp;</p></td>
						<td align="center" width="80"><p>&nbsp;<? echo change_date_format($row["LC_DATE"]);?></p></td>
						<td width="80"><p><? if($row["AMENDMENT_NO"]!=0){echo $row["AMENDMENT_NO"];} ?></p></td>
						<td align="center" width="70"><p>&nbsp;<? echo change_date_format($row["AMENDMENT_DATE"]); ?></p></td>
						<td align="center" width="80"><? if($row["AMENDMENT_NO"]!=0){echo $increase_decrease[$row["VALUE_CHANGE_BY"]];}?></td>
						<td width="80"><? echo $row["ADDENDUM_NO"];?></td>
						<td align="center" width="70">&nbsp;<? echo change_date_format($row["ADDENDUM_DATE"]); ?></td>
						<td align="right" width="90"><? if($row['PAYTERM_ID']==2){echo $lc_value;} ?></td>
						<td align="right" width="90"><p><? if($row['PAYTERM_ID']==1){echo $lc_value;} ?></p></td>
						<td align="right" width="90"><p><? if($row['PAYTERM_ID']==3){echo $lc_value;} ?></p></td>
						<td align="right" width="80"><p><? echo  $row["BTB_LC_VALUE"];?></p></td>
						<td align="center" width="100"><p><? if($exportPiSuppArr[$row["BTB_ID"]]==1) echo $companyArr[$row["SUPPLIER_ID"]]; else echo $supplierArr[$row["SUPPLIER_ID"]];?></p></td>
						<td width="80"><? echo chop($lc_sc_no,', '); ?>&nbsp;</td>
						<td width="100" align="center"><p><? echo chop($buyer_name,', '); ?></p></td>
						<td width="80" align="center"><p>&nbsp;<? echo change_date_format($row["APPLICATION_DATE"]);?></p></td>
						<td width="100" ><p><? echo chop($pi_number,', '); ?></p></td>
						<td width="80" align="center"><p>&nbsp;<? echo change_date_format($row["LAST_SHIPMENT_DATE"]); ?></p></td>
						<td align="center" width="80">&nbsp;<? echo change_date_format($row["LC_EXPIRY_DATE"]); ?></td>
						<td width="80"><p><? echo $item_cat; ?></p></td>
						<td width="80"><p><? echo rtrim($hs_code,', '); ?></p></td>
						<td align="right" width="60"><p><? echo $row["TOLERANCE"]; ?></p></td>
						<td align="center" width="100"><p><? echo $pay_term[$row["PAYTERM_ID"]]; ?></p></td>
						<td  width="60"><p><? echo $row["COVER_NOTE_NO"]; ?></p></td>					
						<td align="center" width="60"><p>&nbsp;<? echo change_date_format($row["COVER_NOTE_DATE"]); ?></p></td>
						<td width="90"><p><? echo $issueBankrArr[$row["ISSUING_BANK_ID"]]; ?></p></td>
						<td width="90"><p><? echo $CountryArr[$row["ORIGIN"]] ?></p></td>
						<td align="center" width="70"><? echo $incoterm[$row["INCO_TERM_ID"]]; ?></td>
						<td  width="100"><? echo $row["PORT_OF_LOADING"]; ?></td>
                        <td width="100"><? echo $row["PORT_OF_DISCHARGE"];  ?></td>						
                        <td width="100"><? echo rtrim($pi_user_name,', ');  ?></td>						
                        <td width="100"><? echo $row["BTB_SYSTEM_ID"];  ?></td>						
                        <td align="center" width="60">&nbsp;<? echo change_date_format($row["INSERT_DATE"]);  ?></td>						
                        <td width="100"><p><? echo $user_name[$row["INSERTED_BY"]]; ?></p></td>
                        <td ><p>
							<? 
								$file_name_btb=$file_arr[$row['BTB_SYSTEM_ID']]['file'];
								if( $file_name_btb != '')
								{
							?>
								<input type="button" class="image_uploader" id="fileno_<? echo $i;?>" style="width:60px" value="File" onClick="openmypage_file_btb('<? echo $row['BTB_SYSTEM_ID']; ?>','<? echo $row['BTB_ID'];?>')"/>
							<?  
								}
							?>	
						</p></td>
					</tr>
					<?
					$i++;
				}
				?>
				</table>
				<!-- <table cellspacing="0" width="<? echo $tbl_width;?>"  border="1" rules="all" class="rpt_table" >
					<tfoot>
						<tr>
							<th width="30">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="90">&nbsp;</th>
							<th width="90">&nbsp;</th>
							<th width="90">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="90">&nbsp;</th>
							<th width="90">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th >&nbsp;</th>
						</tr>
					</tfoot>         
				</table> -->
			</div>
		</div>
		<?
	}

	if($report_type==7) // Import Register
	{
		$item_category_id=str_replace("'","",$cbo_item_category_id);
		//$item_category_arr = array(4,8,10,11,13,15,21);
		$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
		$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
		$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name");
		$issueBankrArr = return_library_array("select id,bank_name from lib_bank ","id","bank_name");
		$categoryNameArr = return_library_array("select category_id,short_name from LIB_ITEM_CATEGORY_LIST ","category_id","short_name");
		$ambdNameArr = return_library_array("select btb_id,amendment_no from COM_BTB_LC_AMENDMENT ","btb_id","amendment_no");
		$candfNameArr = return_library_array("select id,SUPPLIER_NAME from lib_supplier ","id","SUPPLIER_NAME");
		
		//var_dump($assocArray);die;
		$cbo_company=str_replace("'","",$cbo_company_id);
		$cbo_issue=str_replace("'","",$cbo_issue_banking);
		$lc_type_id=str_replace("'","",$cbo_lc_type_id);
		$txt_lc_no=str_replace("'","",$txt_lc_no);
		$cbo_payterm_id=str_replace("'","",$cbo_payterm_id);
		
		$cbo_bonded=str_replace("'","",$cbo_bonded_warehouse);
		$from_date=str_replace("'","",$txt_date_from);
		$to_date=str_replace("'","",$txt_date_to);
		$cbo_supplier=str_replace("'","",$cbo_supplier);
		$txt_search_common=str_replace("'","",$txt_search_common);
		$cbo_status=str_replace("'","",$cbo_status);
		$cbo_reference_close=str_replace("'","",$cbo_reference_close);

		$reference_close_cond='';
		if ($cbo_reference_close == 1)
			$reference_close_cond = " and a.ref_closing_status=1";
		else if ($cbo_reference_close == 2) 
			$reference_close_cond = " and a.ref_closing_status=0";
		else $reference_close_cond = " and a.ref_closing_status in(0,1)";
		$reference_close=array(1=>"Yes",2=>"No");
		$sql_cond="";
		$supply_so=str_pad(str_replace("'","",$cbo_supply_source), 2, "0", STR_PAD_LEFT);
		if (str_replace("'","",$cbo_supply_source)>0) $sql_cond .=" and a.lc_category='".$supply_so."'";
		if ($cbo_company!="") $sql_cond .=" and a.importer_id in($cbo_company) ";
		if ($cbo_issue>0) $sql_cond .=" and a.issuing_bank_id=$cbo_issue ";
		if ($item_category_id>0) $sql_cond .=" and a.pi_entry_form=".$category_wise_entry_form[$item_category_id];
		
		if ($lc_type_id>0) $sql_cond .=" and a.lc_type_id=$lc_type_id ";
		if ($cbo_bonded>0) $sql_cond .=" and a.bonded_warehouse=$cbo_bonded ";
		if ($cbo_payterm_id>0) $sql_cond .=" and a.payterm_id=$cbo_payterm_id ";
		if ($cbo_supplier>0) $sql_cond .=" and a.supplier_id=$cbo_supplier ";
		if($txt_lc_no!="") $sql_cond .=" and a.lc_number = '$txt_lc_no'";
		
		//print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));
		$cbo_based_on=str_replace("'","",$cbo_based_on);
		if($db_type==2)
		{
			if($cbo_based_on==1)
			{
				if( $from_date=="" && $to_date=="" ) $lc_date=""; else $lc_date= " and a.lc_date between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
			}
			else
			{
				if( $from_date=="" && $to_date=="" ) $lc_date=""; else $lc_date= " and a.insert_date between '".date("j-M-Y",strtotime($from_date)) ." 12:00:01 AM"."' and '".date("j-M-Y",strtotime($to_date)). " 11:59:59 PM"."'";
			}
			$select_insert_date=" to_char(a.insert_date,'DD-MM-YYYY') as  insert_date";
			
		}
		else if($db_type==0)
		{
			if($cbo_based_on==1)
			{
				if( $from_date=='' && $to_date=='' ) $lc_date=""; else $lc_date= " and a.lc_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
			}
			else
			{
				if( $from_date=='' && $to_date=='' ) $lc_date=""; else $lc_date= " and a.insert_date between '".change_date_format($from_date,'yyyy-mm-dd'). " 00:00:01"."' and '".change_date_format($to_date,'yyyy-mm-dd'). " 23:59:59"."'";
			}
			
			$select_insert_date=" DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date";
		}
		
		
		if($db_type==0)
		{
			$select_pi_id=" group_concat(b.pi_id) as PI_ID";
			$select_lc_sc_id=" group_concat(p.lc_sc_id) as LC_SC_ID";
		}
		else
		{
			$select_pi_id=" listagg(cast(b.pi_id as varchar(4000)),',') within group(order by b.pi_id) as PI_ID";
			$select_lc_sc_id=" listagg(cast(p.lc_sc_id as varchar(4000)),',') within group(order by p.lc_sc_id) as LC_SC_ID";
		}
		if($txt_search_common!="") 
		{
			if ($cbo_search_by==1) 
			{
				$sql = "SELECT a.id, b.is_lc_sc, b.lc_sc_id  
				from com_btb_lc_master_details a, com_btb_export_lc_attachment b, com_export_lc c
				where a.id=b.import_mst_id and b.lc_sc_id=c.id and b.is_lc_sc=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $issue_banking $category_entry_form_cond $lc_tpe_id $bonded_warehouse $lc_date  $supply_source_cond $lc_no_cond $txt_lc_no_cond order by a.lc_date ";
			}
			else
			{
				$sql = "SELECT a.id, b.is_lc_sc, b.lc_sc_id
				from com_btb_lc_master_details a, com_btb_export_lc_attachment b, com_sales_contract c
				where a.id=b.import_mst_id and b.lc_sc_id=c.id and b.is_lc_sc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $issue_banking $category_entry_form_cond $lc_tpe_id $bonded_warehouse $lc_date  $supply_source_cond $lc_no_cond $txt_sc_no_cond  order by a.lc_date ";
			}
			$lc_sc_sql=sql_select($sql);
			foreach($lc_sc_sql as $row)
			{
				$ls_sc_id.=$row[csf('id')].',';
			}
				
	
			$ls_sc_id_cond = implode(",",array_unique(explode(",",chop($ls_sc_id,','))));
			if($ls_sc_id_cond!="")
			{
				$btb_lc_sc_no_cond=" and a.id in($ls_sc_id_cond)";
			}
			else
			{
				echo "<br><strong><span style='color:red;'>Data Not Found</span></strong>";die;
			}
			
			$main_sql = "SELECT a.id as BTB_ID, a.lc_number as LC_NUMBER, a.application_date as APPLICATION_DATE, a.importer_id as IMPORTER_ID, a.lc_type_id as LC_TYPE_ID, a.lc_date as LC_DATE, a.maturity_from_id as MATURITY_FROM_ID, a.supplier_id as SUPPLIER_ID, a.payterm_id as PAYTERM_ID, a.tenor as TENOR, a.insurance_company_name as INSURANCE_COMPANY_NAME, a.lc_value as LC_VALUE, a.uom_id as UOM_ID, a.lc_category as LC_CATEGORY,a.remarks as REMARKS,a.last_shipment_date as LAST_SHIPMENT_DATE, a.lc_expiry_date from com_btb_lc_master_details a where a.status_active=1 and a.is_deleted=0 $lc_date $sql_cond $btb_lc_sc_no_cond group by a.id, a.lc_number, a.application_date, a.importer_id, a.lc_type_id, a.lc_date, a.maturity_from_id, a.supplier_id, a.payterm_id, a.tenor, a.insurance_company_name, a.lc_value, a.uom_id, a.lc_category, a.remarks,a.last_shipment_date, a.lc_expiry_date order by a.id";

		}
		else
		{
			$main_sql = "SELECT a.id as BTB_ID, a.lc_number as LC_NUMBER, a.application_date as APPLICATION_DATE, a.importer_id as IMPORTER_ID, a.lc_type_id as LC_TYPE_ID, a.lc_date as LC_DATE, a.maturity_from_id as MATURITY_FROM_ID, a.supplier_id as SUPPLIER_ID, a.payterm_id as PAYTERM_ID, a.tenor as TENOR, a.insurance_company_name as INSURANCE_COMPANY_NAME, a.lc_value as LC_VALUE, a.uom_id as UOM_ID, a.lc_category as LC_CATEGORY,a.remarks as REMARKS,a.last_shipment_date as LAST_SHIPMENT_DATE, a.lc_expiry_date from com_btb_lc_master_details a where a.status_active=1 and a.is_deleted=0 $lc_date $sql_cond group by a.id, a.lc_number, a.application_date, a.importer_id, a.lc_type_id, a.lc_date, a.maturity_from_id, a.supplier_id, a.payterm_id, a.tenor, a.insurance_company_name, a.lc_value, a.uom_id, a.lc_category, a.remarks,a.last_shipment_date, a.lc_expiry_date order by a.id";

		}
		//echo $main_sql;
		//die;
		$main_sql_result=sql_select($main_sql);

		$row_count=array();
		$total_inv_val=array();
		foreach($main_sql_result as $val)
		{
			//$row_count[$val['BTB_ID']]++;
			$total_inv_val[$val['BTB_ID']]+=$val['ACCEPTANCE_VALUE'];
			$pi_id = $val['PI_ID'];
		}
		
		//pi details start 
	
		$sql_pi_dtls="SELECT  b.id as PI_ID, b.item_category_id as CATEGORY_ID ,b.pi_number as PI_NUMBER, b.pi_date as PI_DATE, sum(c.quantity) as QTY, a.id AS BTB_ID , a.lc_number AS LC_NUMBER
		from com_btb_lc_master_details a,   com_pi_master_details b, com_pi_item_details c, COM_BTB_LC_PI d where
		a.status_active=1 and a.is_deleted=0 AND b.status_active=1 and b.is_deleted=0 AND c.status_active=1 and c.is_deleted=0 AND d.status_active=1 and d.is_deleted=0 AND  b.id = c.pi_id AND c.pi_id = d.pi_id AND a.id= d.com_btb_lc_master_details_id $lc_date $sql_cond group by b.item_category_id,b.id,
		b.pi_number, b.pi_date, a.id,a.lc_number";
		//echo $sql_pi_dtls;
		$result_pi_dtls=sql_select($sql_pi_dtls);
		foreach($result_pi_dtls as $row_pi)
		{
			$row_count[$row_inv['PI_ID']]++;
			$pi_no_arr[$row_pi[csf("BTB_ID")]][$row_pi[csf("PI_ID")]]["PI_NUMBER"] = $row_pi[csf("pi_number")];
			$pi_no_arr[$row_pi[csf("BTB_ID")]][$row_pi[csf("PI_ID")]]["PI_DATE"] = $row_pi[csf("pi_date")];
			$pi_no_arr[$row_pi[csf("BTB_ID")]][$row_pi[csf("PI_ID")]]["CATEGORY_ID"] = $row_pi[csf("category_id")];
			$pi_no_arr[$row_pi[csf("BTB_ID")]][$row_pi[csf("PI_ID")]]["QTY"] = $row_pi[csf("qty")];
		}

		//pi details end
		unset($result_pi_dtls);

		//invoice start
		$main_sql_inv="SELECT a.id as BTB_ID, a.lc_number as LC_NUMBER, c.id as INVOICE_ID ,c.invoice_no as INVOICE_NO, c.invoice_date as INVOICE_DATE, c.company_acc_date as COMPANY_ACC_DATE, c.bank_acc_date as BANK_ACC_DATE, c.bank_ref as BANK_REF, c.retire_source as RETIRE_SOURCE, c.shipment_date as SHIPMENT_DATE, c.edf_paid_date as EDF_PAID_DATE, c.maturity_date as MATURITY_DATE, c.doc_rcv_date as DOC_RCV_DATE,c.nagotiate_date as NAGOTIATE_DATE, sum(b.current_acceptance_value) as ACCEPTANCE_VALUE, c.bill_entry_value as BILL_ENTRY_VALUE, c.bill_no as BILL_NO, c.bill_date as BILL_DATE, c.bill_of_entry_no as BILL_OF_ENTRY_NO, c.bill_of_entry_date as BILL_OF_ENTRY_DATE,d.cnf_name_id as CNF_NAME_ID
		from com_btb_lc_master_details a
		left join com_import_invoice_dtls b on a.id=b.btb_lc_id and b.status_active=1 and b.is_deleted=0
		left join com_import_invoice_mst c on b.import_invoice_id=c.id and c.status_active=1 and c.is_deleted=0
		left join CNF_BILL_MST d on d.INVOICE_NO=c.INVOICE_NO and d.status_active=1 and d.is_deleted=0
		where a.status_active=1 and a.is_deleted=0  $lc_date $sql_cond
		group by a.id, a.lc_number, c.invoice_no,c.id, c.invoice_date, c.company_acc_date, c.bank_acc_date, c.bank_ref, c.retire_source, c.shipment_date, c.edf_paid_date, c.maturity_date, c.doc_rcv_date,c.nagotiate_date, c.bill_entry_value, c.bill_no, c.bill_date, c.bill_of_entry_no, c.bill_of_entry_date,d.cnf_name_id 
		order by a.id";
		//echo $main_sql_inv;
		$result_inv_dtls=sql_select($main_sql_inv);
		foreach($result_inv_dtls as $row_inv)
		{
			$row_count[$row_inv['BTB_ID']]++;
			$inv_no_arr[$row_inv[csf("BTB_ID")]][$row_inv[csf("INVOICE_ID")]]["INVOICE_DATE"] = $row_inv[csf("invoice_date")];
			$inv_no_arr[$row_inv[csf("BTB_ID")]][$row_inv[csf("INVOICE_ID")]]["INVOICE_NO"] = $row_inv[csf("invoice_no")];
			$inv_no_arr[$row_inv[csf("BTB_ID")]][$row_inv[csf("INVOICE_ID")]]["MATURITY_DATE"] = $row_inv[csf("maturity_date")];
			$inv_no_arr[$row_inv[csf("BTB_ID")]][$row_inv[csf("INVOICE_ID")]]["ACCEPTANCE_VALUE"] += $row_inv["ACCEPTANCE_VALUE"];
			$inv_no_arr[$row_inv[csf("BTB_ID")]][$row_inv[csf("INVOICE_ID")]]["BILL_OF_ENTRY_NO"] = $row_inv[csf("bill_of_entry_no")];
			$inv_no_arr[$row_inv[csf("BTB_ID")]][$row_inv[csf("INVOICE_ID")]]["BILL_OF_ENTRY_DATE"] = $row_inv[csf("bill_of_entry_date")];
			$inv_no_arr[$row_inv[csf("BTB_ID")]][$row_inv[csf("INVOICE_ID")]]["BILL_DATE"] = $row_inv[csf("bill_date")];
			$inv_no_arr[$row_inv[csf("BTB_ID")]][$row_inv[csf("INVOICE_ID")]]["CNF_NAME_ID"] = $row_inv[csf("cnf_name_id")];
			$inv_acc_val += $row_inv["ACCEPTANCE_VALUE"];
		}
		unset($result_ambnt_dtls);
		//invoice result_inv_dtls

		// ambndmnt start
		$sql_ambnt ="SELECT b.btb_id as BTB_ID ,b.amendment_no as AMENDMENT_NO from com_btb_lc_master_details a ,com_btb_lc_amendment b  where a.id=b.btb_id and a.is_deleted=0 and b.is_deleted=0 and b.amendment_no!=0";
		//echo $sql_ambnt;
		$result_ambnt_dtls=sql_select($sql_ambnt);
		foreach($result_ambnt_dtls as $row_ambnt)
		{
			$ambnt_no_arr[$row_ambnt["BTB_ID"]]["AMENDMENT_NO"] = $row_ambnt[csf("amendment_no")];
		}
		unset($result_ambnt_dtls);
		// ambndmnt end
		//
		//echo count($inv_no);
		//echo "<pre>";
		//print_r($inv_no);
	
		ob_start();
		$div_width="2350";
		$tbl_width="2320";
		
		?>
		<div id="scroll_body" align="center" style="height:auto; width:<? echo $div_width; ?>px; margin:0 auto; padding:0;">
			<table width="<? echo $div_width;?>">
				<?
				$company_library=sql_select("select id, company_name, plot_no, level_no,road_no,city from lib_company where id=".$cbo_company_id."");
				foreach( $company_library as $row)
				{
					$company_name.=$row[csf('company_name')].", ";
				}
				?>
				<tr>
					<td colspan="28" align="center" style="font-size:22px"><center><strong><? echo rtrim($company_name,", ");?></strong></center></td>
				</tr>
				<tr>
					<td colspan="28" align="center" style="font-size:18px"><center><strong><u><? echo "IMPORT REGISTER"; //$report_title; ?></u></strong></center></td>
				</tr>
			</table>

			<table cellspacing="0" width="<? echo $tbl_width;?>"  border="1" rules="all" class="rpt_table" align="left">
			   <thead>
					<tr>
						<th colspan="1" rowspan="2" width="30"><p>SL <br> NO</p></th>
						<th colspan="5"><p>L/C INFORMATION</p></th>
						<th colspan="6"><p>INFORMATION OF PROFORMA INVOICE</p></th>
						<th colspan="2"><p>AUTHORIZED SIGNATURE</p></th>
						<th colspan="2"><p>GOODS RECEIVED</p></th>
						<th colspan="1"  rowspan="2" width="90"><p>ACCEPTED <br> VALUE</p></th>
						<th colspan="2"><p>SIGNATURE</p></th>
						<th colspan="2"><p>AUTHORIZED SIGNATURE</p></th>
						<th colspan="2"><p>BILL OF ENTRY</p></th>
						<th colspan="1" rowspan="2" width="80"><p>Maturity Date</p></th>
                        <th colspan="1" rowspan="2"  width="80"><p>Payment <br> Terms</p></th>
						<th colspan="2"><p>Party Name</p></th>  
					</tr>
               		<tr>
                        <th width="120"><p>LC Number</p></th>
                        <th width="70"><p>LC Date</p></th>
                        <th width="70"><p>Ship. Date</p></th>
                        <th width="70"><p>Expiry Date</p></th>
                        <th width="70"><p>Amendment</p></th>

                        <th width="100"><p>Supplier</p></th>
                        <th width="70"><p>PI No</p></th>
                        <th width="70"><p>PI Date</p></th>
                        <th width="80"><p>Category</p></th>
                        <th width="80"><p>Qty(Pcs/Kg)</p></th>
                        <th width="90"><p>LC Value (USD)</p></th>

                        <th width="100"><p>FD/ED</p></th>  
                        <th width="100"><p>MD</p></th>      

                        <th width="70"><p>Invoice Date</p></th>  
						<th width="100"><p>Invoice No.</p></th>
					

                        <th width="90"><p>Audit</p></th>
                        <th width="100"><p>Commercial</p></th>

						<th width="90"><p>FD/ED</p></th>
                        <th width="100"><p>MD</p></th>

						<th width="70"><p>NO of En.</p></th>
                        <th width="80"><p>Date</p></th>

                        <th width="100"><p>Insurance <br> Company</p></th>
                        <th><p>C&F</p></th>
                        
                    </tr>
			   </thead>
			</table>

			<div style="width:<? echo $div_width;?>px; overflow-y: scroll; overflow-x:hidden; max-height:300px;" id="scroll_body2"> 

				<table cellspacing="0" width="<? echo $tbl_width;?>"  border="1" rules="all" class="rpt_table" id="tbl_marginlc_list" style="float: left;" >
				<?
				$i=1;
				// print_r($main_sql_result);die;
				foreach($main_sql_result as $row)
				{     
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					$deferd_liability=0;
					$tot_rec_qty=0;
					$sub_pi_qty=0;
					$sub_lc_value=0;
					$sub_total_ac_val =0;
					$row_count = $row_count[$row['BTB_ID']];
					?>
					<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td valign="top" align="center" width="30"><p><? echo $i; ?></p></td>
						<td valign="top" align="center" width="120"><p><?echo $row["LC_NUMBER"]; ?>&nbsp;</p></td>
						<td valign="top" align="center" width="70"><p>&nbsp;<? if($row["LC_DATE"]!="" && $row["LC_DATE"]!="0000-00-00") echo change_date_format($row["LC_DATE"]); ?></p></td>
						<td valign="top" align="center" width="70"><p>&nbsp;<? if($row["LAST_SHIPMENT_DATE"]!="" && $row["LAST_SHIPMENT_DATE"]!="0000-00-00") echo change_date_format($row["LAST_SHIPMENT_DATE"]); ?></p></td>
						<td valign="top" align="center" width="70"><p>&nbsp;<? if($row["LC_DATE"]!="" && $row["LC_DATE"]!="0000-00-00")  echo change_date_format($row["LC_EXPIRY_DATE"]); ?></p></td>
						<td valign="top" align="center" width="70"><p>&nbsp;<?  echo $ambnt_no_arr[$row['BTB_ID']]["AMENDMENT_NO"]; ?></p></td>
						<td valign="top" width="100"><p><? if($exportPiSuppArr[$row["BTB_ID"]]==1) echo $companyArr[$row["SUPPLIER_ID"]]; else echo $supplierArr[$row["SUPPLIER_ID"]];?></p></td>

						<td valign="top" align="right" width="70"> <p><?  //pi number
							foreach($pi_no_arr[$row['BTB_ID']] as $row_pi)
							{
								if($row_pi["PI_NUMBER"]!='')
								{
									?>
									<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
										<tr>
											<td width ="70"  align="center">
												<p><?echo $row_pi["PI_NUMBER"];?></p>
											</td>
										</tr>
									</table>
									<?
								}
							}
							?></p> 
						</td>

						<td valign="top" align="right" width="70"> <p><?  //pi date
						foreach($pi_no_arr[$row['BTB_ID']] as $row_pi)
						{
							if($row_pi["PI_DATE"]!='')
							{
								?>
								<table border="1" rules="all" class="rpt_table" cellspacing="0" cellpadding="0">
									<tr>
										<td width ="70"  align="center" >
											<? echo change_date_format($row_pi["PI_DATE"]);?>
										</td>
									</tr>
								</table>
								<?
							}
						}
						?></p> 
						</td>

						<td valign="top" align="right" width="80"> <p><?   //pi category
						foreach($pi_no_arr[$row['BTB_ID']] as $row_pi)
						{
							if($row_pi["CATEGORY_ID"]!='')
							{
								?>
								<table border="1" rules="all" class="rpt_table" cellspacing="0" cellpadding="0">
									<tr>
										<td width ="80"  align="center">
											<p><?echo $categoryNameArr[$row_pi["CATEGORY_ID"]];?></p>
										</td>
									</tr>
								</table>
								<?
							}
						}
						?></p> 
						</td>

						<td valign="top" align="right" width="80"> <p><?   // pi qty
							foreach($pi_no_arr[$row['BTB_ID']] as $row_pi)
							{
								if($row_pi["QTY"]!='')
								{
									?>
									<table border="1" rules="all" class="rpt_table" cellspacing="0" cellpadding="0">
										<tr>
											<td width ="80"  align="center">
												<p>
												<? echo number_format($row_pi["QTY"],2);
												$sub_pi_qty += $row_pi["QTY"];
												?>
												</p>
												&nbsp;
											</td>
										</tr>
									</table>
									<?
								}
							}
						?></p> 
						</td>
						
						<td rowspan="<?=$row_count[$row['BTB_ID']];?>" valign="top" align="right" width="90"> <p><? 
						echo number_format($row["LC_VALUE"],2); 
						$sub_lc_value += $row["LC_VALUE"];
						?>&nbsp;</p> </td>
						
						<td rowspan="<?=$row_count[$row['BTB_ID']];?>" valign="top" align="right" width="100"></td> 
						<td rowspan="<?=$row_count[$row['BTB_ID']];?>" valign="top" align="right" width="100"></td> 

						<td valign="top" width="70"><p><?  //invc date
							foreach($inv_no_arr[$row['BTB_ID']] as $row_invc)
							{
								if($row_invc["INVOICE_DATE"]!='')
								{
									?>
									<table border="1" rules="all" class="rpt_table" cellspacing="0" cellpadding="0">
										<tr>
											<td width ="70"  align="center">
												<?echo change_date_format($row_invc["INVOICE_DATE"]);?>
											</td>
										</tr>
									</table>
									<?
								}
							}
							?></p>
						</td>
						
						<td valign="top" width="100"><p><?   //invc number
							foreach($inv_no_arr[$row['BTB_ID']] as $row_invc)
							{
								if($row_invc["INVOICE_NO"]!='')
								{
									?>
									<table border="1" rules="all" class="rpt_table" cellspacing="0" cellpadding="0">
										<tr>
											<td width ="100"  align="center">
												<p><?echo $row_invc["INVOICE_NO"];?>&nbsp;</p>
											</td>
										</tr>
									</table>
									<?
								}
							}
							?></p>
						</td>

						<td valign="top" width="90"><p><?   //invc number wise ACCEPTANCE_VALUE
							foreach($inv_no_arr[$row['BTB_ID']] as $row_invc)
							{
								if($row_invc["ACCEPTANCE_VALUE"]!='')
								{
									?>
									<table border="1" rules="all" class="rpt_table" cellspacing="0" cellpadding="0">
										<tr>
											<td width ="90"  align="right">
												<p><?
													echo number_format($row_invc["ACCEPTANCE_VALUE"],2);
											        $sub_total_ac_val+=$row_invc["ACCEPTANCE_VALUE"];
												?></p>
											</td>
										</tr>
									</table>
									<?
								}
							}
							?></p>
						</td>
			
						<td valign="top" align="right" width="90"></td> 
						<td valign="top" align="right" width="100"></td> 

						<td valign="top" align="right" width="90"></td> 
						<td valign="top" align="right" width="100"></td> 

						<td valign="top" align="right" width="70"><p><?  
							foreach($inv_no_arr[$row['BTB_ID']] as $row_invc)
							{
								if($row_invc["BILL_OF_ENTRY_NO"]!='')
								{
									?>
									<table border="1" rules="all" class="rpt_table" cellspacing="0" cellpadding="0">
										<tr>
											<td width ="80"  align="center" >
												<p>
												<?
												echo $row_invc["BILL_OF_ENTRY_NO"];
												?>
												</p>
											</td>
										</tr>
									</table>
									<?
								}
							}
							?></p>
						</td>	
						
                        <td valign="top" align="right" width="80"><p><?  //bill_of_entry_date
							foreach($inv_no_arr[$row['BTB_ID']] as $row_invc)
							{
								if($row_invc["BILL_OF_ENTRY_DATE"]!='')
								{
									?>
									<table border="1" rules="all" class="rpt_table" cellspacing="0" cellpadding="0">
										<tr>
											<td width ="80"  align="center">
												<?
												echo change_date_format($row_invc["BILL_OF_ENTRY_DATE"]);
												
												?>
											</td>
										</tr>
									</table>
									<?
								}
							}
							?></p>
						</td> 

						<td valign="top" align="center" width="80"><p><?  //maturity date
							foreach($inv_no_arr[$row['BTB_ID']] as $row_invc)
							{
								if($row_invc["MATURITY_DATE"]!='')
								{
									?>
									<table border="1" rules="all" class="rpt_table" cellspacing="0" cellpadding="0">
										<tr>
											<td width ="80"  align="center">
												<?
												echo change_date_format($row_invc["MATURITY_DATE"]);
												
												?>
											</td>
										</tr>
									</table>
									<?
								}
							}
							?></p>
						</td>		
						
						<td valign="top" align="center" width="80"><p><?  echo $pay_term[$row["PAYTERM_ID"]]; ?></p></td> 
						<td valign="top" align="right" width="100"><p><? echo $row["INSURANCE_COMPANY_NAME"]?></p></td> 
						<td valign="top" align="right"><p><?
							foreach($inv_no_arr[$row['BTB_ID']] as $row_invc)
							{
								if($row_invc["CNF_NAME_ID"]!='')
								{
									?>
									<table border="1" rules="all" class="rpt_table" cellspacing="0" cellpadding="0">
										<tr>
											<td  align="center" style=" border-left: 0px solid; border-right: 0px solid;">
												<?
												echo $candfNameArr[$row_invc["CNF_NAME_ID"]];
												?>
											</td>
										</tr>
									</table>
									<?
								}
							}
							?></p>
						</td> 
					</tr>
					
					<tr bgcolor="#CCCCCC">
							<td width="30">&nbsp; </td>
							<td width="120">&nbsp;</td>
							<td width="70">&nbsp; </td>
							<td width="70">&nbsp; </td>
							<td width="70">&nbsp; </td>
							<td width="70">&nbsp; </td>
							<td width="100">&nbsp;</td>
							<td width="70">&nbsp; </td>
							<td width="70">&nbsp; </td>
							<td width="80" align="right">Sub Total &nbsp;</td>
							<td width="80" align="center"><?=number_format($sub_pi_qty,2);?>&nbsp;</td>
							<td width="90" align="right"><?=number_format($sub_lc_value,2);?>&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="70">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="90" align="right"><?=number_format($sub_total_ac_val,2);?>&nbsp;</td>  
							<td width="90">&nbsp; </td>
							<td width="100">&nbsp;</td>
							<td width="90">&nbsp; </td>
							<td width="100">&nbsp;</td>
							<td width="70">&nbsp; </td>
							<td width="80">&nbsp; </td>
							<td width="80">&nbsp; </td>
							<td width="80">&nbsp; </td>
							<td width="100">&nbsp;</td>
							<td >&nbsp; </td>
							
						</tr>
					<?
					$i++;
					$grand_total_pi_qty += $sub_pi_qty;
					$grand_total_lc_value += $sub_lc_value;
					$grand_total_ac_val += $sub_total_ac_val;
				}
				?>
				</table>
				<table cellspacing="0" width="<? echo $tbl_width;?>"  border="1" rules="all" class="rpt_table" align="left">
					<tfoot>
						<tr>
							<th width="30">&nbsp;</th>
							<th width="120">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="80" align="right"><strong>GR.TOTAL</strong></th>
							<th width="80"><?  echo number_format($grand_total_pi_qty,2);?>&nbsp;</th>
							<th width="90"><?=number_format($grand_total_lc_value,2);?>&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="90"><?=number_format($grand_total_ac_val,2);?>&nbsp;</th>  
							<th width="90">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="90">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th>&nbsp;</th>
							
						</tr>
					</tfoot>         
				</table> 
			</div>
		</div>
		<?
	}

	if($report_type==8) //Show 2
	{
		$item_category_id=str_replace("'","",$cbo_item_category_id);
		//$item_category_arr = array(4,8,10,11,13,15,21);
		$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
		$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
		$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name");
		$issueBankrArr = return_library_array("select id,bank_name from lib_bank ","id","bank_name");
		$user_name = return_library_array("select id,user_name from user_passwd ","id","user_name");
		
		$cbo_company=str_replace("'","",$cbo_company_id);
		$cbo_issue=str_replace("'","",$cbo_issue_banking);
		$lc_type_id=str_replace("'","",$cbo_lc_type_id);
		$txt_lc_no=str_replace("'","",$txt_lc_no);
		$cbo_payterm_id=str_replace("'","",$cbo_payterm_id);
		
		$cbo_bonded=str_replace("'","",$cbo_bonded_warehouse);
		$from_date=str_replace("'","",$txt_date_from);
		$to_date=str_replace("'","",$txt_date_to);
		$cbo_supplier=str_replace("'","",$cbo_supplier);
		$txt_search_common=str_replace("'","",$txt_search_common);
		$cbo_status=str_replace("'","",$cbo_status);
		$cbo_reference_close=str_replace("'","",$cbo_reference_close);
		$txt_file_no=str_replace("'","",$txt_file_no);
		$cbo_search_by=str_replace("'","",$cbo_search_by);
		$cbo_based_on=str_replace("'","",$cbo_based_on);

		$sql_cond="";
		$company_id =" and c.beneficiary_name in($cbo_company)";
		$sql_cond = ($txt_file_no != "") ? " AND c.INTERNAL_FILE_NO LIKE '%$txt_file_no%'" : "";
		if ($cbo_issue>0) $sql_cond .=" and a.issuing_bank_id=$cbo_issue ";	
		if ($lc_type_id>0) $sql_cond .=" and a.LC_TYPE_ID=$lc_type_id ";
		if ($item_category_id>0) $sql_cond .=" and c.EXPORT_ITEM_CATEGORY=$item_category_id ";
		// if ($cbo_payterm_id>0) $sql_cond .=" and a.payterm_id=$cbo_payterm_id ";
		if ($cbo_supplier>0) $sql_cond .=" and a.supplier_id=$cbo_supplier ";
		if ($cbo_status>0) $sql_cond .=" and a.status_active=$cbo_status ";


		if($cbo_based_on==1)
		{
			if( $from_date=="" && $to_date=="" ) $lc_date=""; else $lc_date= " and a.lc_date between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
		}
		else
		{
			if( $from_date=="" && $to_date=="" ) $lc_date=""; else $lc_date= " and a.insert_date between '".date("j-M-Y",strtotime($from_date)) ." 12:00:01 AM"."' and '".date("j-M-Y",strtotime($to_date)). " 11:59:59 PM"."'";
		}
				
		$sql = "SELECT A.ID, B.IS_LC_SC, B.LC_SC_ID, C.INTERNAL_FILE_NO, A.LC_NUMBER, A.LC_YEAR, A.LC_DATE, A.ISSUING_BANK_ID, C.BUYER_NAME ,C.LC_YEAR AS FILE_YEAR, A.LC_TYPE_ID, A.LC_VALUE, A.SUPPLIER_ID, C.EXPORT_ITEM_CATEGORY
		FROM COM_BTB_LC_MASTER_DETAILS A, COM_BTB_EXPORT_LC_ATTACHMENT B, COM_EXPORT_LC C
		WHERE A.ID=B.IMPORT_MST_ID AND B.LC_SC_ID=C.ID AND B.IS_LC_SC=0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 $sql_cond  $company_id $lc_date AND c.INTERNAL_FILE_NO LIKE '%$txt_file_no%' AND B.IS_DELETED=0
		UNION ALL
		SELECT A.ID, B.IS_LC_SC, B.LC_SC_ID, C.INTERNAL_FILE_NO, A.LC_NUMBER, A.LC_YEAR, A.LC_DATE, A.ISSUING_BANK_ID, C.BUYER_NAME, C.SC_YEAR AS FILE_YEAR, A.LC_TYPE_ID, A.LC_VALUE, A.SUPPLIER_ID, C.EXPORT_ITEM_CATEGORY
		FROM COM_BTB_LC_MASTER_DETAILS A, COM_BTB_EXPORT_LC_ATTACHMENT B, COM_SALES_CONTRACT C
		WHERE A.ID=B.IMPORT_MST_ID AND B.LC_SC_ID=C.ID AND B.IS_LC_SC=1 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 $sql_cond  $company_id $lc_date AND c.INTERNAL_FILE_NO LIKE '%$txt_file_no%' AND B.IS_DELETED=0";
			
		// echo $sql;
		$lc_sc_sql=sql_select($sql);

		$file_wish_arr=$btb_lc_id_arr_new=array();
		foreach($lc_sc_sql as $row)
		{	
			$btb_lc_id_arr[$row["ID"]]=$row["ID"];
			$btb_lc_id_arr_new[$row["ID"]]=$row["ID"];			
		}
		reset($lc_sc_sql);
		//echo $main_sql;die;

		$con = connect();
		$rid=execute_query("delete from GBL_TEMP_ENGINE where entry_form=129 and user_id=$user_id");
		if($rid) oci_commit($con);

		$pi_qty_arr=array();
		if(!empty($btb_lc_id_arr))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 129, 1, $btb_lc_id_arr,$empty_arr);
			$sql_result_data =sql_select("SELECT a.COM_BTB_LC_MASTER_DETAILS_ID, b.QUANTITY, b.AMOUNT, b.UOM FROM  COM_BTB_LC_PI A, COM_PI_ITEM_DETAILS B, GBL_TEMP_ENGINE D WHERE A.COM_BTB_LC_MASTER_DETAILS_ID=D.REF_VAL AND A.PI_ID=B.PI_ID AND D.USER_ID= $user_id AND D.ENTRY_FORM=129 AND D.REF_FROM=1");			
			foreach($sql_result_data as $row){
				$pi_qty_arr[$row["COM_BTB_LC_MASTER_DETAILS_ID"]]["QUANTITY"]+=$row["QUANTITY"];	
				$pi_qty_arr[$row["COM_BTB_LC_MASTER_DETAILS_ID"]]["AMOUNT"]+=$row["AMOUNT"];	
				$pi_qty_arr[$row["COM_BTB_LC_MASTER_DETAILS_ID"]]["UOM"]=$row["UOM"];	
			}
		}
	
		$invoice_info_arr=array();
		if(!empty($btb_lc_id_arr_new))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 129, 2, $btb_lc_id_arr_new,$empty_arr);
			$sql =sql_select("SELECT A.BTB_LC_ID, B.BTMEA_NO, B.BTMEA_DATE  FROM  COM_IMPORT_INVOICE_DTLS A, COM_IMPORT_INVOICE_MST B, GBL_TEMP_ENGINE D WHERE A.BTB_LC_ID=D.REF_VAL AND A.IMPORT_INVOICE_ID=B.ID AND D.USER_ID= $user_id AND D.ENTRY_FORM=129 AND D.REF_FROM=2 group by A.BTB_LC_ID, B.BTMEA_NO, B.BTMEA_DATE");		
			foreach($sql as $val){
				$invoice_info_arr[$val["BTB_LC_ID"]]["BTMEA_NO"].=$val["BTMEA_NO"];	
				$invoice_info_arr[$val["BTB_LC_ID"]]["BTMEA_DATE"].=$val["BTMEA_DATE"];	
			}
		}
		// echo "<pre>";
		// print_r($invoice_info_arr);
		$sql_bank_info = sql_select("SELECT ID, ADDRESS from lib_bank ");
		$bank_dtls_arr=array();
		foreach($sql_bank_info as $row)
		{
			$bank_dtls_arr[$row['ID']]['ADDRESS']=$row['ADDRESS'];
		}
	
		ob_start();
		$div_width="2120";
		$tbl_width="2100";	
		?>
		<div id="scroll_body" align="center" style="height:auto; width:<? echo $div_width; ?>px; margin:0 auto; padding:0;">
			<table width="<? echo $div_width;?>">
				<?
				$company_library=sql_select("select id, company_name, plot_no, level_no,road_no,city from lib_company where id in(".$cbo_company.")");
				foreach( $company_library as $row)
				{
					$company_name.=$row[csf('company_name')].", ";
				}
				?>
				<tr>
					<td colspan="29" align="center" style="font-size:22px"><center><strong><? echo rtrim($company_name,", ");?></strong></center></td>
				</tr>
				<tr>
					<td colspan="29" align="center" style="font-size:18px"><center><strong><u><? echo $report_title; ?></u></strong></center></td>
				</tr>
			</table>
			<table cellspacing="0" width="<? echo $tbl_width;?>"  border="1" rules="all" class="rpt_table" align="left">
			   <thead>
               		<tr>
                    	<th width="30">SL</th>
                        <th width="120">Bank Name</th>
                        <th width="180">Address</th>
                        <th width="120">Buyer Name</th>
                        <th width="70">File Number</th>
                        <th width="90">File Year</th>
						<th width="100">LC No</th>
                        <th width="120">LC Type</th>
                        <th width="80">LC Date</th>
                        <th width="90">LC Value</th>
                        <th width="90">Supplier name</th>
                        <th width="70">Qty</th>
                        <th width="70">Item name </th>
                        <th width="100">GSP BTMA Certificate Number</th>               
                        <th width="100">GSP Data</th>               
                        <th width="100">Dyeing charge</th>               
                    </tr>
			   </thead>
			</table>
			<div style="width:<? echo $div_width;?>px; overflow-y: scroll; overflow-x:hidden; max-height:300px;" id="scroll_body2"> 
				<table cellspacing="0" width="<? echo $tbl_width;?>"  border="1" rules="all" class="rpt_table" id="tbl_marginlc_list" style="float: left;" >
				<?
				$i=1;

				foreach($lc_sc_sql as $row)
				{            
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					$deferd_liability=0;
					?>
					<tr bgcolor="<? echo $bgcolor ; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td align="center" width="30"><? echo $i; ?></td>
						<td width="120" align="center"><p><? echo $issueBankrArr[$row["ISSUING_BANK_ID"]]; ?></p></td>
						<td align="center" width="180"><? echo $bank_dtls_arr[$row['ISSUING_BANK_ID']]['ADDRESS']; ?></td>
                        <td align="center" width="120"><?  echo $buyerArr[$row["BUYER_NAME"]];  ?></td>
                        <td align="center" width="70"><?  echo $row["INTERNAL_FILE_NO"];  ?></td>
                        <td align="center" width="90"><p>&nbsp;<? echo $row["FILE_YEAR"] ?></p></td>
                        <td align="center" width="100"><p>&nbsp;<? echo $row["LC_NUMBER"] ?></p></td>
                        <td align="center" width="120"><p>&nbsp;<? echo $lc_type[$row["LC_TYPE_ID"]] ?></p></td>
                        <td align="center" width="80"><p>&nbsp;<? echo change_date_format($row["LC_DATE"]) ?></p></td>
                        <td align="center" width="90"><p>&nbsp;<? echo $row["LC_VALUE"] ?></p></td>
                        <td align="center" width="90"><p>&nbsp;<? echo $supplierArr[$row["SUPPLIER_ID"]] ?></p></td>
                        <td align="center" width="70"><p>&nbsp;<? echo $pi_qty_arr[$row["ID"]]["QUANTITY"]."  ".$unit_of_measurement[$pi_qty_arr[$row["ID"]]["UOM"]]; ?></p></td>
                        <td align="center" width="70"><p>&nbsp;<? echo $export_item_category[$row["EXPORT_ITEM_CATEGORY"]] ?></p></td>
						<td width="100"><p><? echo $invoice_info_arr[$row["ID"]]["BTMEA_NO"]; ?></p></td>						
						<td width="100"><p><? echo change_date_format($invoice_info_arr[$row["ID"]]["BTMEA_DATE"]); ?></p></td>						
						<td width="100"><p><? echo number_format($pi_qty_arr[$row["ID"]]["AMOUNT"]/$pi_qty_arr[$row["ID"]]["QUANTITY"],4) ?></p></td>						
					</tr>
					  <?
						$total_accep_value+=$row["ACCEPTANCE_VALUE"];
						$total_upass_amt+=$atsite_payment_data[$row["INV_ID"]]["ACCEPTED_AMMOUNT"];
						$total_balance_value+=$btb_balance_amt;
						//$total_yet_to_accep+=$yet_to_accep;
						$i++;
				}
				?>
				</table>				
			</div>
		</div>
		<?
	}

	if($report_type==9) //Show 3
	{
		$item_category_id=str_replace("'","",$cbo_item_category_id);
		//$item_category_arr = array(4,8,10,11,13,15,21);
		$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
		$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
		$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name");
		$issueBankrArr = return_library_array("select id,bank_name from lib_bank ","id","bank_name");
		$user_name = return_library_array("select id,user_name from user_passwd ","id","user_name");
		//$hscodeArr = return_library_array("select id, hs_code from com_pi_master_details ","id","hs_code"); 
		//$lc_num_arr = return_library_array("select id,export_lc_no from com_export_lc ","id","export_lc_no"); 
		//$sc_num_arr = return_library_array("select id,contract_no from com_sales_contract ","id","contract_no"); 
		
		//var_dump($assocArray);die;
		$cbo_company=str_replace("'","",$cbo_company_id);
		$cbo_issue=str_replace("'","",$cbo_issue_banking);
		$lc_type_id=str_replace("'","",$cbo_lc_type_id);
		$txt_lc_no=str_replace("'","",$txt_lc_no);
		$cbo_payterm_id=str_replace("'","",$cbo_payterm_id);
		
		$cbo_bonded=str_replace("'","",$cbo_bonded_warehouse);
		$from_date=str_replace("'","",$txt_date_from);
		$to_date=str_replace("'","",$txt_date_to);
		$cbo_supplier=str_replace("'","",$cbo_supplier);
		$txt_search_common=str_replace("'","",$txt_search_common);
		$cbo_status=str_replace("'","",$cbo_status);
		$cbo_reference_close=str_replace("'","",$cbo_reference_close);

		$reference_close_cond='';
		if ($cbo_reference_close == 1)
			$reference_close_cond = " and a.ref_closing_status=1";
		else if ($cbo_reference_close == 2) 
			$reference_close_cond = " and a.ref_closing_status=0";
		else $reference_close_cond = " and a.ref_closing_status in(0,1)";
		$reference_close=array(1=>"Yes",2=>"No");
		$sql_cond="";
		$supply_so=str_pad(str_replace("'","",$cbo_supply_source), 2, "0", STR_PAD_LEFT);
		if (str_replace("'","",$cbo_supply_source)>0) $sql_cond .=" and a.lc_category='".$supply_so."'";
		if ($cbo_company!="") $sql_cond .=" and a.importer_id in($cbo_company) ";
		if ($cbo_issue>0) $sql_cond .=" and a.issuing_bank_id=$cbo_issue ";
		if ($item_category_id>0) $sql_cond .=" and a.pi_entry_form=".$category_wise_entry_form[$item_category_id];
		
		if ($lc_type_id>0) $sql_cond .=" and a.lc_type_id=$lc_type_id ";
		if ($cbo_bonded>0) $sql_cond .=" and a.bonded_warehouse=$cbo_bonded ";
		if ($cbo_payterm_id>0) $sql_cond .=" and a.payterm_id=$cbo_payterm_id ";
		if ($cbo_supplier>0) $sql_cond .=" and a.supplier_id=$cbo_supplier ";
		if($txt_lc_no!="") $sql_cond .=" and a.lc_number = '$txt_lc_no'";		
		//print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));
		$cbo_based_on=str_replace("'","",$cbo_based_on);
		$bank_array=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
		$lc_date_cond=" and b.lc_date between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
		$amnd_date_cond=" and c.AMENDMENT_DATE between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
		$maturity_dt_cond=" and c.MATURITY_DATE between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
		$payment_date_cond=" and c.PAYMENT_DATE between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
		$invo_date_cond=" and d.INVOICE_DATE between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
		$bank_acc_date_cond=" and d.BANK_ACC_DATE between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
	

		//BTB LC Open (With TT & RTGS) Start--

		$btb_bank = "SELECT B.ID, b.ISSUING_BANK_ID, B.LC_VALUE
		from COM_BTB_LC_MASTER_DETAILS B 
		WHERE b.IMPORTER_ID in ($cbo_company) $lc_date_cond AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 
		AND B.LC_DATE IS NOT NULL and b.ISSUING_BANK_ID>0";
		//echo $btb_bank;
		$btb_bank_result=sql_select($btb_bank);
		$bank_arr=array();
		$lc_val_arr=array();
		foreach($btb_bank_result as $row)
		{
			$bank_arr[$row['ISSUING_BANK_ID']] = $row['ISSUING_BANK_ID'];
			$all_btb_id_arr[$row['ID']] = $row['ID'];
			$lc_val_arr[$row['ISSUING_BANK_ID']]['LC_VALUE'] += $row['LC_VALUE'];
		}

		//BTB LC Amendment
		$btb_lc_amnd_sql = "SELECT C.ID, b.ISSUING_BANK_ID, B.LC_VALUE,C.BTB_LC_VALUE,c.AMENDMENT_VALUE
		from COM_BTB_LC_MASTER_DETAILS B ,COM_BTB_LC_AMENDMENT c
		WHERE b.id = c.BTB_ID and b.IMPORTER_ID in ($cbo_company) $amnd_date_cond AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0  and c.status_active=1 and c.is_deleted=0
		AND c.AMENDMENT_DATE IS NOT NULL and b.ISSUING_BANK_ID>0";
		//echo $btb_lc_amnd_sql;
		$btb_lc_amnd_result=sql_select($btb_lc_amnd_sql);
		$all_btb_amnd_arr=array();
		$btb_amnd_lc_val=array();

		foreach($btb_lc_amnd_result as $row)
		{
			$bank_arr[$row['ISSUING_BANK_ID']] = $row['ISSUING_BANK_ID'];
			$all_btb_amnd_arr[$row['ID']] = $row['ID'];
			//$btb_amnd_lc_val[$row['ISSUING_BANK_ID']]['LC_VALUE'] = $row['BTB_LC_VALUE'];
			$btb_amnd_lc_val[$row['ISSUING_BANK_ID']]['LC_VALUE'] += $row['AMENDMENT_VALUE'];
		}

		//BTB LC ACCP payment to be Paid

		$sql_acc_paid = "SELECT B.ID,D.ID AS DTLS_ID, b.ISSUING_BANK_ID, B.LC_VALUE,d.CURRENT_ACCEPTANCE_VALUE
		from COM_BTB_LC_MASTER_DETAILS B ,COM_IMPORT_INVOICE_MST c,COM_IMPORT_INVOICE_DTLS D
		WHERE B.ID=D.BTB_LC_ID and D.IMPORT_INVOICE_ID = C.id and b.IMPORTER_ID in ($cbo_company) $maturity_dt_cond AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0  and c.status_active=1 and c.is_deleted=0 and D.status_active=1 and D.is_deleted=0 and c.MATURITY_DATE is not null and b.ISSUING_BANK_ID>0";
		//echo $sql_acc_paid;


		$btb_acc_paid_result=sql_select($sql_acc_paid);
		$all_btb_accp_arr=array();
		$btb_accp_lc_val=array();

		foreach($btb_acc_paid_result as $row)
		{
			$bank_arr[$row['ISSUING_BANK_ID']] = $row['ISSUING_BANK_ID'];
			$all_btb_accp_arr[$row['ID']] = $row['ID'];
			$all_btb_accp_dtls_arr[$row['DTLS_ID']] = $row['DTLS_ID'];
			$btb_accp_lc_val[$row['ISSUING_BANK_ID']]['LC_VALUE'] += $row['CURRENT_ACCEPTANCE_VALUE'];
		}


		//BTB Value Paid (With TT & RTGS)

		$sql_paid = "SELECT B.ID, b.ISSUING_BANK_ID, B.LC_VALUE,C.ID AS MST_ID,D.ACCEPTED_AMMOUNT AS PAYMENT_VALUE
		from COM_BTB_LC_MASTER_DETAILS B ,COM_IMPORT_PAYMENT_COM_MST c,COM_IMPORT_PAYMENT_COM d
		WHERE B.ID=C.LC_ID AND C.ID = D.MST_ID  and b.IMPORTER_ID in ($cbo_company) $payment_date_cond AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.PAYMENT_DATE is not null and b.ISSUING_BANK_ID>0
		union all
		SELECT B.ID, b.ISSUING_BANK_ID, B.LC_VALUE,C.ID AS MST_ID,D.ACCEPTED_AMMOUNT AS PAYMENT_VALUE
		from COM_BTB_LC_MASTER_DETAILS B ,COM_IMPORT_PAYMENT_MST c,COM_IMPORT_PAYMENT D
		WHERE B.ID=C.LC_ID AND C.ID = D.MST_ID  and b.IMPORTER_ID in ($cbo_company) $payment_date_cond AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0  and c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 and c.PAYMENT_DATE is not null and b.ISSUING_BANK_ID>0
		union all
		SELECT B.ID, b.ISSUING_BANK_ID, B.LC_VALUE,D.ID AS MST_ID,C.CURRENT_ACCEPTANCE_VALUE AS PAYMENT_VALUE
		from COM_BTB_LC_MASTER_DETAILS B ,COM_IMPORT_INVOICE_DTLS c,COM_IMPORT_INVOICE_MST d
		WHERE B.ID=D.BTB_LC_ID and c.IMPORT_INVOICE_ID = d.id and c.IS_LC =2 and b.IMPORTER_ID in ($cbo_company) $bank_acc_date_cond AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0  and c.status_active=1 and c.is_deleted=0 AND d.STATUS_ACTIVE=1 AND d.IS_DELETED=0 and d.BANK_ACC_DATE is not null and b.ISSUING_BANK_ID>0";
		//echo $sql_paid;
		$btb_sql_paid_result=sql_select($sql_paid);
		$all_btb_paymnt_arr=array();
		$btb_paymnt_lc_val=array();

		foreach($btb_sql_paid_result as $row)
		{
			$bank_arr[$row['ISSUING_BANK_ID']] = $row['ISSUING_BANK_ID'];
			$all_btb_paymnt_arr[$row['ID']] = $row['ID'];
			$all_btb_paymnt_mst_arr[$row['MST_ID']] = $row['MST_ID'];
			//$btb_paymnt_lc_val[$row['ISSUING_BANK_ID']]['LC_VALUE'] = $row['LC_VALUE'];
			$btb_paymnt_lc_val[$row['ISSUING_BANK_ID']]['LC_VALUE'] += $row['PAYMENT_VALUE'];
		}

		//Accepted BTB LC
	
		$sql_acctd_paid = "SELECT B.ID, b.ISSUING_BANK_ID, B.LC_VALUE,C.ID as DTLS_ID,c.CURRENT_ACCEPTANCE_VALUE 
		from COM_BTB_LC_MASTER_DETAILS B , COM_IMPORT_INVOICE_DTLS  c ,COM_IMPORT_INVOICE_MST D
		WHERE B.ID=D.BTB_LC_ID and c.IMPORT_INVOICE_ID = d.id  and b.IMPORTER_ID in ($cbo_company) $bank_acc_date_cond 
		and b.PAYTERM_ID<>3 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0  and c.status_active=1 and c.is_deleted=0 and D.status_active=1 and D.is_deleted=0 and D.BANK_ACC_DATE is not null and b.ISSUING_BANK_ID>0";
		//echo $sql_acctd_paid;
		$btb_accptd_paid_result=sql_select($sql_acctd_paid);
		$all_btb_accptd_arr=array();
		$btb_accptd_lc_val=array();

		foreach($btb_accptd_paid_result as $row)
		{
			$bank_arr[$row['ISSUING_BANK_ID']] = $row['ISSUING_BANK_ID'];
			$all_btb_accptd_arr[$row['ID']] = $row['ID'];
			$all_btb_accptd_dtls_arr[$row['DTLS_ID']] = $row['DTLS_ID'];
			//$btb_accptd_lc_val[$row['ISSUING_BANK_ID']]['LC_VALUE'] = $row['LC_VALUE'];
			$btb_accptd_lc_val[$row['ISSUING_BANK_ID']]['LC_VALUE'] += $row['CURRENT_ACCEPTANCE_VALUE'];
		}

		//Not Yet Accepted as on (Current Date)
		$sql_not_accp = "SELECT B.ID, b.ISSUING_BANK_ID, B.LC_VALUE
		from COM_BTB_LC_MASTER_DETAILS B
		WHERE B.ID NOT IN (SELECT C.BTB_LC_ID FROM COM_IMPORT_INVOICE_DTLS C WHERE c.status_active=1 and c.is_deleted=0 )  and b.IMPORTER_ID in ($cbo_company) AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 and b.ISSUING_BANK_ID>0 and b.LC_DATE is not null";
		//echo $sql_not_accp;
		$btb_not_accp_result=sql_select($sql_not_accp);
		$all_btb_not_accp_arr=array();
		$btb_not_accp_lc_val=array();

		foreach($btb_not_accp_result as $row)
		{
			$bank_count[$row['ISSUING_BANK_ID']] = $row['ISSUING_BANK_ID'];
			$bank_arr[$row['ISSUING_BANK_ID']] = $row['ISSUING_BANK_ID'];
			$all_btb_not_accp_arr[$row['ID']] = $row['ID'];
			$btb_not_accp_lc_val[$row['ISSUING_BANK_ID']]['LC_VALUE'] += $row['LC_VALUE'];
		}

		ob_start();
		$total_bnk_count = count($bank_count);
		$div_width=270+($total_bnk_count*70);
		$tbl_width=250+($total_bnk_count*70);
		?>
		<div id="scroll_body" align="center" style="height:auto; width:<? echo $div_width;?>px; margin:0 auto; padding-left:10px;">
			<table width="<? echo $div_width;?>">
				<?
				$company_library=sql_select("select id, company_name, plot_no, level_no,road_no,city from lib_company where id in(".$cbo_company.")");
				foreach( $company_library as $row)
				{
					$company_name.=$row[csf('company_name')].", ";
				}
				?>
				<tr>
					<td colspan="29" align="center" style="font-size:22px"><center><strong><? echo rtrim($company_name,", ");?></strong></center></td>
				</tr>
				<tr>
					<td colspan="29" align="center" style="font-size:18px"><center><strong><u><? echo $report_title; ?></u></strong></center></td>
				</tr>
			</table>
	
			<table cellspacing="0" width="<? echo $tbl_width;?>"  border="1" rules="all" class="rpt_table" align="left">
			<br>
			   <thead>
               		<tr>
                        <th width="130">Particulars</th>
                        <th width="40">No L/C</th>
						<?foreach($bank_arr as $row)
						{
						?>
						<th width="70" align="right"><?echo $bank_array[$row] ?></th>
						<?}?>
						<th width="80">Bank Total</th>
                    </tr>
			   </thead>
			   <tbody>
						<tr bgcolor="#E9F3FF" onclick="change_color('tbl4_1','<? echo $bgcolor; ?>')" id="tbl4_1;?>">
							<td align="left" valign="middle">BTB LC Open (With TT & RTGS)</td>
							<td align="right" > <a href="##" onClick="openmypage_lc_popup(1,'<?= $cbo_company; ?>','<?= implode(',',$all_btb_id_arr); ?>','BTB LC Open (With TT and RTGS)','<?=$to_date;?>','<?=$from_date;?>')" ><p><?echo count($all_btb_id_arr); ?></p></a></td>

							<?
							foreach($bank_arr as $all_bank)
							{?>
							<td align="right"> <?echo number_format($lc_val_arr[$all_bank]['LC_VALUE'],2);
							$total_btb_bnk_value += $lc_val_arr[$all_bank]['LC_VALUE'];
							?></td>
							<?}?>
							<td align="right"><?echo number_format($total_btb_bnk_value,2)?></td>	
						</tr>	
						
				</tbody>
				<!-- LC amndmnt start -->
				<tbody>
					<tr bgcolor="#FFFFFF" onclick="change_color('tbl4_2','<? echo $bgcolor; ?>')" id="tbl4_2">
						<td align="left" valign="middle">BTB LC Amendment</td>
						<td align="right" > <a href="##" onClick="openmypage_lc_popup(2,'<?= $cbo_company; ?>','<?= implode(',',$all_btb_amnd_arr); ?>','BTB LC Amendment','<?=$to_date;?>','<?=$from_date;?>')" ><p><?echo count($all_btb_amnd_arr); ?></p></a></td><?
						foreach($bank_arr as $all_bank)
						{?>
						<td align="right"> <?echo number_format($btb_amnd_lc_val[$all_bank]['LC_VALUE'],2);
						$total_btb_amnd_bnk_value +=$btb_amnd_lc_val[$all_bank]['LC_VALUE'];
						?></td>
						<?}?>
						<td align="right"><?echo number_format($total_btb_amnd_bnk_value,2)?></td>	
					</tr>	
				</tbody>
				<!-- BTB LC payment to be Paid -->
				<tbody>
					<tr bgcolor="#E9F3FF" onclick="change_color('tbl4_3','<? echo $bgcolor; ?>')" id="tbl4_3">
						<td align="left" valign="middle">BTB LC payment to be Paid</td>
						<td align="right" > <a href="##" onClick="openmypage_lc_popup(3,'<?= $cbo_company; ?>','<?= implode(',',$all_btb_accp_dtls_arr); ?>','BTB LC payment to be Paid','<?=$to_date;?>','<?=$from_date;?>')" ><p><?echo count($all_btb_accp_arr); ?></p></a></td><?
						foreach($bank_arr as $all_bank)
						{?>
						<td align="right"> <?echo number_format($btb_accp_lc_val[$all_bank]['LC_VALUE'],2);
						$total_btb_acc_bnk_value +=$btb_accp_lc_val[$all_bank]['LC_VALUE'];
						?></td>
						<?}?>
						<td align="right"><?echo number_format($total_btb_acc_bnk_value,2)?></td>	
					</tr>	
				</tbody>

				<!-- BTB Value Paid (With TT & RTGS) -->
				<tbody>
					<tr bgcolor="#FFFFFF" onclick="change_color('tbl4_4','<? echo $bgcolor; ?>')" id="tbl4_4">
						<td align="left" valign="middle">BTB Value Paid (With TT & RTGS)</td>
						<td align="right" > <a href="##" onClick="openmypage_lc_popup(4,'<?= $cbo_company; ?>','<?= implode(',',$all_btb_paymnt_mst_arr); ?>','BTB Value Paid (With TT & RTGS)','<?=$to_date;?>','<?=$from_date;?>')" ><p><?echo count($all_btb_paymnt_arr); ?></p></a></td><?
						foreach($bank_arr as $all_bank)
						{?>
						<td align="right"> <?echo number_format($btb_paymnt_lc_val[$all_bank]['LC_VALUE'],2);
						$total_btb_pymnt_value += $btb_paymnt_lc_val[$all_bank]['LC_VALUE'];
						?></td>
						<?}?>
						<td align="right"><?echo number_format($total_btb_pymnt_value,2);?></td>	
					</tr>	
				</tbody>

				<!-- Accepted BTB LC -->
				<tbody>
					<tr bgcolor="#E9F3FF" onclick="change_color('tbl4_5','<? echo $bgcolor; ?>')" id="tbl4_5">
						<td align="left" valign="middle">Accepted BTB LC</td>
						<td align="right" > <a href="##" onClick="openmypage_lc_popup(5,'<?= $cbo_company; ?>','<?= implode(',',$all_btb_accptd_dtls_arr); ?>','BTB LC payment to be Paid','<?=$to_date;?>','<?=$from_date;?>')" ><p><?echo count($all_btb_accptd_arr); ?></p></a></td><?
						foreach($bank_arr as $all_bank)
						{?>
						<td align="right"> <?echo number_format($btb_accptd_lc_val[$all_bank]['LC_VALUE'],2);
						$total_btb_acptd_value += $btb_accptd_lc_val[$all_bank]['LC_VALUE'];
						?></td>
						<?}?>
						<td align="right"><? echo number_format($total_btb_acptd_value,2)?></td>	
					</tr>	
				</tbody>
				

				<!-- Not Yet Accepted as on (Current Date)-->
				<tbody>
					<tr bgcolor="#FFFFFF" onclick="change_color('tbl4_6','<? echo $bgcolor; ?>')" id="tbl4_6">
						<td align="left" valign="middle">Not Yet Accepted as on (Current Date)</td>
						<td align="right" > <a href="##" onClick="openmypage_lc_popup(6,'<?= $cbo_company; ?>','<?= implode(',',$all_btb_not_accp_arr); ?>','Not Yet Accepted as on (Current Date)','<?=$to_date;?>','<?=$from_date;?>')" ><p><?echo count($all_btb_not_accp_arr); ?></p></a></td><?
						foreach($bank_arr as $all_bank)
						{?>
						<td align="right"> <?echo number_format($btb_not_accp_lc_val[$all_bank]['LC_VALUE'],2);
						$total_btb_not_acp_value += $btb_not_accp_lc_val[$all_bank]['LC_VALUE'];
						?></td>
						<?}?>
						<td align="right"><? echo number_format($total_btb_not_acp_value,2)?></td>	
					</tr>	
				</tbody>
			</table>
		</div>
		<?
	}



	$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form=129 and ref_from in (1,2)");
	oci_commit($con);
	disconnect($con);
    
    foreach (glob("$user_id*.xls") as $filename) 
    {
        if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w') or die('canot open');
    $is_created = fwrite($create_new_doc,ob_get_contents()) or die('canot write');
    echo "$html****$filename****$report_type****$currencyId";
    exit();
}

if($action=="btb_lc_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $report_type."__".$company_id."__".$from_date."__".$to_date."__".$title; die();

	$com_name = return_library_array( "SELECT id, company_name from lib_company","id","company_name");

	if($buyer_id){$buyer_id_cond=" and a.BUYER_ID=$buyer_id";}
	if($buyer_id){$buyer_id=" and e.BUYER_ID=$buyer_id";}

	if($from_date!='' && $to_date!=''){
		$payment_date_cond=" and c.PAYMENT_DATE between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
		$bank_acc_date_cond=" and d.BANK_ACC_DATE between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
	}

	$con = connect();
	$rid=execute_query("delete from GBL_TEMP_ENGINE where entry_form=129 and user_id=$user_id");
	if($rid) oci_commit($con);
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML>'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
		}	
	</script>	
	<?
	if($report_type==1)  //BTB LC Open (With TT and RTGS)
	{
		
		//echo $all_btb; die();
		$explodeval = explode(',',$all_btb);
		foreach($explodeval as $row){
			$btb_lc_id_arr1[$row] = $row;
		}
		if(!empty($btb_lc_id_arr1))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 129, 3, $btb_lc_id_arr1,$empty_arr);

			$btb_sql ="SELECT A.LC_SC_ID,B.ID AS BTB_ID,B.LC_DATE, B.LC_VALUE, B.LC_NUMBER,D.ITEM_CATEGORY_ID
			from GBL_TEMP_ENGINE G , COM_BTB_LC_MASTER_DETAILS B 
			left join COM_BTB_LC_PI C on B.ID = C.COM_BTB_LC_MASTER_DETAILS_ID
			left join COM_PI_MASTER_DETAILS d on C.PI_ID =d.id and d.status_active=1 and d.is_deleted=0
			left join COM_BTB_EXPORT_LC_ATTACHMENT A on A.IMPORT_MST_ID =B.id and A.status_active=1 and A.is_deleted=0
			where B.ID=G.REF_VAL
			and b.status_active=1 and b.is_deleted=0 AND G.USER_ID= $user_id AND G.ENTRY_FORM=129 AND G.REF_FROM=3 order by b.LC_DATE desc";
			//echo $btb_sql;
			$btb_sql_result=sql_select($btb_sql);
			$btb_arr=array();
			$btb_lc_sc_id_arr=array();
			foreach ($btb_sql_result as $row)
			{
				$btb_arr[$row['BTB_ID']]['LC_DATE'] = $row['LC_DATE'];
				$btb_arr[$row['BTB_ID']]['LC_VALUE'] = $row['LC_VALUE'];
				$btb_arr[$row['BTB_ID']]['LC_NUMBER'] = $row['LC_NUMBER'];
				$btb_arr[$row['BTB_ID']]['ITEM_CATEGORY_ID'] .=  $item_category[$row['ITEM_CATEGORY_ID']].",";
				//$btb_arr[$row['LC_NUMBER']]['PI_NUMBER'] .= $row['PI_NUMBER'].",";
				$btb_lc_sc_id_arr[$row["LC_SC_ID"]]=$row["LC_SC_ID"];
			}
		}

		if(!empty($btb_lc_sc_id_arr))
		{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 129, 4, $btb_lc_sc_id_arr,$empty_arr);
		
		$lc_sc_sql ="SELECT A.LC_NUMBER,c.EXPORT_LC_NO as LC_SC_NO
		FROM COM_BTB_LC_MASTER_DETAILS A, COM_BTB_EXPORT_LC_ATTACHMENT B, COM_EXPORT_LC C,GBL_TEMP_ENGINE G
		WHERE B.LC_SC_ID = G.REF_VAL AND A.ID=B.IMPORT_MST_ID AND B.LC_SC_ID=C.ID AND B.IS_LC_SC=0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND G.USER_ID= $user_id AND G.ENTRY_FORM=129 AND G.REF_FROM=4
		UNION ALL
		SELECT A.LC_NUMBER,c.CONTRACT_NO as LC_SC_NO
		FROM COM_BTB_LC_MASTER_DETAILS A, COM_BTB_EXPORT_LC_ATTACHMENT B, COM_SALES_CONTRACT C,GBL_TEMP_ENGINE G
		WHERE B.LC_SC_ID = G.REF_VAL AND A.ID=B.IMPORT_MST_ID AND B.LC_SC_ID=C.ID AND B.IS_LC_SC=1 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND  G.USER_ID= $user_id AND G.ENTRY_FORM=129 AND G.REF_FROM=4";
		//echo  $lc_sc_sql;
		$lc_sc_sql_result=sql_select($lc_sc_sql);
		$lc_sc_arr_arr=array();
		foreach ($lc_sc_sql_result as $row)
		{
			$lc_sc_arr_arr[$row['LC_NUMBER']]['LC_SC_NO'] = $row['LC_SC_NO'];
		}
		}
		?>
		<fieldset style="width:640px; margin-left:10px">
			<p align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
			<br />
			<div id="report_container" align="center" style="width:640px">
				<table class="rpt_table" border="1" rules="all" width="640" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<th width="50">SL No</th>
							<th width="80">BTB Open Date</th>
							<th width="90">BTB No</th>
							<th width="180">Item Category</th>
							<th width="110">Export LC No</th>
							<th >BTB Value</th> 
						</tr>
					</thead>
					<tbody>
						<?
							$i=1;
							foreach($btb_arr as $row)  
							{
								if ($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?=$i;?>">
									<td align="center"><? echo $i; ?></td>
									<td align="center"><? echo change_date_format($row['LC_DATE']); ?></td>
									<td><p><? echo $row['LC_NUMBER']; ?></p></td>
									<td align="left"><p><? echo implode(',',array_unique(explode(',',rtrim($row['ITEM_CATEGORY_ID'],',')))); ?></p></td>
									<td align="left"><p><? echo $lc_sc_arr_arr[$row['LC_NUMBER']]['LC_SC_NO']; ?></p></td>
									<td align="right"><p><? echo number_format($row['LC_VALUE'],2); ?></p></td>
								</tr>
								<?
								$i++;
								$total_btb_value+=$row['LC_VALUE'];
							}
						?>
					</tbody>   
					<tfoot>
						<tr>
							<th colspan="5">Total :</th>
							<th><?=number_format($total_btb_value,2);?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
	}
	if($report_type==2) //BTB LC Amendment
	{
		//echo $all_btb; die();
		$explodeval = explode(',',$all_btb);
		foreach($explodeval as $row){
			$btb_lc_id_arr2[$row] = $row;
		}
		if(!empty($btb_lc_id_arr2))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 129, 5, $btb_lc_id_arr2,$empty_arr);

			$amnd_btb_sql ="SELECT B.ID AS BTB_ID,e.AMENDMENT_DATE,e.AMENDMENT_NO,e.AMENDMENT_VALUE,e.BTB_LC_VALUE, B.LC_VALUE, B.LC_NUMBER,E.ID AS AMNDMNT_ID ,D.ITEM_CATEGORY_ID
			from  GBL_TEMP_ENGINE G, COM_BTB_LC_MASTER_DETAILS B ,COM_BTB_LC_AMENDMENT e
			left join COM_BTB_LC_PI C on e.BTB_ID = C.COM_BTB_LC_MASTER_DETAILS_ID
			left join COM_PI_MASTER_DETAILS d on C.PI_ID =d.id and d.status_active=1 and d.is_deleted=0
			where G.REF_VAL=E.ID and e.BTB_ID =b.id 
			AND  b.IMPORTER_ID in ($company_id)
			AND b.status_active=1 and b.is_deleted=0  and b.ISSUING_BANK_ID>0 and e.status_active=1 and e.is_deleted=0  and e.AMENDMENT_DATE IS NOT NULL AND G.USER_ID= $user_id AND G.ENTRY_FORM=129 AND G.REF_FROM=5  order by b.LC_DATE desc";
			//echo $amnd_btb_sql;
			$amnd_btb_sql_result=sql_select($amnd_btb_sql);
			$amnd_btb_arr=array();
			$amnd_btb_id_arr=array();
			foreach ($amnd_btb_sql_result as $row)
			{
				$amnd_btb_arr[$row['AMNDMNT_ID']]['AMENDMENT_DATE'] = $row['AMENDMENT_DATE'];
				$amnd_btb_arr[$row['AMNDMNT_ID']]['AMENDMENT_NO'] = $row['AMENDMENT_NO'];
				$amnd_btb_arr[$row['AMNDMNT_ID']]['AMENDMENT_VALUE'] = $row['AMENDMENT_VALUE'];
				$amnd_btb_arr[$row['AMNDMNT_ID']]['BTB_LC_VALUE'] = $row['BTB_LC_VALUE'];
				$amnd_btb_arr[$row['AMNDMNT_ID']]['LC_VALUE'] = $row['LC_VALUE'];
				$amnd_btb_arr[$row['AMNDMNT_ID']]['LC_NUMBER'] = $row['LC_NUMBER'];
				$amnd_btb_arr[$row['AMNDMNT_ID']]['ITEM_CATEGORY_ID'] .= $item_category[$row['ITEM_CATEGORY_ID']].",";
				//$amnd_btb_arr[$row['LC_NUMBER']]['PI_NUMBER'] .= $row['PI_NUMBER'].",";
				$amnd_btb_id_arr[$row["BTB_ID"]]=$row["BTB_ID"];
			}
		}
		
		if(!empty($amnd_btb_id_arr))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 129, 6, $amnd_btb_id_arr,$empty_arr);
			
			$lc_sc_sql ="SELECT A.LC_NUMBER,c.EXPORT_LC_NO as LC_SC_NO
			FROM COM_BTB_LC_MASTER_DETAILS A, COM_BTB_EXPORT_LC_ATTACHMENT B, COM_EXPORT_LC C,GBL_TEMP_ENGINE G
			WHERE A.ID = G.REF_VAL AND A.ID=B.IMPORT_MST_ID AND B.LC_SC_ID=C.ID AND B.IS_LC_SC=0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND G.USER_ID= $user_id AND G.ENTRY_FORM=129 AND G.REF_FROM=6
			UNION ALL
			SELECT A.LC_NUMBER,c.CONTRACT_NO as LC_SC_NO
			FROM COM_BTB_LC_MASTER_DETAILS A, COM_BTB_EXPORT_LC_ATTACHMENT B, COM_SALES_CONTRACT C,GBL_TEMP_ENGINE G
			WHERE A.ID = G.REF_VAL AND A.ID=B.IMPORT_MST_ID AND B.LC_SC_ID=C.ID AND B.IS_LC_SC=1 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND  G.USER_ID= $user_id AND G.ENTRY_FORM=129 AND G.REF_FROM=6";
			//ECHO $lc_sc_sql;
			$lc_sc_sql_result=sql_select($lc_sc_sql);
			$lc_sc_arr_arr=array();
			foreach ($lc_sc_sql_result as $row)
			{
				$lc_sc_arr_arr[$row['LC_NUMBER']]['LC_SC_NO'] .= $row['LC_SC_NO'].",";
			}
		}
		?>
		<fieldset style="width:840px; margin-left:10px">
			<p align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
			<br />
			<div id="report_container" align="center" style="width:840px">
				<table class="rpt_table" border="1" rules="all" width="840" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<th width="50">SL No</th>
							<th width="80">Amendment Date</th>
							<th width="50">Amendment No</th>
							<th width="90">BTB No</th>
							<th width="170">Export LC/SC No</th>
							<th width="180">Item Category</th>
							<th width="100">BTB Amendment Value</th>
							<th >BTB Value</th> 
						</tr>
					</thead>
					<tbody>
						<?
							$i=1;
							foreach($amnd_btb_arr as $row)  
							{
								if ($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?=$i;?>">
									<td align="center"><? echo $i; ?></td>
									<td align="center"><? echo change_date_format($row['AMENDMENT_DATE']); ?></td>
									<td align="center"><p><? echo $row['AMENDMENT_NO']; ?></p></td>
									<td align="left"><p><? echo $row['LC_NUMBER']; ?></p></td>
									<td align="left"><p><? echo implode(',',array_unique(explode(',',rtrim($lc_sc_arr_arr[$row['LC_NUMBER']]['LC_SC_NO'],',')))); ?></p></td>
									<td align="left"><p><? echo implode(',',array_unique(explode(',',rtrim($row['ITEM_CATEGORY_ID'],',')))); ?></p></td>
									<td align="right"><p><? echo number_format($row['AMENDMENT_VALUE'],2); ?></p></td>
									<td align="right"><p><? echo number_format($row['BTB_LC_VALUE'],2); ?></p></td>
								</tr>
								<?
								$i++;
								$total_amnd_value+=$row['AMENDMENT_VALUE'];
								$total_btb_value+=$row['BTB_LC_VALUE'];
							}
						?>
					</tbody>   
					<tfoot>
						<tr>
							<th colspan="6">Total :</th>
							<th><?=number_format($total_amnd_value,2);?></th>
							<th><?=number_format($total_btb_value,2);?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
	}
	if($report_type==3) //BTB LC payment to be Paid
	{
		//echo $all_btb; die();
		$explodeval = explode(',',$all_btb);
		foreach($explodeval as $row){
			$btb_lc_id_arr3[$row] = $row;
		}
		if(!empty($btb_lc_id_arr3))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 129, 7, $btb_lc_id_arr3,$empty_arr);

			$accp_btb_sql ="SELECT B.ID AS BTB_ID,C.MATURITY_DATE, B.LC_DATE AS BTB_DATE,B.LC_NUMBER,C.ID AS INV_ID ,F.ITEM_CATEGORY_ID,C.BANK_REF
			FROM  GBL_TEMP_ENGINE G, COM_BTB_LC_MASTER_DETAILS B,COM_IMPORT_INVOICE_MST C,COM_IMPORT_INVOICE_DTLS D
			LEFT JOIN COM_BTB_LC_PI E ON D.BTB_LC_ID = E.COM_BTB_LC_MASTER_DETAILS_ID
			LEFT JOIN COM_PI_MASTER_DETAILS F ON E.PI_ID =F.ID AND F.STATUS_ACTIVE=1 AND F.IS_DELETED=0
			WHERE G.REF_VAL = D.ID AND B.ID=D.BTB_LC_ID and D.IMPORT_INVOICE_ID = C.ID
			AND  B.IMPORTER_ID IN ($company_id) $mat_date_cond
			AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND  C.STATUS_ACTIVE=1 AND C.MATURITY_DATE IS NOT NULL AND G.USER_ID= $user_id AND G.ENTRY_FORM=129 AND G.REF_FROM=7
			GROUP BY B.ID,C.MATURITY_DATE,B.LC_DATE, B.LC_NUMBER,C.ID,F.ITEM_CATEGORY_ID,C.BANK_REF
			order by C.MATURITY_DATE desc";
			//echo $accp_btb_sql;
			$accp_btb_sql_result=sql_select($accp_btb_sql);
			$accp_btb_arr=array();
			$btb_id_arr=array();
			$inv_id_arr=array();
			foreach ($accp_btb_sql_result as $row)
			{
				$accp_btb_arr[$row['INV_ID']]['MATURITY_DATE'] = $row['MATURITY_DATE'];
				//$accp_btb_arr[$row['INV_ID']]['CURRENT_ACCEPTANCE_VALUE'] += $row['CURRENT_ACCEPTANCE_VALUE'];
				$accp_btb_arr[$row['INV_ID']]['LC_NUMBER'] = $row['LC_NUMBER'];
				$accp_btb_arr[$row['INV_ID']]['BANK_REF'] = $row['BANK_REF'];
				$accp_btb_arr[$row['INV_ID']]['INV_ID'] = $row['INV_ID'];
				$accp_btb_arr[$row['INV_ID']]['BTB_DATE'] = $row['BTB_DATE'];
				$accp_btb_arr[$row['INV_ID']]['ITEM_CATEGORY_ID'] .= $item_category[$row['ITEM_CATEGORY_ID']].",";
				$btb_id_arr[$row["BTB_ID"]]=$row["BTB_ID"];
				$inv_id_arr[$row["INV_ID"]]=$row["INV_ID"];
			}
		}
		if(!empty($btb_id_arr))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 129, 8, $btb_id_arr,$empty_arr);
		
			$lc_sc_sql ="SELECT A.LC_NUMBER,c.EXPORT_LC_NO as LC_SC_NO
			FROM COM_BTB_LC_MASTER_DETAILS A, COM_BTB_EXPORT_LC_ATTACHMENT B, COM_EXPORT_LC C,GBL_TEMP_ENGINE G
			WHERE A.ID = G.REF_VAL AND A.ID=B.IMPORT_MST_ID AND B.LC_SC_ID=C.ID AND B.IS_LC_SC=0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND G.USER_ID= $user_id AND G.ENTRY_FORM=129 AND G.REF_FROM=8
			UNION ALL
			SELECT A.LC_NUMBER,c.CONTRACT_NO as LC_SC_NO
			FROM COM_BTB_LC_MASTER_DETAILS A, COM_BTB_EXPORT_LC_ATTACHMENT B, COM_SALES_CONTRACT C,GBL_TEMP_ENGINE G
			WHERE A.ID = G.REF_VAL AND A.ID=B.IMPORT_MST_ID AND B.LC_SC_ID=C.ID AND B.IS_LC_SC=1 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND  G.USER_ID= $user_id AND G.ENTRY_FORM=129 AND G.REF_FROM=8";
			//echo $lc_sc_sql;
			$lc_sc_sql_result=sql_select($lc_sc_sql);

			$lc_sc_arr_arr=array();
			foreach ($lc_sc_sql_result as $row)
			{
				$lc_sc_arr_arr[$row['LC_NUMBER']]['LC_SC_NO'] .= $row['LC_SC_NO'].",";
			}
		}

		if(!empty($inv_id_arr))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 129, 9, $inv_id_arr,$empty_arr);
		
			$accp_sql = "SELECT A.ID AS MST_ID , B.CURRENT_ACCEPTANCE_VALUE 
			FROM COM_IMPORT_INVOICE_MST A, COM_IMPORT_INVOICE_DTLS B, GBL_TEMP_ENGINE G 
			WHERE A.ID = G.REF_VAL AND A.ID =B.IMPORT_INVOICE_ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0  AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND  G.USER_ID= $user_id AND G.ENTRY_FORM=129 AND G.REF_FROM=9";
			//echo $accp_sql;
			$accp_sql_result=sql_select($accp_sql);
			$curr_acc_arr=array();
			foreach ($accp_sql_result as $row)
			{
				$curr_acc_arr[$row['MST_ID']]['CURRENT_ACCEPTANCE_VALUE'] += $row['CURRENT_ACCEPTANCE_VALUE'];
			}
		}
		?>
		<fieldset style="width:840px; margin-left:10px">
			<p align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
			<br />
			<div id="report_container" align="center" style="width:840px">
				<table class="rpt_table" border="1" rules="all" width="840" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<th width="50">SL No</th>
							<th width="90">BTB No</th>
							<th width="80">LC Opening Date</th>
							<th width="80">Maturity Date</th>
							<th width="140">Export LC/SC No</th>
							<th width="170">Item Category</th>
							<th width="100">Accepted Value</th>
							<th >Bank Ref No</th> 
						</tr>
					</thead>
					<tbody>
						<?
							$i=1;
							foreach($accp_btb_arr as $row)  
							{
								if ($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?=$i;?>">
									<td align="center"><? echo $i; ?></td>
									<td align="left"><p><? echo $row['LC_NUMBER']; ?></p></td>
									<td align="center"><? echo change_date_format($row['BTB_DATE']); ?></td>
									<td align="center"><? echo change_date_format($row['MATURITY_DATE']); ?></td>
									<td align="left"><p><? echo implode(',',array_unique(explode(',',rtrim($lc_sc_arr_arr[$row['LC_NUMBER']]['LC_SC_NO'],',')))); ?></p></td>
									<td align="left"><p><? echo implode(',',array_unique(explode(',',rtrim($row['ITEM_CATEGORY_ID'],',')))); ?></p></td>
									<td align="right"><p><? echo number_format($curr_acc_arr[$row['INV_ID']]['CURRENT_ACCEPTANCE_VALUE'],2); 
									$total_acc_value+=$curr_acc_arr[$row['INV_ID']]['CURRENT_ACCEPTANCE_VALUE'];
									?></p></td>
									<td align="right"><p><? echo $row['BANK_REF']; ?></p></td>
								</tr>
								<?
								$i++;
								
					
							}
						?>
					</tbody>   
					<tfoot>
						<tr>
							<th colspan="6">Total :</th>
							<th><?=number_format($total_acc_value,2);?></th>
							<th></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
	}
	if($report_type==4) //BTB Value Paid (With TT & RTGS)
	{
		//echo $all_btb; die();
		$explodeval = explode(',',$all_btb);
		$btb_lc_id_arr4 =array();
		foreach($explodeval as $row){
			$btb_lc_id_arr4[$row] = $row;
		}
		if(!empty($btb_lc_id_arr4))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 129, 10, $btb_lc_id_arr4,$empty_arr);
	

			$sql_paid = "SELECT B.ID AS BTB_ID, B.LC_DATE AS BTB_DATE,B.LC_NUMBER as LC_NUMBER,C.ID AS MST_ID,c.PAYMENT_DATE as PAYMENT_DATE,D.ACCEPTED_AMMOUNT AS PAYMENT_VALUE,C.INVOICE_ID AS INV_ID
			from  GBL_TEMP_ENGINE G,COM_BTB_LC_MASTER_DETAILS B ,COM_IMPORT_PAYMENT_COM_MST c,COM_IMPORT_PAYMENT_COM D
			WHERE G.REF_VAL = C.ID AND B.ID=C.LC_ID AND C.ID = D.MST_ID and b.IMPORTER_ID in ($company_id) AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $payment_date_cond and c.PAYMENT_DATE is not null and b.ISSUING_BANK_ID>0 AND G.USER_ID= $user_id AND G.ENTRY_FORM=129 AND G.REF_FROM=10
			union all
			SELECT B.ID AS BTB_ID, B.LC_DATE AS BTB_DATE,B.LC_NUMBER as LC_NUMBER,C.ID AS MST_ID,c.PAYMENT_DATE as PAYMENT_DATE,D.ACCEPTED_AMMOUNT AS PAYMENT_VALUE,C.INVOICE_ID AS INV_ID
			from  GBL_TEMP_ENGINE G,COM_BTB_LC_MASTER_DETAILS B ,COM_IMPORT_PAYMENT_MST c,COM_IMPORT_PAYMENT D
			WHERE G.REF_VAL = C.ID AND B.ID=C.LC_ID AND C.ID = D.MST_ID and b.IMPORTER_ID in ($company_id) AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $payment_date_cond and c.PAYMENT_DATE is not null and b.ISSUING_BANK_ID>0 AND G.USER_ID= $user_id AND G.ENTRY_FORM=129 AND G.REF_FROM=10
			union all
			SELECT B.ID AS BTB_ID, B.LC_DATE AS BTB_DATE,B.LC_NUMBER as LC_NUMBER,D.ID AS MST_ID,d.BANK_ACC_DATE as PAYMENT_DATE,C.CURRENT_ACCEPTANCE_VALUE AS PAYMENT_VALUE,D.ID AS INV_ID
			from  GBL_TEMP_ENGINE G, COM_BTB_LC_MASTER_DETAILS B ,COM_IMPORT_INVOICE_DTLS c,COM_IMPORT_INVOICE_MST d
			WHERE G.REF_VAL = D.ID AND B.ID=D.BTB_LC_ID and c.IMPORT_INVOICE_ID = d.id and c.IS_LC =2 and b.IMPORTER_ID in ($company_id) AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0  and c.status_active=1 and c.is_deleted=0 $bank_acc_date_cond AND d.STATUS_ACTIVE=1 AND d.IS_DELETED=0 and d.BANK_ACC_DATE is not null and b.ISSUING_BANK_ID>0 AND G.USER_ID= $user_id AND G.ENTRY_FORM=129 AND G.REF_FROM=10 order by PAYMENT_DATE";

			//echo $sql_paid;

			$pymnt_btb_sql_result=sql_select($sql_paid);
			$paymnt_btb_arr=array();
			$btb_id_arr=array();
			$inv_id_arr=array();
			foreach ($pymnt_btb_sql_result as $row)
			{
				$paymnt_btb_arr[$row['MST_ID']]['PAYMENT_DATE'] = $row['PAYMENT_DATE'];
				$paymnt_btb_arr[$row['MST_ID']]['LC_NUMBER'] = $row['LC_NUMBER'];
				$paymnt_btb_arr[$row['MST_ID']]['BANK_REF'] = $row['BANK_REF'];
				$paymnt_btb_arr[$row['MST_ID']]['PAYMENT_VALUE'] += $row['PAYMENT_VALUE'];
				$paymnt_btb_arr[$row['MST_ID']]['BTB_DATE'] = $row['BTB_DATE'];
				$paymnt_btb_arr[$row['MST_ID']]['INV_ID'] = $row['INV_ID'];
				$paymnt_btb_arr[$row['MST_ID']]['BTB_ID'] = $row['BTB_ID'];
				$btb_id_arr[$row["BTB_ID"]]=$row["BTB_ID"];
				$inv_id_arr[$row["INV_ID"]]=$row["INV_ID"];
			}
		}
		if(!empty($btb_id_arr))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 129, 11, $btb_id_arr,$empty_arr);
		
			$lc_sc_sql ="SELECT A.LC_NUMBER,c.EXPORT_LC_NO as LC_SC_NO
			FROM COM_BTB_LC_MASTER_DETAILS A, COM_BTB_EXPORT_LC_ATTACHMENT B, COM_EXPORT_LC C,GBL_TEMP_ENGINE G
			WHERE A.ID = G.REF_VAL AND A.ID=B.IMPORT_MST_ID AND B.LC_SC_ID=C.ID AND B.IS_LC_SC=0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND G.USER_ID= $user_id AND G.ENTRY_FORM=129 AND G.REF_FROM=11
			UNION ALL
			SELECT A.LC_NUMBER,c.CONTRACT_NO as LC_SC_NO
			FROM COM_BTB_LC_MASTER_DETAILS A, COM_BTB_EXPORT_LC_ATTACHMENT B, COM_SALES_CONTRACT C,GBL_TEMP_ENGINE G
			WHERE A.ID = G.REF_VAL AND A.ID=B.IMPORT_MST_ID AND B.LC_SC_ID=C.ID AND B.IS_LC_SC=1 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND  G.USER_ID= $user_id AND G.ENTRY_FORM=129 AND G.REF_FROM=11";
			//echo $lc_sc_sql;
			$lc_sc_sql_result=sql_select($lc_sc_sql);
			$lc_sc_arr_arr=array();
			foreach ($lc_sc_sql_result as $row)
			{
				$lc_sc_arr_arr[$row['LC_NUMBER']]['LC_SC_NO'] .= $row['LC_SC_NO'].",";
			}

			$itm_cat_sql ="SELECT COM_BTB_LC_MASTER_DETAILS_ID AS BTB_ID , B.ITEM_CATEGORY_ID
			FROM  GBL_TEMP_ENGINE G,  COM_BTB_LC_PI A,COM_PI_MASTER_DETAILS B  
			WHERE G.REF_VAL = A.COM_BTB_LC_MASTER_DETAILS_ID AND A.PI_ID =B.ID 
			AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND G.USER_ID= $user_id AND G.ENTRY_FORM=129 AND G.REF_FROM=11";
			//echo $itm_cat_sql;
			$itm_cat_sql_result=sql_select($itm_cat_sql);
			foreach ($itm_cat_sql_result as $row)
			{
				$itm_cat_arr[$row['BTB_ID']]['ITEM_CATEGORY_ID'] .= $item_category[$row['ITEM_CATEGORY_ID']].",";
			}

		}

		if(!empty($inv_id_arr))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 129, 12, $inv_id_arr,$empty_arr);
		
			$accp_sql = "SELECT A.ID AS MST_ID , A.BANK_REF
			FROM COM_IMPORT_INVOICE_MST A, GBL_TEMP_ENGINE G 
			WHERE A.ID = G.REF_VAL AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND G.USER_ID= $user_id AND G.ENTRY_FORM=129 AND G.REF_FROM=12";
			//echo $accp_sql;
			$accp_sql_result=sql_select($accp_sql);
			$curr_acc_arr=array();
			foreach ($accp_sql_result as $row)
			{
				$bank_ref_arr[$row['MST_ID']]['BANK_REF'] = $row['BANK_REF'];
			}
		}



		?>
		<fieldset style="width:840px; margin-left:10px">
			<p align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
			<br />
			<div id="report_container" align="center" style="width:840px">
				<table class="rpt_table" border="1" rules="all" width="840" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<th width="50">SL No</th>
							<th width="90">BTB LC No</th>
							<th width="80">BTB LC Date</th>
							<th width="80">Payment Date</th>
							<th width="130">Export LC/SC No</th>
							<th width="180">Item Category</th>
							<th width="100">Paid Amount</th>
							<th >Bank Ref No</th> 
						</tr>
					</thead>
					<tbody>
						<?
							$i=1;
							foreach($paymnt_btb_arr as $row)  
							{
								if ($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?=$i;?>">
									<td align="center"><? echo $i; ?></td>
									<td align="left"><p><? echo $row['LC_NUMBER']; ?></p></td>
									<td align="center"><? echo change_date_format($row['BTB_DATE']); ?></td>
									<td align="center"><? echo change_date_format($row['PAYMENT_DATE']); ?></td>
									<td align="left"><p><? echo implode(',',array_unique(explode(',',rtrim($lc_sc_arr_arr[$row['LC_NUMBER']]['LC_SC_NO'],',')))); ?></p></td>
									<td align="left"><p><? echo implode(',',array_unique(explode(',',rtrim($itm_cat_arr[$row['BTB_ID']]['ITEM_CATEGORY_ID'],',')))); ?></p></td>
									<td align="right"><p><? echo number_format($row['PAYMENT_VALUE'],2); 
									$total_paid_value+=$row['PAYMENT_VALUE'];
									?></p></td>
									<td align="left"><p><? echo $bank_ref_arr[$row['INV_ID']]['BANK_REF']; ?></p></td>
								</tr>
								<?
								$i++;
								
					
							}
						?>
					</tbody>   
					<tfoot>
						<tr>
							<th colspan="6">Total :</th>
							<th><?=number_format($total_paid_value,2);?></th>
							<th></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
	}
	if($report_type==5) //Accepted BTB LC
	{
		//echo $all_btb; die();
		$explodeval = explode(',',$all_btb);
		foreach($explodeval as $row){
			$btb_lc_id_arr5[$row] = $row;
		}
		if(!empty($btb_lc_id_arr5))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 129, 13, $btb_lc_id_arr5,$empty_arr);

			$accp_btb_sql ="SELECT B.ID AS BTB_ID,c.BANK_ACC_DATE,B.LC_DATE AS BTB_DATE,B.LC_NUMBER,C.ID AS INV_ID ,F.ITEM_CATEGORY_ID,C.BANK_REF
			FROM  GBL_TEMP_ENGINE G, COM_BTB_LC_MASTER_DETAILS B,COM_IMPORT_INVOICE_MST C,COM_IMPORT_INVOICE_DTLS D
			LEFT JOIN COM_BTB_LC_PI E ON D.BTB_LC_ID = E.COM_BTB_LC_MASTER_DETAILS_ID
			LEFT JOIN COM_PI_MASTER_DETAILS F ON E.PI_ID =F.ID AND F.STATUS_ACTIVE=1 AND F.IS_DELETED=0
			WHERE G.REF_VAL = D.ID AND B.ID=D.BTB_LC_ID and D.IMPORT_INVOICE_ID = C.ID
			AND  B.IMPORTER_ID IN ($company_id) $mat_date_cond
			AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND  C.STATUS_ACTIVE=1 AND C.BANK_ACC_DATE IS NOT NULL AND G.USER_ID= $user_id AND G.ENTRY_FORM=129 AND G.REF_FROM=13
			GROUP BY B.ID,C.BANK_ACC_DATE,B.LC_DATE, B.LC_NUMBER,C.ID,F.ITEM_CATEGORY_ID,C.BANK_REF
			order by C.BANK_ACC_DATE desc";

			$accp_btb_sql ="SELECT B.ID AS BTB_ID,c.BANK_ACC_DATE,B.LC_DATE AS BTB_DATE,B.LC_NUMBER,C.ID AS INV_ID ,F.ITEM_CATEGORY_ID,C.BANK_REF
			FROM  GBL_TEMP_ENGINE G, COM_BTB_LC_MASTER_DETAILS B,COM_IMPORT_INVOICE_MST C,COM_IMPORT_INVOICE_DTLS D
			LEFT JOIN COM_BTB_LC_PI E ON D.BTB_LC_ID = E.COM_BTB_LC_MASTER_DETAILS_ID
			LEFT JOIN COM_PI_MASTER_DETAILS F ON E.PI_ID =F.ID AND F.STATUS_ACTIVE=1 AND F.IS_DELETED=0
			WHERE G.REF_VAL = D.ID AND B.ID=D.BTB_LC_ID and D.IMPORT_INVOICE_ID = C.ID
			and b.PAYTERM_ID<>3
			AND  B.IMPORTER_ID IN ($company_id) $mat_date_cond
			AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND  C.STATUS_ACTIVE=1 AND C.BANK_ACC_DATE IS NOT NULL AND G.USER_ID= $user_id AND G.ENTRY_FORM=129 AND G.REF_FROM=13
			GROUP BY B.ID,C.BANK_ACC_DATE,B.LC_DATE, B.LC_NUMBER,C.ID,F.ITEM_CATEGORY_ID,C.BANK_REF
			order by C.BANK_ACC_DATE desc";

			//echo $accp_btb_sql;
			$accp_btb_sql_result=sql_select($accp_btb_sql);
			$accp_btb_arr=array();
			$btb_id_arr=array();
			$inv_id_arr=array();
			foreach ($accp_btb_sql_result as $row)
			{
				//$accp_btb_arr[$row['INV_ID']]['INVOICE_DATE'] = $row['INVOICE_DATE'];
				$accp_btb_arr[$row['INV_ID']]['BANK_ACC_DATE'] = $row['BANK_ACC_DATE'];
				//$accp_btb_arr[$row['INV_ID']]['CURRENT_ACCEPTANCE_VALUE'] += $row['CURRENT_ACCEPTANCE_VALUE'];
				$accp_btb_arr[$row['INV_ID']]['LC_NUMBER'] = $row['LC_NUMBER'];
				$accp_btb_arr[$row['INV_ID']]['BANK_REF'] = $row['BANK_REF'];
				$accp_btb_arr[$row['INV_ID']]['INV_ID'] = $row['INV_ID'];
				$accp_btb_arr[$row['INV_ID']]['BTB_DATE'] = $row['BTB_DATE'];
				$accp_btb_arr[$row['INV_ID']]['ITEM_CATEGORY_ID'] .= $item_category[$row['ITEM_CATEGORY_ID']].",";
				$btb_id_arr[$row["BTB_ID"]]=$row["BTB_ID"];
				$inv_id_arr[$row["INV_ID"]]=$row["INV_ID"];
			}
		}
		if(!empty($btb_id_arr))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 129, 14, $btb_id_arr,$empty_arr);
		
			$lc_sc_sql ="SELECT A.LC_NUMBER,c.EXPORT_LC_NO as LC_SC_NO
			FROM COM_BTB_LC_MASTER_DETAILS A, COM_BTB_EXPORT_LC_ATTACHMENT B, COM_EXPORT_LC C,GBL_TEMP_ENGINE G
			WHERE A.ID = G.REF_VAL AND A.ID=B.IMPORT_MST_ID AND B.LC_SC_ID=C.ID AND B.IS_LC_SC=0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND G.USER_ID= $user_id AND G.ENTRY_FORM=129 AND G.REF_FROM=14
			UNION ALL
			SELECT A.LC_NUMBER,c.CONTRACT_NO as LC_SC_NO
			FROM COM_BTB_LC_MASTER_DETAILS A, COM_BTB_EXPORT_LC_ATTACHMENT B, COM_SALES_CONTRACT C,GBL_TEMP_ENGINE G
			WHERE A.ID = G.REF_VAL AND A.ID=B.IMPORT_MST_ID AND B.LC_SC_ID=C.ID AND B.IS_LC_SC=1 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND  G.USER_ID= $user_id AND G.ENTRY_FORM=129 AND G.REF_FROM=14";
			//echo $lc_sc_sql;
			$lc_sc_sql_result=sql_select($lc_sc_sql);
			$lc_sc_arr_arr=array();
			foreach ($lc_sc_sql_result as $row)
			{
				$lc_sc_arr_arr[$row['LC_NUMBER']]['LC_SC_NO'] .= $row['LC_SC_NO'].",";
			}
		}

		if(!empty($inv_id_arr))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 129, 15, $inv_id_arr,$empty_arr);
		
			$accp_sql = "SELECT A.ID AS MST_ID , B.CURRENT_ACCEPTANCE_VALUE 
			FROM COM_IMPORT_INVOICE_MST A, COM_IMPORT_INVOICE_DTLS B, GBL_TEMP_ENGINE G 
			WHERE A.ID = G.REF_VAL AND A.ID =B.IMPORT_INVOICE_ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0  AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND  G.USER_ID= $user_id AND G.ENTRY_FORM=129 AND G.REF_FROM=15";
			//echo $accp_sql;
			$accp_sql_result=sql_select($accp_sql);
			$curr_acc_arr=array();
			foreach ($accp_sql_result as $row)
			{
				$curr_acc_arr[$row['MST_ID']]['CURRENT_ACCEPTANCE_VALUE'] += $row['CURRENT_ACCEPTANCE_VALUE'];
			}
		}
		?>
		<fieldset style="width:840px; margin-left:10px">
			<p align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
			<br />
			<div id="report_container" align="center" style="width:840px">
				<table class="rpt_table" border="1" rules="all" width="840" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<th width="50">SL No</th>
							<th width="90">BTB LC No</th>
							<th width="80">BTB LC Date</th>
							<th width="80">Accepted Date</th>
							<th width="140">Export LC/SC No</th>
							<th width="170">Item Category</th>
							<th width="100">Accepted Value</th>
							<th >Bank Ref No</th> 
						</tr>
					</thead>
					<tbody>
						<?
							$i=1;
							foreach($accp_btb_arr as $row)  
							{
								if ($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?=$i;?>">
									<td align="center"><? echo $i; ?></td>
									<td align="left"><p><? echo $row['LC_NUMBER']; ?></p></td>
									<td align="center"><? echo change_date_format($row['BTB_DATE']); ?></td>
									<td align="center"><? echo change_date_format($row['BANK_ACC_DATE']); ?></td>
									<td align="left"><p><? echo implode(',',array_unique(explode(',',rtrim($lc_sc_arr_arr[$row['LC_NUMBER']]['LC_SC_NO'],',')))); ?></p></td>
									<td align="left"><p><? echo implode(',',array_unique(explode(',',rtrim($row['ITEM_CATEGORY_ID'],',')))); ?></p></td>
									<td align="right"><p><? echo number_format($curr_acc_arr[$row['INV_ID']]['CURRENT_ACCEPTANCE_VALUE'],2); 
									$total_acc_value+=$curr_acc_arr[$row['INV_ID']]['CURRENT_ACCEPTANCE_VALUE'];
									?></p></td>
									<td align="left"><p><? echo $row['BANK_REF']; ?></p></td>
								</tr>
								<?
								$i++;
								
					
							}
						?>
					</tbody>   
					<tfoot>
						<tr>
							<th colspan="6">Total :</th>
							<th><?=number_format($total_acc_value,2);?></th>
							<th></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
	}
	if($report_type==6) //Not Yet Accepted as on (Current Date)
	{
		//echo $all_btb; die();
		$explodeval = explode(',',$all_btb);
		foreach($explodeval as $row){
			$btb_lc_id_arr6[$row] = $row;
		}
		if(!empty($btb_lc_id_arr6))
		{
			
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 129, 16, $btb_lc_id_arr6,$empty_arr);

			$not_accp_btb_sql ="SELECT B.ID AS BTB_ID, B.LC_DATE AS BTB_DATE,B.LC_NUMBER,B.LC_VALUE
			FROM  GBL_TEMP_ENGINE G, COM_BTB_LC_MASTER_DETAILS B
			WHERE G.REF_VAL = B.ID 
			AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 and b.LC_DATE is not null AND  b.ISSUING_BANK_ID>0 AND G.USER_ID= $user_id AND G.ENTRY_FORM=129 AND G.REF_FROM=16
			order by B.LC_DATE desc";
			//echo $not_accp_btb_sql;
			$not_accp_btb_sql_result=sql_select($not_accp_btb_sql);
			$not_accp_btb_arr=array();
			$btb_id_arr=array();
			$inv_id_arr=array();
			foreach ($not_accp_btb_sql_result as $row)
			{
				$not_accp_btb_arr[$row['BTB_ID']]['LC_NUMBER'] = $row['LC_NUMBER'];
				$not_accp_btb_arr[$row['BTB_ID']]['INV_ID'] = $row['INV_ID'];
				$not_accp_btb_arr[$row['BTB_ID']]['BTB_DATE'] = $row['BTB_DATE'];
				$not_accp_btb_arr[$row['BTB_ID']]['BTB_ID'] = $row['BTB_ID'];
				$not_accp_btb_arr[$row['BTB_ID']]['BTB_VALUE'] = $row['LC_VALUE'];
				$not_accp_btb_arr[$row['BTB_ID']]['ITEM_CATEGORY_ID'] .= $item_category[$row['ITEM_CATEGORY_ID']].",";
				$btb_id_arr[$row["BTB_ID"]]=$row["BTB_ID"];
			}
		}
		if(!empty($btb_id_arr))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 129, 17, $btb_id_arr,$empty_arr);
		
			$lc_sc_sql ="SELECT A.LC_NUMBER,c.EXPORT_LC_NO as LC_SC_NO
			FROM COM_BTB_LC_MASTER_DETAILS A, COM_BTB_EXPORT_LC_ATTACHMENT B, COM_EXPORT_LC C,GBL_TEMP_ENGINE G
			WHERE A.ID = G.REF_VAL AND A.ID=B.IMPORT_MST_ID AND B.LC_SC_ID=C.ID AND B.IS_LC_SC=0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND G.USER_ID= $user_id AND G.ENTRY_FORM=129 AND G.REF_FROM=17
			UNION ALL
			SELECT A.LC_NUMBER,c.CONTRACT_NO as LC_SC_NO
			FROM COM_BTB_LC_MASTER_DETAILS A, COM_BTB_EXPORT_LC_ATTACHMENT B, COM_SALES_CONTRACT C,GBL_TEMP_ENGINE G
			WHERE A.ID = G.REF_VAL AND A.ID=B.IMPORT_MST_ID AND B.LC_SC_ID=C.ID AND B.IS_LC_SC=1 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND  G.USER_ID= $user_id AND G.ENTRY_FORM=129 AND G.REF_FROM=17";
			//echo $lc_sc_sql;
			$lc_sc_sql_result=sql_select($lc_sc_sql);
			$lc_sc_arr_arr=array();
			foreach ($lc_sc_sql_result as $row)
			{
				$lc_sc_arr_arr[$row['LC_NUMBER']]['LC_SC_NO'] .= $row['LC_SC_NO'].",";
			}

			$itm_cat_sql ="SELECT COM_BTB_LC_MASTER_DETAILS_ID AS BTB_ID , B.ITEM_CATEGORY_ID
			FROM  GBL_TEMP_ENGINE G,  COM_BTB_LC_PI A,COM_PI_MASTER_DETAILS B  
			WHERE G.REF_VAL = A.COM_BTB_LC_MASTER_DETAILS_ID AND A.PI_ID =B.ID 
			AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND G.USER_ID= $user_id AND G.ENTRY_FORM=129 AND G.REF_FROM=17";
			//echo $itm_cat_sql;
			$itm_cat_sql_result=sql_select($itm_cat_sql);
			foreach ($itm_cat_sql_result as $row)
			{
				$itm_cat_arr[$row['BTB_ID']]['ITEM_CATEGORY_ID'] .= $item_category[$row['ITEM_CATEGORY_ID']].",";
			}

		}

	
		?>
		<fieldset style="width:650px; margin-left:10px">
			<p align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
			<br />
			<div id="report_container" align="center" style="width:650px">
				<table class="rpt_table" border="1" rules="all" width="650" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<th width="50">SL No</th>
							<th width="90">BTB LC No</th>
							<th width="80">BTB LC Date</th>
							<th width="140">Export LC/SC No</th>
							<th width="170">Item Category</th>
							<th width="100">Not Yet Accepted Value</th>
						</tr>
					</thead>
					<tbody>
						<?
							$i=1;
							foreach($not_accp_btb_arr as $row)  
							{
								if ($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?=$i;?>">
									<td align="center"><? echo $i; ?></td>
									<td align="left"><p><? echo $row['LC_NUMBER']; ?></p></td>
									<td align="center"><? echo change_date_format($row['BTB_DATE']); ?></td>
									<td align="left"><p><? echo implode(',',array_unique(explode(',',rtrim($lc_sc_arr_arr[$row['LC_NUMBER']]['LC_SC_NO'],',')))); ?></p></td>
									<td align="left"><p><? echo implode(',',array_unique(explode(',',rtrim($itm_cat_arr[$row['BTB_ID']]['ITEM_CATEGORY_ID'],',')))); ?></p></td>
									<td align="right"><p><? echo number_format($row['BTB_VALUE'],2); 
									$total_not_acc_value+=$row['BTB_VALUE'];
									?></p></td>
								</tr>
								<?
								$i++;
								
					
							}
						?>
					</tbody>   
					<tfoot>
						<tr>
							<th colspan="5">Total :</th>
							<th><?=number_format($total_not_acc_value,2);?></th>
							<th></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
	}
	// echo $sql;
	$result=sql_select($sql); 
	?>
	

	<?

	$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form=129");
	oci_commit($con);
	disconnect($con);
    exit();
}



if($action=="show_file")
{
    echo load_html_head_contents("Invoice File","../../../", 1, 1, $unicode);
    extract($_REQUEST);

    $pi_cond = " AND master_tble_id IN ('" . str_replace(",", "','", $pi_id) . "')";
    // $data_array_pi=sql_select("select IMAGE_LOCATION, REAL_FILE_NAME from common_photo_library where master_tble_id='$pi_id' and form_name='proforma_invoice' and is_deleted=0 and file_type=2");
	$data_array_pi=sql_select("select IMAGE_LOCATION, REAL_FILE_NAME from common_photo_library where form_name='proforma_invoice'  $pi_cond and is_deleted=0 and file_type=2");

    $data_array_btb=sql_select("select IMAGE_LOCATION, REAL_FILE_NAME from common_photo_library where master_tble_id='$btb_id' and form_name='BTBMargin LC' and is_deleted=0 and file_type=2");
    $data_array_acceptance=sql_select("select IMAGE_LOCATION, REAL_FILE_NAME from common_photo_library where master_tble_id='$acceptance_invoice_id' and form_name='importdocumentacceptance_1' and is_deleted=0 and file_type=2");

	$data_array_amendment=sql_select("select IMAGE_LOCATION, REAL_FILE_NAME from common_photo_library where master_tble_id='$btb_id' and form_name='BTBMargin LC Amendment' and is_deleted=0 and file_type=2");
    ?>
    <!-- <style type="text/css">
        li { list-style: none; font-size: 9pt; margin-top: 0px; margin-left: 7px; float: left; width: 89px;}
    </style> -->
    <table width="700" class="rpt_table" border="1" rules="all"  style="margin-left: 2px">
		<tr>
			<td width="150" align="center">PI Attached File</td>
			<td width="150" align="center">BTB LC Attached File</td>
			<td width="150" align="center">Import Document Acceptance Attached File</td>
			<td width="150" align="center">BTB/Margin LC Amendment</td>
		</tr>
		<tr style="background-color: seashell;">
			<td width="150" align="center"><?
				foreach ($data_array_pi as $row)
				{           
					?>        
					<li>
						<a href="../../../<? echo $row['IMAGE_LOCATION']; ?>" target="_new">
						<img src="../../../file_upload/blank_file.png" height="60" width="70"></a>
						<br>
						<p style="width: 70px; word-break: break-all; margin-top: 1px; font-size: 11px;"><? echo $row['REAL_FILE_NAME']; ?></p>
					</li>
					<?
				}
				?> 
			</td>
			<td width="150" align="center">
				<?
				foreach ($data_array_btb as $row)
				{           
					?>        
					<li>
						<a href="../../../<? echo $row['IMAGE_LOCATION']; ?>" target="_new">
						<img src="../../../file_upload/blank_file.png" height="60" width="70"></a>
						<br>
						<p style="width: 70px; word-break: break-all; margin-top: 1px; font-size: 11px;"><? echo $row['REAL_FILE_NAME']; ?></p>
					</li>
					<?
				}
				?> 
			</td>
			<td width="150" align="center">
				<?
				foreach ($data_array_acceptance as $row)
				{           
					?>        
					<li>
						<a href="../../../<? echo $row['IMAGE_LOCATION']; ?>" target="_new">
						<img src="../../../file_upload/blank_file.png" height="60" width="70"></a>
						<br>
						<p style="width: 70px; word-break: break-all; margin-top: 1px; font-size: 11px;"><? echo $row['REAL_FILE_NAME']; ?></p>
					</li>
					<?
				}
				?>  
			</td>
			<td width="150" align="center">
				<?
				foreach ($data_array_amendment as $row)
				{           
					?>        
					<li>
						<a href="../../../<? echo $row['IMAGE_LOCATION']; ?>" target="_new">
						<img src="../../../file_upload/blank_file.png" height="60" width="70"></a>
						<br>
						<p style="width: 70px; word-break: break-all; margin-top: 1px; font-size: 11px;"><? echo $row['REAL_FILE_NAME']; ?></p>
					</li>
					<?
				}
				?>    
			</td>
		</tr>
    </table>
    <?
}

if($action=="show_file_btb")
{
    echo load_html_head_contents("Invoice File","../../../", 1, 1, $unicode);
    extract($_REQUEST);
    $data_array_btb=sql_select("select IMAGE_LOCATION, REAL_FILE_NAME from common_photo_library where master_tble_id='$btb_sys_id' and form_name='BTBMargin LC' and is_deleted=0 and file_type=2");
	$data_array_amendment=sql_select("select IMAGE_LOCATION, REAL_FILE_NAME from common_photo_library where master_tble_id='$btb_id' and form_name='BTBMargin LC Amendment' and is_deleted=0 and file_type=2");
    ?>
    <style type="text/css">
        li { list-style: none; font-size: 9pt; margin-top: 0px; margin-left: 7px; float: left; width: 89px;}
    </style>
    <table width="100%">
        <tr><td style="font-size: 13px;">BTB LC Attached File</td></tr>
        <tr>
            <td width="100%" height="128" style="vertical-align: top;">         
            <?
            foreach ($data_array_btb as $row)
            {           
                ?>        
                <li>
                    <a href="../../../<? echo $row['IMAGE_LOCATION']; ?>" target="_new">
                    <img src="../../../file_upload/blank_file.png" height="60" width="70"></a>
                    <br>
                    <p style="width: 70px; word-break: break-all; margin-top: 1px; font-size: 11px;"><? echo $row['REAL_FILE_NAME']; ?></p>
                </li>
                <?
            }
            ?>        
            </td>
        </tr>
		<tr><td style="font-size: 13px;">Amendment Attached File</td></tr>
        <tr>
            <td width="100%" height="128" style="vertical-align: top;">         
            <?
            foreach ($data_array_amendment as $row)
            {           
                ?>        
                <li>
                    <a href="../../../<? echo $row['IMAGE_LOCATION']; ?>" target="_new">
                    <img src="../../../file_upload/blank_file.png" height="60" width="70"></a>
                    <br>
                    <p style="width: 70px; word-break: break-all; margin-top: 1px; font-size: 11px;"><? echo $row['REAL_FILE_NAME']; ?></p>
                </li>
                <?
            }
            ?>        
            </td>
        </tr>
    </table>
    <?
}

if($action=="lc_details")
{
    echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
	?>  
	<script>
		
		function window_close()
		{
			parent.emailwindow.hide();
		}
		
	</script>   
	<div style="width:570px" align="center" id="scroll_body" >
	<fieldset style="width:100%; margin-left:10px" >
	<!--<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>&nbsp;&nbsp;&nbsp;&nbsp;-->
		<input type="button" value="Close" onClick="window_close()" style="width:100px"  class="formbutton"/>
		<div style="width:550px" id="report_container" align="center">
		<?
		if($int_file!="")
		{
			?>
			<table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
				<thead>
					<th width="250">File Number : </th>&nbsp;<th><? echo $int_file; ?></th>
				</thead>
			</table>
			<?
		}
				$ls_sql="select a.export_lc_no, a.expiry_date from com_export_lc a, com_btb_lc_master_details b, com_btb_export_lc_attachment c where a.id=c.lc_sc_id and c.import_mst_id=b.id and  b.id=$lc_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0  and c.is_lc_sc=0";
				
				$result_ls_sql=sql_select($ls_sql);
				if(count($result_ls_sql)>0)
				{
			?>
			<table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
				<thead>
					<th width="40">SL</th>
					<th width="200">SC/LC Number</th>
					<th width="200">Expiry Date</th>                    
				</thead>
				<tbody>
				<?
					$i=1;
					foreach( $result_ls_sql as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor ; ?>">
							<td><? echo $i; ?></td>
							<td><? echo $row[csf("export_lc_no")]; ?></td>
							<td>&nbsp;<? echo change_date_format($row[csf("expiry_date")]); ?></td>
						</tr>
					<?
					}
				?>
				</tbody>
			</table>
			<?
				}
			?>
			<br />
			<?
				$sc_sql="select a.contract_no, a.expiry_date from com_sales_contract a, com_btb_lc_master_details b, com_btb_export_lc_attachment c where a.id=c.lc_sc_id and c.import_mst_id=b.id and  b.id=$lc_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and c.is_lc_sc =1";
				$result_sc_sql=sql_select($sc_sql);
				if(count($result_sc_sql)>0)
				{
			?>
			<table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
				<thead>
					<th width="40">SL</th>
					<th width="200">SC/LC Number</th>
					<th width="200">Expiry Date</th>                    
				</thead>
				<tbody>
				<?
					$i=1;
					foreach( $result_sc_sql as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor ; ?>">
							<td><? echo $i; ?></td>
							<td><? echo $row[csf("contract_no")]; ?></td>
							<td>&nbsp;<? echo change_date_format($row[csf("expiry_date")]); ?></td>
						</tr>
					<?
					}
				?>
				</tbody>
			</table>
			<?
				}
			?>
		</div>
	</fieldset>

	</div>
	<?  
}

if($action == "receive_return_details")
{
    echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    //echo "test".$item_group_ids;die;
    //print_r ($pi_id);
	//  echo $item_cate_array."_".$reaceive_basis;
	// if(!in_array($item_cate_array,$item_category) && !in_array($reaceive_basis,$basis) )
    $supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
    $companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
	$user_library=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
	$store_name=return_library_array( "select id, store_name from lib_store_location", "id", "store_name"  );
	$basis=array(1,2);
	$item_category = array(2,8,10,11,13,15,21);

	
		$pi_wo_ref=explode("__",$pi_ids);
		$all_pi_id=$pi_wo_ref[0];
		$all_wo_id=$pi_wo_ref[1];
		// $wo_po_id=explode("*", $all_wo_id);

		if(!empty($all_wo_id)){
			$all_wo_id_arr=explode(",",$all_wo_id);
		}else if(!empty($all_pi_id)){
			$all_wo_id_arr=explode(",",$all_pi_id);
		}

		$all_wo_id_arr=explode(",",$all_wo_id);
		foreach($all_wo_id_arr as $val)
		{
			$allWoId=explode("*",$val);
		
				$wo_ids.=$allWoId[0].',';		
		}			
		$wo_id=rtrim($wo_ids,",");

	   	$sql = "select b.pi_id, b.received_id, a.cons_quantity as cons_quantity, a.cons_rate, a.order_rate, a.cons_amount, b.issue_number, b.issue_date, a.pi_wo_batch_no, a.receive_basis, a.prod_id
		from product_details_master p, inv_transaction a, inv_issue_master b
		where p.id=a.prod_id and a.mst_id = b.id and b.status_active = 1 and a.status_active = 1 and a.transaction_type = 3 and a.pi_wo_batch_no in ($wo_id) and b.company_id = $company_name and p.ITEM_GROUP_ID in($item_group_ids)
		order by b.pi_id"; //group by b.pi_id ,b.issue_number, b.issue_date // and b.item_category = 1
		//echo $sql;
		$result_rtn=sql_select($sql);	
	
		// $sql = "select b.pi_id, b.received_id, a.cons_quantity as cons_quantity, a.cons_rate, a.order_rate, a.cons_amount, b.issue_number, b.issue_date
		// from product_details_master p, inv_transaction a, inv_issue_master b
		// where p.id=a.prod_id and a.mst_id = b.id and b.status_active = 1 and a.status_active = 1 and a.transaction_type = 3 and b.received_id in ($all_mst_id) and b.company_id = $company_name and p.ITEM_GROUP_ID in($item_group_ids)
		// order by b.pi_id"; //group by b.pi_id ,b.issue_number, b.issue_date // and b.item_category = 1
		// $result_rtn=sql_select($sql);
		// echo $sql;die;
    
	?>  
	<script>
		function fnc_print_window()
		{	
			var w = window.open("Surprise2", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('popup_report_container').innerHTML+'</body</html>');
			d.close();
		}
	</script>
	<?ob_start();?>
	<div style="width:800px" align="center" id="scroll_body" >
		<fieldset style="width:100%; margin-left:10px" >
		<div id="popup_report_container" align="center" style="width:100%" > 
			<div id="report_container" align="center" style="width:100%" > 
				<div style="width:800px">
					<table class="rpt_table" border="1" align="center" rules="all" width="100%" cellpadding="0" cellspacing="0">
						<thead bgcolor="#dddddd">
							<tr>

								<th width="30">SL</th>
								<th width="140">PI / WO NO.</th>
								<th width="100">Receive No.</th>
								<th width="70">Receive Date</th>
								<th width="100">Received Store</th>
								<th width="80">Qnty</th>
								<th width="80">Rate</th>
								<th width="80">Amount</th>
								<th >Insert User</th>
							</tr>
						</thead>
						<tbody>
						<?
						$tot_rece_qty=$tot_rec_amount=0;    
						$i=1;
						$pi_wo_ref=explode("__",$pi_ids);
						$all_pi_id=$pi_wo_ref[0];
						$all_wo_id=$pi_wo_ref[1];

						$itemCategoryIDs = explode(',',$item_cate_array);
						//print_r( $itemCategoryIDs );
						foreach ($itemCategoryIDs as $key=> $value) {
						if($value==4){
								$chkCat=1; break;
						}else{
								$chkCat=0;
						}
						}

						//echo $item_cate_array ;die;
						$sql_recvs = "";
						if($all_pi_id!="")
						{
							$pi_wo_sql=sql_select("select id, pi_number, 1 as rcv_basis from com_pi_master_details where status_active=1 and is_deleted=0 and id in($all_pi_id)");
							foreach($pi_wo_sql as $row)
							{
								$piNoArr[$row[csf("rcv_basis")]."**".$row[csf("id")]]=$row[csf("pi_number")];
							}
							//if($chkCat==1 ) $recBasis_cond=' and b.receive_purpose not in(2,43)'; else $recBasis_cond='';

							$sql_recvs .= "SELECT a.receive_basis, a.pi_wo_batch_no, a.cons_quantity as cons_quantity, a.cons_rate, a.order_rate, a.cons_amount, b.recv_number, b.receive_date, b.id as mst_id,b.store_id,b.inserted_by
							from product_details_master p, inv_transaction a, inv_receive_master b
							where p.id=a.prod_id and a.mst_id = b.id and a.transaction_type=1 and b.receive_purpose<>2 and a.item_category in($item_cate_array)
							and a.status_active=1 and a.pi_wo_batch_no in ($all_pi_id) and a.receive_basis=1 and b.company_id = $company_name  and b.status_active=1 $recBasis_cond and p.ITEM_GROUP_ID in($item_group_ids)";
							// and b.receive_purpose not in(2,43)
						}
						
						if($all_wo_id!="")
						{
							//$pi_wo_ref=explode("__",$pi_ids);
							//echo $all_wo_id;die;
							$all_wo_id_arr=explode(",",$all_wo_id);
							$sample_without_wo_id='';$wo_id_trim_arr=$wo_id_general_arr=array();
							$wo_id_trim_arr=$wo_id_general_arr=array();
							foreach($all_wo_id_arr as $val)
							{
								$allWoId=explode("*",$val);
								if($allWoId[1]==1)
								{
									$sample_without_wo_id.=$allWoId[0].',';
								}
								else
								{
									if($allWoId[2]==4)
									{
										$wo_id_trim_arr[$allWoId[0]]=$allWoId[0];
									}
									else
									{
										$wo_id_general_arr[$allWoId[0]]=$allWoId[0];
									}
									
								}
							}
							$sample_without_wo_id=rtrim($sample_without_wo_id,',');

							if($sample_without_wo_id!='')
							{
								$pi_wo_sql=sql_select("SELECT id, booking_no as wo_number, 2 as rcv_basis from wo_non_ord_samp_booking_mst where status_active=1 and is_deleted=0 and id in($sample_without_wo_id)");
								foreach($pi_wo_sql as $row)
								{
									$piNoArr[$row[csf("rcv_basis")]."**".$row[csf("id")]]=$row[csf("wo_number")];
								}
								if($sql_recvs!="") $sql_recvs .=" union all ";
								$sql_recvs .= "SELECT a.receive_basis, a.pi_wo_batch_no, a.cons_quantity as cons_quantity, a.cons_rate, a.order_rate, a.cons_amount, b.recv_number, b.receive_date, b.id as mst_id,b.store_id,b.inserted_by
								from product_details_master p, inv_transaction a, inv_receive_master b
								where p.id=a.prod_id and a.mst_id = b.id and a.transaction_type=1 and a.booking_without_order=1 and b.receive_purpose<>2 and a.item_category in($item_cate_array) and a.status_active=1 and a.pi_wo_batch_no in ($sample_without_wo_id) and a.receive_basis=2 and b.company_id = $company_name and p.ITEM_GROUP_ID in($item_group_ids)";
							}
							
							if(count($wo_id_general_arr)>0 || count($wo_id_trim_arr)>0)
							{
								if(count($wo_id_general_arr)>0)
								{
									$pi_wo_sql=sql_select("SELECT id, wo_number, 2 as rcv_basis from wo_non_order_info_mst where status_active=1 and is_deleted=0 and id in(".implode(",",$wo_id_general_arr).")");
									foreach($pi_wo_sql as $row)
									{
										$piNoArr[$row[csf("rcv_basis")]."**".$row[csf("id")]]=$row[csf("wo_number")];
									}
								}
								
								if(count($wo_id_trim_arr)>0)
								{
									$pi_wo_sql=sql_select("SELECT id, booking_no as wo_number, 2 as rcv_basis from WO_BOOKING_MST where status_active=1 and is_deleted=0 and id in(".implode(",",$wo_id_trim_arr).")");
									foreach($pi_wo_sql as $row)
									{
										$piNoArr[$row[csf("rcv_basis")]."**".$row[csf("id")]]=$row[csf("wo_number")];
									}
								}
								
								$both_wo_id_arr=$wo_id_general_arr+$wo_id_trim_arr;
								//echo "<pre>";print_r($wo_id_general_arr);echo "<pre>";print_r($wo_id_trim_arr);echo "<pre>";print_r($both_wo_id_arr);
								
								if($sql_recvs!="") $sql_recvs .=" union all ";
								if(count($wo_id_general_arr)>0)
								{
									$sql_recvs .= "SELECT a.receive_basis, a.pi_wo_batch_no, a.cons_quantity as cons_quantity, a.cons_rate, a.order_rate, a.cons_amount, b.recv_number, b.receive_date, b.id as mst_id,b.store_id,b.inserted_by, a.prod_id 
									from product_details_master p, inv_transaction a, inv_receive_master b
									where p.id=a.prod_id and a.mst_id = b.id and a.transaction_type=1 and a.booking_without_order=0 and b.receive_purpose<>2 and a.item_category in($item_cate_array) and a.status_active=1 and b.entry_form=20 and a.pi_wo_batch_no in (".implode(",",$wo_id_general_arr).") and a.receive_basis=2 and b.company_id = $company_name and p.ITEM_GROUP_ID in($item_group_ids)";
								}
								
								if($sql_recvs!="") $sql_recvs .=" union all ";
								if(count($wo_id_trim_arr)>0)
								{
									$sql_recvs .= "SELECT a.receive_basis, a.pi_wo_batch_no, a.cons_quantity as cons_quantity, a.cons_rate, a.order_rate, a.cons_amount, b.recv_number, b.receive_date, b.id as mst_id,b.store_id,b.inserted_by,a.prod_id 
									from product_details_master p, inv_transaction a, inv_receive_master b
									where p.id=a.prod_id and a.mst_id = b.id and a.transaction_type=1 and a.booking_without_order=0 and b.receive_purpose<>2 and a.item_category in($item_cate_array) and a.status_active=1 and b.entry_form=24 and a.pi_wo_batch_no in (".implode(",",$wo_id_trim_arr).") and a.receive_basis=2 and b.company_id = $company_name and p.ITEM_GROUP_ID in($item_group_ids)";
								}
								
								//echo $sql_recvs ;
							}
						}
						
						$result=sql_select($sql_recvs);$all_mst_id="";$rcv_wise_wopi=array();
						foreach( $result as $row)
						{
							if($row[csf("recv_number")]!="")
							{
								if ($i%2==0) {$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
								$amount = $row[csf("cons_quantity")]*$row[csf("order_rate")];
								$all_mst_id[$row[csf("mst_id")]]=$row[csf("mst_id")];
								$rcv_wise_wopi[$row[csf("mst_id")]]=$row[csf("pi_wo_batch_no")];
								$rcv_rate_arr[$row[csf("pi_wo_batch_no")]][$row[csf("prod_id")]]['order_rate']=$row[csf("order_rate")];
								?>
								<tr bgcolor="<? echo $bgcolor ; ?>">
									<td align="center"><? echo $i; ?></td>
									<td title="<?= $row[csf("receive_basis")]."**".$row[csf("pi_wo_batch_no")];?>"><? echo $piNoArr[$row[csf("receive_basis")]."**".$row[csf("pi_wo_batch_no")]]; ?></td>
									<td><? echo $row[csf("recv_number")]; ?></td>
									<td align="center">&nbsp;<? echo change_date_format($row[csf("receive_date")]); ?></td>
									<td align="center">&nbsp;<? echo $store_name[$row[csf("store_id")]]; ?></td>
									<td align="right"><? echo number_format($row[csf("cons_quantity")],2); ?></td>
									<td align="right"><? echo number_format($row[csf("order_rate")],2); ?></td>
									<td align="right"><? echo number_format($amount,2); ?></td>
									<td align="right"><? echo $user_library[$row[csf("inserted_by")]]; ?></td>
								</tr>
								<?
								$tot_rece_qty+=$row[csf("cons_quantity")];
								$tot_rec_amount+=$amount;
								$i++;
							}
						}
						$all_mst_id=explode(",",$all_mst_id); 
						
						?>
						</tbody>
					</table>
					<table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
						<tfoot>
							<th width="30">&nbsp;</th>
							<th width="140">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="80"><? if($chkCat==1 ){
								echo ''; 
							}else{
								echo number_format($tot_rece_qty,2);
							}
							?></th>
							<th width="80">&nbsp;</th>
							<th width="80"><? echo number_format($tot_rec_amount,2); ?></th>
							<th >&nbsp;</th>
						</tfoot>         
					</table>
				</div>
			</div>
			<br/>
			<div id="report_container" align="center" style="width:100%" > 
				<div style="width:800px">
					<table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
						<thead bgcolor="#dddddd">
							<tr>
								<th width="30">SL</th>
								<th width="140">PI / WO NO.</th>
								<th width="150">Receive Return No.</th>
								<th width="130">Receive Return Date</th>
								<th width="80">Qnty</th>
								<th width="80">Rate</th>
								<th width="">Amount</th>
							</tr>
						</thead>
						<tbody>
							<?
							//print_r($result_rtn);
							$i=1; $tot_retran_qty=0; $tot_retran_amount=0;
							foreach( $result_rtn as $row)
							{
								
								if($row[csf("issue_number")]!="")
								{
									if ($i%2==0) {$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
									;
									 $amount = $row[csf("cons_quantity")]*$rcv_rate_arr[$row[csf("pi_wo_batch_no")]][$row[csf("prod_id")]]["order_rate"];
									//$amount = $row[csf("cons_amount")];
									?>
									<tr bgcolor="<? echo $bgcolor ; ?>">
										<td align="center"><? echo $i; ?></td> 
										 
										<td><? echo $piNoArr[$row[csf("receive_basis")]."**".$row[csf("pi_wo_batch_no")]];; ?></td>
										<td><? echo $row[csf("issue_number")]; ?></td>
										<td align="center">&nbsp;<? echo change_date_format($row[csf("issue_date")]); ?></td>
										<td align="right"><? echo number_format($row[csf("cons_quantity")],2); ?></td>
										<td align="right"><? echo number_format($rcv_rate_arr[$row[csf("pi_wo_batch_no")]][$row[csf("prod_id")]]["order_rate"],2); ?></td>
										<td align="right"><? echo number_format($amount,2); ?></td>
									</tr> 
									<?  
									$tot_retran_qty+=$row[csf("cons_quantity")];
									$tot_retran_amount+=$amount;
									$i++;
								}
							} 
							?>
						</tbody>
					</table>
					<table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
						<tfoot>
							<th width="30">&nbsp;</th>
							<th width="140">&nbsp;</th>
							<th width="150">&nbsp;</th>
							<th width="130">&nbsp;</th>
							<th width="80"><? if($chkCat==1 ){
								echo ''; 
							}else{
								echo number_format($tot_retran_qty,2);
							}
							?></th>
							<th width="80">&nbsp;</th>
							<th width=""><? echo number_format($tot_retran_amount,2); ?></th>
						</tfoot>         
					</table>
				</div>
				<table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
					<tfoot>
						<th width="30">&nbsp;</th>
						<th width="140">&nbsp;</th>
						<th width="150">&nbsp;</th>
						<th width="130">Balance</th>
						<th width="80"><? if($chkCat==1 ){
							echo ''; 
						}else{
							echo number_format($tot_rece_qty-$tot_retran_qty,2);
						}
						?></th>
						<th width="80">&nbsp;</th>
						<th width=""><? echo  number_format($tot_rec_amount-$tot_retran_amount,2); ?></th>
					</tfoot>         
				</table>
			</div>
		</div>
		</fieldset>
	</div>
	<?
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w') or die('canot open');
	$is_created = fwrite($create_new_doc,ob_get_contents()) or die('canot write');
	?>
	<div style="width:600px" align="center" id="scroll_body" >
		<a href="<?=$filename;?>" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>
		<input type="button" value="Print Preview" onClick="fnc_print_window()" style="width:100px"  class="formbutton"/>&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="button" value="Close" onClick="parent.emailwindow.hide()" style="width:100px"  class="formbutton"/>
	</div>
	<?
	exit();
}

?>