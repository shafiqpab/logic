<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if($action=="pi_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//print_r ($pi_id);
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
	$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
?>	
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
	}
	
	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<div style="width:800px" align="center" id="scroll_body" >
<fieldset style="width:100%; margin-left:10px" >
<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="Close" onClick="window_close()" style="width:100px"  class="formbutton"/>
         <div id="report_container" align="center" style="width:100%" > 
             <div style="width:780px">
                <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
                    <thead>
                        <th colspan="4" align="center"><? echo $companyArr[$company_name]; ?></th>
                    </thead>
                    	<tr>
                            <td width="150"><strong>LC Number : </strong></td> <td width="150"><strong><?  echo $lc_number; ?></strong></td>
                            <td><strong>Last Ship Date :</strong></td><td><strong><?  echo change_date_format($ship_date); ?></strong></td>
                        </tr>
                    	<tr>
                            <td width="150"><strong>Supplier : </strong></td> <td width="150"><strong><?  echo $supplierArr[$supplier_id]; ?></strong></td>
                            <td><strong>Expiry Date :</strong></td><td><strong><?  echo change_date_format($exp_date); ?></strong></td>
                        </tr>
                    	<tr>
                            <td width="150"><strong>LC Date : </strong></td> <td width="150"><strong><?  echo change_date_format($lc_date); ?></strong></td>
                            <td><strong>Pay Term :</strong></td><td><strong><?  echo $pay_term[$payterm]; ?></strong></td>
                        </tr>
                </table>
                <br />
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                	 <thead bgcolor="#dddddd">
                     	<tr>
                            <th width="30">SL</th>
                            <th width="80">PI NO.</th>
                            <th width="100">Item Group</th>
                            <th width="130">Item Description</th>
                            <th width="80">Qnty</th>
                            <th width="70">Rate</th>
                            <th width="90">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                <?
		$i=1;
		//if ($pi_id==0) $piId =""; else $piId =" and a.id in ($pi_id)";
		$yarncountArr = return_library_array("SELECT id,yarn_count FROM lib_yarn_count WHERE status_active = 1 AND is_deleted = 0","id","yarn_count"); 

		$sql="Select a.item_category_id,a.id, a.pi_number, b.item_prod_id, b.determination_id, b.item_group, b.item_description, b.size_id, b.quantity, b.rate, b.amount,b.count_name ,b.yarn_composition_item1 ,b.yarn_type,b.yarn_composition_percentage1 from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.id in ($pi_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.pi_number";
		//echo $sql;
		$result=sql_select($sql);
		
		$pi_arr=array();
		foreach( $result as $row)
		{
/*			if (!in_array($row[csf("pi_number")],$pi_arr) )
			{
				$pi_arr[]=$row[csf('pi_number')];
*/			?>
                   
<!--                        <tr>
                            <td colspan="6" align="left">PI No : <? //echo $row[csf('pi_number')]; ?></td>
                        </tr>
-->                    	
					<?
					$total_qnt+=$row[csf("quantity")];
					$total_amount+=$row[csf("amount")];
					
             // }
				if ($i%2==0)  
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
			?>
            <tr bgcolor="<? echo $bgcolor ; ?>">
            	<td><? echo $i; ?></td>
                 <td><? echo $row[csf("pi_number")]; ?></td>
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
          </tbody>
		<?	
        $i++;
        } 
		   ?>
             <tfoot>
                <th colspan="4" align="right">Total : </th>
                <th align="right"><? echo number_format($total_qnt,0); ?></th>
                <th>&nbsp;</th>
                <th align="right"><? echo number_format($total_amount,2); ?></th>
            </tfoot>
        </table>
		</div>
        </div>
	</fieldset>
</div>
<?
exit();
}

if ($action=="report_generate")
{
	extract($_REQUEST);
	//echo $cbo_company_id;
	$item_category_id=str_replace("'","",$cbo_item_category_id);
	
	
	//echo $item_category_id; die;
	/**
     * [$item_category_arr 4=>Accessories,8=>Spare Parts,10=>Other Capital Items,11=Stationaries,15=Electrical,21=Construction Materials]
     * @var array
     */
	 	$item_category_arr = array(4,8,10,11,13,15,21);
	 
		 
	
	$companyArr = return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0","id","company_short_name"); 
	$supplierArr = return_library_array("select id,short_name from lib_supplier where status_active=1 and is_deleted=0","id","short_name");
	$issueBankrArr = return_library_array("select id,bank_name from lib_bank ","id","bank_name"); 
	$hscodeArr = return_library_array("select id,hs_code from com_pi_master_details ","id","hs_code"); 
	$lc_num_arr = return_library_array("select id,export_lc_no from com_export_lc ","id","export_lc_no"); 
	$sc_num_arr = return_library_array("select id,contract_no from com_sales_contract ","id","contract_no"); 
	
		if($db_type==2)
		{
			$lc_item_category_sql=sql_select("Select  LISTAGG( c.item_category_id, ',') WITHIN GROUP (ORDER BY c.item_category_id) as item_category_id , a.id from  com_btb_lc_master_details a,com_btb_lc_pi b, com_pi_item_details c where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.status_active=1 and b.status_active=1 and c.status_active=1  $company_id group by a.id");	
			//echo "Select  LISTAGG( c.item_category_id, ',') WITHIN GROUP (ORDER BY c.item_category_id) as item_category_id , a.id from  com_btb_lc_master_details a,com_btb_lc_pi b, com_pi_item_details c where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.status_active=1 and b.status_active=1 and c.status_active=1  $company_id group by a.id";
		}
		else if($db_type==0)
		{
			 $lc_item_category_sql=sql_select("Select  group_concat( c.item_category_id) as item_category_id , a.id from  com_btb_lc_master_details a,com_btb_lc_pi b, com_pi_item_details c where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_id group by a.id");
		}
		
		$item_category_data=array();
		foreach($lc_item_category_sql as $row)
		{
			$item_category_data[$row[csf("id")]]['item_category_id']=$row[csf("item_category_id")];
		}
		
		$itemCategoryID="";
					$l=1;
					$catgory_id_arr=array_unique(explode(",",$item_category_data[$row[csf("id")]]['item_category_id']));
										
					foreach($catgory_id_arr as $cat_id)
					{
						if($l!=1) $itemCategoryID .=", ";
						$itemCategoryID .=$cat_id;
						$l++;
					}                		
					
		
		//echo $item_category_id; die;

	$tot_lc_qty_arr = return_library_array("select pi_id,sum(quantity) as quantity from com_pi_item_details where status_active=1 and is_deleted=0 group by  pi_id","pi_id","quantity");
	if(!in_array($item_category_id,$item_category_arr))
    {
		//change code  mahbub and item_category in($itemCategoryID);
		
		// $rcv_sql="select pi_wo_batch_no, sum(cons_quantity) as cons_quantity, sum(cons_amount) as cons_amount from inv_transaction where receive_basis=1 and transaction_type=1 and status_active=1 and company_id=$cbo_company_id and item_category not in(4,8,10,11,15,21) and item_category in($itemCategoryID) group by pi_wo_batch_no";
		 
		  $rcv_sql="select pi_wo_batch_no, sum(cons_quantity) as cons_quantity, sum(cons_amount) as cons_amount from inv_transaction where receive_basis=1 and transaction_type=1 and status_active=1 and company_id=$cbo_company_id and item_category not in(4,8,10,11,15,21) group by pi_wo_batch_no";
		//echo "select pi_wo_batch_no, sum(cons_quantity) as cons_quantity, sum(cons_amount) as cons_amount from inv_transaction where receive_basis=1 and transaction_type=1 and status_active=1 and company_id=$cbo_company_id and item_category not in(4,8,10,11,15,21) and item_category in($itemCategoryID) group by pi_wo_batch_no";
		
		$rcv_result=sql_select($rcv_sql);
		$tot_rec_qty_arr=array();
		foreach($rcv_result as $row)
		{
			$tot_rec_qty_arr[$row[csf("pi_wo_batch_no")]]["cons_quantity"]=$row[csf("cons_quantity")];
			$tot_rec_qty_arr[$row[csf("pi_wo_batch_no")]]["cons_amount"]=$row[csf("cons_amount")];
		}
		//$tot_rec_qty_arr = return_library_array("select pi_wo_batch_no,sum(cons_quantity) as cons_quantity from inv_transaction where receive_basis=1 and transaction_type=1 and status_active=1 group by  pi_wo_batch_no","pi_wo_batch_no","cons_quantity"); //and item_category=1
		//$tot_return_qty_arr = return_library_array("select b.pi_id,sum(a.cons_quantity) as cons_quantity from inv_transaction a, inv_issue_master b where a.mst_id = b.id and b.status_active = 1 and b.pi_id <> 0 and a.transaction_type = 3  group by b.pi_id","pi_id","cons_quantity");//and b.item_category = 1
		
		$rcv_rtn_sql="select b.pi_id, sum(a.cons_quantity) as cons_quantity, sum(a.cons_amount) as cons_amount from inv_transaction a, inv_issue_master b where a.mst_id = b.id and b.status_active = 1 and b.pi_id <> 0 and a.transaction_type = 3 and a.company_id=$cbo_company_id and a.item_category not in(4,8,10,11,15,21) group by b.pi_id";
		$rcv_rtn_result=sql_select($rcv_rtn_sql);
		$tot_return_qty_arr=array();
		foreach($rcv_rtn_result as $row)
		{
			$tot_return_qty_arr[$row[csf("pi_id")]]["cons_quantity"]=$row[csf("cons_quantity")];
			$tot_return_qty_arr[$row[csf("pi_id")]]["cons_amount"]=$row[csf("cons_amount")];
		}
	}
	
	ob_start();
	
	if(!in_array($item_category_id,$item_category_arr))
    {
		$div_width="2760";
		$tbl_width="2740";
	}
	else
	{
		$div_width="2440";
		$tbl_width="2420";
	}
	
	?>
	<div id="scroll_body" align="center" style="height:auto; width:<? echo $div_width; ?>px; margin:0 auto; padding:0;">
     <table width="2200px" >
		<?
        $company_library=sql_select("select id, company_name, plot_no, level_no,road_no,city from lib_company where id=".$cbo_company_id."");
        foreach( $company_library as $row)
        {
        ?>
            <tr>
                <td colspan="29" align="center" style="font-size:22px"><center><strong><? echo $row[csf('company_name')];?></strong></center></td>
            </tr>
    <!--        <span style="font-size:20px"><center><b><?// echo $row[csf('company_name')];?></b></center></span>
    -->	<?
        }
        ?>
        <tr>
            <td colspan="29" align="center" style="font-size:18px"><center><strong><u><? echo $report_title; ?></u></strong></center></td>
        </tr>
    </table>
    <table cellspacing="0" width="<? echo $div_width;?>"  border="1" rules="all" class="rpt_table">
       <thead>
            <th width="30">SL</th>
            <th width="60" align="center">Company</th>
            <th width="100" align="center">LC Number</th>
            <th width="70" align="center">Internal File No</th>
            <th width="80" align="center">Supply Source</th>
            <th width="100" align="center">LC/SC</th>
            <th width="70" align="center">LC Date</th>
            <th width="70" align="center">Insert Date</th>
            <th width="60" align="center">Supplier</th>
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
                <th width="70" align="center">Acceptance</th>
                <th width="70" align="center">Paid Ammount</th>
                <th width="50" align="center">Inco Term</th>
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
                <th width="80" align="center">Status</th>
                <th align="center">Bonded</th>
        </thead>
    </table>
    <div style="width:<? echo $div_width;?>px; overflow-y: scroll; overflow-x:hidden; max-height:300px;" id="scroll_body2">
    <table cellspacing="0" width="<? echo $tbl_width;?>"  border="1" rules="all" class="rpt_table" id="tbl_marginlc_list" style="float: left;" >
        <?
		//var_dump($assocArray);die;
		$cbo_company=str_replace("'","",$cbo_company_id);
		$cbo_issue=str_replace("'","",$cbo_issue_banking);
		$lc_type_id=str_replace("'","",$cbo_lc_type_id);
		
		$supply_so=str_pad(str_replace("'","",$cbo_supply_source), 2, "0", STR_PAD_LEFT);
		if (str_replace("'","",$cbo_supply_source)==0) $supply_source_cond =""; else $supply_source_cond ="and a.lc_category='".$supply_so."'";
		
		$cbo_bonded=str_replace("'","",$cbo_bonded_warehouse);
		$from_date=str_replace("'","",$txt_date_from);
		$to_date=str_replace("'","",$txt_date_to);
		//echo $from_date."**".$to_date."jahid";die;
		
		if ($cbo_company==0) $company_id =""; else $company_id =" and a.importer_id=$cbo_company ";
		if ($cbo_issue==0) $issue_banking =""; else $issue_banking =" and a.issuing_bank_id=$cbo_issue ";
		$category_id ="";
		if ($item_category_id>0)
		{
			
			if($item_category_id==1)
			{
				$category_id =" and a.pi_entry_form=165 ";
			}
			else if($item_category_id==2 || $item_category_id ==3 || $item_category_id==13 || $item_category_id ==14)
			{
				$category_id =" and a.pi_entry_form=166 ";
				
				//$entry_form = "166";
			}
			else if
			($item_category_id==5 || $item_category_id==6 ||  $item_category_id==7 || $item_category_id==23)
			{
				$category_id =" and a.pi_entry_form=227 ";
			}
			else if($item_category_id==4)
			{
				$category_id =" and a.pi_entry_form=167 ";
			}
			else if($item_category_id==12)
			{
				$category_id =" and a.pi_entry_form=168 ";
				//$entry_form =168;
			}
			else if($item_category_id==24)
			{
				$category_id =" and a.pi_entry_form=169 ";
				//$entry_form = "169";
			}
			else if($item_category_id==25)
			{
				$category_id =" and a.pi_entry_form=170 ";
				//$entry_form = "170";
			}
			else if($item_category_id==30)
			{
				
				$category_id =" and a.pi_entry_form=197 ";
				//$entry_form =197;
			}
			else if($item_category_id==31)
			{
				$category_id =" and a.pi_entry_form=171 ";
				//$entry_form =171;
			}
			else
			{
				$category_id =" and a.pi_entry_form=172 ";
				//$entry_form = "172";
			}
			
			
			/*else if(str_replace("'", '',$cbo_item_category_id) == "12")
			{
				$entry_form = "168";
			}
			else if(str_replace("'", '',$cbo_item_category_id) == "24")
			{
				$entry_form = "169";
			}
			else if(str_replace("'", '',$cbo_item_category_id) == "25")
			{
				$entry_form = "170";
			}
			else if(str_replace("'", '',$cbo_item_category_id) == "30")
			{
				$entry_form = "197";
			}
			else if(str_replace("'", '',$cbo_item_category_id) == "31")
			{
				$entry_form = "171";
			}
			else if(str_replace("'", '',$cbo_item_category_id) == "5" || str_replace("'", '',$cbo_item_category_id) == "6" || str_replace("'", '',$cbo_item_category_id) == "7" || str_replace("'", '',$cbo_item_category_id) == "23")
			{
				$entry_form = "227";
			}
			else
			{
				$entry_form = "172";
			}*/
			
		}
		//if ($item_category_id==0) $category_id =""; else $category_id =" and a.item_category_id=$item_category_id ";
		
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
		
		if($item_category_id==4)
		{
			$sql="Select a.id, a.importer_id, a.lc_number, a.lc_date, a.lc_category, a.supplier_id, a.currency_id, a.lc_value, a.pi_id, a.inco_term_id, a.inco_term_place, a.issuing_bank_id, a.item_category_id, a.lc_type_id, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.hs_code, a.doc_presentation_days, a.delivery_mode_id, a.psi_company, a.insurance_company_name, a.cover_note_no, a.maturity_from_id, a.margin, a.bonded_warehouse, $select_insert_date, a.status_active, 1 as type
			from com_btb_lc_master_details a
			where a.is_deleted=0 $company_id  $issue_banking $category_id $lc_tpe_id $bonded_warehouse $lc_date  $supply_source_cond
			union all
			Select a.id, a.importer_id, a.lc_number, a.lc_date, a.lc_category, a.supplier_id, a.currency_id, a.lc_value, a.pi_id, a.inco_term_id, a.inco_term_place, a.issuing_bank_id, d.item_category as item_category_id, a.lc_type_id, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.hs_code, a.doc_presentation_days, a.delivery_mode_id, a.psi_company, a.insurance_company_name, a.cover_note_no, a.maturity_from_id, a.margin, a.bonded_warehouse, $select_insert_date, a.status_active, 2 as type
			from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c, wo_non_order_info_mst d
			where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and c.work_order_id=d.id and a.is_deleted=0 $company_id  $issue_banking $category_cond_wo $lc_tpe_id $bonded_warehouse $lc_date  $supply_source_cond 
			group by a.id, a.importer_id, a.lc_number, a.lc_date, a.lc_category, a.supplier_id, a.currency_id, a.lc_value, a.pi_id, a.inco_term_id, a.inco_term_place, a.issuing_bank_id, d.item_category, a.lc_type_id, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.hs_code, a.doc_presentation_days, a.delivery_mode_id, a.psi_company, a.insurance_company_name, a.cover_note_no, a.maturity_from_id, a.margin, a.bonded_warehouse, a.insert_date, a.status_active";
		}
		else if($item_category_id==11)
		{
			$sql="Select a.id, a.importer_id, a.lc_number, a.lc_date, a.lc_category, a.supplier_id, a.currency_id, a.lc_value, a.pi_id, a.inco_term_id, a.inco_term_place, a.issuing_bank_id, a.item_category_id, a.lc_type_id, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.hs_code, a.doc_presentation_days, a.delivery_mode_id, a.psi_company, a.insurance_company_name, a.cover_note_no, a.maturity_from_id, a.margin, a.bonded_warehouse, , a.status_active, $select_insert_date
			from com_btb_lc_master_details a
			where a.is_deleted=0 $company_id  $issue_banking $category_id $lc_tpe_id $bonded_warehouse $lc_date  $supply_source_cond and a.id not in(Select a.id from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c, wo_non_order_info_mst d  where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and c.work_order_id=d.id and a.status_active=1 and a.is_deleted=0  $category_id and  d.item_category=4 group by a.id)";
		}
		else
		{
			$sql="Select a.id, a.importer_id, a.lc_number, a.lc_date, a.lc_category, a.supplier_id, a.currency_id, a.lc_value, a.pi_id, a.inco_term_id, a.inco_term_place, a.issuing_bank_id, a.item_category_id, a.lc_type_id, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.hs_code, a.doc_presentation_days, a.delivery_mode_id, a.psi_company, a.insurance_company_name, a.cover_note_no, a.maturity_from_id, a.margin, a.bonded_warehouse, a.status_active, $select_insert_date
			from com_btb_lc_master_details a
			where a.is_deleted=0 and a.status_active=1 $company_id  $issue_banking $category_id $lc_tpe_id $bonded_warehouse $lc_date  $supply_source_cond";
		}
		//echo $sql;//die;
		
		
		
		if($db_type==2)
		{
			$lc_sc_sql=sql_select("Select a.id,b.is_lc_sc ,LISTAGG(CAST(b.lc_sc_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.lc_sc_id) as lc_sc_id
					from com_btb_lc_master_details a, com_btb_export_lc_attachment b
					where a.id=b.import_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id 
					group by a.id,b.is_lc_sc");	
		}
		else if($db_type==0)
		{
			$lc_sc_sql=sql_select("Select a.id,b.is_lc_sc ,group_concat(b.lc_sc_id) as lc_sc_id
					from com_btb_lc_master_details a, com_btb_export_lc_attachment b
					where a.id=b.import_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id 
					group by a.id,b.is_lc_sc");
		}
		
		foreach($lc_sc_sql as $row)
		{
			$ls_sc_data[$row[csf("id")]]['is_lc_sc']=$row[csf("is_lc_sc")];
			$ls_sc_data[$row[csf("id")]]['lc_sc_id']=$row[csf("lc_sc_id")];
		}

		$file_sql_all=sql_select("select b.id, a.internal_file_no, 1 as type from com_export_lc a, com_btb_lc_master_details b, com_btb_export_lc_attachment c where a.id=c.lc_sc_id and c.import_mst_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.is_lc_sc=0
	union all
	select b.id, a.internal_file_no, 2 as type from com_sales_contract a, com_btb_lc_master_details b, com_btb_export_lc_attachment c where a.id=c.lc_sc_id and c.import_mst_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.is_lc_sc=1");
		$file_no_arr=array();
		foreach($file_sql_all as $row)
		{
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
		$sql_data = sql_select($sql);
		$i=1;
		$bal=0;
		$rec=0;
		$tlc_qty=0;
		$sql_accep = sql_select("select a.btb_lc_id, a.current_acceptance_value, b.accepted_ammount 
            from com_import_invoice_dtls a left join com_import_payment b on a.import_invoice_id=b.invoice_id and b.status_active=1  
            where a.status_active=1 and a.is_deleted=0 and a.current_acceptance_value>0");
		$accep_data=array(); 
		$paid_ammount=array();
		foreach( $sql_accep as $row)
		{
			$accep_data[$row[csf("btb_lc_id")]]+=$row[csf("current_acceptance_value")];
			$paid_ammount[$row[csf("btb_lc_id")]]+=$row[csf("accepted_ammount")];
		}

		foreach( $sql_data as $row)
		{			 
			if ($i%2==0)  
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
				
			$tot_lc_value+=$row[csf("lc_value")];
			$tol_lc_qnty+=$ttlc_qty;
			$tol_rec_qnty+=$ttrec_qty;
			$tot_balance+=$balance;
			$ttl_acceptance_vlu += $accep_data[$row[csf("id")]];
			$ttl_paid_amount += $paid_ammount[$row[csf("id")]];
			
			$company_name=$row[csf("importer_id")];
			$lc_number=$row[csf("lc_number")];
			$ship_date=$row[csf("last_shipment_date")];
			$supplier_id=$row[csf("supplier_id")];
			$lc_date=$row[csf("lc_date")];
			$exp_date=$row[csf("lc_expiry_date")];
			$payterm=$row[csf("payterm_id")];
			$pi_id_arr=array_unique(explode(",",$row[csf("pi_id")]));
			
			$tot_rec_qty=0;$pi_ids="";$tot_rec_amt=0;
			foreach($pi_id_arr as $pi_id)
			{
				$ttlc_qty=$tot_lc_qty +=$tot_lc_qty_arr[$pi_id];
				$ttrec_qty=$tot_rec_qty +=$tot_rec_qty_arr[$pi_id]["cons_quantity"] + $tot_return_qty_arr[$pi_id]["cons_quantity"];
				$ttrec_amt=$tot_rec_amt +=(($tot_rec_qty_arr[$pi_id]["cons_amount"] - $tot_return_qty_arr[$pi_id]["cons_amount"])/$currency_conversion_rate);
				$pi_ids .= $pi_id.",";
			}				
			
			
			?>
            <tr bgcolor="<? echo $bgcolor ; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30" ><? echo $i; ?></td>
                <td align="center"  width="60"><p><? echo $companyArr[$row[csf("importer_id")]]; ?></p></td>
                <td width="100"><p><a href="#report_details" onclick="lc_details_popup('<? echo $row[csf('id')];?>','lc_details','LC Details','<? echo $file_no_arr[$row[csf("id")]];?>');"><? echo $row[csf("lc_number")]; ?></a></p></td>
                <td width="70" align="center"><p><? echo $file_no_arr[$row[csf("id")]]; ?></p></td>
                <td width="80"><p><? echo $supply_source[(int)$row[csf("lc_category")]]; ?></p></td>
                <td width="100"><p>
                <?
				$lc_sc_num="";
				$p=1;
				$lc_sc_id_arr=array_unique(explode(",",$ls_sc_data[$row[csf("id")]]['lc_sc_id']));
				foreach($lc_sc_id_arr as $lc_sc_id)
				{
					if($p!=1) $lc_sc_num .=", ";
					if($ls_sc_data[$row[csf("id")]]['is_lc_sc']==0)
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
				?>
                </p></td>
                <td width="70"><p><? if($row[csf("lc_date")]!="" && $row[csf("lc_date")]!="0000-00-00") echo change_date_format($row[csf("lc_date")]); ?></p></td>
                <td width="70"><p><? if($row[csf("insert_date")]!="" && $row[csf("insert_date")]!="0000-00-00") echo change_date_format($row[csf("insert_date")]); ?></p></td>
                <td width="60"><p><? echo $supplierArr[$row[csf("supplier_id")]]; ?></p></td>
                <td width="50" align="center"><p><? echo $currency[$row[csf("currency_id")]]; ?></p></td>
                <td width="90" align="right"><p><? echo number_format($row[csf("lc_value")],2); ?></p></td>
                <?
				if(!in_array($item_category_id,$item_category_arr))
                {
				?>
                	<td width="80" align="right"><? echo number_format($tot_rec_amt,2); ?></td>
                    <td width="80" align="right"><p><? $balance_amt=$row[csf("lc_value")]-$tot_rec_amt; echo number_format($balance_amt,2); ?></p></td>
				<?
				}
				?>
                <td width="80" align="right"><p><? echo number_format($tot_lc_qty,2); ?></p></td>
                <?
				
				
				if(!in_array($item_category_id, $item_category_arr))
				{
					$item_cate_array="";
					$l=1;
					$cat_id_array=array_unique(explode(",",$item_category_data[$row[csf("id")]]['item_category_id']));
					foreach($cat_id_array as $cat_id)
					{
						if($l!=1) $item_cate_array .=", ";
						$item_cate_array .=$cat_id;
						$l++;
					}   
					
					?>
                	<td width="80" align="right"><p><a href="##" onclick="openmypagRcvDetails('<? echo $pi_ids;?>', <? echo $item_cate_array;?>)"><? echo number_format($tot_rec_qty,2); ?></a></p></td>
                    <td width="80" align="right"><p><? $balance=$tot_lc_qty-$tot_rec_qty; echo number_format($balance,2); ?></p></td>
					<?
				}
				?>
                
                <td width="50" align="center"><p>
				<?
				$pi_id=$row[csf("pi_id")]; 
				echo "<a href='#report_details' style='color:#000' onclick= \"openmypage('$company_name','$lc_number','$ship_date','$supplier_id','$lc_date','$exp_date','$payterm','$pi_id','pi_details','PI Details');\">"."View"."</a>"; //$row[csf("pi_id")]; 
				?>
                </p></td>
                <td width="70" align="right"><p><? echo number_format($accep_data[$row[csf("id")]],2); ?></p></td>
                <td width="70" align="right"><p><? echo number_format($paid_ammount[$row[csf("id")]],2); ?></p></td>
                <td width="50" align="center"><p><? echo $incoterm[$row[csf("inco_term_id")]]; ?></p></td>
                <td width="80" align="center"><p><? echo $row[csf("inco_term_place")]; ?></p></td>
                <td width="120"><p><? echo $issueBankrArr[$row[csf("issuing_bank_id")]]; ?></p></td>
                <td width="80"><p><? 
					$itemCategory="";
					$l=1;
					$cat_id_arr=array_unique(explode(",",$item_category_data[$row[csf("id")]]['item_category_id']));
					foreach($cat_id_arr as $cat_id)
					{
						if($l!=1) $itemCategory .=", ";
						$itemCategory .=$item_category[$cat_id];
						$l++;
					}                		
					echo $itemCategory;

                //echo $item_category[$row[csf("item_category_id")]]; ?></p></td>
                <td width="70"><p><? echo $lc_type[$row[csf("lc_type_id")]]; ?></p></td>
                <td  width="40" align="center"><p><? echo $row[csf("tenor")]; ?></p></td>
                <td width="70"><p><? echo change_date_format($row[csf("last_shipment_date")]); ?></p></td>
                <td width="70" align="center"><p><? echo change_date_format($row[csf("lc_expiry_date")]); ?></p></td>
                <td width="60" align="center"><p><? echo $hscodeArr[$row[csf("pi_id")]]; ?></p></td>
                <td width="40" align="center"><p><? echo $row[csf("doc_presentation_days")]; ?></p></td>
                <td width="60" align="center"><p><? echo $shipment_mode[$row[csf("delivery_mode_id")]]; ?></p></td>
                <td width="110" align="center"><p><? echo $row[csf("psi_company")]; ?></p></td>
                <td width="110"><p><? echo $row[csf("insurance_company_name")]; ?></p></td>
                <td width="80"><p><? echo $row[csf("cover_note_no")]; ?></p></td>
                <td width="100" align="center"><p><? echo $maturity_from[$row[csf("maturity_from_id")]]; ?></p></td>
                <td width="50" align="center"><p><? echo $row[csf("margin")]; ?></p></td>
                
                <td width="80" align="center"><p><? echo $row_status[$row[csf("status_active")]]; ?></p></td>
                <td align="center"><p><? echo $yes_no[$row[csf("bonded_warehouse")]]; ?></p></td>
            </tr>
			<?
            $tot_lc_qty=$tot_rec_qty=0;	
            $bal=$bal+$balance;
			$bal_amt=$bal_amt+$balance_amt;
            $rec=$rec+$ttrec_qty;
			$rec_amt=$rec_amt+$ttrec_amt;
            $tlc_qty=$tlc_qty+$ttlc_qty;

            $i++;
        }
	?>
         </table>
          <table cellspacing="0" width="<? echo $div_width;?>"  border="1" rules="all" class="rpt_table" >
            <tfoot>
            	<th width="30" >&nbsp;</th>
                <th align="center"  width="60">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="50" align="center">Total : </th>
                <th align="right" width="90" id="value_tot_lc_value"><? echo number_format($tot_lc_value,2); ?></th>
                <?
				if(!in_array($item_category_id,$item_category_arr))
                {
				?>
                    <th width="80" align="center"><? echo number_format($rec_amt,2);?></th>
                	<th width="80" align="center"><? echo number_format($bal_amt,2);?></th>
                <?
				}
				?>
                <th width="80" align="center"><? echo number_format($tlc_qty,2);?></th>
                <?
				if(!in_array($item_category_id,$item_category_arr))
                {
				?>
                    <th width="80" align="center"><? echo number_format($rec,2);?></th>
                	<th width="80" align="center"><? echo number_format($bal,2);?></th>
                <?
				}
				?>
                <th width="50" align="center">&nbsp;</th>
                <th width="70" align="center"><? echo $ttl_acceptance_vlu;?></th>
                <th width="70" align="center"><? echo $ttl_paid_amount;?></th>                
                <th width="50" align="center">&nbsp;</th>
                <th width="80" align="center">&nbsp;</th>
                <th width="120">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th  width="40" align="center">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="70" align="center">&nbsp;</th>
                <th width="60" align="center">&nbsp;</th>
                <th width="40" align="center">&nbsp;</th>
                <th width="60" align="center">&nbsp;</p></th>
                <th width="110" align="center">&nbsp;</th>
                <th width="110">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="100" align="center">&nbsp;</th>
                <th width="50" align="center"></th>
                <th width="80" align="center">&nbsp;</th>
                <th align="center">&nbsp;</th>
            </tfoot>         
    </table>
	</div>
	</div>
	<?
		/*foreach (glob("*.xls") as $filename) {
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');	
		$is_created = fwrite($create_new_doc,ob_get_contents());
		echo "$html****$filename****$item_category_id"; 
		exit();	*/
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
                <th width="200">LC Number</th>
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
                        <td><? echo change_date_format($row[csf("expiry_date")]); ?></td>
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
                <th width="200">LC Number</th>
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
                        <td><? echo change_date_format($row[csf("expiry_date")]); ?></td>
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
	//print_r ($pi_id);
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
	$piNoArr = return_library_array("select id,pi_number from  com_pi_master_details where status_active=1 and is_deleted=0","id","pi_number");
?>	
	
<div style="width:600px" align="center" id="scroll_body" >
<fieldset style="width:100%; margin-left:10px" >
         <div id="report_container" align="center" style="width:100%" > 
             <div style="width:600px">
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                    <thead bgcolor="#dddddd">
                     	<tr>
                            <th width="30">SL</th>
                            <th width="80">PI NO.</th>
                            <th width="100">Receive No.</th>
                            <th width="130">Receive Date</th>
                            <th width="100">Qnty</th>
                            <th width="80">Rate</th>
                            <th width="">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                <?
		$i=1;
                $pi_ids = implode(",",array_filter(explode(",",chop($pi_ids,","))));
		if ($pi_ids=="" || $pi_ids == 0) 
                    $piId =""; 
                else 
                    $piId =" and a.pi_wo_batch_no in ($pi_ids)";
                 	$sql = "select a.pi_wo_batch_no,(a.cons_quantity) as cons_quantity,a.cons_rate,a.order_rate,a.cons_amount, b.recv_number, b.receive_date
                        from inv_transaction a, inv_receive_master b
                        where a.mst_id = b.id and a.receive_basis=1 and a.transaction_type=1 and b.item_category in($item_cate_array)
                        and a.status_active=1 $piId and b.company_id = $company_name";
                       //group by  a.pi_wo_batch_no,b.recv_number, b.receive_date"; //and a.item_category=1
					  // echo $sql;
                $result=sql_select($sql);
		foreach( $result as $row)
		{
				if ($i%2==0)  
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
			$amount = $row[csf("cons_quantity")]*$row[csf("order_rate")];
			?>
            <tr bgcolor="<? echo $bgcolor ; ?>">
            	<td><? echo $i; ?></td>
                <td><? echo $piNoArr[$row[csf("pi_wo_batch_no")]]; ?></td>
                <td><? echo $row[csf("recv_number")]; ?></td>
                <td align="center"><? echo $row[csf("receive_date")]; ?></td>
                <td align="right"><? echo number_format($row[csf("cons_quantity")],2); ?></td>
                <td align="right"><? echo number_format($row[csf("order_rate")],2); ?></td>
                <td align="right"><? echo number_format($amount,2); ?></td>
            </tr>
          </tbody>
		<?	
        $i++;
        } 
		?>
        </table>
            </div>
        </div>
	</fieldset>
    <br/>
    <fieldset style="width:100%; margin-left:10px" >
         <div id="report_container" align="center" style="width:100%" > 
             <div style="width:600px">
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                    <thead bgcolor="#dddddd">
                     	<tr>
                            <th width="30">SL</th>
                            <th width="80">PI NO.</th>
                            <th width="100">Receive Return No.</th>
                            <th width="130">Receive Return Date</th>
                            <th width="100">Qnty</th>
                            <th width="80">Rate</th>
                            <th width="">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                <?
		$i=1;
                $pi_ids = implode(",",array_filter(explode(",",chop($pi_ids,","))));
		if ($pi_ids=="" || $pi_ids == 0) 
                    $piId =""; 
                else 
                    $piId =" and b.pi_id in ($pi_ids)";
                $sql = "select b.pi_id,(cons_quantity) as cons_quantity,a.cons_rate,a.order_rate,a.cons_amount,b.issue_number, b.issue_date
                        from inv_transaction a, inv_issue_master b
                        where a.mst_id = b.id  
                        and b.status_active = 1 and b.pi_id <> 0 
                        and a.transaction_type = 3 $piId and b.company_id = $company_name
                        order by b.pi_id"; //group by b.pi_id ,b.issue_number, b.issue_date // and b.item_category = 1
                $result=sql_select($sql);
		foreach( $result as $row)
		{
				if ($i%2==0)  
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
			$amount = $row[csf("order_rate")]*$row[csf("cons_quantity")];
			?>
            <tr bgcolor="<? echo $bgcolor ; ?>">
            	<td><? echo $i; ?></td>
                <td><? echo $piNoArr[$row[csf("pi_id")]]; ?></td>
                <td><? echo $row[csf("issue_number")]; ?></td>
                <td align="center"><? echo $row[csf("issue_date")]; ?></td>
                <td align="right"><? echo number_format($row[csf("cons_quantity")],2); ?></td>
                <td align="right"><? echo number_format($row[csf("order_rate")],2); ?></td>
                <td align="right"><? echo number_format($amount,2); ?></td>
            </tr>
          </tbody>
		<?	
        $i++;
        } 
		?>
        </table>
            </div>
        </div>
	</fieldset>
</div>
<?
exit();
}
?>