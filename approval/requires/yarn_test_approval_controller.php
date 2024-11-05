<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];
include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];
$user_id=$_SESSION['logic_erp']['user_id'];


$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');


if($action=='user_popup')
{
	echo load_html_head_contents("Popup Info","../../",1, 1,'',1,'');
	?>
	<script>

	// flowing script for multy select data---------------------------------------start;
	  function js_set_value(id)
	  {
		document.getElementById('selected_id').value=id;
		parent.emailwindow.hide();
	  }
	// avobe script for multy select data---------------------------end;
	</script>
	<form>
        <input type="hidden" id="selected_id" name="selected_id" />
       	<?php
		$custom_designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');
		$Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');	;
	
		$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and valid=1 and a.id!=$user_id  and b.is_deleted=0  and b.page_id=$menu_id";
		$arr=array (2=>$custom_designation,3=>$Department);
		echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
		?> 
	</form>
	<script language="javascript" type="text/javascript">
	  setFilterGrid("tbl_style_ref");
	</script> 
	<?
	exit();
}

if ($action=="load_supplier_dropdown")
{
	$data = explode('_',$data);	
	echo create_drop_down( "cbo_supplier_id", 120,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type=2 and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
	exit();
}



if($action=="report_generate")
{  

	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_supplier_id=str_replace("'","",$cbo_supplier_id);
	$cbo_get_upto=str_replace("'","",$cbo_get_upto);
	$txt_date=str_replace("'","",$txt_date);
	$txt_lot_no=str_replace("'","",$txt_lot_no);
	$cbo_yarn_count=str_replace("'","",$cbo_yarn_count);
	$cbo_yarn_type=str_replace("'","",$cbo_yarn_type);
	$txt_product_id=str_replace("'","",$txt_product_id);
	$approval_type=str_replace("'","",$cbo_approval_type);
	
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if($txt_alter_user_id!=""){$app_user_id=$txt_alter_user_id;}
	else{$app_user_id=$user_id;}

	$count_arr=return_library_array( "select id,yarn_count from lib_yarn_count where is_deleted = 0 AND status_active = 1 ORDER BY yarn_count ASC",'id','yarn_count');
	$supplierArr = return_library_array("select id, SUPPLIER_NAME from lib_supplier", "id", "SUPPLIER_NAME");
	$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');

	//-------------------------------------------------

   

	if($txt_date!="")
	{
		$txt_date=change_date_format($txt_date,"yyyy-mm-dd","-",1);
		if($cbo_get_upto==1) $whereCon.=" and a.TEST_DATE>'".$txt_date."'";
		else if($cbo_get_upto==2) $whereCon.=" and a.TEST_DATE<='".$txt_date."'";
		else if($cbo_get_upto==3) $whereCon.=" and a.TEST_DATE='".$txt_date."'";
	}
	// echo $date_cond;die;
	
	if($cbo_supplier_id){$whereCon.=" and b.SUPPLIER_ID=$cbo_supplier_id";}
	if($txt_lot_no!=''){$whereCon.=" and a.LOT_NUMBER like('%$txt_lot_no')";}
	if($cbo_yarn_type){$whereCon.=" and b.YARN_TYPE=$cbo_yarn_type";}
	if($cbo_yarn_count){$whereCon.=" and b.YARN_COUNT_ID=$cbo_yarn_count";}
	if($txt_product_id){$whereCon.=" and a.PROD_ID=$txt_product_id";}
	
//echo $whereCon;
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$app_user_id and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0","seq");
    $is_not_last_user = return_field_value("max(sequence_no)","electronic_approval_setup","page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0 and company_id=$cbo_company_name");

	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Fabric Booking.</font>";
		die;
	}

	

	
	if($approval_type==0)
	{

		$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted=0 and company_id=$cbo_company_name");
		
		if($user_sequence_no==$min_sequence_no) // first approval authority
		{
			$sql="SELECT b.yarn_comp_percent1st,b.yarn_comp_percent2nd,b.yarn_comp_type1st,b.yarn_comp_type2nd,a.ID, a.COMPANY_ID, a.TEST_DATE, a.PROD_ID, a.LOT_NUMBER, b.YARN_COUNT_ID, a.APPROVED,a.COLOR,b.YARN_TYPE,b.SUPPLIER_ID,b.PRODUCT_NAME_DETAILS from inv_yarn_test_mst a,product_details_master b
			where b.id=a.prod_id and a.COMPANY_ID=$cbo_company_name $whereCon and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved=1 and a.approved < 1";
			//echo $sql;die;

		}
		else if($sequence_no=="")
		{
			$sequence_no_by=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no", "electronic_approval_setup","page_id=$menu_id and sequence_no<$user_sequence_no and bypass=1 and is_deleted=0 and COMPANY_ID=$cbo_company_name","sequence_no");
		

			$prev_user_app_mst_id=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as mst_id","inv_yarn_test_mst a, approval_history b","a.id=b.mst_id and a.COMPANY_ID=$cbo_company_name  and b.sequence_no in ($sequence_no_by) and b.entry_form=58 and b.current_approval_status=1","mst_id");
			$prev_user_app_mst_id=implode(",",array_unique(explode(",",$prev_user_app_mst_id)));


			$booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as mst_id","inv_yarn_test_mst a,
			approval_history b","a.id=b.mst_id and a.COMPANY_ID=$cbo_company_name and b.sequence_no=$user_sequence_no and b.entry_form=58 and
			b.current_approval_status=1","mst_id");
			$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
			
			$result=array_diff(explode(',',$prev_user_app_mst_id),explode(',',$booking_id_app_byuser));
			$booking_id=implode(",",$result);
 			$booking_id_cond="";
			if($booking_id_app_byuser!="") $booking_id_cond=" and a.id not in($booking_id_app_byuser)";
			
 			
			
			$sql="SELECT b.yarn_comp_percent1st,b.yarn_comp_percent2nd,b.yarn_comp_type1st,b.yarn_comp_type2nd,a.ID, a.COMPANY_ID, a.TEST_DATE, a.PROD_ID, a.LOT_NUMBER, b.YARN_COUNT_ID, a.APPROVED,a.COLOR,b.YARN_TYPE,b.SUPPLIER_ID,b.PRODUCT_NAME_DETAILS from inv_yarn_test_mst a,product_details_master b
			where b.id=a.prod_id and a.COMPANY_ID=$cbo_company_name $whereCon and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved=1 and a.approved in(0,3) $booking_id_cond";
			
			
			
		}
		else // bypass No
		{

			$user_sequence_no=$user_sequence_no-1;

			$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no", "electronic_approval_setup","page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and is_deleted=0","sequence_no");
 
			if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$user_sequence_no'";
			else $sequence_no_cond=" and (c.sequence_no='$sequence_no' or c.sequence_no in ($sequence_no_by_pass))";
			
			$sql="SELECT b.yarn_comp_percent1st,b.yarn_comp_percent2nd,b.yarn_comp_type1st,b.yarn_comp_type2nd,a.ID, a.COMPANY_ID, a.TEST_DATE, a.PROD_ID, a.LOT_NUMBER, b.YARN_COUNT_ID, a.APPROVED,a.COLOR,b.YARN_TYPE,b.SUPPLIER_ID,b.PRODUCT_NAME_DETAILS from inv_yarn_test_mst a,product_details_master b, approval_history c
			where b.id=a.prod_id and a.id=c.mst_id and c.entry_form=58 and a.COMPANY_ID=$cbo_company_name $whereCon and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved=1 and a.approved<>1 and c.current_approval_status=1 $sequence_no_cond";
           
		}
	}
	else
	{
		$sequence_no_cond=" and c.approved_by='$app_user_id'";
 		$sql="SELECT b.yarn_comp_percent1st,b.yarn_comp_percent2nd,b.yarn_comp_type1st,b.yarn_comp_type2nd,a.ID,c.id as APP_HIS_ID, a.COMPANY_ID, a.TEST_DATE, a.PROD_ID, a.LOT_NUMBER, b.YARN_COUNT_ID, a.APPROVED,a.COLOR,b.YARN_TYPE,b.SUPPLIER_ID,b.PRODUCT_NAME_DETAILS from inv_yarn_test_mst a,product_details_master b,approval_history c
			where b.id=a.prod_id and a.id=c.mst_id and a.COMPANY_ID=$cbo_company_name $whereCon and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved=1 and a.approved in(1,3)  and c.entry_form=58 and c.CURRENT_APPROVAL_STATUS=1 $sequence_no_cond";
	}
		
		
     $sqlResult=sql_select( $sql );
	
	  // echo $sql;
	
	//$submittedByArr=return_library_array( "select id, user_full_name from user_passwd ",'id','user_full_name');

	?> 
 <script>
		function fnc_print(company_id,prod_id)
		{ 
			var data=company_id + '*' + prod_id;

				print_report( data, "yarn_test_report2", "requires/yarn_test_approval_controller" )
				return;
		
			
		}


	
</script>
    
   
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:950px; margin-top:10px">
        <legend>Yarn Work Order Approval</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="921" class="rpt_table" >
                <thead>
                	<th width="35">&nbsp;</th>
                    <th width="30">SL</th>
                    <th width="70">Prod.ID</th>
                    <th width="120">Lot</th>
                    <th width="70">Count</th>
                    <th width="150">Composition</th>
                    <th width="100">Color</th>
                    <th width="80">Yarn Type</th>
                    <th>Supplier</th>
                </thead>
            </table>
            <div style="width:920px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="left">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
                            $i=1;
                            
                            foreach ($sqlResult as $row)
                            {
								$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
	                                
								
								$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
								if ($row[csf("yarn_comp_type2nd")] != 0)
									$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";
											
								
								
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                	<td width="35" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="yarn_test_id_<? echo $i;?>" name="yarn_test_id[]" type="hidden" value="<?= $row[ID]; ?>" />
                                        
										<input id="approval_id_<?=$i;?>" name="approval_id[]" type="hidden" value="<?=$value=$row[ID].'**'.$row[APP_HIS_ID]; ?>" />
                                    </td>
									<td width="30" align="center"><? echo $i; ?></td>
									<td width="70"><?= $row[PROD_ID]; ?></td>
                                    <!-- <td width="120"><//?= //$row[LOT_NUMBER]; ?></td> -->
                                    <td width="120"><p><a  href="##"  onClick="fnc_print(<?php echo $row[COMPANY_ID]?>,'<?= $row[PROD_ID]; ?>')" ><?= $row[LOT_NUMBER]; ?></a></p></td>
									<td width="70"><?= $count_arr[$row[YARN_COUNT_ID]];?></td>
									<td width="150"><?= $compositionDetails; ?></td>
                                    <td width="100"><?= $color_name_arr[$row[COLOR]]; ?></td>
                                    <td width="80"><?= $yarn_type[$row[YARN_TYPE]]; ?></td>
                                    <td><?= $supplierArr[$row[SUPPLIER_ID]]; ?></td>
								</tr>
								<?
								$i++;
							}

							$denyBtn="";
							if($approval_type==0) $denyBtn=""; else $denyBtn=" display:none";
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="920" class="rpt_table">
				<tfoot>
                    <td width="35" align="center" ><input type="checkbox" id="all_check" onClick="check_all('all_check')" /></td>
                    <td colspan="2" align="left">
                    <input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<?=$i; ?>,<?=$approval_type; ?>);"/>&nbsp;&nbsp;&nbsp;
                    <input type="button" value="<? if($approval_type==0) echo "Deny"; ?>" class="formbutton" style="width:100px;display:none;<?=$denyBtn; ?>" onClick="submit_approved(<?=$i; ?>,5);"/>
                    </td>
				</tfoot>
			</table>
        </fieldset>
    </form>
	<?
	exit();
}

if ($action == "yarn_test_report2")
{
	extract($_REQUEST);
	//print_r($_REQUEST);
	echo load_html_head_contents("Yarn Test Report", "../../../../", 1, 1, '', '', '');
	list($company_ID, $prod_ID) = explode("*", $data);
	
	$yarn_test_for_arr = array(1=>'Bulk Yarn',2=>'Sample Yarn');
	$yarn_test_result_arr = array(1=>'Nil',2=>'Major',3=>'Minor');
	$yarn_test_acceptance_arr = array(1=>'Yes',2=>'No');
	$comments_acceptance_arr = array(1=>'Acceptable',2=>'Special',3=>'Consideration',4=>'Not Acceptable');
	$phys_test_knitting_arr = array(1=>'Stripe(Patta)', 2=>'Thick & Thin Yarn', 3=>'Neps', 4=>'Poly-Propaline(Plastic Conta)', 5=>'Color Conta/Yarn', 6=>'Dead Fiber', 7=>'No Of Slub', 8=>'No Of Hole', 9=>'No Of Slub Hole', 10=>'Moisture Efect', 11=>'No Of Yarn Breakage', 12=>'No Of Setup', 13=>'Knotting End', 14=>'Haireness', 15=>'Hand Feel', 16=>'Twisting', 17=>'Contamination', 18=>'Foregin Fiber', 19=>'Oil Stain Yarn', 20=>'Foreign Matters', 21=>'Unlevel', 22=>'Double Yarn', 23=>'Fiber Migration', 24=>'Excessive Hard Yarn');
	//asort($phys_test_knitting_arr);
	$phys_test_dyeing_and_finishing_arr = array(1=>'Stripe(Patta)', 2=>'Thick & Thin Yarn', 3=>'Neps', 4=>'Color Conta', 5=>'Dead Fiber/Cotton', 6=>'No Of Slub', 7=>'No Of Hole', 8=>'No Of Slub Hole', 9=>'Moisture Efect', 10=>'Shrinkage', 11=>'Dye Pick Up%', 12=>'Enzyme Dosting %', 13=>'Knotting End', 14=>'Haireness', 15=>'Hand Feel', 16=>'Contamination', 17=>'Soft Yarn/Loose Yarn', 18=>'Oil Stain Yarn', 19=>'Bad Piecing', 20=>'Oily Slub', 21=>'Foreign Matters', 22=>'Black Specks Test', 23=>'Cotton Seeds Test', 24=>'Bursting', 25=>'Pilling', 26=>'Lustre', 27=>'Process loss %');
	//asort($phys_test_dyeing_and_finishing_arr);
	
	$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
    $product_name_details = return_field_value("product_name_details","product_details_master","id=$prod_ID");
    $lot_number = return_field_value("lot","product_details_master","id=$prod_ID");
   

    $sql_brand="select a.id, a.brand_name from lib_brand a, product_details_master b where a.id=b.brand and b.id=$prod_ID";
    //echo $sql_brand; die;
    $sql_brand_name=sql_select($sql_brand);
    $brand_name=$sql_brand_name[0][csf('brand_name')];

    
	?>

	<style type="text/css">
        /* .a4size {
           width: 21cm;
           height: 26.7cm;
           font-family: Cambria, Georgia, serif;
        } */
     /*   @media print {
               table {border-spacing: 0px; padding: 0px;}
				td th{padding: 0px;}
        size: A4 portrait;
        }*/
        .rpt_table2 td{border: 1px solid #8bAF00;}
    </style>
	<table style="width: 1300px;" align="center">
	    <tr><td align="center" style="font-size:xx-large"><strong><? echo $company_arr[$company_ID]; ?></strong></td></tr>
	    <tr>
	    	<td align="center" style="font-size: 16px;">
	    		<?
				$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$company_ID");
				foreach ($nameArray as $result)
				{
					?>
					<? echo $result[csf('plot_no')]; ?>
					<? echo $result[csf('level_no')]; ?>
					<? echo $result[csf('road_no')]; ?>
					<? echo $result[csf('block_no')];?>
					<? echo $result[csf('city')]; ?>
					<? echo $result[csf('zip_code')]; ?>
					<? echo $result[csf('province')]; ?>
					<? echo $country_arr[$result[csf('country_id')]]; ?><br>
					<? echo $result[csf('email')]; ?>
					<? echo $result[csf('website')];
				}
				?>
	    	</td>
	    </tr>
	    <tr><td align="center" style="font-size: 25px;">Assessment of Numerical Test &amp; Physical Inspection Report</td></tr>
	    <tr><td align="center" style="font-size: 25px;">Yarn Test Report</td></tr>
	</table>
	<br/>

	<div style="font-size: 25px; margin-left: 5px;" title='Product ID=<? echo $prod_ID ;?> and Lot Number=<? echo $lot_number ;?>'><strong>Product Details: <? echo $prod_ID.', '.$lot_number.', '.$brand_name.', '.$product_name_details; ?></strong></div><br>
	<div style="margin-left: 5px;">
		<?
		$sql_mst_comments = "select a.id, a.company_id, a.prod_id, a.lot_number, a.test_date, a.test_for, a.specimen_wgt, a.specimen_length, a.color, a.receive_qty, a.lc_number, a.lc_qty, a.actual_yarn_count, a.actual_yarn_count_phy, a.yarn_apperance_grad, a.yarn_apperance_phy, a.actual_yarn_comp, a.actual_yarn_comp_phy, a.pilling, a.pilling_phy, a.brusting, a.brusting_phy, a.twist_per_inc, a.twist_per_inc_phy, a.moisture_content, a.moisture_content_phy, a.ipi_value, a.ipi_value_phy, a.csp_minimum, a.csp_minimum_phy, a.csp_actual, a.csp_actual_phy, a.thin_yarn, a.thin_yarn_phy, a.thick, a.thick_phy, a.u, a.u_phy, a.cv, a.cv_phy, a.neps_per_km, a.neps_per_km_phy, a.heariness, a.heariness_phy, a.counts_cv, a.system_result, a.counts_cv_phy, a.grey_gsm, a.grey_wash_gsm, a.required_gsm, a.required_dia, a.machine_dia, a.stich_length, a.grey_gsm_dye, a.batch, a.finish_gsm, a.finish_dia, a.length, a.width, b.comments_knit_acceptance, b.comments_knit, b.comments_dye_acceptance, b.comments_dye, b.comments_author_acceptance, b.comments_author from inv_yarn_test_mst a, inv_yarn_test_comments b where a.id=b.mst_table_id and a.prod_id=$prod_ID and a.company_id=$company_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id desc";
		$sql_mst_comments_rslt = sql_select($sql_mst_comments);
		$yarn_info_arr = array();

		foreach ($sql_mst_comments_rslt as $value)
		{
			$attribute = array('id', 'company_id', 'prod_id', 'lot_number', 'test_date', 'test_for', 'specimen_wgt', 'specimen_length', 'color', 'receive_qty', 'lc_number', 'lc_qty', 'actual_yarn_count', 'actual_yarn_count_phy', 'yarn_apperance_grad', 'yarn_apperance_phy', 'actual_yarn_comp', 'actual_yarn_comp_phy', 'pilling', 'pilling_phy', 'brusting', 'brusting_phy', 'twist_per_inc', 'twist_per_inc_phy', 'moisture_content', 'moisture_content_phy', 'ipi_value', 'ipi_value_phy', 'csp_minimum', 'csp_minimum_phy', 'csp_actual', 'csp_actual_phy', 'thin_yarn', 'thin_yarn_phy', 'thick', 'thick_phy', 'u', 'u_phy', 'cv', 'cv_phy', 'neps_per_km', 'neps_per_km_phy', 'heariness', 'heariness_phy', 'counts_cv', 'system_result', 'counts_cv_phy');

			foreach ($attribute as $attr)
			{
				$yarn_info_arr[$value[csf('id')]][$attr] = $value[csf($attr)];
			}
		}

		$sql_dtls = "select a.id, a.color, a.grey_gsm, a.grey_wash_gsm, a.required_gsm, a.required_dia, a.machine_dia, a.stich_length, a.grey_gsm_dye, a.batch, a.finish_gsm, a.finish_dia, a.length, a.width, b.id as dtls_id, b.testing_parameters_id, b.fab_type, b.testing_parameters, b.fabric_point, b.result, b.acceptance, b.fabric_class, b.remarks from inv_yarn_test_mst a, inv_yarn_test_dtls b where a.id=b.mst_id and a.prod_id=$prod_ID and a.company_id=$company_ID and b.fab_type in(1,2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.color, b.testing_parameters_id";
		$sql_dtls_result = sql_select($sql_dtls);
		$color_range_arr = array();
		$knit_mstdata_arr=array();
		$color_knit_dtls_arr=array();
		$color_dye_dtls_arr=array();
		$dtls_data_arr = array();
		foreach ($sql_dtls_result as $value)
		{
			$color_range_arr[$value[csf('id')]]['color'] = $value[csf('color')];
			$attribute_mst = array('grey_gsm', 'grey_wash_gsm', 'required_gsm', 'required_dia', 'machine_dia', 'stich_length', 'grey_gsm_dye', 'batch', 'finish_gsm', 'finish_dia', 'length', 'width');
			foreach ($attribute_mst as $attr)
			{
				$knit_mstdata_arr[$value[csf('id')]][$attr] = $value[csf($attr)];
			}

			$color_knit_dtls_arr[$value[csf('fab_type')]][$value[csf('id')]]['dtls_id'] .= $value[csf('dtls_id')].',';
			$color_dye_dtls_arr[$value[csf('fab_type')]][$value[csf('id')]]['dtls_id'] .= $value[csf('dtls_id')].',';

			//$dtls_data_arr[$value[csf('dtls_id')]]['testing_parameters']=$value[csf('testing_parameters')];
			$dtls_data_arr[$value[csf('dtls_id')]]['testing_parameters_id']=$value[csf('testing_parameters_id')];
			$dtls_data_arr[$value[csf('dtls_id')]]['fabric_point']=$value[csf('fabric_point')];
			$dtls_data_arr[$value[csf('dtls_id')]]['result']=$value[csf('result')];
			$dtls_data_arr[$value[csf('dtls_id')]]['acceptance']=$value[csf('acceptance')];
			$dtls_data_arr[$value[csf('dtls_id')]]['fabric_class']=$value[csf('fabric_class')];
			$dtls_data_arr[$value[csf('dtls_id')]]['remarks']=$value[csf('remarks')];
		}
		?>
	    <table cellspacing="1" cellpadding="1" class="rpt_table2" rules="all" style="border: 2px solid black;">
	        <caption style="background-color: #dbb768; font-weight: bold; text-align: left; border-top: 2px solid black; border-right: 2px solid black; border-left: 2px solid black;">Basic Yarn Information</caption>
	        <tr>
	            <td width="50"><b>1</b></td>
	            <td width="150"><b>Color Range</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="121" style="border-left: 2px solid black;"><b><? echo $color_range[$value['color']]; ?></b></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
	            <td width="50"><b>2</b></td>
	            <td width="150"><b>Test For</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="121" style="border-left: 2px solid black;"><? echo $yarn_test_for_arr[$value['test_for']]; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
	            <td width="50"><b>3</b></td>
	            <td width="150"><b>Specimen Weight</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="121" style="border-left: 2px solid black;"><? if($value['specimen_wgt']==0) echo ''; else echo $value['specimen_wgt']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
	            <td width="50"><b>4</b></td>
	            <td width="150"><b>Specimen Length</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="121" style="border-left: 2px solid black;"><? if($value['specimen_length']==0) echo ''; else echo $value['specimen_length']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
	            <td width="50"><b>5</b></td>
	            <td width="150"><b>Receive Quantity</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="121" style="border-left: 2px solid black;"><? if($value['receive_qty']==0) echo ''; else echo $value['receive_qty']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
	            <td width="50"><b>6</b></td>
	            <td width="150"><b>LC Quantity</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="121" style="border-left: 2px solid black;"><? if($value['lc_qty']==0) echo ''; else echo $value['lc_qty']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
	            <td width="50"><b>7</b></td>
	            <td width="150"><b>LC Number</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="121" style="border-left: 2px solid black;"><? echo $value['lc_number']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
	            <td width="50"><b>8</b></td>
	            <td width="150"><b>Test Date</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="121" style="border-left: 2px solid black;"><? echo $value['test_date']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	    </table>
	    <br>
	    <table cellspacing="1" cellpadding="1" class="rpt_table2" rules="all" style="border: 2px solid black;">
	        <caption style="background-color: #dbb768; font-weight: bold; text-align: left; border-top: 2px solid black; border-right: 2px solid black; border-left: 2px solid black;">Numerical Test</caption>
	        <tr style="background-color: #ddb;">
	        	<td colspan="2"></td>
	        	<?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	        		<td style="border-left: 2px solid black;"><b>Require</b></td>
	        		<td><b>Physical</b></td>
	        		<?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>1</b></td>
	            <td width="150"><b>Actual Yarn Count</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['actual_yarn_count']==0) echo ''; else echo $value['actual_yarn_count']; ?></td>
	                    <td width="60"><? if($value['actual_yarn_count_phy']==0) echo ''; else echo $value['actual_yarn_count_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>2</b></td>
	            <td width="150"><b>Yarn Apperance (Grade)</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['yarn_apperance_grad']=='') echo ''; else echo $value['yarn_apperance_grad']; ?></td>
	                    <td width="60"><? if($value['yarn_apperance_phy']=='') echo ''; else echo $value['yarn_apperance_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>3</b></td>
	            <td width="150"><b>Twist Per Inch (TPI)</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['twist_per_inc']==0) echo ''; else echo $value['twist_per_inc']; ?></td>
	                    <td width="60"><? if($value['twist_per_inc_phy']==0) echo ''; else echo $value['twist_per_inc_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>4</b></td>
	            <td width="150"><b>Moisture Content</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['moisture_content']==0) echo ''; else echo $value['moisture_content']; ?></td>
	                    <td width="60"><? if($value['moisture_content_phy']==0) echo ''; else echo $value['moisture_content_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>5</b></td>
	            <td width="150"><b>IPI Value (Uster)</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['ipi_value']==0) echo ''; else echo $value['ipi_value']; ?></td>
	                    <td width="60"><? if($value['ipi_value_phy']==0) echo ''; else echo $value['ipi_value_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>6</b></td>
	            <td width="150"><b>CSP Minimum</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['csp_minimum']==0) echo ''; else echo $value['csp_minimum']; ?></td>
	                    <td width="60"><? if($value['csp_minimum_phy']==0) echo ''; else echo $value['csp_minimum_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>7</b></td>
	            <td width="150"><b>CSP Actua</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['csp_actual']==0) echo ''; else echo $value['csp_actual']; ?></td>
	                    <td width="60"><? if($value['csp_actual_phy']==0) echo ''; else echo $value['csp_actual_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>8</b></td>
	            <td width="150"><b>Thin Yarn</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['thin_yarn']==0) echo ''; else echo $value['thin_yarn']; ?></td>
	                    <td width="60"><? if($value['thin_yarn_phy']==0) echo ''; else echo $value['thin_yarn_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>9</b></td>
	            <td width="150"><b>Thick</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['thick']==0) echo ''; else echo $value['thick']; ?></td>
	                    <td width="60"><? if($value['thick_phy']==0) echo ''; else echo $value['thick_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>10</b></td>
	            <td width="150"><b>U %</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['u']==0) echo ''; else echo $value['u']; ?></td>
	                    <td width="60"><? if($value['u_phy']==0) echo ''; else echo $value['u_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>11</b></td>
	            <td width="150"><b>CV %</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['cv']==0) echo ''; else echo $value['cv']; ?></td>
	                    <td width="60"><? if($value['cv_phy']==0) echo ''; else echo $value['cv_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>12</b></td>
	            <td width="150"><b>Neps Per KM</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['neps_per_km']==0) echo ''; else echo $value['neps_per_km']; ?></td>
	                    <td width="60"><? if($value['neps_per_km_phy']==0) echo ''; else echo $value['neps_per_km_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>13</b></td>
	            <td width="150"><b>Heariness %</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['heariness']==0) echo ''; else echo $value['heariness']; ?></td>
	                    <td width="60"><? if($value['heariness_phy']==0) echo ''; else echo $value['heariness_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>14</b></td>
	            <td width="150"><b>Counts CV %</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['counts_cv']==0) echo ''; else echo $value['counts_cv']; ?></td>
	                    <td width="60"><? if($value['counts_cv_phy']==0) echo ''; else echo $value['counts_cv_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>15</b></td>
	            <td width="150"><b>Actual Yarn Composition</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['actual_yarn_comp']=='') echo ''; else echo $value['actual_yarn_comp']; ?></td>
	                    <td width="60"><? if($value['actual_yarn_comp_phy']=='') echo ''; else echo $value['actual_yarn_comp_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>16</b></td>
	            <td width="150"><b>Pilling Test</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['pilling']=='') echo ''; else echo $value['pilling']; ?></td>
	                    <td width="60"><? if($value['pilling_phy']=='') echo ''; else echo $value['pilling_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>17</b></td>
	            <td width="150"><b>Brusting Test</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['brusting']=='') echo ''; else echo $value['brusting']; ?></td>
	                    <td width="60"><? if($value['brusting_phy']=='') echo ''; else echo $value['brusting_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>18</b></td>
	            <td width="150"><b>System Result</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td colspan="2" width="120" style="border-left: 2px solid black;"><? echo $value['system_result']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	    </table>
	</div>
	<br>
	<div style="margin-left: 5px;">
		i am here 
    	<table>
    		<?
			foreach ($color_range_arr as $mst_id => $color_val)
			{
				$knit_dtls=array_unique(explode(",",rtrim($color_knit_dtls_arr[1][$mst_id]['dtls_id'],',')));
				$dye_dtls=array_unique(explode(",",rtrim($color_dye_dtls_arr[2][$mst_id]['dtls_id'],',')));

				$grey_gsm=$knit_mstdata_arr[$mst_id]['grey_gsm'];
				if($grey_gsm==0) $grey_gsm='';
				$grey_wash_gsm=$knit_mstdata_arr[$mst_id]['grey_wash_gsm'];
				if($grey_wash_gsm==0) $grey_wash_gsm='';
				$required_gsm=$knit_mstdata_arr[$mst_id]['required_gsm'];
				if($required_gsm==0) $required_gsm='';
				$required_dia=$knit_mstdata_arr[$mst_id]['required_dia'];
				if($required_dia==0) $required_dia='';
				$machine_dia=$knit_mstdata_arr[$mst_id]['machine_dia'];
				if($machine_dia==0) $machine_dia='';
				$stich_length=$knit_mstdata_arr[$mst_id]['stich_length'];
				if($stich_length==0) $stich_length='';

				$grey_gsm_dye=$knit_mstdata_arr[$mst_id]['grey_gsm_dye'];
				if($grey_gsm_dye==0) $grey_gsm_dye='';
				$finish_gsm=$knit_mstdata_arr[$mst_id]['finish_gsm'];
				if($finish_gsm==0) $finish_gsm='';
				$finish_dia=$knit_mstdata_arr[$mst_id]['finish_dia'];
				if($finish_dia==0) $finish_dia='';
				$length=$knit_mstdata_arr[$mst_id]['length'];
				if($length==0) $length='';
				$width=$knit_mstdata_arr[$mst_id]['width'];
				if($width==0) $width='';
				?>
	        	<tr>
	        		<td style="vertical-align: top;">
		                <table cellspacing="1" cellpadding="1" class="rpt_table2" rules="all" border="1" style="width: 660px; margin-right: 20px; border: 2px solid black;">
		                    <caption style="background-color: #dbb768; font-weight: bold; border-top: 2px solid black; border-left: 2px solid black; border-right: 2px solid black;">Knitting For <? echo $color_range[$color_val['color']]; ?></caption>
		                    <tr>
		                        <td width="80"><b>Gray GSM</b></td>
		                        <td width="80"><? echo $grey_gsm; ?></td>
		                        <td width="80" rowspan="2"></td>
		                        <td width="95"><b>Gray Wash GSM</b></td>
		                        <td width="80"><? echo $grey_wash_gsm; ?></td>
		                        <td width="80" rowspan="2"></td>
		                        <td width="80"><b>Required GSM</b></td>
		                        <td><? echo $required_gsm; ?></td>
		                    </tr>
		                    <tr>
		                        <td width="80"><b>Required Dia</b></td>
		                        <td width="80"><? echo $required_dia; ?></td>
		                        <td width="95"><b>Machine Dia</b></td>
		                        <td width="80"><? echo $machine_dia; ?></td>
		                        <td width="80"><b>Stich Length</b></td>
		                        <td><? echo $stich_length; ?></td>
		                    </tr>
		                    <tr style="background-color: #ddb;">
		                    	<td width="160" colspan="2"><b>Testing Parameters</b></td>
		                    	<td width="80"><b>Point</b></td>
		                    	<td width="95"><b>Result</b></td>
		                    	<td width="80"><b>Acceptance</b></td>
		                    	<td width="80"><b>Fabric Class</b></td>
		                    	<td colspan="2"><b>Remarks</b></td>
		                    </tr>
		                    <?
							foreach ($knit_dtls as $row)
							{
		                        ?>
			                    <tr>
			                    	<td width="160" colspan="2"><strong><? echo $phys_test_knitting_arr[$dtls_data_arr[$row]['testing_parameters_id']]; ?></strong></td>
			                    	<td width="80"><? if($dtls_data_arr[$row]['fabric_point']==0) echo ''; else echo $dtls_data_arr[$row]['fabric_point']; ?></td>
			                    	<td width="95"><? echo $yarn_test_result_arr[$dtls_data_arr[$row]['result']]; ?></td>
			                    	<td width="80"><? echo $yarn_test_acceptance_arr[$dtls_data_arr[$row]['acceptance']]; ?></td>
			                    	<td width="80"><? echo $dtls_data_arr[$row]['fabric_class']; ?></td>
			                    	<td colspan="2"><? echo $dtls_data_arr[$row]['remarks']; ?></td>
			                    </tr>
			                    <?
			                }
			                ?>
		                </table>
		            </td>
		            <td style="vertical-align: top;">
		                <table cellspacing="1" cellpadding="1" class="rpt_table2" rules="all" border="1" style="width: 650px; margin-right: 20px; border: 2px solid black;">
		                    <caption style="background-color: #dbb768; font-weight: bold; border-top: 2px solid black; border-left: 2px solid black; border-right: 2px solid black;">Dyeing For <? echo $color_range[$color_val['color']]; ?></caption>
		                    <tr>
		                        <td width="80"><b>Gray GSM</b></td>
		                        <td width="80"><? echo $grey_gsm_dye; ?></td>
		                        <td width="80" rowspan="2"></td>
		                        <td width="80"><b>Batch</b></td>
		                        <td width="80"><? echo $knit_mstdata_arr[$mst_id]['batch']; ?></td>
		                        <td width="80" rowspan="2"></td>
		                        <td width="80"><b>Finish GSM</b></td>
		                        <td><? echo $finish_gsm; ?></td>
		                    </tr>
		                    <tr>
		                        <td width="80"><b>Finish Dia</b></td>
		                        <td width="80"><? echo $finish_dia; ?></td>
		                        <td width="80"><b>Length %</b></td>
		                        <td width="80"><? echo $length; ?></td>
		                        <td width="80"><b>Width</b></td>
		                        <td><? echo $width; ?></td>
		                    </tr>
		                    <tr style="background-color: #ddb;">
		                    	<td width="160" colspan="2"><b>Testing Parameters</b></td>
		                    	<td width="80"><b>Point</b></td>
		                    	<td width="80"><b>Result</b></td>
		                    	<td width="80"><b>Acceptance</b></td>
		                    	<td width="80"><b>Fabric Class</b></td>
		                    	<td colspan="2"><b>Remarks</b></td>
		                    </tr>
		                    <?
							foreach ($dye_dtls as $row)
							{
		                        ?>
		                        <tr>
			                    	<td width="160" colspan="2"><b><? echo $phys_test_dyeing_and_finishing_arr[$dtls_data_arr[$row]['testing_parameters_id']]; ?></b></td>
			                    	<td width="80"><? if($dtls_data_arr[$row]['fabric_point']==0) echo ''; else echo $dtls_data_arr[$row]['fabric_point']; ?></td>
			                    	<td width="80"><? echo $yarn_test_result_arr[$dtls_data_arr[$row]['result']]; ?></td>
			                    	<td width="80"><? echo $yarn_test_acceptance_arr[$dtls_data_arr[$row]['acceptance']]; ?></td>
			                    	<td width="80"><? echo $dtls_data_arr[$row]['fabric_class']; ?></td>
			                    	<td colspan="2"><? echo $dtls_data_arr[$row]['remarks']; ?></td>
			                    </tr>
			                    <?
			                }
			                ?>
		                </table>
		            </td>
	            </tr>
	            <tr><td></td></tr>
	            <tr><td></td></tr>
	            <tr><td></td></tr>
	            <tr><td></td></tr>
	        	<?
	        }
	        ?>
	    </table>
	</div>
	<br>
    <div style="width: 1320px; margin-left: 5px;">
    	<?
		foreach ($sql_mst_comments_rslt as $row)
		{
    		?>
	    	<table cellspacing="1" cellpadding="1" class="rpt_table2" rules="all" border="1" style="width: 1335px; border: 2px solid black;">
	    		<caption style="background-color: #dbb768; font-weight: bold; border-top: 2px solid black; border-left: 2px solid black; border-right:2px solid black;">Comments For <? echo $color_range[$row[csf('color')]]; ?></caption>
	    		<tr style="font-weight: bold;">
	    			<td width="200" align="center"><b>Department</b></td>
	    			<td width="100" align="center"><b>Acceptance</b></td>
	    			<td align="center"><b>Comments</b></td>
	    		</tr>
	    		<tr>
	    			<td width="200"><b>Comentes For Knitting Dept.</b></td>
	    			<td width="100"><? echo $comments_acceptance_arr[$row[csf('comments_knit_acceptance')]];?></td>
	    			<td><? echo $row[csf('comments_knit')];?></td>
	    		</tr>
	    		<tr>
	    			<td width="200"><b>Comentes For Dyeing/Finishing Dept.</b></td>
	    			<td width="100"><? echo $comments_acceptance_arr[$row[csf('comments_dye_acceptance')]];?></td>
	    			<td><? echo $row[csf('comments_dye')];?></td>
	    		</tr>
	    		<tr>
	    			<td width="200"><b>Comentes For Authorize Dept.</b></td>
	    			<td width="100"><? echo $comments_acceptance_arr[$row[csf('comments_author_acceptance')]];?></td>
	    			<td><? echo $row[csf('comments_author')];?></td>
	    		</tr>
	    	</table>
	    	<br>
	    	<?
			echo signature_table(259, $company_ID, "1030px",'',0);
	    }
	    ?>
    </div>
	<?
}

 
 

if ($action=="approve")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$con = connect();

	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if($txt_alter_user_id!="") $user_id_approval=$txt_alter_user_id;
	else $user_id_approval=$user_id;



	$msg=''; $flag=''; $response='';

	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id_approval and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");



	if($approval_type==0) //Approve button
	{
		$response=$req_nos;

        $is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0","sequence_no");

        if($is_not_last_user ==""){
            $partial_approval=1;
        }else{
            $partial_approval=3;
        }

		//$rID=sql_multirow_update("inv_yarn_test_mst","approved","$partial_approval","id",$yarn_test_id,0);
		//if($rID) $flag=1; else $flag=0;


		$max_approved_no_arr=return_library_array( "select mst_id,max(approved_no) as approved_no from approval_history where mst_id in($yarn_test_id) group by mst_id",'mst_id','approved_no');
	
	
	
		
		$yarn_test_id_arr=explode(",",$yarn_test_id);
		$field_array="id, entry_form, mst_id, approved_no, approved_by, approved_date, sequence_no, current_approval_status, inserted_by, insert_date";
		$id=return_next_id( "id","approval_history", 1 ) ;
		$approved_no_array=array();
		$i=0;
		foreach($yarn_test_id_arr as $val)
		{
			$approved_no=$max_approved_no_arr[$val];
			$approved_no=$approved_no+1;

			if($i!=0) $data_array.=",";
			$data_array.="(".$id.",58,".$val.",".$approved_no.",".$user_id_approval.",'".$pc_date_time."',".$user_sequence_no.",1,".$user_id.",'".$pc_date_time."')";
			$approved_no_array[$val]=$approved_no;
			$id=$id+1;
			$i++;
		}
	    $query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=58 and mst_id in ($yarn_test_id)";
		$rIDapp=execute_query($query,1);
		if($flag==1)
		{
			if($rIDapp) $flag=1; else $flag=0;
		}
 
		$rID=sql_multirow_update("inv_yarn_test_mst","approved","$partial_approval","id",$yarn_test_id,0);
		if($rID) $flag=1; else $flag=0;
 
        $rID2=sql_insert("approval_history",$field_array,$data_array,0);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}

		
		if($flag==1) $msg='19'; else $msg='21';
		
		
	}
	else if($approval_type==1)
	{
		$approved_id_arr=explode(",",$approved_id);
		$test_id_arr=array(); $app_his_id_arr=array();

		foreach($approved_id_arr as $value)
		{
			list($test_id,$app_id) = explode('**',$value);
			$test_id_arr[$test_id]=$test_id;
			$app_his_id_arr[$reqs_id]=$app_id;
		}
		$test_ids=implode(',',$test_id_arr);
		$app_his_ids=implode(',',$app_his_id_arr);
 
		$rID=sql_multirow_update("inv_yarn_test_mst","approved*ready_to_approved","0*0","id",$test_ids,0);
		if($rID) $flag=1; else $flag=0;

		//$data=$user_id_approval."*'".$pc_date_time."'*0*".$user_id."*'".$pc_date_time."'";
		//$rID2=sql_multirow_update("approval_history","un_approved_by*un_approved_date*current_approval_status*updated_by*update_date",$data,"id",$app_his_ids,0);

		
	    $query="UPDATE approval_history SET current_approval_status=0,un_approved_by=$user_id_approval,un_approved_date='$pc_date_time',updated_by=$user_id,update_date='$pc_date_time' WHERE entry_form=58 and mst_id in ($test_ids)";
		$rID2=execute_query($query,1);
		
		
		
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}


		$response=$reqs_ids;

		if($flag==1) $msg='20'; else $msg='22';
	}
	else if($approval_type==5)
	{
		$sqlBookinghistory="select id, mst_id from approval_history where current_approval_status=1 and entry_form=2 and mst_id in ($req_nos) ";
		//echo "10**".$sqlBookinghistory;
		$nameArray=sql_select($sqlBookinghistory); $bookidstr=""; $approval_id="";
		foreach ($nameArray as $row)
		{
			if($bookidstr=="") $bookidstr=$row[csf('mst_id')]; else $bookidstr.=','.$row[csf('mst_id')];
			if($approval_id=="") $approval_id=$row[csf('id')]; else $approval_id.=','.$row[csf('id')];
		}
		
		$appBookNoId=implode(",",array_filter(array_unique(explode(",",$bookidstr))));
		$approval_ids=implode(",",array_filter(array_unique(explode(",",$approval_id))));
		/*$book_ids=count(explode(",",$bookidstr)); $bookingidCond="";
		if($db_type==2 && $book_ids>1000)
		{
			$bookingidCond=" and (";
			$bookingnoIdArr=array_chunk(explode(",",$booknoId),999);
			foreach($bookingnoIdArr as $ids)
			{
				$ids=implode(",",$ids);
				$bookingidCond.=" mst_id in($ids) or"; 
			}
			$bookingidCond=chop($bookingidCond,'or ');
			$bookingidCond.=")";
		}
		else $bookingidCond=" and mst_id in($booknoId)";*/ 
		
		$rID=sql_multirow_update("wo_non_order_info_mst","is_approved*ready_to_approved","0*0","id",$req_nos,0);
		if($rID) $flag=1; else $flag=0;

		//$rID2=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,0);
		if($approval_ids!="")
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=2 and current_approval_status=1 and id in ($approval_ids)";
			//echo "10**".$query;
			$rID2=execute_query($query,1);
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0;
			}
		}
		//echo "10**".$rID.'='.$rID2.'='.$rID4.'='.$flag; die;
		$response=$req_nos;
		if($flag==1) $msg='50'; else $msg='51';
	}

	//echo "10**".$rID.'**'.$rID2.'**'.$rID3.'**'.$rID4.'**'.$app_ids;die;

	if($db_type==0)
	{
		if($flag==1)
		{
			mysql_query("COMMIT");
			echo $msg."**".$response;
		}
		else
		{
			mysql_query("ROLLBACK");
			echo $msg."**".$response;
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($flag==1)
		{
			oci_commit($con);
			echo $msg."**".$response;
		}
		else
		{
			oci_rollback($con);
			echo $msg."**".$response;
		}
	}
	disconnect($con);
	die;
}
?>
