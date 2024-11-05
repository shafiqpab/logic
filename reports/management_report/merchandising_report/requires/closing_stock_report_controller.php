<?
require_once('../../../../includes/common.php');

session_start();
extract($_REQUEST);
$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

if ($action=="load_drop_down_location")
{
    echo create_drop_down( "cbo_location_id", 130, "select id, location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name ASC","id,location_name", 1, "-Select Location-", "", "" );
    exit();
}

$date=date('Y-m-d');
$company_arr=return_library_array( "select id,company_name from lib_company",'id','company_name');

if($action=="report_generate")
{
	$company_id   = str_replace("'","",$cbo_company_id);
	$from_date    = str_replace("'","",$txt_date_from);
	$to_date    = str_replace("'","",$txt_date_to);
	$exchange_rate  = str_replace("'","",$txt_exchange_rate);
	$prev_date    =  date('d-M-Y', strtotime($from_date .' -1 day')); 
	$conversion_date=date("Y/m/d");
	//$curr_exchange_rate=set_conversion_rate( 2, $conversion_date,$company_id );
	
	/*==========================================================================================/
	/                   Receive                                                                 /
	/==========================================================================================*/ 
	$trim_acc_arr=return_library_array("select id from product_details_master where entry_form=24 and item_category_id=4 and company_id=$company_id","id","id");
	//echo "<pre>";print_r($trim_acc_arr);die;
    $sql_trans = "SELECT a.STORE_ID, a.CONS_RATE, a.CONS_QUANTITY, a.TRANSACTION_DATE, a.TRANSACTION_TYPE, a.ITEM_CATEGORY, a.CONS_AMOUNT, a.PROD_ID
	from inv_transaction a 
	where a.item_category in (1,4,5,6,7,23) and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and a.store_id > 0 and item_category in(1,4,5,6,7,23)"; 	
	//and a.transaction_date between '$from_date' and '$to_date'
    //echo $sql_trans;die;
    $sql_trans_result = sql_select($sql_trans);
    
	$total_accessories_rcv_opening_amt=$total_accessories_issue_opening_amt=$total_accessories_rcv_amt=$total_accessories_issue_amt=$total_rcv_opening_amt=$total_issue_opening_amt=$total_rcv_amt=$total_issue_amt=$total_dyes_rcv_opening_amt=$total_dyes_issue_opening_amt=$total_dyes_rcv_amt=$total_dyes_issue_amt=0;
	
    foreach ($sql_trans_result as $row)
    {
		$trans_date = $row["TRANSACTION_DATE"];
		if(strtotime($trans_date) < strtotime($from_date))
		{
			if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
			{
				if($row["ITEM_CATEGORY"]==1)
				{
					$total_rcv_opening_amt = bcadd($total_rcv_opening_amt,$row["CONS_AMOUNT"],15);
				}
				elseif($row["ITEM_CATEGORY"]==4)
				{
					if($trim_acc_arr[$row["PROD_ID"]]!="") 
					{
						$total_accessories_rcv_opening_amt =bcadd($total_accessories_rcv_opening_amt,$row["CONS_AMOUNT"],15);
					}
				}
				else
				{
					$total_dyes_rcv_opening_amt = bcadd($total_dyes_rcv_opening_amt,$row["CONS_AMOUNT"],15);
				}
			}
			else
			{
				if($row["ITEM_CATEGORY"]==1)
				{
					$total_issue_opening_amt = bcadd($total_issue_opening_amt,$row["CONS_AMOUNT"],15);
				}
				elseif($row["ITEM_CATEGORY"]==4)
				{
					if($trim_acc_arr[$row["PROD_ID"]]!="")
					{
						$total_accessories_issue_opening_amt =  bcadd($total_accessories_issue_opening_amt,$row["CONS_AMOUNT"],15);
					}
				}
				else
				{
					$total_dyes_issue_opening_amt = bcadd($total_dyes_issue_opening_amt,$row["CONS_AMOUNT"],15);
				}
			}
		}
		
		if(strtotime($trans_date) >= strtotime($from_date) && strtotime($trans_date) <= strtotime($to_date))
		{
			if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
			{
				if($row["ITEM_CATEGORY"]==1)
				{
					$total_rcv_amt =bcadd($total_rcv_amt,$row["CONS_AMOUNT"],15);
				}
				elseif($row["ITEM_CATEGORY"]==4)
				{
					if($trim_acc_arr[$row["PROD_ID"]]!="")
					{
						$total_accessories_rcv_amt=bcadd($total_accessories_rcv_amt,$row["CONS_AMOUNT"],15);
					}
				}
				else
				{
					$total_dyes_rcv_amt =bcadd($total_dyes_rcv_amt,$row["CONS_AMOUNT"],15);
				}
			}
			else
			{
				if($row["ITEM_CATEGORY"]==1)
				{
					$total_issue_amt =bcadd($total_issue_amt,$row["CONS_AMOUNT"],15);
				}
				elseif($row["ITEM_CATEGORY"]==4)
				{
					if($trim_acc_arr[$row["PROD_ID"]]!="")
					{
						$total_accessories_issue_amt=bcadd($total_accessories_issue_amt,$row["CONS_AMOUNT"],15);
					}
				}
				else
				{
					$total_dyes_issue_amt =bcadd($total_dyes_issue_amt,$row["CONS_AMOUNT"],15);
				}
			}
		}
    }
	
	unset($sql_trans_result);
	
	//=============================**********************=======================================
    //                   Accessories                        /
    //==========================================================================================
    $opening_value_accessories = bcsub($total_accessories_rcv_opening_amt,$total_accessories_issue_opening_amt,15);
    $receive_value_accessories = $total_accessories_rcv_amt;
    $delivery_value_accessories = $total_accessories_issue_amt;
    $closing_value_accessories = bcsub(bcadd($opening_value_accessories,$receive_value_accessories,15),$delivery_value_accessories,15);

    //=============================**********************=======================================
    //                   Yarn                        /
    //==========================================================================================
    $opening_value  = bcsub($total_rcv_opening_amt,$total_issue_opening_amt,15);
    $receive_value  = $total_rcv_amt;
    $delivery_value = $total_issue_amt;
    $closing_value  = bcsub(bcadd($opening_value,$receive_value,15),$delivery_value,15);
	
    //=============================**********************=======================================
    //                   Dyes                        /
    //==========================================================================================
    
    
    $opening_value_dyes = bcsub($total_dyes_rcv_opening_amt,$total_dyes_issue_opening_amt,15);
    $receive_value_dyes = $total_dyes_rcv_amt;
    $delivery_value_dyes = $total_dyes_issue_amt;
    $closing_value_dyes = bcsub(bcadd($opening_value_dyes,$receive_value_dyes,15),$delivery_value_dyes,15);
    
	$tbl_width = 900;
	ob_start(); 
	?>
	
	<!--=====================================Total Summary Start=====================================-->
	<fieldset class="first_part" style="width:<? echo $tbl_width+30;?>px">
    <div class="report_heading">
        <table width="<? echo $tbl_width;?>"  cellspacing="0">
            <tr class="form_caption">
                <td colspan="7" align="center"><strong style="font-size:20px">Closing Stock Report</strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="7" align="center" ><strong style="font-size:16px"><?php echo $company_arr[$company_id]; ?></strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="7" align="center"><strong style="font-size:16px;">Date : <? echo change_date_format($from_date);?> to <? echo change_date_format($to_date);?></strong></td>
            </tr>
        </table>
    </div>
      
      <div id="yearly_revenue_report">
      <table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
      
            <thead>
              <tr>
                  <th width="150">Category</th>
                  <th width="150">Item</th>
                  <th width="120">Opening Value</th>
                  <th width="120">Receive Value </th>
                  <th width="120">Delivery Value</th>
                  <th width="120">Closing Value</th>
                  <th width="120">Close Order Value</th>
                </tr>
            </thead>
            <tbody>   
              <tr>
                <td valign="middle" rowspan="4">Row Material</td>
                <td>
                  <a href="##" onclick="open_popup('<? echo $company_id?>','<? echo $from_date?>','<? echo $to_date?>','<? echo $exchange_rate?>','open_yarn_details_popup',1)">
                    Yarn
                  </a>
                </td>
                <td align="right"><? echo number_format($opening_value,0)?></td>
                <td align="right"><? echo number_format($receive_value,0)?></td>
                <td align="right"><? echo number_format($delivery_value,0)?></td>
                <td align="right"><? echo number_format($closing_value,0)?></td>
                <td align="right"><? echo number_format($a,0)?></td>
              </tr>
              <? if($opening_value_dyes>0 || $receive_value_dyes>0 || $delivery_value_dyes>0 || $closing_value_dyes>0 ){
                ?>
                <tr>
                  <td>
                    <a href="##" onclick="open_popup('<? echo $company_id?>','<? echo $from_date?>','<? echo $to_date?>','<? echo $exchange_rate?>','open_dyes_chemical_location_details_popup',2)">
                      Dyes Chemical
                    </a>
                  </td>
                  <td align="right"><? echo number_format($opening_value_dyes,0)?></td>
                  <td align="right"><? echo number_format($receive_value_dyes,0)?></td>
                  <td align="right"><? echo number_format($delivery_value_dyes,0)?></td>
                  <td align="right"><? echo number_format($closing_value_dyes,0)?></td>
                  <td align="right"><? echo number_format($a,0)?></td>
                </tr> 
                <?
              } ?>   
              <tr>
                <td>
                  <a href="##" onclick="open_popup('<? echo $company_id?>','<? echo $from_date?>','<? echo $to_date?>','<? echo $exchange_rate?>','open_accessories_location_buyer_details_popup',3)">
                    Accessories
                  </a>
                </td>
                <td align="right"><? echo number_format($opening_value_accessories,0)?></td>
                <td align="right"><? echo number_format($receive_value_accessories,0)?></td>
                <td align="right"><? echo number_format($delivery_value_accessories,0)?></td>
                <td align="right"><? echo number_format($closing_value_accessories,0)?></td>
                <td align="right"><? echo number_format($a,0)?></td>
              </tr>    
              <!--<tr bgcolor="#c4c4c4" style="font-weight:bold;text-align:right;">
                <td>Total</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td valign="middle" rowspan="7">Work In Process</td>
                <td>
                  <a href="javascript:void()" onclick="open_popup('')">
                    Grey Fabric
                  </a>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>   
              <tr>
                <td>
                  <a href="javascript:void()" onclick="open_popup('')">
                    Dyed Fabric
                  </a>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>    
              <tr>
                <td>
                  <a href="javascript:void()" onclick="open_popup('')">
                    Finish Fabric
                  </a>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>    
              <tr>
                <td>
                  <a href="javascript:void()" onclick="open_popup('')">
                    Cutting Parts
                  </a>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>    
              <tr>
                <td>
                  <a href="javascript:void()" onclick="open_popup('')">
                    Unfinished Garments
                  </a>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>    
              <tr>
                <td>
                  <a href="javascript:void()" onclick="open_popup('')">
                    Finished Garments
                  </a>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>    
              <tr bgcolor="#c4c4c4" style="font-weight:bold;text-align:right;">
                <td>Total</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td valign="middle" rowspan="2">Factory Maintenance Item </td>
                <td>
                  <a href="javascript:void()" onclick="open_popup('')">
                    General item
                  </a>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>   
              <tr bgcolor="#c4c4c4" style="font-weight:bold;text-align:right;">
                <td>Total</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>   
              <tr bgcolor="#A1A1A1" style="font-weight:bold;text-align:right;">
                <td colspan="2">Grand Total</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>-->             
            </tbody>
        </table>     
      </div>  
	</fieldset>  
      
	<?
    unset($main_array);
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    	@unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');  
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename"; 
    exit();
}  

if($action=="open_yarn_details_popup")
{
	echo load_html_head_contents("Yarn Details Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$location_type_array = array(
	0 => 'Name of Yarn store',
	1 => 'Name of knitting Factory For Yarn WIP',
	2 => 'Name of Yarn Dyeing Factory For Yarn WIP',
	3 => 'Name of Twisting Factory For Yarn WIP',
	4 => 'Name of Re-Waxing Factory For Yarn WIP',
	5 => 'Name of Reconning Factory For Yarn WIP',
	);
	$lib_store    = return_library_array("select id,store_name from lib_store_location", "id", "store_name");
	$lib_company  = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$lib_supplier   = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
	/*==========================================================================================/
	/                   Yarn Receive                    /
	/==========================================================================================*/
    $sql_receive = "SELECT a.ORDER_RATE, c.CURRENCY_ID, a.CONS_RATE, a.CONS_QUANTITY, a.CONS_AMOUNT, a.TRANSACTION_DATE, a.STORE_ID, a.ITEM_CATEGORY, c.RECEIVE_PURPOSE, c.KNITTING_COMPANY, c.KNITTING_SOURCE 
	from inv_transaction a, inv_receive_master c  
	where a.mst_id=c.id and a.transaction_type in (1,4) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$company and a.STORE_ID > 0"; 
    // echo $sql_receive;
    $receive_res = sql_select($sql_receive);    
    
    $yarn_store = array();
    $store_wise_opn_rcv_array   = array();
    $dyeing_opn_rcv_array     = array();
    $twisting_opn_rcv_array   = array();
    $re_waxing_opn_rcv_array  = array();    
    $reconning_opn_rcv_array  = array();    
    
    $store_wise_rcv_array   = array();
    $dyeing_rcv_array     = array();
    $twisting_rcv_array   = array();
    $re_waxing_rcv_array  = array();
    $reconning_rcv_array  = array();

    foreach ($receive_res as $row)
    {
		$yarn_store[0][$row["STORE_ID"]] = $row["STORE_ID"];
		$trans_date = $row["TRANSACTION_DATE"];
		
        if(strtotime($trans_date) < strtotime($from_date))
		{
			$store_wise_opn_rcv_array[$row["STORE_ID"]]['value'] =bcadd($store_wise_opn_rcv_array[$row["STORE_ID"]]['value'],$row["CONS_AMOUNT"],15);
			$store_wise_opn_rcv_array[$row["STORE_ID"]]['qty'] =bcadd($store_wise_opn_rcv_array[$row["STORE_ID"]]['qty'],$row["CONS_QUANTITY"],15);
			
			if($row["RECEIVE_PURPOSE"]==2)
			{
				$dyeing_opn_rcv_array[$row["KNITTING_COMPANY"]]['value'] =bcadd($dyeing_opn_rcv_array[$row["KNITTING_COMPANY"]]['value'],$row["CONS_AMOUNT"],15);
				$dyeing_opn_rcv_array[$row["KNITTING_COMPANY"]]['qty'] =bcadd($dyeing_opn_rcv_array[$row["KNITTING_COMPANY"]]['qty'],$row["CONS_QUANTITY"],15);
			}
			if($row["RECEIVE_PURPOSE"]==15)
			{
				$twisting_opn_rcv_array[$row["KNITTING_COMPANY"]]['value'] =bcadd($twisting_opn_rcv_array[$row["KNITTING_COMPANY"]]['value'],$row["CONS_AMOUNT"],15);
				$twisting_opn_rcv_array[$row["KNITTING_COMPANY"]]['qty'] =bcadd($twisting_opn_rcv_array[$row["KNITTING_COMPANY"]]['qty'],$row["CONS_QUANTITY"],15);
			}
			if($row["RECEIVE_PURPOSE"]==38)
			{
				$re_waxing_opn_rcv_array[$row["KNITTING_COMPANY"]]['value'] =bcadd($re_waxing_opn_rcv_array[$row["KNITTING_COMPANY"]]['value'],$row["CONS_AMOUNT"],15);
				$re_waxing_opn_rcv_array[$row["KNITTING_COMPANY"]]['qty'] =bcadd($re_waxing_opn_rcv_array[$row["KNITTING_COMPANY"]]['qty'],$row["CONS_QUANTITY"],15);
			}
			if($row["RECEIVE_PURPOSE"]==12)
			{
				$reconning_opn_rcv_array[$row["KNITTING_COMPANY"]]['value'] =bcadd($reconning_opn_rcv_array[$row["KNITTING_COMPANY"]]['value'],$row["CONS_AMOUNT"],15);
				$reconning_opn_rcv_array[$row["KNITTING_COMPANY"]]['qty'] =bcadd($reconning_opn_rcv_array[$row["KNITTING_COMPANY"]]['qty'],$row["CONS_QUANTITY"],15);
			}
		}
		
		if(strtotime($trans_date) >= strtotime($from_date) && strtotime($trans_date) <= strtotime($to_date))
		{
			$store_wise_rcv_array[$row["STORE_ID"]]['value'] =bcadd($store_wise_rcv_array[$row["STORE_ID"]]['value'],$row["CONS_AMOUNT"],15);
			$store_wise_rcv_array[$row["STORE_ID"]]['qty'] =bcadd($store_wise_rcv_array[$row["STORE_ID"]]['qty'],$row["CONS_QUANTITY"],15);
			
			if($row["RECEIVE_PURPOSE"]==2)
			{
				$dyeing_rcv_array[$row["KNITTING_COMPANY"]]['value'] =bcadd($dyeing_rcv_array[$row["KNITTING_COMPANY"]]['value'],$row["CONS_AMOUNT"],15);
				$dyeing_rcv_array[$row["KNITTING_COMPANY"]]['qty'] =bcadd($dyeing_rcv_array[$row["KNITTING_COMPANY"]]['qty'],$row["CONS_QUANTITY"],15);
			}
			if($row["RECEIVE_PURPOSE"]==15)
			{
				$twisting_rcv_array[$row["KNITTING_COMPANY"]]['value'] =bcadd($twisting_rcv_array[$row["KNITTING_COMPANY"]]['value'],$row["CONS_AMOUNT"],15);
				$twisting_rcv_array[$row["KNITTING_COMPANY"]]['qty'] =bcadd($twisting_rcv_array[$row["KNITTING_COMPANY"]]['qty'],$row["CONS_QUANTITY"],15);
			}
			if($row["RECEIVE_PURPOSE"]==38)
			{
				$re_waxing_rcv_array[$row["KNITTING_COMPANY"]]['value'] =bcadd($re_waxing_rcv_array[$row["KNITTING_COMPANY"]]['value'],$row["CONS_AMOUNT"],15);
				$re_waxing_rcv_array[$row["KNITTING_COMPANY"]]['qty'] =bcadd($re_waxing_rcv_array[$row["KNITTING_COMPANY"]]['qty'],$row["CONS_QUANTITY"],15);
			}
			if($row["RECEIVE_PURPOSE"]==12)
			{
				$reconning_rcv_array[$row["KNITTING_COMPANY"]]['value'] =bcadd($reconning_rcv_array[$row["KNITTING_COMPANY"]]['value'],$row["CONS_AMOUNT"],15);
				$reconning_rcv_array[$row["KNITTING_COMPANY"]]['qty'] =bcadd($reconning_rcv_array[$row["KNITTING_COMPANY"]]['qty'],$row["CONS_QUANTITY"],15);
			}
		}
	}
    // echo "<pre>asdsad"; print_r($store_wise_rcv_array[304]['qty']);die();
    /*==========================================================================================/
    /                   Yarn Issue                      /
    /==========================================================================================*/
    $sql_issue = "SELECT a.CONS_RATE, a.CONS_QUANTITY, a.CONS_AMOUNT, a.TRANSACTION_DATE, a.STORE_ID, c.ISSUE_PURPOSE, c.KNIT_DYE_SOURCE, c.KNIT_DYE_COMPANY 
	from inv_transaction a, inv_issue_master c 
	where a.mst_id=c.id and a.transaction_type in (2,3) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$company and a.STORE_ID > 0 "; //  and transaction_date between '$from_date' and '$to_date'
    // echo $sql_issue;
    $issue_res = sql_select($sql_issue);    
    
    $store_wise_opn_issue_array   = array();
    $kniting_opn_issue_array    = array();
    $dyeing_opn_issue_array     = array();
    $twisting_opn_issue_array   = array();
    $re_waxing_opn_issue_array  = array();    
    $reconning_opn_issue_array  = array();    
    
    $store_wise_issue_array   = array();
    $kniting_issue_array    = array();
    $dyeing_issue_array     = array();
    $twisting_issue_array   = array();
    $re_waxing_issue_array  = array();
    $reconning_issue_array  = array();

    $kniting_party_array  = array();
    $dyeing_party_array   = array();
    $twisting_party_array   = array();
    $re_waxing_party_array  = array();
    $reconning_party_array  = array();
	
    foreach ($issue_res as $row)
    {     
		//if($row["ISSUE_PURPOSE"]==1)
		//{
			//$kniting_party_array[1][$row["KNIT_DYE_COMPANY"]] = $row["KNIT_DYE_COMPANY"];
		//}
		if($row["ISSUE_PURPOSE"]==2)
		{
			$dyeing_party_array[2][$row["KNIT_DYE_COMPANY"]] = $row["KNIT_DYE_COMPANY"];
		}
		if($row["ISSUE_PURPOSE"]==15)
		{
			$twisting_party_array[3][$row["KNIT_DYE_COMPANY"]] = $row["KNIT_DYE_COMPANY"];
		}
		if($row["ISSUE_PURPOSE"]==38)
		{
			$re_waxing_party_array[4][$row["KNIT_DYE_COMPANY"]] = $row["KNIT_DYE_COMPANY"];
		}
		if($row["ISSUE_PURPOSE"]==12)
		{
			$reconning_party_array[5][$row["KNIT_DYE_COMPANY"]] = $row["KNIT_DYE_COMPANY"];
		}
		
		$trans_date = $row["TRANSACTION_DATE"];
		if(strtotime($trans_date) < strtotime($from_date))
		{     
			$store_wise_opn_issue_array[$row["STORE_ID"]]['value'] =bcadd($store_wise_opn_issue_array[$row["STORE_ID"]]['value'],$row["CONS_AMOUNT"],15);
			$store_wise_opn_issue_array[$row["STORE_ID"]]['qty'] =bcadd($store_wise_opn_issue_array[$row["STORE_ID"]]['qty'],$row["CONS_QUANTITY"],15);
			if($row["ISSUE_PURPOSE"]==1 && $row["KNIT_DYE_COMPANY"] !=0)
			{
				$kniting_opn_issue_array[$row["KNIT_DYE_COMPANY"]]['value'] =bcadd($kniting_opn_issue_array[$row["KNIT_DYE_COMPANY"]]['value'],$row["CONS_AMOUNT"],15);
				$kniting_opn_issue_array[$row["KNIT_DYE_COMPANY"]]['qty'] =bcadd($kniting_opn_issue_array[$row["KNIT_DYE_COMPANY"]]['qty'],$row["CONS_QUANTITY"],15);
			}
			if($row["ISSUE_PURPOSE"]==2 && $row["KNIT_DYE_COMPANY"] !=0)
			{
				$dyeing_opn_issue_array[$row["KNIT_DYE_COMPANY"]]['value'] =bcadd($dyeing_opn_issue_array[$row["KNIT_DYE_COMPANY"]]['value'],$row["CONS_AMOUNT"],15);
				$dyeing_opn_issue_array[$row["KNIT_DYE_COMPANY"]]['qty'] =bcadd($dyeing_opn_issue_array[$row["KNIT_DYE_COMPANY"]]['qty'],$row["CONS_QUANTITY"],15);
			}
			if($row["ISSUE_PURPOSE"]==15 && $row["KNIT_DYE_COMPANY"] !=0)
			{
				$twisting_opn_issue_array[$row["KNIT_DYE_COMPANY"]]['value'] =bcadd($twisting_opn_issue_array[$row["KNIT_DYE_COMPANY"]]['value'],$row["CONS_AMOUNT"],15);
				$twisting_opn_issue_array[$row["KNIT_DYE_COMPANY"]]['qty'] =bcadd($twisting_opn_issue_array[$row["KNIT_DYE_COMPANY"]]['qty'],$row["CONS_QUANTITY"],15);
			}
			if($row["ISSUE_PURPOSE"]==38 && $row["KNIT_DYE_COMPANY"] !=0)
			{
				$re_waxing_opn_issue_array[$row["KNIT_DYE_COMPANY"]]['value'] =bcadd($re_waxing_opn_issue_array[$row["KNIT_DYE_COMPANY"]]['value'],$row["CONS_AMOUNT"],15);
				$re_waxing_opn_issue_array[$row["KNIT_DYE_COMPANY"]]['qty'] =bcadd($re_waxing_opn_issue_array[$row["KNIT_DYE_COMPANY"]]['qty'],$row["CONS_QUANTITY"],15);
			}
			if($row["ISSUE_PURPOSE"]==12 && $row["KNIT_DYE_COMPANY"] !=0)
			{
				$reconning_opn_issue_array[$row["KNIT_DYE_COMPANY"]]['value'] =bcadd($reconning_opn_issue_array[$row["KNIT_DYE_COMPANY"]]['value'],$row["CONS_AMOUNT"],15);
				$reconning_opn_issue_array[$row["KNIT_DYE_COMPANY"]]['qty'] =bcadd($reconning_opn_issue_array[$row["KNIT_DYE_COMPANY"]]['qty'],$row["CONS_QUANTITY"],15);
			}
		}
		
		if(strtotime($trans_date) >= strtotime($from_date) && strtotime($trans_date) <= strtotime($to_date))
		{
			$store_wise_issue_array[$row["STORE_ID"]]['value'] =bcadd($store_wise_issue_array[$row["STORE_ID"]]['value'],$row["CONS_AMOUNT"],15);
			$store_wise_issue_array[$row["STORE_ID"]]['qty'] =bcadd($store_wise_issue_array[$row["STORE_ID"]]['qty'],$row["CONS_QUANTITY"],15);
			if($row["ISSUE_PURPOSE"]==1 && $row["KNIT_DYE_COMPANY"] !=0)
			{
				$kniting_issue_array[$row["KNIT_DYE_COMPANY"]]['value'] =bcadd($kniting_issue_array[$row["KNIT_DYE_COMPANY"]]['value'],$row["CONS_AMOUNT"],15);
				$kniting_issue_array[$row["KNIT_DYE_COMPANY"]]['qty'] =bcadd($kniting_issue_array[$row["KNIT_DYE_COMPANY"]]['qty'],$row["CONS_QUANTITY"],15);
			}
			if($row["ISSUE_PURPOSE"]==2 && $row["KNIT_DYE_COMPANY"] !=0)
			{
				$dyeing_issue_array[$row["KNIT_DYE_COMPANY"]]['value'] =bcadd($dyeing_issue_array[$row["KNIT_DYE_COMPANY"]]['value'],$row["CONS_AMOUNT"],15);
				$dyeing_issue_array[$row["KNIT_DYE_COMPANY"]]['qty'] =bcadd($dyeing_issue_array[$row["KNIT_DYE_COMPANY"]]['qty'],$row["CONS_QUANTITY"],15);
			}
			if($row["ISSUE_PURPOSE"]==15 && $row["KNIT_DYE_COMPANY"] !=0)
			{
				$twisting_issue_array[$row["KNIT_DYE_COMPANY"]]['value'] =bcadd($twisting_issue_array[$row["KNIT_DYE_COMPANY"]]['value'],$row["CONS_AMOUNT"],15);
				$twisting_issue_array[$row["KNIT_DYE_COMPANY"]]['qty'] =bcadd($twisting_issue_array[$row["KNIT_DYE_COMPANY"]]['qty'],$row["CONS_QUANTITY"],15);
			}
			if($row["ISSUE_PURPOSE"]==38 && $row["KNIT_DYE_COMPANY"] !=0)
			{
				$re_waxing_issue_array[$row["KNIT_DYE_COMPANY"]]['value'] =bcadd($re_waxing_issue_array[$row["KNIT_DYE_COMPANY"]]['value'],$row["CONS_AMOUNT"],15);
				$re_waxing_issue_array[$row["KNIT_DYE_COMPANY"]]['qty'] =bcadd($re_waxing_issue_array[$row["KNIT_DYE_COMPANY"]]['qty'],$row["CONS_QUANTITY"],15);
			}
			if($row["ISSUE_PURPOSE"]==12 && $row["KNIT_DYE_COMPANY"] !=0)
			{
				$reconning_issue_array[$row["KNIT_DYE_COMPANY"]]['value'] =bcadd($reconning_issue_array[$row["KNIT_DYE_COMPANY"]]['value'],$row["CONS_AMOUNT"],15);
				$reconning_issue_array[$row["KNIT_DYE_COMPANY"]]['qty'] =bcadd($reconning_issue_array[$row["KNIT_DYE_COMPANY"]]['qty'],$row["CONS_QUANTITY"],15);
			}
		}
    }
	
    // echo "<pre>";print_r($store_wise_opn_issue_array[304]['qty']);die();
    /*==========================================================================================/
	/                   Yarn Transfer                   /
	/==========================================================================================*/
	
	$sql_transfer = "SELECT a.CONS_RATE, a.CONS_QUANTITY, a.CONS_AMOUNT, a.TRANSACTION_DATE, a.TRANSACTION_TYPE, a.STORE_ID 
	from inv_transaction a 
	where a.transaction_type in (5,6) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company and a.STORE_ID>0"; // and transaction_date between '$from_date' and '$to_date'
    //echo $sql_transfer;
    $transfer_res = sql_select($sql_transfer);    
    
    $store_wise_opn_trnsfr_array = array();
    $store_wise_trnsfr_array = array();
    $party_wise_opn_trnsfr_array = array();
    $party_wise_trnsfr_array = array();
    foreach ($transfer_res as $row)
    {
		$trans_date = $row["TRANSACTION_DATE"];
		if(strtotime($trans_date) < strtotime($from_date))
		{
			$store_wise_opn_trnsfr_array[$row["STORE_ID"]][$row["TRANSACTION_TYPE"]]['value'] =bcadd($store_wise_opn_trnsfr_array[$row["STORE_ID"]][$row["TRANSACTION_TYPE"]]['value'],$row["CONS_AMOUNT"],15);       
			$store_wise_opn_trnsfr_array[$row["STORE_ID"]][$row["TRANSACTION_TYPE"]]['qty'] =bcadd($store_wise_opn_trnsfr_array[$row["STORE_ID"]][$row["TRANSACTION_TYPE"]]['qty'],$row["CONS_QUANTITY"],15); 
		}
		
		if(strtotime($trans_date) >= strtotime($from_date) && strtotime($trans_date) <= strtotime($to_date))
		{
			$store_wise_trnsfr_array[$row["STORE_ID"]][$row["TRANSACTION_TYPE"]]['value'] =bcadd($store_wise_trnsfr_array[$row["STORE_ID"]][$row["TRANSACTION_TYPE"]]['value'],$row["CONS_AMOUNT"],15);       
			$store_wise_trnsfr_array[$row["STORE_ID"]][$row["TRANSACTION_TYPE"]]['qty'] =bcadd($store_wise_trnsfr_array[$row["STORE_ID"]][$row["TRANSACTION_TYPE"]]['qty'],$row["CONS_QUANTITY"],15);   
		}   
    }
	
	
	/*$sql_trans = "SELECT a.STORE_ID, a.CONS_RATE, a.CONS_QUANTITY, a.TRANSACTION_DATE, a.TRANSACTION_TYPE, a.ITEM_CATEGORY, a.CONS_AMOUNT
	from inv_transaction a 
	where a.item_category=1 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company and a.store_id > 0"; // and transaction_date between '$from_date' and '$to_date'
    //echo $sql_transfer;
    $sql_trans_result = sql_select($sql_trans);    
    foreach ($sql_trans_result as $row)
    {
		$trans_date = $row["TRANSACTION_DATE"];
		if(strtotime($trans_date) < strtotime($from_date))
		{
			if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
			{
				$store_wise_closing_array[$row["STORE_ID"]]['value']=bcadd($store_wise_closing_array[$row["STORE_ID"]]['value'],$row["CONS_AMOUNT"],15);       
			}
			else
			{
				$store_wise_closing_array[$row["STORE_ID"]]['value']=bcsub($store_wise_closing_array[$row["STORE_ID"]]['value'],$row["CONS_AMOUNT"],15);
			}
			 
		}
		if(strtotime($trans_date) >= strtotime($from_date) && strtotime($trans_date) <= strtotime($to_date))
		{
			if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
			{
				$store_wise_closing_array[$row["STORE_ID"]]['value']=bcadd($store_wise_closing_array[$row["STORE_ID"]]['value'],$row["CONS_AMOUNT"],15);       
			}
			else
			{
				$store_wise_closing_array[$row["STORE_ID"]]['value']=bcsub($store_wise_closing_array[$row["STORE_ID"]]['value'],$row["CONS_AMOUNT"],15);
			}  
		}   
    }*/
	
    //echo "<pre>";print_r($store_wise_closing_array);die;
    // echo $total_rcv_opening_amt;

    /*==========================================================================================/
  /                   grey fab Receive                  /
  /==========================================================================================*/
    $sql_grey = "SELECT a.ORDER_RATE, c.CURRENCY_ID, a.CONS_RATE, a.CONS_AMOUNT, a.CONS_QUANTITY, a.TRANSACTION_DATE, a.STORE_ID, a.ITEM_CATEGORY, c.RECEIVE_PURPOSE, c.KNITTING_COMPANY, c.KNITTING_SOURCE 
	from inv_transaction a, inv_receive_master c where a.mst_id=c.id and a.transaction_type in (1,4) and a.item_category=13 and c.KNITTING_COMPANY <> 0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$company"; 
    // echo $sql_grey;die();
    $grey_res = sql_select($sql_grey);
    
    $knit_opn_rcv_array = array();      
    $knit_rcv_array   = array();
    // $knit_party_array  = array();

    foreach ($grey_res as $row)
    {     
		$trans_date = $row["TRANSACTION_DATE"];
		if(strtotime($trans_date) < strtotime($from_date))
		{
			$knit_opn_rcv_array[1][$row["KNITTING_COMPANY"]]['value'] =bcadd($knit_opn_rcv_array[1][$row["KNITTING_COMPANY"]]['value'],$row["CONS_AMOUNT"],15); 
			$knit_opn_rcv_array[1][$row["KNITTING_COMPANY"]]['qty'] =bcadd($knit_opn_rcv_array[1][$row["KNITTING_COMPANY"]]['qty'],$row["CONS_QUANTITY"],15);
			$knit_opn_rcv_array[1][$row["KNITTING_COMPANY"]]['source'] = $row["KNITTING_SOURCE"];
		}
		
		if(strtotime($trans_date) >= strtotime($from_date) && strtotime($trans_date) <= strtotime($to_date))
		{
			$knit_rcv_array[$row["KNITTING_COMPANY"]]['value'] =bcadd($knit_rcv_array[$row["KNITTING_COMPANY"]]['value'],$row["CONS_AMOUNT"],15);
			$knit_rcv_array[$row["KNITTING_COMPANY"]]['qty'] =bcadd($knit_rcv_array[$row["KNITTING_COMPANY"]]['qty'],$row["CONS_QUANTITY"],15);
			$knit_rcv_array[$row["KNITTING_COMPANY"]]['source'] = $row["KNITTING_SOURCE"];
		}
    }

    //echo "<pre>";print_r($store_wise_opn_trnsfr_array);die();

    $yarn_rowspan = 0;
    foreach ($yarn_store as $loc_type => $loc_type_data) 
    {
		foreach ($loc_type_data as $store_id => $row) 
		{
			$yarn_opn_rcv_qty   = $store_wise_opn_rcv_array[$store_id]['qty'];
			$yarn_opn_rcv_val   = $store_wise_opn_rcv_array[$store_id]['value'];
			
			$yarn_opn_issue_qty = $store_wise_opn_issue_array[$store_id]['qty'];
			$yarn_opn_issue_val = $store_wise_opn_issue_array[$store_id]['value'];
			
			$trnsfr_opn_in_qty  = $store_wise_opn_trnsfr_array[$store_id][5]['qty'];
			$trnsfr_opn_in_val  = $store_wise_opn_trnsfr_array[$store_id][5]['value'];
			
			$trnsfr_out_opn_qty = $store_wise_opn_trnsfr_array[$store_id][6]['qty'];
			$trnsfr_out_opn_val = $store_wise_opn_trnsfr_array[$store_id][6]['value'];
			//====================================================================
			$yarn_rcv_qty   = $store_wise_rcv_array[$store_id]['qty'];
			$yarn_rcv_val   = $store_wise_rcv_array[$store_id]['value'];
			
			$yarn_issue_qty = $store_wise_issue_array[$store_id]['qty'];
			$yarn_issue_val = $store_wise_issue_array[$store_id]['value'];
			
			$trnsfr_in_qty  = $store_wise_trnsfr_array[$store_id][5]['qty'];
			$trnsfr_in_val  = $store_wise_trnsfr_array[$store_id][5]['value'];
			
			$trnsfr_out_qty = $store_wise_trnsfr_array[$store_id][6]['qty'];
			$trnsfr_out_val = $store_wise_trnsfr_array[$store_id][6]['value'];
			
			$yarn_opening_qty   = bcsub(bcadd($yarn_opn_rcv_qty,$trnsfr_opn_in_qty,15),bcadd($yarn_opn_issue_qty,$trnsfr_out_opn_qty,15),15);
			// echo $store_id."==(".$yarn_opn_rcv_qty ."+". $trnsfr_opn_in_qty.") - (".$yarn_opn_issue_qty ."+". $trnsfr_out_opn_qty.")<br>";
			
			$tot_yarn_rcv_qty   = bcadd($yarn_rcv_qty,$trnsfr_in_qty,15);
			// echo $store_id."==".$yarn_rcv_qty ."+". $trnsfr_in_qty."<br>";
			$tot_yarn_issue_qty = bcadd($yarn_issue_qty,$trnsfr_out_qty,15);
			// echo $store_id."==".$yarn_issue_qty ."+". $trnsfr_out_qty."<br>";
			$yarn_closing_qty   = bcsub(bcadd($yarn_opening_qty,$tot_yarn_rcv_qty,15),$tot_yarn_issue_qty,15);
			if($yarn_opening_qty>0 || $tot_yarn_rcv_qty>0 || $tot_yarn_issue_qty>0 || $yarn_closing_qty>0)
			{
				$yarn_rowspan++;
				//echo $store_id."=$yarn_opening_qty=$tot_yarn_rcv_qty=$tot_yarn_issue_qty=$yarn_closing_qty<br>";
			}
		}
    }
	//echo $yarn_rowspan;die;
    //====================
    /*
    $kniting_rowspan = 0;
    foreach ($kniting_party_array as $loc_type => $loc_type_data) 
    {
		foreach ($loc_type_data as $party_id => $row) 
		{
			$knit_opn_rcv_qty   = $knit_opn_rcv_array[1][$party_id]['qty'];
			$knit_opn_rcv_val   = $knit_opn_rcv_array[1][$party_id]['value'];
			
			$yarn_opn_issue_qty = $kniting_opn_issue_array[$party_id]['qty'];
			$yarn_opn_issue_val = $kniting_opn_issue_array[$party_id]['value'];
			
			$knit_rcv_qty   = $knit_rcv_array[$party_id]['qty'];
			$knit_rcv_val   = $knit_rcv_array[$party_id]['value'];
			$source     = $row['source'];
			
			$knit_issue_qty = $kniting_issue_array[$party_id]['qty'];
			$knit_issue_val = $kniting_issue_array[$party_id]['value'];
			$knit_opening_qty   = bcsub($yarn_opn_issue_qty,$knit_opn_rcv_qty,15);
			$tot_knit_rcv_qty   = bcadd($knit_rcv_qty,$trnsfr_in_qty,15);
			$tot_knit_issue_qty = bcadd($knit_issue_qty,$trnsfr_out_qty,15);
			$knit_closing_qty   = bcsub(bcadd($knit_opening_qty,$tot_knit_issue_qty,15),$tot_knit_rcv_qty,15);
			$knit_closing_val   = bcsub(bcadd($yarn_opn_issue_val,$trnsfr_out_qty,15),bcadd($knit_opn_rcv_val,$trnsfr_in_val,15),15);
			if($knit_opening_qty>=1 || $tot_knit_rcv_qty>=1 || $tot_knit_issue_qty>=1 || $knit_closing_qty>=1)
			{
				$kniting_rowspan++;
			}
		}
    }
    */
    //====================
    $dyeing_rowspan = 0;
    foreach ($dyeing_party_array as $loc_type => $loc_type_data) 
    {
		foreach ($loc_type_data as $party_id => $row) 
		{
			$dyeing_opn_rcv_qty   = $dyeing_opn_rcv_array[$party_id]['qty'];
			$dyeing_opn_rcv_val   = $dyeing_opn_rcv_array[$party_id]['value'];
			
			$yarn_opn_issue_qty = $kniting_opn_issue_array[$party_id]['qty'];
			$yarn_opn_issue_val = $kniting_opn_issue_array[$party_id]['value'];
			
			$dyeing_rcv_qty   = $dyeing_rcv_array[$party_id]['qty'];
			$dyeing_rcv_val   = $dyeing_rcv_array[$party_id]['value'];
			
			$dyeing_issue_qty = $dyeing_issue_array[$party_id]['qty'];
			$dyeing_issue_val = $dyeing_issue_array[$party_id]['value'];
			
			$dyeing_opening_qty   = bcsub($yarn_opn_issue_qty,$dyeing_opn_rcv_qty,15);
			$tot_dyeing_rcv_qty   = bcadd($dyeing_rcv_qty,$trnsfr_in_qty,15);
			$tot_dyeing_issue_qty   = bcadd($dyeing_issue_qty,$trnsfr_out_qty,15);
			$dyeing_closing_qty   = bcsub(bcadd($dyeing_opening_qty,$tot_dyeing_issue_qty,15),$tot_dyeing_rcv_qty,15);
			$dyeing_closing_val   = bcsub($yarn_opn_issue_val,$dyeing_opn_rcv_val,15);
			if($dyeing_opening_qty>=1 || $tot_dyeing_rcv_qty>=1 || $tot_dyeing_issue_qty>=1 || $dyeing_closing_qty>=1)
			{
				$dyeing_rowspan++;
			}
		}
    }
    //====================
    $twisting_rowspan = 0;
    foreach ($twisting_party_array as $loc_type => $loc_type_data) 
    {
		foreach ($loc_type_data as $party_id => $row) 
		{
			$twisting_opn_rcv_qty   = $twisting_opn_rcv_array[$party_id]['qty'];
			$twisting_opn_rcv_val   = $twisting_opn_rcv_array[$party_id]['value'];
			
			$yarn_opn_issue_qty = $kniting_opn_issue_array[$party_id]['qty'];
			$yarn_opn_issue_val = $kniting_opn_issue_array[$party_id]['value'];
			
			$twisting_rcv_qty   = $twisting_rcv_array['qty'];
			$twisting_rcv_val   = $twisting_rcv_array['value'];
			
			$twisting_issue_qty = $twisting_issue_array[$party_id]['qty'];
			$twisting_issue_val = $twisting_issue_array[$party_id]['value'];
			
			$twisting_opening_qty   = bcsub($yarn_opn_issue_qty,$twisting_opn_rcv_qty,15); 
			$tot_twisting_rcv_qty   = bcadd($twisting_rcv_qty,$trnsfr_in_qty,15);
			$tot_twisting_issue_qty = bcadd($twisting_issue_qty,$trnsfr_out_qty,15);
			$twisting_closing_qty   = bcsub(bcadd($twisting_opening_qty,$tot_twisting_issue_qty,15),$tot_twisting_rcv_qty,15);
			$twisting_closing_val   = bcsub(bcadd($yarn_opn_issue_val,$trnsfr_out_qty,15),bcadd($twisting_opn_rcv_val,$trnsfr_in_val,15),15);
			if($twisting_opening_qty>=1 || $tot_twisting_rcv_qty>=1 || $tot_twisting_issue_qty>=1 || $twisting_closing_qty>=1)
			{
				$twisting_rowspan++;
			}
		}
    }
    //====================
    $re_waxing_rowspan = 0;
    foreach ($re_waxing_party_array as $loc_type => $loc_type_data) 
    {
		foreach ($loc_type_data as $party_id => $row) 
		{
			$re_waxing_opn_rcv_qty  = $re_waxing_opn_rcv_array[$party_id]['qty'];
			$re_waxing_opn_rcv_val  = $re_waxing_opn_rcv_array[$party_id]['value'];
			
			$yarn_opn_issue_qty = $kniting_opn_issue_array[$party_id]['qty'];
			$yarn_opn_issue_val = $kniting_opn_issue_array[$party_id]['value'];
			
			$re_waxing_rcv_qty  = $re_waxing_rcv_array['qty'];
			$re_waxing_rcv_val  = $re_waxing_rcv_array['value'];
			
			$re_waxing_issue_qty = $re_waxing_issue_array[$party_id]['qty'];
			$re_waxing_issue_val = $re_waxing_issue_array[$party_id]['value'];
			
			$re_waxing_opening_qty  = bcsub($yarn_opn_issue_qty,$re_waxing_opn_rcv_qty,15);
			$tot_re_waxing_rcv_qty  = bcadd($re_waxing_rcv_qty,$trnsfr_in_qty,15);
			$tot_re_waxing_issue_qty = bcadd($re_waxing_issue_qty,$trnsfr_out_qty,15);
			$re_waxing_closing_qty  = bcsub(bcadd($re_waxing_opening_qty,$tot_re_waxing_issue_qty,15),$tot_re_waxing_rcv_qty,15);
			$re_waxing_closing_val  = bcsub(bcadd($yarn_opn_issue_val,$trnsfr_out_qty,15),bcadd($re_waxing_opn_rcv_val,$trnsfr_in_val,15),15);
			if($re_waxing_opening_qty>=1 || $tot_re_waxing_rcv_qty>=1 || $tot_re_waxing_issue_qty>=1 || $re_waxing_closing_qty>=1)
			{
				$re_waxing_rowspan++;
			}
		}
    }
    //====================
    $reconning_rowspan = 0;
    foreach ($reconning_party_array as $loc_type => $loc_type_data) 
    {
		foreach ($loc_type_data as $party_id => $row) 
		{
			$reconning_opn_rcv_qty  = $reconning_opn_rcv_array[$party_id]['qty'];
			$reconning_opn_rcv_val  = $reconning_opn_rcv_array[$party_id]['value'];
			
			$yarn_opn_issue_qty = $kniting_opn_issue_array[$party_id]['qty'];
			$yarn_opn_issue_val = $kniting_opn_issue_array[$party_id]['value'];
			
			$reconning_rcv_qty  = $reconning_rcv_array['qty'];
			$reconning_rcv_val  = $reconning_rcv_array['value'];
			
			$reconning_issue_qty = $reconning_issue_array[$party_id]['qty'];
			$reconning_issue_val = $reconning_issue_array[$party_id]['value'];
			
			$reconning_opening_qty  = bcsub($yarn_opn_issue_qty,$reconning_opn_rcv_qty,15);
			$tot_reconning_rcv_qty  = bcadd($reconning_rcv_qty,$trnsfr_in_qty,15);
			$tot_reconning_issue_qty = bcadd($reconning_issue_qty,$trnsfr_out_qty,15);
			$reconning_closing_qty  = bcsub(bcadd($reconning_opening_qty,$tot_reconning_issue_qty,15),$tot_reconning_rcv_qty,15);
			$reconning_closing_val  = bcsub(bcadd($yarn_opn_issue_val,$trnsfr_out_qty,15),bcadd($reconning_opn_rcv_val,$trnsfr_in_val,15),15);
			if($reconning_opening_qty>=1 || $tot_reconning_rcv_qty>=1 || $tot_reconning_issue_qty>=1 || $reconning_closing_qty>=1)
			{
				$reconning_rowspan++;
			}
		}
    }
    
  ?>
  <div>
    <center><h3>Yarn Details</h3></center>
    <center><h3>Date : <? echo change_date_format($from_date);?> To <? echo change_date_format($to_date);?></h3></center>
    <table border="1" rules="all" class="rpt_table" width="1040" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th width="150">Location Type</th>
                <th width="150">Location Name</th>
                <th width="120">Opening Qty</th>
                <th width="120">Receive Qty </th>
                <th width="120">Delivery Qty</th>
                <th width="120">Closing Qty</th>
                <th width="120">Closing Value</th>
                <th width="">Waiver qty</th>
              </tr>
        </thead>
    </table>
    <div style="width:1040px; max-height:330px; overflow-y:auto" id="scroll_body">
        <table border="1" rules="all" class="rpt_table" width="1020" cellpadding="0" cellspacing="0">
            <tbody>
			<?
            $r=1;
            $i=1;
            $grnd_opening_qty   = 0;
            $grnd_rcv_qty     = 0;
            $grnd_issue_qty   = 0;
            $grnd_closing_qty   = 0;
            $grnd_closing_val   = 0;
            
            $total_yarn_opening_qty   = 0;
            $total_yarn_rcv_qty     = 0;
            $total_yarn_issue_qty     = 0;
            $total_yarn_closing_qty   = 0;
            $total_yarn_closing_val   = 0;
			//echo "<pre>";print_r($store_wise_opn_trnsfr_array);die;

            //echo $yarn_rowspan."==".count($yarn_store[0]);
			foreach ($yarn_store as $loc_key => $loc_value) 
			{
                foreach ($loc_value as $store_id => $row) 
                {    
					$yarn_opn_rcv_qty   = $store_wise_opn_rcv_array[$store_id]['qty'];
					$yarn_opn_rcv_val   = $store_wise_opn_rcv_array[$store_id]['value'];
					
					$yarn_opn_issue_qty = $store_wise_opn_issue_array[$store_id]['qty'];
					$yarn_opn_issue_val = $store_wise_opn_issue_array[$store_id]['value'];
					
					$trnsfr_opn_in_qty  = $store_wise_opn_trnsfr_array[$store_id][5]['qty'];
					$trnsfr_opn_in_val  = $store_wise_opn_trnsfr_array[$store_id][5]['value'];
					
					$trnsfr_out_opn_qty = $store_wise_opn_trnsfr_array[$store_id][6]['qty'];
					$trnsfr_out_opn_val = $store_wise_opn_trnsfr_array[$store_id][6]['value'];
					//====================================================================
					$yarn_rcv_qty   = $store_wise_rcv_array[$store_id]['qty'];
					$yarn_rcv_val   = $store_wise_rcv_array[$store_id]['value'];
					
					$yarn_issue_qty = $store_wise_issue_array[$store_id]['qty'];
					$yarn_issue_val = $store_wise_issue_array[$store_id]['value'];
					
					$trnsfr_in_qty  = $store_wise_trnsfr_array[$store_id][5]['qty'];
					$trnsfr_in_val  = $store_wise_trnsfr_array[$store_id][5]['value'];
					
					$trnsfr_out_qty = $store_wise_trnsfr_array[$store_id][6]['qty'];
					$trnsfr_out_val = $store_wise_trnsfr_array[$store_id][6]['value'];
					
					$yarn_opening_qty   = bcsub(bcadd($yarn_opn_rcv_qty,$trnsfr_opn_in_qty,15),bcadd($yarn_opn_issue_qty,$trnsfr_out_opn_qty,15),15);
					// echo $store_id."==(".$yarn_opn_rcv_qty ."+". $trnsfr_opn_in_qty.") - (".$yarn_opn_issue_qty ."+". $trnsfr_out_opn_qty.")<br>";
					
					$tot_yarn_rcv_qty   = bcadd($yarn_rcv_qty,$trnsfr_in_qty,15);
					// echo $store_id.'=='.$yarn_issue_qty."==".$trnsfr_out_qty ."<br>";
					$tot_yarn_issue_qty = bcadd($yarn_issue_qty,$trnsfr_out_qty,15);
					// echo $store_id."==".$yarn_issue_qty ."+". $trnsfr_out_qty."<br>";
					$yarn_closing_qty   = bcsub(bcadd($yarn_opening_qty,$tot_yarn_rcv_qty,15),$tot_yarn_issue_qty,15);
					$yarn_closing_val   = bcsub(bcadd(bcadd(bcadd($yarn_opn_rcv_val,$trnsfr_opn_in_val,15),$yarn_rcv_val,15),$trnsfr_in_val,15),bcadd(bcadd(bcadd($yarn_opn_issue_val,$trnsfr_out_opn_val,15),$yarn_issue_val,15),$trnsfr_out_val,15),15);
					//($yarn_opn_rcv_val + $trnsfr_opn_in_val+$yarn_rcv_val+$trnsfr_in_val) - ($yarn_opn_issue_val + $trnsfr_out_opn_val+$yarn_issue_val+$trnsfr_out_val);
					
					if(number_format($yarn_opening_qty,2)!=0 || number_format($tot_yarn_rcv_qty,2)!=0 || number_format($tot_yarn_issue_qty,2)!=0 || number_format($yarn_closing_qty,2)!=0)
					{
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;        
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">
						<? if($r==1){?>
                            <td width="150" valign="middle" rowspan="<? echo $yarn_rowspan+1;?>" align="left"><? echo $location_type_array[$loc_key];;?></td>
                            <?}?>
                            <td width="150" align="left" title="<? echo $store_id;?>">
                            <a href="javascript:void()" onclick="open_store_popup('<? echo $company."_".$from_date."_".$to_date."_".$exchange_rate."_".$store_id;?>','open_store_wise_details_popup')">
                            <? echo $lib_store[$store_id];?>
                            </a>
                            </td>
                            <td width="120" align="right"><? echo number_format($yarn_opening_qty,2);?></td>
                            <td width="120" align="right"><? echo number_format($tot_yarn_rcv_qty,2);?></td>
                            <td width="120" align="right"><? echo number_format($tot_yarn_issue_qty,2);?></td>
                            <td width="120" align="right"><? echo number_format($yarn_closing_qty,2);?></td>
                            <td width="120" align="right"><? echo number_format($yarn_closing_val,2);?></td>
                            <td width="" align="right">&nbsp;</td>
						</tr>
						<?
						$r++;
						$i++;
						$total_yarn_opening_qty   =bcadd($total_yarn_opening_qty,$yarn_opening_qty,15);
						$total_yarn_rcv_qty     =bcadd($total_yarn_rcv_qty,$tot_yarn_rcv_qty,15);
						$total_yarn_issue_qty   =bcadd($total_yarn_issue_qty,$tot_yarn_issue_qty,15);
						$total_yarn_closing_qty   =bcadd($total_yarn_closing_qty,$yarn_closing_qty,15);
						$total_yarn_closing_val   =bcadd($total_yarn_closing_val,$yarn_closing_val,15);
					}
                }
            }
            ?>
            <tr bgcolor="#C1C1C1" style="font-weight:bold;text-align:right;">
                <td>Total</td>
                <td><? echo number_format($total_yarn_opening_qty,0);?></td>
                <td><? echo number_format($total_yarn_rcv_qty,0);?></td>
                <td><? echo number_format($total_yarn_issue_qty,0);?></td>
                <td><? echo number_format($total_yarn_closing_qty,0);?></td>
                <td><? echo number_format($total_yarn_closing_val,0);?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
             <!-- =========================== knitting part start ======================== -->
            <tr bgcolor="#999999" style="font-weight:bold;text-align:center;">
                <td width="150"></td>
                <td width="150">Party Name</td>
                <td width="120">Opening Qty</td>
                <td width="120">Receive Qty </td>
                <td width="120">Delivery Qty</td>
                <td width="120">Closing Qty</td>                
                <td width="120">Closing Value</td>
                <td width="">Waiver qty</td>
            </tr>
            <?

            $close_prog_sql="select a.inv_pur_req_mst_id,max(a.closing_date) as closing_date ,b.requisition_no from inv_reference_closing a,ppl_yarn_requisition_entry b where a.inv_pur_req_mst_id=b.knit_id and a.reference_type=2 and a.closing_status=1 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company  group by a.inv_pur_req_mst_id,b.requisition_no";//and a.closing_date<='" . $to_date . "'

            //echo $close_prog_sql;

            $close_prog_resutl = sql_select($close_prog_sql);

            $close_prog_data = array();
            $close_prog_requisition_no = array();
            $waiver_prog_no = array();
            foreach ($close_prog_resutl as $row) 
            {
            	$prog_no = $row[csf("inv_pur_req_mst_id")];
            	$requisition_no = $row[csf("requisition_no")];
            	$closing_date = $row[csf("closing_date")];

            	$close_prog_data[$prog_no]['prog_no']=$row[csf("inv_pur_req_mst_id")];
            	$close_prog_data[$prog_no]['closing_date']=$row[csf("closing_date")];
            	$close_prog_data[$prog_no]['production_mrr_no']=$row[csf("mrr_system_no")];	

            	$close_prog_requisition_no[$requisition_no]=$row[csf("requisition_no")];           	

            	if( strtotime($closing_date) >= strtotime($from_date) && strtotime($closing_date) <= strtotime($to_date) )
				{
					$waiver_prog_no[$prog_no]= $prog_no;
					$waiver_requisition_no[$requisition_no]= $requisition_no;
				}

            }

            //echo "<pre>";
            //print_r($waiver_requisition_no); //die();
            //pi_wo_batch_no,

            $production_sql = "select c.knitting_company,c.booking_id,c.receive_basis,c.ref_closing_status,

            sum(case when a.transaction_type =1 and c.entry_form=2 and a.transaction_date<'" . $from_date . "' then b.used_qty else 0 end) as production_qty_opening,
            sum(case when a.transaction_type =1 and c.entry_form=2 and a.transaction_date<'" . $from_date . "' then b.amount else 0 end) as production_opening_amt,          

            sum(case when a.transaction_type =1 and c.entry_form=2 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then b.used_qty else 0 end) as production_qty,
            sum(case when a.transaction_type =1 and c.entry_form=2 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then b.amount else 0 end) as production_amt,

            sum(case when a.transaction_type =1 and c.entry_form=2 and a.transaction_date<='" . $to_date . "' then b.used_qty else 0 end) as production_picup_waiver_qty
           
            from inv_transaction a,pro_material_used_dtls b,inv_receive_master c where a.mst_id=c.id and b.mst_id=c.id and b.item_category=1 and b.entry_form=2 and c.entry_form=2 and c.receive_basis in (1,2) and c.company_id=$company and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 group by c.knitting_company,c.booking_id,c.receive_basis,c.ref_closing_status";

            //echo $production_sql; die();

            $result_production_sql = sql_select($production_sql);

            $production_array = array();
            foreach ($result_production_sql as $row)
            {
                $party = $row[csf("knitting_company")];
               
                if( $row[csf('receive_basis')] == 2) // plan production
                {
                	$close_prog = $close_prog_data[$row[csf('booking_id')]]['prog_no'];                	
                	//echo "$party=>$close_prog==ttttt".$row[csf('booking_id')]."<br>";

                	if($close_prog!=$row[csf('booking_id')]) // close prog production
                	{
                		$production_array[$party]['production_qty_opening'] += $row[csf("production_qty_opening")]; 
                		$production_array[$party]['production_opening_amt'] += $row[csf("production_opening_amt")]; 
                	}                	
                }
                else // sample booking production
                {
                	$production_array[$party]['production_qty_opening'] += $row[csf("production_qty_opening")]; 
                	$production_array[$party]['production_opening_amt'] += $row[csf("production_opening_amt")]; 
                }
                
                $production_array[$party]['production_qty'] += $row[csf("production_qty")]; // date range between
                $production_array[$party]['production_amt'] += $row[csf("production_amt")]; // date range between
                //echo "<pre>";
                //print_r($waiver_prog_no);

                if( $row[csf('receive_basis')] == 2 ) // Close Prog 
                {
                	//echo $party."=>".$row[csf('receive_basis')]."&&". $close_prog."==".$row[csf('booking_id')]."<br>"; 
                	$waiverProgNo = $waiver_prog_no[$row[csf('booking_id')]];

                	if($waiverProgNo==$row[csf('booking_id')])
                	{
                		$production_array[$party]['close_prog_production_qty'] += $row[csf("production_picup_waiver_qty")];
                	}                       
                }  

            }
            unset($result_production_sql);
            //echo "<pre>";
            //print_r($production_array);
            //die();

            $sql_issue = "select a.requisition_no,c.issue_basis,c.knit_dye_source,c.knit_dye_company,
            sum(case when a.transaction_type =2 and c.entry_form=3 and a.transaction_date<'" . $from_date . "' then a.cons_quantity else 0 end) as issue_opening_qty,
            sum(case when a.transaction_type =2 and c.entry_form=3 and a.transaction_date<'" . $from_date . "' then a.cons_amount else 0 end) as issue_opening_amt,          
            sum(case when a.transaction_type=2 and c.entry_form=3 and a.transaction_date  between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as issue_qty,
            sum(case when a.transaction_type=2 and c.entry_form=3 and a.transaction_date  between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as issue_amt,

            sum(case when a.transaction_type =2 and c.entry_form=3 and a.transaction_date<='" . $to_date . "' then a.cons_quantity else 0 end) as issue_pickup_waiver_qty

            from inv_transaction a, inv_issue_master c
            where a.mst_id=c.id and a.item_category=1 and a.transaction_type in (2) and c.issue_basis in (1,3) and c.issue_purpose in (1,8) and c.company_id=$company and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.requisition_no,c.issue_basis,c.knit_dye_source,c.knit_dye_company";

            //echo $sql_issue; die();
            $result_sql_issue = sql_select($sql_issue);

            $issue_array = array();
            $yarn_party_arr= array();
            foreach ($result_sql_issue as $row)
            {
                $party = $row[csf("knit_dye_company")];

                if($issue_array[$party]['issue_source']==1)
                {
                	$partyName = $lib_company[$party];
                }else{
                	$partyName = $lib_supplier[$party];
                }

                $yarn_party_arr[$party]=$partyName;

                $issue_array[$party]['issue_source']=$row[csf("knit_dye_source")];

                if($row[csf("issue_basis")]==3) // plan/requisition
                {
                	$close_prog_reqs_no = $close_prog_requisition_no[$row[csf("requisition_no")]];
                	//echo $close_prog_requisition_no[$row[csf("requisition_no")]]."<br>";
                	if($close_prog_reqs_no!=$row[csf("requisition_no")]) // ommit close prog
	                {
	                	$issue_array[$party]['issue_opening_qty'] += $row[csf("issue_opening_qty")];
	                	$issue_array[$party]['issue_opening_amt'] += $row[csf("issue_opening_amt")];
	                } 
                }
                else // sample booking 
                {
                	$issue_array[$party]['issue_opening_qty'] += $row[csf("issue_opening_qty")];
                	$issue_array[$party]['issue_opening_amt'] += $row[csf("issue_opening_amt")];
                }           

                $issue_array[$party]['issue_qty'] += $row[csf("issue_qty")]; // Date Range
                $issue_array[$party]['issue_amt'] += $row[csf("issue_amt")]; // Date Range            
              
                if( $row[csf("issue_basis")]==3) // Plan/requsition
                {
                	
                	$waiverRequisitionNo = $waiver_requisition_no[$row[csf('requisition_no')]];

                	//echo  $waiverRequisitionNo ."==". $row[csf('requisition_no')]."<br>";

                	if( $waiverRequisitionNo == $row[csf('requisition_no')] )
                	{
                		//echo $party."=>".$waiverRequisitionNo."==".$row[csf('requisition_no')]."<br>";
                		$issue_array[$party]['close_prog_issue_qty'] += $row[csf("issue_pickup_waiver_qty")];
                	}                    
                }                 
            }
            unset($result_sql_issue);
            
            //echo "<pre>";
            //print_r($issue_array);
            
            //die();

            $sql_receive = "Select c.knitting_company,c.booking_id,c.receive_basis,
            sum(case when a.transaction_type =4 and c.entry_form=9 and a.transaction_date<'" . $from_date . "' then (a.cons_quantity+a.cons_reject_qnty) else 0 end) as rcv_total_opening,
            sum(case when a.transaction_type =4 and c.entry_form=9 and a.transaction_date<'" . $from_date . "' then a.cons_amount else 0 end) as rcv_total_opening_amt,
            sum(case when a.transaction_type =4 and c.entry_form=9 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then (a.cons_quantity+a.cons_reject_qnty) else 0 end) as receive_qty,
            sum(case when a.transaction_type =4 and c.entry_form=9 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as receive_amt,
            sum(case when a.transaction_type =4 and c.entry_form=9 and a.transaction_date<='" . $to_date . "' then (a.cons_quantity+a.cons_reject_qnty) else 0 end) as rcv_picup_waiver_qty

            from inv_transaction a, inv_receive_master c where a.mst_id=c.id and a.transaction_type in (4) and a.item_category in (1) and c.receive_basis in (1,3) and c.company_id=$company and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
            group by c.knitting_company,c.booking_id,c.receive_basis";

            //echo $sql_receive;
            //,tmp_issue_id d --and c.issue_id= d.issue_id 
            $result_sql_receive = sql_select($sql_receive);
            $receive_array = array();
            foreach ($result_sql_receive as $row)
            {
                $party = $row[csf("knitting_company")];
                
                if($row[csf("receive_basis")]==3) // plan/requsition basis 
                {
                	$requisition_no = $row[csf('booking_id')];
                	$close_prog_reqs_no = $close_prog_requisition_no[$requisition_no];

                	if($close_prog_reqs_no!=$requisition_no) // ommit close prog by requisition match
                	{
                		$receive_array[$party]['rcv_total_opening'] += $row[csf("rcv_total_opening")];
                		$receive_array[$party]['rcv_total_opening_amt'] += $row[csf("rcv_total_opening_amt")];
                	}               	
                }
                else // sample Booking 
                {
                	$receive_array[$party]['rcv_total_opening'] += $row[csf("rcv_total_opening")];
                	$receive_array[$party]['rcv_total_opening_amt'] += $row[csf("rcv_total_opening_amt")];
                }

                $receive_array[$party]['receive_qty'] += $row[csf("receive_qty")]; //  Date range
                $receive_array[$party]['receive_amt'] += $row[csf("receive_amt")]; //  Date range
                
                if($row[csf("receive_basis")]==3)
                {
                	$waiverRequisitionNo = $waiver_requisition_no[$requisition_no];

                	if($waiverRequisitionNo==$requisition_no)
                	{
                		$receive_array[$party]['close_prog_receive_qty'] += $row[csf("rcv_picup_waiver_qty")];
                	}
                }  

            }
            unset($result_sql_receive);
            //echo "<pre>";
            //print_r($receive_array);// die();

            $party_wise_knitting_factory_data = array();
            $knitingyarn_rowspan=0;

            asort($yarn_party_arr);           
            foreach ($yarn_party_arr as $party_id=>$value) 
            {  
                $knit_opening_qty   = $issue_array[$party_id]['issue_opening_qty'] - ($receive_array[$party_id]['rcv_total_opening']+$production_array[$party_id]['production_qty_opening']);

               // echo "$party_id=>".$issue_array[$party_id]['issue_opening_qty']."-". $receive_array[$party_id]['rcv_total_opening']."+".$production_array[$party_id]['production_qty_opening']."<br>";

                $knit_rcv_qty   =  ($receive_array[$party_id]['receive_qty']+$production_array[$party_id]['production_qty']);

                //echo $party_id."==".$receive_array[$party_id]['receive_qty']."+".$production_array[$party_id]['production_qty']."<br>";

                $knit_issue_qty   =  $issue_array[$party_id]['issue_qty'];
                
                $knit_waiver_qty = ($issue_array[$party_id]['close_prog_issue_qty']-($receive_array[$party_id]['close_prog_receive_qty']+$production_array[$party_id]['close_prog_production_qty']));// date range 

                //echo $party_id."==>".$issue_array[$party_id]['close_prog_issue_qty']."-".$receive_array[$party_id]['close_prog_receive_qty']."+".$production_array[$party_id]['close_prog_production_qty']."<br>";

                //echo $party_id."==>".$issue_array[$party_id]['close_prog_issue_qty']."-".$receive_array[$party_id]['close_prog_receive_qty']."+".$production_array[$party_id]['close_prog_production_qty']."<br>";

                $knit_closing_qty = (($knit_opening_qty+$knit_issue_qty)-($knit_rcv_qty+$knit_waiver_qty));
                $knit_closing_val   = 0;
                
                if($knit_opening_qty>0 || $knit_rcv_qty>0 || $knit_issue_qty>0 || $knit_closing_qty>0)
                {
                    $knitingyarn_rowspan++;
                    $kniting_party_array[1][$party_id]=$party_id;
                    $party_wise_knitting_factory_data[$party_id]['knit_opening_qty'] = $knit_opening_qty;
                    $party_wise_knitting_factory_data[$party_id]['knit_rcv_qty'] = $knit_rcv_qty;
                    $party_wise_knitting_factory_data[$party_id]['knit_issue_qty'] = $knit_issue_qty;
                    $party_wise_knitting_factory_data[$party_id]['knit_waiver_qty'] = $knit_waiver_qty;
                    $party_wise_knitting_factory_data[$party_id]['knit_closing_qty'] = $knit_closing_qty;
                }
            } 

            $total_knit_opening_qty   = 0;
            $total_knit_rcv_qty       = 0;
            $total_knit_issue_qty     = 0;
            $total_knit_closing_qty   = 0;
            $total_knit_closing_val   = 0;

            //echo $knitingyarn_rowspan."==".count($kniting_party_array[1]);
            $r=1;

            foreach ($kniting_party_array as $loc_key => $loc_value) 
            {
                foreach ($loc_value as $party_id => $row) 
                {  
                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;  

                    if($issue_array[$party_id]['issue_source']==1)
                    {
                    	$partyName = $lib_company[$party_id];
                    }else{
                    	$partyName = $lib_supplier[$party_id];
                    }
                    
                    $yarn_knit_opening_qty  = $party_wise_knitting_factory_data[$party_id]['knit_opening_qty'];
                    $yarn_knit_rcv_qty      = $party_wise_knitting_factory_data[$party_id]['knit_rcv_qty'];
                    $yarn_knit_issue_qty    = $party_wise_knitting_factory_data[$party_id]['knit_issue_qty'];
                    $yarn_knit_closing_qty  = $party_wise_knitting_factory_data[$party_id]['knit_closing_qty'];
                    $yarn_knit_waiver_qty   = $party_wise_knitting_factory_data[$party_id]['knit_waiver_qty'];
                    $yarn_knit_closing_val  =0;
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">
                        <? if($r==1){?>
                        <td width="150" valign="middle" rowspan="<? echo $knitingyarn_rowspan+1;?>" align="left"><? echo $location_type_array[$loc_key];;?></td>
                        <?}?>
                        <td width="150" align="left" title="<? echo $party_id;?>">
                            <a href="javascript:void()" onclick="open_party_popup('<? echo $company."_".$from_date."_".$to_date."_".$exchange_rate."_".$party_id;?>','party_wise_yarn_details_popup')">
                                <? echo $partyName;//isset($lib_supplier[$party_id]) ? $lib_supplier[$party_id] : $lib_company[$party_id];?>
                            </a>                           
                        </td>
                        <td width="120" align="right"><? echo number_format($yarn_knit_opening_qty,0);?></td>
                        <td width="120" align="right"><? echo number_format($yarn_knit_rcv_qty,0);?></td>
                        <td width="120" align="right"><? echo number_format($yarn_knit_issue_qty,0);?></td>
                        <td width="120" align="right"><? echo number_format($yarn_knit_closing_qty,0);?></td>
                        <td width="120" align="right"><? echo number_format($knit_closing_val,0);?></td>
                        <td width="" align="right"><? echo number_format($yarn_knit_waiver_qty,0);?></td>
                    </tr>
                    <?
                    $r++;
                    $i++;

                    $total_knit_opening_qty   += $yarn_knit_opening_qty;
                    $total_knit_rcv_qty       += $yarn_knit_rcv_qty;
                    $total_knit_issue_qty     += $yarn_knit_issue_qty;
                    $total_knit_closing_qty   += $yarn_knit_closing_qty;
                    $total_knit_closing_val   =0;
                    $total_knit_waiver_qty    += $yarn_knit_waiver_qty;
                }
            }
                    
            ?>
            <tr bgcolor="#C1C1C1" style="font-weight:bold;text-align:right;">
                <td>Total</td>
                <td><? echo number_format($total_knit_opening_qty,0);?></td>
                <td><? echo number_format($total_knit_rcv_qty,0);?></td>
                <td><? echo number_format($total_knit_issue_qty,0);?></td>
                <td><? echo number_format($total_knit_closing_qty,0);?></td>
                <td><? echo number_format($total_knit_closing_val,0);?></td>
                <td><? echo number_format($total_knit_waiver_qty,0);?></td>
            </tr>

            <!-- =========================== dyeing part start ======================== -->
            <tr bgcolor="#999999" style="font-weight:bold;text-align:center;">
                <td width="150"></td>
                <td width="150">Party Name</td>
                <td width="120">Opening Qty</td>
                <td width="120">Receive Qty </td>
                <td width="120">Delivery Qty</td>
                <td width="120">Closing Qty</td>
                <td width="120">Closing Value</td>
                <td width="">Waiver qty</td>
            </tr>
            <?
			$r=1;
			$total_dyeing_opening_qty   = 0;
			$total_dyeing_rcv_qty     = 0;
			$total_dyeing_issue_qty   = 0;
			$total_dyeing_closing_qty   = 0;
			$total_dyeing_closing_val   = 0;
			foreach ($dyeing_party_array as $loc_key => $loc_value) 
			{
				foreach ($loc_value as $party_id => $row) 
				{    
					$dyeing_opn_rcv_qty   = $dyeing_opn_rcv_array[$party_id]['qty'];
					$dyeing_opn_rcv_val   = $dyeing_opn_rcv_array[$party_id]['value'];
					
					$yarn_opn_issue_qty = $kniting_opn_issue_array[$party_id]['qty'];
					$yarn_opn_issue_val = $kniting_opn_issue_array[$party_id]['value'];
					
					// $trnsfr_opn_in_qty   = $dyeing_opn_trnsfr_array[$party_id][5]['qty'];
					// $trnsfr_opn_in_val   = $dyeing_opn_trnsfr_array[$party_id][5]['value'];
					
					// $trnsfr_out_opn_qty = $dyeing_opn_trnsfr_array[$party_id][6]['qty'];
					// $trnsfr_out_opn_val = $dyeing_opn_trnsfr_array[$party_id][6]['value'];
					//====================================================================
					$dyeing_rcv_qty   = $dyeing_rcv_array[$party_id]['qty'];
					$dyeing_rcv_val   = $dyeing_rcv_array[$party_id]['value'];
					
					$dyeing_issue_qty = $dyeing_issue_array[$party_id]['qty'];
					$dyeing_issue_val = $dyeing_issue_array[$party_id]['value'];
					
					$dyeing_opening_qty   = bcsub($yarn_opn_issue_qty,$dyeing_opn_rcv_qty,15);
					$tot_dyeing_rcv_qty   = bcadd($dyeing_rcv_qty,$trnsfr_in_qty,15);
					$tot_dyeing_issue_qty   = bcadd($dyeing_issue_qty,$trnsfr_out_qty,15);
					$dyeing_closing_qty   = bcsub(bcadd($dyeing_opening_qty,$tot_dyeing_issue_qty,15),$tot_dyeing_rcv_qty,15);
					$dyeing_closing_val   = bcsub($yarn_opn_issue_val,$dyeing_opn_rcv_val,15);
					if($dyeing_opening_qty>=1 || $tot_dyeing_rcv_qty>=1 || $tot_dyeing_issue_qty>=1 || $dyeing_closing_qty>=1)
					{
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";         
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">
						<? if($r==1){?>
                            <td width="150" valign="middle" rowspan="<? echo $dyeing_rowspan+1;?>" align="left"><? echo $location_type_array[$loc_key];;?></td>
                            <?}?>
                            <td width="150" align="left" title="<? echo $party_id;?>"><? echo $lib_supplier[$party_id];;?></td>
                            <td width="120" align="right"><? echo number_format($dyeing_opening_qty,0);?></td>
                            <td width="120" align="right"><? echo number_format($tot_dyeing_rcv_qty,0);?></td>
                            <td width="120" align="right"><? echo number_format($tot_dyeing_issue_qty,0);?></td>
                            <td width="120" align="right"><? echo number_format($dyeing_closing_qty,0);?></td>
                            <td width="120" align="right"><? echo number_format($dyeing_closing_val,0);?></td>
                            <td width="" align="right">&nbsp;</td>
						</tr>
						<?
						$r++;
						$i++;
						$total_dyeing_opening_qty   = bcadd($total_dyeing_opening_qty,$dyeing_opening_qty,15);
						$total_dyeing_rcv_qty     = bcadd($total_dyeing_rcv_qty,$tot_dyeing_rcv_qty,15);
						$total_dyeing_issue_qty   = bcadd($total_dyeing_issue_qty,$tot_dyeing_issue_qty,15);
						$total_dyeing_closing_qty   = bcadd($total_dyeing_closing_qty,$dyeing_closing_qty,15);
						$total_dyeing_closing_val   = bcadd($total_dyeing_closing_val,$dyeing_closing_val,15);
					}
				}
			}
            ?>
            <tr bgcolor="#C1C1C1" style="font-weight:bold;text-align:right;">
              <td>Total</td>
              <td><? echo number_format($total_dyeing_opening_qty,0);?></td>
              <td><? echo number_format($total_dyeing_rcv_qty,0);?></td>
              <td><? echo number_format($total_dyeing_issue_qty,0);?></td>
              <td><? echo number_format($total_dyeing_closing_qty,0);?></td>
              <td><? echo number_format($total_dyeing_closing_val,0);?></td> 
              <td>&nbsp;</td> 
            </tr>
            <!-- =========================== twisting part start ======================== -->
            <tr bgcolor="#999999" style="font-weight:bold;text-align:center;">
                <td width="150"></td>
                <td width="150">Party Name</td>
                <td width="120">Opening Qty</td>
                <td width="120">Receive Qty </td>
                <td width="120">Delivery Qty</td>
                <td width="120">Closing Qty</td>
                <td width="120">Closing Value</td>
                <td width="">Waiver qty</td>
            </tr>
			<?
            $r=1;
            $total_twisting_opening_qty   = 0;
            $total_twisting_rcv_qty     = 0;
            $total_twisting_issue_qty     = 0;
            $total_twisting_closing_qty   = 0;
            $total_twisting_closing_val   = 0;
            foreach ($twisting_party_array as $loc_key => $loc_value) 
            {
				foreach ($loc_value as $party_id => $row) 
				{    
					$twisting_opn_rcv_qty   = $twisting_opn_rcv_array[$party_id]['qty'];
					$twisting_opn_rcv_val   = $twisting_opn_rcv_array[$party_id]['value'];
					
					$yarn_opn_issue_qty = $kniting_opn_issue_array[$party_id]['qty'];
					$yarn_opn_issue_val = $kniting_opn_issue_array[$party_id]['value'];
					
					// $trnsfr_opn_in_qty   = $twisting_opn_trnsfr_array[$party_id][5]['qty'];
					// $trnsfr_opn_in_val   = $twisting_opn_trnsfr_array[$party_id][5]['value'];
					
					// $trnsfr_out_opn_qty = $twisting_opn_trnsfr_array[$party_id][6]['qty'];
					// $trnsfr_out_opn_val = $twisting_opn_trnsfr_array[$party_id][6]['value'];
					//====================================================================
					$twisting_rcv_qty   = $twisting_rcv_array['qty'];
					$twisting_rcv_val   = $twisting_rcv_array['value'];
					
					$twisting_issue_qty = $twisting_issue_array[$party_id]['qty'];
					$twisting_issue_val = $twisting_issue_array[$party_id]['value'];
					
					
					$twisting_opening_qty   = bcsub($yarn_opn_issue_qty,$twisting_opn_rcv_qty,15);
					$tot_twisting_rcv_qty   = bcadd($twisting_rcv_qty,$trnsfr_in_qty,15);
					$tot_twisting_issue_qty = bcadd($twisting_issue_qty,$trnsfr_out_qty,15);
					$twisting_closing_qty   = bcsub(bcadd($twisting_opening_qty,$tot_twisting_issue_qty,15),$tot_twisting_rcv_qty,15);
					$twisting_closing_val   = bcsub(bcadd($yarn_opn_issue_val,$trnsfr_out_qty,15),bcadd($twisting_opn_rcv_val,$trnsfr_in_val,15),15);($yarn_opn_issue_val + $trnsfr_out_qty) - ($twisting_opn_rcv_val + $trnsfr_in_val);
					if($twisting_opening_qty>=1 || $tot_twisting_rcv_qty>=1 || $tot_twisting_issue_qty>=1 || $twisting_closing_qty>=1)
					{
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;        
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">
						<? if($r==1){?>
						<td width="150" valign="middle" rowspan="<? echo $twisting_rowspan+1;?>" align="left"><? echo $location_type_array[$loc_key];;?></td>
						<?}?>
						<td width="150" align="left" title="<? echo $party_id;?>"><? echo $lib_supplier[$party_id];;?></td>
						<td width="120" align="right"><? echo number_format($twisting_opening_qty,0);?></td>
						<td width="120" align="right"><? echo number_format($tot_twisting_rcv_qty,0);?></td>
						<td width="120" align="right"><? echo number_format($tot_twisting_issue_qty,0);?></td>
						<td width="120" align="right"><? echo number_format($twisting_closing_qty,0);?></td>
						<td width="120" align="right"><? echo number_format($twisting_closing_val,0);?></td>
                        <td width="" align="right">&nbsp;</td>
						</tr>
						<?
						$r++;
						$i++;
						
						$total_twisting_opening_qty   = bcadd($total_twisting_opening_qty,$twisting_opening_qty,15);
						$total_twisting_rcv_qty     = bcadd($total_twisting_rcv_qty,$tot_twisting_rcv_qty,15);
						$total_twisting_issue_qty   = bcadd($total_twisting_issue_qty,$tot_twisting_issue_qty,15);
						$total_twisting_closing_qty   = bcadd($total_twisting_closing_qty,$twisting_closing_qty,15);
						$total_twisting_closing_val   = bcadd($total_twisting_closing_val,$twisting_closing_val,15);
						
						//$total_twisting_opening_qty   += $twisting_opening_qty;
						//$total_twisting_rcv_qty     += $tot_twisting_rcv_qty;
						//$total_twisting_issue_qty     += $tot_twisting_issue_qty;
						//$total_twisting_closing_qty   += $twisting_closing_qty;
						//$total_twisting_closing_val   += $twisting_closing_val;
					}
				}
            }
            ?>
            <tr bgcolor="#C1C1C1" style="font-weight:bold;text-align:right;">
              <? if($twisting_rowspan==0) echo '<td width="150"></td>'; ?>
              <td>Total</td>
              <td><? echo number_format($total_twisting_opening_qty,0);?></td>
              <td><? echo number_format($total_twisting_rcv_qty,0);?></td>
              <td><? echo number_format($total_twisting_issue_qty,0);?></td>
              <td><? echo number_format($total_twisting_closing_qty,0);?></td>
              <td><? echo number_format($total_twisting_closing_val,0);?></td>
              <td>&nbsp;</td>
            </tr>
            <!-- =========================== re_waxing part start ======================== -->
            <tr bgcolor="#999999" style="font-weight:bold;text-align:center;">
                <td width="150"></td>
                <td width="150">Party Name</td>
                <td width="120">Opening Qty</td>
                <td width="120">Receive Qty </td>
                <td width="120">Delivery Qty</td>
                <td width="120">Closing Qty</td>
                <td width="120">Closing Value</td>
                <td width="">Waiver qty</td>
            </tr>
			<?
            $r=1;
            $total_re_waxing_opening_qty  = 0;
            $total_re_waxing_rcv_qty    = 0;
            $total_re_waxing_issue_qty    = 0;
            $total_re_waxing_closing_qty  = 0;
            $total_re_waxing_closing_val  = 0;
            foreach ($re_waxing_party_array as $loc_key => $loc_value) 
            {
				foreach ($loc_value as $party_id => $row) 
				{    
					$re_waxing_opn_rcv_qty  = $re_waxing_opn_rcv_array[$party_id]['qty'];
					$re_waxing_opn_rcv_val  = $re_waxing_opn_rcv_array[$party_id]['value'];
					
					$yarn_opn_issue_qty = $kniting_opn_issue_array[$party_id]['qty'];
					$yarn_opn_issue_val = $kniting_opn_issue_array[$party_id]['value'];
					
					// $trnsfr_opn_in_qty   = $re_waxing_opn_trnsfr_array[$party_id][5]['qty'];
					// $trnsfr_opn_in_val   = $re_waxing_opn_trnsfr_array[$party_id][5]['value'];
					
					// $trnsfr_out_opn_qty = $re_waxing_opn_trnsfr_array[$party_id][6]['qty'];
					// $trnsfr_out_opn_val = $re_waxing_opn_trnsfr_array[$party_id][6]['value'];
					//====================================================================
					$re_waxing_rcv_qty  = $re_waxing_rcv_array['qty'];
					$re_waxing_rcv_val  = $re_waxing_rcv_array['value'];
					
					$re_waxing_issue_qty = $re_waxing_issue_array[$party_id]['qty'];
					$re_waxing_issue_val = $re_waxing_issue_array[$party_id]['value'];
					
					$re_waxing_opening_qty   = bcsub($yarn_opn_issue_qty,$re_waxing_opn_rcv_qty,15);
					$tot_re_waxing_rcv_qty   = bcadd($re_waxing_rcv_qty,$trnsfr_in_qty,15);
					$tot_re_waxing_issue_qty = bcadd($re_waxing_issue_qty,$trnsfr_out_qty,15);
					$re_waxing_closing_qty   = bcsub(bcadd($re_waxing_opening_qty,$tot_re_waxing_issue_qty,15),$tot_re_waxing_rcv_qty,15);
					$re_waxing_closing_val   = bcsub(bcadd($yarn_opn_issue_val,$trnsfr_out_qty,15),bcadd($re_waxing_opn_rcv_val,$trnsfr_in_val,15),15);
					
					//$re_waxing_opening_qty  = $yarn_opn_issue_qty - $re_waxing_opn_rcv_qty;
					//$tot_re_waxing_rcv_qty  = $re_waxing_rcv_qty + $trnsfr_in_qty;
					//$tot_re_waxing_issue_qty = $re_waxing_issue_qty + $trnsfr_out_qty;
					//$re_waxing_closing_qty  = ($re_waxing_opening_qty + $tot_re_waxing_issue_qty) - $tot_re_waxing_rcv_qty;
					//$re_waxing_closing_val  = ($yarn_opn_issue_val + $trnsfr_out_qty) - ($re_waxing_opn_rcv_val + $trnsfr_in_val);
					
					if($re_waxing_opening_qty>=1 || $tot_re_waxing_rcv_qty>=1 || $tot_re_waxing_issue_qty>=1 || $re_waxing_closing_qty>=1)
					{
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;        
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">
						<? if($r==1){?>
                            <td width="150" valign="middle" rowspan="<? echo $re_waxing_rowspan+1;?>" align="left"><? echo $location_type_array[$loc_key];;?></td>
                            <?}?>
                            <td width="150" align="left" title="<? echo $party_id;?>"><? echo $lib_supplier[$party_id];?></td>
                            <td width="120" align="right"><? echo number_format($re_waxing_opening_qty,0);?></td>
                            <td width="120" align="right"><? echo number_format($tot_re_waxing_rcv_qty,0);?></td>
                            <td width="120" align="right"><? echo number_format($tot_re_waxing_issue_qty,0);?></td>
                            <td width="120" align="right"><? echo number_format($re_waxing_closing_qty,0);?></td>
                            <td width="120" align="right"><? echo number_format($re_waxing_closing_val,0);?></td>
                            <td width="" align="right">&nbsp;</td>
						</tr>
						<?
						$r++;
						$i++;
						
						$total_re_waxing_opening_qty   = bcadd($total_re_waxing_opening_qty,$re_waxing_opening_qty,15);
						$total_re_waxing_rcv_qty     = bcadd($total_re_waxing_rcv_qty,$tot_re_waxing_rcv_qty,15);
						$total_re_waxing_issue_qty   = bcadd($total_re_waxing_issue_qty,$tot_re_waxing_issue_qty,15);
						$total_re_waxing_closing_qty   = bcadd($total_re_waxing_closing_qty,$re_waxing_closing_qty,15);
						$total_re_waxing_closing_val   = bcadd($total_re_waxing_closing_val,$re_waxing_closing_val,15);
						
						//$total_re_waxing_opening_qty  += $re_waxing_opening_qty;
						//$total_re_waxing_rcv_qty    += $tot_re_waxing_rcv_qty;
						//$total_re_waxing_issue_qty    += $tot_re_waxing_issue_qty;
						//$total_re_waxing_closing_qty  += $re_waxing_closing_qty;
						//$total_re_waxing_closing_val  += $re_waxing_closing_val;
					}
				}
            }
            ?>
            <tr bgcolor="#C1C1C1" style="font-weight:bold;text-align:right;">
              <? if($re_waxing_rowspan==0) echo '<td width="150"></td>'; ?>
              <td>Total</td>
              <td><? echo number_format($total_re_waxing_opening_qty,0);?></td>
              <td><? echo number_format($total_re_waxing_rcv_qty,0);?></td>
              <td><? echo number_format($total_re_waxing_issue_qty,0);?></td>
              <td><? echo number_format($total_re_waxing_closing_qty,0);?></td>
              <td><? echo number_format($total_re_waxing_closing_val,0);?></td>
              <td>&nbsp;</td>
            </tr>
            <!-- =========================== reconning part start ======================== -->
            <tr bgcolor="#999999" style="font-weight:bold;text-align:center;">
                <td width="150"></td>
                <td width="150">Party Name</td>
                <td width="120">Opening Qty</td>
                <td width="120">Receive Qty </td>
                <td width="120">Delivery Qty</td>
                <td width="120">Closing Qty</td>
                <td width="120">Closing Value</td>
                <td width="">Waiver qty</td>
            </tr>
			<?
            $r=1;
            $total_reconning_opening_qty  = 0;
            $total_reconning_rcv_qty    = 0;
            $total_reconning_issue_qty    = 0;
            $total_reconning_closing_qty  = 0;
            $total_reconning_closing_val  = 0;
            foreach ($reconning_party_array as $loc_key => $loc_value) 
            {
				foreach ($loc_value as $party_id => $row) 
				{    
					$reconning_opn_rcv_qty  = $reconning_opn_rcv_array[$party_id]['qty'];
					$reconning_opn_rcv_val  = $reconning_opn_rcv_array[$party_id]['value'];
					
					$yarn_opn_issue_qty = $kniting_opn_issue_array[$party_id]['qty'];
					$yarn_opn_issue_val = $kniting_opn_issue_array[$party_id]['value'];
					
					// $trnsfr_opn_in_qty   = $reconning_opn_trnsfr_array[$party_id][5]['qty'];
					// $trnsfr_opn_in_val   = $reconning_opn_trnsfr_array[$party_id][5]['value'];
					
					// $trnsfr_out_opn_qty = $reconning_opn_trnsfr_array[$party_id][6]['qty'];
					// $trnsfr_out_opn_val = $reconning_opn_trnsfr_array[$party_id][6]['value'];
					//====================================================================
					$reconning_rcv_qty  = $reconning_rcv_array['qty'];
					$reconning_rcv_val  = $reconning_rcv_array['value'];
					
					$reconning_issue_qty = $reconning_issue_array[$party_id]['qty'];
					$reconning_issue_val = $reconning_issue_array[$party_id]['value'];
					
					$reconning_opening_qty   = bcsub($yarn_opn_issue_qty,$reconning_opn_rcv_qty,15);
					$tot_reconning_rcv_qty   = bcadd($reconning_rcv_qty,$trnsfr_in_qty,15);
					$tot_reconning_issue_qty = bcadd($reconning_issue_qty,$trnsfr_out_qty,15);
					$reconning_closing_qty   = bcsub(bcadd($reconning_opening_qty,$tot_reconning_issue_qty,15),$tot_reconning_rcv_qty,15);
					$reconning_closing_val   = bcsub(bcadd($yarn_opn_issue_val,$trnsfr_out_qty,15),bcadd($reconning_opn_rcv_val,$trnsfr_in_val,15),15);
					
					//$reconning_opening_qty  = $yarn_opn_issue_qty - $reconning_opn_rcv_qty;
					//$tot_reconning_rcv_qty  = $reconning_rcv_qty + $trnsfr_in_qty;
					//$tot_reconning_issue_qty = $reconning_issue_qty + $trnsfr_out_qty;
					//$reconning_closing_qty  = ($reconning_opening_qty + $tot_reconning_issue_qty) - $tot_reconning_rcv_qty;
					//$reconning_closing_val  = ($yarn_opn_issue_val + $trnsfr_out_qty) - ($reconning_opn_rcv_val + $trnsfr_in_val);
					if($reconning_opening_qty>=1 || $tot_reconning_rcv_qty>=1 || $tot_reconning_issue_qty>=1 || $reconning_closing_qty>=1)
					{
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;        
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">
						<? if($r==1){?>
                            <td width="150" valign="middle" rowspan="<? echo $reconning_rowspan+1;?>" align="left"><? echo $location_type_array[$loc_key];;?></td>
                            <?}?>
                            <td width="150" align="left" title="<? echo $party_id;?>"><? echo $lib_supplier[$party_id];;?></td>
                            <td width="120" align="right"><? echo number_format($reconning_opening_qty,0);?></td>
                            <td width="120" align="right"><? echo number_format($tot_reconning_rcv_qty,0);?></td>
                            <td width="120" align="right"><? echo number_format($tot_reconning_issue_qty,0);?></td>
                            <td width="120" align="right"><? echo number_format($reconning_closing_qty,0);?></td>
                            <td width="120" align="right"><? echo number_format($reconning_closing_val,0);?></td>
                            <td width="" align="right">&nbsp;</td>
						</tr>
						<?
						$r++;
						$i++;
						
						$total_reconning_opening_qty   = bcadd($total_reconning_opening_qty,$reconning_opening_qty,15);
						$total_reconning_rcv_qty     = bcadd($total_reconning_rcv_qty,$tot_reconning_rcv_qty,15);
						$total_reconning_issue_qty   = bcadd($total_reconning_issue_qty,$tot_reconning_issue_qty,15);
						$total_reconning_closing_qty   = bcadd($total_reconning_closing_qty,$reconning_closing_qty,15);
						$total_reconning_closing_val   = bcadd($total_reconning_closing_val,$reconning_closing_val,15);
						
						//$total_reconning_opening_qty  += $reconning_opening_qty;
						//$total_reconning_rcv_qty    += $tot_reconning_rcv_qty;
						//$total_reconning_issue_qty    += $tot_reconning_issue_qty;
						//$total_reconning_closing_qty  += $reconning_closing_qty;
						//$total_reconning_closing_val  += $reconning_closing_val;
					}
				}
            }
            //========================== grand total sum ===============================
            $grnd_opening_qty   = bcadd(bcadd(bcadd(bcadd(bcadd(bcadd($grnd_opening_qty,$total_reconning_opening_qty),$total_re_waxing_opening_qty,15),$total_twisting_opening_qty,15),$total_dyeing_opening_qty,15),$total_knit_opening_qty,15),$total_yarn_opening_qty,15);
			$grnd_rcv_qty   = bcadd(bcadd(bcadd(bcadd(bcadd(bcadd($grnd_rcv_qty,$total_reconning_rcv_qty),$total_re_waxing_rcv_qty,15),$total_twisting_rcv_qty,15),$total_dyeing_rcv_qty,15),$total_knit_rcv_qty,15),$total_yarn_rcv_qty,15);
			$grnd_issue_qty   = bcadd(bcadd(bcadd(bcadd(bcadd(bcadd($grnd_issue_qty,$total_reconning_issue_qty),$total_re_waxing_issue_qty,15),$total_twisting_issue_qty,15),$total_dyeing_issue_qty,15),$total_knit_issue_qty,15),$total_yarn_issue_qty,15);
			$grnd_closing_qty   = bcadd(bcadd(bcadd(bcadd(bcadd(bcadd($grnd_closing_qty,$total_reconning_closing_qty),$total_re_waxing_closing_qty,15),$total_twisting_closing_qty,15),$total_dyeing_closing_qty,15),$total_knit_closing_qty,15),$total_yarn_closing_qty,15);
			$grnd_closing_val   = bcadd(bcadd(bcadd(bcadd(bcadd(bcadd($grnd_closing_val,$total_reconning_closing_val),$total_re_waxing_closing_val,15),$total_twisting_closing_val,15),$total_dyeing_closing_val,15),$total_knit_closing_val,15),$total_yarn_closing_val,15);
			
            //$grnd_rcv_qty     += $total_reconning_rcv_qty + $total_re_waxing_rcv_qty + $total_twisting_rcv_qty + $total_dyeing_rcv_qty + $total_knit_rcv_qty + $total_yarn_rcv_qty;
            //$grnd_issue_qty   += $total_reconning_issue_qty + $total_re_waxing_issue_qty + $total_twisting_issue_qty + $total_dyeing_issue_qty + $total_knit_issue_qty + $total_yarn_issue_qty;
            //$grnd_closing_qty   += $total_reconning_closing_qty + $total_re_waxing_closing_qty + $total_twisting_closing_qty + $total_dyeing_closing_qty + $total_knit_closing_qty + $total_yarn_closing_qty;
            //$grnd_closing_val   += $total_reconning_closing_val + $total_re_waxing_closing_val + $total_twisting_closing_val + $total_dyeing_closing_val + $total_knit_closing_val + $total_yarn_closing_val;
            ?>
            <tr bgcolor="#C1C1C1" style="font-weight:bold;text-align:right;">
            	<? if($reconning_rowspan==0) echo '<td width="150"></td>'; ?>
                <td>Total</td>
                <td><? echo number_format($total_reconning_opening_qty,0);?></td>
                <td><? echo number_format($total_reconning_rcv_qty,0);?></td>
                <td><? echo number_format($total_reconning_issue_qty,0);?></td>
                <td><? echo number_format($total_reconning_closing_qty,0);?></td>
                <td><? echo number_format($total_reconning_closing_val,0);?></td>
                <td>&nbsp;</td>
            </tr>
            </tbody>
        </table>
    </div>
    <table  border="1" rules="all" class="rpt_table"  width="<? echo 1020;?>"  cellpadding="0" cellspacing="0">
      <tfoot>
        <tr bgcolor="#C4C4C4" style="font-weight:bold;text-align:right;">
          <td width="150">&nbsp;</td>
          <td width="150">Grand Total</td>
          <td width="120"><? echo number_format($grnd_opening_qty,0);?></td>
          <td width="120"><? echo number_format($grnd_rcv_qty,0);?></td>
          <td width="120"><? echo number_format($grnd_issue_qty,0);?></td>
          <td width="120"><? echo number_format($grnd_closing_qty,0);?></td>
          <td width="120"><? echo number_format($grnd_closing_val,0);?></td>
          <td width="">&nbsp;</td>
        </tr>
      </tfoot>
    </table>
  </div>    
    <script type="text/javascript">

        function open_store_popup(data,action)
        {
          	width = 870; 
            emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'closing_stock_report_controller.php?data=' + data + '&action=' + action, 'Location Wise Details', 'width='+width+'px,height=300px,center=1,resize=0,scrolling=0', '../../../');
        }

        function open_party_popup(data,action)
        {
            width = 1040; 
            emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'closing_stock_report_controller.php?data=' + data + '&action=' + action, 'Knitting Factory Wise Details', 'width='+width+'px,height=350px,center=1,resize=0,scrolling=0', '../../../');
        }
    </script>

  <?
}

if($action=="open_store_wise_details_popup")
{
    echo load_html_head_contents("Yarn Details Info", "../../../../", 1, 1,'','','');

    $lib_store   = return_library_array("select id,store_name from lib_store_location", "id", "store_name");
    $count_array = return_library_array("select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1", "id", "yarn_count");
    $lib_buyer = return_library_array("select id,buyer_name from  lib_buyer where is_deleted=0 and status_active=1", "id", "buyer_name");
    $lib_color = return_library_array("select id,color_name from  lib_color where is_deleted=0 and status_active=1", "id", "color_name");
    
    $data_ex  = explode("_", $data);
    $company  = $data_ex[0];
    $from_date  = $data_ex[1];
    $to_date  = $data_ex[2];
    $ex_rate  = $data_ex[3];
    $store_id   = $data_ex[4];
    if($store_id<1) {echo "No Store Location Found"; die;}
    //==========================================================================================/
    //                 Grey yarn receive                     /
    //==========================================================================================
    //and b.yarn_count_id !=0 and b.yarn_comp_type1st !=0 
    /*$sql_receive = "SELECT a.ORDER_RATE, b.id as prod_id, b.YARN_COUNT_ID, c.CURRENCY_ID, b.YARN_COMP_TYPE1ST, b.AVAILABLE_QNTY, a.TRANSACTION_DATE, c.RECEIVE_PURPOSE, a.CONS_QUANTITY, a.CONS_RATE, a.CONS_AMOUNT
    from inv_transaction a, inv_receive_master c, product_details_master b 
    where a.mst_id=c.id and b.id=a.prod_id and a.transaction_type in (1,4) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.company_id=$company and b.item_category_id=1 and a.store_id=$store_id and b.DYED_TYPE <> 1";
    //echo $sql_receive;
    $recv_res = sql_select($sql_receive);
    $yarn_opn_rcv_array = array();
    $yarn_rcv_array = array();
    $grey_yarn_data_array = array();
    foreach ($recv_res as $row) 
    {
        $comp = "";
        $comp = strtolower(trim($composition[$row['YARN_COMP_TYPE1ST']]));
        $trans_date = $row["TRANSACTION_DATE"];
        $grey_yarn_data_array[$comp][$count_array[$row['YARN_COUNT_ID']]]['qty'] =bcadd($grey_yarn_data_array[$comp][$count_array[$row['YARN_COUNT_ID']]]['qty'],$row['CONS_QUANTITY'],15); 
        if(strtotime($trans_date) < strtotime($from_date))
        {
            $yarn_opn_rcv_array[$comp][$count_array[$row['YARN_COUNT_ID']]]['qty'] =bcadd($yarn_opn_rcv_array[$comp][$count_array[$row['YARN_COUNT_ID']]]['qty'],$row['CONS_QUANTITY'],15); 
            $yarn_opn_rcv_array[$comp][$count_array[$row['YARN_COUNT_ID']]]['value'] =bcadd($yarn_opn_rcv_array[$comp][$count_array[$row['YARN_COUNT_ID']]]['value'],$row['CONS_AMOUNT'],15); 
        }
        // echo $trans_date .">=". $from_date ."==".$trans_date ."<=". $to_date."<br>";
        if(strtotime($trans_date) >= strtotime($from_date) && strtotime($trans_date) <= strtotime($to_date))
        {
            $yarn_rcv_array[$comp][$count_array[$row['YARN_COUNT_ID']]]['qty'] =bcadd($yarn_rcv_array[$comp][$count_array[$row['YARN_COUNT_ID']]]['qty'],$row['CONS_QUANTITY'],15); 
            $yarn_rcv_array[$comp][$count_array[$row['YARN_COUNT_ID']]]['value']=bcadd($yarn_rcv_array[$comp][$count_array[$row['YARN_COUNT_ID']]]['value'],$row['CONS_AMOUNT'],15); 
        } 
    }
    unset($recv_res);
    //echo "<pre>";print_r($yarn_opn_rcv_array);echo "</pre>";
    //==========================================================================================/
    //                 Grey yarn issue                     /
    //==========================================================================================
    $sql_issue = "SELECT b.id as prod_id, b.YARN_COUNT_ID, b.YARN_COMP_TYPE1ST, b.AVAILABLE_QNTY, a.TRANSACTION_DATE, c.ISSUE_PURPOSE, a.CONS_QUANTITY, a.CONS_RATE, a.CONS_AMOUNT
    from inv_transaction a, inv_issue_master c, product_details_master b 
    where a.mst_id=c.id and b.id=a.prod_id and a.transaction_type in (2,3) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.company_id=$company and b.item_category_id=1 and a.store_id=$store_id and b.DYED_TYPE<>1";
    //echo $sql_issue;
    $issue_res = sql_select($sql_issue);
    $yarn_opn_issue_array = array();
    $yarn_issue_array = array();
    $grey_yarn_issue_array = array();
    foreach ($issue_res as $row) 
    {
        $grey_yarn_issue_array[$comp][$count_array[$row['YARN_COUNT_ID']]]['qty'] =bcadd($grey_yarn_issue_array[$comp][$count_array[$row['YARN_COUNT_ID']]]['qty'],$row['CONS_QUANTITY'],15); 
        $comp = "";
        $comp = strtolower(trim($composition[$row['YARN_COMP_TYPE1ST']]));
        $trans_date = $row["TRANSACTION_DATE"];
        if(strtotime($trans_date) < strtotime($from_date))
        {
            $yarn_opn_issue_array[$comp][$count_array[$row['YARN_COUNT_ID']]]['qty'] =bcadd($yarn_opn_issue_array[$comp][$count_array[$row['YARN_COUNT_ID']]]['qty'],$row['CONS_QUANTITY'],15); 
            $yarn_opn_issue_array[$comp][$count_array[$row['YARN_COUNT_ID']]]['value'] =bcadd($yarn_opn_issue_array[$comp][$count_array[$row['YARN_COUNT_ID']]]['value'],$row['CONS_AMOUNT'],15); 
        }
        
        if(strtotime($trans_date) >= strtotime($from_date) && strtotime($trans_date) <= strtotime($to_date))
        {
            $yarn_issue_array[$comp][$count_array[$row['YARN_COUNT_ID']]]['qty'] =bcadd($yarn_issue_array[$comp][$count_array[$row['YARN_COUNT_ID']]]['qty'],$row['CONS_QUANTITY'],15);  
            $yarn_issue_array[$comp][$count_array[$row['YARN_COUNT_ID']]]['value'] =bcadd($yarn_issue_array[$comp][$count_array[$row['YARN_COUNT_ID']]]['value'],$row['CONS_AMOUNT'],15);           
        } 
    }
    unset($issue_res);

    //==========================================================================================/
    //                   Yarn Transfer                   /
    //==========================================================================================
    $sql_transfer = "SELECT b.id as prod_id, b.YARN_COUNT_ID, b.YARN_COMP_TYPE1ST, b.AVAILABLE_QNTY, a.CONS_RATE, a.CONS_QUANTITY, a.TRANSACTION_DATE, a.TRANSACTION_TYPE, a.STORE_ID, a.CONS_AMOUNT 
    from inv_transaction a, inv_item_transfer_mst c, product_details_master b 
    where a.mst_id=c.id and a.prod_id=b.id and a.transaction_type in (5,6) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.is_deleted=0 and a.company_id=$company and a.store_id=$store_id and b.DYED_TYPE<>1 and a.STORE_ID <> 0"; // and transaction_date between '$from_date' and '$to_date'
    // echo $sql_transfer;
    $transfer_res = sql_select($sql_transfer);    
    
    $yarn_opn_trnsfr_array = array();
    $yarn_trnsfr_array = array();
    foreach ($transfer_res as $row) 
    {
        $comp = "";
        $comp = strtolower(trim($composition[$row['YARN_COMP_TYPE1ST']]));
        $trans_date = $row["TRANSACTION_DATE"];
        if(strtotime($trans_date) < strtotime($from_date))
        {
          $yarn_opn_trnsfr_array[$row["TRANSACTION_TYPE"]][$comp][$count_array[$row['YARN_COUNT_ID']]]['qty'] =bcadd($yarn_opn_trnsfr_array[$row["TRANSACTION_TYPE"]][$comp][$count_array[$row['YARN_COUNT_ID']]]['qty'],$row['CONS_QUANTITY'],15); 
          $yarn_opn_trnsfr_array[$row["TRANSACTION_TYPE"]][$comp][$count_array[$row['YARN_COUNT_ID']]]['value'] =bcadd($yarn_opn_trnsfr_array[$row["TRANSACTION_TYPE"]][$comp][$count_array[$row['YARN_COUNT_ID']]]['value'],$row['CONS_AMOUNT'],15); 
        }
        if(strtotime($trans_date) >= strtotime($from_date) && strtotime($trans_date) <= strtotime($to_date))
        {
          $yarn_trnsfr_array[$row["TRANSACTION_TYPE"]][$comp][$count_array[$row['YARN_COUNT_ID']]]['qty'] =bcadd($yarn_trnsfr_array[$row["TRANSACTION_TYPE"]][$comp][$count_array[$row['YARN_COUNT_ID']]]['qty'],$row['CONS_QUANTITY'],15); 
          $yarn_trnsfr_array[$row["TRANSACTION_TYPE"]][$comp][$count_array[$row['YARN_COUNT_ID']]]['value'] =bcadd($yarn_trnsfr_array[$row["TRANSACTION_TYPE"]][$comp][$count_array[$row['YARN_COUNT_ID']]]['value'],$row['CONS_AMOUNT'],15);            
        } 
        $grey_yarn_data_array[$comp][$count_array[$row['YARN_COUNT_ID']]]['extra'] =bcadd($grey_yarn_data_array[$comp][$count_array[$row['YARN_COUNT_ID']]]['extra'],$row['CONS_QUANTITY'],15);
    }*/
    
    //echo "<pre>";print_r($grey_yarn_data_array);die;
    
    $sql_gery_yarn = "SELECT b.ID AS PROD_ID, a.ID as TRANS_ID, a.ITEM_CATEGORY, b.COLOR, b.YARN_COUNT_ID, b.YARN_COMP_TYPE1ST, a.STORE_ID, a.ORDER_RATE, a.CONS_RATE, a.CONS_QUANTITY, a.TRANSACTION_DATE, a.ITEM_CATEGORY, a.CONS_AMOUNT, a.TRANSACTION_TYPE, b.AVG_RATE_PER_UNIT
    from inv_transaction a, product_details_master b
    where a.prod_id=b.id and a.item_category in (1) and b.item_category_id in (1) and a.status_active=1 and a.is_deleted=0 and a.company_id=$company and a.store_id=$store_id and b.DYED_TYPE<>1
    order by TRANS_ID asc"; //and a.transaction_date between '$from_date' and '$to_date'
    //echo $sql_receive;die();
    $sql_gery_yarn_result = sql_select($sql_gery_yarn);  $data_array_grey=array();   
    foreach($sql_gery_yarn_result as $row)
    {
        $comp = strtolower(trim($composition[$row['YARN_COMP_TYPE1ST']]));
        $trans_date = $row["TRANSACTION_DATE"];
        if(strtotime($trans_date) < strtotime($from_date))
        {
            if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
            {
                $data_array_grey[$comp][$count_array[$row['YARN_COUNT_ID']]]['rcv_total_opening_amt'] =bcadd($data_array_grey[$comp][$count_array[$row['YARN_COUNT_ID']]]['rcv_total_opening_amt'],$row["CONS_AMOUNT"],15);
                $data_array_grey[$comp][$count_array[$row['YARN_COUNT_ID']]]['rcv_total_opening'] =bcadd($data_array_grey[$comp][$count_array[$row['YARN_COUNT_ID']]]['rcv_total_opening'],$row["CONS_QUANTITY"],15);
            }
            else
            {
                $data_array_grey[$comp][$count_array[$row['YARN_COUNT_ID']]]['iss_total_opening_amt'] =bcadd($data_array_grey[$comp][$count_array[$row['YARN_COUNT_ID']]]['iss_total_opening_amt'],$row["CONS_AMOUNT"],15);
                $data_array_grey[$comp][$count_array[$row['YARN_COUNT_ID']]]['iss_total_opening'] =bcadd($data_array_grey[$comp][$count_array[$row['YARN_COUNT_ID']]]['iss_total_opening'],$row["CONS_QUANTITY"],15);
            }
        }
        if(strtotime($trans_date) >= strtotime($from_date) && strtotime($trans_date) <= strtotime($to_date))
        {
            if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
            {
                $data_array_grey[$comp][$count_array[$row['YARN_COUNT_ID']]]['total_rcv_amt'] =bcadd($data_array_grey[$comp][$count_array[$row['YARN_COUNT_ID']]]['total_rcv_amt'],$row["CONS_AMOUNT"],15);
                $data_array_grey[$comp][$count_array[$row['YARN_COUNT_ID']]]['total_rcv_qnty'] =bcadd($data_array_grey[$comp][$count_array[$row['YARN_COUNT_ID']]]['total_rcv_qnty'],$row["CONS_QUANTITY"],15);
            }
            else
            {
                $data_array_grey[$comp][$count_array[$row['YARN_COUNT_ID']]]['total_issue_amt'] =bcadd($data_array_grey[$comp][$count_array[$row['YARN_COUNT_ID']]]['total_issue_amt'],$row["CONS_AMOUNT"],15);
                $data_array_grey[$comp][$count_array[$row['YARN_COUNT_ID']]]['total_issue_qnty'] =bcadd($data_array_grey[$comp][$count_array[$row['YARN_COUNT_ID']]]['total_issue_qnty'],$row["CONS_QUANTITY"],15);
            }
        }
    }
    unset($sql_gery_yarn_result);
    $comp_rowspan = array();
    ksort($data_array_grey);
    //echo "test";die;
    $r=1;
    foreach ($data_array_grey as $com_key => $comp_data) 
    {
         
        if($com_key=='')$com_key=0;
        ksort($comp_data);
        //if( $r >1 )
        //{
            //echo "<pre>";print_r($comp_data);
            //ksort($comp_data);
            //echo "<pre>";print_r($comp_data);die;
        //}
        
        foreach ($comp_data as $count => $row) 
        {
            $yarn_opening_qty = bcsub($row["rcv_total_opening"],$row["iss_total_opening"],15);
            $yarn_rcv_qty = $row["total_rcv_qnty"];
            $yarn_issue_qty = $row["total_issue_qnty"];
            $yarn_closing_qty = bcsub(bcadd($yarn_opening_qty,$yarn_rcv_qty,15),$yarn_issue_qty,15);
            
            $yarn_opening_val = bcsub($row["rcv_total_opening_amt"],$row["iss_total_opening_amt"],15);
            $yarn_rcv_val = $row["total_rcv_amt"];
            $yarn_issue_val = $row["total_issue_amt"];
            $yarn_closing_val = bcsub(bcadd($yarn_opening_val,$yarn_rcv_val,15),$yarn_issue_val,15);
            
            $avg_rate = ($yarn_closing_qty !=0) ? bcdiv($yarn_closing_val,$yarn_closing_qty,15) : 0;
                
            if(number_format($yarn_opening_qty,2)!=0 || number_format($yarn_rcv_qty,2)!=0 || number_format($yarn_issue_qty,2)!=0 || number_format($yarn_closing_qty,2)!=0)
            {
                $comp_rowspan[$com_key]++;
            }
        }
        $r++;
    }
  //echo "<pre>"; print_r($comp_rowspan);die;
  ?>
  <div>
    <div>
      <div>
        <center><h2>Location Wise Details</h2></center>
        <table border="1" rules="all" class="rpt_table">
          <tr style="font-weight:bold; font-size:14px;">
            <td style="font-weight:bold; font-size:14px;" width="123" align="left">Item Name : </td><td style="font-weight:bold; font-size:14px;" width="123" align="left">Yarn</td>
            <td style="font-weight:bold; font-size:14px;" width="123" align="left">Location Type : </td><td style="font-weight:bold; font-size:14px;" width="123" align="left">Yarn Store</td>
            <td style="font-weight:bold; font-size:14px;" width="123" align="left">Location Name : </td><td style="font-weight:bold; font-size:14px;" width="123" align="left"><? echo $lib_store[$store_id]; ?></td>
          </tr>
          <tr style="font-weight:bold; font-size:14px;">
            <td style="font-weight:bold; font-size:14px;" width="123" align="left">Date : </td><td style="font-weight:bold; font-size:14px;" colspan="5" align="left"><? echo change_date_format($from_date);?> To <? echo change_date_format($to_date);?></td>
          </tr>
        </table>
      </div>
      <table border="1" rules="all" class="rpt_table" width="740" cellpadding="0" cellspacing="0">
        <caption><h3>Grey Yarn</h3></caption>
        <thead>
              <tr>
                  <th width="120">Yarn Composition</th>
                  <th width="50">Count</th>
                  <th width="100">Opening Qty</th>
                  <th width="100">Receive Qty </th>
                  <th width="100">Delivery Qty</th>
                  <th width="100">Closing Qty</th>
                  <th width="100">Closing Value</th>
                  <th >Avg. Rate</th>
              </tr>
            </thead>
        </table>
        <div style="width:760px; max-height:150px; overflow-y:auto" id="scroll_body">
          <table border="1" rules="all" class="rpt_table" width="740" cellpadding="0" cellspacing="0">
            <tbody>
              <?
              // echo "<pre>";print_r($grey_yarn_issue_array);
              $i=1;
              $gr_yarn_opn_qty  = 0;
              $gr_yarn_rcv_qty  = 0;
              $gr_yarn_issue_qty  = 0;
              $gr_yarn_close_qty  = 0;
              $gr_yarn_close_val  = 0;
              //echo "<pre>";print_r($data_array_grey);die;
              ksort($data_array_grey);
              foreach ($data_array_grey as $com_key => $comp_data) 
              {
                $r=1;
                if($com_key=='')$com_key=0;
                ksort($comp_data);
                foreach ($comp_data as $count => $row) 
                {
                    $yarn_opening_qty = bcsub($row["rcv_total_opening"],$row["iss_total_opening"],15);
                    $yarn_rcv_qty = $row["total_rcv_qnty"];
                    $yarn_issue_qty = $row["total_issue_qnty"];
                    $yarn_closing_qty = bcsub(bcadd($yarn_opening_qty,$yarn_rcv_qty,15),$yarn_issue_qty,15);
                    
                    $yarn_opening_val = bcsub($row["rcv_total_opening_amt"],$row["iss_total_opening_amt"],15);
                    $yarn_rcv_val = $row["total_rcv_amt"];
                    $yarn_issue_val = $row["total_issue_amt"];
                    $yarn_closing_val = bcsub(bcadd($yarn_opening_val,$yarn_rcv_val,15),$yarn_issue_val,15);
                    
                    $avg_rate = ($yarn_closing_qty !=0) ? bcdiv($yarn_closing_val,$yarn_closing_qty,15) : 0;

                    if(number_format($yarn_opening_qty,2)!=0 || number_format($yarn_rcv_qty,2)!=0 || number_format($yarn_issue_qty,2)!=0 || number_format($yarn_closing_qty,2)!=0)
                    {
                        ?>
                        <tr>
                            <? if($r==1){?>
                            <td valign="middle" width="120" rowspan="<? echo $comp_rowspan[$com_key];?>"><p><? echo $com_key;?></p></td>
                            <? }?>
                            <td width="50"><p><? echo $count;?></p></td>
                            <td align="right" width="100"><p><? echo number_format($yarn_opening_qty,2);?></p></td>
                            <td align="right" width="100"><p><? echo number_format($yarn_rcv_qty,2);?></p></td>
                            <td align="right" width="100"><p><? echo number_format($yarn_issue_qty,2);?></p></td>
                            <td align="right" width="100"><p><? echo number_format($yarn_closing_qty,2);?></p></td>
                            <td align="right" width="100" title="<? echo $yarn_closing_val_title;?>"><p><? echo number_format($yarn_closing_val,2);?></p></td>
                            <td align="right" ><p><? echo number_format($avg_rate,4);?></p></td>
                        </tr>
                        <?
                        $i++;
                        $r++;
                        $gr_yarn_opn_qty =bcadd($gr_yarn_opn_qty,$yarn_opening_qty,15);
                        $gr_yarn_rcv_qty =bcadd($gr_yarn_rcv_qty,$yarn_rcv_qty,15);
                        $gr_yarn_issue_qty =bcadd($gr_yarn_issue_qty,$yarn_issue_qty,15);
                        $gr_yarn_close_qty =bcadd($gr_yarn_close_qty,$yarn_closing_qty,15);
                        $gr_yarn_close_val =bcadd($gr_yarn_close_val,$yarn_closing_val,15);
                    }
                }
              }
              ?>
            </tbody>
          </table>
      </div>
      <table  border="1" rules="all" class="rpt_table"  width="<? echo 740;?>"  cellpadding="0" cellspacing="0">
        <tfoot>
          <tr bgcolor="#C4C4C4" style="font-weight:bold;text-align:right;">
            <td width="172" >Grand Total</td>
            <td width="100"><? echo number_format($gr_yarn_opn_qty,2);?></td>
            <td width="100"><? echo number_format($gr_yarn_rcv_qty,2);?></td>
            <td width="100"><? echo number_format($gr_yarn_issue_qty,2);?></td>
            <td width="100"><? echo number_format($gr_yarn_close_qty,2);?></td>
            <td width="100"><? echo number_format($gr_yarn_close_val,2);?></td>
            <td>&nbsp;</td>
          </tr>
        </tfoot>
      </table>
    </div>
    
    <!-- =============================== dyed yarn part =========================== -->
    <?
    $sql_receive = "SELECT b.ID AS PROD_ID, a.ID as TRANS_ID, a.ITEM_CATEGORY, b.COLOR, b.YARN_COUNT_ID, b.YARN_COMP_TYPE1ST, a.STORE_ID, a.ORDER_RATE, a.CONS_RATE, a.CONS_QUANTITY, a.TRANSACTION_DATE, a.ITEM_CATEGORY, a.CONS_AMOUNT, a.TRANSACTION_TYPE, b.AVG_RATE_PER_UNIT
    from inv_transaction a, product_details_master b
    where a.prod_id=b.id and a.item_category in (1) and b.item_category_id in (1) and a.status_active=1 and a.is_deleted=0 and a.company_id=$company and a.store_id=$store_id and b.DYED_TYPE in(1)
    order by TRANS_ID asc"; //and a.transaction_date between '$from_date' and '$to_date'
    //echo $sql_receive;die();
    $receive_res = sql_select($sql_receive);  $all_store_arr=array();   
    foreach($receive_res as $row)
    {
        $comp = strtolower(trim($composition[$row['YARN_COMP_TYPE1ST']]));
        $trans_date = $row["TRANSACTION_DATE"];
        if(strtotime($trans_date) < strtotime($from_date))
        {
            if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
            {
                $data_array[$comp][$count_array[$row['YARN_COUNT_ID']]][$row['COLOR']]['rcv_total_opening_amt'] =bcadd($data_array[$comp][$count_array[$row['YARN_COUNT_ID']]][$row['COLOR']]['rcv_total_opening_amt'],$row["CONS_AMOUNT"],15);
                $data_array[$comp][$count_array[$row['YARN_COUNT_ID']]][$row['COLOR']]['rcv_total_opening'] =bcadd($data_array[$comp][$count_array[$row['YARN_COUNT_ID']]][$row['COLOR']]['rcv_total_opening'],$row["CONS_QUANTITY"],15);
            }
            else
            {
                $data_array[$comp][$count_array[$row['YARN_COUNT_ID']]][$row['COLOR']]['iss_total_opening_amt'] =bcadd($data_array[$comp][$count_array[$row['YARN_COUNT_ID']]][$row['COLOR']]['iss_total_opening_amt'],$row["CONS_AMOUNT"],15);
                $data_array[$comp][$count_array[$row['YARN_COUNT_ID']]][$row['COLOR']]['iss_total_opening'] =bcadd($data_array[$comp][$count_array[$row['YARN_COUNT_ID']]][$row['COLOR']]['iss_total_opening'],$row["CONS_QUANTITY"],15);
            }
        }
        if(strtotime($trans_date) >= strtotime($from_date) && strtotime($trans_date) <= strtotime($to_date))
        {
            if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
            {
                $data_array[$comp][$count_array[$row['YARN_COUNT_ID']]][$row['COLOR']]['total_rcv_amt'] =bcadd($data_array[$comp][$count_array[$row['YARN_COUNT_ID']]][$row['COLOR']]['total_rcv_amt'],$row["CONS_AMOUNT"],15);
                $data_array[$comp][$count_array[$row['YARN_COUNT_ID']]][$row['COLOR']]['total_rcv_qnty'] =bcadd($data_array[$comp][$count_array[$row['YARN_COUNT_ID']]][$row['COLOR']]['total_rcv_qnty'],$row["CONS_QUANTITY"],15);
            }
            else
            {
                $data_array[$comp][$count_array[$row['YARN_COUNT_ID']]][$row['COLOR']]['total_issue_amt'] =bcadd($data_array[$comp][$count_array[$row['YARN_COUNT_ID']]][$row['COLOR']]['total_issue_amt'],$row["CONS_AMOUNT"],15);
                $data_array[$comp][$count_array[$row['YARN_COUNT_ID']]][$row['COLOR']]['total_issue_qnty'] =bcadd($data_array[$comp][$count_array[$row['YARN_COUNT_ID']]][$row['COLOR']]['total_issue_qnty'],$row["CONS_QUANTITY"],15);
            }
        }

        $data_array[$comp][$count_array[$row['YARN_COUNT_ID']]][$row['COLOR']]['comp_id']=$row['YARN_COMP_TYPE1ST'];
        $data_array[$comp][$count_array[$row['YARN_COUNT_ID']]][$row['COLOR']]['count_id']=$row['YARN_COUNT_ID'];
    }
    unset($receive_res);
    $yarn_opening_qty = $yarn_rcv_qty = $yarn_issue_qty = $yarn_closing_qty = $yarn_opening_val =  $yarn_rcv_val = $yarn_issue_val = $yarn_closing_val = 0;
    $dayed_comp_rowspan = array();

    foreach ($data_array as $composition_id => $composition_data) 
    {
        foreach ($composition_data as $count_id => $count_data) 
        {
            foreach ($count_data as $color_id => $row)
            {
                $yarn_opening_qty = bcsub($row["rcv_total_opening"],$row["iss_total_opening"],15);
                $yarn_rcv_qty = $row["total_rcv_qnty"];
                $yarn_issue_qty = $row["total_issue_qnty"];
                $yarn_closing_qty = bcsub(bcadd($yarn_opening_qty,$yarn_rcv_qty,15),$yarn_issue_qty,15);
                
                $yarn_opening_val = bcsub($row["rcv_total_opening_amt"],$row["iss_total_opening_amt"],15);
                $yarn_rcv_val = $row["total_rcv_amt"];
                $yarn_issue_val = $row["total_issue_amt"];
                $yarn_closing_val = bcsub(bcadd($yarn_opening_val,$yarn_rcv_val,15),$yarn_issue_val,15);
                
                $avg_rate = ($yarn_closing_qty !=0) ? bcdiv($yarn_closing_val,$yarn_closing_qty,15) : 0;
                
                if(number_format($yarn_opening_qty,2)!=0 || number_format($yarn_rcv_qty,2)!=0 || number_format($yarn_issue_qty,2)!=0 || number_format($yarn_closing_qty,2)!=0)
                {
                    $dayed_comp_rowspan[$composition_id]++;
                }
            }
        }
    }
    
    ?>
    <div style="margin-top: 30px;">
      <table border="1" rules="all" class="rpt_table" width="830" cellpadding="0" cellspacing="0">
        <caption><h3>Dyed Yarn</h3></caption>
        <thead>
              <tr>
                  <th width="100">Yarn Composition</th>
                  <th width="50">Count</th>
                  <th width="100">Color Name</th>
                  <th width="100">Opening Qty</th>
                  <th width="100">Receive Qty </th>
                  <th width="100">Delivery Qty</th>
                  <th width="100">Closing Qty</th>
                  <th width="100">Closing Value</th>
                  <th>Avg. Rate</th>
                </tr>
            </thead>
        </table>
        <div style="width:850px; max-height:150px; overflow-y:auto" id="scroll_body">
          <table border="1" rules="all" class="rpt_table" width="830" cellpadding="0" cellspacing="0">
            <tbody>
                <?
                // echo "<pre>";print_r($dyed_yarn_data_array);
                $i=1;
                $gr_yarn_opn_qty  = 0;
                $gr_yarn_rcv_qty  = 0;
                $gr_yarn_issue_qty  = 0;
                $gr_yarn_close_qty  = 0;
                $gr_yarn_close_val  = 0;
                
                ksort($data_array);
                $yarn_opening_qty = $yarn_rcv_qty = $yarn_issue_qty = $yarn_closing_qty = $yarn_opening_val =  $yarn_rcv_val = $yarn_issue_val = $yarn_closing_val = 0;
                foreach ($data_array as $composition_id => $composition_data) 
                {
                    $j=1;
                    if($composition_id=='')$composition_id=0;
                    ksort($composition_data);
                    foreach ($composition_data as $count_id => $count_data) 
                    {
                        foreach ($count_data as $color_id => $row)
                        {
                            $yarn_opening_qty = bcsub($row["rcv_total_opening"],$row["iss_total_opening"],15);
                            $yarn_rcv_qty = $row["total_rcv_qnty"];
                            $yarn_issue_qty = $row["total_issue_qnty"];
                            $yarn_closing_qty = bcsub(bcadd($yarn_opening_qty,$yarn_rcv_qty,15),$yarn_issue_qty,15);
                            
                            $yarn_opening_val = bcsub($row["rcv_total_opening_amt"],$row["iss_total_opening_amt"],15);
                            $yarn_rcv_val = $row["total_rcv_amt"];
                            $yarn_issue_val = $row["total_issue_amt"];
                            $yarn_closing_val = bcsub(bcadd($yarn_opening_val,$yarn_rcv_val,15),$yarn_issue_val,15);
                            
                            $avg_rate = ($yarn_closing_qty !=0) ? bcdiv($yarn_closing_val,$yarn_closing_qty,15) : 0; 

                            if(number_format($yarn_opening_qty,2)!=0 || number_format($yarn_rcv_qty,2)!=0 || number_format($yarn_issue_qty,2)!=0 || number_format($yarn_closing_qty,2)!=0)
                            {
                                ?>
                                <tr>
                                <? if($j==1){?>
                                <td valign="middle" width="100" rowspan="<? echo $dayed_comp_rowspan[$composition_id];?>" ><p><? echo $composition_id;?></p></td>
                                <? }?>
                                
                                <td width="50"><p><? echo $count_id;?></p></td>
                                <td width="100"><p><? echo $lib_color[$color_id];?></p></td>
                                <td align="right" width="100"><p><? echo number_format($yarn_opening_qty,2);?></p></td>
                                <td align="right" width="100"><p><? echo number_format($yarn_rcv_qty,2);?></p></td>
                                <td align="right" width="100"><p><? echo number_format($yarn_issue_qty,2);?></p></td>
                                <td align="right" width="100"><p>
                                    <a href="javascript:void()" onclick="open_job_popup('<? echo $company."_".$from_date."_".$to_date."_".$exchange_rate."_".$store_id."_".$row['comp_id']."_".$row['count_id']."_".$color_id;?>','open_job_wise_details_popup')"><? echo number_format($yarn_closing_qty,2);?></a>
                                    </p>
                                </td>
                                <td align="right" width="100"><p><? echo number_format($yarn_closing_val,2);?></p></td>
                                <td align="right"><p><? echo number_format($avg_rate,4);?></p></td>
                                </tr>
                                <?
                                $i++; $j++;
                                $gr_yarn_opn_qty  =bcadd($gr_yarn_opn_qty,$yarn_opening_qty,15);
                                $gr_yarn_rcv_qty  =bcadd($gr_yarn_rcv_qty,$yarn_rcv_qty,15);
                                $gr_yarn_issue_qty  =bcadd($gr_yarn_issue_qty,$yarn_issue_qty,15);
                                $gr_yarn_close_qty  =bcadd($gr_yarn_close_qty,$yarn_closing_qty,15);
                                $gr_yarn_close_val  =bcadd($gr_yarn_close_val,$yarn_closing_val,15);
                            }
                        }
                    }
                }
                ?>
            </tbody>
          </table>
      </div>
      <table  border="1" rules="all" class="rpt_table"  width="<? echo 830;?>"  cellpadding="0" cellspacing="0">
        <tfoot>
          <tr bgcolor="#C4C4C4" style="font-weight:bold;text-align:right;">
            <td width="253" >Grand Total</td>
            <td width="100"><? echo number_format($gr_yarn_opn_qty,2);?></td>
            <td width="100"><? echo number_format($gr_yarn_rcv_qty,2);?></td>
            <td width="100"><? echo number_format($gr_yarn_issue_qty,2);?></td>
            <td width="100"><? echo number_format($gr_yarn_close_qty,2);?></td>
            <td width="100"><? echo number_format($gr_yarn_close_val,2);?></td>
            <td >&nbsp;</td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
  <script type="text/javascript">
      function open_job_popup(data,action,type)
      {
          width=800;      
          emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'closing_stock_report_controller.php?data=' + data + '&action=' + action, 'Location Wise Details', 'width='+width+'px,height=250px,center=1,resize=0,scrolling=0', '../../../');
      }
  </script>
  <?
}

if($action=="party_wise_yarn_details_popup")
{
    echo load_html_head_contents("Yarn Details Info", "../../../../", 1, 1,'','','');

    $lib_color = return_library_array("select id,color_name from  lib_color where is_deleted=0 and status_active=1", "id", "color_name");
    $lib_company  = return_library_array("select id,company_name from lib_company", "id", "company_name");
    $lib_supplier   = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
    $count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
    
    $data_ex  = explode("_", $data);
    $company  = $data_ex[0];
    $from_date  = $data_ex[1];
    $to_date  = $data_ex[2];
    $ex_rate  = $data_ex[3];
    $party_id   = $data_ex[4];
    if($party_id<1) {echo "No Party Found"; die;}
    
    //=====
    $close_prog_sql="select a.inv_pur_req_mst_id,max(a.closing_date) as closing_date ,b.requisition_no from inv_reference_closing a,ppl_yarn_requisition_entry b where a.inv_pur_req_mst_id=b.knit_id and a.reference_type=2 and a.closing_status=1 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company  group by a.inv_pur_req_mst_id,b.requisition_no";//and a.closing_date<='" . $to_date . "'

    $close_prog_resutl = sql_select($close_prog_sql);

    $close_prog_data = array();
    $close_prog_requisition_no = array();
    foreach ($close_prog_resutl as $row) 
    {
      $prog_no = $row[csf("inv_pur_req_mst_id")];
      $requisition_no = $row[csf("requisition_no")];
      $closing_date = $row[csf("closing_date")];
      $close_prog_data[$prog_no]['prog_no']=$row[csf("inv_pur_req_mst_id")];
      $close_prog_data[$prog_no]['closing_date']=$row[csf("closing_date")];
      $close_prog_data[$prog_no]['production_mrr_no']=$row[csf("mrr_system_no")]; 

      $close_prog_requisition_no[$requisition_no]=$row[csf("requisition_no")];

    if( strtotime($closing_date) >= strtotime($from_date) && strtotime($closing_date) <= strtotime($to_date) )
    {
      $waiver_prog_no[$prog_no]= $prog_no;
      $waiver_requisition_no[$requisition_no]= $requisition_no;
    }
    }

    //echo "<pre>";
    //print_r($close_prog_data); die();
    //pi_wo_batch_no,

    $production_sql = "select c.knitting_company,c.booking_id,c.receive_basis,c.ref_closing_status,d.color, d.yarn_count_id, d.yarn_comp_type1st,d.dyed_type,

    sum(case when a.transaction_type =1 and c.entry_form=2 and a.transaction_date<'" . $from_date . "' then b.used_qty else 0 end) as production_qty_opening,
    sum(case when a.transaction_type =1 and c.entry_form=2 and a.transaction_date<'" . $from_date . "' then b.amount else 0 end) as production_opening_amt,          

    sum(case when a.transaction_type =1 and c.entry_form=2 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then b.used_qty else 0 end) as production_qty,
    sum(case when a.transaction_type =1 and c.entry_form=2 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then b.amount else 0 end) as production_amt,
    sum(case when a.transaction_type =1 and c.entry_form=2 and a.transaction_date<='" . $to_date . "' then b.used_qty else 0 end) as production_picup_waiver_qty

    from inv_transaction a,pro_material_used_dtls b,inv_receive_master c,product_details_master d where a.mst_id=c.id and b.mst_id=c.id and d.id=b.prod_id and b.item_category=1 and b.entry_form=2 and c.entry_form=2 and c.receive_basis in (1,2)  and a.status_active=1 and a.is_deleted=0  and c.status_active=1and c.is_deleted=0 and c.company_id=$company and c.knitting_company=$party_id group by c.knitting_company,c.booking_id,c.receive_basis,c.ref_closing_status,d.color, d.yarn_count_id, d.yarn_comp_type1st,d.dyed_type";


    //echo $production_sql; die();

    $result_production_sql = sql_select($production_sql);

    $production_array = array();
    foreach ($result_production_sql as $row)
    {
      $comp = $row[csf("yarn_comp_type1st")];
      $count = $row[csf("yarn_count_id")];
      $dyed_type = $row[csf("dyed_type")];
      $color = $row[csf("color")];

        if( $row[csf('receive_basis')] == 2) // plan production
        {
          $close_prog = $close_prog_data[$row[csf('booking_id')]]['prog_no'];

          if($close_prog!=$row[csf('booking_id')])
          {
            $production_array[$comp][$count][$dyed_type][$color]['production_qty_opening'] += $row[csf("production_qty_opening")]; 
            $production_array[$comp][$count][$dyed_type][$color]['production_opening_amt'] += $row[csf("production_opening_amt")]; 
          }                 
        }
        else // sample booking production
        {
          $production_array[$comp][$count][$dyed_type][$color]['production_qty_opening'] += $row[csf("production_qty_opening")]; 
          $production_array[$comp][$count][$dyed_type][$color]['production_opening_amt'] += $row[csf("production_opening_amt")]; 
        }
        
        $production_array[$comp][$count][$dyed_type][$color]['production_qty'] += $row[csf("production_qty")]; // date range between
        $production_array[$comp][$count][$dyed_type][$color]['production_amt'] += $row[csf("production_amt")]; // date range between

        if( $row[csf('receive_basis')] == 2 ) // Close Prog 
        {
          //echo $party."=>".$row[csf('receive_basis')]."&&". $close_prog."==".$row[csf('booking_id')]."<br>"; 
          $waiverProgNo = $waiver_prog_no[$row[csf('booking_id')]];

          if($waiverProgNo==$row[csf('booking_id')])
          {
            $production_array[$comp][$count][$dyed_type][$color]['close_prog_production_qty'] += $row[csf("production_picup_waiver_qty")];
          }               
        } 
      
    }

    unset($result_production_sql);
    //echo "<pre>";
    //print_r($production_array); die();

    $sql_issue = "select a.requisition_no,c.issue_basis,c.knit_dye_source,c.knit_dye_company,d.color, d.yarn_count_id, d.yarn_comp_type1st,d.dyed_type,
    sum(case when a.transaction_type =2 and c.entry_form=3 and a.transaction_date<'" . $from_date . "' then a.cons_quantity else 0 end) as issue_opening_qty,
    sum(case when a.transaction_type =2 and c.entry_form=3 and a.transaction_date<'" . $from_date . "' then a.cons_amount else 0 end) as issue_opening_amt,          
    sum(case when a.transaction_type=2 and c.entry_form=3 and a.transaction_date  between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as issue_qty,
    sum(case when a.transaction_type=2 and c.entry_form=3 and a.transaction_date  between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as issue_amt,
    sum(case when a.transaction_type =2 and c.entry_form=3 and a.transaction_date<='" . $to_date . "' then a.cons_quantity else 0 end) as issue_pickup_waiver_qty                    
    from inv_transaction a, inv_issue_master c, product_details_master d
    where a.mst_id=c.id and d.id=a.prod_id and a.item_category=1 and a.transaction_type in (2) and c.issue_basis in (1,3) and c.issue_purpose in (1,8) and c.company_id=$company and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.company_id=$company and c.knit_dye_company=$party_id group by a.requisition_no,c.issue_basis,c.knit_dye_source,c.knit_dye_company,d.color, d.yarn_count_id, d.yarn_comp_type1st,d.dyed_type";

    //echo $sql_issue; die();
    $result_sql_issue = sql_select($sql_issue);

    $issue_array = array();
    
    foreach ($result_sql_issue as $row)
    {   
      $comp = $row[csf("yarn_comp_type1st")];
        $count = $row[csf("yarn_count_id")];
        $dyed_type = $row[csf("dyed_type")];
        $color = $row[csf("color")];

        $comp_count_arr[$comp][$count][$dyed_type][$color] = $count;

        $issue_array[$comp][$count][$dyed_type][$color]['issue_source']=$row[csf("knit_dye_source")];

        if($row[csf("issue_basis")]==3) // plan/requisition
        {
          $close_prog_reqs_no = $close_prog_requisition_no[$row[csf("requisition_no")]];

          if($close_prog_reqs_no!=$row[csf("requisition_no")]) // ommit close prog
            {
              $issue_array[$comp][$count][$dyed_type][$color]['issue_opening_qty'] += $row[csf("issue_opening_qty")];
              $issue_array[$comp][$count][$dyed_type][$color]['issue_opening_amt'] += $row[csf("issue_opening_amt")];
            } 
        }
        else // sample booking 
        {
          $issue_array[$comp][$count][$dyed_type][$color]['issue_opening_qty'] += $row[csf("issue_opening_qty")];
          $issue_array[$comp][$count][$dyed_type][$color]['issue_opening_amt'] += $row[csf("issue_opening_amt")];
        }           
        
        $issue_array[$comp][$count][$dyed_type][$color]['issue_qty'] += $row[csf("issue_qty")]; // Date Range
        $issue_array[$comp][$count][$dyed_type][$color]['issue_amt'] += $row[csf("issue_amt")]; // Date Range             

        if( $row[csf("issue_basis")]==3) // Plan/requsition
        {         
          $waiverRequisitionNo = $waiver_requisition_no[$row[csf('requisition_no')]];
          //echo  $waiverRequisitionNo ."==". $row[csf('requisition_no')]."<br>";
          if( $waiverRequisitionNo == $row[csf('requisition_no')] )
          {
            //echo $party."=>".$waiverRequisitionNo."==".$row[csf('requisition_no')]."<br>";
            $issue_array[$comp][$count][$dyed_type][$color]['close_prog_issue_qty'] += $row[csf("issue_pickup_waiver_qty")];
          }                    
        }   
    }
    unset($result_sql_issue);

    /*
    echo "<pre>";
    print_r($issue_array);
    */
    
    $sql_receive = "Select c.knitting_company,c.booking_id,c.receive_basis,d.color, d.yarn_count_id, d.yarn_comp_type1st,d.dyed_type,
    sum(case when a.transaction_type =4 and c.entry_form=9 and a.transaction_date<'" . $from_date . "' then (a.cons_quantity+a.cons_reject_qnty) else 0 end) as rcv_total_opening,
    sum(case when a.transaction_type =4 and c.entry_form=9 and a.transaction_date<'" . $from_date . "' then a.cons_amount else 0 end) as rcv_total_opening_amt,
    sum(case when a.transaction_type =4 and c.entry_form=9 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then (a.cons_quantity+a.cons_reject_qnty) else 0 end) as receive_qty,
    sum(case when a.transaction_type =4 and c.entry_form=9 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as receive_amt,
    sum(case when a.transaction_type =4 and c.entry_form=9 and a.transaction_date<='" . $to_date . "' then (a.cons_quantity+a.cons_reject_qnty) else 0 end) as rcv_picup_waiver_qty       
    from inv_transaction a, inv_receive_master c, product_details_master d where a.mst_id=c.id and d.id=a.prod_id and a.transaction_type in (4) and a.item_category in (1) and c.receive_basis in (1,3) and c.company_id=$company and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.knitting_company=$party_id
    group by c.knitting_company,c.booking_id,c.receive_basis,d.color, d.yarn_count_id, d.yarn_comp_type1st,d.dyed_type";

    //echo $sql_receive;
    //,tmp_issue_id d --and c.issue_id= d.issue_id 
    $result_sql_receive = sql_select($sql_receive);
    $receive_array = array();
    foreach ($result_sql_receive as $row)
    {    
      $comp = $row[csf("yarn_comp_type1st")];
      $count = $row[csf("yarn_count_id")];
      $dyed_type = $row[csf("dyed_type")];
      $color = $row[csf("color")];

        if($row[csf("receive_basis")]==3) // plan/requsition basis 
        {
          $requisition_no = $row[csf('booking_id')];
          $close_prog_reqs_no = $close_prog_requisition_no[$requisition_no];

          if($close_prog_reqs_no!=$requisition_no) // ommit close prog by requisition match
          {
            $receive_array[$comp][$count][$dyed_type][$color]['rcv_total_opening'] += $row[csf("rcv_total_opening")];
            $receive_array[$comp][$count][$dyed_type][$color]['rcv_total_opening_amt'] += $row[csf("rcv_total_opening_amt")];
          }                 
        }
        else // sample Booking 
        {
          $receive_array[$comp][$count][$dyed_type][$color]['rcv_total_opening'] += $row[csf("rcv_total_opening")];
          $receive_array[$comp][$count][$dyed_type][$color]['rcv_total_opening_amt'] += $row[csf("rcv_total_opening_amt")];
        }

        $receive_array[$comp][$count][$dyed_type][$color]['receive_qty'] += $row[csf("receive_qty")]; //  Date range
        $receive_array[$comp][$count][$dyed_type][$color]['receive_amt'] += $row[csf("receive_amt")]; //  Date range

        if($row[csf("receive_basis")]==3)
        {
          $waiverRequisitionNo = $waiver_requisition_no[$requisition_no];
          if($waiverRequisitionNo==$requisition_no)
          {
            $receive_array[$comp][$count][$dyed_type][$color]['close_prog_receive_qty'] += $row[csf("rcv_picup_waiver_qty")];
          }
        } 

    }
    unset($result_sql_receive);
    //echo "<pre>";
    //print_r($receive_array); die();

    //===== 

    $grey_yarn_data = array();
    $dyed_yar_data = array();
    foreach ($comp_count_arr as $comp_id=>$compArr) 
    {  
        foreach ($compArr as $count_id => $dyedTypeArr) 
        {
            foreach ($dyedTypeArr as $dyed_type => $colorArr) 
            {    
                foreach ($colorArr as $color_id => $row) 
                {
                  $issue_source = $issue_array[$comp_id][$count_id][$dyed_type][$color_id]['issue_source'];

                  if($issue_source==1)
                  {
                      $partyName=$lib_company[$party_id];
                  }
                  else{
                    $partyName = $lib_supplier[$party_id];
                  }
                  
                    if($dyed_type==1) // dyed yarn 
                    {
                      
                        $dyed_knit_opening_qty = $issue_array[$comp_id][$count_id][$dyed_type][$color_id]['issue_opening_qty']- ( $receive_array[$comp_id][$count_id][$dyed_type][$color_id]['rcv_total_opening']+$production_array[$comp_id][$count_id][$dyed_type][$color_id]['production_qty_opening']);

                        $dyed_knit_rcv_qty   =  ($receive_array[$comp_id][$count_id][$dyed_type][$color_id]['receive_qty']+$production_array[$comp_id][$count_id][$dyed_type][$color_id]['production_qty']);

                        $dyed_knit_issue_qty   =  $issue_array[$comp_id][$count_id][$dyed_type][$color_id]['issue_qty'];
                        
                        $dyed_knit_waiver_qty = ($issue_array[$comp_id][$count_id][$dyed_type][$color_id]['close_prog_issue_qty']-($receive_array[$comp_id][$count_id][$dyed_type][$color_id]['close_prog_receive_qty']+$production_array[$comp_id][$count_id][$dyed_type][$color_id]['close_prog_production_qty']));// date range 

                        $dyed_knit_closing_qty = (($dyed_knit_opening_qty+$dyed_knit_issue_qty)-($dyed_knit_rcv_qty+$dyed_knit_waiver_qty));

                        //echo $dyed_type."==".$dyed_knit_opening_qty+$dyed_knit_issue_qty."-".$dyed_knit_rcv_qty+$dyed_knit_waiver_qty."<br>";
                        
                        $dyed_knit_closing_val   = 0;

                        if($dyed_knit_opening_qty>0 || $dyed_knit_rcv_qty>0 || $dyed_knit_issue_qty>0 || $dyed_knit_closing_qty>0)
                        {
                            $dyed_yarn_rowspan[$com_key]++;
                            $comPosition = $composition[strtolower(trim($comp_id))];
                            $dyed_yar_data[$comPosition][$count_arr[$count_id]][$color_id]['knit_opening_qty'] = $dyed_knit_opening_qty;
                            $dyed_yar_data[$comPosition][$count_arr[$count_id]][$color_id]['knit_rcv_qty'] =$dyed_knit_rcv_qty;
                            $dyed_yar_data[$comPosition][$count_arr[$count_id]][$color_id]['knit_issue_qty'] =$dyed_knit_issue_qty;
                            $dyed_yar_data[$comPosition][$count_arr[$count_id]][$color_id]['knit_closing_qty']=$dyed_knit_closing_qty;
                            $dyed_yar_data[$comPosition][$count_arr[$count_id]][$color_id]['knit_waiver_qty'] =$dyed_knit_waiver_qty;
                        }
                    }
                    else // grey yarn 
                    {
                        
                        $grey_knit_opening_qty   = $issue_array[$comp_id][$count_id][$dyed_type][$color_id]['issue_opening_qty']- ( $receive_array[$comp_id][$count_id][$dyed_type][$color_id]['rcv_total_opening']+$production_array[$comp_id][$count_id][$dyed_type][$color_id]['production_qty_opening']);

                        //echo "$dyed_type=>".$issue_array[$comp_id][$count_id][$dyed_type][$color_id]['issue_opening_qty']."- ( ".$receive_array[$comp_id][$count_id][$dyed_type][$color_id]['rcv_total_opening']."+".$production_array[$comp_id][$count_id][$dyed_type][$color_id]['production_qty_opening'].")<br>";

                        $grey_knit_rcv_qty   =  ($receive_array[$comp_id][$count_id][$dyed_type][$color_id]['receive_qty']+$production_array[$comp_id][$count_id][$dyed_type][$color_id]['production_qty']);

                        $grey_knit_issue_qty   =  $issue_array[$comp_id][$count_id][$dyed_type][$color_id]['issue_qty'];

                        $grey_knit_waiver_qty = ($issue_array[$comp_id][$count_id][$dyed_type][$color_id]['close_prog_issue_qty']-($receive_array[$comp_id][$count_id][$dyed_type][$color_id]['close_prog_receive_qty']+$production_array[$comp_id][$count_id][$dyed_type][$color_id]['close_prog_production_qty']));// date range 

                        $grey_knit_closing_qty = (($grey_knit_opening_qty+$grey_knit_issue_qty)-($grey_knit_rcv_qty+$grey_knit_waiver_qty));

                        $grey_knit_closing_val   = 0;

                        if($grey_knit_opening_qty>0 || $grey_knit_rcv_qty>0 || $grey_knit_issue_qty>0 || $grey_knit_closing_qty>0)
                        {
                            $grey_yarn_rowspan[$com_key]++;
                            $comPosition = $composition[strtolower(trim($comp_id))];
                            $grey_yar_data[$comPosition][$count_arr[$count_id]]['knit_opening_qty'] = $grey_knit_opening_qty;
                            $grey_yar_data[$comPosition][$count_arr[$count_id]]['knit_rcv_qty'] = $grey_knit_rcv_qty;
                            $grey_yar_data[$comPosition][$count_arr[$count_id]]['knit_issue_qty'] = $grey_knit_issue_qty;
                            $grey_yar_data[$comPosition][$count_arr[$count_id]]['knit_closing_qty'] = $grey_knit_closing_qty;
                            $grey_yar_data[$comPosition][$count_arr[$count_id]]['knit_waiver_qty'] = $grey_knit_waiver_qty;
                        }
                    }         
                }          
            }
        }             
    }    
    ?>
    <div>

        <!-- Grey yarn part-->
        <div>
            <div>
                <center><h2>Knitting Factory Wise Details</h2></center>
                <table border="1" rules="all" class="rpt_table">
                    <tr style="font-weight:bold; font-size:14px;">
                    <td style="font-weight:bold; font-size:14px;" width="123" align="left">Item Name : </td><td style="font-weight:bold; font-size:14px;" width="123" align="left">Yarn</td>
                    <td style="font-weight:bold; font-size:14px;" width="123" align="left">Location Type : </td>
                    <td style="font-weight:bold; font-size:14px;" width="123" align="left">Knitting</td>
                    <td style="font-weight:bold; font-size:14px;" width="123" align="left">Location Name : </td><td style="font-weight:bold; font-size:14px;" width="123" align="left"><? echo $partyName;?></td>
                    </tr>
                    <tr style="font-weight:bold; font-size:14px;">
                    <td style="font-weight:bold; font-size:14px;" width="123" align="left">Date : </td><td style="font-weight:bold; font-size:14px;" colspan="5" align="left"><? echo change_date_format($from_date);?> To <? echo change_date_format($to_date);?></td>
                    </tr>
                </table>
            </div>
            <table border="1" rules="all" class="rpt_table" width="1010" cellpadding="0" cellspacing="0">
            <caption><h3>Grey Yarn</h3></caption>
                <thead>
                    <tr>
                        <th width="150">Yarn Composition</th>
                        <th width="50">Count</th>
                        <th width="110">Opening Qty</th>
                        <th width="110">Receive Qty </th>
                        <th width="110">Delivery Qty</th>
                        <th width="110">Closing Qty</th>
                        <th width="110">Closing Value</th>
                        <th width="110">Avg. Rate</th>
                        <th width="">Waiver qty</th>
                    </tr>
                </thead>
            </table>
            <div style="width:1010px; max-height:150px; overflow-y:auto" id="scroll_body">
                <table border="1" rules="all" class="rpt_table" width="990" cellpadding="0" cellspacing="0">
                    <tbody>
                    <?
                    // echo "<pre>";print_r($grey_yarn_issue_array);
                    $i=1;
                    $r=1;
                    $gr_yarn_opn_qty  = 0;
                    $gr_yarn_rcv_qty  = 0;
                    $gr_yarn_issue_qty  = 0;
                    $gr_yarn_close_qty  = 0;
                    $gr_yarn_close_val  = 0;
                    //echo "<pre>";print_r($data_array_grey);die;
                    ksort($grey_yar_data);
                    foreach ($grey_yar_data as $compositionName => $comp_data) 
                    {
                      ksort($comp_data);
                        $r=1;
                        //if($comp=='')$comp=0;
                        foreach ($comp_data as $count => $row) 
                        {   
                            $avg_rate = 0;
                            $yarn_closing_val=0;

                            if(number_format($row["knit_opening_qty"],2)!=0 || number_format($row["knit_rcv_qty"],2)!=0 || number_format($row["knit_issue_qty"],2)!=0 || number_format($row["knit_closing_qty"],2)!=0)
                            {
                              //echo $comp_rowspan[$com_key];
                                ?>
                                <tr>  
                                    <td valign="middle" width="150" rowspan=""><p><? echo $compositionName;?></p></td>
                                    <td width="50" ><p><? echo $count;?></p></td>
                                    <td align="right" width="110"><p><? echo number_format($row["knit_opening_qty"],2);?></p></td>
                                    <td align="right" width="110"><p><? echo number_format($row["knit_rcv_qty"],2);?></p></td>
                                    <td align="right" width="110"><p><? echo number_format($row["knit_issue_qty"],2);?></p></td>
                                    <td align="right" width="110"><p><? echo number_format($row["knit_closing_qty"],2);?></p></td>
                                    <td align="right" width="110" title="<? echo $yarn_closing_val_title;?>"><p><? echo number_format($yarn_closing_val,2);?></p></td>
                                    <td align="right" width="110"><p><? echo number_format($avg_rate,4);?></p></td>
                                    <td align="right" width=""><p><? echo number_format($row["knit_waiver_qty"],2);?></p></td>
                                </tr>
                                <?
                                $i++;
                                $r++;
                                $gr_yarn_opn_qty += $row["knit_opening_qty"];
                                $gr_yarn_rcv_qty +=$row["knit_rcv_qty"];
                                $gr_yarn_issue_qty +=$row["knit_issue_qty"];
                                $gr_yarn_close_qty +=$row["knit_closing_qty"];
                                $gr_yarn_close_val =0;
                                $gr_yarn_waiver_qty +=$row["knit_waiver_qty"];
                            }
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <table  border="1" rules="all" class="rpt_table"  width="<? echo 1010;?>"  cellpadding="0" cellspacing="0">
                <tfoot>
                  <tr bgcolor="#C4C4C4" style="font-weight:bold;text-align:right;">
                        <td width="200">Grand Total</td>
                        <td width="110"><? echo number_format($gr_yarn_opn_qty,2);?></td>
                        <td width="110"><? echo number_format($gr_yarn_rcv_qty,2);?></td>
                        <td width="110"><? echo number_format($gr_yarn_issue_qty,2);?></td>
                        <td width="110"><? echo number_format($gr_yarn_close_qty,2);?></td>
                        <td width="110"><? echo number_format($gr_yarn_close_val,2);?></td>
                        <td width="110">&nbsp;</td>
                        <td width=""><? echo number_format($gr_yarn_waiver_qty,2);?></td>
                  </tr>
                </tfoot>
            </table>
        </div> 
        
        <!-- Dyed yarn part-->
        <div style="margin-top: 30px;">
            <table border="1" rules="all" class="rpt_table" width="1010" cellpadding="0" cellspacing="0">
                <caption><h3>Dyed Yarn</h3></caption>
                <thead>
                  <tr>
                      <th width="150">Yarn Composition</th>
                      <th width="50">Count</th>
                      <th width="110">Color Name</th>
                      <th width="110">Opening Qty</th>
                      <th width="110">Receive Qty </th>
                      <th width="110">Delivery Qty</th>
                      <th width="110">Closing Qty</th>
                      <th width="110">Closing Value</th>
                      <th width="80">Avg. Rate</th>
                      <th>Waiver qty</th>
                    </tr>
                </thead>
            </table>
            <div style="width:1010px; max-height:150px; overflow-y:auto" id="scroll_body">
                <table border="1" rules="all" class="rpt_table" width="990" cellpadding="0" cellspacing="0">
                <tbody>
                    <?
                    // echo "<pre>";print_r($dyed_yarn_data_array);
                    $i=1;
                    $gr_yarn_opn_qty  = 0;
                    $gr_yarn_rcv_qty  = 0;
                    $gr_yarn_issue_qty  = 0;
                    $gr_yarn_close_qty  = 0;
                    $gr_yarn_close_val  = 0;

                    $yarn_opening_qty = $yarn_rcv_qty = $yarn_issue_qty = $yarn_closing_qty = $yarn_opening_val =  $yarn_rcv_val = $yarn_issue_val = $yarn_closing_val = 0;

                    ksort($dyed_yar_data);
                    foreach ($dyed_yar_data as $compositionName => $composition_data) 
                    {
                        $j=1;
                        //if($compositionName=='')$compositionName=0;
                        ksort($composition_data);
                        foreach ($composition_data as $count => $count_data) 
                        {
                          ksort($count_data);
                            foreach ($count_data as $color_id => $row)
                            {
                                $yarn_opening_qty = $row["knit_opening_qty"];
                                $yarn_rcv_qty = $row["knit_rcv_qty"];
                                $yarn_issue_qty = $row["knit_issue_qty"];
                                $yarn_closing_qty = $row["knit_closing_qty"];
                                $yarn_waiver_qty = $row["knit_waiver_qty"];
                            
                                $yarn_closing_val = 0;                               
                                $avg_rate = 0; 
                                
                                if(number_format($yarn_opening_qty,2)!=0 || number_format($yarn_rcv_qty,2)!=0 || number_format($yarn_issue_qty,2)!=0 || number_format($yarn_closing_qty,2)!=0)
                                {
                                  //echo $dayed_comp_rowspan[$composition_id];
                                    ?>
                                    <tr>
                                      <td valign="middle" width="150" rowspan="" ><p><? echo $compositionName;?></p></td>
                                      <td width="50"><p><? echo $count;?></p></td>
                                      <td width="110"><p><? echo $lib_color[$color_id];?></p></td>
                                      <td align="right" width="110"><p><? echo number_format($yarn_opening_qty,2);?></p></td>
                                      <td align="right" width="110"><p><? echo number_format($yarn_rcv_qty,2);?></p></td>
                                      <td align="right" width="110"><p><? echo number_format($yarn_issue_qty,2);?></p></td>
                                      <td align="right" width="110"><p><? echo number_format($yarn_closing_qty,2);?></p></td>
                                      <td align="right" width="110"><p><? echo number_format($yarn_closing_val,2);?></p></td>
                                      <td align="right" width="80"><p><? echo number_format($avg_rate,4);?></p></td>
                                      <td align="right" width=""><p><? echo number_format($yarn_waiver_qty,2);?></p></td>
                                    </tr>
                                    <?
                                    $gr_yarn_opn_qty  +=$yarn_opening_qty;
                                    $gr_yarn_rcv_qty  +=$yarn_rcv_qty;
                                    $gr_yarn_issue_qty  +=$yarn_issue_qty;
                                    $gr_yarn_close_qty  +=$yarn_closing_qty;
                                    $gr_yarn_close_val  +=$yarn_closing_val;
                                    $i++; $j++;
                                }
                            }
                        }
                    }
                    ?>
                </tbody>
                </table>
            </div>
            <table  border="1" rules="all" class="rpt_table"  width="<? echo 1010;?>"  cellpadding="0" cellspacing="0">
            <tfoot>
                <tr bgcolor="#C4C4C4" style="font-weight:bold;text-align:right;">
                    <td width="310" >Grand Total</td>
                    <td width="110"><? echo number_format($gr_yarn_opn_qty,2);?></td>
                    <td width="110"><? echo number_format($gr_yarn_rcv_qty,2);?></td>
                    <td width="110"><? echo number_format($gr_yarn_issue_qty,2);?></td>
                    <td width="110"><? echo number_format($gr_yarn_close_qty,2);?></td>
                    <td width="110"><? echo number_format($gr_yarn_close_val,2);?></td>
                    <td >&nbsp;</td>
                </tr>
            </tfoot>
            </table>
        </div>
    </div>

    <?
    exit();
}

if($action=="open_dyes_chemical_location_details_popup")
{
	echo load_html_head_contents("Dyes Chemical Info.", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo "121"; die;
	$storeArr = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0","id","store_name");
	if($db_type==0)
	{
		$from_date=change_date_format($from_date,'yyyy-mm-dd');
		$to_date=change_date_format($to_date,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$from_date=change_date_format($from_date,'','',1);
		$to_date=change_date_format($to_date,'','',1);
	}
	else
	{
		$from_date=""; $to_date="";
	}
	
	/*if($from_date!="" && $to_date!="")
	{
		$sql_loan="Select a.store_id as STORE_ID, sum(a.cons_quantity) as CONS_QUANTITY, sum(a.cons_amount) as CONS_AMOUNT, 1 as TYPE
		from inv_transaction a, inv_receive_master b
		where a.mst_id=b.id and a.transaction_type=1 and b.receive_purpose=5 and a.transaction_date between '".$from_date."' and '".$to_date."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_loan_cond and a.company_id=$company  group by a.store_id
		union all
		Select a.store_id as STORE_ID, sum(a.cons_quantity) as CONS_QUANTITY, sum(a.cons_amount) as CONS_AMOUNT, 2 as TYPE
		from inv_transaction a, inv_issue_master b
		where a.mst_id=b.id and a.transaction_type=2 and b.issue_purpose=5 and a.transaction_date between '".$from_date."' and '".$to_date."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_loan_cond and a.company_id=$company group by a.store_id
		order by store_id ASC";
	}
	//echo $sql_loan;die;
	$sql_loan_result=sql_select($sql_loan);
	$loan_data=array();
	foreach($sql_loan_result as $row)
	{
		if($row["TYPE"]==1)
		{
			$loan_data[$row["STORE_ID"]]["loan_rcv_qnty"]=$row["CONS_QUANTITY"];
			$loan_data[$row["STORE_ID"]]["loan_rcv_amount"]=$row["CONS_AMOUNT"];
		}
		else
		{
			$loan_data[$row["STORE_ID"]]["loan_issue_qnty"]=$row["CONS_QUANTITY"];
			$loan_data[$row["STORE_ID"]]["loan_issue_amount"]=$row["CONS_AMOUNT"];
		}
	}
  	unset($sql_loan_result);*/

  	/*==========================================================================================/
  	/                   Receive                     /
  	/==========================================================================================*/   
    $sql_dyes = "SELECT a.STORE_ID, a.ORDER_RATE, a.CONS_RATE, a.CONS_QUANTITY, a.TRANSACTION_DATE, a.ITEM_CATEGORY, a.CONS_AMOUNT, a.TRANSACTION_TYPE 
	from inv_transaction a
	where a.item_category in (5,6,7,23) and a.status_active=1 and a.is_deleted=0 and a.company_id=$company and a.store_id>0"; //and a.transaction_date between '$from_date' and '$to_date'
    //echo $sql_receive;die();
    $sql_dyes_result = sql_select($sql_dyes); $all_store_arr=array();   
    foreach($sql_dyes_result as $row)
    {
		$all_store_arr[]=$row["STORE_ID"];
		$trans_date = $row["TRANSACTION_DATE"];
		if(strtotime($trans_date) < strtotime($from_date))
		{
			if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
			{
				$data_array[$row["STORE_ID"]]['total_dyes_rcv_opening_amt']=bcadd($data_array[$row["STORE_ID"]]['total_dyes_rcv_opening_amt'],$row["CONS_AMOUNT"],15);
			}
			else
			{
				$data_array[$row["STORE_ID"]]['total_dyes_issue_opening_amt']=bcadd($data_array[$row["STORE_ID"]]['total_dyes_issue_opening_amt'],$row["CONS_AMOUNT"],15);
			}
		} 
		
		if(strtotime($trans_date) >= strtotime($from_date) && strtotime($trans_date) <= strtotime($to_date))
		{
			if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
			{
				$data_array[$row["STORE_ID"]]['total_dyes_rcv_amt']=bcadd($data_array[$row["STORE_ID"]]['total_dyes_rcv_amt'],$row["CONS_AMOUNT"],15);
			}
			else
			{
				$data_array[$row["STORE_ID"]]['total_dyes_issue_amt']=bcadd($data_array[$row["STORE_ID"]]['total_dyes_issue_amt'],$row["CONS_AMOUNT"],15);
			}
		}
    }
	//echo "<pre>";print_r($data_array);die;
	unset($sql_dyes_result);
	?>
	
	<div align="center">
		<style type="text/css">
        .rpt_table tr TD {font-size: 13px; }
        .rpt_table tr TD a {font-size: 13px; display: block;}
        </style>
        <center><h3>Dyes Chemical</h3></center>
        <center><h3>Date : <? echo change_date_format($from_date);?> To <? echo change_date_format($to_date);?></h3></center>
        <table border="1" rules="all" class="rpt_table" width="800" cellpadding="0" cellspacing="0">
            <thead>
                <tr>        
                    <th width="200">Location Name</th>
                    <th width="150">Opening Value</th>
                    <th width="150">Receive Value</th>
                    <th width="150">Delivery Value</th>
                    <th width="150">Closing Value</th>
                </tr>
            </thead>
        </table>
        <div style="width:820px; max-height:500px; overflow-y:auto" id="scroll_body">
        <table border="1" rules="all" class="rpt_table" width="800" cellpadding="0" cellspacing="0">
            <tbody>
            <?
            $total_dyes_opening_value = 0;
            $total_dyes_rcv_value = 0;
            $total_dyes_delivery_value  = 0;
            $total_dyes_closing_value = 0;
            $i=0;
            $conversion_date=date("Y/m/d");
            $curr_exchange_rate=set_conversion_rate( 2, $conversion_date,$company );
            //echo "<pre>";
            //print_r($all_store_arr);
            $all_store=array_unique($all_store_arr);
            foreach($all_store as $index=> $val)
            {
				$dyes_opening_value =  bcsub($data_array[$val]['total_dyes_rcv_opening_amt'],$data_array[$val]['total_dyes_issue_opening_amt'],15);
				$dyes_rcv_value   = $data_array[$val]['total_dyes_rcv_amt'];
				$dyes_delivery_value = $data_array[$val]['total_dyes_issue_amt'];
				$dyes_closing_value = bcsub(bcadd($dyes_opening_value,$dyes_rcv_value,15),$dyes_delivery_value,15);
				
				if($dyes_opening_value>0 || $dyes_rcv_value>0 || $dyes_delivery_value>0 || $dyes_closing_value>0 )
				{ 
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;       
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trl_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trl_<? echo $i; ?>">
					<td width="200" title="<? echo $store_id;?>"><a href="javascript:void()" onclick="open_store_popup('<? echo $company."_".$from_date."_".$to_date."_".$exchange_rate."_".$val;?>','open_dyes_chemical_item_details_popup')">
					<? echo $storeArr[$val];?>
					</a>
					</td>
					<td width="150" align="right"><p><? echo number_format($dyes_opening_value,0) ?></p></td>
					<td width="150" align="right"><p><? echo number_format($dyes_rcv_value,0) ?></p></td>
					<td width="150" align="right"><p><? echo number_format($dyes_delivery_value,0) ?></p></td>
					<td width="150" align="right"><p><? echo number_format($dyes_closing_value,0) ?></p></td>
					</tr>
					<?
					$i++;
					$total_dyes_opening_value   =bcadd($total_dyes_opening_value,$dyes_opening_value,15);
					$total_dyes_rcv_value       =bcadd($total_dyes_rcv_value,$dyes_rcv_value,15);
					$total_dyes_delivery_value  =bcadd($total_dyes_delivery_value,$dyes_delivery_value,15);
					$total_dyes_closing_value   =bcadd($total_dyes_closing_value,$dyes_closing_value,15);
				}
            }
            ?>
            </tbody>
        </table>
        </div>
        <table border="1" rules="all" class="rpt_table" width="<? echo 800;?>" cellpadding="0" cellspacing="0"> 
            <tfoot>
                <tr bgcolor="#A1A1A1" style="font-weight:bold;text-align:right;">
                    <td width="200">Grand Total:</td>
                    <td width="150" align="right"><p><? echo number_format($total_dyes_opening_value,0) ?></p></td>
                    <td width="150" align="right"><p><? echo number_format($total_dyes_rcv_value,0) ?></p></td>
                    <td width="150" align="right"><p><? echo number_format($total_dyes_delivery_value,0) ?></p></td>
                    <td width="150" align="right"><p><? echo number_format($total_dyes_closing_value,0) ?></p></td>
                </tr>
            </tfoot>
        </table>
	</div> 
     
    <script type="text/javascript">
        function open_store_popup(data,action)
        {
           	width=1330;    
            emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'closing_stock_report_controller.php?data=' + data + '&action=' + action, 'Location Wise Details', 'width='+width+'px,height=350px,center=1,resize=0,scrolling=0', '../../../');
        }
    </script>

  <?
}

if($action=="open_dyes_chemical_item_details_popup")
{
	echo load_html_head_contents(" Dyes Chemical Details", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$data_ex  = explode("_", $data);
	$company  = $data_ex[0];
	$from_date  = $data_ex[1];
	$to_date  = $data_ex[2];
	$exchange_rate= $data_ex[3];
	$store_id   = $data_ex[4];
	
	if($db_type==0)
	{
		$from_date=change_date_format($from_date,'yyyy-mm-dd');
		$to_date=change_date_format($to_date,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$from_date=change_date_format($from_date,'','',1);
		$to_date=change_date_format($to_date,'','',1);
	}
	else
	{
		$from_date=""; $to_date="";
	}
	
	/*if($from_date!="" && $to_date!="")
	{
		$sql_loan="Select a.prod_id as PROD_ID, sum(a.cons_quantity) as CONS_QUANTITY, 1 as type
		from inv_transaction a, inv_receive_master b
		where a.mst_id=b.id and a.transaction_type=1 and b.receive_purpose=5 and a.transaction_date between '".$from_date."' and '".$to_date."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_loan_cond  
		group by a.prod_id
		union all
		Select a.prod_id as PROD_ID, sum(a.cons_quantity) as CONS_QUANTITY, 2 as type
		from inv_transaction a, inv_issue_master b
		where a.mst_id=b.id and a.transaction_type=2 and b.issue_purpose=5 and a.transaction_date between '".$from_date."' and '".$to_date."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_loan_cond and a.store_id=$store_id  
		group by a.prod_id
		order by prod_id ASC";
	}
	//echo $sql_loan;die;
	$sql_loan_result=sql_select($sql_loan);
	$loan_data=array();
	foreach($sql_loan_result as $row)
	{
		if($row[csf("type")]==1)
		{
			$loan_data[$row["PROD_ID"]]["loan_rcv_qnty"]=$row["CONS_QUANTITY"];
		}
		else
		{
			$loan_data[$row["PROD_ID"]]["loan_issue_qnty"]=$row["CONS_QUANTITY"];
		}
	}
	unset($sql_loan_result);
	*/
	//var_dump($loan_data);die;
	
	//product_details_master a, lib_item_group b where a.item_group_id=b.id and a.id=$prod_id
	$itemgroupArr = return_library_array("select id, item_name from lib_item_group where status_active=1 and is_deleted=0","id","item_name");
	$sql_receive = "SELECT b.ID AS PROD_ID, a.ID as TRANS_ID, a.ITEM_CATEGORY, b.ITEM_GROUP_ID, b.ITEM_DESCRIPTION, b.UNIT_OF_MEASURE, a.STORE_ID, a.ORDER_RATE, a.CONS_RATE, a.CONS_QUANTITY, a.TRANSACTION_DATE, a.ITEM_CATEGORY, a.CONS_AMOUNT, a.TRANSACTION_TYPE, b.AVG_RATE_PER_UNIT
	from inv_transaction a, product_details_master b
	where a.prod_id=b.id and a.item_category in (5,6,7,23) and b.item_category_id in (5,6,7,23) and a.status_active=1 and a.is_deleted=0 and a.company_id=$company and a.store_id=$store_id
	order by b.ITEM_DESCRIPTION, TRANS_ID asc"; //and a.transaction_date between '$from_date' and '$to_date'
    //echo $sql_receive;die();
    $receive_res = sql_select($sql_receive);  $all_store_arr=array();   
    foreach($receive_res as $row)
    {
		$data_array[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["PROD_ID"]]['item_description']=$row["ITEM_DESCRIPTION"];
		$data_array[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["PROD_ID"]]['uom']=$row["UNIT_OF_MEASURE"];
		$data_array[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["PROD_ID"]]['avg_rate']=$row["AVG_RATE_PER_UNIT"];
		
		if($row["TRANSACTION_TYPE"]==1)
		{
			$data_array[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["PROD_ID"]]['max_date']=$row["TRANSACTION_DATE"];
			$data_array[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["PROD_ID"]]['last_quantity']=$row["CONS_QUANTITY"];
			$data_array[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["PROD_ID"]]['last_rate']=$row["CONS_RATE"];
		}
		
		$trans_date = $row["TRANSACTION_DATE"];
		if(strtotime($trans_date) < strtotime($from_date))
		{
			if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
			{
				$data_array[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["PROD_ID"]]['rcv_total_opening_amt'] =bcadd($data_array[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["PROD_ID"]]['rcv_total_opening_amt'],$row["CONS_AMOUNT"],15);
				$data_array[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["PROD_ID"]]['rcv_total_opening'] =bcadd($data_array[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["PROD_ID"]]['rcv_total_opening'],$row["CONS_QUANTITY"],15);
			}
			else
			{
				$data_array[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["PROD_ID"]]['iss_total_opening_amt'] =bcadd($data_array[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["PROD_ID"]]['iss_total_opening_amt'],$row["CONS_AMOUNT"],15);
				$data_array[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["PROD_ID"]]['iss_total_opening'] =bcadd($data_array[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["PROD_ID"]]['iss_total_opening'],$row["CONS_QUANTITY"],15);
			}
		}
		if(strtotime($trans_date) >= strtotime($from_date) && strtotime($trans_date) <= strtotime($to_date))
		{
			if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
			{
				$data_array[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["PROD_ID"]]['total_rcv_amt'] =bcadd($data_array[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["PROD_ID"]]['total_rcv_amt'],$row["CONS_AMOUNT"],15);
				$data_array[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["PROD_ID"]]['total_rcv_qnty'] =bcadd($data_array[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["PROD_ID"]]['total_rcv_qnty'],$row["CONS_QUANTITY"],15);
			}
			else
			{
				$data_array[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["PROD_ID"]]['total_issue_amt'] =bcadd($data_array[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["PROD_ID"]]['total_issue_amt'],$row["CONS_AMOUNT"],15);
				$data_array[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["PROD_ID"]]['total_issue_qnty'] =bcadd($data_array[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["PROD_ID"]]['total_issue_qnty'],$row["CONS_QUANTITY"],15);
			}
		}
    }
	//echo "<pre>";print_r($data_array);die;
	$category_rowspan_arr=array(); $group_rowspan_arr=array(); 
	foreach($data_array as $item_category_id=> $item_category_data)
	{
		$category_rowspan=0;
		foreach($item_category_data as $item_group_id=> $item_group_id_data)
		{
			$group_rowspan=0;$group_check=0;
			foreach($item_group_id_data as $prod_id=> $row)
			{
				$dyes_opening_qty		= bcsub($row['rcv_total_opening'],$row['iss_total_opening'],15);
				$dyes_rcv_qty			= $row['total_rcv_qnty'];
				$dyes_issue_qty			= $row['total_issue_qnty'];
				$dyes_closing_qty		= bcsub(bcadd($dyes_opening_qty,$dyes_rcv_qty,15),$dyes_issue_qty,15);
				$opening_value_dyes     = bcsub($row['rcv_total_opening_amt'],$row['iss_total_opening_amt'],15);
				$receive_value_dyes     = $row['total_rcv_amt'];
				$delivery_value_dyes    = $row['total_issue_amt'];
				$closing_value_dyes     = bcsub(bcadd($opening_value_dyes,$receive_value_dyes,15),$delivery_value_dyes,15);
				
				if(number_format($dyes_opening_qty,0)>0 || number_format($dyes_rcv_qty,0)>0 || number_format($dyes_issue_qty,0)>0 || number_format($dyes_closing_qty,0)>0 || number_format($closing_value_dyes,0)>0 )
				{ 
					$category_rowspan++;
					$group_rowspan++;
					$group_check=1;
				}
				$group_rowspan_arr[$item_category_id][$item_group_id]=$group_rowspan;
			}
			//$category_rowspan++;
			if($group_check)
			{
				$category_rowspan++;
			}
		}
		$category_rowspan_arr[$item_category_id]=$category_rowspan;
	}
	//echo "<pre>"; print_r($category_rowspan_arr); die;
  ?>
  <!-- margin:15px 0; -->
  <fieldset class="first_part" style="width:1300px; max-height:320px; ">
    <div id="report_by_buyer_style">
      <style type="text/css">
        .rpt_table tr TD {font-size: 13px; }
        .rpt_table tr TD a {font-size: 13px; display: block;}
      </style>
      <center><h3>Dyes Chemical Details : <? echo return_field_value("store_name","lib_store_location","id=$store_id","store_name"); ?></h3></center>
      <center><h3>Date : <? echo change_date_format($from_date);?> To <? echo change_date_format($to_date);?></h3></center>
      <table border="1" rules="all" class="rpt_table" width="1300" cellpadding="0" cellspacing="0">            
        <thead>
          <tr>
            <th width="100">Category</th>
            <th width="100">Item Group</th>
            <th width="200">Item name</th>
            <th width="60">UOM</th>
            <th width="100">Opening Qty</th>
            <th width="100">Receive Qty</th>
            <th width="100">Delivery Qty</th>
            <th width="100">Closing Qty</th>
            <th width="100">Closing Value</th>
            <th width="70">Avg. Rate</th>
            <th width="100">Last Date Rcv Qty</th>
            <th width="70">Last Rcv Date</th>
            <th>Last Unit Price</th>
          </tr>
        </thead>
      </table>
        <div style="max-height: 250px;overflow-y: scroll;width:1320px">
        <table border="1" rules="all" class="rpt_table" width="1300" cellpadding="0" cellspacing="0"> 
            <tbody>
            <?
            $r=$i=1;
            $grnd_opening_qty=$grnd_rcv_qty=$grnd_issue_qty=$grnd_closing_qty=$grnd_closing_val=$grand_last_rcv_qty = 0;
			ksort($data_array);
            foreach($data_array as $item_category_id=> $item_category_data)
            {
				ksort($item_category_data);
				$category_rowspan=0;
				$total_dyes_opening_qty=$total_dyes_rcv_qty=$total_dyes_issue_qty=$total_dyes_closing_qty=$total_dyes_closing_val=$total_last_rcv_qty= 0;
				foreach($item_category_data as $item_group_id=> $item_group_id_data)
				{
					$group_total_dyes_opening_qty=$group_total_dyes_rcv_qty=$group_total_dyes_issue_qty=$group_total_dyes_closing_qty=$group_total_dyes_closing_val=$group_total_last_rcv_qty=0;
					$group_rowspan=0;
					$group_check=0;
					foreach($item_group_id_data as $prod_id=> $row)
					{
						$dyes_opening_qty		= bcsub($row['rcv_total_opening'],$row['iss_total_opening'],15);
						$dyes_rcv_qty			= $row['total_rcv_qnty'];
						$dyes_issue_qty			= $row['total_issue_qnty'];
						$dyes_closing_qty		= bcsub(bcadd($dyes_opening_qty,$dyes_rcv_qty,15),$dyes_issue_qty,15);
						$opening_value_dyes     = bcsub($row['rcv_total_opening_amt'],$row['iss_total_opening_amt'],15);
						$receive_value_dyes     = $row['total_rcv_amt'];
						$delivery_value_dyes    = $row['total_issue_amt'];
						$closing_value_dyes     = bcsub(bcadd($opening_value_dyes,$receive_value_dyes,15),$delivery_value_dyes,15);
						if($dyes_closing_qty!=0 && $closing_value_dyes !=0) $dyes_closing_avg_rate	= bcdiv($closing_value_dyes,$dyes_closing_qty,15);
						if(number_format($dyes_opening_qty,0)>0 || number_format($dyes_rcv_qty,0)>0 || number_format($dyes_issue_qty,0)>0 || number_format($dyes_closing_qty,0)>0 || number_format($closing_value_dyes,0)>0 )
						{
							$group_check=1; 
							$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;        
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">
							<? if($category_rowspan==0){?>
							<td width="100" valign="middle" rowspan="<? echo $category_rowspan_arr[$item_category_id];?>" align="left"><p><? echo $item_category[$item_category_id];?></p></td>
							<?}?>
							<? if($group_rowspan==0){?>
							<td width="100" valign="middle" rowspan="<? echo $group_rowspan_arr[$item_category_id][$item_group_id];?>" align="left"><p><? echo $itemgroupArr[$item_group_id];?></p></td>
							<?}?>
							<td width="200" title="<? echo $prod_id;?>"><p><? echo $row['item_description'];?></p></td>
							<td width="60" align="center"><p><? echo $unit_of_measurement[$row['uom']];?></p></td>
							<td width="100" align="right"><? echo number_format($dyes_opening_qty,0) ?></td>
							<td width="100" align="right"><? echo number_format($dyes_rcv_qty,0) ?></td>
							<td width="100" align="right"><? echo number_format($dyes_issue_qty,0) ?></td>
							<td width="100" align="right"><? echo number_format($dyes_closing_qty,0) ?></td>
							<td width="100" align="right"><? echo number_format($closing_value_dyes,0) ?></td>
							<td width="70" align="right"><? if( number_format($dyes_closing_qty,0) !=0) echo number_format($dyes_closing_avg_rate,2); else echo "0"; ?></td>
							<td width="100" align="right"><? echo number_format($row['last_quantity'],0) ?></td>
							<td width="70" align="center"><? echo change_date_format($row['max_date']); ?> </td>
							<td align="right"><? echo number_format($row['last_rate'],2) ?></td>
							</tr>
							<?
							$r++; $i++; $category_rowspan++; $group_rowspan++;
							
							$total_dyes_opening_qty   		=bcadd($total_dyes_opening_qty,$dyes_opening_qty,15);
							$total_dyes_rcv_qty       		=bcadd($total_dyes_rcv_qty,$dyes_rcv_qty,15);
							$total_dyes_issue_qty     		=bcadd($total_dyes_issue_qty,$dyes_issue_qty,15);
							$total_dyes_closing_qty   		=bcadd($total_dyes_closing_qty,$dyes_closing_qty,15);
							$total_dyes_closing_val   		=bcadd($total_dyes_closing_val,$closing_value_dyes,15);
							$total_last_rcv_qty       		=bcadd($total_last_rcv_qty,$row['last_quantity'],15);
							
							$group_total_dyes_opening_qty   =bcadd($group_total_dyes_opening_qty,$dyes_opening_qty,15);
							$group_total_dyes_rcv_qty       =bcadd($group_total_dyes_rcv_qty,$dyes_rcv_qty,15);
							$group_total_dyes_issue_qty     =bcadd($group_total_dyes_issue_qty,$dyes_issue_qty,15);
							$group_total_dyes_closing_qty   =bcadd($group_total_dyes_closing_qty,$dyes_closing_qty,15);
							$group_total_dyes_closing_val   =bcadd($closing_value_dyes,15);
							$group_total_last_rcv_qty       =bcadd($group_total_last_rcv_qty,$row['last_quantity'],15);
							
							$grnd_opening_qty   			=bcadd($grnd_opening_qty,$dyes_opening_qty,15);
							$grnd_rcv_qty       			=bcadd($grnd_rcv_qty,$dyes_rcv_qty,15);
							$grnd_issue_qty     			=bcadd($grnd_issue_qty,$dyes_issue_qty,15);
							$grnd_closing_qty   			=bcadd($grnd_closing_qty,$dyes_closing_qty,15);
							$grnd_closing_val   			=bcadd($grnd_closing_val,$closing_value_dyes,15);
							$grand_last_rcv_qty 			=bcadd($grand_last_rcv_qty,$row['last_quantity'],15);
						}
					}
					
					if($group_check)
					{
						?>
						<tr bgcolor="#e7eaed" style="font-weight:bold;text-align:right;">
							<td colspan="3">Group Total:</td>
							<td width="100" align="right"><p><? echo number_format($group_total_dyes_opening_qty,0) ?></p></td>
							<td width="100" align="right"><p><? echo number_format($group_total_dyes_rcv_qty,0) ?></p></td>
							<td width="100" align="right"><p><? echo number_format($group_total_dyes_issue_qty,0) ?></p></td>
							<td width="100" align="right"><p><? echo number_format($group_total_dyes_closing_qty,0) ?></p></td>
							<td width="100" align="right"><p><? echo number_format($group_total_dyes_closing_val,0) ?></p></td>
							<td width="70">&nbsp;</td>
							<td width="100" align="right"><p><? echo number_format($group_total_last_rcv_qty,0) ?></p></td>
							<td width="70" align="right">&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<?
					}
				}
				?>
				<tr bgcolor="#C1C1C1" style="font-weight:bold;text-align:right;">
                    <td colspan="4">Category Total:</td>
                    <td width="100" align="right"><p><? echo number_format($total_dyes_opening_qty,0) ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($total_dyes_rcv_qty,0) ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($total_dyes_issue_qty,0) ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($total_dyes_closing_qty,0) ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($total_dyes_closing_val,0) ?></p></td>
                    <td width="70">&nbsp;</td>
                    <td width="100" align="right"><p><? echo number_format($total_last_rcv_qty,0) ?></p></td>
                    <td width="70">&nbsp;</td>
                    <td>&nbsp;</td>
				</tr>
				<?
            }
            ?>
            </tbody>
      </table>
      </div>
      <table border="1" rules="all" class="rpt_table" width="1300" cellpadding="0" cellspacing="0"> 
        <tfoot>
            <tr bgcolor="#A1A1A1" style="font-weight:bold;text-align:right;">
                <td>Grand Total:</td>
                <td width="100" align="right"><p><? echo number_format($grnd_opening_qty,0) ?></p></td>
                <td width="100" align="right"><p><? echo number_format($grnd_rcv_qty,0) ?></p></td>
                <td width="100" align="right"><p><? echo number_format($grnd_issue_qty,0) ?></p></td>
                <td width="100" align="right"><p><? echo number_format($grnd_closing_qty,0) ?></p></td>
                <td width="100" align="right"><p><? echo number_format($grnd_closing_val,0) ?></p></td>
                <td width="70">&nbsp;</td>
                <td width="100" align="right"><p><? echo number_format($grand_last_rcv_qty,0) ?></p></td>
                <td width="70">&nbsp;</td>
                <td width="85">&nbsp;</td>
            </tr>
        </tfoot>
      </table>
    </div>

    <script type="text/javascript">
        function open_store_popup(data,action)
        {
          width=870;
              
            emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'closing_stock_report_controller.php?data=' + data + '&action=' + action, 'Location Wise Details', 'width='+width+'px,height=350px,center=1,resize=0,scrolling=0', '../../../');
        }
    </script>

  <?
}

if($action=="open_accessories_location_buyer_details_popup")
{
	echo load_html_head_contents("Accessories Location Details Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
	if($db_type==0)
	{
		$from_date=change_date_format($from_date,'yyyy-mm-dd');
		$to_date=change_date_format($to_date,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$from_date=change_date_format($from_date,'','',1);
		$to_date=change_date_format($to_date,'','',1);
	}
	else
	{
		$from_date=""; $to_date="";
	}
	
	/*$po_sql ="Select a.buyer_name, b.id, b.shiping_status, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$po_arr[$row[csf("id")]]['buyer_name']=$row[csf("buyer_name")];
		$po_arr[$row[csf("id")]]['shiping_status']=$row[csf("shiping_status")];
		$po_arr[$row[csf("id")]]['ref_no']=$row[csf("grouping")];
	}
	unset($po_sql_res);
	
	$sql_receive = "SELECT b.id as prod_id,a.item_category,b.item_group_id, b.item_description,b.unit_of_measure, a.store_id, a.order_rate,c.currency_id,a.cons_rate,a.cons_quantity,a.transaction_date ,a.item_category,a.cons_amount, a.transaction_type,d.po_breakdown_id from inv_transaction a, product_details_master b ,inv_receive_master c,  order_wise_pro_details d  where a.mst_id=c.id and a.id=d.trans_id and b.id=d.prod_id and a.transaction_type in (1,4) and a.item_category in (4)  and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company and  a.prod_id=b.id and a.store_id <> 0 and a.company_id=b.company_id ";

    //and a.transaction_date between '$from_date' and '$to_date'
    //die;
    //echo $sql_receive;die();
    $receive_res = sql_select($sql_receive);  $all_store_arr=array();   
    foreach($receive_res as $row)
    {
      $trans_date = $row[csf("transaction_date")];
      $buyer_name=$po_arr[$row[csf("po_breakdown_id")]]['buyer_name'];
      $shiping_status =$po_arr[$row[csf("po_breakdown_id")]]['shiping_status'];
      if(strtotime($trans_date) < strtotime($from_date))
      {
        if($row[csf("currency_id")]==2 && $row[csf("transaction_type")]==1)//usd
        {
          $rate = $row[csf("order_rate")]*$exchange_rate;
          if($row[csf("item_category")]==4){
            $data_array_loc[$row[csf("store_id")]]['total_dyes_rcv_opening_amt']+=$row[csf("cons_amount")];
            $data_array_buyer[$buyer_name]['total_dyes_rcv_opening_amt']+=$row[csf("cons_amount")];
            if($shiping_status==3)
            {
              $data_array_loc[$row[csf("store_id")]]['total_closed_rcv_opening_amt']+=$row[csf("cons_amount")];
              $data_array_buyer[$buyer_name]['total_closed_rcv_opening_amt']+=$row[csf("cons_amount")];
            }
          }
        }
        else
        {
          if($row[csf("item_category")]==4){
            $data_array_loc[$row[csf("store_id")]]['total_dyes_rcv_opening_amt']+=$row[csf("cons_amount")];
            $data_array_buyer[$buyer_name]['total_dyes_rcv_opening_amt']+=$row[csf("cons_amount")];
            if($shiping_status==3)
            {
              $data_array_loc[$row[csf("store_id")]]['total_closed_rcv_opening_amt']+=$row[csf("cons_amount")];
              $data_array_buyer[$buyer_name]['total_closed_rcv_opening_amt']+=$row[csf("cons_amount")];
            }
          }
        }
      } 

      if(strtotime($trans_date) >= strtotime($from_date) && strtotime($trans_date) <= strtotime($to_date))
      {
        if($row[csf("currency_id")]==2  && $row[csf("transaction_type")]==1)//usd
        {
          $rate = $row[csf("order_rate")]*$exchange_rate;
          if($row[csf("item_category")]==4){
            $data_array_loc[$row[csf("store_id")]]['total_dyes_rcv_amt']+= $row[csf("cons_amount")];
            $data_array_buyer[$buyer_name]['total_dyes_rcv_amt']+=$row[csf("cons_amount")];
            if($shiping_status==3)
            {
              $data_array_loc[$row[csf("store_id")]]['total_closed_rcv_amt']+=$row[csf("cons_amount")];
              $data_array_buyer[$buyer_name]['total_closed_rcv_amt']+=$row[csf("cons_amount")];
            }
          }
        }
        else
        {
          if($row[csf("item_category")]==4){
            $data_array_loc[$row[csf("store_id")]]['total_dyes_rcv_amt']+= $row[csf("cons_amount")];
            $data_array_buyer[$buyer_name]['total_dyes_rcv_amt']+=$row[csf("cons_amount")];
            if($shiping_status==3)
            {
              $data_array_loc[$row[csf("store_id")]]['total_closed_rcv_amt']+=$row[csf("cons_amount")];
              $data_array_buyer[$buyer_name]['total_closed_rcv_amt']+=$row[csf("cons_amount")];
            }
          }
        }
      }
    }

    $sql_dyes_issue = "select b.id as prod_id,a.item_category,b.item_group_id, b.item_description,b.unit_of_measure, a.store_id, a.order_rate,a.cons_rate,a.cons_quantity,a.transaction_date ,a.item_category,a.cons_amount, a.transaction_type,d.po_breakdown_id from inv_transaction a,product_details_master b, order_wise_pro_details d where a.transaction_type in (2,3) and a.item_category in (4) and a.status_active=1 and a.is_deleted=0 and a.company_id=$company  and a.prod_id=b.id and a.id=d.trans_id and a.store_id <> 0 and a.company_id=b.company_id "; 

    $issue_dyes_res = sql_select($sql_dyes_issue); 
    foreach ($issue_dyes_res as $row)
    {
      $trans_date = $row[csf("transaction_date")];
      $buyer_name=$po_arr[$row[csf("po_breakdown_id")]]['buyer_name'];
      $shiping_status =$po_arr[$row[csf("po_breakdown_id")]]['shiping_status'];
      if(strtotime($trans_date) < strtotime($from_date))
      {
        if($row[csf("item_category")]==4){
          $data_array_loc[$row[csf("store_id")]]['total_dyes_issue_opening_amt']+=$row[csf("cons_amount")];
          $data_array_buyer[$buyer_name]['total_dyes_issue_opening_amt']+=$row[csf("cons_amount")];
          if($shiping_status==3)
          {
            $data_array_loc[$row[csf("store_id")]]['total_closed_issue_opening_amt']+=$row[csf("cons_amount")];
            $data_array_buyer[$buyer_name]['total_closed_issue_opening_amt']+=$row[csf("cons_amount")];
          }
        }
      }

      if(strtotime($trans_date) >= strtotime($from_date) && strtotime($trans_date) <= strtotime($to_date))
      {
        if($row[csf("item_category")]==4){
          $data_array_loc[$row[csf("store_id")]]['total_dyes_issue_amt']+=$row[csf("cons_amount")];
          $data_array_buyer[$buyer_name]['total_dyes_issue_amt']+=$row[csf("cons_amount")];
          if($shiping_status==3)
          {
            $data_array_loc[$row[csf("store_id")]]['total_closed_issue_amt']+=$row[csf("cons_amount")];
            $data_array_buyer[$buyer_name]['total_closed_issue_amt']+=$row[csf("cons_amount")];
          }
        }
      }
    }
    
    // echo $total_issue_opening_amt;
    //==========================================================================================/
    //                   Transfer                      /
    //==========================================================================================
    $sql_transfer = "select b.id as prod_id,a.item_category,b.item_group_id, b.item_description,b.unit_of_measure, a.store_id, a.order_rate,a.cons_rate,a.cons_quantity,a.transaction_date ,a.item_category,a.cons_amount, a.transaction_type,d.po_breakdown_id from inv_transaction a, inv_item_transfer_mst c, product_details_master b, order_wise_pro_details d where a.mst_id=c.id and a.transaction_type in (5,6) and a.item_category in (4) and a.status_active=1 and a.is_deleted=0 and c.status_active=1  and c.is_deleted=0 and a.company_id=$company  and a.prod_id=b.id and a.id=d.trans_id and a.store_id <> 0 and a.company_id=b.company_id"; 
    $transfer_res = sql_select($sql_transfer);    
    
    $total_trnsfr_in_opening_amt= 0; $total_trnsfr_out_opening_amt= 0; $total_trnsfr_in_amt= 0; $total_trnsfr_out_amt= 0;
    $total_accessories_trnsfr_in_opening_amt=0; $total_accessories_trnsfr_out_opening_amt= 0; $total_accessories_trnsfr_in_amt= 0; $total_accessories_trnsfr_out_amt= 0;
    $total_dyes_trnsfr_in_opening_amt= 0; $total_dyes_trnsfr_out_opening_amt= 0; $total_dyes_trnsfr_in_amt= 0; $total_dyes_trnsfr_out_amt= 0;
    foreach ($transfer_res as $row)
    {
      $trans_date = $row[csf("transaction_date")];
      $buyer_name=$po_arr[$row[csf("po_breakdown_id")]]['buyer_name'];
      $shiping_status =$po_arr[$row[csf("po_breakdown_id")]]['shiping_status'];
      if(strtotime($trans_date) < strtotime($from_date))
      {
        if($row[csf("transaction_type")]==5)
        {
          if($row[csf("item_category")]==4){
            $data_array_loc[$row[csf("store_id")]]['total_dyes_trnsfr_in_opening_amt']+=$row[csf("cons_amount")];
            $data_array_buyer[$buyer_name]['total_dyes_trnsfr_in_opening_amt']+=$row[csf("cons_amount")];
            if($shiping_status==3)
            {
              $data_array_loc[$row[csf("store_id")]]['total_closed_trnsfr_in_opening_amt']+=$row[csf("cons_amount")];
              $data_array_buyer[$buyer_name]['total_closed_trnsfr_in_opening_amt']+=$row[csf("cons_amount")];
            }
          }
        }
        else
        {
          if($row[csf("item_category")]==4){
            $data_array_loc[$row[csf("store_id")]]['total_dyes_trnsfr_out_opening_amt']+=$row[csf("cons_amount")];
            $data_array_buyer[$buyer_name]['total_dyes_trnsfr_out_opening_amt']+=$row[csf("cons_amount")];
            if($shiping_status==3)
            {
              $data_array_loc[$row[csf("store_id")]]['total_closed_trnsfr_out_opening_amt']+=$row[csf("cons_amount")];
              $data_array_buyer[$buyer_name]['total_closed_trnsfr_out_opening_amt']+=$row[csf("cons_amount")];
            }
          }
        }
      }

      if(strtotime($trans_date) >= strtotime($from_date) && strtotime($trans_date) <= strtotime($to_date))
      {
        if($row[csf("transaction_type")]==5)
        {
          if($row[csf("item_category")]==4){
            $data_array_loc[$row[csf("store_id")]]['total_dyes_trnsfr_in_amt']+=$row[csf("cons_amount")];
            $data_array_buyer[$buyer_name]['total_dyes_trnsfr_in_amt']+=$row[csf("cons_amount")];
            if($shiping_status==3)
            {
              $data_array_loc[$row[csf("store_id")]]['total_closed_trnsfr_in_amt']+=$row[csf("cons_amount")];
              $data_array_buyer[$buyer_name]['total_closed_trnsfr_in_amt']+=$row[csf("cons_amount")];
            }
          }
        }
        else
        {
          if($row[csf("item_category")]==4){
            $data_array_loc[$row[csf("store_id")]]['total_dyes_trnsfr_out_amt']+=$row[csf("cons_amount")];
            $data_array_buyer[$buyer_name]['total_dyes_trnsfr_out_amt']+=$row[csf("cons_amount")];
            if($shiping_status==3)
            {
              $data_array_loc[$row[csf("store_id")]]['total_closed_trnsfr_out_amt']+=$row[csf("cons_amount")];
              $data_array_buyer[$buyer_name]['total_closed_trnsfr_out_amt']+=$row[csf("cons_amount")];
            }
          }
        }
      }
    }
	
	

  //echo "<pre>"; print_r($data_array); //die;  
  unset($result);*/
  
  
	$sql_trans = "SELECT m.ID AS JOB_ID, m.BUYER_NAME, n.SHIPING_STATUS, n.GROUPING AS REF_NO, b.ID AS PROD_ID, a.ID as TRANS_ID, a.ITEM_CATEGORY, b.ITEM_GROUP_ID, b.ITEM_DESCRIPTION, b.UNIT_OF_MEASURE, a.STORE_ID, a.ORDER_RATE, a.CONS_RATE, a.CONS_QUANTITY, a.TRANSACTION_DATE, a.ITEM_CATEGORY, a.CONS_AMOUNT, a.TRANSACTION_TYPE, b.AVG_RATE_PER_UNIT
	from wo_po_details_master m, wo_po_break_down n, order_wise_pro_details p, inv_transaction a, product_details_master b
	where m.job_no=n.job_no_mst and n.id=p.po_breakdown_id and p.trans_id=a.id and a.prod_id=b.id and a.item_category=4 and b.item_category_id=4 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and a.company_id=$company and a.store_id>0"; //and a.transaction_date between '$from_date' and '$to_date'
    //echo $sql_trans;die();
    $sql_trans_result = sql_select($sql_trans);
	$store_wise_arr=array();$buyer_wise_arr=array(); 
    foreach($sql_trans_result as $row)
    {
		if($trans_check[$row["TRANS_ID"]]=="")
		{
			$trans_check[$row["TRANS_ID"]]=$row["TRANS_ID"];
			$trans_date = $row["TRANSACTION_DATE"];
			if(strtotime($trans_date) < strtotime($from_date))
			{
				if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
				{
					if($row["SHIPING_STATUS"]==3)
					{
						$store_wise_arr[$row["STORE_ID"]]['rcv_total_opening_amt_close'] =bcadd($store_wise_arr[$row["STORE_ID"]]['rcv_total_opening_amt_close'],$row["CONS_AMOUNT"],15);
						$store_wise_arr[$row["STORE_ID"]]['rcv_total_opening_close'] =bcadd($store_wise_arr[$row["STORE_ID"]]['rcv_total_opening_close'],$row["CONS_QUANTITY"],15);
						
						$buyer_wise_arr[$row["BUYER_NAME"]]['rcv_total_opening_amt_close'] =bcadd($buyer_wise_arr[$row["BUYER_NAME"]]['rcv_total_opening_amt_close'],$row["CONS_AMOUNT"],15);
						$buyer_wise_arr[$row["BUYER_NAME"]]['rcv_total_opening_close'] =bcadd($buyer_wise_arr[$row["BUYER_NAME"]]['rcv_total_opening_close'],$row["CONS_QUANTITY"],15);
					}
					else
					{
						$store_wise_arr[$row["STORE_ID"]]['rcv_total_opening_amt'] =bcadd($store_wise_arr[$row["STORE_ID"]]['rcv_total_opening_amt'],$row["CONS_AMOUNT"],15);
						$store_wise_arr[$row["STORE_ID"]]['rcv_total_opening'] =bcadd($store_wise_arr[$row["STORE_ID"]]['rcv_total_opening'],$row["CONS_QUANTITY"],15);
						
						$buyer_wise_arr[$row["BUYER_NAME"]]['rcv_total_opening_amt'] =bcadd($buyer_wise_arr[$row["BUYER_NAME"]]['rcv_total_opening_amt'],$row["CONS_AMOUNT"],15);
						$buyer_wise_arr[$row["BUYER_NAME"]]['rcv_total_opening'] =bcadd($buyer_wise_arr[$row["BUYER_NAME"]]['rcv_total_opening'],$row["CONS_QUANTITY"],15);
					}
					
				}
				else
				{
					if($row["SHIPING_STATUS"]==3)
					{
						$store_wise_arr[$row["STORE_ID"]]['iss_total_opening_amt_close'] =bcadd($store_wise_arr[$row["STORE_ID"]]['iss_total_opening_amt_close'],$row["CONS_AMOUNT"],15);
						$store_wise_arr[$row["STORE_ID"]]['iss_total_opening_close'] =bcadd($store_wise_arr[$row["STORE_ID"]]['iss_total_opening_close'],$row["CONS_QUANTITY"],15);
						
						$buyer_wise_arr[$row["BUYER_NAME"]]['iss_total_opening_amt_close'] =bcadd($buyer_wise_arr[$row["BUYER_NAME"]]['iss_total_opening_amt_close'],$row["CONS_AMOUNT"],15);
						$buyer_wise_arr[$row["BUYER_NAME"]]['iss_total_opening_close'] =bcadd($buyer_wise_arr[$row["BUYER_NAME"]]['iss_total_opening_close'],$row["CONS_QUANTITY"],15);
					}
					else
					{
						$store_wise_arr[$row["STORE_ID"]]['iss_total_opening_amt'] =bcadd($store_wise_arr[$row["STORE_ID"]]['iss_total_opening_amt'],$row["CONS_AMOUNT"],15);
						$store_wise_arr[$row["STORE_ID"]]['iss_total_opening'] =bcadd($store_wise_arr[$row["STORE_ID"]]['iss_total_opening'],$row["CONS_QUANTITY"],15);
						
						$buyer_wise_arr[$row["BUYER_NAME"]]['iss_total_opening_amt'] =bcadd($buyer_wise_arr[$row["BUYER_NAME"]]['iss_total_opening_amt'],$row["CONS_AMOUNT"],15);
						$buyer_wise_arr[$row["BUYER_NAME"]]['iss_total_opening'] =bcadd($buyer_wise_arr[$row["BUYER_NAME"]]['iss_total_opening'],$row["CONS_QUANTITY"],15);
					}
				}
			}
			
			if(strtotime($trans_date) >= strtotime($from_date) && strtotime($trans_date) <= strtotime($to_date))
			{
				if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
				{
					if($row["SHIPING_STATUS"]==3)
					{
						$store_wise_arr[$row["STORE_ID"]]['total_rcv_amt_close'] =bcadd($store_wise_arr[$row["STORE_ID"]]['total_rcv_amt_close'],$row["CONS_AMOUNT"],15);
						$store_wise_arr[$row["STORE_ID"]]['total_rcv_qnty_close'] =bcadd($store_wise_arr[$row["STORE_ID"]]['total_rcv_qnty_close'],$row["CONS_QUANTITY"],15);
						
						$buyer_wise_arr[$row["BUYER_NAME"]]['total_rcv_amt_close'] =bcadd($buyer_wise_arr[$row["BUYER_NAME"]]['total_rcv_amt_close'],$row["CONS_AMOUNT"],15);
						$buyer_wise_arr[$row["BUYER_NAME"]]['total_rcv_qnty_close'] =bcadd($buyer_wise_arr[$row["BUYER_NAME"]]['total_rcv_qnty_close'],$row["CONS_QUANTITY"],15);
					}
					else
					{
						$store_wise_arr[$row["STORE_ID"]]['total_rcv_amt'] =bcadd($store_wise_arr[$row["STORE_ID"]]['total_rcv_amt'],$row["CONS_AMOUNT"],15);
						$store_wise_arr[$row["STORE_ID"]]['total_rcv_qnty'] =bcadd($store_wise_arr[$row["STORE_ID"]]['total_rcv_qnty'],$row["CONS_QUANTITY"],15);
						
						$buyer_wise_arr[$row["BUYER_NAME"]]['total_rcv_amt'] =bcadd($buyer_wise_arr[$row["BUYER_NAME"]]['total_rcv_amt'],$row["CONS_AMOUNT"],15);
						$buyer_wise_arr[$row["BUYER_NAME"]]['total_rcv_qnty'] =bcadd($buyer_wise_arr[$row["BUYER_NAME"]]['total_rcv_qnty'],$row["CONS_QUANTITY"],15);
					}
				}
				else
				{
					if($row["SHIPING_STATUS"]==3)
					{
						$store_wise_arr[$row["STORE_ID"]]['total_issue_amt_close'] =bcadd($store_wise_arr[$row["STORE_ID"]]['total_issue_amt_close'],$row["CONS_AMOUNT"],15);
						$store_wise_arr[$row["STORE_ID"]]['total_issue_qnty_close'] =bcadd($store_wise_arr[$row["STORE_ID"]]['total_issue_qnty_close'],$row["CONS_QUANTITY"],15);
						
						$buyer_wise_arr[$row["BUYER_NAME"]]['total_issue_amt_close'] =bcadd($buyer_wise_arr[$row["BUYER_NAME"]]['total_issue_amt_close'],$row["CONS_AMOUNT"],15);
						$buyer_wise_arr[$row["BUYER_NAME"]]['total_issue_qnty_close'] =bcadd($buyer_wise_arr[$row["BUYER_NAME"]]['total_issue_qnty_close'],$row["CONS_QUANTITY"],15);
					}
					else
					{
						$store_wise_arr[$row["STORE_ID"]]['total_issue_amt'] =bcadd($store_wise_arr[$row["STORE_ID"]]['total_issue_amt'],$row["CONS_AMOUNT"],15);
						$store_wise_arr[$row["STORE_ID"]]['total_issue_qnty'] =bcadd($store_wise_arr[$row["STORE_ID"]]['total_issue_qnty'],$row["CONS_QUANTITY"],15);
						
						$buyer_wise_arr[$row["BUYER_NAME"]]['total_issue_amt'] =bcadd($buyer_wise_arr[$row["BUYER_NAME"]]['total_issue_amt'],$row["CONS_AMOUNT"],15);
						$buyer_wise_arr[$row["BUYER_NAME"]]['total_issue_qnty'] =bcadd($buyer_wise_arr[$row["BUYER_NAME"]]['total_issue_qnty'],$row["CONS_QUANTITY"],15);
					}
				}
			}
		}
    }
  
	//echo "<pre>";print_r($buyer_wise_arr);die;
  
	foreach($store_wise_arr as $store_id => $row)
	{
		$total_opening_value=bcsub($row["rcv_total_opening_amt"],$row["iss_total_opening_amt"],15);
		$total_rcv_amt=$row["total_rcv_amt"];
		$total_issue_amt=$row["total_issue_amt"];
		$total_closing_value=bcsub(bcadd($total_opening_value,$total_rcv_amt,15),$total_issue_amt,15);
		
		$total_opening_value_close=bcsub($row["rcv_total_opening_amt_close"],$row["iss_total_opening_amt_close"],15);
		$total_rcv_amt_close=$row["total_rcv_amt_close"];
		$total_issue_amt_close=$row["total_issue_amt_close"];
		$total_closing_value_close=bcsub(bcadd($total_opening_value_close,$total_rcv_amt_close,15),$total_issue_amt_close,15);
		
		if($total_opening_value>=1 || $total_rcv_amt>=1 || $total_issue_amt>=1 || $total_closing_value>=1 || $total_closing_value_close>=1 ){ 
			$rowspan++;
		}
	}

	foreach($buyer_wise_arr as $buyer_id => $row)
	{
		$total_opening_value=bcsub($row["rcv_total_opening_amt"],$row["iss_total_opening_amt"],15);
		$total_rcv_amt=$row["total_rcv_amt"];
		$total_issue_amt=$row["total_issue_amt"];
		$total_closing_value=bcsub(bcadd($total_opening_value,$total_rcv_amt,15),$total_issue_amt,15);
		
		$total_opening_value_close=bcsub($row["rcv_total_opening_amt_close"],$row["iss_total_opening_amt_close"],15);
		$total_rcv_amt_close=$row["total_rcv_amt_close"];
		$total_issue_amt_close=$row["total_issue_amt_close"];
		$total_closing_value_close=bcsub(bcadd($total_opening_value_close,$total_rcv_amt_close,15),$total_issue_amt_close,15);
		if($total_opening_value>=1 || $total_rcv_amt>=1 || $total_issue_amt>=1 || $total_closing_value>=1 || $total_closing_value_close>=1 ){ 
			$b_rowspan++;
		}
	}
	//$rowspan_loc_arr[]=$rowspan;
	$storeArr = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0","id","store_name");
	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	//echo "<pre>"; print_r($rowspan_loc_arr); die;
	?>
	<div align="center">
		<style type="text/css">
        .rpt_table tr TD {font-size: 13px; }
        .rpt_table tr TD a {font-size: 13px; display: block;}
        </style>
        <center><h3>Date : <? echo change_date_format($from_date);?> To <? echo change_date_format($to_date);?></h3></center>
        <table border="1" rules="all" class="rpt_table" width="940" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th width="200">Location Wise Summery</th> 
                    <th width="200">Location Name</th>
                    <th width="100">Opening Value</th>
                    <th width="100">Receive Value</th>
                    <th width="100">Delivery Value</th>
                    <th width="100">Closing Value</th>
                    <th>Closed Order Value</th>
                </tr>
            </thead>
        </table>
        <div style="width:940px; max-height:145px; overflow-y:auto" id="scroll_body">
        <table border="1" rules="all" class="rpt_table" width="923" cellpadding="0" cellspacing="0">
            <tbody>
            <?
            $total_dyes_opening_value = 0;
            $total_dyes_rcv_value     = 0;
            $total_dyes_delivery_value= 0;
            $total_dyes_closing_value = 0;
            $total_closed_value       = 0;
            $i=0;
            $conversion_date=date("Y/m/d");
            $curr_exchange_rate=set_conversion_rate( 2, $conversion_date,$company );
            foreach($store_wise_arr  as $store_id=> $row)
            {
				$total_opening_value=bcsub($row["rcv_total_opening_amt"],$row["iss_total_opening_amt"],15);
				$total_rcv_amt=$row["total_rcv_amt"];
				$total_issue_amt=$row["total_issue_amt"];
				$total_closing_value=bcsub(bcadd($total_opening_value,$total_rcv_amt,15),$total_issue_amt,15);
				
				$total_opening_value_close=bcsub($row["rcv_total_opening_amt_close"],$row["iss_total_opening_amt_close"],15);
				$total_rcv_amt_close=$row["total_rcv_amt_close"];
				$total_issue_amt_close=$row["total_issue_amt_close"];
				$total_closing_value_close=bcsub(bcadd($total_opening_value_close,$total_rcv_amt_close,15),$total_issue_amt_close,15);
				if($total_opening_value>=1 || $total_rcv_amt>=1 || $total_issue_amt>=1 || $total_closing_value>=1 || $total_closing_value_close>=1 )
				{ 
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;        
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trl_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trl_<? echo $i; ?>">
					<? if($condition==0){?>
					<td width="200" valign="middle" rowspan="<? echo $rowspan; ?>" align="left"  style="transform: rotate(270deg);font: bold 14px Sans-Serif"><strong>LOCATION WISE SUMMERY</strong></td>
					<?}?>
					<td width="200" ><a href="javascript:void()" onclick="open_acc_loc_popup('<? echo $company."_".$from_date."_".$to_date."_".$exchange_rate."_".$store_id."_1";?>','open_acc_location_details_popup')">
					<? echo $storeArr[$store_id];?>
					</a>
					</td>
					<td width="100" align="right"><p><? echo number_format($total_opening_value,0) ?></p></td>
					<td width="100" align="right"><p><? echo number_format($total_rcv_amt,0) ?></p></td>
					<td width="100" align="right"><p><? echo number_format($total_issue_amt,0) ?></p></td>
					<td width="100" align="right"><p><? echo number_format($total_closing_value,0) ?></p></td>
					<td align="right"><p><? echo number_format($total_closing_value_close,0) ?></p></td>
					</tr>
					<?
					$i++; $condition++;
					$total_dyes_opening_value   =bcadd($total_dyes_opening_value,$total_opening_value,15);
					$total_dyes_rcv_value       =bcadd($total_dyes_rcv_value,$total_rcv_amt,15);
					$total_dyes_delivery_value  =bcadd($total_dyes_delivery_value,$total_issue_amt,15);
					$total_dyes_closing_value   =bcadd($total_dyes_closing_value,$total_closing_value,15);
					$total_closed_value         =bcadd($total_closed_value,$total_closing_value_close,15);
				}
            }
            ?>
            </tbody>
        </table>
        </div>
        <table border="1" rules="all" class="rpt_table" width="940" cellpadding="0" cellspacing="0">
        <tr bgcolor="#339fff">
        <td width="403" align="right"><strong>Total:</strong></td>
        <td width="100" align="right"><p><strong><? echo number_format($total_dyes_opening_value,0) ?></strong></p></td>
        <td width="100" align="right"><p><strong><? echo number_format($total_dyes_rcv_value,0) ?></strong></p></td>
        <td width="100" align="right"><p><strong><? echo number_format($total_dyes_delivery_value,0) ?></strong></p></td>
        <td width="100" align="right"><p><strong><? echo number_format($total_dyes_closing_value,0) ?></strong></p></td>
        <td align="right"><p><strong><? echo number_format($total_closed_value,0) ?></strong></p></td>
        </tr>
        </table>
        <table border="1" rules="all" class="rpt_table" width="940" cellpadding="0" cellspacing="0">
        <thead>
        <tr>
        <th width="200">Buyer Wise Summery</th> 
        <th width="200">Buyer Name</th>
        <th width="100">Opening Value</th>
        <th width="100">Receive Value</th>
        <th width="100">Delivery Value</th>
        <th width="100">Closing Value</th>
        <th>Closed Order Value</th>
        </tr>
        </thead>
        </table>
        <div style="width:940px; max-height:145px; overflow-y:auto" id="scroll_body">
        <table border="1" rules="all" class="rpt_table" width="923" cellpadding="0" cellspacing="0">
            <tbody>
            <?
            $total_dyes_opening_value = 0;
            $total_dyes_rcv_value     = 0;
            $total_dyes_delivery_value= 0;
            $total_dyes_closing_value = 0;
            $total_closed_value       = 0;
            $i=0;
            $conversion_date=date("Y/m/d");
            $curr_exchange_rate=set_conversion_rate( 2, $conversion_date,$company );
            //echo "<pre>";
            //print_r($all_store_arr);
            $all_store=array_unique($all_store_arr);
            foreach($buyer_wise_arr  as $buyer_id=> $row)
            {
                $total_opening_value=$row["rcv_total_opening_amt"]-$row["iss_total_opening_amt"];
                $total_rcv_amt=$row["total_rcv_amt"];
                $total_issue_amt=$row["total_issue_amt"];
                $total_closing_value=bcsub(bcadd($total_opening_value,$total_rcv_amt,15),$total_issue_amt,15);
                
                $total_opening_value_close=$row["rcv_total_opening_amt_close"]-$row["iss_total_opening_amt_close"];
                $total_rcv_amt_close=$row["total_rcv_amt_close"];
                $total_issue_amt_close=$row["total_issue_amt_close"];
                $total_closing_value_close=bcsub(bcadd($total_opening_value_close,$total_rcv_amt_close,15),$total_issue_amt_close,15);
                if($total_opening_value>=1 || $total_rcv_amt>=1 || $total_issue_amt>=1 || $total_closing_value>=1 || $total_closing_value_close>=1 )
                { 
                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;        
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trl_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trl_<? echo $i; ?>">
                    <? if($b_condition==0){?>
                    <td width="200" valign="middle" rowspan="<? echo $b_rowspan; ?>" align="left"  style="transform: rotate(270deg);font: bold 14px Sans-Serif"><strong>BUYER WISE SUMMERY</strong></td>
                    <?}?>
                    <td width="200" title="<? echo $buyer_id;?>"><a href="javascript:void()" onclick="open_acc_loc_popup('<? echo $company."_".$from_date."_".$to_date."_".$exchange_rate."_".$buyer_id."_2";?>','open_acc_location_details_popup')">
                    <? echo $buyer_arr[$buyer_id];?>
                    </a>
                    </td>
                    <td width="100" align="right"><p><? echo number_format($total_opening_value,0) ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($total_rcv_amt,0) ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($total_issue_amt,0) ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($total_closing_value,0) ?></p></td>
                    <td align="right"><p><? echo number_format($total_closing_value_close,0) ?></p></td>
                    </tr>
                    <?
                    $i++; $b_condition++;
                    $total_dyes_opening_value   =bcadd($total_dyes_opening_value,$total_opening_value,15);

					$total_dyes_rcv_value       =bcadd($total_dyes_rcv_value,$total_rcv_amt,15);
					$total_dyes_delivery_value  =bcadd($total_dyes_delivery_value,$total_issue_amt,15);
					$total_dyes_closing_value   =bcadd($total_dyes_closing_value,$total_closing_value,15);
					$total_closed_value         =bcadd($total_closed_value,$total_closing_value_close,15);
                }
            }
            ?>
            </tbody>
        </table>
        </div>
        <table border="1" rules="all" class="rpt_table" width="940" cellpadding="0" cellspacing="0">
        <tr bgcolor="#339fff">
        <td width="403" align="right"><strong>Total:</strong></td>
        <td width="100" align="right"><p><strong><? echo number_format($total_dyes_opening_value,0) ?></strong></p></td>
        <td width="100" align="right"><p><strong><? echo number_format($total_dyes_rcv_value,0) ?></strong></p></td>
        <td width="100" align="right"><p><strong><? echo number_format($total_dyes_delivery_value,0) ?></strong></p></td>
        <td width="100" align="right"><p><strong><? echo number_format($total_dyes_closing_value,0) ?></strong></p></td>
        <td align="right"><p><strong><? echo number_format($total_closed_value,0) ?></strong></p></td>
        </tr>
        </table>
	</div>    
	<script type="text/javascript">
	function open_acc_loc_popup(data,action,type)
	{
	width=1070;  
	//open_acc_loc_popup('1_02-Apr-2021_15-Apr-2021_82_211_1','open_acc_location_details_popup')
	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'closing_stock_report_controller.php?data=' + data + '&action=' + action, 'Location Wise Details', 'width='+width+'px,height=340px,center=1,resize=0,scrolling=0', '../../../');
	}
	</script>
	<?
}

if($action=="open_acc_location_details_popup")
{
	echo load_html_head_contents("Accessories Location Details Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$data_ex      = explode("_", $data);
	$company      = $data_ex[0];
	$from_date    = $data_ex[1];
	$to_date      = $data_ex[2];
	$exchange_rate  = $data_ex[3];
	$store_buyer_id = $data_ex[4];
	$type         = $data_ex[5];
	//echo $store_buyer_id; die;
	
	if($db_type==0)
	{
		$from_date=change_date_format($from_date,'yyyy-mm-dd');
		$to_date=change_date_format($to_date,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$from_date=change_date_format($from_date,'','',1);
		$to_date=change_date_format($to_date,'','',1);
	}
	else
	{
		$from_date=""; $to_date="";
	}
	
	
	
	/*$po_sql ="Select a.buyer_name, b.id, b.shiping_status, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
	$po_arr[$row[csf("id")]]['buyer_name']    =$row[csf("buyer_name")];
	$po_arr[$row[csf("id")]]['shiping_status']=$row[csf("shiping_status")];
	$po_arr[$row[csf("id")]]['ref_no']        =$row[csf("grouping")];
	}
	unset($po_sql_res);

  $sql_receive = "SELECT b.id as prod_id,a.item_category,b.item_group_id, b.item_description,b.unit_of_measure, a.store_id, a.order_rate,c.currency_id,a.cons_rate,a.cons_quantity,a.transaction_date ,a.item_category,a.cons_amount, a.transaction_type,d.po_breakdown_id from inv_transaction a, product_details_master b ,inv_receive_master c,  order_wise_pro_details d  where a.mst_id=c.id and a.id=d.trans_id and b.id=d.prod_id and a.transaction_type in (1,4) and a.item_category in (4)  and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company $store_cond $itemGroup_cond and a.prod_id=b.id and a.store_id <> 0 and a.company_id=b.company_id ";

    //and a.transaction_date between '$from_date' and '$to_date'
    //die;
    //echo $sql_receive;die();
    $receive_res = sql_select($sql_receive);  $all_store_arr=array();   
    //$row[csf("item_category")]]
    //$total_rcv_opening_amt = 0; $total_rcv_amt = 0; $total_dyes_rcv_opening_amt = 0; $total_dyes_rcv_amt = 0; $total_accessories_rcv_opening_amt = 0; $total_accessories_rcv_amt = 0;
    
    foreach($receive_res as $row)
    {
      $trans_date     = $row[csf("transaction_date")];
      $shiping_status =$po_arr[$row[csf("po_breakdown_id")]]['shiping_status'];
      $buyer_name     =$po_arr[$row[csf("po_breakdown_id")]]['buyer_name'];
      $ref_no         =$po_arr[$row[csf("po_breakdown_id")]]['ref_no'];
      
      if($type==1 || ($type==2 & ($buyer_name==$buyer_id)))
      {
        //echo 1111;
        if(strtotime($trans_date) < strtotime($from_date))
        {
          if($row[csf("currency_id")]==2 && $row[csf("transaction_type")]==1)//usd
          {
            $rate = $row[csf("order_rate")]*$exchange_rate;
            if($row[csf("item_category")]==4){
              $data_array[$row[csf("item_group_id")]][$row[csf("unit_of_measure")]]['total_dyes_rcv_opening_amt']+=$row[csf("cons_amount")];
              $data_array[$row[csf("item_group_id")]][$row[csf("unit_of_measure")]]['rcv_total_opening']+=$row[csf("cons_quantity")];
              if($shiping_status==3)
              {
                $data_array[$row[csf("item_group_id")]][$row[csf("unit_of_measure")]]['total_closed_rcv_opening_amt']+=$row[csf("cons_amount")];
              }
              $all_store_arr[]=$row[csf("item_group_id")];
            }
          }
          else
          {
            //echo "m";
            if($row[csf("item_category")]==4){
              $data_array[$row[csf("item_group_id")]][$row[csf("unit_of_measure")]]['total_dyes_rcv_opening_amt']+=$row[csf("cons_amount")];
              $data_array[$row[csf("item_group_id")]][$row[csf("unit_of_measure")]]['rcv_total_opening']+=$row[csf("cons_quantity")];
              if($shiping_status==3)
              {
                $data_array[$row[csf("item_group_id")]][$row[csf("unit_of_measure")]]['']+=$row[csf("cons_amount")];
              }
              $all_store_arr[]=$row[csf("item_group_id")];
            }
          }
        } 

        if(strtotime($trans_date) >= strtotime($from_date) && strtotime($trans_date) <= strtotime($to_date))
        {
          if($row[csf("currency_id")]==2  && $row[csf("transaction_type")]==1)//usd
          {
            $rate = $row[csf("order_rate")]*$exchange_rate;
            if($row[csf("item_category")]==4){
              $data_array[$row[csf("item_group_id")]][$row[csf("unit_of_measure")]]['total_dyes_rcv_amt']+= $row[csf("cons_amount")];
              $data_array[$row[csf("item_group_id")]][$row[csf("unit_of_measure")]]['purchase']+=$row[csf("cons_quantity")];
              if($shiping_status==3)
              {
                $data_array[$row[csf("item_group_id")]][$row[csf("unit_of_measure")]]['total_closed_rcv_amt']+=$row[csf("cons_amount")];
              }
              $all_store_arr[]=$row[csf("item_group_id")];
            }
          }
          else
          {
            if($row[csf("item_category")]==4){
              $data_array[$row[csf("item_group_id")]][$row[csf("unit_of_measure")]]['total_dyes_rcv_amt']+= $row[csf("cons_amount")];
              $data_array[$row[csf("item_group_id")]][$row[csf("unit_of_measure")]]['purchase']+=$row[csf("cons_quantity")];
              if($shiping_status==3)
              {
                $data_array[$row[csf("item_group_id")]][$row[csf("unit_of_measure")]]['total_closed_rcv_amt']+=$row[csf("cons_amount")];
              }
              $all_store_arr[]=$row[csf("item_group_id")];
            }
          }
        }
      }
    }
    //echo "<pre>"; print_r($data_array); die;
    $sql_dyes_issue = "select b.id as prod_id,a.item_category,b.item_group_id, b.item_description,b.unit_of_measure, a.store_id, a.order_rate,a.cons_rate,a.cons_quantity,a.transaction_date ,a.item_category,a.cons_amount, a.transaction_type,d.po_breakdown_id from inv_transaction a,product_details_master b, order_wise_pro_details d where a.transaction_type in (2,3) and a.item_category in (4) and a.status_active=1 and a.is_deleted=0 and a.company_id=$company $store_cond $itemGroup_cond and a.prod_id=b.id and a.id=d.trans_id and a.store_id <> 0 and a.company_id=b.company_id "; 

    $issue_dyes_res = sql_select($sql_dyes_issue); 
    foreach ($issue_dyes_res as $row)
    {
      $trans_date     = $row[csf("transaction_date")];
      $shiping_status =$po_arr[$row[csf("po_breakdown_id")]]['shiping_status'];
      $rate = $row["ORDER_RATE"]*$exchange_rate;
      $buyer_name     =$po_arr[$row[csf("po_breakdown_id")]]['buyer_name'];
      $ref_no         =$po_arr[$row[csf("po_breakdown_id")]]['ref_no'];
      
      if($type==1 || ($type==2 & ($buyer_name==$buyer_id)))
      {
        if(strtotime($trans_date) < strtotime($from_date))
        {
          if($row[csf("item_category")]==4){
            $data_array[$row[csf("item_group_id")]][$row[csf("unit_of_measure")]]['total_dyes_issue_opening_amt']+=$row[csf("cons_amount")];
            $data_array[$row[csf("item_group_id")]][$row[csf("unit_of_measure")]]['iss_total_opening']+=$row[csf("cons_quantity")];
            if($shiping_status==3)
            {
              $data_array[$row[csf("item_group_id")]][$row[csf("unit_of_measure")]]['total_closed_issue_opening_amt']+=$row[csf("cons_amount")];
            }
            $all_store_arr[]=$row[csf("item_group_id")];
          }
        }

        if(strtotime($trans_date) >= strtotime($from_date) && strtotime($trans_date) <= strtotime($to_date))
        {
          if($row[csf("item_category")]==4){
            $data_array[$row[csf("item_group_id")]][$row[csf("unit_of_measure")]]['total_dyes_issue_amt']+=$row[csf("cons_amount")];
            $data_array[$row[csf("item_group_id")]][$row[csf("unit_of_measure")]]['issue']+=$row[csf("cons_quantity")];
            if($shiping_status==3)
            {
              $data_array[$row[csf("item_group_id")]][$row[csf("unit_of_measure")]]['total_closed_issue_amt']+=$row[csf("cons_amount")];
            }
            $all_store_arr[]=$row[csf("item_group_id")];
          }
        }
      }
    }
    
    //==========================================================================================/
    //                   Transfer                      /
    //==========================================================================================/
    $sql_transfer = "select b.id as prod_id,a.item_category,b.item_group_id, b.item_description,b.unit_of_measure, a.store_id, a.order_rate,a.cons_rate,a.cons_quantity,a.transaction_date ,a.item_category,a.cons_amount, a.transaction_type,d.po_breakdown_id from inv_transaction a, inv_item_transfer_mst c, product_details_master b, order_wise_pro_details d where a.mst_id=c.id and a.transaction_type in (5,6) and a.item_category in (4) and a.status_active=1 and a.is_deleted=0 and c.status_active=1  and c.is_deleted=0 and a.company_id=$company $store_cond $itemGroup_cond and a.prod_id=b.id and a.id=d.trans_id and a.store_id <> 0 and a.company_id=b.company_id"; 

    $transfer_res = sql_select($sql_transfer);    
    
    $total_trnsfr_in_opening_amt= 0; $total_trnsfr_out_opening_amt= 0; $total_trnsfr_in_amt= 0; $total_trnsfr_out_amt= 0;
    $total_accessories_trnsfr_in_opening_amt=0; $total_accessories_trnsfr_out_opening_amt= 0; $total_accessories_trnsfr_in_amt= 0; $total_accessories_trnsfr_out_amt= 0;
    $total_dyes_trnsfr_in_opening_amt= 0; $total_dyes_trnsfr_out_opening_amt= 0; $total_dyes_trnsfr_in_amt= 0; $total_dyes_trnsfr_out_amt= 0;
    foreach ($transfer_res as $row)
    {
      $trans_date     = $row[csf("transaction_date")];
      $shiping_status =$po_arr[$row[csf("po_breakdown_id")]]['shiping_status'];
      $buyer_name     =$po_arr[$row[csf("po_breakdown_id")]]['buyer_name'];
      $ref_no         =$po_arr[$row[csf("po_breakdown_id")]]['ref_no'];
      
      if($type==1 || ($type==2 & ($buyer_name==$buyer_id)))
      {
        if(strtotime($trans_date) < strtotime($from_date))
        {
          //echo $exchange_rate.'==';
          if($row[csf("transaction_type")]==5)
          {
            if($row[csf("item_category")]==4){
              $data_array[$row[csf("item_group_id")]][$row[csf("unit_of_measure")]]['total_dyes_trnsfr_in_opening_amt']+=$row[csf("cons_amount")];
              $data_array[$row[csf("item_group_id")]][$row[csf("unit_of_measure")]]['item_opening_transfer_receive']+=$row[csf("cons_quantity")];
              if($shiping_status==3)
              {
                $data_array[$row[csf("item_group_id")]][$row[csf("unit_of_measure")]]['total_closed_trnsfr_in_opening_amt']+=$row[csf("cons_amount")];
              }
              $all_store_arr[]=$row[csf("store_id")];
            }
          }
          else
          {
            if($row[csf("item_category")]==4){
            $data_array[$row[csf("item_group_id")]][$row[csf("unit_of_measure")]]['total_dyes_trnsfr_out_opening_amt']+=$row[csf("cons_amount")];
            $data_array[$row[csf("item_group_id")]][$row[csf("unit_of_measure")]]['item_opening_transfer_issue']+=$row[csf("cons_quantity")];
            if($shiping_status==3)
            {
              $data_array[$row[csf("item_group_id")]][$row[csf("unit_of_measure")]]['total_closed_trnsfr_out_opening_amt']+=$row[csf("cons_amount")];
            }
            $all_store_arr[]=$row[csf("store_id")];
            }
          }
        }

        if(strtotime($trans_date) >= strtotime($from_date) && strtotime($trans_date) <= strtotime($to_date))
        {
          if($row[csf("transaction_type")]==5)
          {
            if($row[csf("item_category")]==4){
            $data_array[$row[csf("item_group_id")]][$row[csf("unit_of_measure")]]['total_dyes_trnsfr_in_amt']+=$row[csf("cons_amount")];
            $data_array[$row[csf("item_group_id")]][$row[csf("unit_of_measure")]]['item_transfer_receive']+=$row[csf("cons_quantity")];
            if($shiping_status==3)
            {
              $data_array[$row[csf("item_group_id")]][$row[csf("unit_of_measure")]]['total_closed_trnsfr_in_amt']+=$row[csf("cons_amount")];
            }
            $all_store_arr[]=$row[csf("store_id")];
            }
          }
          else
          {
            if($row[csf("item_category")]==4){
              $data_array[$row[csf("item_group_id")]][$row[csf("unit_of_measure")]]['total_dyes_trnsfr_out_amt']+=$row[csf("cons_amount")];
              $data_array[$row[csf("item_group_id")]][$row[csf("unit_of_measure")]]['item_transfer_issue']+=$row[csf("cons_quantity")];
              if($shiping_status==3)
              {
                $data_array[$row[csf("item_group_id")]][$row[csf("unit_of_measure")]]['total_closed_trnsfr_out_amt']+=$row[csf("cons_amount")];
              }
              $all_store_arr[]=$row[csf("store_id")];
            }
          }
        }
      }
    }

  //echo "<pre>"; print_r($data_array); //die;  
  unset($result);*/
	$sql_item_group=sql_select("select id, item_name, conversion_factor from lib_item_group where status_active=1 and is_deleted=0");
	foreach($sql_item_group as $val)
	{
		$itemgroupArr[$val[csf("id")]]=$val[csf("item_name")];
		$conversion_factor_arr[$val[csf("id")]]=$val[csf("conversion_factor")];
	}
	unset($sql_item_group);
	//
	if($type==1) 
	{
		$sql_trans = "SELECT m.ID AS JOB_ID, m.BUYER_NAME, n.SHIPING_STATUS, n.GROUPING AS REF_NO, b.ID AS PROD_ID, a.ID as TRANS_ID, a.ITEM_CATEGORY, b.ITEM_GROUP_ID, b.ITEM_DESCRIPTION, b.UNIT_OF_MEASURE, a.STORE_ID, a.ORDER_RATE, a.CONS_RATE, a.CONS_QUANTITY, a.TRANSACTION_DATE, a.CONS_AMOUNT, a.TRANSACTION_TYPE, b.AVG_RATE_PER_UNIT, p.ID as PROPO_ID, p.QUANTITY
		from wo_po_details_master m, wo_po_break_down n, order_wise_pro_details p, inv_transaction a, product_details_master b
		where m.job_no=n.job_no_mst and n.id=p.po_breakdown_id and p.trans_id=a.id and a.prod_id=b.id and a.item_category=4 and b.item_category_id=4 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and a.company_id=$company and a.store_id=$store_buyer_id";
	}
	else
	{
		$sql_trans = "SELECT m.ID AS JOB_ID, m.BUYER_NAME, n.SHIPING_STATUS, n.GROUPING AS REF_NO, b.ID AS PROD_ID, a.ID as TRANS_ID, a.ITEM_CATEGORY, b.ITEM_GROUP_ID, b.ITEM_DESCRIPTION, b.UNIT_OF_MEASURE, a.STORE_ID, a.ORDER_RATE, a.CONS_RATE, a.CONS_QUANTITY, a.TRANSACTION_DATE, a.CONS_AMOUNT, a.TRANSACTION_TYPE, b.AVG_RATE_PER_UNIT, p.ID as PROPO_ID, p.QUANTITY
		from wo_po_details_master m, wo_po_break_down n, order_wise_pro_details p, inv_transaction a, product_details_master b
		where m.job_no=n.job_no_mst and n.id=p.po_breakdown_id and p.trans_id=a.id and a.prod_id=b.id and a.item_category=4 and b.item_category_id=4 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and a.company_id=$company and m.BUYER_NAME=$store_buyer_id";
	}  
  	//echo $sql_trans;
	$sql_trans_result = sql_select($sql_trans);
	$data_array=array();
    foreach($sql_trans_result as $row)
    {
		if($propotion_check[$row["PROPO_ID"]]=="")
		{
			$conversion_factor=$conversion_factor_arr[$row["ITEM_GROUP_ID"]];
			$trans_check[$row["PROPO_ID"]]=$row["PROPO_ID"];
			$trans_date = $row["TRANSACTION_DATE"];
			$cons_qnty=$row["QUANTITY"]*$conversion_factor;
			$cons_amount=$cons_qnty*$row["CONS_RATE"];
			if(strtotime($trans_date) < strtotime($from_date))
			{
				if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
				{
					if($row["SHIPING_STATUS"]==3)
					{
						$data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['rcv_total_opening_amt_close'] =bcadd($data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['rcv_total_opening_amt_close'],$cons_amount,15);
						$data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['rcv_total_opening_close'] =bcadd($data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['rcv_total_opening_close'],$cons_qnty,15);
					}
					$data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['rcv_total_opening_amt'] =bcadd($data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['rcv_total_opening_amt'],$cons_amount,15);
					$data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['rcv_total_opening'] =bcadd($data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['rcv_total_opening'],$cons_qnty,15);
					
				}
				else
				{
					if($row["SHIPING_STATUS"]==3)
					{
						$data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['iss_total_opening_amt_close'] =bcadd($data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['iss_total_opening_amt_close'],$cons_amount,15);
						$data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['iss_total_opening_close'] =bcadd($data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['iss_total_opening_close'],$cons_qnty,15);
					}
					$data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['iss_total_opening_amt'] =bcadd($data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['iss_total_opening_amt'],$cons_amount,15);
					$data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['iss_total_opening'] =bcadd($data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['iss_total_opening'],$cons_qnty,15);
				}
			}
			
			if(strtotime($trans_date) >= strtotime($from_date) && strtotime($trans_date) <= strtotime($to_date))
			{
				if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
				{
					if($row["SHIPING_STATUS"]==3)
					{
						$data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['total_rcv_amt_close'] =bcadd($data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['total_rcv_amt_close'],$cons_amount,15);
						$data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['total_rcv_qnty_close'] =bcadd($data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['total_rcv_qnty_close'],$cons_qnty,15);
					}
					$data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['total_rcv_amt'] =bcadd($data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['total_rcv_amt'],$cons_amount,15);
					$data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['total_rcv_qnty'] =bcadd($data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['total_rcv_qnty'],$cons_qnty,15);
				}
				else
				{
					if($row["SHIPING_STATUS"]==3)
					{
						$data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['total_issue_amt_close'] =bcadd($data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['total_issue_amt_close'],$cons_amount,15);
						$data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['total_issue_qnty_close'] =bcadd($data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['total_issue_qnty_close'],$cons_qnty,15);
					}
					$data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['total_issue_amt'] =bcadd($data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['total_issue_amt'],$cons_amount,15);
					$data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['total_issue_qnty'] =bcadd($data_array[$row["ITEM_GROUP_ID"]][$row["UNIT_OF_MEASURE"]]['total_issue_qnty'],$cons_qnty,15);
				}
			}
		}
    }
	//echo "<pre>";print_r($data_array); //die;
	$storeArr = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0","id","store_name");
	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	
	?>
	<div align="center" style="width:1030px;">
		<style type="text/css">
        .rpt_table tr TD {font-size: 13px; }
        .rpt_table tr TD a {font-size: 13px; display: block;}
        </style>
        <center><h3>Location :<? echo $storeArr[$store_id];  ?> </h3></center>
        <center><h3>Date : <? echo change_date_format($from_date);?> To <? echo change_date_format($to_date);?></h3></center>
        <table border="1" rules="all" class="rpt_table" width="1030" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th width="150">Item Group</th>
                    <th width="100">UOM</th>
                    <th width="100">Opening Quantity</th>
                    <th width="100">Receive Quantity</th>
                    <th width="100">Delivery Quantity</th>
                    <th width="100">Closing Quantity</th>
                    <th width="100">Closing Value</th>
                    <th width="100">Avg. Rate</th>
                    <th>Closed Order Value</th>
                </tr>
            </thead>
        </table>
        <div style="width:1030px; max-height:250px; overflow-y:auto" id="scroll_body">
        <table border="1" rules="all" class="rpt_table" width="1010" cellpadding="0" cellspacing="0">
            <tbody>
            <?
            $total_dyes_opening_value = 0;
            $total_dyes_rcv_value     = 0;
            $total_dyes_delivery_value  = 0;
            $total_dyes_closing_value = 0;
            $i=0;
            $conversion_date=date("Y/m/d");
            $curr_exchange_rate=set_conversion_rate( 2, $conversion_date,$company );
			
            foreach($data_array  as $item_group_id=> $item_group_data)
            {
				foreach($item_group_data as $uom=> $row)
				{
					$total_opening_value=bcsub($row["rcv_total_opening_amt"],$row["iss_total_opening_amt"],15);
					$total_rcv_amt=$row["total_rcv_amt"];
					$total_issue_amt=$row["total_issue_amt"];
					$total_closing_value=bcsub(bcadd($total_opening_value,$total_rcv_amt,15),$total_issue_amt,15);
					
					
					$total_opening_qnty=bcsub($row["rcv_total_opening"],$row["iss_total_opening"],15);
					$total_rcv_qnty=$row["total_rcv_qnty"];
					$total_issue_qnty=$row["total_issue_qnty"];
					$total_closing_qnty=bcsub(bcadd($total_opening_qnty,$total_rcv_qnty,15),$total_issue_qnty,15);
					
					$total_opening_value_close=bcsub($row["rcv_total_opening_amt_close"],$row["iss_total_opening_amt_close"],15);
					$total_rcv_amt_close=$row["total_rcv_amt_close"];
					$total_issue_amt_close=$row["total_issue_amt_close"];
					$total_closing_value_close=bcsub(bcadd($total_opening_value_close,$total_rcv_amt_close,15),$total_issue_amt_close,15);
					//|| $total_closing_value>=1 || $total_closing_value_close>=1 
					if($total_opening_qnty>=1 || $total_rcv_qnty>=1 || $total_issue_qnty>=1 || $total_closing_qnty>=1 )
					{
						if($total_closing_value>0 && $total_closing_qnty > 0)
						{
							$dyes_closing_avg_rate=bcdiv($total_closing_value,$total_closing_qnty,15);
						}
						else
						{
							$dyes_closing_avg_rate=0;
						}
						
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;        
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trl_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trl_<? echo $i; ?>">
                            <td width="150" title="<? echo $item_group_id;?>"><a href="javascript:void()" onclick="open_acc_store_popup('<? echo $company."_".$from_date."_".$to_date."_".$exchange_rate."_".$store_buyer_id."_".$item_group_id."_".$type;?>','open_acc_location_item_details_popup')">
                            <? echo $itemgroupArr[$item_group_id];?>
                            </a>
                            </td>
                            <td width="100" align="center"><p><? echo $unit_of_measurement[$uom]; ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($total_opening_qnty,0) ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($total_rcv_qnty,0) ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($total_issue_qnty,0) ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($total_closing_qnty,0) ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($total_closing_value,0) ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($dyes_closing_avg_rate,4) ?></p></td>
                            <td align="right"><p><? echo number_format($total_closing_value_close,0) ?></p></td>
						</tr>
						<?
						$i++;
						$total_dyes_opening_qty		=bcadd($total_dyes_opening_qty,$total_opening_qnty,15);
						$total_dyes_rcv_qty			=bcadd($total_dyes_rcv_qty,$total_rcv_qnty,15);
						$total_dyes_issue_qty		=bcadd($total_dyes_issue_qty,$total_issue_qnty,15);
						$total_dyes_closing_qty		=bcadd($total_dyes_closing_qty,$total_closing_qnty,15);
						$total_dyes_closing_value	=bcadd($total_dyes_closing_value,$total_closing_value,15);
						$total_closed_value 		=bcadd($total_closed_value,$total_closing_value_close,15);
					}
				}
            }
            ?>
            </tbody>
        </table>
        </div>
        <table border="1" rules="all" class="rpt_table" width="1030" cellpadding="0" cellspacing="0">
        <tr bgcolor="#339fff">
        <td width="223" align="right"><strong>Total:</strong></td>
        <td width="100" align="right"><p><strong><? echo number_format($total_dyes_opening_qty,0) ?></strong></p></td>
        <td width="100" align="right"><p><strong><? echo number_format($total_dyes_rcv_qty,0) ?></strong></p></td>
        <td width="100" align="right"><p><strong><? echo number_format($total_dyes_issue_qty,0) ?></strong></p></td>
        <td width="100" align="right"><p><strong><? echo number_format($total_dyes_closing_qty,0) ?></strong></p></td>
        <td width="100" align="right"><p><strong><? echo number_format($total_dyes_closing_value,0) ?></strong></p></td>
        <td align="right"><p><strong><? echo number_format($total_closed_value,0) ?></strong></p></td>
        </tr>
        </table>
	</div>    
    <script type="text/javascript">
        function open_acc_store_popup(data,action,type)
        {
            width=950;      
            emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'closing_stock_report_controller.php?data=' + data + '&action=' + action, 'Location Wise Details', 'width='+width+'px,height=280px,center=1,resize=0,scrolling=0', '../../../');
        }
    </script>

  <?
}

if($action=="open_acc_location_item_details_popup")
{
	echo load_html_head_contents("Accessories Item Details Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$data_ex    = explode("_", $data);
	$company    = $data_ex[0];
	$from_date  = $data_ex[1];
	$to_date    = $data_ex[2];
	$exchange_rate  = $data_ex[3];
	$store_buyer_id = $data_ex[4];
	$item_group_id  = $data_ex[5];
	$type       = $data_ex[6];
	
	$conversion_date      =date("Y/m/d");
	$curr_exchange_rate   =set_conversion_rate( 2, $conversion_date,$company );
	$itemgroupArr         = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
	if($db_type==0)
	{
	$from_date=change_date_format($from_date,'yyyy-mm-dd');
	$to_date=change_date_format($to_date,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
	$from_date=change_date_format($from_date,'','',1);
	$to_date=change_date_format($to_date,'','',1);
	}
	else
	{
	$from_date=""; $to_date="";
	}

  /*$po_sql ="Select a.buyer_name, b.id, b.shiping_status, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
  $po_sql_res=sql_select($po_sql);
  foreach ($po_sql_res as $row)
  {
    $po_arr[$row[csf("id")]]['buyer_name']    =$row[csf("buyer_name")];
    $po_arr[$row[csf("id")]]['shiping_status']=$row[csf("shiping_status")];
    $po_arr[$row[csf("id")]]['ref_no']        =$row[csf("grouping")];
  }
  unset($po_sql_res);
  $itemGroup_cond=" and b.item_group_id=$item_group_id ";
  if($type==1)
  {
    $store_id=$store_buyer_id;
    $store_cond=" and a.store_id=$store_id  ";
  }
  
  
  $sql_receive = "SELECT b.id as prod_id,a.item_category,b.item_group_id, b.item_description,b.unit_of_measure, a.store_id, a.order_rate,c.currency_id,a.cons_rate,a.cons_quantity,a.transaction_date ,a.item_category,a.cons_amount, a.transaction_type,d.po_breakdown_id from inv_transaction a, product_details_master b ,inv_receive_master c,  order_wise_pro_details d  where a.mst_id=c.id and a.id=d.trans_id and b.id=d.prod_id and a.transaction_type in (1,4) and a.item_category in (4)  and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company $store_cond $itemGroup_cond and a.prod_id=b.id and a.store_id <> 0 and a.company_id=b.company_id ";


  //and a.transaction_date between '$from_date' and '$to_date'
  $receive_res = sql_select($sql_receive);  $all_store_arr=array();   
  //$total_rcv_opening_amt = 0; $total_rcv_amt = 0; $total_dyes_rcv_opening_amt = 0; $total_dyes_rcv_amt = 0; $total_accessories_rcv_opening_amt = 0; $total_accessories_rcv_amt = 0;

  foreach($receive_res as $row)
  {
    $trans_date     = $row[csf("transaction_date")];
    $shiping_status =$po_arr[$row[csf("po_breakdown_id")]]['shiping_status'];
    $buyer_name     =$po_arr[$row[csf("po_breakdown_id")]]['buyer_name'];
    $ref_no         =$po_arr[$row[csf("po_breakdown_id")]]['ref_no'];
    if($shiping_status!=3) $shiping_status=1; else $shiping_status=$shiping_status; 

    if(strtotime($trans_date) < strtotime($from_date))
    {
      if($row[csf("currency_id")]==2 && $row[csf("transaction_type")]==1)//usd
      {
        if($row[csf("item_category")]==4 ){
          
          $data_array[$shiping_status][$buyer_name][$ref_no]['rcv_total_opening_amt']   +=$row[csf("cons_amount")];
          $data_array[$shiping_status][$buyer_name][$ref_no]['rcv_total_opening']       +=$row[csf("cons_quantity")];
          //echo $shiping_status.'**'.$buyer_name.'**'.$ref_no.'==';
        }
      }
      else
      {
        if($row[csf("item_category")]==4){
          $data_array[$shiping_status][$buyer_name][$ref_no]['rcv_total_opening_amt']    +=$row[csf("cons_amount")];
          $data_array[$shiping_status][$buyer_name][$ref_no]['rcv_total_opening']        +=$row[csf("cons_quantity")];
        }
      }
    } 
    if(strtotime($trans_date) >= strtotime($from_date) && strtotime($trans_date) <= strtotime($to_date))
    {
      if($row[csf("currency_id")]==2  && $row[csf("transaction_type")]==1)//usd
      {
        $rate = $row[csf("order_rate")]*$exchange_rate;
        if($row[csf("item_category")]==4){
          $data_array[$shiping_status][$buyer_name][$ref_no]['total_dyes_rcv_amt']      +=$row[csf("cons_amount")];
          $data_array[$shiping_status][$buyer_name][$ref_no]['purchase']                +=$row[csf("cons_quantity")];
          
        }
      }
      else
      {
        if($row[csf("item_category")]==4){
          $data_array[$shiping_status][$buyer_name][$ref_no]['total_dyes_rcv_amt']      +=$row[csf("cons_amount")];
          $data_array[$shiping_status][$buyer_name][$ref_no]['purchase']                +=$row[csf("cons_quantity")];
        }
      }
    }
    
  }
  //echo "<pre>"; print_r($data_array);
  //==========================================================================================/
  /                   Issue                         /
  //==========================================================================================
  $sql_dyes_issue = "select b.id as prod_id,a.item_category,b.item_group_id, b.item_description,b.unit_of_measure, a.store_id, a.order_rate,a.cons_rate,a.cons_quantity,a.transaction_date ,a.item_category,a.cons_amount, a.transaction_type,d.po_breakdown_id from inv_transaction a,product_details_master b, order_wise_pro_details d where a.transaction_type in (2,3) and a.item_category in (4) and a.status_active=1 and a.is_deleted=0 and a.company_id=$company $store_cond $itemGroup_cond and a.prod_id=b.id and a.id=d.trans_id and a.store_id <> 0 and a.company_id=b.company_id "; 
  // echo $sql_issue;
  $issue_dyes_res = sql_select($sql_dyes_issue); 
  foreach ($issue_dyes_res as $row)
  {
    $trans_date = $row[csf("transaction_date")];
    $shiping_status=$po_arr[$row[csf("po_breakdown_id")]]['shiping_status'];
    $buyer_name=$po_arr[$row[csf("po_breakdown_id")]]['buyer_name'];
    $ref_no=$po_arr[$row[csf("po_breakdown_id")]]['ref_no'];
    
    if($shiping_status!=3) $shiping_status=1; else $shiping_status=$shiping_status;
    if(strtotime($trans_date) < strtotime($from_date))
    {
      if($row[csf("item_category")]==4 ){
        $data_array[$shiping_status][$buyer_name][$ref_no]['iss_total_opening_amt']   +=$row[csf("cons_amount")];
        $data_array[$shiping_status][$buyer_name][$ref_no]['iss_total_opening']       +=$row[csf("cons_quantity")];
      }
    }

    if(strtotime($trans_date) >= strtotime($from_date) && strtotime($trans_date) <= strtotime($to_date))
    {
      //echo "cdsfbfkib";
      if($row[csf("item_category")]==4 ){
        $data_array[$shiping_status][$buyer_name][$ref_no]['total_dyes_issue_amt']    +=$row[csf("cons_amount")];
        $data_array[$shiping_status][$buyer_name][$ref_no]['issue']                   +=$row[csf("cons_quantity")];
      }
    }
    
  }

  // echo $total_issue_opening_amt;
  //==========================================================================================/
  //                   Transfer                      /
  //==========================================================================================
 
  $sql_transfer = "select b.id as prod_id,a.item_category,b.item_group_id, b.item_description,b.unit_of_measure, a.store_id, a.order_rate,a.cons_rate,a.cons_quantity,a.transaction_date ,a.item_category,a.cons_amount, a.transaction_type,d.po_breakdown_id from inv_transaction a, inv_item_transfer_mst c, product_details_master b, order_wise_pro_details d where a.mst_id=c.id and a.transaction_type in (5,6) and a.item_category in (4) and a.status_active=1 and a.is_deleted=0 and c.status_active=1  and c.is_deleted=0 and a.company_id=$company $store_cond $itemGroup_cond and a.prod_id=b.id and a.id=d.trans_id and a.store_id <> 0 and a.company_id=b.company_id"; 

  // echo $sql_transfer;
  $transfer_res = sql_select($sql_transfer);    
  
  $total_trnsfr_in_opening_amt= 0; $total_trnsfr_out_opening_amt= 0; $total_trnsfr_in_amt= 0; $total_trnsfr_out_amt= 0;
  $total_accessories_trnsfr_in_opening_amt=0; $total_accessories_trnsfr_out_opening_amt= 0; $total_accessories_trnsfr_in_amt= 0; $total_accessories_trnsfr_out_amt= 0;
  $total_dyes_trnsfr_in_opening_amt= 0; $total_dyes_trnsfr_out_opening_amt= 0; $total_dyes_trnsfr_in_amt= 0; $total_dyes_trnsfr_out_amt= 0;
  foreach ($transfer_res as $row)
  {
    $trans_date = $row[csf("transaction_date")];
    $shiping_status=$po_arr[$row[csf("po_breakdown_id")]]['shiping_status'];
    $buyer_name=$po_arr[$row[csf("po_breakdown_id")]]['buyer_name'];
    $ref_no=$po_arr[$row[csf("po_breakdown_id")]]['ref_no'];
    
    if($shiping_status!=3) $shiping_status=1; else $shiping_status=$shiping_status;
    if(strtotime($trans_date) < strtotime($from_date))
    {
      //echo $exchange_rate.'==';
      if($row[csf("transaction_type")]==5)
      {
        if($row[csf("item_category")]==4 ){
          $data_array[$shiping_status][$buyer_name][$ref_no]['total_dyes_trnsfr_in_opening_amt']  +=$row[csf("cons_amount")];
          $data_array[$shiping_status][$buyer_name][$ref_no]['item_opening_transfer_receive']     +=$row[csf("cons_quantity")];
        }
      }
      else
      {
        if($row[csf("item_category")]==4 ){
        $data_array[$shiping_status][$buyer_name][$ref_no]['total_dyes_trnsfr_out_opening_amt']   +=$row[csf("cons_amount")];
        $data_array[$shiping_status][$buyer_name][$ref_no]['item_opening_transfer_issue']         +=$row[csf("cons_quantity")];
        }
      }
    }

    if(strtotime($trans_date) >= strtotime($from_date) && strtotime($trans_date) <= strtotime($to_date))
    {
      if($row[csf("transaction_type")]==5)
      {
        //echo $row[csf("cons_amount")].'_________';
        if($row[csf("item_category")]==4 ){
          $data_array[$shiping_status][$buyer_name][$ref_no]['total_dyes_trnsfr_in_amt']    +=$row[csf("cons_amount")];
          $data_array[$shiping_status][$buyer_name][$ref_no]['item_transfer_receive']       +=$row[csf("cons_quantity")];
        }
      }
      else
      {
        if($row[csf("item_category")]==4 ){
          $data_array[$shiping_status][$buyer_name][$ref_no]['total_dyes_trnsfr_out_amt']    +=$row[csf("cons_amount")];
          $data_array[$shiping_status][$buyer_name][$ref_no]['item_transfer_issue']          +=$row[csf("cons_quantity")];
        }
      }
    }
  }
  //echo "<pre>"; print_r($data_array); 
  $shiping_status_arr=array(); $byuer_name_arr=array(); 
 
  foreach($data_array as $shiping_status_id=> $shiping_status_data)
  {
    $shiping_status_rowspan=0;
    foreach($shiping_status_data as $buyer_name_id=> $buyer_name_data)
    {
      $buyer_name_rowspan=1;
      foreach($buyer_name_data as $ref_no=> $row)
      {
        $purchase_qnty      =$row['purchase']-$loan_data[$prod_id]["loan_rcv_qnty"];
        $loan_receive       =$loan_data[$prod_id]["loan_rcv_qnty"];
        $issue_return       =$row['issue_return'];
        $issue_qnty         =$row['issue']-$loan_data[$prod_id]["loan_issue_qnty"];
        $loan_issue         =$loan_data[$prod_id]["loan_issue_qnty"];
        $receive_return     =$row['receive_return'];
        $transfer_out_qty   =$row['item_transfer_issue'];
        $transfer_in_qty    =$row['item_transfer_receive'];
        $dyes_opening_qty       = ($row['rcv_total_opening'] + $row['item_opening_transfer_receive']) -($row['iss_total_opening']+$row['item_opening_transfer_issue']);
        $dyes_rcv_qty           = $purchase_qnty+$issue_return+$transfer_in_qty;
        $dyes_issue_qty         = $issue_qnty+$loan_issue+$receive_return+$transfer_out_qty;
        $dyes_closing_qty       = ($dyes_opening_qty+$dyes_rcv_qty)-$dyes_issue_qty;
        $rcv_total_opening_amt  =$row['rcv_total_opening_amt'];
        $total_dyes_rcv_amt     =$row['total_dyes_rcv_amt'];

        $dyes_opening_value     = $rcv_total_opening_amt-$row['iss_total_opening_amt'];
        $dyes_closing_val       = ($dyes_opening_value+$total_dyes_rcv_amt)-$row['issue_amount'];
        $dyes_closing_avg_rate  = $dyes_closing_val/$dyes_closing_qty;
        
        $total_dyes_trnsfr_in_opening_amt =$row['total_dyes_trnsfr_in_opening_amt'];
        $iss_total_opening_amt            =$row['iss_total_opening_amt'];
        $total_dyes_trnsfr_out_opening_amt=$row['total_dyes_trnsfr_out_opening_amt'];
        $total_dyes_trnsfr_in_amt         =$row['total_dyes_trnsfr_in_amt'];
        $total_dyes_issue_amt             =$row['total_dyes_issue_amt'];
        $total_dyes_trnsfr_out_amt        =$row['total_dyes_trnsfr_out_amt'];

        $opening_value_dyes     = ($rcv_total_opening_amt + $total_dyes_trnsfr_in_opening_amt) - ($iss_total_opening_amt + $total_dyes_trnsfr_out_opening_amt);
        $receive_value_dyes     =  $total_dyes_rcv_amt + $total_dyes_trnsfr_in_amt;
        $delivery_value_dyes    =  $total_dyes_issue_amt + $total_dyes_trnsfr_out_amt;
        $closing_value_dyes     = ($opening_value_dyes + $receive_value_dyes) - $delivery_value_dyes;
        if($dyes_opening_qty>0 || $dyes_rcv_qty>0 || $dyes_issue_qty>0 || $dyes_closing_qty>0 || $closing_value_dyes>0 ){
          $shiping_status_rowspan++;
          $buyer_name_rowspan++;
        }
      }
      $shiping_status_rowspan++;
      $byuer_name_arr[$shiping_status_id][$buyer_name_id]=$buyer_name_rowspan;
    }
    $shiping_status_arr[$shiping_status_id]=$shiping_status_rowspan;
  }*/
  
	//echo "<pre>"; print_r($shiping_status_arr);
	
	$conversion_factor=return_field_value("conversion_factor","lib_item_group","id=$item_group_id","conversion_factor");
	
	if($type==1)
	{
		$sql_trans = "SELECT m.ID AS JOB_ID, m.BUYER_NAME, n.SHIPING_STATUS, n.GROUPING AS REF_NO, b.ID AS PROD_ID, a.ID as TRANS_ID, a.ITEM_CATEGORY, b.ITEM_GROUP_ID, b.ITEM_DESCRIPTION, b.UNIT_OF_MEASURE, a.STORE_ID, a.ORDER_RATE, a.CONS_RATE, a.CONS_QUANTITY, a.TRANSACTION_DATE, a.ITEM_CATEGORY, a.CONS_AMOUNT, a.TRANSACTION_TYPE, b.AVG_RATE_PER_UNIT, b.ITEM_GROUP_ID, p.ID as PROPO_ID, p.QUANTITY
		from wo_po_details_master m, wo_po_break_down n, order_wise_pro_details p, inv_transaction a, product_details_master b
		where m.job_no=n.job_no_mst and n.id=p.po_breakdown_id and p.trans_id=a.id and a.prod_id=b.id and a.item_category=4 and b.item_category_id=4 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and a.company_id=$company and a.store_id=$store_buyer_id and b.ITEM_GROUP_ID=$item_group_id";
	}
	else
	{
		$sql_trans = "SELECT m.ID AS JOB_ID, m.BUYER_NAME, n.SHIPING_STATUS, n.GROUPING AS REF_NO, b.ID AS PROD_ID, a.ID as TRANS_ID, a.ITEM_CATEGORY, b.ITEM_GROUP_ID, b.ITEM_DESCRIPTION, b.UNIT_OF_MEASURE, a.STORE_ID, a.ORDER_RATE, a.CONS_RATE, a.CONS_QUANTITY, a.TRANSACTION_DATE, a.ITEM_CATEGORY, a.CONS_AMOUNT, a.TRANSACTION_TYPE, b.AVG_RATE_PER_UNIT, b.ITEM_GROUP_ID, p.ID as PROPO_ID, p.QUANTITY
		from wo_po_details_master m, wo_po_break_down n, order_wise_pro_details p, inv_transaction a, product_details_master b
		where m.job_no=n.job_no_mst and n.id=p.po_breakdown_id and p.trans_id=a.id and a.prod_id=b.id and a.item_category=4 and b.item_category_id=4 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and a.company_id=$company and m.BUYER_NAME=$store_buyer_id and b.ITEM_GROUP_ID=$item_group_id";
	}
    //echo $sql_trans;
    $sql_trans_result = sql_select($sql_trans);
	$data_array=array();
	$propotion_check=array();
    foreach($sql_trans_result as $row)
    {
		if($propotion_check[$row["PROPO_ID"]]=="")
		{
			$propotion_check[$row["PROPO_ID"]]=$row["PROPO_ID"];
			$trans_date = $row["TRANSACTION_DATE"];
			$cons_qnty=$row["QUANTITY"]*$conversion_factor;
			$cons_amount=$cons_qnty*$row["CONS_RATE"];
			if(strtotime($trans_date) < strtotime($from_date))
			{
				if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
				{
					if($row["SHIPING_STATUS"]==3)
					{
						$data_array["Closed"][$row["BUYER_NAME"]][$row["REF_NO"]]['rcv_total_opening_amt'] =bcadd($data_array["Closed"][$row["BUYER_NAME"]][$row["REF_NO"]]['rcv_total_opening_amt'],$cons_amount,15);
						$data_array["Closed"][$row["BUYER_NAME"]][$row["REF_NO"]]['rcv_total_opening'] =bcadd($data_array["Closed"][$row["BUYER_NAME"]][$row["REF_NO"]]['rcv_total_opening'],$cons_qnty,15);
					}
					else
					{
						$data_array["Open"][$row["BUYER_NAME"]][$row["REF_NO"]]['rcv_total_opening_amt'] =bcadd($data_array["Open"][$row["BUYER_NAME"]][$row["REF_NO"]]['rcv_total_opening_amt'],$cons_amount,15);
						$data_array["Open"][$row["BUYER_NAME"]][$row["REF_NO"]]['rcv_total_opening'] =bcadd($data_array["Open"][$row["BUYER_NAME"]][$row["REF_NO"]]['rcv_total_opening'],$cons_qnty,15);
					}
					
				}
				else
				{
					if($row["SHIPING_STATUS"]==3)
					{
						$data_array["Closed"][$row["BUYER_NAME"]][$row["REF_NO"]]['iss_total_opening_amt'] =bcadd($data_array["Closed"][$row["BUYER_NAME"]][$row["REF_NO"]]['iss_total_opening_amt'],$cons_amount,15);
						$data_array["Closed"][$row["BUYER_NAME"]][$row["REF_NO"]]['iss_total_opening'] =bcadd($data_array["Closed"][$row["BUYER_NAME"]][$row["REF_NO"]]['iss_total_opening'],$cons_qnty,15);
					}
					else
					{
						$data_array["Open"][$row["BUYER_NAME"]][$row["REF_NO"]]['iss_total_opening_amt'] =bcadd($data_array["Open"][$row["BUYER_NAME"]][$row["REF_NO"]]['iss_total_opening_amt'],$cons_amount,15);
						$data_array["Open"][$row["BUYER_NAME"]][$row["REF_NO"]]['iss_total_opening'] =bcadd($data_array["Open"][$row["BUYER_NAME"]][$row["REF_NO"]]['iss_total_opening'],$cons_qnty,15);
					}
				}
			}
			
			if(strtotime($trans_date) >= strtotime($from_date) && strtotime($trans_date) <= strtotime($to_date))
			{
				if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
				{
					if($row["SHIPING_STATUS"]==3)
					{
						$data_array["Closed"][$row["BUYER_NAME"]][$row["REF_NO"]]['total_rcv_amt'] =bcadd($data_array["Closed"][$row["BUYER_NAME"]][$row["REF_NO"]]['total_rcv_amt'],$cons_amount,15);
						$data_array["Closed"][$row["BUYER_NAME"]][$row["REF_NO"]]['total_rcv_qnty'] =bcadd($data_array["Closed"][$row["BUYER_NAME"]][$row["REF_NO"]]['total_rcv_qnty'],$cons_qnty,15);
					}
					else
					{
						$data_array["Open"][$row["BUYER_NAME"]][$row["REF_NO"]]['total_rcv_amt'] =bcadd($data_array["Open"][$row["BUYER_NAME"]][$row["REF_NO"]]['total_rcv_amt'],$cons_amount,15);
						$data_array["Open"][$row["BUYER_NAME"]][$row["REF_NO"]]['total_rcv_qnty'] =bcadd($data_array["Open"][$row["BUYER_NAME"]][$row["REF_NO"]]['total_rcv_qnty'],$cons_qnty,15);
					}
				}
				else
				{
					if($row["SHIPING_STATUS"]==3)
					{
						$data_array["Closed"][$row["BUYER_NAME"]][$row["REF_NO"]]['total_issue_amt'] =bcadd($data_array["Closed"][$row["BUYER_NAME"]][$row["REF_NO"]]['total_issue_amt'],$cons_amount,15);
						$data_array["Closed"][$row["BUYER_NAME"]][$row["REF_NO"]]['total_issue_qnty'] =bcadd($data_array["Closed"][$row["BUYER_NAME"]][$row["REF_NO"]]['total_issue_qnty'],$cons_qnty,15);
					}
					else
					{
						$data_array["Open"][$row["BUYER_NAME"]][$row["REF_NO"]]['total_issue_amt'] =bcadd($data_array["Open"][$row["BUYER_NAME"]][$row["REF_NO"]]['total_issue_amt'],$cons_amount,15);
						$data_array["Open"][$row["BUYER_NAME"]][$row["REF_NO"]]['total_issue_qnty'] =bcadd($data_array["Open"][$row["BUYER_NAME"]][$row["REF_NO"]]['total_issue_qnty'],$cons_qnty,15);
					}
				}
			}
		}
    }		  
	
	//echo "<pre>";print_r($data_array);die;
	
	
	foreach($data_array as $shiping_status_id=> $shiping_status_data)
	{
		$shiping_status_rowspan=0;
		foreach($shiping_status_data as $buyer_name_id=> $buyer_name_data)
		{
			$buyer_name_rowspan=0;
			foreach($buyer_name_data as $ref_no=> $row)
			{
				$total_opening_value=bcsub($row["rcv_total_opening_amt"],$row["iss_total_opening_amt"],15);
				$total_rcv_amt=$row["total_rcv_amt"];
				$total_issue_amt=$row["total_issue_amt"];
				$total_closing_value=bcsub(bcadd($total_opening_value,$total_rcv_amt,15),$total_issue_amt,15);
				
				$total_opening_qnty=bcsub($row["rcv_total_opening"],$row["iss_total_opening"],15);
				$total_rcv_qnty=$row["total_rcv_qnty"];
				$total_issue_qnty=$row["total_issue_qnty"];
				$total_closing_qnty=bcsub(bcadd($total_opening_qnty,$total_rcv_qnty,15),$total_issue_qnty,15);
				//|| $total_closing_value>=1 
				if(number_format($total_opening_qnty,2) !=0 || number_format($total_rcv_qnty,2) !=0 || number_format($total_issue_qnty,2) !=0 || number_format($total_closing_qnty,2) !=0 )
				{
					$shiping_status_rowspan++;
					$buyer_name_rowspan++;
				}
			}
			if($buyer_name_rowspan>0)
			{
				$shiping_status_rowspan++;
				$byuer_name_arr[$shiping_status_id][$buyer_name_id]=$buyer_name_rowspan;
			}
		}
		$shiping_status_arr[$shiping_status_id]=$shiping_status_rowspan;
	}
	
	//echo "<pre>";print_r($shiping_status_arr);
	//echo "<pre>";print_r($byuer_name_arr);die;	  

	$storeArr = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0","id","store_name");
	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	//echo "<pre>";
	//print_r($rowspan_loc_arr); die;
	?>
	<div style="width:940px;">
		<style type="text/css">
        .rpt_table tr TD {font-size: 13px; }
        .rpt_table tr TD a {font-size: 13px; display: block;}
        </style>
        <center><h3>Item Name :<? echo $itemgroupArr[$item_group_id];  ?> </h3></center>
        <center><h3>Date : <? echo change_date_format($from_date);?> To <? echo change_date_format($to_date);?></h3></center>
        <table border="1" rules="all" class="rpt_table" width="940" cellpadding="0" cellspacing="0">
            <thead>
                <tr>          
                    <th width="80">Order Status</th> 
                    <th width="150">Buyer</th>
                    <th width="100">Ref No</th>
                    <th width="100">Opening Qty</th>
                    <th width="100">Receive Qty</th>
                    <th width="100">Delivery Qty</th>
                    <th width="100">Closing Qty</th>
                    <th width="100">Closing Value</th>
                    <th>Avg. Rate</th>
                </tr>
            </thead>
        </table>
        <div style="width:940px; max-height:150px; overflow-y:auto" id="scroll_body">
        <table border="1" rules="all" class="rpt_table" width="920" cellpadding="0" cellspacing="0">
            <tbody>
            <?
            $r=$i=1;
            $shiping_arr = array(1 => "Open", 3 => "Closed");
            $grnd_opening_qty=$grnd_rcv_qty=$grnd_issue_qty=$grnd_closing_qty=$grnd_closing_val=$grand_last_rcv_qty = 0; 
            foreach($data_array as $shiping_status_id=> $shiping_status_data)
            { 
				$shiping_status_rowspan=0;
				foreach($shiping_status_data as $buyer_name_id=> $buyer_name_data)
				{
					$buyer_name_rowspan=0;
					$total_dyes_opening_qty=$total_dyes_rcv_qty=$total_dyes_issue_qty=$total_dyes_closing_qty=$total_dyes_closing_val=$total_last_rcv_qty= 0;
					foreach($buyer_name_data as $ref_no=> $row)
					{
						$total_opening_value=bcsub($row["rcv_total_opening_amt"],$row["iss_total_opening_amt"],15);
						$total_rcv_amt=$row["total_rcv_amt"];
						$total_issue_amt=$row["total_issue_amt"];
						$total_closing_value=bcsub(bcadd($total_opening_value,$total_rcv_amt,15),$total_issue_amt,15);
						
						$total_opening_qnty=bcsub($row["rcv_total_opening"],$row["iss_total_opening"],15);
						$total_rcv_qnty=$row["total_rcv_qnty"];
						$total_issue_qnty=$row["total_issue_qnty"];
						$total_closing_qnty=bcsub(bcadd($total_opening_qnty,$total_rcv_qnty,15),$total_issue_qnty,15);
						//|| $total_closing_value>=1 
						
						if(number_format($total_opening_qnty,2) !=0 || number_format($total_rcv_qnty,2)!=0 || number_format($total_issue_qnty,2)!=0 || number_format($total_closing_qnty,2)!=0 )
						{
							$dyes_closing_avg_rate=0;
							if($total_closing_value>0 && $total_closing_qnty>0)
							{
								$dyes_closing_avg_rate=bcdiv($total_closing_value,$total_closing_qnty,15);
							}
							$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;        
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">
								<? 
								if($shiping_status_rowspan==0){?>
                                <td width="80" valign="middle" rowspan="<? echo $shiping_status_arr[$shiping_status_id];?>" align="left"><p><? echo $shiping_status_id;?></p></td>
                                <? } 
								
								if($buyer_name_rowspan==0){?>
                                <td width="150" valign="middle" rowspan="<? echo $byuer_name_arr[$shiping_status_id][$buyer_name_id];?>" align="left"><p><? echo $buyer_arr[$buyer_name_id];?></p></td>
                                <? } 
								?>
                               
                                <td width="100"><p><? echo $ref_no;?></td>
                                <td width="100"  align="right"><? echo number_format($total_opening_qnty,2); ?></td>
                                <td width="100" align="right"><? echo number_format($total_rcv_qnty,2); ?></td>
                                <td width="100" align="right"><? echo number_format($total_issue_qnty,2); ?></td>
                                <td width="100" align="right"><? echo number_format($total_closing_qnty,2); ?></td>
                                <td width="100" align="right"><? echo number_format($total_closing_value,2); ?></td>
                                <td align="right"><? echo number_format($dyes_closing_avg_rate,4); ?></td>
							</tr>
							<?
							$r++; $i++; $shiping_status_rowspan++; $buyer_name_rowspan++;
							
							$total_dyes_opening_qty   =bcadd($total_dyes_opening_qty,$total_opening_qnty,15);
							$total_dyes_rcv_qty       =bcadd($total_dyes_rcv_qty,$total_rcv_qnty,15);
							$total_dyes_issue_qty     =bcadd($total_dyes_issue_qty,$total_issue_qnty,15);
							$total_dyes_closing_qty   =bcadd($total_dyes_closing_qty,$total_closing_qnty,15);
							$total_dyes_closing_val   =bcadd($total_dyes_closing_val,$total_closing_value,15);
						}
					}
					$grnd_opening_qty   =bcadd($grnd_opening_qty,$total_dyes_opening_qty,15);
					$grnd_rcv_qty       =bcadd($grnd_rcv_qty,$total_dyes_rcv_qty,15);
					$grnd_issue_qty     =bcadd($grnd_issue_qty,$total_dyes_issue_qty,15);
					$grnd_closing_qty   =bcadd($grnd_closing_qty,$total_dyes_closing_qty,15);
					$grnd_closing_val   =bcadd($grnd_closing_val,$total_dyes_closing_val,15);
					
					if($buyer_name_rowspan>0)
					{
						?>
						<tr bgcolor="#C1C1C1" style="font-weight:bold;text-align:right;">
                            <td colspan="2"> Buyer Total:</td>
							<td align="right"><p><? echo number_format($total_dyes_opening_qty,2); ?></p></td>
							<td align="right"><p><? echo number_format($total_dyes_rcv_qty,2); ?></p></td>
							<td align="right"><p><? echo number_format($total_dyes_issue_qty,2); ?></p></td>
							<td align="right"><p><? echo number_format($total_dyes_closing_qty,2); ?></p></td>
							<td align="right"><p><? echo number_format($total_dyes_closing_val,2); ?></p></td>
							<td></td>
						</tr>
						<?
					}
				}
            }
            ?>
            </tbody>
        </table>
        </div>
        <table border="1" rules="all" class="rpt_table" width="940" cellpadding="0" cellspacing="0">
            <tr bgcolor="#339fff">
                <td width="333" align="right"><strong>Grand Total:</strong></td>
                <td width="100" align="right"><p><strong><? echo number_format($grnd_opening_qty,2); ?></strong></p></td>
                <td width="100" align="right"><p><strong><? echo number_format($grnd_rcv_qty,2); ?></strong></p></td>
                <td width="100" align="right"><p><strong><? echo number_format($grnd_issue_qty,2); ?></strong></p></td>
                <td width="100" align="right"><p><strong><? echo number_format($grnd_closing_qty,2); ?></strong></p></td>
                <td width="100" align="right"><p><strong><? echo number_format($grnd_closing_val,2); ?></strong></p></td>
                <td >&nbsp;</td>
            </tr>
        </table>
	</div>    
    <script type="text/javascript">
        function open_store_popup(data,action,type)
        {
            width=1350;      
            emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'closing_stock_report_controller.php?data=' + data + '&action=' + action, 'Location Wise Details', 'width='+width+'px,height=350px,center=1,resize=0,scrolling=0', '../../../');
        }
    </script>
  <?
}

if($action=="open_job_wise_details_popup")
{
    echo load_html_head_contents("Job wise Yarn Details Info", "../../../../", 1, 1,'','','');

    $lib_color = return_library_array("select id,color_name from  lib_color where is_deleted=0 and status_active=1", "id", "color_name");
    
    $data_ex  = explode("_", $data);
    $company  = $data_ex[0];
    $from_date  = $data_ex[1];
    $to_date  = $data_ex[2];
    $ex_rate  = $data_ex[3];
    $store_id   = $data_ex[4];
    $comp_id   = $data_ex[5];
    $count_id   = $data_ex[6];
    $color_id   = $data_ex[7];

    if($store_id<1) {echo "No Store Location Found"; die;}
   
  ?>
  <div>
    <!-- =============================== dyed yarn part =========================== -->
    <?
    //if ($db_type == 0) $grp_conct="group_concat(distinct(b.pi_wo_batch_no)) as WO_NO";
      //      else $grp_conct="LISTAGG(b.pi_wo_batch_no, ',') WITHIN GROUP (ORDER BY b.pi_wo_batch_no) as WO_NO";

    $sql_trans = "SELECT f.job_no as JOB_NO ,e.grouping as INTERNAL_REF,f.style_ref_no as STYLE_REF_NO,b.pi_wo_batch_no as WO_NO,c.color as YARN_COLOR,b.id as TRANS_ID ,b.mst_id as MST_ID, b.transaction_date as TRANSACTION_DATE,b.transaction_type as TRANSACTION_TYPE, b.cons_quantity as CONS_QUANTITY
    FROM product_details_master c,inv_transaction b 
    LEFT JOIN order_wise_pro_details d ON d.trans_id=b.id AND b.prod_id = d.prod_id and d.status_active=1 and d.is_deleted=0
    LEFT JOIN wo_po_break_down e ON  e.id= d.po_breakdown_id
    LEFT JOIN wo_po_details_master f on  f.id= e.job_id   
    WHERE  b.prod_id = c.id AND b.item_category = 1 AND b.company_id = $company AND b.store_id = $store_id AND b.transaction_date<='" . $to_date . "' AND b.status_active = 1 AND b.is_deleted = 0 AND c.yarn_comp_type1st = $comp_id AND c.yarn_count_id = $count_id AND c.color = $color_id AND c.dyed_type =1 AND c.status_active = 1 AND c.is_deleted = 0";
    //echo $sql_trans; die(); 

    $result_trans = sql_select($sql_trans);
    $data_array = array();
    $trans_check = array();
    foreach($result_trans as $row)
    {
        $trans_date = $row["TRANSACTION_DATE"];
        $trans_type = $row["TRANSACTION_TYPE"];
        $job_no = $row["JOB_NO"];
        $int_ref = $row["INTERNAL_REF"];
        $style_ref = $row["STYLE_REF_NO"];
        $color = $row["YARN_COLOR"];

        $smn_booking_no = 0;
        if( $job_no=="" ) // sample
        {
            if($trans_type==1)
            {
                $smn_booking_no =  return_field_value("booking_no"," wo_yarn_dyeing_dtls","mst_id ='".$row["WO_NO"]."'  and is_deleted=0 and status_active=1");
            }
            else if($trans_type==2)
            {
                $smn_booking_no =  return_field_value("booking_no"," inv_issue_master","id ='".$row["MST_ID"]."'  and is_deleted=0 and status_active=1");
            }
            else if($trans_type==3)
            {
                //FAL-YRR-21-00063
                $rcv_id_from_rcv_rtn =  return_field_value("received_id"," inv_issue_master","id ='".$row["MST_ID"]."'  and is_deleted=0 and status_active=1");
                $smn_booking_no =  return_field_value("booking_no"," inv_receive_master","id ='".$rcv_id_from_rcv_rtn."'  and is_deleted=0 and status_active=1");
            }
            else if($trans_type==4)
            {
                $smn_booking_no =  return_field_value("booking_no"," inv_receive_master","id ='".$row["MST_ID"]."'  and is_deleted=0 and status_active=1");
            }                              
        }
        else
        {
            $smn_booking_no = 0;
        }  
 
        if($trans_check[$row["MST_ID"]][$row["TRANS_ID"]]=="")
        {
            $trans_check[$row["MST_ID"]][$row["TRANS_ID"]]=$row["TRANS_ID"];

            if(strtotime($trans_date) < strtotime($from_date)) // opening data
            {
                if($trans_type==1 || $trans_type==4 || $trans_type==5)
                {
                    $data_array[$job_no][$int_ref][$style_ref][$color][$smn_booking_no]['rcv_total_opening'] += $row["CONS_QUANTITY"];
                }
                else
                {
                    $data_array[$job_no][$int_ref][$style_ref][$color][$smn_booking_no]['iss_total_opening'] += $row["CONS_QUANTITY"];
                }
            }

            if(strtotime($trans_date) >= strtotime($from_date) && strtotime($trans_date) <= strtotime($to_date))
            {
                if($trans_type==1 || $trans_type==4 || $trans_type==5)
                {
                    $data_array[$job_no][$int_ref][$style_ref][$color][$smn_booking_no]['total_rcv_qnty'] += $row["CONS_QUANTITY"];
                }
                else
                {
                    $data_array[$job_no][$int_ref][$style_ref][$color][$smn_booking_no]['total_issue_qnty'] += $row["CONS_QUANTITY"];
                }
            }
        }

    }

   // echo "<pre>";
    //print_r($data_array);
    ?>

    <div style="margin-top: 30px;">
      <table border="1" rules="all" class="rpt_table" width="730" cellpadding="0" cellspacing="0">
        <caption><h3>Dyed Yarn</h3></caption>
        <thead>
              <tr>
                  <th width="50">SL No</th>
                  <th width="100">Job No</th>
                  <th width="100">Internal Ref </th>
                  <th width="100">Style No</th>
                  <th width="100">Sample FB No</th>
                  <th width="100">Yarn Color</th>
                  <th>Stock qty</th>
                </tr>
            </thead>
        </table>
        <div style="width:750px; max-height:150px; overflow-y:auto" id="scroll_body">
          <table border="1" rules="all" class="rpt_table" width="730" cellpadding="0" cellspacing="0">
            <tbody>
                <?
                $i=1; 
                foreach ($data_array as $job_no=>$int_ref_arr)
                {
                    foreach ($int_ref_arr as $int_ref=>$style_ref_arr)
                    {
                        foreach ($style_ref_arr as $style_ref=>$color_arr)
                        {
                            foreach ($color_arr as $color_id=>$booking_arr)
                            {  
                                foreach ($booking_arr as $booking_no=>$row)
                                { 
                                    if ($i % 2 == 0)
                                        $bgcolor = "#E9F3FF";
                                    else
                                        $bgcolor = "#FFFFFF";

                                    $yarn_closing_qty = ($row["rcv_total_opening"]+$row["total_rcv_qnty"])-($row["iss_total_opening"]+$row["total_issue_qnty"]);
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr<? echo $i; ?>">
                                  
                                    <td width="50" align="center"><p><? echo $i;?></p></td>
                                    <td width="100"><p><? echo $job_no;?></p></td>
                                    <td width="100"><p><? echo $int_ref;?></p></td>
                                    <td align="right" width="100"><p><? echo $style_ref;?></p></td>
                                    <td align="right" width="100"><p><? echo $booking_no;?></p></td>
                                    <td align="right" width="100"><p><? echo $lib_color[$color_id];?></p></td>
                                    <td align="right"><p><? echo number_format($yarn_closing_qty,2);?></p></td>
                                    </tr>
                                    <?
                                    $i++; 
                                    $gr_yarn_close_qty +=$yarn_closing_qty;
                                }
                            }
                        }
                    }
                }                
                ?>
            </tbody>
          </table>
      </div>
      <table  border="1" rules="all" class="rpt_table"  width="<? echo 730;?>"  cellpadding="0" cellspacing="0">
        <tfoot>
          <tr bgcolor="#C4C4C4" style="font-weight:bold;text-align:right;">
            <td width="550">Total</td>
            <td><? echo number_format($gr_yarn_close_qty,2);?></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
  <?
}
?>