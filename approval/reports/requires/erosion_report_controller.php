<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$department_arr=return_library_array( "select id,DEPARTMENT_NAME from LIB_DEPARTMENT comp  ",'id','DEPARTMENT_NAME');
$profit_arr=return_library_array( "select id,PROFIT_CENTER_NAME from LIB_PROFIT_CENTER comp ",'id','PROFIT_CENTER_NAME');

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}
if ($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_supplier", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(3) and c.tag_company =$data and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
}


$tmplte=explode("**",$data);
if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;


if($action=="report_generate"){ 

	
	$process = array( &$_POST );
	//print_r($process);
	extract(check_magic_quote_gpc( $process ));
	
	    $company_name = str_replace("'","",$cbo_company_name);
        $cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
        $approval_type = str_replace("'","",$cbo_approval_type);
		$cbo_search_type = str_replace("'","",$cbo_search_type);
		$txt_search_data = str_replace("'","",$txt_search_data);
        $cbo_erosion_type = str_replace("'","",$cbo_erosion_type);
        $txt_erosion_date_from = str_replace("'","",$txt_erosion_date_from);
        $txt_erosion_date_to = str_replace("'","",$txt_erosion_date_to);
		
        $txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
  
        if($cbo_buyer_name){$buyer_con .= " and a.BUYER_NAME =".$cbo_buyer_name.""; }
        if($cbo_erosion_type){$where_con .= " and c.EROSION_TYPE =".$cbo_erosion_type.""; }
       // if($approval_type){$where_con .= " and c.APPROVED =".$approval_type.""; }
        //if($txt_erosion_no){$where_con .= " and c.SHIP_APP_REQ_NO like('%".$txt_erosion_no."')"; }

		if($cbo_search_type==1 && $txt_search_data!=''){
			$where_con .= " and c.SHIP_APP_REQ_NO like('%".$txt_search_data."')"; 
		}
		else if($cbo_search_type==2 && $txt_search_data!=''){
			$where_con .= " and a.JOB_NO like('%".$txt_search_data."')"; 
		}
		else if($cbo_search_type==3 && $txt_search_data!=''){
			$where_con .= " and b.PO_NUMBER like('%".$txt_search_data."')"; 
		}
		if($txt_erosion_date_from && $txt_erosion_date_to){
            $where_con .= " and c.EROSION_DATE BETWEEN '".$txt_erosion_date_from."' AND '".$txt_erosion_date_to."'";	
        }
 
		//.if ($cbo_erosion_type != "") $er_cond=" and c.EROSION_TYPE=$cbo_erosion_type";
		$erosion_type=array(1=>"Discount Shipment",2=>"Sea-Air Shipment",3=>"Air Shipment");
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
		$search_by_arr=array(2=>"Pending",3=>"Partial Approved",1=>"Full Approved");

	$user_name_array = array();
	$userData = sql_select( "SELECT id, user_name, user_full_name, designation from user_passwd");
	foreach($userData as $user_row)
	{
		$user_name_array[$user_row[csf('id')]]['name']=$user_row[csf('user_name')];
		$user_name_array[$user_row[csf('id')]]['full_name']=$user_row[csf('user_full_name')];
		$user_name_array[$user_row[csf('id')]]['designation']=$designation_array[$user_row[csf('designation')]];	
	}

		
	$approved_no_array = array();
	$queryApp = "SELECT mst_id, approved_by, approved_no from approval_history where entry_form=66 and un_approved_by=0";
	$resultApp = sql_select($queryApp);
	foreach ($resultApp as $row)
	{
		$approved_no_array[$row[csf('mst_id')]][$row[csf('approved_by')]]=$row[csf('approved_no')];
	}
	

	foreach($signatory_data_arr as $sval)
	{
		$signatory_main[$sval[csf('user_id')]]=$sval[csf('bypass')];
		$userArr[$sval[csf('user_id')]]=$sval[csf('user_id')];
	}
 
	if($approval_type==2) // pending
	{      $approved_cond=" and c.APPROVED in(0,2)";  
		    $sql = "SELECT a.JOB_NO,c.PROFIT_CENTER,c.DEPARTMENT,c.ID,c.COMPANY_ID,c.APPROVED,c.SHIP_APP_REQ_PREFIX,c.SHIP_APP_REQ_NUM,c.  SHIP_APP_REQ_NO,c.PO_BREAK_DOWN_ID,c.EROSION_TYPE,c.SHIP_APP_REQ_DATE,c.EROSION_DATE,c.EROSION_VALUE,c.TO_BE_SHIPPED_QTY,c.EXPECTED_SHIP_DATE,c.THE_PROBLEMS,c.ROOT_CAUSES,c.CORRECTIVE_ACTION_PLANS, c.PRECAUTIONERY_FUTURE_PLANS, b.PUB_SHIPMENT_DATE,
			b.SHIPMENT_DATE,b.PO_NUMBER,b.PO_RECEIVED_DATE,b.PO_QUANTITY,a.GMTS_ITEM_ID,a.DEALING_MARCHANT,a.TEAM_LEADER,a.FACTORY_MARCHANT,a.TOTAL_SET_QNTY,a.JOB_NO,d.MARGIN_DZN,e.COSTING_PER,
			a.BUYER_NAME,a.STYLE_REF_NO,  max(e.SEW_SMV) as SEW_SMV FROM erosion_entry c,wo_po_details_master  a, wo_po_break_down b LEFT JOIN WO_PRE_COST_DTLS d ON b.job_no_mst = d.job_no
			LEFT JOIN wo_pre_cost_mst e ON e.job_no = b.job_no_mst WHERE     a.job_no = b.job_no_mst AND b.id = c.PO_BREAK_DOWN_ID AND c.COMPANY_ID =$company_name AND a.is_deleted = 0 AND b.status_active = 1 AND c.is_deleted = 0 AND c.status_active = 1
			AND b.is_deleted = 0 $where_con $approved_cond $buyer_con group by a.JOB_NO,c.PROFIT_CENTER,c.DEPARTMENT,c.ID,c.COMPANY_ID,c.APPROVED,c.SHIP_APP_REQ_PREFIX,c.SHIP_APP_REQ_NUM,c.SHIP_APP_REQ_NO,c.PO_BREAK_DOWN_ID,c.EROSION_TYPE,c.SHIP_APP_REQ_DATE,c.EROSION_DATE,
			c.EROSION_VALUE,c.TO_BE_SHIPPED_QTY,c.EXPECTED_SHIP_DATE,c.THE_PROBLEMS,c.ROOT_CAUSES,c.CORRECTIVE_ACTION_PLANS, c.PRECAUTIONERY_FUTURE_PLANS, b.PUB_SHIPMENT_DATE,
			b.SHIPMENT_DATE,b.PO_NUMBER,b.PO_RECEIVED_DATE,b.PO_QUANTITY,a.GMTS_ITEM_ID,a.DEALING_MARCHANT,a.TEAM_LEADER,a.FACTORY_MARCHANT,a.TOTAL_SET_QNTY,a.JOB_NO,d.MARGIN_DZN,e.COSTING_PER,a.BUYER_NAME,a.STYLE_REF_NO"; 
	
	}
	elseif($approval_type==1){ //Full_approved
		$approved_cond=" and c.APPROVED=1";  
		$sql = "SELECT a.JOB_NO,c.PROFIT_CENTER,c.DEPARTMENT,c.ID,c.COMPANY_ID,c.APPROVED,c.SHIP_APP_REQ_PREFIX,c.SHIP_APP_REQ_NUM,c.  SHIP_APP_REQ_NO,c.PO_BREAK_DOWN_ID,c.EROSION_TYPE,c.SHIP_APP_REQ_DATE,c.EROSION_DATE,
		c.EROSION_VALUE,c.TO_BE_SHIPPED_QTY,c.EXPECTED_SHIP_DATE,c.THE_PROBLEMS,c.ROOT_CAUSES,c.CORRECTIVE_ACTION_PLANS, c.PRECAUTIONERY_FUTURE_PLANS, b.PUB_SHIPMENT_DATE,
		b.SHIPMENT_DATE,b.PO_NUMBER,b.PO_RECEIVED_DATE,b.PO_QUANTITY,a.GMTS_ITEM_ID,a.DEALING_MARCHANT,a.TEAM_LEADER,a.FACTORY_MARCHANT,a.TOTAL_SET_QNTY,a.JOB_NO,d.MARGIN_DZN,e.COSTING_PER,
		a.BUYER_NAME,a.STYLE_REF_NO,  max(e.SEW_SMV) as SEW_SMV FROM erosion_entry c,wo_po_details_master  a, wo_po_break_down b LEFT JOIN WO_PRE_COST_DTLS d ON b.job_no_mst = d.job_no
		LEFT JOIN wo_pre_cost_mst e ON e.job_no = b.job_no_mst WHERE     a.job_no = b.job_no_mst AND b.id = c.PO_BREAK_DOWN_ID AND c.COMPANY_ID =$company_name  AND a.is_deleted = 0 AND b.status_active = 1 AND c.is_deleted = 0 AND c.status_active = 1
		AND b.is_deleted = 0 $where_con $approved_cond $buyer_con group by a.JOB_NO,c.PROFIT_CENTER,c.DEPARTMENT,c.ID,c.COMPANY_ID,c.APPROVED,c.SHIP_APP_REQ_PREFIX,c.SHIP_APP_REQ_NUM,c.SHIP_APP_REQ_NO,c.PO_BREAK_DOWN_ID,c.EROSION_TYPE,c.SHIP_APP_REQ_DATE,c.EROSION_DATE,
		c.EROSION_VALUE,c.TO_BE_SHIPPED_QTY,c.EXPECTED_SHIP_DATE,c.THE_PROBLEMS,c.ROOT_CAUSES,c.CORRECTIVE_ACTION_PLANS, c.PRECAUTIONERY_FUTURE_PLANS, b.PUB_SHIPMENT_DATE,
		b.SHIPMENT_DATE,b.PO_NUMBER,b.PO_RECEIVED_DATE,b.PO_QUANTITY,a.GMTS_ITEM_ID,a.DEALING_MARCHANT,a.TEAM_LEADER,a.FACTORY_MARCHANT,a.TOTAL_SET_QNTY,a.JOB_NO,d.MARGIN_DZN,e.COSTING_PER,a.BUYER_NAME,a.STYLE_REF_NO"; //echo $sql;die();
    }
	elseif($approval_type==0){ //Full_approved
		$approved_cond=" and c.APPROVED in(0,1,2,3)";
		$sql = "SELECT a.JOB_NO,c.PROFIT_CENTER,c.DEPARTMENT,c.ID,c.COMPANY_ID,c.APPROVED,c.SHIP_APP_REQ_PREFIX,c.SHIP_APP_REQ_NUM,c.  SHIP_APP_REQ_NO,c.PO_BREAK_DOWN_ID,c.EROSION_TYPE,c.SHIP_APP_REQ_DATE,c.EROSION_DATE,
		c.EROSION_VALUE,c.TO_BE_SHIPPED_QTY,c.EXPECTED_SHIP_DATE,c.THE_PROBLEMS,c.ROOT_CAUSES,c.CORRECTIVE_ACTION_PLANS, c.PRECAUTIONERY_FUTURE_PLANS, b.PUB_SHIPMENT_DATE,
		b.SHIPMENT_DATE,b.PO_NUMBER,b.PO_RECEIVED_DATE,b.PO_QUANTITY,a.GMTS_ITEM_ID,a.DEALING_MARCHANT,a.TEAM_LEADER,a.FACTORY_MARCHANT,a.TOTAL_SET_QNTY,a.JOB_NO,d.MARGIN_DZN,e.COSTING_PER,
		a.BUYER_NAME,a.STYLE_REF_NO,  max(e.SEW_SMV) as SEW_SMV FROM erosion_entry c,wo_po_details_master  a, wo_po_break_down b LEFT JOIN WO_PRE_COST_DTLS d ON b.job_no_mst = d.job_no
		LEFT JOIN wo_pre_cost_mst e ON e.job_no = b.job_no_mst WHERE     a.job_no = b.job_no_mst AND b.id = c.PO_BREAK_DOWN_ID AND c.COMPANY_ID =$company_name  AND a.is_deleted = 0 AND b.status_active = 1 AND c.is_deleted = 0 AND c.status_active = 1
		AND b.is_deleted = 0 $where_con $approved_cond $buyer_con group by a.JOB_NO,c.PROFIT_CENTER,c.DEPARTMENT,c.ID,c.COMPANY_ID,c.APPROVED,c.SHIP_APP_REQ_PREFIX,c.SHIP_APP_REQ_NUM,c.SHIP_APP_REQ_NO,c.PO_BREAK_DOWN_ID,c.EROSION_TYPE,c.SHIP_APP_REQ_DATE,c.EROSION_DATE,
		c.EROSION_VALUE,c.TO_BE_SHIPPED_QTY,c.EXPECTED_SHIP_DATE,c.THE_PROBLEMS,c.ROOT_CAUSES,c.CORRECTIVE_ACTION_PLANS, c.PRECAUTIONERY_FUTURE_PLANS, b.PUB_SHIPMENT_DATE,
		b.SHIPMENT_DATE,b.PO_NUMBER,b.PO_RECEIVED_DATE,b.PO_QUANTITY,a.GMTS_ITEM_ID,a.DEALING_MARCHANT,a.TEAM_LEADER,a.FACTORY_MARCHANT,a.TOTAL_SET_QNTY,a.JOB_NO,d.MARGIN_DZN,e.COSTING_PER,a.BUYER_NAME,a.STYLE_REF_NO"; 


	}
	else 
	 {
		$approved_cond=" and c.APPROVED=3";  
		$sql = "SELECT a.JOB_NO,c.PROFIT_CENTER,c.DEPARTMENT,c.ID,c.COMPANY_ID,c.APPROVED,c.SHIP_APP_REQ_PREFIX,c.SHIP_APP_REQ_NUM,c.  SHIP_APP_REQ_NO,c.PO_BREAK_DOWN_ID,c.EROSION_TYPE,c.SHIP_APP_REQ_DATE,c.EROSION_DATE,
		c.EROSION_VALUE,c.TO_BE_SHIPPED_QTY,c.EXPECTED_SHIP_DATE,c.THE_PROBLEMS,c.ROOT_CAUSES,c.CORRECTIVE_ACTION_PLANS, c.PRECAUTIONERY_FUTURE_PLANS, b.PUB_SHIPMENT_DATE,
		b.SHIPMENT_DATE,b.PO_NUMBER,b.PO_RECEIVED_DATE,b.PO_QUANTITY,a.GMTS_ITEM_ID,a.DEALING_MARCHANT,a.TEAM_LEADER,a.FACTORY_MARCHANT,a.TOTAL_SET_QNTY,a.JOB_NO,d.MARGIN_DZN,e.COSTING_PER,
		a.BUYER_NAME,a.STYLE_REF_NO,  max(e.SEW_SMV) as SEW_SMV FROM erosion_entry c,wo_po_details_master  a, wo_po_break_down b LEFT JOIN WO_PRE_COST_DTLS d ON b.job_no_mst = d.job_no
		LEFT JOIN wo_pre_cost_mst e ON e.job_no = b.job_no_mst WHERE     a.job_no = b.job_no_mst AND b.id = c.PO_BREAK_DOWN_ID AND c.COMPANY_ID =$company_name $buyer_con  AND a.is_deleted = 0 AND b.status_active = 1 AND c.is_deleted = 0 AND c.status_active = 1
		AND b.is_deleted = 0 $where_con $approved_cond group by a.JOB_NO,c.PROFIT_CENTER,c.DEPARTMENT,c.ID,c.COMPANY_ID,c.APPROVED,c.SHIP_APP_REQ_PREFIX,c.SHIP_APP_REQ_NUM,c.SHIP_APP_REQ_NO,c.PO_BREAK_DOWN_ID,c.EROSION_TYPE,c.SHIP_APP_REQ_DATE,c.EROSION_DATE,
		c.EROSION_VALUE,c.TO_BE_SHIPPED_QTY,c.EXPECTED_SHIP_DATE,c.THE_PROBLEMS,c.ROOT_CAUSES,c.CORRECTIVE_ACTION_PLANS, c.PRECAUTIONERY_FUTURE_PLANS, b.PUB_SHIPMENT_DATE,
		b.SHIPMENT_DATE,b.PO_NUMBER,b.PO_RECEIVED_DATE,b.PO_QUANTITY,a.GMTS_ITEM_ID,a.DEALING_MARCHANT,a.TEAM_LEADER,a.FACTORY_MARCHANT,a.TOTAL_SET_QNTY,a.JOB_NO,d.MARGIN_DZN,e.COSTING_PER,a.BUYER_NAME,a.STYLE_REF_NO"; 
	 }

	 //echo $sql;die();
	
	$nameArray=sql_select( $sql );
	//print_r($nameArray);
    $profit_data_arr=array();
	$profit_data_sql=sql_select("select a.mst_id,c.ID, a.profit_center_id,a.percentage,a.distribution_value  FROM erosion_entry_profits a,erosion_entry c where c.ID=a.MST_ID $where_con  ");

	//  echo "select a.mst_id,c.ID, a.profit_center_id,a.percentage,a.distribution_value  FROM erosion_entry_profits a,erosion_entry c where c.ID=a.MST_ID and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 $where_con order by a.mst_id ";die;

	// echo "select a.mst_id, a.profit_center_id  FROM erosion_entry_profits a,erosion_entry c where c.ID=a.MST_ID $where_con order by a.mst_id ";die;

	
	



	foreach($profit_data_sql as $row)
		{
			//$pro_item_arr[$row[csf("product_id")]]=$item_category[$row[csf("item_category_id")]];
			$profit_data_arr[$row[csf("id")]].=$profit_arr[$row[csf("profit_center_id")]].",";
			$profit_per_arr[$row[csf("id")]].=$row[csf("percentage")].",";
			$profit_distribute_arr[$row[csf("id")]].=$row[csf("distribution_value")].",";
		}
		//
		
		//print_r($profit_distribute_arr);
    $width=1150;
	//echo '<pre>';print_r($user_approval_array);
	ob_start();
	?>

<style>
    .bordered-cell {
        border: 1px solid #000;
    }

    .rowspan-cell {
        border-bottom: 1px solid #000; 
    }

    .inner-cell {
        border-top: 1px solid #000; 
    }
</style>

        <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:<? echo $width+25; ?>px;">
        <legend>Erosion Approval</legend>
        <div style="width:<? echo $width; ?>px; margin:0 auto;" id="scroll_body">
        	
            <table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="left" >
                <thead>
                    <th width="30">SL</th>
					<th width="125">Erosion No.</th>         
                    <th width="125">Erosion Type</th>
                    <th width="100">Job No.</th> 
                    <th width="100">Order No.</th> 
                    <th width="100">Buyer name</th>                   
                    <th width="100">Profit Center</th>                                  
                    <th width="100">Percentage %</th>                                   
                    <th width="100">Erosion Value</th> 
					<th width="100">Department</th>                      
                    <th width="80">Erosion Date</th> 
					<th>Status</th>
                </thead>
            </table>            
            <div style="min-width:<? echo $width+25; ?>px; float:left; overflow-y:auto; max-height:330px;" id="buyer_list_view">
               <table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all"    class="rpt_table" id="tbl_list_search" align="left">
                    <tbody>
						<?
						$i=1; 
						foreach($nameArray as $row)
						{ 
							$costingPerQty=0;
							if($row['COSTING_PER']==1) $costingPerQty=12;
							elseif($row['COSTING_PER']==2) $costingPerQty=1;	
							elseif($row['COSTING_PER']==3) $costingPerQty=24;
							elseif($row['COSTING_PER']==4) $costingPerQty=36;
							elseif($row['COSTING_PER']==5) $costingPerQty=48;
							else $costingPerQty=0;
							//$precost_data_arr= ($row['MARGIN_DZN']/$costingPerQty)*($row['PO_QUANTITY']*$row['TOTAL_SET_QNTY']); 

							$dept_count_id=array_filter(array_unique(explode(",",$row[csf('DEPARTMENT')])));
							//print_r($dept_count_id);
							foreach($dept_count_id as $count_id)
							{
								if($count_id=="") $dept_count=$department_arr[$count_id]; else $dept_count.=', '.$department_arr[$count_id];
							}
							     $department = implode(",", explode(',', $dept_count));
                                $department = substr($department, 2); 
                                   //echo $department;
							//echo $depatment;die;
							
							$bgcolor = ($i%2==0)?"#fff":"#E9F3FF";

						?>
						  
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>',      '<?   echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                    <td width="30" align="center"><?=$i;?></td>
                                    <td width="125" align="center"><a href="javascript:fn_generate_print(<? echo $row['ID']; ?>,<? echo $row['COMPANY_ID']; ?>)"><? echo $row[csf('SHIP_APP_REQ_NO')]; ?></a></td>	
									 <td width="125" align="center"><?echo $erosion_type[$row[csf('EROSION_TYPE')]];?></td>	
                                    <td width="100" align="center"><? echo $row[csf('JOB_NO')]; ?></td>
                                    <td width="100" style="word-break: break-all;"><? echo $row[csf('PO_NUMBER')]; ?></td>
                                    <td width="100"><?echo $buyer_arr[$row[csf('BUYER_NAME')]];?></td>				
                                    <?php


                                    $values = array_unique(explode(',', rtrim($profit_data_arr[$row[csf("id")]], ',')));

                                            echo '<td width="100">';

                                                foreach ($values as $index => $value) {
                                             if ($index === 0) {
                                         echo '<p  rowspan="' . count($values) . '">' . $value . '</p>';
                                              } else {
                                       echo '<p class="inner-cell">' . $value . '</p>';
                                                      }
                                                    }

                                            echo '</td>';
                                                     ?>
                                    			
									<?php


                                    $values = explode(',', rtrim($profit_per_arr[$row[csf("id")]], ','));

                                            echo '<td width="100">';

                                                foreach ($values as $index => $value) {
                                             if ($index === 0) {
                                         echo '<p rowspan="' . count($values) . '">' . $value . '</p>';
                                              } else {
                                       echo '<p class="inner-cell">' . $value . '</p>';
    }
                                                      }

                                            echo '</td>';
                                                     ?>
			
			                                    <?php


                                               $values =explode(',', rtrim($profit_distribute_arr[$row[csf("id")]], ','));

		                                     echo '<td width="100">';

			                                 foreach ($values as $index => $value) {
		                                  if ($index === 0) {
	                                           echo '<p rowspan="' . count($values) . '">' . $value . '</p>';
		                                          } else {
                                             echo '<p class="inner-cell">' . $value . '</p>';
                                                 }
				                                }

		                                    echo '</td>';
				                              ?>
											  
									<td width="100"><?echo $department;?></td>	
                                    <td width="80"  align="center"><? echo change_date_format($row[csf('EROSION_DATE')]); ?></td>
                                    <td align="right">
									<? if($row[csf('APPROVED')]==1){
									?>
										<!-- <a href="##" onClick="open_popup('< ?=$company_name.'_'.$row[csf('ID')].'_'.$row[csf('APPROVED')]; ?>','full_approved_popup')">< ? echo $search_by_arr[$row[csf('APPROVED')]]; ?> -->
										<? $statusText = $search_by_arr[$row[csf('APPROVED')]]; ?>
									<?
									}
									else if($row[csf('APPROVED')]==3){$statusText = "Partial Approved";}
									else {$statusText = "Pending";}
									?>

										<a href="##" onClick="open_popup('<?=$company_name.'_'.$row[csf('ID')].'_'.$row[csf('APPROVED')]; ?>','full_approved_popup')"><? echo $statusText; ?>

									</td>        
                            </tr>
					   <?
						
					   $i++; 
					   } 
					   ?>                     
                    </tbody>
                </table>
            </div>  
         </div>
     </fieldset>
        
    </form>       
	<?	
	foreach (glob("$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}

	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename="requires/".$user_name."_".$name.".xls";
	echo "$total_data####$filename";
	exit(); 	
}

if($action=="full_approved_popup")
{   extract($_REQUEST);
	list($company_id,$erosion_id,$approved)=explode('_',$data);
	echo load_html_head_contents($tittle." Info", "../../../", 1, 1,'','','');

	$designation_a=return_library_array("select id,custom_designation from lib_designation","id","custom_designation");
	$department_arr=return_library_array( "select id,DEPARTMENT_NAME from LIB_DEPARTMENT comp where status_active =1 and is_deleted=0 order by DEPARTMENT_NAME",'id','DEPARTMENT_NAME');

	// $profit_arr=return_library_array( "select id,PROFIT_CENTER_NAME from LIB_PROFIT_CENTER comp where status_active =1 and is_deleted=0 order by PROFIT_CENTER_NAME",'id','PROFIT_CENTER_NAME');

	$profit_arr=return_library_array("select ID, PROFIT_CENTER_NAME from LIB_PROFIT_CENTER", "ID", "PROFIT_CENTER_NAME");

	$hisSql =  "select MST_ID,APPROVED_BY,APPROVED_DATE, SEQUENCE_NO,APPROVED from APPROVAL_HISTORY where ENTRY_FORM=66 and MST_ID in(".$erosion_id.") order by sequence_no";
	 //echo $hisSql; 
	$hisSqlRes=sql_select($hisSql);
	foreach($hisSqlRes as $row){
		$row['APPROVED_DATE']=strtotime($row['APPROVED_DATE']);
		//$row['APPROVED_BY']
		$sys_id_arr[$row['APPROVED_BY']][]=array(
		'APPROVED'=>$row['APPROVED'],
		'APPROVED_BY'=>$row['APPROVED_BY'],
		'SEQUENCE_NO'=>$row['SEQUENCE_NO'],
		'APPROVED_DATE'=>date('d-m-Y h:i:s A',$row['APPROVED_DATE']),
	  );
	  $userIdArr[$row['APPROVED_BY']]=$row['APPROVED_BY'];
	}
// 	echo "<pre>";
// print_r($sys_id_arr); 
//   echo "</pre>";die();

    //$users = implode(',',$dataArr[$erosion_id]);
	$sql="select a.USER_ID,b.DEPARTMENT_ID as DEPARTMENT,b.DESIGNATION,b.USER_NAME from electronic_approval_setup a,user_passwd b,EROSION_ENTRY c where b.id=a.USER_ID ".where_con_using_array($userIdArr,0,'a.USER_ID')."";
   //echo $sql;die();
    $sql_res=sql_select($sql);
	foreach($sql_res as $row){
		$userName[$row['USER_ID']]=$row['USER_NAME'];
		$userDeg[$row['USER_ID']]=$row['DESIGNATION'];
		if($department_arr[$row['DEPARTMENT']]!=''){$userDep[$row['USER_ID']][$row['DEPARTMENT']]=$department_arr[$row['DEPARTMENT']];}
	}

	$hisSql ="select id,MST_ID,REFUSING_REASON,INSERTED_BY from REFUSING_CAUSE_HISTORY where ENTRY_FORM=66 and MST_ID in(".$erosion_id.") order by INSERTED_BY,id";
    //echo $hisSql;die();
	$hisSqlRes=sql_select($hisSql);
	$refusing_res_arr = [];
	foreach($hisSqlRes as $key => $row){
		$refusing_res_arr[$row['INSERTED_BY']][] = $row['REFUSING_REASON'];
	}

  ?> 

  <div  id="data_panel" align="center" style="width:99%" >
    <fieldset style="width: 100%">
       <table width="100%" cellspacing="0" class="rpt_table" border="0" id="tbl_returnable_details" rules="all">
         <thead>
			<tr>
				<th width="25">Sl</th>
				<th width="80">Department</th>
				<th width="80">Name</th>
				<th width="100">Designation</th>
				<th width="80" >Comment</th>
				<th width="80">Approve/Reject</th>
				<th width="80">Time/Date</th>
			</tr>
           
        </thead>
		  <?
		  $appStatusArr=[1=>'Full Approved',2=>'Deny',3=>'Partial',0=>'Pending'];
		  $i=1;
		  foreach ($sys_id_arr as $user_id => $userRow) 
		  { $sl=0;
			foreach ($userRow as $row) 
			{  
				//$row[csf('APPROVED_DATE')] = strtotime($row[csf('APPROVED_DATE')]);
				$bgcolor = ($i%2==0)?"#fff":"#E9F3FF";
			?>  

				<tr  bgColor="<?= $bgcolor;?>">
					<td align="center"><?=$i;?></td>
					<td><p><?=implode(', ',$userDep[$row['APPROVED_BY']]);?></p></td> 
					<td><?=$userName[$row['APPROVED_BY']];?></td>
					<td align="center"><?=$designation_a[$userDeg[$row['APPROVED_BY']]];?></td>
					<td align="center"><?= $refusing_res_arr[$user_id][$sl];?></td> 
					<td align="center"><?=$appStatusArr[$row['APPROVED']];?></td> 
					<td align="center"><?=$row['APPROVED_DATE'];?></td> 
					
				</tr>
				<?
				$sl++;
				$i++;
				}
			}
			?>
      </table>
    </fieldset>

     
  </div>
    <?
exit(); 
 
}

if($action=="show_image")
{
	echo load_html_head_contents("Image View", "../../../", 1, 1,'','','');
	extract($_REQUEST);	
    ?>
	<fieldset style="width:600px; margin-left:5px">
		<div style="width:100%; word-wrap:break-word" id="scroll_body">
             <table border="0" rules="all" width="100%" cellpadding="2" cellspacing="2">
             	<tr>
					<?
					$i=0;
                    $sql="select image_location from common_photo_library where master_tble_id='$quotation_no' and form_name='quotation_entry' and file_type=1";
                    $result=sql_select($sql);
                    foreach($result as $row)
                    {
						$i++;
                        ?>
                    	<td align="center"><img width="300px" height="180px" src="../../../<? echo $row[csf('image_location')];?>" /></td>
                        <?
						if($i%2==0) echo "</tr><tr>";
                    }
                    ?>
                </tr>
            </table>
        </div>	
	</fieldset>     
	<?
	exit();
}

?>