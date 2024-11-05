<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 130, "select a.id,a.location_name from lib_location a where a.status_active=1 and a.is_deleted=0 and a.company_id='$data'  order by a.location_name","id,location_name", 1, "--Select Location--", 0, "",0 );
	exit();
}


if ($action=="load_drop_down_floor")
{
	$data=explode("_",$data);
	$company_id=$data[0];
	$location_id=$data[1];
	$machine_category=$data[2];
	if($location_id==0 || $location_id=="") $location_cond=""; else $location_cond=" and b.location_id=$location_id";
	if($machine_category==0 || $machine_category=="") $category_cond=""; else $category_cond=" and b.category_id=$machine_category";
	
	echo create_drop_down( "cbo_floor_id", 130, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.company_id=$company_id and b.status_active=1 and b.is_deleted=0 $location_cond $category_cond  group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "-- Select Floor --", 0, "load_drop_down( 'requires/machine_wise_cost_report_controller',document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_machine_category').value+'_'+this.value, 'load_drop_machine', 'machine_td' );","" );
  exit();	 
}
if ($action=="load_drop_machine")
{
	$data=explode("_",$data);
	$company_id=$data[0];
	$machine_category=$data[1];
	$floor_id=$data[2];
	if($machine_category==0 || $machine_category=="") $machine_cond=""; else $machine_cond=" and category_id=$machine_category";
	if($floor_id==0 || $floor_id=="") $floor_cond=""; else $floor_cond=" and floor_id=$floor_id";
	
	echo create_drop_down( "cbo_machine_name", 130, "select id, machine_no as machine_name from lib_machine_name where  company_id=$company_id and status_active=1 and is_deleted=0 and is_locked=0 $floor_cond $machine_cond order by machine_no","id,machine_name", 1, "-- Select Machine --", 0, "","" );
	exit();
}

if ($action=="load_drop_down_store")
{
        
    echo create_drop_down( "cbo_store_name", 130, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$data and a.status_active=1 and a.is_deleted=0 $cetegory_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", $selected, "" ,0);
    die;
}


if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_location_id=str_replace("'","",$cbo_location_id);
    $cbo_store_id=str_replace("'","",$cbo_store_name);
	$cbo_machine_category=str_replace("'","",$cbo_machine_category);
	$cbo_floor_id=str_replace("'","",$cbo_floor_id);
	$cbo_machine_name=str_replace("'","",$cbo_machine_name);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
		
	$str_cond="";
	if ($cbo_location_id>0) $str_cond .=" and b.location_id=$cbo_location_id";
	if ($cbo_machine_category>0) $str_cond .=" and b.machine_category=$cbo_machine_category";
	if ($cbo_floor_id>0) $str_cond .=" and b.production_floor=$cbo_floor_id";
	if ($cbo_machine_name>0) $str_cond .=" and b.machine_id=$cbo_machine_name";
    if ($cbo_store_id>0) $str_cond .=" and b.STORE_ID=$cbo_store_name";
	if($txt_date_from!="" && $txt_date_to!="") $str_cond .=" and a.issue_date between '$txt_date_from' and '$txt_date_to'";
	
     

	$sql="SELECT a.id as issue_id, a.issue_date, a.issue_number,b.store_id, b.id as trans_id, b.item_category, b.prod_id, b.machine_category, b.production_floor, b.machine_id, b.cons_quantity, b.cons_rate, b.cons_amount from  inv_issue_master a,  inv_transaction b where a.id=b.mst_id and a.entry_form=21 and b.item_category in(4,8,9,10,11,15,16,17,18,19,20,21,22,32,40,64,94) and b.transaction_type=2 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 $str_cond order by b.machine_category, b.machine_id, a.issue_date, b.prod_id ";
	//echo $sql;die;
	$sql_result=sql_select($sql);
	$machine_wise_summary=array();$flore_wise_summary=array();
	foreach($sql_result as $row)
	{
		$machine_wise_summary[$row[csf("machine_category")]][$row[csf("machine_id")]]+=$row[csf("cons_amount")];
		$flore_wise_summary[$row[csf("machine_category")]][$row[csf("production_floor")]]+=$row[csf("cons_amount")];
	}
	$prod_sql=sql_select("select id, item_group_id, product_name_details from product_details_master");
	$prod_data=array();
	foreach($prod_sql as $row)
	{
		$prod_data[$row[csf("id")]]["item_group_id"]=$row[csf("item_group_id")];
		$prod_data[$row[csf("id")]]["product_name_details"]=$row[csf("product_name_details")];
	}
	unset($prod_sql);
	
	$com_name=return_field_value("company_name","lib_company","id=$cbo_company_name","company_name");
	$machine_name_arr=return_library_array("select id, machine_no from lib_machine_name","id","machine_no");
	$flore_name_arr=return_library_array("select id, floor_name from lib_prod_floor","id","floor_name");
	$item_group_name_arr=return_library_array("select id, item_name  from lib_item_group","id","item_name");
    $store_name=return_library_array("select id, store_name  from lib_store_location","id","store_name");
	$i=1;
	ob_start();	
	?>
    <div style="width:920px;">
        <table width="900" cellpadding="0" cellspacing="0" id="caption">
            <tr>
            	<td align="center" width="100%" colspan="20" class="form_caption" ><strong style="font-size:18px">Company Name:<? echo " ". $com_name; ?></strong></td>
            </tr> 
            <tr>  
            	<td align="center" width="100%" colspan="20" class="form_caption" ><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
            </tr>
            <tr style="border:none;">
            	<td colspan="20" align="center" style="border:none;font-size:12px; font-weight:bold"><? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?></td>
            </tr>
        </table>
        <table width="900" cellpadding="0" cellspacing="0" id="caption" align="left">
        	<tr>
            	<td width="440">
                    <table width="440" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="tbl_machine_wise" align="left">
                    	<thead>
                        	<tr>
                            	<th width="50">SL</th>
                                <th width="120">M/C Category</th>
                                <th width="150">M/C No</th>
                                <th>Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                        	<?
							$i=1;
							foreach($machine_wise_summary as $mc_cat=>$val)
							{
								foreach($val as $mc_id=>$value)
								{
									if ($i%2==0)
									$bgcolor="#E9F3FF";
									else
									$bgcolor="#FFFFFF";
									?>
                                    <tr bgcolor="<? echo $bgcolor; ?>">
                                    	<td align="center"><? echo $i; ?></td>
                                        <td><p><? echo $machine_category[$mc_cat]; ?>&nbsp;</p></td>
                                        <td><p><? echo $machine_name_arr[$mc_id]; ?>&nbsp;</p></td>
                                        <td align="right"><? echo number_format($value,2); ?></td>
                                    </tr>
                                    <?
									$i++;
									$to_mc_val+=$value;
								}
							}
							?>
                        </tbody>
                        <tfoot>
                        	<tr>
                                <th colspan="3" align="right">Total:</th>
                                <th  align="right"><? echo number_format($to_mc_val,2); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </td>
                <td>&nbsp;</td>
                <td width="440">
                	<table width="440" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="tbl_flore_wise" align="left">
                    	<thead>
                        	<tr>
                            	<th width="50">SL</th>
                                <th width="120">M/C Category</th>
                                <th width="150">Floor</th>
                                <th>Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                        	<?
							$i=1;
							foreach($flore_wise_summary as $mc_cat=>$val)
							{
								foreach($val as $fl_id=>$value)
								{
									if ($i%2==0)
									$bgcolor="#E9F3FF";
									else
									$bgcolor="#FFFFFF";
									?>
                                    <tr bgcolor="<? echo $bgcolor; ?>">
                                    	<td align="center"><? echo $i; ?></td>
                                        <td><p><? echo $machine_category[$mc_cat]; ?>&nbsp;</p></td>
                                        <td><p><? echo $flore_name_arr[$fl_id]; ?>&nbsp;</p></td>
                                        <td align="right"><? echo number_format($value,2); ?></td>
                                    </tr>
                                    <?
									$i++;
									$to_flore_val+=$value;
								}
							}
							?>
                        </tbody>
                        <tfoot>
                        	<tr>
                                <th colspan="3" align="right">Total:</th>
                                <th  align="right"><? echo number_format($to_flore_val,2); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
        </table>
    	<table width="900" cellpadding="0" cellspacing="0">
        	<tr><td>&nbsp;</td></tr>
        </table>
        <table width="1020" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" align="left">
            <thead>
                <tr>
                    <th width="30">Sl</th>
                    <th width="70">Issue Date</th>
                    <th width="120">Issue No</th>
                    <th width="100">Item Catagury</th>
                    <th width="150">Store Name</th>
                    <th width="120">Item Group</th>
                    <th width="150">Item Description</th>
                    <th width="80">Quantity</th>
                    <th width="80">Rate</th>
                    <th>Amount</th>
                </tr>
            </thead>
        </table>
        <div style="width:1040px; overflow-y:scroll; max-height:230px;font-size:12px; overflow-x:hidden;" id="scroll_body" align="left">
        <table width="1020" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
            <tbody>
            <?
			
            $i=1;$k=1;
            foreach($sql_result as $row)
            {
				if($group_check[$row[csf('machine_category')]][$row[csf('production_floor')]][$row[csf('machine_id')]]=="")
				{
					
					if ($k!=1)
					{
						//$k1=$k-2;
						?>	
						<tr bgcolor="#CCCCCC">
							<td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
							<td align="right"><b>Sub Total:</b></td>
						   	<td align="right"> <p><? echo number_format($machine_flore_wise_qnty,2); ?></p></td>
                            <td>&nbsp;</td>
							<td align="right"><p><? echo number_format($machine_flore_wise_value,2); ?></p></td>
						</tr>
						<?
						$machine_flore_wise_qnty = 0;
						$machine_flore_wise_value = 0;
					}
					$k++;
					$group_check[$row[csf('machine_category')]][$row[csf('production_floor')]][$row[csf('machine_id')]]=$row[csf('machine_category')];
					?>
                    <tr>
                        <td colspan="9" bgcolor="#EEEEEE"><? echo "Machine Category: &nbsp;".$machine_category[$row[csf("machine_category")]].". &nbsp; Flore Name: &nbsp;".$flore_name_arr[$row[csf("production_floor")]].". &nbsp; Machine Name: &nbsp;".$machine_name_arr[$row[csf("machine_id")]]; ?></td>
                    </tr>
                    <?
				}
                if ($i%2==0)
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="70"><p><? if($row[csf("issue_date")]!="" && $row[csf("issue_date")]!="0000-00-00") echo change_date_format($row[csf("issue_date")]); ?>&nbsp;</p></td>
                    <td width="120"><p> <? echo $row[csf("issue_number")]; ?></p></td>
                    <td width="100"><p> <? echo $item_category[$row[csf("item_category")]]; ?></p></td>

                    <td width="150" style="text-align:center;"><p> <? echo $store_name[$row["STORE_ID"]]; ?></p></td>

                    <td width="120"><p> <? echo $item_group_name_arr[$prod_data[$row[csf("prod_id")]]["item_group_id"]]; ?></p></td>
                    <td width="150"><p> <? echo $prod_data[$row[csf("prod_id")]]["product_name_details"];  ?></p></td>
                    <td width="80" align="right"><? echo number_format($row[csf("cons_quantity")],2); ?></td>
                    <td width="80" align="right"><? echo number_format($row[csf("cons_rate")],2); ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_amount")],2); ?></td>
                </tr>
                <?
				$machine_flore_wise_qnty+=$row[csf("cons_quantity")];
				$machine_flore_wise_value+=$row[csf("cons_amount")];
				$total_qnty+=$row[csf("cons_quantity")];
				$total_value+=$row[csf("cons_amount")];
                $i++;
            }
            ?>
                <tr bgcolor="#CCCCCC">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><b>Sub Total:</b></td>
                    <td align="right"> <p><? echo number_format($machine_flore_wise_qnty,2); ?></p></td>
                    <td>&nbsp;</td>
                    <td align="right"><p><? echo number_format($machine_flore_wise_value,2); ?></p></td>
                </tr>
            </tbody>
        </table>
        </div>
        <table width="1022" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_footer" align="left">
            <tfoot>
                <tr>
                    <th width="30">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="120">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="150">&nbsp;</th>
                    <th width="120">&nbsp;</th>
                    <th width="150" align="right">Grand Total:</th>
                    <th width="80" align="right"><? echo number_format($total_qnty,2); ?></th>
                    <th width="80">&nbsp;</th>
                    <th  align="center"><? echo number_format($total_value,2); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
            
           
    <?
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
    echo "$html**$filename"; 
    exit();
}
?>