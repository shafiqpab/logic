<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

//--------------------------------------------------------------------------------------------------------------------

if($action=="print_button_variable_setting")
{

    $print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name in($data) and module_id=7 and report_id=154 and is_deleted=0 and status_active=1","format_id","format_id");
    echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
    exit();
}



if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 150, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in($data) order by location_name","id,location_name", 0, "-- Select --", $selected, "",0 );
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	//echo $datediff;
	$cbo_company=str_replace("'","",$cbo_company_id);
	$comapny_id=str_replace("'","",$cbo_company_id);
	//$cbo_working_company=str_replace("'","",$cbo_working_company_id);
	$cbo_location=str_replace("'","",$cbo_location_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$cbo_production_type=str_replace("'","",$cbo_production_type);

	///if($cbo_company==0) $cbo_company_cond=""; else $cbo_company_cond =" and a.company_id in($cbo_company)";
	if($cbo_company==0) $cbo_company_cond1=""; else $cbo_company_cond1 =" and a.inspection_company in($cbo_company)";
	if($cbo_location=="") $cbo_location_cond=""; else $cbo_location_cond =" and a.working_location in($cbo_location)";
	//if($cbo_working_company==0) $company_working_cond=""; else $company_working_cond=" and a.serving_company=$cbo_working_company";

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );

	if($type==3) // show button
	{
		if ($cbo_production_type==1)
		{


			$sql_floor=sql_select("Select a.id, a.floor_name from  lib_prod_floor a where a.location_id=$cbo_location $cbo_company_cond and a.status_active=1 and a.is_deleted=0 order by a.floor_name ");

			$sql_floor_knitting=sql_select("select b.floor_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0  and b.floor_id !=0  group by b.floor_id order by  b.floor_id");

			$sql_floor_dyeing=sql_select("SELECT a.floor_id from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.entry_form=35 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.floor_id !=0 and a.load_unload_id in(2) and a.result=1  group by a.floor_id order by  a.floor_id");

			 $sql_machineID_finish_fab=sql_select("select b.machine_no_id from inv_receive_master a,pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form=7 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.machine_no_id !=0  group by b.machine_no_id order by  b.machine_no_id");
			 $machineIDs="";
			foreach ( $sql_machineID_finish_fab as $row )
			{
				$machineIDs.=$row[csf("machine_no_id")].',';
			}
			$machineIDs= chop($machineIDs,",");
			$sql_floor_finishing_fab=sql_select("select floor_id from lib_machine_name  where id in ($machineIDs)  and is_deleted=0 and status_active=1  group by floor_id order by  floor_id");

			$sql_floor_cutting=sql_select("SELECT a.floor_id,b.floor_serial_no from  pro_garments_production_mst a, lib_prod_floor b where b.id=a.floor_id and a.location=$cbo_location $cbo_company_cond and a.production_type=1 and a.status_active=1 and a.is_deleted=0 and a.floor_id !=0  group by a.floor_id,b.floor_serial_no order by  b.floor_serial_no ");

	        $sql_floor_sewing=sql_select("SELECT a.floor_id from  pro_garments_production_mst a where a.location=$cbo_location $cbo_company_cond and a.production_type=5 and a.status_active=1 and a.is_deleted=0 and a.floor_id !=0  group by a.floor_id order by  a.floor_id ");

	        $sql_floor_finishing=sql_select("SELECT a.floor_id from  pro_garments_production_mst a where a.location=$cbo_location $cbo_company_cond and a.production_type=8 and a.status_active=1 and a.is_deleted=0 and a.floor_id !=0  group by a.floor_id order by  a.floor_id ");

			$sql_floor_iron=sql_select("SELECT a.floor_id from  pro_garments_production_mst a where a.location=$cbo_location $cbo_company_cond and a.production_type=7 and a.status_active=1 and a.is_deleted=0 and a.floor_id !=0  group by a.floor_id order by  a.floor_id ");

			$sql_floor_poly=sql_select("SELECT a.floor_id from  pro_garments_production_mst a where a.location=$cbo_location $cbo_company_cond and a.production_type=11 and a.status_active=1 and a.is_deleted=0 and a.floor_id !=0  group by a.floor_id order by  a.floor_id ");

	        $count_data=count($sql_floor_knitting)+count($sql_floor_dyeing)+count($sql_floor_finishing_fab)+count($sql_floor_cutting)+count($sql_floor_sewing)+count($sql_floor_finishing)+count($sql_floor_iron)+count($sql_floor_poly);
	        //echo $count_data;die;

	         //$count_hd=count($sql_floor)+1;
	        $count_hd=count($sql_floor_knitting)+count($sql_floor_dyeing)+count($sql_floor_finishing_fab)+count($sql_floor_cutting)+count($sql_floor_sewing)+count($sql_floor_finishing)+count($sql_floor_iron)+count($sql_floor_poly)+1;
	        $width_hd=$count_hd*80;
	        $count_knitting=count($sql_floor_knitting)+1;
	        $width_knitting=$count_knitting*80;

	        $count_dyeing=count($sql_floor_dyeing)*2+1;
	        $width_dyeing=$count_dyeing*80;

	        $count_finishing_fab=count($sql_floor_finishing_fab)+1;
	        $width_finishing_fab=$count_finishing_fab*80;

			$count_cutting=count($sql_floor_cutting)+1;
	        $width_cutting=$count_cutting*80;

	        $count_sewing=count($sql_floor_sewing)+1;
	        $width_sewing=$count_sewing*80;

	        $count_finishing=count($sql_floor_finishing)+1;
	        $width_finishing=$count_finishing*80;

			$count_iron=count($sql_floor_iron)+1;
	        $width_iron=$count_iron*80;

			$count_poly=count($sql_floor_poly)+1;
	        $width_poly=$count_poly*80;

	        $table_width=90+($count_data*100);
			ob_start();
			//$table_width=90+($datediff*160);
			?>
			<div id="scroll_body" align="center" style="height:auto; width:auto; margin:0 auto; padding:0;">
		        <table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="caption" align="center">
		            <tr>
		               <td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:18px">
		               <?
					   if ($cbo_company!=0){echo ' Company Name:' .$company_library[$cbo_company];} else{echo ' Working Company Name:' .$company_library[$cbo_working_company];}
					   ?>
						</strong>
		               </td>
		            </tr>
		            <tr>
		               <td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:14px"><? echo $report_title; ?></strong></td>
		            </tr>
		            <tr>
		               <td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:14px"> <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
		            </tr>
		        </table>
		        <?

				if ($cbo_location==0) $location_id =""; else $location_id =" and a.location_id=$cbo_location ";
				if ($cbo_location==0) $location_id_cond =""; else $location_id_cond =" and a.location=$cbo_location "; // garments production

				if($db_type==0)
				{

					if( $date_from==0 && $date_to==0 ) $production_date=""; else $production_date= " and a.production_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; //garments production
					if( $date_from==0 && $date_to==0 ) $production_date_knitting=""; else $production_date_knitting= " and a.receive_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; //knitting
					if( $date_from==0 && $date_to==0 ) $production_date_dyeing=""; else $production_date_dyeing= " and a.process_end_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; // dyeing
					if( $date_from==0 && $date_to==0 ) $production_date_finishing=""; else $production_date_finishing= " and a.receive_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; //finishing
				}
				else
				{
					if( $date_from==0 && $date_to==0 ) $production_date=""; else $production_date= " and a.production_date between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'"; //garments production
					if( $date_from==0 && $date_to==0 ) $production_date_knitting=""; else $production_date_knitting= " and a.receive_date between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'"; //knitting
					if( $date_from==0 && $date_to==0 ) $production_date_dyeing=""; else $production_date_dyeing= " and a.process_end_date between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'"; //dyeing
					if( $date_from==0 && $date_to==0 ) $production_date_finishing=""; else $production_date_finishing= " and a.receive_date between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'"; //finishing
				}

				$sql_result_knitting="Select a.id, a.entry_form, b.floor_id, a.receive_date, b.grey_receive_qnty as production_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.entry_form in (2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $cbo_company_cond  $location_id $production_date_knitting";
				//echo $sql_result;
				$sql_dtls_knitting=sql_select($sql_result_knitting);
				$floor_qnty_knitting=array();

				foreach ( $sql_dtls_knitting as $row )
				{
					$floor_qnty_knitting[change_date_format($row[csf("receive_date")],'','',1)][$row[csf("floor_id")]][$row[csf("entry_form")]] +=$row[csf("production_qnty")];
				}

				//dyeing prod

				/*$sql_result_dyeing="Select a.id, a.entry_form, a.floor_id, a.process_end_date, b.production_qty as production_qnty from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.entry_form in(35) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.floor_id !=0 and a.load_unload_id in(2) and a.result=1 and b.is_deleted=0 $cbo_company_cond  $production_date_dyeing";
				//echo $sql_result;
				$sql_dtls_dyeing=sql_select($sql_result_dyeing);
				$floor_qnty_dyeing=array();

				foreach ( $sql_dtls_dyeing as $row )
				{
					$floor_qnty_dyeing[change_date_format($row[csf("process_end_date")],'','',1)][$row[csf("floor_id")]][$row[csf("entry_form")]] +=$row[csf("production_qnty")];
				}*/
				/*======================================================================================
				*																						*
				*								GETTING DYEING DATA	 pro_ex_factory_mst									*
				*																						*
				=========================================================================================*/
				//=============================================sample data =========================================
				$production_date_dyeing2 = str_replace("a.process_end_date", "f.process_end_date", $production_date_dyeing);
				$sql_sam="(SELECT f.process_end_date,f.floor_id, a.total_trims_weight as trims_weight,SUM(b.batch_qnty) AS batch_qnty  from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where f.batch_id=a.id and   a.entry_form=0 and  g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and f.result=1 and a.batch_against in(3) and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0 and f.company_id=$cbo_company $production_date_dyeing2 group by f.process_end_date,f.floor_id, a.total_trims_weight)
					union
				 	(select f.process_end_date,f.floor_id,a.total_trims_weight as trims_weight,SUM(b.batch_qnty) AS batch_qnty from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g,wo_non_ord_samp_booking_mst h where f.batch_id=a.id  and h.booking_no=a.booking_no  and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and f.result=1 and a.batch_against in(3) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  f.status_active=1 and f.is_deleted=0 and f.company_id=$cbo_company $production_date_dyeing2 GROUP BY f.process_end_date,f.floor_id,a.total_trims_weight)";
				 	// echo $sql_sam;die();
				$floor_qnty_dyeing=array();

				// $sqlDye="SELECT f.process_end_date,f.floor_id,sum(distinct a.total_trims_weight) as total_trims_weight, SUM(b.batch_qnty) AS batch_qnty from pro_batch_create_dtls b, wo_non_ord_samp_booking_mst h, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where f.batch_id=a.id and  a.entry_form=0 and  g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2  and a.batch_against in(3)  and h.booking_no=a.booking_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0 and f.company_id=$cbo_company $production_date_dyeing2 GROUP BY  f.process_end_date,f.floor_id ";
				// echo $sqlDye;//  and f.fabric_type in(1,2,4,5,6,7,8,9,10,11,12)
				$sqlDyeRes = sql_select($sql_sam);
				foreach ( $sqlDyeRes as $row )
				{
					$tot_trim_qty=$row[csf('total_trims_weight')];
					$floor_qnty_dyeing[change_date_format($row[csf("process_end_date")],'','',1)][$row[csf("floor_id")]]['shadematch'] +=$row[csf("batch_qnty")]+$tot_trim_qty;
				}
				// print_r($floor_qnty_dyeing);die();

				$sql_dt="(SELECT a.total_trims_weight,SUM(b.batch_qnty) AS batch_qnty, f.process_end_date, f.floor_id from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d,  pro_fab_subprocess f, lib_machine_name g, pro_batch_create_mst a where f.batch_id=a.id and f.batch_id=b.mst_id and g.id=f.machine_id and a.entry_form=0  and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1) and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and f.result=1 and  f.status_active=1 and f.is_deleted=0 and f.company_id=$cbo_company $production_date_dyeing2
					GROUP BY a.total_trims_weight,f.process_end_date, f.floor_id)
					union
					(select a.total_trims_weight, SUM(b.batch_qnty) AS batch_qnty,f.process_end_date,f.floor_id from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g,wo_non_ord_samp_booking_mst h where h.booking_no=a.booking_no and f.batch_id=a.id  and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and f.result=1 and  f.status_active=1 and f.is_deleted=0
						and f.company_id=$cbo_company $production_date_dyeing2
						GROUP BY a.total_trims_weight,f.process_end_date,f.floor_id
					)";
				// echo $sql_dt;die();
				//====================================== batch qnty =============================
				// $sql_result="SELECT f.process_end_date,f.floor_id, sum(distinct CASE WHEN a.batch_against in(1) THEN a.total_trims_weight ELSE 0 END) AS total_trims_weight, SUM(b.batch_qnty) AS batch_qnty from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where f.batch_id=a.id and a.entry_form=0 and  g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2  and a.batch_against in(1,2,3)  and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and f.fabric_type in(1,2,4,5,6,7,8,9,10,11,12) and  f.status_active=1 and f.is_deleted=0 and f.company_id=$cbo_company $production_date_dyeing2 GROUP BY f.process_end_date,f.floor_id";
				// echo $sql_result;die();
				$sqlResult=sql_select($sql_dt);
				foreach ( $sqlResult as $row )
				{
					$tot_trim_qty=$row[csf('total_trims_weight')];
					$floor_qnty_dyeing[change_date_format($row[csf("process_end_date")],'','',1)][$row[csf("floor_id")]]['shadematch'] +=$row[csf("batch_qnty")]+$tot_trim_qty;
				}
				// print_r($floor_qnty_dyeing);
				//========================================== REPROCESS QNTY ==============================
				$fabric_type_arr_chk=array(11,12);
				$sql_result_redyeing="SELECT a.fabric_type, a.floor_id, a.process_end_date,b.batch_against, sum(c.batch_qnty) as production_qnty,b.total_trims_weight from pro_fab_subprocess a,pro_batch_create_mst b,pro_batch_create_dtls c where b.id=c.mst_id and a.batch_id=b.id and a.entry_form in(35) and b.entry_form=0  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and a.floor_id !=0 and a.load_unload_id in(2) and a.result=1 and b.is_deleted=0 and b.batch_against in(2) $cbo_company_cond  $production_date_dyeing group by a.fabric_type, a.floor_id, a.process_end_date,b.batch_against,b.total_trims_weight";
				// echo $sql_result_redyeing;
				$sql_dtls_dyeing=sql_select($sql_result_redyeing);


				foreach ( $sql_dtls_dyeing as $row )
				{
					if(!in_array($row[csf('fabric_type')],$fabric_type_arr_chk))
					 {
						if($row[csf('batch_against')]==2)
						{
							$floor_qnty_dyeing[change_date_format($row[csf("process_end_date")],'','',1)][$row[csf("floor_id")]]['reprocess']+=$row[csf("production_qnty")]+$row[csf("total_trims_weight")];
						}
					}
					else
					{
					    $floor_qnty_dyeing[change_date_format($row[csf("process_end_date")],'','',1)][$row[csf("floor_id")]]['shadematch'] +=$row[csf("production_qnty")];
					}
				}

				//============================== FOR SUBCONTACT ============================
				$sql_sub_dye="SELECT a.id, a.floor_id, a.process_end_date,f.extention_no, b.batch_qnty AS sub_batch_qnty from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst f, pro_fab_subprocess a where a.batch_id=f.id  and  f.entry_form=36 and f.id=b.mst_id and a.entry_form=38 and a.batch_id=b.mst_id and a.load_unload_id=2 and a.result=1  and f.batch_against in(1,2) and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and  f.status_active=1 and f.is_deleted=0 $cbo_company_cond  $production_date_dyeing";
				// echo $sql_sub_dye;
				$sql_sub_dye_res=sql_select($sql_sub_dye);

				foreach ($sql_sub_dye_res as $row )
				{
					if($row[csf("extention_no")]>0)
					{
						$floor_qnty_dyeing[change_date_format($row[csf("process_end_date")],'','',1)][$row[csf("floor_id")]]['reprocess'] +=$row[csf("sub_batch_qnty")];
					}
					else
					{
						$floor_qnty_dyeing[change_date_format($row[csf("process_end_date")],'','',1)][$row[csf("floor_id")]]['shadematch'] +=$row[csf("sub_batch_qnty")];
					}
				}
				// print_r($floor_qnty_dyeing);die();
				//end dyeing prod
				//finishing prod
				$fin_production_date=str_replace("production_date", "receive_date", $production_date);
				$sql_result_finishing_fab="Select b.machine_no_id,c.floor_id,a.id ,a.entry_form,a.receive_date,b.receive_qnty as production_qnty
				from inv_receive_master a,pro_finish_fabric_rcv_dtls b ,lib_machine_name c
				where a.id=b.mst_id and a.entry_form=7 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.machine_no_id !=0 and b.machine_no_id=c.id and c.floor_id !=0 $cbo_company_cond  $location_id $fin_production_date
				group by b.machine_no_id,c.floor_id  ,a.id ,a.entry_form,a.receive_date,b.receive_qnty ";

				//echo $sql_result;
				$sql_dtls_finishing_fab=sql_select($sql_result_finishing_fab);
				$floor_qnty_finishing_fab=array();

				foreach ( $sql_dtls_finishing_fab as $row )
				{
					$floor_qnty_finishing_fab[change_date_format($row[csf("receive_date")],'','',1)][$row[csf("floor_id")]][$row[csf("entry_form")]] +=$row[csf("production_qnty")];
				}
				//end finishing prod
				$sql_result="Select a.id, a.production_type, a.floor_id, a.production_date, b.production_qnty as production_qnty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.production_type in (1,5,8,7,11) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $cbo_company_cond  $location_id_cond $production_date";
				//echo $sql_result;
				$sql_dtls=sql_select($sql_result);
				$floor_qnty=array();

				foreach ( $sql_dtls as $row )
				{
					$floor_qnty[change_date_format($row[csf("production_date")],'','',1)][$row[csf("floor_id")]][$row[csf("production_type")]] +=$row[csf("production_qnty")];
				}

				$head_arr=array(2=>"Knitting Production",35=>"Dyeing Production",7=>"Fabric Finishing" ,1=>"Cutting Production",5=>"Sewing Production",103=>"Iron",11=>"Poly",8=>"Packing");
				$unit_wise_total_array=array();
		        $floor_arr = return_library_array("select id,floor_name from  lib_prod_floor ","id","floor_name");
				?>
				<style type="text/css">
					.alignment_css
					{
						word-break: break-all;
						word-wrap: break-word;
					}
				</style>
		        <div align="center" >
		        <table width="<? echo $table_width; ?> " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
		            <thead>
		                <tr>
		                    <th class='alignment_css' width="90" rowspan="2" >Date</th>
		                    <?
		                     foreach ( $head_arr as $head=>$headval )
							 {
								if($head==2)
		                        {


		                        ?>
		                            <th class='alignment_css' width="<? echo $width_knitting; ?>" colspan="<? echo $count_knitting; ?>"><? echo $headval; ?></th>
		                        <?
		                         }

		                        else if($head==35)
		                        {


		                        ?>
		                            <th class='alignment_css' width="<? echo $width_dyeing; ?>" colspan="<? echo $count_dyeing; ?>"><? echo $headval; ?></th>
		                        <?
		                         }
								else if($head==7)
		                        {


		                        ?>
		                            <th class='alignment_css' width="<? echo $width_finishing_fab; ?>" colspan="<? echo $count_finishing_fab; ?>"><? echo $headval; ?></th>
		                        <?
		                         }

								else if($head==1)
		                        {


		                        ?>
		                            <th class='alignment_css' width="<? echo $width_cutting; ?>" colspan="<? echo $count_cutting; ?>"><? echo $headval; ?></th>
		                        <?
		                         }

		                        else if($head==5)
		                        {


		                        ?>
		                            <th class='alignment_css' width="<? echo $width_sewing; ?>" colspan="<? echo $count_sewing; ?>"><? echo $headval; ?></th>
		                        <?
		                         }

								 else if($head==103)
		                        {
		                        ?>
		                            <th class='alignment_css' width="<? echo $width_iron; ?>" colspan="<? echo $count_iron; ?>"><? echo $headval; ?></th>
		                        <?
		                         }

		                        else if($head==11)
		                        {


		                        ?>
		                            <th class='alignment_css' width="<? echo $width_poly; ?>" colspan="<? echo $count_poly; ?>"><? echo $headval; ?></th>
		                        <?
		                         }

								else if($head==8)
		                        {


		                        ?>
		                            <th class='alignment_css' width="<? echo $width_finishing; ?>" colspan="<? echo $count_finishing; ?>"><? echo $headval; ?></th>
		                        <?
		                         }


							 }
							?>
		                </tr>
		               <tr>
							<?
							foreach ( $head_arr as $head=>$headval )
							{
		                        if($head==2)
		                        {
		    						foreach ( $sql_floor_knitting as $rows )
		    						{
		    							?>
		                                    <th class='alignment_css' width="80" colspan=""><? echo $floor_arr[$rows[csf("floor_id")]]; ?></th>
		    							<?
		    						}
		                        }

		                        else if($head==35)
		                        {
		                            foreach ( $sql_floor_dyeing as $rows )
		                            {
		                                ?>
		                                    <th class='alignment_css' width="80" colspan=""><? echo $floor_arr[$rows[csf("floor_id")]]; ?>-S. Match</th>
		                                    <th class='alignment_css' width="80" colspan=""><? echo $floor_arr[$rows[csf("floor_id")]]; ?>-Re Process</th>
		                                <?
		                            }
		                        }
								else if($head==7)
		                        {
		                            foreach ( $sql_floor_finishing_fab as $rows )
		                            {
		                                ?>
		                                    <th class='alignment_css' width="80" colspan=""><? echo $floor_arr[$rows[csf("floor_id")]]; ?></th>
		                                <?
		                            }
		                        }

								else if($head==1)
		                        {
		    						foreach ( $sql_floor_cutting as $rows )
		    						{
		    							?>
		                                    <th class='alignment_css' width="80" colspan=""><? echo $floor_arr[$rows[csf("floor_id")]]; ?></th>
		    							<?
		    						}
		                        }

		                        else if($head==5)
		                        {
		                            foreach ( $sql_floor_sewing as $rows )
		                            {
		                                ?>
		                                    <th class='alignment_css' width="80" colspan=""><? echo $floor_arr[$rows[csf("floor_id")]]; ?></th>
		                                <?
		                            }
		                        }

								else if($head==103)
		                        {
		                            foreach ( $sql_floor_iron as $rows )
		                            {
		                                ?>
		                                    <th class='alignment_css' width="80" colspan=""><? echo $floor_arr[$rows[csf("floor_id")]]; ?></th>
		                                <?
		                            }
		                        }

								else if($head==11)
		                        {
		                            foreach ( $sql_floor_poly as $rows )
		                            {
		                                ?>
		                                    <th class='alignment_css' width="80" colspan=""><? echo $floor_arr[$rows[csf("floor_id")]]; ?></th>
		                                <?
		                            }
		                        }

								else if($head==8)
		                        {
		                            foreach ( $sql_floor_finishing as $rows )
		                            {
		                                ?>
		                                    <th class='alignment_css' width="80" colspan=""><? echo $floor_arr[$rows[csf("floor_id")]]; ?></th>
		                                <?
		                            }
		                        }




								?>
								<th class='alignment_css' width="80" colspan="">Total</th>
							<?
							}
		                   ?>
		               </tr>
		            </thead>
		        </table>
		        </div>
		        <div align="center" style="max-height:300px" id="scroll_body2">
		        <table align="center" cellspacing="0" width="<? echo $table_width; ?> "  border="1" rules="all" class="rpt_table" >
					<?
					$value_define=array();
		            for($j=0;$j<$datediff;$j++)
		            {
		                if ($j%2==0)
		                    $bgcolor="#E9F3FF";
		                else
		                    $bgcolor="#FFFFFF";

		                $date_data_array=array();
						$newdate =add_date(str_replace("'","",$txt_date_from),$j);
						$newdate=date("d-M-Y",strtotime(str_replace("'","",$newdate)));
		                $date_data_array[$j]=$newdate;
		            ?>
		            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>">
		                <td class='alignment_css' width="90"><? echo change_date_format($newdate); ?></td>
		                <?
		                foreach ( $head_arr as $head=>$headval )
		                {
		                    $site_tot_qnty='';
		                    if($head==2)
		                    {
		                        foreach ( $sql_floor_knitting as $rows )
		                        {
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><?  echo number_format($floor_qnty_knitting[$date_data_array[$j]][$rows[csf("floor_id")]][$head],0); ?></td>
		                            <?
		                             $site_tot_qnty+=$floor_qnty_knitting[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
		                             $unit_wise_total_array[$head][$rows[csf("floor_id")]] +=$floor_qnty_knitting[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
		                             if($floor_qnty_knitting[$date_data_array[$j]][$rows[csf("floor_id")]][$head]!="")   $value_define[$head][$rows[csf("floor_id")]] +=1;

		                        }
		                        ?>
		                            <td class='alignment_css' width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>
		                        <?
		                    }
		                    else if($head==35)
		                    {
		                        foreach ( $sql_floor_dyeing as $rows )
		                        {
			                            ?>
			                                <td width="80" align="right"><?  echo number_format($floor_qnty_dyeing[$date_data_array[$j]][$rows[csf("floor_id")]]['shadematch'],0); ?></td>
			                                <td width="80" align="right"><?  echo number_format($floor_qnty_dyeing[$date_data_array[$j]][$rows[csf("floor_id")]]['reprocess'],0); ?></td>
			                            <?
			                             $site_tot_qnty+=$floor_qnty_dyeing[$date_data_array[$j]][$rows[csf("floor_id")]]['shadematch']+$floor_qnty_dyeing[$date_data_array[$j]][$rows[csf("floor_id")]]['reprocess'];
			                             $unit_wise_total_array2[$head][$rows[csf("floor_id")]][1] +=$floor_qnty_dyeing[$date_data_array[$j]][$rows[csf("floor_id")]]['shadematch'];
			                             $unit_wise_total_array2[$head][$rows[csf("floor_id")]][3] +=$floor_qnty_dyeing[$date_data_array[$j]][$rows[csf("floor_id")]]['reprocess'];
			                             if($floor_qnty_dyeing[$date_data_array[$j]][$rows[csf("floor_id")]]['shadematch']!="")   $value_define[$head][$rows[csf("floor_id")]] +=1;

		                        }
		                        ?>
		                            <td class='alignment_css' width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>
		                        <?
		                    }
							else if($head==7)
		                    {
		                        foreach ( $sql_floor_finishing_fab as $rows )
		                        {
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><?  echo number_format($floor_qnty_finishing_fab[$date_data_array[$j]][$rows[csf("floor_id")]][$head],0); ?></td>
		                            <?
		                             $site_tot_qnty+=$floor_qnty_finishing_fab[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
		                             $unit_wise_total_array[$head][$rows[csf("floor_id")]] +=$floor_qnty_finishing_fab[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
		                             if($floor_qnty_finishing_fab[$date_data_array[$j]][$rows[csf("floor_id")]][$head]!="")   $value_define[$head][$rows[csf("floor_id")]] +=1;

		                        }
		                        ?>
		                            <td class='alignment_css' width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>
		                        <?
		                    }

							else if($head==1)
		                    {
		                        foreach ( $sql_floor_cutting as $rows )
		                        {
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><?  echo number_format($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head],0); ?></td>
		                            <?
		                             $site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
		                             $unit_wise_total_array[$head][$rows[csf("floor_id")]] +=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
		                             if($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head]!="")   $value_define[$head][$rows[csf("floor_id")]] +=1;

		                        }
		                        ?>
		                            <td class='alignment_css' width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>
		                        <?
		                    }
		                    else if($head==5)
		                    {
		                        foreach ( $sql_floor_sewing as $rows )
		                        {
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><?  echo number_format($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head],0); ?></td>
		                            <?
		                             $site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
		                             $unit_wise_total_array[$head][$rows[csf("floor_id")]] +=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
		                             if($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head]!="")   $value_define[$head][$rows[csf("floor_id")]] +=1;

		                        }
		                        ?>
		                            <td class='alignment_css' width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>
		                        <?
		                    }


							else if($head==103)
		                    {
		                        foreach ( $sql_floor_iron as $rows )
		                        {
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><?  echo number_format($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][7],0); ?></td>
		                            <?
		                             $site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][7];
		                             $unit_wise_total_array[7][$rows[csf("floor_id")]] +=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][7];
		                             if($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][7]!="")   $value_define[7][$rows[csf("floor_id")]] +=1;

		                        }
		                        ?>
		                            <td class='alignment_css' width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>
		                        <?
		                    }

							else if($head==11)
		                    {
		                        foreach ( $sql_floor_poly as $rows )
		                        {
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><?  echo number_format($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head],0); ?></td>
		                            <?
		                             $site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
		                             $unit_wise_total_array[$head][$rows[csf("floor_id")]] +=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
		                             if($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head]!="")   $value_define[$head][$rows[csf("floor_id")]] +=1;

		                        }
		                        ?>
		                            <td class='alignment_css' width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>
		                        <?
		                    }

							else if($head==8)
		                    {
		                        foreach ( $sql_floor_finishing as $rows )
		                        {
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><?  echo number_format($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head],0); ?></td>
		                            <?
		                             $site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
		                             $unit_wise_total_array[$head][$rows[csf("floor_id")]] +=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
		                             if($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head]!="")   $value_define[$head][$rows[csf("floor_id")]] +=1;

		                        }
		                        ?>
		                            <td class='alignment_css' width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>
		                        <?
		                    }




		                }
		                ?>
		            </tr>
		            <?
		            }
		            ?>
		            <tr bgcolor="#dddddd">
		                <td class='alignment_css' align="center" width="90"><strong>Total : </strong></td>
		                <?
		                foreach ( $head_arr as $head=>$headval )
		                {
		                    $grand_total_tot='';
		                    if($head==2)
		                    {
		                        foreach ( $sql_floor_knitting as $rows )
		                        {
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><strong><? echo number_format($unit_wise_total_array[$head][$rows[csf("floor_id")]],0); ?></strong></td>
		                            <?
		                            $grand_total_tot+=$unit_wise_total_array[$head][$rows[csf("floor_id")]];
		                        }
		                    }

		                   else if($head==35)
		                    {
		                        foreach ( $sql_floor_dyeing as $rows )
		                        {
		                            ?>
		                                <td width="80" align="right"><strong><? echo number_format($unit_wise_total_array2[$head][$rows[csf("floor_id")]][1],0); ?></strong></td>
		                                <td width="80" align="right"><strong><? echo number_format($unit_wise_total_array2[$head][$rows[csf("floor_id")]][3],0); ?></strong></td>
		                            <?
		                            $grand_total_tot+=$unit_wise_total_array2[$head][$rows[csf("floor_id")]][1];
		                            $grand_total_tot_sub+=$unit_wise_total_array2[$head][$rows[csf("floor_id")]][3];
		                        }
		                    }
							else if($head==7)
		                    {
		                        foreach ( $sql_floor_finishing_fab as $rows )
		                        {
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><strong><? echo number_format($unit_wise_total_array[$head][$rows[csf("floor_id")]],0); ?></strong></td>
		                            <?
		                            $grand_total_tot+=$unit_wise_total_array[$head][$rows[csf("floor_id")]];
		                        }
		                    }

							else if($head==1)
		                    {
		                        foreach ( $sql_floor_cutting as $rows )
		                        {
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><strong><? echo number_format($unit_wise_total_array[$head][$rows[csf("floor_id")]],0); ?></strong></td>
		                            <?
		                            $grand_total_tot+=$unit_wise_total_array[$head][$rows[csf("floor_id")]];
		                        }
		                    }

		                   else if($head==5)
		                    {
		                        foreach ( $sql_floor_sewing as $rows )
		                        {
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><strong><? echo number_format($unit_wise_total_array[$head][$rows[csf("floor_id")]],0); ?></strong></td>
		                            <?
		                            $grand_total_tot+=$unit_wise_total_array[$head][$rows[csf("floor_id")]];
		                        }
		                    }

							else if($head==103)
		                    {
		                        foreach ( $sql_floor_iron as $rows )
		                        {
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><strong><? echo number_format($unit_wise_total_array[7][$rows[csf("floor_id")]],0); ?></strong></td>
		                            <?
		                            $grand_total_tot+=$unit_wise_total_array[7][$rows[csf("floor_id")]];
		                        }
		                    }

							else if($head==11)
		                    {
		                        foreach ( $sql_floor_poly as $rows )
		                        {
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><strong><? echo number_format($unit_wise_total_array[$head][$rows[csf("floor_id")]],0); ?></strong></td>
		                            <?
		                            $grand_total_tot+=$unit_wise_total_array[$head][$rows[csf("floor_id")]];
		                        }
		                    }

							 else if($head==8)
		                    {
		                        foreach ( $sql_floor_finishing as $rows )
		                        {
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><strong><? echo number_format($unit_wise_total_array[$head][$rows[csf("floor_id")]],0); ?></strong></td>
		                            <?
		                            $grand_total_tot+=$unit_wise_total_array[$head][$rows[csf("floor_id")]];
		                        }
		                    }




		                    ?>
		                    <td class='alignment_css' width="80" align="right"><strong><? echo number_format($grand_total_tot,0); ?></strong></td>
		                    <?
		                }
		                ?>
		            </tr>

		            <tr bgcolor="#dddddd">
		                <td class='alignment_css' align="center" width="90"><strong>Avg : </strong></td>
		                <?
						$m=1;
		                foreach ( $head_arr as $head=>$headval )
		                {
		                    $avg='';
		                    $avg_tot='';
		                    if($head==2)
		                    {
		                        foreach ( $sql_floor_knitting as $rows )
		                        {
		                            $avg=$unit_wise_total_array[$head][$rows[csf("floor_id")]]/ $value_define[$head][$rows[csf("floor_id")]];
		    						//$avg=$unit_wise_total_array[$head][$rows[csf("id")]]/$value_define[$head][$rows[csf("id")]];
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><strong><? echo number_format( $avg,2); ?></strong></td>
		                            <?
		                            $avg_tot+=$avg;
		                        }
		                    }

		                    else if($head==35)
		                    {
		                        foreach ( $sql_floor_dyeing as $rows )
		                        {
		                            $avg=$unit_wise_total_array2[$head][$rows[csf("floor_id")]][1]/ $value_define[$head][$rows[csf("floor_id")]];
		                            $avg_subcon=$unit_wise_total_array2[$head][$rows[csf("floor_id")]][3]/ $value_define[$head][$rows[csf("floor_id")]];
		                            //$avg=$unit_wise_total_array[$head][$rows[csf("id")]]/$value_define[$head][$rows[csf("id")]];
		                            ?>
		                                <td width="80" align="right"><strong><? echo number_format( $avg,2); ?></strong></td>
		                                <td width="80" align="right"><strong><? echo number_format( $avg_subcon,2); ?></strong></td>
		                            <?
		                            $avg_tot += $avg+$avg_subcon;
		                        }
		                    }
							else if($head==7)
		                    {
		                        foreach ( $sql_floor_finishing_fab as $rows )
		                        {
		                            $avg=$unit_wise_total_array[$head][$rows[csf("floor_id")]]/ $value_define[$head][$rows[csf("floor_id")]];
		                            //$avg=$unit_wise_total_array[$head][$rows[csf("id")]]/$value_define[$head][$rows[csf("id")]];
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><strong><? echo number_format( $avg,2); ?></strong></td>
		                            <?
		                            $avg_tot+=$avg;
		                        }
		                    }

							else if($head==1)
		                    {
		                        foreach ( $sql_floor_cutting as $rows )
		                        {
		                            $avg=$unit_wise_total_array[$head][$rows[csf("floor_id")]]/ $value_define[$head][$rows[csf("floor_id")]];
		    						//$avg=$unit_wise_total_array[$head][$rows[csf("id")]]/$value_define[$head][$rows[csf("id")]];
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><strong><? echo number_format( $avg,2); ?></strong></td>
		                            <?
		                            $avg_tot+=$avg;
		                        }
		                    }

		                    else if($head==5)
		                    {
		                        foreach ( $sql_floor_sewing as $rows )
		                        {
		                            $avg=$unit_wise_total_array[$head][$rows[csf("floor_id")]]/ $value_define[$head][$rows[csf("floor_id")]];
		                            //$avg=$unit_wise_total_array[$head][$rows[csf("id")]]/$value_define[$head][$rows[csf("id")]];
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><strong><? echo number_format( $avg,2); ?></strong></td>
		                            <?
		                            $avg_tot+=$avg;
		                        }
		                    }

							else if($head==103)
		                    {
		                        foreach ( $sql_floor_iron as $rows )
		                        {
		                            $avg=$unit_wise_total_array[7][$rows[csf("floor_id")]]/ $value_define[7][$rows[csf("floor_id")]];
		                            //$avg=$unit_wise_total_array[$head][$rows[csf("id")]]/$value_define[$head][$rows[csf("id")]];
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><strong><? echo number_format( $avg,2); ?></strong></td>
		                            <?
		                            $avg_tot+=$avg;
		                        }
		                    }

							else if($head==11)
		                    {
		                        foreach ( $sql_floor_poly as $rows )
		                        {
		                            $avg=$unit_wise_total_array[$head][$rows[csf("floor_id")]]/ $value_define[$head][$rows[csf("floor_id")]];
		                            //$avg=$unit_wise_total_array[$head][$rows[csf("id")]]/$value_define[$head][$rows[csf("id")]];
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><strong><? echo number_format( $avg,2); ?></strong></td>
		                            <?
		                            $avg_tot+=$avg;
		                        }
		                    }

							else if($head==8)
		                    {
		                        foreach ( $sql_floor_finishing as $rows )
		                        {
		                            $avg=$unit_wise_total_array[$head][$rows[csf("floor_id")]]/ $value_define[$head][$rows[csf("floor_id")]];
		                            //$avg=$unit_wise_total_array[$head][$rows[csf("id")]]/$value_define[$head][$rows[csf("id")]];
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><strong><? echo number_format( $avg,2); ?></strong></td>
		                            <?
		                            $avg_tot+=$avg;
		                        }
		                    }



		                    ?>
		                    <td class='alignment_css' width="80" align="right"><strong><?  echo number_format($avg_tot,2); ?></strong></td>
		                <?
		                }
		                ?>
		            </tr>
		        </table>
		    </div>
		    </div>
		    <?

		}
		else if ($cbo_production_type==2)
		{

			$sql_floor=sql_select("Select a.id, a.floor_name from  lib_prod_floor a where a.location_id=$cbo_location $cbo_company_cond and a.status_active=1 and a.is_deleted=0 order by a.floor_name ");

			$sql_floor_knitting=sql_select("select b.floor_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0  and b.floor_id !=0  group by b.floor_id order by  b.floor_id");

			$sql_floor_dyeing=sql_select("SELECT a.floor_id from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.entry_form=35 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.floor_id !=0 and a.load_unload_id in(2) and a.result=1  group by a.floor_id order by  a.floor_id");

			 $sql_machineID_finish_fab=sql_select("select b.machine_no_id from inv_receive_master a,pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form=7 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.machine_no_id !=0  group by b.machine_no_id order by  b.machine_no_id");
			 $machineIDs="";
			foreach ( $sql_machineID_finish_fab as $row )
			{
				$machineIDs.=$row[csf("machine_no_id")].',';
			}
			$machineIDs= chop($machineIDs,",");
			$sql_floor_finishing_fab=sql_select("select floor_id from lib_machine_name  where id in ($machineIDs)  and is_deleted=0 and status_active=1  group by floor_id order by  floor_id");


	        $count_data=count($sql_floor_knitting)+(count($sql_floor_dyeing)*2)+count($sql_floor_finishing_fab);
	        //echo $count_data;die;

	         //$count_hd=count($sql_floor)+1;
	        $count_hd=count($sql_floor_knitting)+(count($sql_floor_dyeing)*2)+count($sql_floor_finishing_fab)+1;
	        $width_hd=$count_hd*80;
	        $count_knitting=count($sql_floor_knitting)+1;
	        $width_knitting=$count_knitting*80;

	        $count_dyeing=count($sql_floor_dyeing)*2+1;
	        $width_dyeing=$count_dyeing*80;

	        $count_finishing_fab=count($sql_floor_finishing_fab)+1;
	        $width_finishing_fab=$count_finishing_fab*80;



	        $table_width=90+($count_data*100);
			ob_start();
			//$table_width=90+($datediff*160);
			?>
			<div id="scroll_body" align="center" style="height:auto; width:auto; margin:0 auto; padding:0;">
		        <table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="caption" align="center">
		            <tr>
		               <td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:18px">
		               <?
					   if ($cbo_company!=0){echo ' Company Name:' .$company_library[$cbo_company];} else{echo ' Working Company Name:' .$company_library[$cbo_working_company];}
					   ?>
						</strong>
		               </td>
		            </tr>
		            <tr>
		               <td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:14px"><? echo $report_title; ?></strong></td>
		            </tr>
		            <tr>
		               <td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:14px"> <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
		            </tr>
		        </table>
		        <?

				if ($cbo_location==0) $location_id =""; else $location_id =" and a.location_id=$cbo_location ";
				if($db_type==0)
				{
					if( $date_from==0 && $date_to==0 ) $production_date=""; else $production_date= " and a.receive_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
					if( $date_from==0 && $date_to==0 ) $production_date_dyeing=""; else $production_date_dyeing= " and a.process_end_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; // dyeing
					if( $date_from==0 && $date_to==0 ) $production_date_finishing=""; else $production_date_finishing= " and a.receive_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; //finishing
				}
				else
				{
					if( $date_from==0 && $date_to==0 ) $production_date=""; else $production_date= " and a.receive_date between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'";
					if( $date_from==0 && $date_to==0 ) $production_date_dyeing=""; else $production_date_dyeing= " and a.process_end_date between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'"; //dyeing
					if( $date_from==0 && $date_to==0 ) $production_date_finishing=""; else $production_date_finishing= " and a.receive_date between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'"; //finishing
				}

				$sql_result="Select a.id, a.entry_form, b.floor_id, a.receive_date, b.grey_receive_qnty as production_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.entry_form in (2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $cbo_company_cond  $location_id $production_date";
				//echo $sql_result;
				$sql_dtls=sql_select($sql_result);
				$floor_qnty=array();

				foreach ( $sql_dtls as $row )
				{
					$floor_qnty[change_date_format($row[csf("receive_date")],'','',1)][$row[csf("floor_id")]][$row[csf("entry_form")]] +=$row[csf("production_qnty")];
				}

				/*======================================================================================
				*																						*
				*								GETTING DYEING DATA										*
				*																						*
				=========================================================================================*/
				//=============================================sample data =========================================
				$production_date_dyeing2 = str_replace("a.process_end_date", "f.process_end_date", $production_date_dyeing);
				$sql_sam="(SELECT f.process_end_date,f.floor_id, a.total_trims_weight as trims_weight,SUM(b.batch_qnty) AS batch_qnty  from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where f.batch_id=a.id and   a.entry_form=0 and  g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and f.result=1 and a.batch_against in(3) and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0 and f.company_id=$cbo_company $production_date_dyeing2 group by f.process_end_date,f.floor_id, a.total_trims_weight)
					union
				 	(select f.process_end_date,f.floor_id,a.total_trims_weight as trims_weight,SUM(b.batch_qnty) AS batch_qnty from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g,wo_non_ord_samp_booking_mst h where f.batch_id=a.id  and h.booking_no=a.booking_no  and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and f.result=1 and a.batch_against in(3) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  f.status_active=1 and f.is_deleted=0 and f.company_id=$cbo_company $production_date_dyeing2 GROUP BY f.process_end_date,f.floor_id,a.total_trims_weight)";
				 	// echo $sql_sam;die();
				$floor_qnty_dyeing=array();

				// $sqlDye="SELECT f.process_end_date,f.floor_id,sum(distinct a.total_trims_weight) as total_trims_weight, SUM(b.batch_qnty) AS batch_qnty from pro_batch_create_dtls b, wo_non_ord_samp_booking_mst h, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where f.batch_id=a.id and  a.entry_form=0 and  g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2  and a.batch_against in(3)  and h.booking_no=a.booking_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0 and f.company_id=$cbo_company $production_date_dyeing2 GROUP BY  f.process_end_date,f.floor_id ";
				// echo $sqlDye;//  and f.fabric_type in(1,2,4,5,6,7,8,9,10,11,12)
				$sqlDyeRes = sql_select($sql_sam);
				foreach ( $sqlDyeRes as $row )
				{
					$tot_trim_qty=$row[csf('total_trims_weight')];
					$floor_qnty_dyeing[change_date_format($row[csf("process_end_date")],'','',1)][$row[csf("floor_id")]]['shadematch'] +=$row[csf("batch_qnty")]+$tot_trim_qty;
				}
				// print_r($floor_qnty_dyeing);die();

				$sql_dt="(SELECT a.total_trims_weight,SUM(b.batch_qnty) AS batch_qnty, f.process_end_date, f.floor_id from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d,  pro_fab_subprocess f, lib_machine_name g, pro_batch_create_mst a where f.batch_id=a.id and f.batch_id=b.mst_id and g.id=f.machine_id and a.entry_form=0  and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1) and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and f.result=1 and  f.status_active=1 and f.is_deleted=0 and f.company_id=$cbo_company $production_date_dyeing2
					GROUP BY a.total_trims_weight,f.process_end_date, f.floor_id)
					union
					(select a.total_trims_weight, SUM(b.batch_qnty) AS batch_qnty,f.process_end_date,f.floor_id from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g,wo_non_ord_samp_booking_mst h where h.booking_no=a.booking_no and f.batch_id=a.id  and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and f.result=1 and  f.status_active=1 and f.is_deleted=0
						and f.company_id=$cbo_company $production_date_dyeing2
						GROUP BY a.total_trims_weight,f.process_end_date,f.floor_id
					)";
				// echo $sql_dt;die();
				//====================================== batch qnty =============================
				// $sql_result="SELECT f.process_end_date,f.floor_id, sum(distinct CASE WHEN a.batch_against in(1) THEN a.total_trims_weight ELSE 0 END) AS total_trims_weight, SUM(b.batch_qnty) AS batch_qnty from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where f.batch_id=a.id and a.entry_form=0 and  g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2  and a.batch_against in(1,2,3)  and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and f.fabric_type in(1,2,4,5,6,7,8,9,10,11,12) and  f.status_active=1 and f.is_deleted=0 and f.company_id=$cbo_company $production_date_dyeing2 GROUP BY f.process_end_date,f.floor_id";
				// echo $sql_result;die();
				$sqlResult=sql_select($sql_dt);
				foreach ( $sqlResult as $row )
				{
					$tot_trim_qty=$row[csf('total_trims_weight')];
					$floor_qnty_dyeing[change_date_format($row[csf("process_end_date")],'','',1)][$row[csf("floor_id")]]['shadematch'] +=$row[csf("batch_qnty")]+$tot_trim_qty;
				}
				// print_r($floor_qnty_dyeing);
				//========================================== REPROCESS QNTY ==============================
				$fabric_type_arr_chk=array(11,12);
				$sql_result_redyeing="SELECT a.fabric_type, a.floor_id, a.process_end_date,b.batch_against, sum(c.batch_qnty) as production_qnty,b.total_trims_weight from pro_fab_subprocess a,pro_batch_create_mst b,pro_batch_create_dtls c where b.id=c.mst_id and a.batch_id=b.id and a.entry_form in(35) and b.entry_form=0  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and a.floor_id !=0 and a.load_unload_id in(2) and a.result=1 and b.is_deleted=0 and b.batch_against in(2) $cbo_company_cond  $production_date_dyeing group by a.fabric_type, a.floor_id, a.process_end_date,b.batch_against,b.total_trims_weight";
				// echo $sql_result_redyeing;
				$sql_dtls_dyeing=sql_select($sql_result_redyeing);


				foreach ( $sql_dtls_dyeing as $row )
				{
					if(!in_array($row[csf('fabric_type')],$fabric_type_arr_chk))
					 {
						if($row[csf('batch_against')]==2)
						{
							$floor_qnty_dyeing[change_date_format($row[csf("process_end_date")],'','',1)][$row[csf("floor_id")]]['reprocess']+=$row[csf("production_qnty")]+$row[csf("total_trims_weight")];
						}
					}
					else
					{
					    $floor_qnty_dyeing[change_date_format($row[csf("process_end_date")],'','',1)][$row[csf("floor_id")]]['shadematch'] +=$row[csf("production_qnty")];
					}
				}

				//============================== FOR SUBCONTACT ============================
				$sql_sub_dye="SELECT a.id, a.floor_id, a.process_end_date,f.extention_no, b.batch_qnty AS sub_batch_qnty from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst f, pro_fab_subprocess a where a.batch_id=f.id  and  f.entry_form=36 and f.id=b.mst_id and a.entry_form=38 and a.batch_id=b.mst_id and a.load_unload_id=2 and a.result=1  and f.batch_against in(1,2) and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and  f.status_active=1 and f.is_deleted=0 $cbo_company_cond  $production_date_dyeing";
				// echo $sql_sub_dye;
				$sql_sub_dye_res=sql_select($sql_sub_dye);

				foreach ($sql_sub_dye_res as $row )
				{
					if($row[csf("extention_no")]>0)
					{
						$floor_qnty_dyeing[change_date_format($row[csf("process_end_date")],'','',1)][$row[csf("floor_id")]]['reprocess'] +=$row[csf("sub_batch_qnty")];
					}
					else
					{
						$floor_qnty_dyeing[change_date_format($row[csf("process_end_date")],'','',1)][$row[csf("floor_id")]]['shadematch'] +=$row[csf("sub_batch_qnty")];
					}
				}
				// print_r($floor_qnty_dyeing);die();
				//end dyeing prod
				//finishing prod

				$sql_result_finishing_fab="Select b.machine_no_id,c.floor_id,a.id ,a.entry_form,a.receive_date,b.receive_qnty as production_qnty
				from inv_receive_master a,pro_finish_fabric_rcv_dtls b ,lib_machine_name c
				where a.id=b.mst_id and a.entry_form=7 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.machine_no_id !=0 and b.machine_no_id=c.id and c.floor_id !=0 $cbo_company_cond  $location_id $production_date
				group by b.machine_no_id,c.floor_id  ,a.id ,a.entry_form,a.receive_date,b.receive_qnty ";

				//echo $sql_result;
				$sql_dtls_finishing_fab=sql_select($sql_result_finishing_fab);
				$floor_qnty_finishing_fab=array();

				foreach ( $sql_dtls_finishing_fab as $row )
				{
					$floor_qnty_finishing_fab[change_date_format($row[csf("receive_date")],'','',1)][$row[csf("floor_id")]][$row[csf("entry_form")]] +=$row[csf("production_qnty")];
				}
			//end finishing prod

				$head_arr=array(2=>"Knitting Production",35=>"Dyeing Production",7=>"Fabric Finishing");
				$unit_wise_total_array=array();
				$unit_wise_total_array2=array();
		        $floor_arr = return_library_array("select id,floor_name from  lib_prod_floor ","id","floor_name");
				?>
		        <div align="center" style="height:auto;">
		        <table width="<? echo $table_width; ?> " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
		            <thead>
		                <tr>
		                    <th width="90" rowspan="2" >Date</th>
		                    <?
		                     foreach ( $head_arr as $head=>$headval )
							 {
								if($head==2)
		                        {


		                        ?>
		                            <th width="<? echo $width_knitting; ?>" colspan="<? echo $count_knitting; ?>"><? echo $headval; ?></th>
		                        <?
		                         }

		                        else if($head==35)
		                        {


		                        ?>
		                            <th width="<? echo $width_dyeing; ?>" colspan="<? echo $count_dyeing; ?>"><? echo $headval; ?></th>

		                        <?
		                         }
								else if($head==7)
		                        {


		                        ?>
		                            <th width="<? echo $width_finishing_fab; ?>" colspan="<? echo $count_finishing_fab; ?>"><? echo $headval; ?></th>
		                        <?
		                         }


							 }
							?>
		                </tr>
		               <tr>
							<?
							foreach ( $head_arr as $head=>$headval )
							{
		                        if($head==2)
		                        {
		    						foreach ( $sql_floor_knitting as $rows )
		    						{
		    							?>
		                                    <th width="80" colspan=""><? echo $floor_arr[$rows[csf("floor_id")]]; ?></th>
		    							<?
		    						}
		                        }

		                        else if($head==35)
		                        {
		                            foreach ( $sql_floor_dyeing as $rows )
		                            {
		                                ?>
		                                    <th width="80" colspan=""><? echo $floor_arr[$rows[csf("floor_id")]]; ?>-S. Match</th>
		                                    <th width="80" colspan=""><? echo $floor_arr[$rows[csf("floor_id")]]; ?>-Re Process</th>
		                                <?
		                            }
		                        }
								else if($head==7)
		                        {
		                            foreach ( $sql_floor_finishing_fab as $rows )
		                            {
		                                ?>
		                                    <th width="80" colspan=""><? echo $floor_arr[$rows[csf("floor_id")]]; ?></th>
		                                <?
		                            }
		                        }




								?>
								<th width="80" colspan="">Total</th>
							<?
							}
		                   ?>
		               </tr>
		            </thead>
		        </table>
		        </div>
		        <div align="center" style="max-height:300px" id="scroll_body2">
		        <table align="center" cellspacing="0" width="<? echo $table_width; ?> "  border="1" rules="all" class="rpt_table" >
					<?
					$value_define=array();
		            for($j=0;$j<$datediff;$j++)
		            {
		                if ($j%2==0)
		                    $bgcolor="#E9F3FF";
		                else
		                    $bgcolor="#FFFFFF";

		                $date_data_array=array();
						$newdate =add_date(str_replace("'","",$txt_date_from),$j);
						$newdate=date("d-M-Y",strtotime(str_replace("'","",$newdate)));
		                $date_data_array[$j]=$newdate;
			            ?>
			            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>">
			                <td width="90"><? echo change_date_format($newdate); ?></td>
			                <?
			                foreach ( $head_arr as $head=>$headval )
			                {
			                    $site_tot_qnty='';
			                    if($head==2)
			                    {
			                        foreach ( $sql_floor_knitting as $rows )
			                        {
			                            ?>
			                                <td width="80" align="right"><?  echo number_format($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head],0); ?></td>
			                            <?
			                             $site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
			                             $unit_wise_total_array[$head][$rows[csf("floor_id")]] +=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
			                             if($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head]!="")   $value_define[$head][$rows[csf("floor_id")]] +=1;

			                        }
			                        ?>
			                            <td width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>
			                        <?
			                    }
			                    else if($head==35)
			                    {
			                        foreach ( $sql_floor_dyeing as $rows )
			                        {
			                            ?>
			                                <td width="80" align="right"><?  echo number_format($floor_qnty_dyeing[$date_data_array[$j]][$rows[csf("floor_id")]]['shadematch'],0); ?></td>
			                                <td width="80" align="right"><?  echo number_format($floor_qnty_dyeing[$date_data_array[$j]][$rows[csf("floor_id")]]['reprocess'],0); ?></td>
			                            <?
			                             $site_tot_qnty+=$floor_qnty_dyeing[$date_data_array[$j]][$rows[csf("floor_id")]]['shadematch']+$floor_qnty_dyeing[$date_data_array[$j]][$rows[csf("floor_id")]]['reprocess'];
			                             $unit_wise_total_array2[$head][$rows[csf("floor_id")]][1] +=$floor_qnty_dyeing[$date_data_array[$j]][$rows[csf("floor_id")]]['shadematch'];
			                             $unit_wise_total_array2[$head][$rows[csf("floor_id")]][3] +=$floor_qnty_dyeing[$date_data_array[$j]][$rows[csf("floor_id")]]['reprocess'];
			                             if($floor_qnty_dyeing[$date_data_array[$j]][$rows[csf("floor_id")]]['shadematch']!="")   $value_define[$head][$rows[csf("floor_id")]] +=1;

			                        }
			                        ?>
			                            <td width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>
			                        <?
			                    }
								else if($head==7)
			                    {
			                        foreach ( $sql_floor_finishing_fab as $rows )
			                        {
			                            ?>
			                                <td width="80" align="right"><?  echo number_format($floor_qnty_finishing_fab[$date_data_array[$j]][$rows[csf("floor_id")]][$head],0); ?></td>
			                            <?
			                             $site_tot_qnty+=$floor_qnty_finishing_fab[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
			                             $unit_wise_total_array[$head][$rows[csf("floor_id")]] +=$floor_qnty_finishing_fab[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
			                             if($floor_qnty_finishing_fab[$date_data_array[$j]][$rows[csf("floor_id")]][$head]!="")   $value_define[$head][$rows[csf("floor_id")]] +=1;

			                        }
			                        ?>
			                            <td width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>
			                        <?
			                    }
			                }
			                ?>
			            </tr>
			            <?
			            }
			            ?>
			            <tr bgcolor="#dddddd">
			                <td align="center" width="90"><strong>Total : </strong></td>
			                <?
			                foreach ( $head_arr as $head=>$headval )
			                {
			                    $grand_total_tot='';
			                    $grand_total_tot_sub='';
			                    if($head==2)
			                    {
			                        foreach ( $sql_floor_knitting as $rows )
			                        {
			                            ?>
			                                <td width="80" align="right"><strong><? echo number_format($unit_wise_total_array[$head][$rows[csf("floor_id")]],0); ?></strong></td>
			                            <?
			                            $grand_total_tot+=$unit_wise_total_array[$head][$rows[csf("floor_id")]];
			                        }
			                    }

			                   else if($head==35)
			                    {
			                        foreach ( $sql_floor_dyeing as $rows )
			                        {
			                            ?>
			                                <td width="80" align="right"><strong><? echo number_format($unit_wise_total_array2[$head][$rows[csf("floor_id")]][1],0); ?></strong></td>
			                                <td width="80" align="right"><strong><? echo number_format($unit_wise_total_array2[$head][$rows[csf("floor_id")]][3],0); ?></strong></td>
			                            <?
			                            $grand_total_tot+=$unit_wise_total_array2[$head][$rows[csf("floor_id")]][1];
			                            $grand_total_tot_sub+=$unit_wise_total_array2[$head][$rows[csf("floor_id")]][3];
			                        }
			                    }
								else if($head==7)
			                    {
			                        foreach ( $sql_floor_finishing_fab as $rows )
			                        {
			                            ?>
			                                <td width="80" align="right"><strong><? echo number_format($unit_wise_total_array[$head][$rows[csf("floor_id")]],0); ?></strong></td>
			                            <?
			                            $grand_total_tot+=$unit_wise_total_array[$head][$rows[csf("floor_id")]];
			                        }
			                    }




			                    ?>
			                    <td width="80" align="right"><strong><? echo number_format($grand_total_tot+$grand_total_tot_sub,0); ?></strong></td>
			                    <?
			                }
			                ?>
			            </tr>

			            <tr bgcolor="#dddddd">
			                <td align="center" width="90"><strong>Avg : </strong></td>
			                <?
							$m=1;
			                foreach ( $head_arr as $head=>$headval )
			                {
			                    $avg='';
			                    $avg_tot='';
			                    if($head==2)
			                    {
			                        foreach ( $sql_floor_knitting as $rows )
			                        {
			                            $avg=$unit_wise_total_array[$head][$rows[csf("floor_id")]]/ $value_define[$head][$rows[csf("floor_id")]];
			    						//$avg=$unit_wise_total_array[$head][$rows[csf("id")]]/$value_define[$head][$rows[csf("id")]];
			                            ?>
			                                <td width="80" align="right"><strong><? echo number_format( $avg,2); ?></strong></td>
			                            <?
			                            $avg_tot+=$avg;
			                        }
			                    }

			                    else if($head==35)
			                    {
			                        foreach ( $sql_floor_dyeing as $rows )
			                        {
			                            $avg=$unit_wise_total_array2[$head][$rows[csf("floor_id")]][1]/ $value_define[$head][$rows[csf("floor_id")]];
			                            $avg_subcon=$unit_wise_total_array2[$head][$rows[csf("floor_id")]][3]/ $value_define[$head][$rows[csf("floor_id")]];
			                            //$avg=$unit_wise_total_array[$head][$rows[csf("id")]]/$value_define[$head][$rows[csf("id")]];
			                            ?>
			                                <td width="80" align="right"><strong><? echo number_format( $avg,2); ?></strong></td>
			                                <td width="80" align="right"><strong><? echo number_format( $avg_subcon,2); ?></strong></td>
			                            <?
			                            $avg_tot += $avg+$avg_subcon;
			                        }
			                    }
								else if($head==7)
			                    {
			                        foreach ( $sql_floor_finishing_fab as $rows )
			                        {
			                            $avg=$unit_wise_total_array[$head][$rows[csf("floor_id")]]/ $value_define[$head][$rows[csf("floor_id")]];
			                            //$avg=$unit_wise_total_array[$head][$rows[csf("id")]]/$value_define[$head][$rows[csf("id")]];
			                            ?>
			                                <td width="80" align="right"><strong><? echo number_format( $avg,2); ?></strong></td>
			                            <?
			                            $avg_tot+=$avg;
			                        }
			                    }



			                    ?>
			                    <td width="80" align="right"><strong><?  echo number_format($avg_tot,2); ?></strong></td>
			                <?
			                }
			                ?>
			            </tr>
			        </table>
			    </div>
		    </div>
		    <?
		}
		else if ($cbo_production_type==3)
		{

			$sql_floor=sql_select("Select a.id, a.floor_name from  lib_prod_floor a where a.location_id=$cbo_location $cbo_company_cond and a.status_active=1 and a.is_deleted=0 order by a.floor_name ");

	      	$sql_floor_cutting=sql_select("SELECT a.floor_id,b.floor_serial_no from  pro_garments_production_mst a, lib_prod_floor b where b.id=a.floor_id and a.location=$cbo_location $cbo_company_cond and a.production_type=1 and a.status_active=1 and a.is_deleted=0 and a.floor_id !=0  group by a.floor_id,b.floor_serial_no order by  b.floor_serial_no ");

	        $sql_floor_sewing=sql_select("SELECT a.floor_id from  pro_garments_production_mst a where a.location=$cbo_location $cbo_company_cond and a.production_type=5 and a.status_active=1 and a.is_deleted=0 and a.floor_id !=0  group by a.floor_id order by  a.floor_id ");

	        $sql_floor_finishing=sql_select("SELECT a.floor_id from  pro_garments_production_mst a where a.location=$cbo_location $cbo_company_cond and a.production_type=8 and a.status_active=1 and a.is_deleted=0 and a.floor_id !=0  group by a.floor_id order by  a.floor_id ");

			$sql_floor_iron=sql_select("SELECT a.floor_id from  pro_garments_production_mst a where a.location=$cbo_location $cbo_company_cond and a.production_type=7 and a.status_active=1 and a.is_deleted=0 and a.floor_id !=0  group by a.floor_id order by  a.floor_id ");

			$sql_floor_poly=sql_select("SELECT a.floor_id from  pro_garments_production_mst a where a.location=$cbo_location $cbo_company_cond and a.production_type=11 and a.status_active=1 and a.is_deleted=0 and a.floor_id !=0  group by a.floor_id order by  a.floor_id ");

			//echo "SELECT a.floor_id from  pro_garments_production_mst a where a.location=$cbo_location $cbo_company_cond and a.production_type=7 and a.status_active=1 and a.is_deleted=0 and a.floor_id !=0  group by a.floor_id order by  a.floor_id ";

	       /* echo "<pre>";
	        print_r($sql_floor_finishing);die;*/
	      //  $count_data=count($sql_floor);

	        $count_data=count($sql_floor_cutting)+count($sql_floor_finishing)+count($sql_floor_sewing)+count($sql_floor_iron)+count($sql_floor_poly);
	        //echo $count_data;die;

	         //$count_hd=count($sql_floor)+1;
	        $count_hd=count($sql_floor_cutting)+count($sql_floor_finishing)+count($sql_floor_sewing)+count($sql_floor_iron)+count($sql_floor_poly)+1;
	        $width_hd=$count_hd*80;
	        $count_cutting=count($sql_floor_cutting)+1;
	        $width_cutting=$count_cutting*80;

	        $count_sewing=count($sql_floor_sewing)+1;
	        $width_sewing=$count_sewing*80;

	        $count_finishing=count($sql_floor_finishing)+1;
	        $width_finishing=$count_finishing*80;

			$count_iron=count($sql_floor_iron)+1;
	        $width_iron=$count_iron*80;

			$count_poly=count($sql_floor_poly)+1;
	        $width_poly=$count_poly*80;

	        $table_width=90+($count_data*100);
			ob_start();
			//$table_width=90+($datediff*160);
			?>
			<div id="scroll_body" align="center" style="height:auto; width:auto; margin:0 auto; padding:0;">
		        <table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="caption" align="center">
		            <tr>
		               <td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:18px">
		               <?
					   if ($cbo_company!=0){echo ' Company Name:' .$company_library[$cbo_company];} else{echo ' Working Company Name:' .$company_library[$cbo_working_company];}
					   ?>
						</strong>
		               </td>
		            </tr>
		            <tr>
		               <td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:14px"><? echo $report_title; ?></strong></td>
		            </tr>
		            <tr>
		               <td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:14px"> <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
		            </tr>
		        </table>
		        <?

				if ($cbo_location==0) $location_id =""; else $location_id =" and a.location=$cbo_location ";
				if($db_type==0)
				{
					if( $date_from==0 && $date_to==0 ) $production_date=""; else $production_date= " and a.production_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
				}
				else
				{
					if( $date_from==0 && $date_to==0 ) $production_date=""; else $production_date= " and a.production_date between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'";
				}


				$sql_result="Select a.id, a.production_type, a.floor_id, a.production_date, b.production_qnty as production_qnty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.production_type in (1,5,8,7,11) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $cbo_company_cond  $location_id $production_date";
				//echo $sql_result;
				$sql_dtls=sql_select($sql_result);
				$floor_qnty=array();

				foreach ( $sql_dtls as $row )
				{
					$floor_qnty[change_date_format($row[csf("production_date")],'','',1)][$row[csf("floor_id")]][$row[csf("production_type")]] +=$row[csf("production_qnty")];
				}
				// echo "<pre>";
				// print_r($floor_qnty);
				$head_arr=array(1=>"Cutting Production",5=>"Sewing Production",7=>"Iron",11=>"Poly",8=>"Packing");
				$unit_wise_total_array=array();
		        $floor_arr = return_library_array("select id,floor_name from  lib_prod_floor ","id","floor_name");
				?>
		        <div align="center" style="height:auto;">
		        <table width="<? echo $table_width; ?> " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
		            <thead>
		                <tr>
		                    <th style="word-wrap: break-word;word-break: break-all;" width="90" rowspan="2" >Date</th>
		                    <?
		                     foreach ( $head_arr as $head=>$headval )
							 {
								if($head==1)
		                        {


		                        ?>
		                            <th style="word-wrap: break-word;word-break: break-all;" width="<? echo $width_cutting; ?>" colspan="<? echo $count_cutting; ?>"><? echo $headval; ?></th>
		                        <?
		                         }

		                        else if($head==5)
		                        {


		                        ?>
		                            <th style="word-wrap: break-word;word-break: break-all;" width="<? echo $width_sewing; ?>" colspan="<? echo $count_sewing; ?>"><? echo $headval; ?></th>
		                        <?
		                         }

								 else if($head==7)
		                        {
		                        ?>
		                            <th style="word-wrap: break-word;word-break: break-all;" width="<? echo $width_iron; ?>" colspan="<? echo $count_iron; ?>"><? echo $headval; ?></th>
		                        <?
		                         }

		                        else if($head==11)
		                        {


		                        ?>
		                            <th style="word-wrap: break-word;word-break: break-all;" width="<? echo $width_poly; ?>" colspan="<? echo $count_poly; ?>"><? echo $headval; ?></th>
		                        <?
		                         }

								else if($head==8)
		                        {


		                        ?>
		                            <th style="word-wrap: break-word;word-break: break-all;" width="<? echo $width_finishing; ?>" colspan="<? echo $count_finishing; ?>"><? echo $headval; ?></th>
		                        <?
		                         }
							 }
							?>
		                </tr>
		               <tr>
							<?
							foreach ( $head_arr as $head=>$headval )
							{
		                        if($head==1)
		                        {
		    						foreach ( $sql_floor_cutting as $rows )
		    						{
		    							?>
		                                    <th style="word-wrap: break-word;word-break: break-all;" width="80" colspan=""><? echo $floor_arr[$rows[csf("floor_id")]]; ?></th>
		    							<?
		    						}
		                        }

		                        else if($head==5)
		                        {
		                            foreach ( $sql_floor_sewing as $rows )
		                            {
		                                ?>
		                                    <th style="word-wrap: break-word;word-break: break-all;" width="80" colspan=""><? echo $floor_arr[$rows[csf("floor_id")]]; ?></th>
		                                <?
		                            }
		                        }

								else if($head==7)
		                        {
		                            foreach ( $sql_floor_iron as $rows )
		                            {
		                                ?>
		                                    <th style="word-wrap: break-word;word-break: break-all;" width="80" colspan=""><? echo $floor_arr[$rows[csf("floor_id")]]; ?></th>
		                                <?
		                            }
		                        }

								else if($head==11)
		                        {
		                            foreach ( $sql_floor_poly as $rows )
		                            {
		                                ?>
		                                    <th style="word-wrap: break-word;word-break: break-all;" width="80" colspan=""><? echo $floor_arr[$rows[csf("floor_id")]]; ?></th>
		                                <?
		                            }
		                        }

								else if($head==8)
		                        {
		                            foreach ( $sql_floor_finishing as $rows )
		                            {
		                                ?>
		                                    <th style="word-wrap: break-word;word-break: break-all;" width="80" colspan=""><? echo $floor_arr[$rows[csf("floor_id")]]; ?></th>
		                                <?
		                            }
		                        }


								?>
								<th style="word-wrap: break-word;word-break: break-all;" width="80" colspan="">Total</th>
							<?
							}
		                   ?>
		               </tr>
		            </thead>
		        </table>
		        </div>
		        <div align="center" style="max-height:300px" id="scroll_body2">
		        <table align="center" cellspacing="0" cellpadding="0" width="<? echo $table_width; ?>"  border="1" rules="all" class="rpt_table" >
					<?
					$value_define=array();
		            for($j=0;$j<$datediff;$j++)
		            {
		                if ($j%2==0)
		                    $bgcolor="#E9F3FF";
		                else
		                    $bgcolor="#FFFFFF";

		                $date_data_array=array();
						$newdate =add_date(str_replace("'","",$txt_date_from),$j);
						$newdate=date("d-M-Y",strtotime(str_replace("'","",$newdate)));
		                $date_data_array[$j]=$newdate;
		            ?>
		            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>">
		                <td style="word-wrap: break-word;word-break: break-all;" width="90"><? echo change_date_format($newdate); ?></td>
		                <?
		                foreach ( $head_arr as $head=>$headval )
		                {
		                    $site_tot_qnty='';
		                    if($head==1)
		                    {
		                        foreach ( $sql_floor_cutting as $rows )
		                        {
		                            ?>
		                                <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><?  echo number_format($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head],0); ?></td>
		                            <?
		                             $site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
		                             $unit_wise_total_array[$head][$rows[csf("floor_id")]] +=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
		                             if($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head]!="")   $value_define[$head][$rows[csf("floor_id")]] +=1;

		                        }
		                        ?>
		                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>
		                        <?
		                    }
		                    else if($head==5)
		                    {
		                        foreach ( $sql_floor_sewing as $rows )
		                        {
		                            ?>
		                                <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><?  echo number_format($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head],0); ?></td>
		                            <?
		                             $site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
		                             $unit_wise_total_array[$head][$rows[csf("floor_id")]] +=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
		                             if($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head]!="")   $value_define[$head][$rows[csf("floor_id")]] +=1;

		                        }
		                        ?>
		                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>
		                        <?
		                    }


							else if($head==7)
		                    {
		                        foreach ( $sql_floor_iron as $rows )
		                        {
		                            ?>
		                                <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><?  echo number_format($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head],0); ?></td>
		                            <?
		                             $site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
		                             $unit_wise_total_array[$head][$rows[csf("floor_id")]] +=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
		                             if($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head]!="")   $value_define[$head][$rows[csf("floor_id")]] +=1;

		                        }
		                        ?>
		                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>
		                        <?
		                    }

							else if($head==11)
		                    {
		                        foreach ( $sql_floor_poly as $rows )
		                        {
		                            ?>
		                                <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><?  echo number_format($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head],0); ?></td>
		                            <?
		                             $site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
		                             $unit_wise_total_array[$head][$rows[csf("floor_id")]] +=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
		                             if($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head]!="")   $value_define[$head][$rows[csf("floor_id")]] +=1;

		                        }
		                        ?>
		                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>
		                        <?
		                    }

							else if($head==8)
		                    {
		                        foreach ( $sql_floor_finishing as $rows )
		                        {
		                            ?>
		                                <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><?  echo number_format($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head],0); ?></td>
		                            <?
		                             $site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
		                             $unit_wise_total_array[$head][$rows[csf("floor_id")]] +=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
		                             if($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head]!="")   $value_define[$head][$rows[csf("floor_id")]] +=1;

		                        }
		                        ?>
		                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>
		                        <?
		                    }

		                }
		                ?>
		            </tr>
		            <?
		            }
		            ?>
		            <tr bgcolor="#dddddd">
		                <td style="word-wrap: break-word;word-break: break-all;" align="center" width="90"><strong>Total : </strong></td>
		                <?
		                foreach ( $head_arr as $head=>$headval )
		                {
		                    $grand_total_tot='';
		                    if($head==1)
		                    {
		                        foreach ( $sql_floor_cutting as $rows )
		                        {
		                            ?>
		                                <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><strong><? echo number_format($unit_wise_total_array[$head][$rows[csf("floor_id")]],0); ?></strong></td>
		                            <?
		                            $grand_total_tot+=$unit_wise_total_array[$head][$rows[csf("floor_id")]];
		                        }
		                    }

		                   else if($head==5)
		                    {
		                        foreach ( $sql_floor_sewing as $rows )
		                        {
		                            ?>
		                                <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><strong><? echo number_format($unit_wise_total_array[$head][$rows[csf("floor_id")]],0); ?></strong></td>
		                            <?
		                            $grand_total_tot+=$unit_wise_total_array[$head][$rows[csf("floor_id")]];
		                        }
		                    }

							else if($head==7)
		                    {
		                        foreach ( $sql_floor_iron as $rows )
		                        {
		                            ?>
		                                <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><strong><? echo number_format($unit_wise_total_array[$head][$rows[csf("floor_id")]],0); ?></strong></td>
		                            <?
		                            $grand_total_tot+=$unit_wise_total_array[$head][$rows[csf("floor_id")]];
		                        }
		                    }

							else if($head==11)
		                    {
		                        foreach ( $sql_floor_poly as $rows )
		                        {
		                            ?>
		                                <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><strong><? echo number_format($unit_wise_total_array[$head][$rows[csf("floor_id")]],0); ?></strong></td>
		                            <?
		                            $grand_total_tot+=$unit_wise_total_array[$head][$rows[csf("floor_id")]];
		                        }
		                    }

							 else if($head==8)
		                    {
		                        foreach ( $sql_floor_finishing as $rows )
		                        {
		                            ?>
		                                <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><strong><? echo number_format($unit_wise_total_array[$head][$rows[csf("floor_id")]],0); ?></strong></td>
		                            <?
		                            $grand_total_tot+=$unit_wise_total_array[$head][$rows[csf("floor_id")]];
		                        }
		                    }

		                    ?>
		                    <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><strong><? echo number_format($grand_total_tot,0); ?></strong></td>
		                    <?
		                }
		                ?>
		            </tr>

		            <tr bgcolor="#dddddd">
		                <td style="word-wrap: break-word;word-break: break-all;" align="center" width="90"><strong>Avg : </strong></td>
		                <?
						$m=1;
		                foreach ( $head_arr as $head=>$headval )
		                {
		                    $avg='';
		                    $avg_tot='';
		                    if($head==1)
		                    {
		                        foreach ( $sql_floor_cutting as $rows )
		                        {
		                            $avg=$unit_wise_total_array[$head][$rows[csf("floor_id")]]/ $value_define[$head][$rows[csf("floor_id")]];
		    						//$avg=$unit_wise_total_array[$head][$rows[csf("id")]]/$value_define[$head][$rows[csf("id")]];
		                            ?>
		                                <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><strong><? echo number_format( $avg,2); ?></strong></td>
		                            <?
		                            $avg_tot+=$avg;
		                        }
		                    }

		                    else if($head==5)
		                    {
		                        foreach ( $sql_floor_sewing as $rows )
		                        {
		                            $avg=$unit_wise_total_array[$head][$rows[csf("floor_id")]]/ $value_define[$head][$rows[csf("floor_id")]];
		                            //$avg=$unit_wise_total_array[$head][$rows[csf("id")]]/$value_define[$head][$rows[csf("id")]];
		                            ?>
		                                <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><strong><? echo number_format( $avg,2); ?></strong></td>
		                            <?
		                            $avg_tot+=$avg;
		                        }
		                    }

							else if($head==7)
		                    {
		                        foreach ( $sql_floor_iron as $rows )
		                        {
		                            $avg=$unit_wise_total_array[$head][$rows[csf("floor_id")]]/ $value_define[$head][$rows[csf("floor_id")]];
		                            //$avg=$unit_wise_total_array[$head][$rows[csf("id")]]/$value_define[$head][$rows[csf("id")]];
		                            ?>
		                                <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><strong><? echo number_format( $avg,2); ?></strong></td>
		                            <?
		                            $avg_tot+=$avg;
		                        }
		                    }

							else if($head==11)
		                    {
		                        foreach ( $sql_floor_poly as $rows )
		                        {
		                            $avg=$unit_wise_total_array[$head][$rows[csf("floor_id")]]/ $value_define[$head][$rows[csf("floor_id")]];
		                            //$avg=$unit_wise_total_array[$head][$rows[csf("id")]]/$value_define[$head][$rows[csf("id")]];
		                            ?>
		                                <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><strong><? echo number_format( $avg,2); ?></strong></td>
		                            <?
		                            $avg_tot+=$avg;
		                        }
		                    }

							else if($head==8)
		                    {
		                        foreach ( $sql_floor_finishing as $rows )
		                        {
		                            $avg=$unit_wise_total_array[$head][$rows[csf("floor_id")]]/ $value_define[$head][$rows[csf("floor_id")]];
		                            //$avg=$unit_wise_total_array[$head][$rows[csf("id")]]/$value_define[$head][$rows[csf("id")]];
		                            ?>
		                                <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><strong><? echo number_format( $avg,2); ?></strong></td>
		                            <?
		                            $avg_tot+=$avg;
		                        }
		                    }

		                    ?>
		                    <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><strong><?  echo number_format($avg_tot,2); ?></strong></td>
		                <?
		                }
		                ?>
		            </tr>
		        </table>
		    </div>
		    </div>
		    <?
		}
	}
	elseif ($type==4) // show2 button
	{
	    $floor_arr = return_library_array("select id,floor_name from  lib_prod_floor ","id","floor_name");
		$lineArr = return_library_array("select a.id,a.line_name from lib_sewing_line a","id","line_name");
		$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

		if ($cbo_location==0) $location_id =""; else $location_id =" and a.location=$cbo_location ";
		if($db_type==0)
		{
			if( $date_from==0 && $date_to==0 ) $production_date=""; else $production_date= " and a.production_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
		}
		else
		{
			if( $date_from==0 && $date_to==0 ) $production_date=""; else $production_date= " and a.production_date between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'";
		}

		//***************************************************************************************************************************
		$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
		order by sewing_line_serial");
		foreach($lineDataArr as $lRow)
		{
			$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
			$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
			$lastSlNo=$lRow[csf('sewing_line_serial')];
		}


		if($db_type==0)
		{
			$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$comapny_id and shift_id=1 and pr_date between $txt_date_from and $txt_date_to and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
		}
		else
		{
			$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$comapny_id and shift_id=1 and pr_date between $txt_date_from and $txt_date_to and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
		}

		if($min_shif_start=="")
		{
			echo "<p style='font-size:20px', align='center'>No Line Engage for the selected Date.Please Check Actual Production Resource Entry.<p/>";die;

		}


		//==============================shift time===================================================================================================
		$start_time_arr=array();
		if($db_type==0)
		{
			$start_time_data_arr=sql_select("SELECT company_name, shift_id, TIME_FORMAT( prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( lunch_start_time, '%H:%i' ) as lunch_start_time from variable_settings_production where company_name in($comapny_id) and shift_id=1  and variable_list=26 and status_active=1 and is_deleted=0");
		}
		else
		{
			$start_time_data_arr=sql_select("SELECT company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time,TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($comapny_id) and  shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");
		}

		foreach($start_time_data_arr as $row)
		{
			$start_time_arr[$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
			$start_time_arr[$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];
		}

		$prod_start_hour=$start_time_arr[1]['pst'];
		$global_start_lanch=$start_time_arr[1]['lst'];
		if($prod_start_hour=="") $prod_start_hour="08:00";
		$start_time=explode(":",$prod_start_hour);
		$hour=substr($start_time[0],1,1); $minutes=$start_time[1]; $last_hour=23;
		$lineWiseProd_arr=array(); $prod_arr=array(); $start_hour_arr=array();
		$start_hour=$prod_start_hour;
		$start_hour_arr[$hour]=$start_hour;
		for($j=$hour;$j<$last_hour;$j++)
		{
			$start_hour=add_time($start_hour,60);
			$start_hour_arr[$j+1]=substr($start_hour,0,5);
		}
		//echo $pc_date_time;die;
		$start_hour_arr[$j+1]='23:59';
		if($prod_start_hour>$min_shif_start)  $prod_start_hour=$min_shif_start;
		$actual_date=date("Y-m-d");
		$actual_production_date=date("Y-m-d",strtotime(str_replace("'","",$txt_date_from)));
		$actual_time=substr(date("Y-m-d H:i:s",strtotime($pc_date_time)),11,2);
		$acturl_hour_minute=date("H:i",strtotime($pc_date_time));
		$generated_hourarr=array();
		$first_hour_time=explode(":",$min_shif_start);
		$hour_line=substr($first_hour_time[0],1,1); $minutes_one=$start_time[1];
		$line_start_hour_arr[$hour_line]=$min_shif_start;

		for($l=$hour_line;$l<$last_hour;$l++)
		{
			$min_shif_start=add_time($min_shif_start,60);
			$line_start_hour_arr[$l+1]=substr($min_shif_start,0,5);
		}

		$line_start_hour_arr[$j+1]='23:59';
		$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_id and variable_list=23 and is_deleted=0
		and status_active=1");

		//if(str_replace("'","",$cbo_company_id)==0) $company_name=""; else $company_name="and a.company_id=".str_replace("'","",$cbo_company_id)."";
		if(str_replace("'","",$cbo_company_id)==0) $company_name=""; else $company_name="and a.serving_company=".str_replace("'","",$cbo_company_id)."";

		if(str_replace("'","",$cbo_location_id)==0)
		{
			$subcon_location="";
			$location="";
		}
		else
		{
			$location=" and a.location=".str_replace("'","",$cbo_location_id)."";
			$subcon_location=" and a.location_id=".str_replace("'","",$cbo_location_id)."";
		}
		$cbo_floor_id=str_replace("'","",$cbo_floor_id);
		if($cbo_floor_id=="") $floor=""; else $floor="and a.floor_id in(".$cbo_floor_id.")";
	    if(str_replace("'","",$hidden_line_id)==0)
		{
			$line="";
			$subcon_line="";
		}
		else
		{
			$subcon_line="and a.line_id in(".str_replace("'","",$hidden_line_id).")";
			$line="and a.sewing_line in(".str_replace("'","",$hidden_line_id).")";
		}
		$cbo_no_prod_type=str_replace("'","",$cbo_no_prod_type);
		$file_no=str_replace("'","",$txt_file_no);
		$ref_no=str_replace("'","",$txt_ref_no);
		if($file_no!="") $file_cond="and c.file_no=$file_no";else $file_cond="";
		if($ref_no!="") $ref_cond="and c.grouping='$ref_no'";else $ref_cond="";
		//echo $file_cond;

		// if(str_replace("'","",trim($txt_date_from))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date between $txt_date_from and $txt_date_to";
		// echo $txt_date_from; die;

		/* =============================================================================================/
		/									Prod Resource Data											/
		/============================================================================================= */
		if($prod_reso_allo[0]==1)
		{
			$prod_resource_array=array();

			$dataArray_sql=sql_select("SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id=$comapny_id and b.pr_date between $txt_date_from and $txt_date_to and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0");
			foreach($dataArray_sql as $val)
			{
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['man_power']=$val[csf('man_power')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['operator']=$val[csf('operator')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['helper']=$val[csf('helper')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['terget_hour']=$val[csf('target_per_hour')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['working_hour']=$val[csf('working_hour')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['tpd']=$val[csf('target_per_hour')]*$val[csf('working_hour')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_start']=$val[csf('from_date')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_end']=$val[csf('to_date')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['capacity']=$val[csf('capacity')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust']=$val[csf('smv_adjust')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];
			}
			// print_r($prod_resource_array);die();
			if(str_replace("'","",trim($txt_date_from))==""){$pr_date_con="";}else{$pr_date_con=" and b.pr_date between $txt_date_from and $txt_date_to";}

			if($db_type==0)
			{
				$dataArray=sql_select("SELECT a.id,b.pr_date,d.shift_id,TIME_FORMAT( d.prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( d.lunch_start_time, '%H:%i' ) as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 $pr_date_con");
			}
			else
			{
				$dataArray=sql_select("SELECT a.id,b.pr_date,d.shift_id,TO_CHAR(d.prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR( d.lunch_start_time,'HH24:MI') as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 $pr_date_con");
			}

			$line_number_arr=array();
			foreach($dataArray as $val)
			{
				$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['shift_id']=$val[csf('shift_id')];
				$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['prod_start_time']=$val[csf('prod_start_time')];
				$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['lunch_start_time']=$val[csf('lunch_start_time')];
			}
		}
	 	//***************************************************************************************************
	  	if($db_type==0)
		{
			$manufacturing_company=return_field_value("group_concat(comp.id) as company_id","lib_company as comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
		}
		else
		{
			$manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
		}


		if($db_type==0) $prod_start_cond="prod_start_time";
		else if($db_type==2) $prod_start_cond="TO_CHAR(prod_start_time,'DD-MON-YYYY HH24:MI')";

		$variable_start_time_arr='';
		$prod_start_time=sql_select("SELECT $prod_start_cond as prod_start_time from variable_settings_production where company_name=$cbo_company_id and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");
		//echo "select company_name, prod_start_time from variable_settings_production where company_name=$cbo_company_id and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1";
		foreach($prod_start_time as $row)
		{
			$ex_time=explode(" ",$row[csf('prod_start_time')]);
			if($db_type==0) $variable_start_time_arr=$row[csf('prod_start_time')];
			else if($db_type==2) $variable_start_time_arr=$ex_time[1];
		}//die;
		//echo $variable_start_time_arr;
		unset($prod_start_time);
		$current_date_time=date('d-m-Y H:i');
		$variable_date=change_date_format(str_replace("'","",$txt_date_from)).' '.$variable_start_time_arr;
		//echo $variable_date.'='.$current_date_time;
		$datediff_=datediff("n",$variable_date,$current_date_time);

		$ex_date_time=explode(" ",$current_date_time);
		$current_date=$ex_date_time[0];
		$current_time=$ex_date_time[1];
		$ex_time=explode(":",$current_time);

		$search_prod_date=change_date_format(str_replace("'","",$txt_date_from));
		$current_eff_min=($ex_time[0]*60)+$ex_time[1];
		//echo $current_date.'='.$search_prod_date;
		$variable_time= explode(":",$variable_start_time_arr);
		$vari_min=($variable_time[0]*60)+$variable_time[1];
		$difa_time=explode(".",number_format(($current_eff_min-$vari_min)/60,2));//datediff("",$ctime,$variable_start_time_arr);
		$dif_time=number_format($datediff_/60,2);
		$dif_hour_min=date("H", strtotime($dif_time));

	   	//$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($manufacturing_company) and variable_list=25 and   status_active=1 and is_deleted=0");

		/* =============================================================================================/
		/											SMV Source											/
		/============================================================================================= */
	   	$smv_source=return_field_value("smv_source","variable_settings_production","company_name =$comapny_id and variable_list=25 and   status_active=1 and is_deleted=0");


		 //echo $smv_source;die;
	    if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;

	    if($smv_source==3)
		{
			$sql_item="SELECT b.id, a.sam_style, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0
		and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$resultItem=sql_select($sql_item);

			foreach($resultItem as $itemData)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
			}
		}
		else
		{
			$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost,c.smv_set from wo_po_details_master a,wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.company_name in($manufacturing_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$resultItem=sql_select($sql_item);

			foreach($resultItem as $itemData)
			{
				if($smv_source==1)
				{
					$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
				}
				if($smv_source==2)
				{
					$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs_precost')];
				}
			}
		}
		// echo $sql_item;

		if($db_type==2)
		{
			$pr_date= str_replace("'", "", $txt_date_from);
			$pr_date_old=explode("-",str_replace("'","",$txt_date_from));
			$month=strtoupper($pr_date_old[1]);
			$year=substr($pr_date_old[2],2);
			$pr_date=$pr_date_old[0]."-".$month."-".$year;
		}
		if($db_type==0)
		{
			$pr_date=str_replace("'","",$txt_date_from);
		}

		$i=1; $grand_total_good=0; $grand_alter_good=0; $grand_total_reject=0;
		$html="";
		$floor_html="";
	    $check_arr=array();
		/* =============================================================================================/
		/									Prod Inhouse Data											/
		/============================================================================================= */
		if($db_type==0)
		{
			$sql="SELECT  a.company_id, a.location, a.floor_id,d.floor_serial_no,e.sewing_line_serial, a.prod_reso_allo, a.production_date, a.sewing_line,
			b.buyer_name  as buyer_name,b.style_ref_no,b.job_no,a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.unit_price,c.file_no,c.grouping as ref,sum(a.production_quantity) as good_qnty,";
			$first=1;
			for($h=$hour;$h<$last_hour;$h++)
			{
				$bg=$start_hour_arr[$h];
				$end=substr(add_time($start_hour_arr[$h],60),0,5);
				$prod_hour="prod_hour".substr($bg,0,2);
				if($first==1)
				{
					$sql.="sum(CASE WHEN   a.production_hour<='$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,";
				}
				else
				{
					$sql.="sum(CASE WHEN a.production_hour>'$bg' and  a.production_hour<='$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,";
				}
				$first=$first+1;
			}
			$sql.="sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour23 from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a left join lib_prod_floor d on a.floor_id=d.id and d.is_deleted=0 left join lib_sewing_line e on a.sewing_line=e.id and e.is_deleted=0  where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $company_name $location $floor $line $buyer_id_cond $production_date $file_cond $ref_cond group by b.job_no,a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,e.sewing_line_serial,b.buyer_name,a.item_number_id,c.po_number,c.file_no,c.unit_price,c.grouping,b.style_ref_no order by a.location, a.floor_id,e.sewing_line_serial,a.prod_reso_allo";
		}
		else if($db_type==2)
		{
			$sql="SELECT  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name  as buyer_name,b.style_ref_no,b.job_no, a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.file_no,c.unit_price,c.grouping as ref,sum(d.production_qnty) as good_qnty,";
			$first=1;
			for($h=$hour;$h<$last_hour;$h++)
			{
				$bg=$start_hour_arr[$h];
				$end=substr(add_time($start_hour_arr[$h],60),0,5);
				$prod_hour="prod_hour".substr($bg,0,2);
				if($first==1)
				{
					$sql.="sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN production_qnty else 0 END) AS $prod_hour,";
				}
				else
				{
					$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5
					THEN production_qnty else 0 END) AS $prod_hour,";
				}
				$first++;
			}
			$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN production_qnty else 0 END) AS prod_hour23
			FROM  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a ,pro_garments_production_dtls d
			WHERE a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_name $location $floor $line $buyer_id_cond  $production_date $file_cond $ref_cond
			GROUP BY b.job_no, a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name,b.style_ref_no,a.item_number_id,c.po_number,c.unit_price,c.file_no,c.grouping
			ORDER BY a.location,a.floor_id,a.sewing_line";
		}
		//echo $sql;die;
		$sql_resqlt=sql_select($sql);
		$production_data_arr=array();
		$production_po_data_arr=array();
		$production_serial_arr=array(); $reso_line_ids=''; $all_po_id="";
		$active_days_arr=array();
		$duplicate_date_arr=array();
		foreach($sql_resqlt as $val)
		{

			if($val[csf('prod_reso_allo')]==1)
			{
				$sewing_line_id=$prod_reso_arr[$val[csf('sewing_line')]];
				$reso_line_ids.=$val[csf('sewing_line')].',';
			}
			else
			{
				$sewing_line_id=$val[csf('sewing_line')];
			}

			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id]=$slNo;
			}
			else $slNo=$lineSerialArr[$sewing_line_id];
			$production_serial_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]]=$val[csf('sewing_line')];

			$line_start=$line_number_arr[$val[csf('sewing_line')]][$val[csf('production_date')]]['prod_start_time'];
			if($line_start!="")
			{
				$line_start_hour=substr($line_start,0,2);
				if(substr($line_start_hour,0,1)==0)  $line_start_hour=substr($line_start_hour,1,1);
			}
			else
			{
				$line_start_hour=$hour;
			}

		 	for($h=$hour;$h<$last_hour;$h++)
			{
				$prod_hour="prod_hour".substr($start_hour_arr[$h],0,2)."";
				$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$prod_hour]+=$val[csf($prod_hour)];

				if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
				{
					if( $h>=$line_start_hour && $h<=$actual_time)
					{
						$production_po_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf($prod_hour)];
					}
				}

				if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date))
				{
					$production_po_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf($prod_hour)];
				}
			}

			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
			{
				if( $h>=$line_start_hour && $h<=$actual_time)
				{
					$production_po_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf('prod_hour23')];
				}
			}
			else
			{
				$production_po_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf('prod_hour23')];
			}

		 	$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['prod_hour23']+=$val[csf('prod_hour23')];
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['prod_reso_allo']=$val[csf('prod_reso_allo')];

		 	if($production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']!="")
			{
				$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name'].=",".$val[csf('buyer_name')];
			}
		 	else
			{
				$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']=$val[csf('buyer_name')];
			}

		 	if($production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']!="")
			{
				$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number'].=",".$val[csf('po_number')];
				$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['job_no'].=",".$val[csf('job_no')];
				$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_id'].=",".$val[csf('po_break_down_id')];
				$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['style'].=",".$val[csf('style_ref_no')];
				$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['file'].=",".$val[csf('file_no')];
				$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['ref'].=",".$val[csf('ref')];
			}
		 	else
			{
				$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']=$val[csf('po_number')];
				$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['job_no']=$val[csf('job_no')];
				$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_id']=$val[csf('po_break_down_id')];
				$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['style']=$val[csf('style_ref_no')];
				$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['file']=$val[csf('file_no')];
				$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['ref']=$val[csf('ref')];
			}
			$fob_rate_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['rate']=$val[csf('unit_price')];

			if($production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']!="")
			{
				$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')];
			}
			else
			{
				 $production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')];
			}
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['quantity']+=$val[csf('good_qnty')];
			$production_data_arr_qty[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]['quantity']+=$val[csf('good_qnty')];

			if($all_po_id=="") $all_po_id=$val[csf('po_break_down_id')]; else $all_po_id.=",".$val[csf('po_break_down_id')];
		}
		// print_r($production_data_arr_qty);die();
		$po_ids=count(array_unique(explode(",",$all_po_id)));
		$po_numIds=chop($all_po_id,',');
		$poIds_cond="";
		$poIds_cond2="";
		if($all_po_id!='' || $all_po_id!=0)
		{
			if($db_type==2 && $po_ids>1000)
			{
				$poIds_cond=" and (";
				$poIds_cond2=" and (";
				$poIdsArr=array_chunk(explode(",",$po_numIds),990);
				foreach($poIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$poIds_cond.=" b.id  in($ids) or ";
					$poIds_cond2.=" c.id  in($ids) or ";
				}
				$poIds_cond=chop($poIds_cond,'or ');
				$poIds_cond2=chop($poIds_cond,'or ');
				$poIds_cond.=")";
				$poIds_cond2.=")";
			}
			else
			{
				$poIds_cond=" and  b.id  in($all_po_id)";
				$poIds_cond2=" and  c.id  in($all_po_id)";
			}
		}

		/* =============================================================================================/
		/										Active PO Data											/
		/============================================================================================= */
	    $po_active_sql="SELECT a.floor_id,a.sewing_line,a.production_date,a.po_break_down_id,a.item_number_id from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $company_name $location $floor $line $buyer_id_cond   $file_cond $ref_cond $poIds_cond2 group by  a.floor_id,a.sewing_line,a.production_date ,a.po_break_down_id,a.item_number_id";
	    //echo $po_active_sql;die;
		foreach(sql_select($po_active_sql) as $vals)
		{
			$prod_dates=$vals[csf('production_date')];
			if($duplicate_date_arr[$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]][$prod_dates]=="")
			{
				$active_days_arr[$vals[csf('floor_id')]][$vals[csf('sewing_line')]]+=1;
				$active_days_arr_powise[$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]]+=1;
				$duplicate_date_arr[$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]][$prod_dates]=$prod_dates;
			}

		}
		//print_r($duplicate_date_arr);

		$sql_item_rate="SELECT b.id ,c.item_number_id, c.order_quantity, c.order_total from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c where b.job_no_mst=a.job_no and b.id=c.po_break_down_id and b.job_no_mst=c.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1  $file_cond $ref_cond  $poIds_cond";
		$resultRate=sql_select($sql_item_rate);
		$item_po_array=array();
		foreach($resultRate as $row)
		{
			$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['qty']+=$row[csf('order_quantity')];
			$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['amt']+=$row[csf('order_total')];
		}

		/* =============================================================================================/
		/										Prod Subcon Data										/
		/============================================================================================= */

	    if($db_type==0)
	    {
			$sql_sub_contuct= "SELECT  a.company_id, a.location_id,d.floor_serial_no,e.sewing_line_serial,a.prod_reso_allo, a.floor_id, a.production_date, a.line_id,b.party_id  as buyer_name,a.order_id,c.order_no as po_number,c.cust_style_ref,b.subcon_job as job_no, max(c.smv) as smv,sum(a.production_qnty) as good_qnty,";

			$first=1;
			for($h=$hour;$h<$last_hour;$h++)
			{
				$bg=$start_hour_arr[$h];
				$end=substr(add_time($start_hour_arr[$h],60),0,5);
				$prod_hour="prod_hour".substr($bg,0,2);
				if($first==1)
				{
					$sql_sub_contuct.="sum(CASE WHEN  a.hour<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,";
				}
				else
				{
					$sql_sub_contuct.="sum(CASE WHEN a.hour>'$bg' and a.hour<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,";
				}
				$first=$first+1;
	   		}
	   		$sql_sub_contuct.="sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a left join lib_prod_floor d on a.floor_id=d.id and d.is_deleted=0 left join lib_sewing_line e on a.line_id=e.id and e.is_deleted=0 where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id=$comapny_id $subcon_location $floor $subcon_line   $production_date group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date,a.prod_reso_allo,a.line_id,b.party_id,c.order_no,c.cust_style_ref,b.subcon_job ,e.sewing_line_serial order by a.location_id,d.floor_serial_no,e.sewing_line_serial,a.prod_reso_allo";
		}
		else
		{
			$sql_sub_contuct= "select  a.company_id, a.location_id, a.floor_id,d.floor_serial_no,e.sewing_line_serial, a.production_date, a.line_id,b.party_id  as buyer_name,a.prod_reso_allo,a.order_id,c.order_no as po_number,c.cust_style_ref, b.subcon_job as job_no,  max(c.smv) as smv,a.prod_reso_allo,sum(a.production_qnty) as good_qnty,";
			$first=1;
			for($h=$hour;$h<$last_hour;$h++)
			{
				$bg=$start_hour_arr[$h];
				$end=substr(add_time($start_hour_arr[$h],60),0,5);
				$prod_hour="prod_hour".substr($bg,0,2);
				if($first==1)
				{
					$sql_sub_contuct.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,";
				}
				else
				{
					$sql_sub_contuct.="sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')>'$bg' and TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,";
				}
				$first++;
			}

		   	$sql_sub_contuct.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]'	and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a left join lib_prod_floor d on a.floor_id=d.id  and d.is_deleted=0 left join lib_sewing_line e on a.line_id=e.id and e.is_deleted=0 where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id=$comapny_id $subcon_location $floor $subcon_line $production_date group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date,  b.subcon_job,a.line_id,b.party_id,c.order_no,c.cust_style_ref,e.sewing_line_serial,a.prod_reso_allo order by a.location_id, a.floor_id,e.sewing_line_serial,a.prod_reso_allo";

		}
		//echo $sql_sub_contuct;die;
		$sub_result=sql_select($sql_sub_contuct);
		$subcon_order_smv=array();
		foreach($sub_result as $subcon_val)
		{

			if($val[csf('prod_reso_allo')]==1)
			{
				$sewing_line_id=$prod_reso_arr[$subcon_val[csf('sewing_line')]];
			}
			else
			{
				$sewing_line_id=$subcon_val[csf('sewing_line')];
			}

			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id]=$slNo;
			}
			else $slNo=$lineSerialArr[$sewing_line_id];
			//$production_serial_arr[$subcon_val[csf('floor_id')]][$slNo][$subcon_val[csf('sewing_line')]]=$subcon_val[csf('sewing_line')];

			$production_po_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$subcon_val[csf('good_qnty')];
			if($production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['buyer_name']!="")
			{
				$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['buyer_name'].=",".$subcon_val[csf('buyer_name')];
			}
			else
			{
				$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['buyer_name']=$subcon_val[csf('buyer_name')];
			}

			if($production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number']!="")
			{
				$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number'].=",".$subcon_val[csf('po_number')];
				$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['job_no'].=",".$subcon_val[csf('job_no')];
				$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['style'].=",".$subcon_val[csf('cust_style_ref')];
			}
			else
			{
				$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number']=$subcon_val[csf('po_number')];
				$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['job_no']=$subcon_val[csf('job_no')];
				$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['style']=$subcon_val[csf('cust_style_ref')];
			}

			if($production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['order_id']!="")
			{
				$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['order_id'].=",".$subcon_val[csf('order_id')];
			}
			else
			{
				$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['order_id'].=$subcon_val[csf('order_id')];
			}
			$subcon_order_smv[$subcon_val[csf('order_id')]]=$subcon_val[csf('smv')];
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['quantity']+=$subcon_val[csf('good_qnty')];

		 	$line_start=$line_number_arr[$val[csf('line_id')]][$val[csf('production_date')]]['prod_start_time']	;
		 	if($line_start!="")
		 	{
				$line_start_hour=substr($line_start,0,2);
				if(substr($line_start_hour,0,1)==0)  $line_start_hour=substr($line_start_hour,1,1);
		 	}
			else
		 	{
				$line_start_hour=$hour;
		 	}
			for($h=$hour;$h<=$last_hour;$h++)
			{
				$prod_hour="prod_hour".substr($start_hour_arr[$h],0,2)."";
				$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$prod_hour]+=$subcon_val[csf($prod_hour)];
				if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
				{
					 if( $h>=$line_start_hour && $h<=$actual_time)
					 {
					 $production_po_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf($prod_hour)];	                 }
				}
				if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
				{
					$production_po_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf($prod_hour)];	            }
			 }
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
			{
				if( $h>=$line_start_hour && $h<=$actual_time)
				{
					$production_po_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf('prod_hour23')];
				}
			}
			else
			{
				$production_po_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf('prod_hour23')];
			}
			$production_data_arr[$subcon_val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('line_id')]]['prod_hour23']+=$val[csf('prod_hour23')];
		}
		//For Summary Report New Add No Prodcut
		$cbo_no_prod_type=1;
		if($cbo_no_prod_type==1)
		{

		/* =============================================================================================/
		/									No Production line Start									/
		/============================================================================================= */
		$prod_reso_arr=return_library_array( "SELECT id, line_number from prod_resource_mst",'id','line_number');
		$sql_active_line=sql_select("SELECT sewing_line,sum(production_quantity) as total_production from pro_garments_production_mst  where production_date between $txt_date_from and $txt_date_to and production_type=5 and status_active=1 and is_deleted=0 and prod_reso_allo=1 group by  sewing_line");
					//$actual_line_arr=array();
					foreach($sql_active_line as $inf)
					{
					   if(str_replace("","",$inf[csf('sewing_line')])!="")
					   {
							//if(str_replace("","",$actual_line_arr)!="") $actual_line_arr.=",";
						    //$actual_line_arr.="'".$inf[csf('sewing_line')]."'";
					   }
					}
							//echo $actual_line_arr;die;
				$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$comapny_id and variable_list=23 and is_deleted=0 and status_active=1");
				$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
				if(str_replace("'","",$cbo_location_id)==0)
				{
				$location_cond="";
				}
				else
				{
				$location=" and a.location_id=".str_replace("'","",$cbo_location_id)."";
				}

				if(str_replace("'","",$cbo_floor_id)==0) $floor=""; else $floor="and a.floor_id=".str_replace("'","",$cbo_floor_id)."";
				$lin_ids=str_replace("'","",$hidden_line_id);
				$res_line_cond=rtrim($reso_line_ids,",");

				 $dataArray_sum=sql_select("SELECT a.id, a.floor_id,1 as prod_reso_allo,2 as type_line, a.line_number as line_no, b.man_power, b.operator,b.pr_date, b.helper, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type,d.prod_start_time from  prod_resource_dtls b,prod_resource_dtls_time d,prod_resource_mst a  where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and b.pr_date between $txt_date_from and $txt_date_to and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 and a.id not in($res_line_cond) and a.line_number like '%$lin_ids%'  $location  $floor  group by a.id, a.floor_id, a.line_number, d.prod_start_time,b.man_power, b.operator, b.helper,b.pr_date, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type order by a.floor_id");
				 $no_prod_line_arr=array();
				 foreach( $dataArray_sum as $row)
				 {

					$sewing_line_id=$row[csf('line_no')];

					if($lineSerialArr[$sewing_line_id]=="")
					{
						$lastSlNo++;
						$slNo=$lastSlNo;
						$lineSerialArr[$sewing_line_id]=$slNo;
					}
					else $slNo=$lineSerialArr[$sewing_line_id];

					 $production_serial_arr[$row[csf('pr_date')]][$row[csf('floor_id')]][$slNo][$row[csf('id')]]=$row[csf('id')];
					 $production_data_arr[$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['type_line']=$row[csf('type_line')];
					 $production_data_arr[$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['prod_reso_allo']=$row[csf('prod_reso_allo')];
					 $production_data_arr[$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['man_power']=$row[csf('man_power')];
					 $production_data_arr[$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['operator']=$row[csf('operator')];
					 $production_data_arr[$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['helper']=$row[csf('helper')];
					 $production_data_arr[$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['working_hour']=$row[csf('working_hour')];
					 $production_data_arr[$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['terget_hour']=$row[csf('target_per_hour')];
					 $production_data_arr[$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['total_line_hour']=$row[csf('man_power')]*$row[csf('working_hour')];
					 $production_data_arr[$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['smv_adjust']=$row[csf('smv_adjust')];
					 $production_data_arr[$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['smv_adjust_type']=$row[csf('smv_adjust_type')];
					 $production_data_arr[$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['prod_start_time']=$row[csf('prod_start_time')];
				 }
				 $dataArray_sql_cap=sql_select("SELECT  a.floor_id, a.line_number as line_no,b.pr_date,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id  and a.company_id=$comapny_id and b.pr_date between $txt_date_from and $txt_date_to  and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  group by a.id,a.line_number,b.pr_date, a.floor_id, a.line_number, b.man_power, b.operator,c.capacity, b.helper, b.working_hour,b.target_per_hour order by a.floor_id");

				 //$prod_resource_array_summary=array();
				 foreach( $dataArray_sql_cap as $row)
				 {
					 $production_data_arr[$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('line_no')]]['capacity']=$row[csf('capacity')];
				 }

		} //End

		// echo "<pre>"; print_r($production_serial_arr);die;

		$avable_min=0;
		$today_product=0;
	    $floor_name="";
	    $floor_man_power=0;
		$floor_operator=$floor_produc_min=0;
		$floor_smv=$floor_row=$floor_helper=$floor_tgt_h=$floor_days_run=$floor_working_hour=$line_floor_production=$floor_today_product=$floor_avale_minute=0;
		$total_operator=$total_helper=$gnd_hit_rate=0;
	    $total_smv=$total_terget=$grand_total_product=$gnd_line_effi=0;
	    $total_man_power=$gnd_avable_min=$gnd_product_min=0;
		$item_smv=$item_smv_total=$line_efficiency=$days_run=$days_active=$total_working_hour=$gnd_total_tgt_h=$total_capacity=0;
		$j=1;

		$line_number_check_arr=array();
		$smv_for_item="";
		$total_production=array();
		$floor_production=array();
	    $line_floor_production=0;
	    $line_total_production=0; $gnd_total_fob_val=0; $gnd_final_total_fob_val=0;

	    foreach($production_serial_arr as $pdate=>$date_data)
	    {
			foreach($date_data as $f_id=>$fname)
			{
				ksort($fname);
				foreach($fname as $sl=>$s_data)
				{
					foreach($s_data as $l_id=>$ldata)
					{
					  	$po_value=$production_data_arr[$pdate][$f_id][$ldata]['po_number'];
					  	if($po_value)
					  	{
							//$item_ids=$production_data_arr[$f_id][$ldata]['item_number_id'];
							$germents_item=array_unique(explode('****',$production_data_arr[$pdate][$f_id][$ldata]['item_number_id']));

							$buyer_neme_all=array_unique(explode(',',$production_data_arr[$pdate][$f_id][$ldata]['buyer_name']));
							$buyer_name="";
							foreach($buyer_neme_all as $buy)
							{
								if($buyer_name!='') $buyer_name.=',';
								$buyer_name.=$buyerArr[$buy];
							}
							$garment_itemname='';
							$active_days='';
							$item_smv="";$item_ids='';
							$smv_for_item="";
							$produce_minit="";
							$order_no_total="";
							$efficiency_min=0;
							$tot_po_qty=0;$fob_val=0;
							foreach($germents_item as $g_val)
							{
								$po_garment_item=explode('**',$g_val);
								if($garment_itemname!='') $garment_itemname.=',';
								$garment_itemname.=$garments_item[$po_garment_item[1]];
								if($item_ids=='') $item_ids=$po_garment_item[1];else $item_ids.=",".$po_garment_item[1];
								if($active_days=="")$active_days=$active_days_arr_powise[$po_garment_item[0]][$po_garment_item[1]];
								else $active_days.=','.$active_days_arr_powise[$po_garment_item[0]][$po_garment_item[1]];


								//echo $item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt'].'<br>';
								$tot_po_qty+=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];
								$tot_po_amt+=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt'];
								if($item_smv!='') $item_smv.='/';
								//echo $po_garment_item[0].'='.$po_garment_item[1];

								$item_smv.=$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
								if($order_no_total!="") $order_no_total.=",";
								$order_no_total.=$po_garment_item[0];
								if($smv_for_item!="") $smv_for_item.="****".$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
								else
								$smv_for_item=$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
								$produce_minit+=$production_po_data_arr[$pdate][$f_id][$l_id][$po_garment_item[0]]*$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
								$fob_rate=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt']/$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];
								$prod_qty=$production_data_arr_qty[$f_id][$l_id][$po_garment_item[0]][$po_garment_item[1]]['quantity'];
								//echo $prod_qty.'<br>';
								if(is_nan($fob_rate)){ $fob_rate=0; }
								$fob_val+=$prod_qty*$fob_rate;
							}
							//$fob_rate=$tot_po_amt/$tot_po_qty;

							$subcon_po_id=array_unique(explode(',',$production_data_arr[$pdate][$f_id][$ldata]['order_id']));
							$subcon_order_id="";
							foreach($subcon_po_id as $sub_val)
							{
								$subcon_po_smv=explode(',',$sub_val);
								if($sub_val!=0)
								{
								if($item_smv!='') $item_smv.='/';
								if($item_smv!='') $item_smv.='/';
								$item_smv.=$subcon_order_smv[$sub_val];
								}
								$produce_minit+=$production_po_data_arr[$pdate][$f_id][$l_id][$sub_val]*$subcon_order_smv[$sub_val];
								if($subcon_order_id!="") $subcon_order_id.=",";
								$subcon_order_id.=$sub_val;
							}
							if($order_no_total!="")
							{
								$day_run_sql=sql_select("SELECT min(production_date) as min_date from pro_garments_production_mst
								where po_break_down_id in(".$order_no_total.")  and production_type=4");
								foreach($day_run_sql as $row_run)
								{
								$sewing_day=$row_run[csf('min_date')];
								}
								if($sewing_day!="")
								{
								$days_run=datediff("d",$sewing_day,$pdate);
								}
								else  $days_run=0;
							}
							$type_line=$production_data_arr[$pdate][$f_id][$ldata]['type_line'];
							$prod_reso_allo=$production_data_arr[$pdate][$f_id][$ldata]['prod_reso_allo'];
							/*if($type_line==2)
							{
								 $sewing_line='';
								if($production_data_arr[$f_id][$ldata]['prod_reso_allo']==1)
								{
									$line_number='';
									$line_number=explode(",",$ldata);
									foreach($line_data as $lin_id)
									{
										//echo $lin_id.'dd';
										$line_number=explode(",",$prod_reso_arr[$lin_id]);
									}
									foreach($line_number as $val)
									{
										if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
									}
								}
								else $sewing_line=$lineArr[$lin_id];
							}
							else
							{*/
								$sewing_line='';
								if($production_data_arr[$pdate][$f_id][$ldata]['prod_reso_allo']==1)
								{
									$line_number=explode(",",$prod_reso_arr[$ldata]);
									foreach($line_number as $val)
									{
										if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
									}
								}
								else $sewing_line=$lineArr[$ldata];
							//}

							/*$sewing_line='';
							if($production_data_arr[$f_id][$ldata]['prod_reso_allo']==1)
							{
							$line_number=explode(",",$prod_reso_arr[$ldata]);
							foreach($line_number as $val)
							{
							if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
							}
							}
							else $sewing_line=$lineArr[$ldata];*/


							$lunch_start="";
							$lunch_start=$line_number_arr[$ldata][$pdate]['lunch_start_time'];
							$lunch_hour=$start_time_arr[$row[1]]['lst'];
							if($lunch_start!="")
							{
							$lunch_start_hour=$lunch_start;
							}
							else
							{
							$lunch_start_hour=$lunch_hour;
							}

							$production_hour=array();
							for($h=$hour;$h<=$last_hour;$h++)
							{
								 $prod_hour="prod_hour".substr($line_start_hour_arr[$h],0,2)."";
								 $production_hour[$prod_hour]=$production_data_arr[$pdate][$f_id][$ldata][$prod_hour];
								 $floor_production[$prod_hour]+=$production_data_arr[$pdate][$f_id][$ldata][$prod_hour];
								 $total_production[$prod_hour]+=$production_data_arr[$pdate][$f_id][$ldata][$prod_hour];
							}

			 				$floor_production['prod_hour24']+=$production_data_arr[$pdate][$f_id][$ldata]['prod_hour23'];
							$total_production['prod_hour24']+=$production_data_arr[$pdate][$f_id][$ldata]['prod_hour23'];
							$production_hour['prod_hour24']=$production_data_arr[$pdate][$f_id][$ldata]['prod_hour23'];
							$line_production_hour=0;
							if(str_replace("'","",$actual_production_date)>str_replace("'","",$actual_date))
							{
								if($type_line==2) //No Profuction Line
								{
									$line_start=$production_data_arr[$pdate][$f_id][$l_id]['prod_start_time'];
								}
								else
								{
									$line_start=$line_number_arr[$ldata][$pdate]['prod_start_time'];
								}
								if($line_start!="")
								{
									$line_start_hour=substr($line_start,0,2);
									if(substr($line_start_hour,0,1)==0)  $line_start_hour=substr($line_start_hour,1,1);
								}
								else
								{
									$line_start_hour=$hour;
								}
								$actual_time_hour=0;
								$total_eff_hour=0;
								for($lh=$line_start_hour;$lh<=$last_hour;$lh++)
								{
									$bg=$start_hour_arr[$lh];
									if($lh<$actual_time)
									{
									$total_eff_hour=$total_eff_hour+1;;
									$line_hour="prod_hour".substr($bg,0,2)."";
									echo $line_production_hour+=$production_data_arr[$pdate][$f_id][$ldata][$line_hour];
									$line_floor_production+=$production_data_arr[$pdate][$f_id][$ldata][$line_hour];
									$line_total_production+=$production_data_arr[$pdate][$f_id][$ldata][$line_hour];
									$actual_time_hour=$start_hour_arr[$lh+1];
									}
								}
			 					if($start_hour_arr[$actual_time]>$lunch_start_hour) $total_eff_hour=$total_eff_hour-1;

								if($type_line==2)
								{
									if($total_eff_hour>$production_data_arr[$pdate][$f_id][$l_id]['working_hour'])
									{
										 $total_eff_hour=$production_data_arr[$pdate][$f_id][$l_id]['working_hour'];
									}
								}
								else
								{
									if($total_eff_hour>$prod_resource_array[$ldata][$pdate]['working_hour'])
									{
										$total_eff_hour=$prod_resource_array[$ldata][$pdate]['working_hour'];
									}
								}

							}
							if(str_replace("'","",$actual_production_date)<=str_replace("'","",$actual_date))
							{
								for($ah=$hour;$ah<=$last_hour;$ah++)
								{
									$prod_hour="prod_hour".substr($start_hour_arr[$ah],0,2)."";
									$line_production_hour+=$production_data_arr[$pdate][$f_id][$ldata][$prod_hour];
									$line_floor_production+=$production_data_arr[$pdate][$f_id][$ldata][$prod_hour];
									$line_total_production+=$production_data_arr[$pdate][$f_id][$ldata][$prod_hour];
								}
								if($type_line==2)
								{
									$total_eff_hour=$production_data_arr[$pdate][$f_id][$l_id]['working_hour'];
								}
								else
								{
									$total_eff_hour=$prod_resource_array[$ldata][$pdate]['working_hour'];
								}
							}

							if($sewing_day!="")
							{
								$days_run= $diff=datediff("d",$sewing_day,$pdate);
								$days_active= $active_days_arr[$f_id][$l_id];
							}
							else
							{
								 $days_run=0;
								 $days_active=0;
							}

							$current_wo_time=0;
							if($current_date==$search_prod_date)
							{
								$prod_wo_hour=$total_eff_hour;

								if ($dif_time<$prod_wo_hour)//
								{
									$current_wo_time=$dif_hour_min;
									$cla_cur_time=$dif_time;
								}
								else
								{
									$current_wo_time=$prod_wo_hour;
									$cla_cur_time=$prod_wo_hour;
								}
							}
							else
							{
								$current_wo_time=$total_eff_hour;
								$cla_cur_time=$total_eff_hour;
							}
							$total_adjustment=0;
							if($type_line==2) //No Production Line
							{
								$smv_adjustmet_type=$production_data_arr[$pdate][$f_id][$l_id]['smv_adjust_type'];
								$eff_target=($production_data_arr[$pdate][$f_id][$l_id]['terget_hour']*$total_eff_hour);

								if($total_eff_hour>=$production_data_arr[$pdate][$f_id][$l_id]['working_hour'])
								{
									if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$production_data_arr[$pdate][$f_id][$l_id]['smv_adjust'];
									if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($production_data_arr[$pdate][$f_id][$l_id]['smv_adjust'])*(-1);
								}
								$efficiency_min+=$total_adjustment+($production_data_arr[$pdate][$f_id][$l_id]['man_power'])*$cla_cur_time*60;
								$line_efficiency=($efficiency_min>0) ? (($produce_minit)*100)/$efficiency_min : 0;
							}
							else
							{
								$smv_adjustmet_type=$prod_resource_array[$ldata][$pdate]['smv_adjust_type'];
								$eff_target=($prod_resource_array[$ldata][$pdate]['terget_hour']*$total_eff_hour);

								if($total_eff_hour>=$prod_resource_array[$ldata][$pdate]['working_hour'])
								{
								if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array[$ldata][$pdate]['smv_adjust'];
								if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array[$ldata][$pdate]['smv_adjust'])*(-1);
								}

								/*$actual_hours=date("H",time())-$line_start_hour; for metro
								$cur_time=date("H",time());
								if($cur_time>$line_start_hour){
									$actual_hours=$actual_hours-1;
								}
								else
								{
									$actual_hours=$actual_hours	;
								}
								$total_eff_hour_custom=($actual_hours>$total_eff_hour)?$total_eff_hour:$actual_hours;

			  					$producting_day=strtotime("Y-m-d",$txt_producting_day);

			  					if($producting_day>$today_date)
								{

									$efficiency_min+=$total_adjustment+($prod_resource_array[$ldata][$pdate]['man_power'])*$total_eff_hour*60;
									//echo $total_eff_hour."_";
								}
								else
								{
									$efficiency_min+=$total_adjustment+($prod_resource_array[$ldata][$pdate]['man_power'])*$total_eff_hour_custom*60;
									//echo $total_eff_hour_custom."_";
								}*/




								$efficiency_min+=$total_adjustment+($prod_resource_array[$ldata][$pdate]['man_power'])*$cla_cur_time*60;
								$line_efficiency=($efficiency_min>0) ? (($produce_minit)*100)/$efficiency_min : 0;
							}




							if($type_line==2) //No Production Line
							{
								$man_power=$production_data_arr[$pdate][$f_id][$l_id]['man_power'];
								$operator=$production_data_arr[$pdate][$f_id][$l_id]['operator'];
								$helper=$production_data_arr[$pdate][$f_id][$l_id]['helper'];
								$terget_hour=$production_data_arr[$pdate][$f_id][$l_id]['target_hour'];
								$capacity=$production_data_arr[$pdate][$f_id][$l_id]['capacity'];
								$working_hour=$production_data_arr[$pdate][$f_id][$l_id]['working_hour'];

								$floor_working_hour+=$production_data_arr[$pdate][$f_id][$l_id]['working_hour'];
								$eff_target_floor+=$eff_target;
								$floor_today_product+=$today_product;
								$floor_avale_minute+=$efficiency_min;
								$floor_produc_min+=$produce_minit;
								$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
								$floor_capacity+=$production_data_arr[$pdate][$f_id][$l_id]['capacity'];
								$floor_helper+=$production_data_arr[$pdate][$ldata][$pdate]['helper'];
								$floor_man_power+=$production_data_arr[$pdate][$f_id][$l_id]['man_power'];
								$floor_operator+=$production_data_arr[$pdate][$f_id][$l_id]['operator'];
								$total_operator+=$production_data_arr[$pdate][$f_id][$l_id]['operator'];
								$total_man_power+=$production_data_arr[$pdate][$f_id][$l_id]['man_power'];
								$total_helper+=$production_data_arr[$pdate][$f_id][$l_id]['helper'];
								$total_capacity+=$production_data_arr[$pdate][$f_id][$l_id]['capacity'];
								$floor_tgt_h+=$production_data_arr[$pdate][$f_id][$l_id]['target_hour'];
								$total_working_hour+=$production_data_arr[$pdate][$f_id][$l_id]['working_hour'];
								$gnd_total_tgt_h+=$production_data_arr[$pdate][$f_id][$l_id]['target_hour'];
								$total_terget+=$eff_target;
								$grand_total_product+=$today_product;
								$gnd_avable_min+=$efficiency_min;
								$gnd_product_min+=$produce_minit;

								$gnd_total_fob_val+=$fob_val;
								$gnd_final_total_fob_val+=$fob_val;
							}
							else
							{
								$man_power=$prod_resource_array[$ldata][$pdate]['man_power'];
								$operator=$prod_resource_array[$ldata][$pdate]['operator'];
								$helper=$prod_resource_array[$ldata][$pdate]['helper'];
								$terget_hour=$prod_resource_array[$ldata][$pdate]['terget_hour'];
								$capacity=$prod_resource_array[$ldata][$pdate]['capacity'];
								$working_hour=$prod_resource_array[$ldata][$pdate]['working_hour'];

								$floor_capacity+=$prod_resource_array[$ldata][$pdate]['capacity'];
								$floor_man_power+=$prod_resource_array[$ldata][$pdate]['man_power'];
								$floor_operator+=$prod_resource_array[$ldata][$pdate]['operator'];
								$floor_helper+=$prod_resource_array[$ldata][$pdate]['helper'];
								$floor_tgt_h+=$prod_resource_array[$ldata][$pdate]['terget_hour'];
								$floor_working_hour+=$prod_resource_array[$ldata][$pdate]['working_hour'];
								$eff_target_floor+=$eff_target;
								$floor_today_product+=$today_product;
								$floor_avale_minute+=$efficiency_min;
								$floor_produc_min+=$produce_minit;
								$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;

								$total_operator+=$prod_resource_array[$ldata][$pdate]['operator'];
								$total_man_power+=$prod_resource_array[$ldata][$pdate]['man_power'];
								$total_helper+=$prod_resource_array[$ldata][$pdate]['helper'];
								$total_capacity+=$prod_resource_array[$ldata][$pdate]['capacity'];
								$total_working_hour+=$prod_resource_array[$ldata][$pdate]['working_hour'];
								$gnd_total_tgt_h+=$prod_resource_array[$ldata][$pdate]['terget_hour'];
								$total_terget+=$eff_target;
								$grand_total_product+=$today_product;
								$gnd_avable_min+=$efficiency_min;
								$gnd_product_min+=$produce_minit;
								$gnd_total_fob_val+=$fob_val;
								$gnd_final_total_fob_val+=$fob_val;

							}
							$po_id=rtrim($production_data_arr[$pdate][$f_id][$ldata]['po_id'],',');
							$po_id=array_unique(explode(",",$po_id));
							$style=rtrim($production_data_arr[$pdate][$f_id][$ldata]['style']);
							$style=implode(",",array_unique(explode(",",$style)));

							$cbo_get_upto=str_replace("'","",$cbo_get_upto);
							$txt_parcentage=str_replace("'","",$txt_parcentage);

							$floor_name=$floorArr[$f_id];
							$floor_smv+=$item_smv;

							$floor_days_run+=$days_run;
							$floor_days_active+=$days_active;

							$po_id=$production_data_arr[$pdate][$f_id][$ldata]['po_id'];//$item_ids//$subcon_order_id
							$styles=explode(",",$style);
							 $style_button='';//

							$as_on_current_hour_target=0; $as_on_current_hour_variance=0;
							$as_on_current_hour_target=$terget_hour*$cla_cur_time;
							$as_on_current_hour_variance=$line_production_hour-$as_on_current_hour_target;


							// echo $line_efficiency;echo "<br>";

							// number_format($produce_minit,0)
							// ($line_production_hour/$eff_target)*100

							// number_format($line_efficiency,2)
							$resurce_data_array[date('d-M-Y',strtotime($pdate))]['target'] += $eff_target;//$terget_hour;
							$resurce_data_array[date('d-M-Y',strtotime($pdate))]['production'] += $line_production_hour;
							$resurce_data_array[date('d-M-Y',strtotime($pdate))]['traget_qty_achieved'] += ($eff_target>0) ? ($line_production_hour/$eff_target)*100 : 0;

							$resurce_data_array[date('d-M-Y',strtotime($pdate))]['man_power'] += $man_power;
							$resurce_data_array[date('d-M-Y',strtotime($pdate))]['prod_hours'] += $prod_hours;
							$resurce_data_array[date('d-M-Y',strtotime($pdate))]['available_minit'] += $efficiency_min;
							$resurce_data_array[date('d-M-Y',strtotime($pdate))]['target_min'] += $target_min;
							$resurce_data_array[date('d-M-Y',strtotime($pdate))]['produce_minit'] += $produce_minit;
							$resurce_data_array[date('d-M-Y',strtotime($pdate))]['line_efficiency'] += $line_efficiency;

							$resurce_data_array[date('d-M-Y',strtotime($pdate))]['style_change'] += $style_change;
							$resurce_data_array[date('d-M-Y',strtotime($pdate))]['target_effi'] += $target_effi;
							$resurce_data_array[date('d-M-Y',strtotime($pdate))]['achive_effi'] += $achive_effi;
							$resurce_data_array[date('d-M-Y',strtotime($pdate))]['cm_earn'] += $ttl_cm;
							$resurce_data_array[date('d-M-Y',strtotime($pdate))]['fob_earn'] += $ttl_fob_val;

						}
					}
				}
			}
		}



		// echo "<pre>";print_r($resurce_data_array);die();

		/* ==========================================================================================/
		/										production  data									 /
		/ ========================================================================================= */

		$sql="SELECT a.id,a.sewing_line,a.prod_reso_allo, a.production_type, a.floor_id, a.production_date, b.production_qnty as production_qnty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.production_type in (1,2,3,5,8,9,11,80) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.floor_id!=0 $cbo_company_cond  $production_date order by a.production_date";
		// echo $sql; die();
		$sql_dtls = sql_select($sql);
		$floor_qnty = array();
		$floor_wise_total = array();
		$prod_floor_array = array();
		$wash_qty_array = array();
		$sewing_line_array = array();

		foreach ( $sql_dtls as $row )
		{
			if($row[csf('prod_reso_allo')]==1)
			{
				$resource_line= explode(",", $prod_reso_arr[$row[csf('sewing_line')]]);
				$line_name='';
    			foreach($resource_line as $actual_line)
    			{
    				$line_name.= ($line_name=='') ? $lineArr[$actual_line] : ", ".$lineArr[$actual_line];
    			}
			}
			else
			{
				$line_name=$lineArr[$row[csf('sewing_line')]];
			}

			// $floor_qnty[change_date_format($row[csf("production_date")],'','',1)][$row[csf("floor_id")]][$row[csf("production_type")]] += $row[csf("production_qnty")];
			$wash_qty_array[change_date_format($row[csf("production_date")],'','',1)][$row[csf("production_type")]] +=$row[csf("production_qnty")];
			if($row[csf("production_type")]==11)
			{
				$floor_id = 0;
				$prod_type = 0;
				if($row[csf("production_type")]==11)
				{
					if($row[csf("floor_id")]==624)
					{
						$floor_id= 669;
					}
					else
					{
						$floor_id= 331;
					}
					$prod_type = 80;
				}
				$prod_floor_array[$prod_type][$floor_id] = $floor_id;
				$floor_wise_total[$floor_id][$prod_type] += $row[csf("production_qnty")];
				$floor_qnty[change_date_format($row[csf("production_date")],'','',1)][$floor_id][$prod_type] += $row[csf("production_qnty")];
			}
			else
			{
				$prod_floor_array[$row[csf("production_type")]][$row[csf("floor_id")]] = $row[csf("floor_id")];
				$floor_wise_total[$row[csf("floor_id")]][$row[csf("production_type")]] += $row[csf("production_qnty")];
				$floor_qnty[change_date_format($row[csf("production_date")],'','',1)][$row[csf("floor_id")]][$row[csf("production_type")]] += $row[csf("production_qnty")];
			}

			$sewing_line_array[change_date_format($row[csf("production_date")],'','',1)][$row[csf("production_type")]] .= $line_name.",";


		}
		// echo "<pre>"; print_r($floor_wise_total);die();

		// ================================ cutting delivery data ============================
		$delivery_date = str_replace("a.production_date", "b.cut_delivery_date", $production_date);
		$delivery_location = str_replace("a.location", "a.location_id", $location_id);
		$sqlDelv = "SELECT b.CUT_DELIVERY_DATE,b.CUT_DELIVERY_QNTY from pro_cut_delivery_mst a,pro_cut_delivery_order_dtls b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $cbo_company_cond  $delivery_date";
		// echo $sqlDelv;die();
		$delvRes = sql_select($sqlDelv);
		$delivery_to_input_qty = array();
		foreach ($delvRes as $val)
		{
			$delivery_to_input_qty[date('d-M-Y',strtotime($val['CUT_DELIVERY_DATE']))] += $val['CUT_DELIVERY_QNTY'];
		}
		// echo "<pre>"; print_r($delivery_to_input_qty);die();


		// ================================ ex-factory data ============================
		$delivery_date = str_replace("a.production_date", "a.delivery_date", $production_date);
		$delivery_location = str_replace("a.location", "a.delivery_location_id", $location_id);
		$sqlEx = "SELECT a.DELIVERY_FLOOR_ID,a.DELIVERY_DATE,b.EX_FACTORY_QNTY from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $cbo_company_cond  $delivery_date";
		// echo $sqlEx;die();
		$exRes = sql_select($sqlEx);
		$ex_factory_qty = array();
		foreach ($exRes as $val)
		{
			$ex_factory_qty[date('d-M-Y',strtotime($val['DELIVERY_DATE']))] += $val['EX_FACTORY_QNTY'];
		}
		// echo "<pre>"; print_r($ex_factory_qty);die();

		// ================================ buyer inspection data ============================
		$inspection_date = str_replace("a.production_date", "a.inspection_date", $production_date);
		$working_location = str_replace("a.location", "a.working_location", $location_id);
		$working_company = str_replace("a.company_id", "a.working_company", $cbo_company_cond);
		$sql = "SELECT a.INSPECTION_STATUS,a.INSPECTION_DATE,a.INSPECTION_QNTY from pro_buyer_inspection a where a.status_active=1 and a.is_deleted=0 and a.inspection_level=3 $working_company  $working_location $inspection_date and a.inspection_status in(1,3)";
		//echo $sql;die();
		$res = sql_select($sql);
		$buyer_insp_qty = array();
		foreach ($res as $val)
		{
			$buyer_insp_qty[date('d-M-Y',strtotime($val['INSPECTION_DATE']))][$val['INSPECTION_STATUS']] += $val['INSPECTION_QNTY'];
		}
		// echo "<pre>"; print_r($buyer_insp_qty);die();

        $count_data=count($prod_floor_array[1])+count($prod_floor_array[5])+count($prod_floor_array[8])+count($prod_floor_array[80]);
        // echo $count_data;die;

         //$count_hd=count($sql_floor)+1;
        $count_hd=count($prod_floor_array[1])+count($prod_floor_array[5])+count($prod_floor_array[8])+count($prod_floor_array[80]);

        $width_hd=$count_hd*80;
        $count_cutting=count($prod_floor_array[1])+1;
        $width_cutting=$count_cutting*80;

        $count_sewing=count($prod_floor_array[5])+1;
        $width_sewing=$count_sewing*80;

        $count_finishing=count($prod_floor_array[8])+1;
        $width_finishing=$count_finishing*80;

		$count_poly=count($prod_floor_array[80])+1;
        $width_poly=$count_poly*80;

        $table_width=940+(($count_data+4)*80);
		ob_start();

		?>
		<fieldset>
	        <table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="caption" align="center">
	            <tr>
	               <td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:18px">

				   Company Name: <?= $company_library[str_replace("'","",$cbo_company_id)]; ?>

					</strong>
	               </td>
	            </tr>
	            <tr>
	               <td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:14px"><? echo $report_title; ?></strong></td>
	            </tr>
	            <tr>
	               <td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:14px"> <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
	            </tr>
	        </table>

	        <div style="height:auto;">

		        <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left">
		            <thead>
		                <tr>
		                    <th width="60" rowspan="2" >Date</th>
		                    <th width="<? echo $width_cutting; ?>" colspan="<? echo $count_cutting; ?>">Cutting Output</th>
		                    <th width="80" rowspan="2">Cutting Issue</th>
		                    <th width="80" rowspan="2">Sewing Line</th>
		                    <th width="<? echo $width_sewing; ?>" colspan="<? echo $count_sewing; ?>">Sewing Output</th>
		                    <th width="160" colspan="2">Wash</th>
		                    <th width="<? echo $width_poly; ?>" colspan="<? echo $count_poly; ?>">Gmts. Finishing</th>
		                    <th width="<? echo $width_finishing; ?>" colspan="<? echo $count_finishing; ?>">Packing</th>
		                    <th width="80" rowspan="2" title="Produce min/Available min*100">Avg. Efficiency (%)</th>
		                    <th width="80" rowspan="2">Produced Minute</th>
		                    <th width="80" rowspan="2">Available Minutes</th>
		                    <th width="80" rowspan="2" title="Production/Target*100">Target Qty Achieved (%)</th>
		                    <th width="80" rowspan="2">Buyer Insp. Passed Qty</th>
		                    <th width="80" rowspan="2">Buyer Insp. Failed Qty</th>
		                    <th width="80" rowspan="2">Ex-Factory Qty</th>
		                </tr>
		               	<tr>
							<?
							foreach ( $prod_floor_array[1] as $rows )
							{
								?>
	                            <th width="80" title="<?=$rows;?>"><? echo $floor_arr[$rows]; ?></th>
								<?
							}
							?>
							<th width="80" colspan="">Total</th>
							<?

	                        foreach ( $prod_floor_array[5] as $rows )
	                        {
	                            ?>
	                            <th width="80"><? echo $floor_arr[$rows]; ?></th>
	                            <?
	                        }
	                        ?>
							<th width="80" colspan="">Total</th>
	                        <th width="80">Sent</th>
	                        <th width="80">Receive</th>
	                        <?
	                        foreach ( $prod_floor_array[80] as $rows )
	                        {
	                            ?>
	                            <th width="80"><? echo $floor_arr[$rows]; ?></th>
	                            <?
	                        }
							?>
							<th width="80" colspan="">Total</th>
							<?

	                        foreach ( $prod_floor_array[8] as $rows )
	                        {
	                            ?>
	                            <th width="80"><? echo $floor_arr[$rows]; ?></th>
	                            <?
	                        }
							?>
							<th width="80" colspan="">Total</th>

		               	</tr>
		            </thead>
		        </table>
	        </div>
	        <? //echo $datediff; die('kakku');?>
	        <div align="left" style="max-height:300px;width: <?= $table_width+20;?>px" id="scroll_body">
		        <table align="left" cellspacing="0" cellpadding="0" width="<? echo $table_width; ?>"  border="1" rules="all" class="rpt_table" >
					<?
					$unit_wise_total_array=array();
					$value_define=array();
					$value_define2=array();
					$wash_issue_total = 0;
					$wash_rcv_total = 0;
					$ex_qty_total = 0;
					$produce_minit_total = 0;
					$tot_efficiency = 0;
					$tot_efficiency_count = 0;
					$tot_target_achive = 0;
					$tot_target_achive_count = 0;

					$gr_tot_pro_min = 0;
					$gr_tot_avlable_min = 0;
					$gr_tot_target = 0;
					$gr_tot_production = 0;

		            for($j=0;$j<$datediff;$j++)
		            {
		                if ($j%2==0)
		                    $bgcolor="#E9F3FF";
		                else
		                    $bgcolor="#FFFFFF";

		                $date_data_array=array();
						$newdate =add_date(str_replace("'","",$txt_date_from),$j);
						$newdate=date("d-M-Y",strtotime(str_replace("'","",$newdate)));
		                $date_data_array[$j]=$newdate;
			            ?>
			            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>">
			                <td width="60" align="center">&nbsp;<? echo change_date_format($newdate); ?></td>
			                <?
		                    $site_tot_qnty=0;
	                        foreach ( $prod_floor_array[1] as $rows )
	                        {
	                        	// echo $newdate."==".$rows."<br>";
	                            ?>
	                                <td width="80" align="right"><?  echo number_format($floor_qnty[$date_data_array[$j]][$rows][1],0); ?></td>
	                            <?
	                            $site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows][1];
	                            if($floor_qnty[$date_data_array[$j]][$rows][1]!="")
	                            {
	                            	$value_define[1][$rows] +=1;
	                            }
	                        }
	                        ?>
	                        <td width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>

	                        <td width="80" align="right">
	                        	<?
	                        	if($delivery_to_input_qty[$date_data_array[$j]]!="")
		                        {
		                        	$value_define2['cut_delv'] +=1;
		                        }
	                        	echo number_format($delivery_to_input_qty[$date_data_array[$j]],0);
	                        	?>

	                        </td>
	                        <td width="80" align="left"><? echo implode(", ",array_unique(array_filter(explode(",", $sewing_line_array[$date_data_array[$j]][5])))); ?></td>
	                        <?
		                    $site_tot_qnty=0;
	                        foreach ( $prod_floor_array[5] as $rows )
	                        {
	                            ?>
	                                <td width="80" align="right"><?  echo number_format($floor_qnty[$date_data_array[$j]][$rows][5],0); ?></td>
	                            <?
	                            $site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows][5];
	                            if($floor_qnty[$date_data_array[$j]][$rows][5]!="")
	                            {
	                            	$value_define[5][$rows] +=1;
	                            }
	                        }
	                        ?>
	                        <td width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>

	                        <td width="80" align="right">
	                        <?
	                        if($wash_qty_array[$date_data_array[$j]][2]!="")
	                        {
	                        	$value_define2['wash_iss'] +=1;
	                        }
	                        echo number_format($wash_qty_array[$date_data_array[$j]][2],0);
	                        ?>

	                        </td>
	                        <td width="80" align="right">
	                        <?
	                        if($wash_qty_array[$date_data_array[$j]][3]!="")
	                        {
	                        	$value_define2['wash_rcv'] +=1;
	                        }
	                        echo number_format($wash_qty_array[$date_data_array[$j]][3],0);
	                        ?>

	                        </td>

							<?
	                        $woven_finish_site_tot_qnty=0;
	                        foreach ( $prod_floor_array[80] as $rows )
	                        {
								if ($rows == 669)
								{
									?>
										<td width="80" align="right">
											<? echo number_format(($floor_qnty[$date_data_array[$j]][$rows][80]+$floor_qnty[$date_data_array[$j]][624][11]),0); ?>
										</td>
									<?
									$woven_finish_site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows][80]+$floor_qnty[$date_data_array[$j]][624][11];
								}
								else if($rows == 331)
								{
									?>
									<td width="80" align="right">
										<? echo number_format(($floor_qnty[$date_data_array[$j]][$rows][80]+$floor_qnty[$date_data_array[$j]][676][11]),0); ?>
									</td>
								    <?
									$woven_finish_site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows][80]+$floor_qnty[$date_data_array[$j]][676][11];
								}
								else
								{
									?>
										<td width="80" align="right">
											<? echo number_format($floor_qnty[$date_data_array[$j]][$rows][80],0); ?>
										</td>
									<?
									$woven_finish_site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows][80];
								}

	                            if($floor_qnty[$date_data_array[$j]][$rows][80]!="")
	                            {
	                            	$value_define[80][$rows] +=1;
	                            }
	                        }
	                        ?>
							<td width="80" align="right">
								<strong><? echo number_format($woven_finish_site_tot_qnty,0); ?></strong>
						    </td>

	                        <?
	                        $site_tot_qnty=0;
	                        foreach ( $prod_floor_array[8] as $rows )
	                        {
	                            ?>
	                                <td width="80" align="right">
	                                	<? echo number_format($floor_qnty[$date_data_array[$j]][$rows][8],0); ?>
	                                </td>
	                            <?
	                            $site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows][8];
	                            if($floor_qnty[$date_data_array[$j]][$rows][8]!="")
	                            {
	                            	$value_define[8][$rows] +=1;
	                            }
	                        }
	                        ?>
	                        <td width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>

	                        <td width="80" align="right">
	                        	<?
	                        	echo ($resurce_data_array[$date_data_array[$j]]['available_minit']>0) ? number_format(($resurce_data_array[$date_data_array[$j]]['produce_minit']/$resurce_data_array[$date_data_array[$j]]['available_minit']*100),2) : 0;

	                        	if($resurce_data_array[$date_data_array[$j]]['available_minit']>0)
	                        	{
	                        		$tot_efficiency += $resurce_data_array[$date_data_array[$j]]['produce_minit']/$resurce_data_array[$date_data_array[$j]]['available_minit']*100;
	                        		$tot_efficiency_count++;
	                        	}

	                        	?>%
	                        </td>
	                        <td width="80" align="right"><? echo number_format($resurce_data_array[$date_data_array[$j]]['produce_minit'],0); ?></td>
	                        <td width="80" align="right"><? echo number_format($resurce_data_array[$date_data_array[$j]]['available_minit'],0); ?></td>
	                        <td width="80" align="right">
	                        	<?
	                        	echo ($resurce_data_array[$date_data_array[$j]]['target']>0) ? number_format(($resurce_data_array[$date_data_array[$j]]['production']/$resurce_data_array[$date_data_array[$j]]['target']*100),2) : 0;

	                        	if($resurce_data_array[$date_data_array[$j]]['target']>0)
	                        	{
	                        		$tot_target_achive +=  $resurce_data_array[$date_data_array[$j]]['production']/$resurce_data_array[$date_data_array[$j]]['target']*100;
	                        		$tot_target_achive_count++;
	                        	}
	                        	?>%
	                        </td>
	                        <td width="80" align="right">
	                        	<a href="javascript:void(0)" onclick="open_popup('<?=$cbo_company;?>','<?=$cbo_location;?>','<?=$newdate;?>',1,'buyer-insp-popup')">
	                        		<? echo number_format($buyer_insp_qty[$date_data_array[$j]][1],0); ?>
	                        	</a>
	                        </td>
	                        <?
	                        if($buyer_insp_qty[$date_data_array[$j]][1]!="")
	                        {
	                        	$value_define2['bi-pass'] +=1;
	                        }
	                        ?>


	                        <td width="80" align="right">
	                        	<a href="javascript:void(0)" onclick="open_popup('<?=$cbo_company;?>','<?=$cbo_location;?>','<?=$newdate;?>',3,'buyer-insp-popup')">
	                        		<? echo number_format($buyer_insp_qty[$date_data_array[$j]][3],0); ?>
	                        	</a>
	                        </td>
	                        <?
	                        if($buyer_insp_qty[$date_data_array[$j]][3]!="")
	                        {
	                        	$value_define2['bi-faill'] +=1;
	                        }
	                        ?>

	                        <td width="80" align="right">
	                        	<a href="javascript:void(0)" onclick="open_popup('<?=$cbo_company;?>','<?=$cbo_location;?>','<?=$newdate;?>',0,'ex-factory-popup')">
	                        		<? echo number_format($ex_factory_qty[$date_data_array[$j]],0); ?>
	                        	</a>
	                        </td>
	                        <?
	                        if($ex_factory_qty[$date_data_array[$j]]!="")
	                        {
	                        	$value_define2['ex'] +=1;
	                        }
	                        ?>

			            </tr>
			            <?
			            $wash_issue_total += $wash_qty_array[$date_data_array[$j]][2];
						$wash_rcv_total += $wash_qty_array[$date_data_array[$j]][3];
						$cutting_delv_total += $delivery_to_input_qty[$date_data_array[$j]];
						$buyer_insp_pass_total += $buyer_insp_qty[$date_data_array[$j]][1];
						$buyer_insp_faill_total += $buyer_insp_qty[$date_data_array[$j]][3];
						$ex_qty_total += $ex_factory_qty[$date_data_array[$j]];
						$produce_minit_total += $resurce_data_array[$date_data_array[$j]]['produce_minit'];
						$available_minit_total += $resurce_data_array[$date_data_array[$j]]['available_minit'];

						$gr_tot_pro_min += $resurce_data_array[$date_data_array[$j]]['produce_minit'];
						$gr_tot_avlable_min += $resurce_data_array[$date_data_array[$j]]['available_minit'];
						$gr_tot_target += $resurce_data_array[$date_data_array[$j]]['target'];
						$gr_tot_production += $resurce_data_array[$date_data_array[$j]]['production'];


			        }
			        ?>
			        <tfoot>
			            <tr bgcolor="#dddddd">
			                <th align="center" width="60"><strong>Total : </strong></th>
			                <?
		                    $grand_total_tot=0;

	                        foreach ( $prod_floor_array[1] as $rows )
	                        {
	                            ?>
	                                <th width="80" align="right"><strong><? echo number_format($floor_wise_total[$rows][1],0); ?></strong></th>
	                            <?
	                            $grand_total_tot+=$floor_wise_total[$rows][1];
	                        }

			                ?>
			                <th width="80" align="right"><strong><? echo number_format($grand_total_tot,0); ?></strong></th>

			                <th width="80" align="right"><? echo number_format($cutting_delv_total,0); ?></th>
			                <th width="80" align="right"></th>
			                <?
		                    $grand_total_tot=0;

	                        foreach ( $prod_floor_array[5] as $rows )
	                        {
	                            ?>
	                                <th width="80" align="right"><strong><? echo number_format($floor_wise_total[$rows][5],0); ?></strong></th>
	                            <?
	                            $grand_total_tot+=$floor_wise_total[$rows][5];
	                        }

			                ?>
			                <th width="80" align="right"><strong><? echo number_format($grand_total_tot,0); ?></strong></th>

			                <th width="80" align="right"><strong><? echo number_format($wash_issue_total,0); ?></strong></th>
			                <th width="80" align="right"><strong><? echo number_format($wash_rcv_total,0); ?></strong></th>

			                <?
							$grand_total_tot=0;
							foreach ( $prod_floor_array[80] as $rows )
	                        {
								if ($rows == 669)
								{
									?>
										<th width="80" align="right"><strong><? echo number_format(($floor_wise_total[$rows][80]+$floor_wise_total[624][11]),0); ?></strong></th>
									<?
									$grand_total_tot+=$floor_wise_total[$rows][80]+$floor_wise_total[624][11];
								}
								else if ($rows == 331)
								{
									?>
										<th width="80" align="right"><strong><? echo number_format(($floor_wise_total[$rows][80]+$floor_wise_total[676][11]),0); ?></strong></th>
									<?
									$grand_total_tot+=$floor_wise_total[$rows][80]+$floor_wise_total[676][11];
								}
								else
								{
									?>
										<th width="80" align="right"><strong><? echo number_format($floor_wise_total[$rows][80],0); ?></strong></th>
									<?
									$grand_total_tot+=$floor_wise_total[$rows][80];
								}
	                        }

			                ?>
							<th width="80" align="right"><strong><? echo number_format($grand_total_tot,0); ?></strong></th>

			                <?
		                    $grand_total_tot=0;

	                        foreach ( $prod_floor_array[8] as $rows )
	                        {
	                            ?>
	                                <th width="80" align="right"><strong><? echo number_format($floor_wise_total[$rows][8],0); ?></strong></th>
	                            <?
	                            $grand_total_tot+=$floor_wise_total[$rows][8];
	                        }

			                ?>
			                <th width="80" align="right"><strong><? echo number_format($grand_total_tot,0); ?></strong></th>

			                <th width="80" align="right"><strong><? //echo number_format($grand_total_tot,0); ?></strong></th>
			                <th width="80" align="right"><strong><? echo number_format($produce_minit_total,0); ?></strong></th>
			                <th width="80" align="right"><strong><? echo number_format($available_minit_total,0); ?></strong></th>
			                <th width="80" align="right"><strong><? //echo number_format($grand_total_tot,0); ?></strong></th>
			                <th width="80" align="right"><strong><? echo number_format($buyer_insp_pass_total,0); ?></strong></th>
			                <th width="80" align="right"><strong><? echo number_format($buyer_insp_faill_total,0); ?></strong></th>
			                <th width="80" align="right"><strong><? echo number_format($ex_qty_total,0); ?></strong></th>

			            </tr>

			            <tr bgcolor="#dddddd">
			                <th align="center" width="60"><strong>Avg : </strong></th>
			                <?
		                    $avg='';
		                    $avg_tot=0;
	                        foreach ( $prod_floor_array[1] as $rows )
	                        {
	                            $avg=$floor_wise_total[$rows][1]/ $value_define[1][$rows];
	                            ?>
	                            <th width="80" align="right"><strong><? echo number_format( $avg,0); ?></strong></th>
	                            <?
	                            $avg_tot+=$avg;
	                        }
		                    ?>
		                    <th width="80" align="right"><strong><?  echo number_format($avg_tot,0); ?></strong></th>
		                    <th width="80" align="right"><?
		                    $avg_tot = 0;
	                    	$avg_tot = ($value_define2['cut_delv']>0) ? $cutting_delv_total/ $value_define2['cut_delv'] : 0;
		                    echo number_format($avg_tot,0);
		                    ?></th>
		                    <th width="80" align="right"></th>
		                    <?
		                    $avg='';
		                    $avg_tot=0;
	                        foreach ( $prod_floor_array[5] as $rows )
	                        {
	                            $avg=$floor_wise_total[$rows][5]/ $value_define[5][$rows];
	                            ?>
	                            <th width="80" align="right"><strong><? echo number_format( $avg,0); ?></strong></th>
	                            <?
	                            $avg_tot+=$avg;
	                        }
		                    ?>
		                    <th width="80" align="right"><strong><?  echo number_format($avg_tot,0); ?></strong></th>
		                    <th width="80" align="right"><strong>
	                    	<?
	                    	$avg_tot = 0;
	                    	$avg_tot = ($value_define2['wash_iss']>0) ? $wash_issue_total/ $value_define2['wash_iss'] : 0;
	                    	echo number_format($avg_tot,0);

	                    	?>

		                    </strong></th>
		                    <th width="80" align="right"><strong>
		                    <?
	                    	$avg_tot = 0;
	                    	$avg_tot = ($value_define2['wash_rcv']>0) ? $wash_rcv_total / $value_define2['wash_rcv'] : 0;
	                    	echo number_format($avg_tot,0);

	                    	?>

		                    </strong></th>


							<?
							$avg='';
		                    $avg_tot=0;
	                        foreach ( $prod_floor_array[80] as $rows )
	                        {
								if($rows == 669)
								    {
										$avg=($floor_wise_total[$rows][80]+$floor_wise_total[624][11])/ $value_define[80][$rows];
										?>
											<th width="80" align="right"><strong><? echo number_format( $avg,0); ?></strong></th>
										<?
										$avg_tot+=$avg;
								    }
								else if( $rows == 331)
								    {
										$avg=($floor_wise_total[$rows][80]+$floor_wise_total[676][11])/ $value_define[80][$rows];
										?>
											<th width="80" align="right"><strong><? echo number_format( $avg,0); ?></strong></th>
										<?
										$avg_tot+=$avg;
								    }
								else
								    {
										$avg=$floor_wise_total[$rows][80]/ $value_define[80][$rows];
										?>
											<th width="80" align="right"><strong><? echo number_format( $avg,0); ?></strong></th>
										<?
										$avg_tot+=$avg;
								    }
	                        }
		                    ?>
							<th width="80" align="right"><?  echo number_format($avg_tot,0); ?></th>

		                    <?
		                    $avg='';
		                    $avg_tot=0;
	                        foreach ( $prod_floor_array[8] as $rows )
	                        {
	                            $avg=$floor_wise_total[$rows][8]/ $value_define[8][$rows];
	                            ?>
	                                <th width="80" align="right"><strong><? echo number_format( $avg,0); ?></strong></th>
	                            <?
	                            $avg_tot+=$avg;
	                        }
		                    ?>
		                    <th width="80" align="right"><?  echo number_format($avg_tot,0); ?></th>

		                    <th width="80" align="right" title="<?=$gr_tot_pro_min."/".$gr_tot_avlable_min;?>">
		                    	<?  echo number_format(($gr_tot_pro_min/$gr_tot_avlable_min)*100,2); ?>%
		                    </th>
		                    <th width="80" align="right"><?  //echo number_format($avg_tot,0); ?></th>
		                    <th width="80" align="right"><?  //echo number_format($avg_tot,0); ?></th>
		                    <th width="80" align="right" title="<?=$gr_tot_production."/".$gr_tot_target;?>">
		                    	<?  echo number_format(($gr_tot_production/$gr_tot_target)*100,2); ?>%
		                    </th>
		                    <th width="80" align="right">
		                    <?
	                    	$avg_tot = 0;
	                    	$avg_tot = ($value_define2['bi-pass']>0) ? $buyer_insp_pass_total / $value_define2['bi-pass'] : 0;
	                    	echo number_format($avg_tot,0);
	                    	?>
		                    </th>
		                    <th width="80" align="right">
		                    <?
	                    	$avg_tot = 0;
	                    	$avg_tot = ($value_define2['bi-faill']>0) ? $buyer_insp_faill_total / $value_define2['bi-faill'] : 0;
	                    	echo number_format($avg_tot,0);
	                    	?>
		                    </th>
		                    <th width="80" align="right">
	                    	<?
	                    	$avg_tot = 0;
	                    	$avg_tot = $ex_qty_total/ $value_define2['ex'];
	                    	echo number_format($avg_tot,0);
	                    	?>
		                    </th>

			            </tr>
		            </tfoot>
		        </table>
		    </div>
	    </fieldset>
	    <?
	}

	elseif ($type==5) // show3 button
	{
		if ($cbo_production_type==1)
		{
			$sql_floor=sql_select("Select a.id, a.floor_name from  lib_prod_floor a where a.location_id=$cbo_location $cbo_company_cond and a.status_active=1 and a.is_deleted=0 order by a.floor_name ");

			 $sql_machineID_finish_fab=sql_select("select b.machine_no_id from inv_receive_master a,pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form=7 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.machine_no_id !=0  group by b.machine_no_id order by  b.machine_no_id");
			 $machineIDs="";
			foreach ( $sql_machineID_finish_fab as $row )
			{
				$machineIDs.=$row[csf("machine_no_id")].',';
			}
			$machineIDs= chop($machineIDs,",");

			$sql_cutting="SELECT a.floor_id,b.floor_serial_no from  pro_garments_production_mst a, lib_prod_floor b where b.id=a.floor_id and a.location=$cbo_location $cbo_company_cond and a.production_type=1 and a.status_active=1 and a.is_deleted=0 and a.floor_id !=0  group by a.floor_id,b.floor_serial_no order by  b.floor_serial_no";
			//echo $sql_cutting;
			$sql_floor_cutting=sql_select($sql_cutting);
			$sql_sewing_input="SELECT a.floor_id from  pro_garments_production_mst a where a.location=$cbo_location $cbo_company_cond and a.production_type=4 and a.status_active=1 and a.is_deleted=0 and a.floor_id !=0  group by a.floor_id order by  a.floor_id ";
			//echo $sql_sewing_input;
			$sql_floor_sewing_input=sql_select($sql_sewing_input);


	        $sql_floor_sewing=sql_select("SELECT a.floor_id from  pro_garments_production_mst a where a.location=$cbo_location $cbo_company_cond and a.production_type=5 and a.status_active=1 and a.is_deleted=0 and a.floor_id !=0  group by a.floor_id order by  a.floor_id ");


	        $sql_floor_finishing=sql_select("SELECT a.floor_id from  pro_garments_production_mst a where a.location=$cbo_location $cbo_company_cond and a.production_type=8 and a.status_active=1 and a.is_deleted=0 and a.floor_id !=0  group by a.floor_id order by  a.floor_id ");

			$finish_query="SELECT b.uom
			FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b
			WHERE a.id = b.mst_id and b.uom in (12,23) and a.location_id=$cbo_location $cbo_company_cond  and a.is_deleted = 0 and a.status_active = 1 and b.is_deleted = 0 and b.status_active = 1
			GROUP BY b.uom order by b.uom";
			//echo $finish_query;//die();
			//echo $sql_sipment_result;
			$sql_finish_fabric_receive=sql_select($finish_query);
			//print_r($sql_finish_fabric_receive);

	        $count_data=count($sql_finish_fabric_receive)+count($sql_floor_cutting)+count($sql_floor_sewing)+count($sql_floor_sewing_input)+count($sql_floor_finishing);
	        //echo $count_data;die;
	         //$count_hd=count($sql_floor)+1;
	        $count_hd=count($sql_floor_cutting)+count($sql_floor_sewing)+count($sql_floor_sewing_input)+count($sql_floor_poly)+1;
	        $width_hd=$count_hd*80;

			$count_cutting=count($sql_floor_cutting)+1;
	        $width_cutting=$count_cutting*80;

			$count_finish_fabric=count($sql_finish_fabric_receive);
	        $width_finish_fabric=$count_finish_fabric*80;

	        $count_sewing=count($sql_floor_sewing)+1;
	        $width_sewing=$count_sewing*80;

	        $count_sewing_input=count($sql_floor_sewing_input)+1;
	        $width_sewing_input=$count_sewing*80;

			$count_packing=count($sql_floor_finishing)+1;
	        $width_packing=$count_finishing_fab*80;
			//echo $width_packing;

	        $table_width=220+($count_data*100);
			ob_start();
			//$table_width=90+($datediff*160);
			?>
			<div id="scroll_body_" align="center" style="height:auto; width:auto; margin:0 auto; padding:0;">
		        <table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="caption" align="center">
		            <tr>
		               <td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:18px">
		               <?
					   if ($cbo_company!=0){echo ' Company Name:' .$company_library[$cbo_company];} else{echo ' Working Company Name:' .$company_library[$cbo_working_company];}
					   ?>
						</strong>
		               </td>
		            </tr>
		            <tr>
		               <td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:14px"><? echo $report_title; ?></strong></td>
		            </tr>
		            <tr>
		               <td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:14px"> <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
		            </tr>
		        </table>
		        <?

				if ($cbo_location==0) $location_id =""; else $location_id =" and a.location_id=$cbo_location ";
				if ($cbo_location==0) $location_id_cond =""; else $location_id_cond =" and a.location=$cbo_location "; // garments production

				if($db_type==0)
				{

					if( $date_from==0 && $date_to==0 ) $production_date=""; else $production_date= " and a.production_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; //garments production

					if( $date_from==0 && $date_to==0 ) $ex_factory_date=""; else $ex_factory_date= " and a.ex_factory_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; //shipment date

					if( $date_from==0 && $date_to==0 ) $production_date_knitting=""; else $production_date_knitting= " and a.receive_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; //knitting
					if( $date_from==0 && $date_to==0 ) $production_date_dyeing=""; else $production_date_dyeing= " and a.process_end_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; // dyeing
					if( $date_from==0 && $date_to==0 ) $production_date_finishing=""; else $production_date_finishing= " and a.receive_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; //finishing
				}
				else
				{
					if( $date_from==0 && $date_to==0 ) $production_date=""; else $production_date= " and a.production_date between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'"; //garments production
					if( $date_from==0 && $date_to==0 ) $ex_factory_date=""; else $ex_factory_date= " and b.ex_factory_date between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'"; //shipment date

					if( $date_from==0 && $date_to==0 ) $production_date_knitting=""; else $production_date_knitting= " and a.receive_date between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'"; //knitting
					if( $date_from==0 && $date_to==0 ) $production_date_dyeing=""; else $production_date_dyeing= " and a.process_end_date between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'"; //dyeing
					if( $date_from==0 && $date_to==0 ) $production_date_finishing=""; else $production_date_finishing= " and a.receive_date between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'"; //finishing
				}
				//=========================sample data =========================================
				$production_date_dyeing2 = str_replace("a.process_end_date", "f.process_end_date", $production_date_dyeing);
				$sql_sam="(SELECT f.process_end_date,f.floor_id, a.total_trims_weight as trims_weight,SUM(b.batch_qnty) AS batch_qnty  from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where f.batch_id=a.id and   a.entry_form=0 and  g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and f.result=1 and a.batch_against in(3) and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0 and f.company_id=$cbo_company $production_date_dyeing2 group by f.process_end_date,f.floor_id, a.total_trims_weight)
					union
				 	(select f.process_end_date,f.floor_id,a.total_trims_weight as trims_weight,SUM(b.batch_qnty) AS batch_qnty from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g,wo_non_ord_samp_booking_mst h where f.batch_id=a.id  and h.booking_no=a.booking_no  and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and f.result=1 and a.batch_against in(3) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  f.status_active=1 and f.is_deleted=0 and f.company_id=$cbo_company $production_date_dyeing2 GROUP BY f.process_end_date,f.floor_id,a.total_trims_weight)";
				 	// echo $sql_sam;die();
				$floor_qnty_dyeing=array();

				// $sqlDye="SELECT f.process_end_date,f.floor_id,sum(distinct a.total_trims_weight) as total_trims_weight, SUM(b.batch_qnty) AS batch_qnty from pro_batch_create_dtls b, wo_non_ord_samp_booking_mst h, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where f.batch_id=a.id and  a.entry_form=0 and  g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2  and a.batch_against in(3)  and h.booking_no=a.booking_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0 and f.company_id=$cbo_company $production_date_dyeing2 GROUP BY  f.process_end_date,f.floor_id ";
				// echo $sqlDye;//  and f.fabric_type in(1,2,4,5,6,7,8,9,10,11,12)
				$sqlDyeRes = sql_select($sql_sam);
				foreach ( $sqlDyeRes as $row )
				{
					$tot_trim_qty=$row[csf('total_trims_weight')];
					$floor_qnty_dyeing[change_date_format($row[csf("process_end_date")],'','',1)][$row[csf("floor_id")]]['shadematch'] +=$row[csf("batch_qnty")]+$tot_trim_qty;
				}
				// print_r($floor_qnty_dyeing);die();

				$sql_dt="(SELECT a.total_trims_weight,SUM(b.batch_qnty) AS batch_qnty, f.process_end_date, f.floor_id from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d,  pro_fab_subprocess f, lib_machine_name g, pro_batch_create_mst a where f.batch_id=a.id and f.batch_id=b.mst_id and g.id=f.machine_id and a.entry_form=0  and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1) and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and f.result=1 and  f.status_active=1 and f.is_deleted=0 and f.company_id=$cbo_company $production_date_dyeing2
					GROUP BY a.total_trims_weight,f.process_end_date, f.floor_id)
					union
					(SELECT a.total_trims_weight, SUM(b.batch_qnty) AS batch_qnty,f.process_end_date,f.floor_id from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g,wo_non_ord_samp_booking_mst h where h.booking_no=a.booking_no and f.batch_id=a.id  and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and f.result=1 and  f.status_active=1 and f.is_deleted=0
						and f.company_id=$cbo_company $production_date_dyeing2
						GROUP BY a.total_trims_weight,f.process_end_date,f.floor_id
					)";
				// echo $sql_dt;die();
				//====================================== batch qnty =============================
				// $sql_result="SELECT f.process_end_date,f.floor_id, sum(distinct CASE WHEN a.batch_against in(1) THEN a.total_trims_weight ELSE 0 END) AS total_trims_weight, SUM(b.batch_qnty) AS batch_qnty from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where f.batch_id=a.id and a.entry_form=0 and  g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2  and a.batch_against in(1,2,3)  and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and f.fabric_type in(1,2,4,5,6,7,8,9,10,11,12) and  f.status_active=1 and f.is_deleted=0 and f.company_id=$cbo_company $production_date_dyeing2 GROUP BY f.process_end_date,f.floor_id";
				// echo $sql_result;die();
				$sqlResult=sql_select($sql_dt);
				foreach ( $sqlResult as $row )
				{
					$tot_trim_qty=$row[csf('total_trims_weight')];
					$floor_qnty_dyeing[change_date_format($row[csf("process_end_date")],'','',1)][$row[csf("floor_id")]]['shadematch'] +=$row[csf("batch_qnty")]+$tot_trim_qty;
				}
				// print_r($floor_qnty_dyeing);pro_ex_factory_mst
				//========================================== REPROCESS QNTY ==============================
				$fabric_type_arr_chk=array(11,12);
				$sql_result_redyeing="SELECT a.fabric_type, a.floor_id, a.process_end_date,b.batch_against, sum(c.batch_qnty) as production_qnty,b.total_trims_weight from pro_fab_subprocess a,pro_batch_create_mst b,pro_batch_create_dtls c where b.id=c.mst_id and a.batch_id=b.id and a.entry_form in(35) and b.entry_form=0  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and a.floor_id !=0 and a.load_unload_id in(2) and a.result=1 and b.is_deleted=0 and b.batch_against in(2) $cbo_company_cond  $production_date_dyeing group by a.fabric_type, a.floor_id, a.process_end_date,b.batch_against,b.total_trims_weight";
				// echo $sql_result_redyeing;
				$sql_dtls_dyeing=sql_select($sql_result_redyeing);


				foreach ( $sql_dtls_dyeing as $row )
				{
					if(!in_array($row[csf('fabric_type')],$fabric_type_arr_chk))
					 {
						if($row[csf('batch_against')]==2)
						{
							$floor_qnty_dyeing[change_date_format($row[csf("process_end_date")],'','',1)][$row[csf("floor_id")]]['reprocess']+=$row[csf("production_qnty")]+$row[csf("total_trims_weight")];
						}
					}
					else
					{
					    $floor_qnty_dyeing[change_date_format($row[csf("process_end_date")],'','',1)][$row[csf("floor_id")]]['shadematch'] +=$row[csf("production_qnty")];
					}
				}
				//============================== FOR SUBCONTACT ============================
				$sql_sub_dye="SELECT a.id, a.floor_id, a.process_end_date,f.extention_no, b.batch_qnty AS sub_batch_qnty from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst f, pro_fab_subprocess a where a.batch_id=f.id  and  f.entry_form=36 and f.id=b.mst_id and a.entry_form=38 and a.batch_id=b.mst_id and a.load_unload_id=2 and a.result=1  and f.batch_against in(1,2) and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and  f.status_active=1 and f.is_deleted=0 $cbo_company_cond  $production_date_dyeing";

				$sql_sub_dye_res=sql_select($sql_sub_dye);

				foreach ($sql_sub_dye_res as $row )
				{
					if($row[csf("extention_no")]>0)
					{
						$floor_qnty_dyeing[change_date_format($row[csf("process_end_date")],'','',1)][$row[csf("floor_id")]]['reprocess'] +=$row[csf("sub_batch_qnty")];
					}
					else
					{
						$floor_qnty_dyeing[change_date_format($row[csf("process_end_date")],'','',1)][$row[csf("floor_id")]]['shadematch'] +=$row[csf("sub_batch_qnty")];
					}
				}
				$fin_production_date=str_replace("production_date", "receive_date", $production_date);
				$sql_result_finishing_fab="SELECT a.receive_date,b.receive_qnty as production_qnty,b.uom
				from inv_receive_master a,pro_finish_fabric_rcv_dtls b
				where a.id=b.mst_id and b.uom in(12,23)and a.entry_form in (7,37) and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $cbo_company_cond  $location_id $fin_production_date";
				//echo $sql_result_finishing_fab;
				$sql_dtls_finishing_fab=sql_select($sql_result_finishing_fab);
				$floor_qnty_finishing_fab=array();
				foreach ( $sql_dtls_finishing_fab as $row )
				{
					$floor_qnty_finishing_fab[change_date_format($row[csf("receive_date")],'','',1)][$row[csf("uom")]]+=$row[csf("production_qnty")];

				}
				$sql_result="SELECT a.id, a.production_type, a.floor_id, a.production_date, b.production_qnty as production_qnty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.production_type in (1,4,5,8,7,11) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $cbo_company_cond  $location_id_cond $production_date";
				$sql_dtls=sql_select($sql_result);
				$floor_qnty=array();
				foreach ( $sql_dtls as $row )
				{
					$floor_qnty[change_date_format($row[csf("production_date")],'','',1)][$row[csf("floor_id")]][$row[csf("production_type")]] +=$row[csf("production_qnty")];
				}
				$head_arr=array(1=>"Cutting Production",4=>"Sewing Input",5=>"Sewing Production",8=>"Packing");
				$unit_wise_total_array=array();
				$unit_wise_fab_total_array=array();
		        $floor_arr = return_library_array("select id,floor_name from  lib_prod_floor ","id","floor_name");

				$sql_sip_result="SELECT b.id,b.ex_factory_date,sum(b.ex_factory_qnty) as production_qnty
				from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b
				where a.id=b.delivery_mst_id and b.entry_form!=85 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $cbo_company_cond  $location_id $ex_factory_date group by b.id,b.ex_factory_date";
				//echo $sql_sip_result;
				$sql_ship=sql_select($sql_sip_result);
				$ship_qnty_data=array();
				foreach ( $sql_ship as $row )
				{
					$ship_qnty_data[change_date_format($row[csf("ex_factory_date")],'','',1)]+=$row[csf("production_qnty")];
				}
				$grand_total_tot="";
				?>
				<style type="text/css">
					.alignment_css
					{
						word-break: break-all;
						word-wrap: break-word;
					}
				</style>
		        <div align="center" style="height:auto;">
		        <table width="<? echo $table_width; ?> " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
		            <thead>
		                <tr>
		                    <th class='alignment_css' width="90" rowspan="2" >Date</th>
							<th class='alignment_css' width="<? echo $width_finish_fabric; ?>" colspan="<? echo $count_finish_fabric; ?>">Finish Fabric Receive</th>
		                    <?
		                     foreach ( $head_arr as $head=>$headval )
							 {
								 if ($head==1) {
								?>
		                             <th class='alignment_css' width="<? echo $width_cutting; ?>" colspan="<? echo $count_cutting; ?>"><? echo $headval; ?></th>
		                        <?
								 }
								 else if($head==4)
		                        {
		                        	?>
		                            <th class='alignment_css' width="<? echo $width_sewing_input; ?>" colspan="<? echo $count_sewing_input; ?>"><? echo $headval; ?></th>
		                        	<?
		                        }
		                        else if($head==5)
		                        {
									?>
										<th class='alignment_css' width="<? echo $width_sewing; ?>" colspan="<? echo $count_sewing; ?>"><? echo $headval; ?></th>
									<?
		                        }
								else if($head==8)
		                        {
									?>
		                            <th class='alignment_css' width="<? echo $width_packing; ?>" colspan="<? echo $count_packing; ?>"><? echo $headval; ?></th>
		                        	<?
		                        }
							 }
							?>
							<th class='alignment_css' width="80" rowspan="2">Shipment Delivery Qty.</th>
		                </tr>
		               <tr>
						<?
							foreach ( $sql_finish_fabric_receive as $rows )
							{
								//echo $unit_of_measurement[$rows[csf("uom")]];
								?>
								<th class='alignment_css' width="80" colspan="">
									<? echo $unit_of_measurement[$rows[csf("uom")]]; ?>
								</th>
								<?
							}
							foreach ( $head_arr as $head=>$headval )
							{
								 if($head==1)
		                        {
		    						foreach ( $sql_floor_cutting as $rows )
		    						{
		    							?>
		                                    <th class='alignment_css' width="80" colspan=""><? echo $floor_arr[$rows[csf("floor_id")]]; ?></th>
		    							<?
		    						}
		                        }
								else if($head==4)
		                        {
		                            foreach ( $sql_floor_sewing as $rows )
		                            {
		                                ?>
		                                    <th class='alignment_css' width="80" colspan=""><? echo $floor_arr[$rows[csf("floor_id")]]; ?></th>
		                                <?
		                            }
		                        }

		                        else if($head==5)
		                        {
		                            foreach ( $sql_floor_sewing as $rows )
		                            {
		                                ?>
		                                    <th class='alignment_css' width="80" colspan=""><? echo $floor_arr[$rows[csf("floor_id")]]; ?></th>
		                                <?
		                            }
		                        }

								else if($head==8)
		                        {
		                            foreach ( $sql_floor_finishing as $rows )
		                            {
		                                ?>
		                                    <th class='alignment_css' width="80" colspan=""><? echo $floor_arr[$rows[csf("floor_id")]]; ?></th>
		                                <?
		                            }
		                        }
								?>
								<th class='alignment_css' width="80" colspan="">Total</th>
							<?
							}
		                   ?>
		               </tr>
		            </thead>
		        </table>
		        </div>
		        <div align="center" style="max-height:300px" id="scroll_body">
		        <table align="center" cellspacing="0" width="<? echo $table_width; ?> "  border="1" rules="all" class="rpt_table" >
					<?
					$value_define=array();
		            for($j=0;$j<$datediff;$j++)
		            {
		                if ($j%2==0)
		                    $bgcolor="#E9F3FF";
		                else
		                    $bgcolor="#FFFFFF";

		                $date_data_array=array();
						$newdate =add_date(str_replace("'","",$txt_date_from),$j);
						$newdate=date("d-M-Y",strtotime(str_replace("'","",$newdate)));
		                $date_data_array[$j]=$newdate;


		            ?>
		            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>">
		                <td class='alignment_css' width="90"><? echo change_date_format($newdate); ?></td>
						<?
						foreach ( $sql_finish_fabric_receive as $rows )
						{
							?>

							<td class='alignment_css' width="80" align="right"><?  echo number_format($floor_qnty_finishing_fab[$date_data_array[$j]][$rows[csf("uom")]],0); ?></td>
							<?
							$unit_wise_fab_total_array[$rows[csf("uom")]]+=$floor_qnty_finishing_fab[$date_data_array[$j]][$rows[csf("uom")]];
							if($floor_qnty_finishing_fab[$date_data_array[$j]][$rows[csf("uom")]]!="")   $fab_value_define[$rows[csf("uom")]] +=1;
						}

		                foreach ( $head_arr as $head=>$headval )
		                {
							//echo "<pre>";
						// print_r( $date_data_array);
						//print_r($sql_sipment_del_data);

		                    $site_tot_qnty='';
							 if($head==1)
		                    {

		                        foreach ( $sql_floor_cutting as $rows )
		                        {
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><?  echo number_format($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head],0); ?></td>
		                            <?
		                             $site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
		                             $unit_wise_total_array[$head][$rows[csf("floor_id")]] +=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
		                             if($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head]!="")   $value_define[$head][$rows[csf("floor_id")]] +=1;

		                        }
		                        ?>
		                            <td class='alignment_css' width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>
		                        <?
		                    }
							else if($head==4)
		                    {
		                        foreach ( $sql_floor_sewing_input as $rows )
		                        {

		                            ?>
		                                <td class='alignment_css' width="80" align="right"><?  echo number_format($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head],0); ?></td>
		                            <?
		                             $site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];

		                             $unit_wise_total_array[$head][$rows[csf("floor_id")]] +=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];

		                             if($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head]!="")   $value_define[$head][$rows[csf("floor_id")]] +=1;

		                        }
		                        ?>
		                            <td class='alignment_css' width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>
		                        <?
		                    }
		                    else if($head==5)
		                    {
		                        foreach ( $sql_floor_sewing as $rows )
		                        {
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><?  echo number_format($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head],0); ?></td>
		                            <?
		                             $site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
		                             $unit_wise_total_array[$head][$rows[csf("floor_id")]] +=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
		                             if($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head]!="")   $value_define[$head][$rows[csf("floor_id")]] +=1;

		                        }
		                        ?>
		                            <td class='alignment_css' width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>
		                        <?
		                    }

							else if($head==8)
		                    {
		                        foreach ( $sql_floor_finishing as $rows )
		                        {
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><?  echo number_format($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head],0); ?></td>
		                            <?
		                             $site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
		                             $unit_wise_total_array[$head][$rows[csf("floor_id")]] +=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
		                             if($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head]!="")   $value_define[$head][$rows[csf("floor_id")]] +=1;

		                        }
		                        ?>
		                            <td class='alignment_css' width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>


		                        <?
		                    }

		                }
		                ?>
						<td class='alignment_css' align="right"><?  echo number_format($ship_qnty_data[$date_data_array[$j]],0); ?></td>
							<?
							 $grand_total_ship+=$ship_qnty_data[$date_data_array[$j]];

							 if($ship_qnty_data[$date_data_array[$j]]!="")$value_define[$head][$rows[csf("floor_id")]]+=1;
							 ?>

		            </tr>
		            <?
		            }
		            ?>
		            <tr bgcolor="#dddddd">
		                <td class='alignment_css' align="center" width="90"><strong>Total : </strong></td>
		                <?
							foreach ( $sql_finish_fabric_receive as $rows )
							{
								?>

								<td class='alignment_css' width="80" align="right"><?  echo number_format($unit_wise_fab_total_array[$rows[csf("uom")]],0); ?></td>
								<?
								// $unit_wise_fab_total_array[$rows[csf("uom")]]+=$floor_qnty_finishing_fab[$date_data_array[$j]][$rows[csf("uom")]];
								// if($floor_qnty_finishing_fab[$date_data_array[$j]][$rows[csf("uom")]]!="")   $fab_value_define[$rows[csf("uom")]] +=1;
							}
						foreach ( $head_arr as $head=>$headval )
		                {
							if($head==1)
		                    {
								$grand_total_cutting="";
		                        foreach ( $sql_floor_cutting as $rows )
		                        {
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><strong><? echo number_format($unit_wise_total_array[$head][$rows[csf("floor_id")]],0); ?></strong></td>
		                            <?
		                            $grand_total_cutting+=$unit_wise_total_array[$head][$rows[csf("floor_id")]];
		                        }
								?>
		                    	<td class='alignment_css' width="80" align="right"><strong><? echo number_format($grand_total_cutting,0); ?></strong></td>
		                    	<?
		                    }
							else if($head==4)
		                    {
								$grand_total_sewing_input="";
		                        foreach ( $sql_floor_sewing_input as $rows )
		                        {

		                            ?>
		                                <td class='alignment_css' width="80" align="right">
											<?
											echo number_format($unit_wise_total_array[$head][$rows[csf("floor_id")]] ,0);
										 ?></td>
		                            <?

		                             $grand_total_sewing_input+=$unit_wise_total_array[$head][$rows[csf("floor_id")]];
		                        }
		                        ?>
		                            <td class='alignment_css' width="80" align="right"><strong><? echo number_format($grand_total_sewing_input,0); ?></strong></td>
		                        <?
		                    }
							else if($head==5)
		                    {
								$grand_total_sewing="";
		                        foreach ( $sql_floor_sewing as $rows )
		                        {
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><strong><? echo number_format($unit_wise_total_array[$head][$rows[csf("floor_id")]],0); ?></strong></td>
		                            <?
		                            $grand_total_sewing+=$unit_wise_total_array[$head][$rows[csf("floor_id")]];
		                        }
								?>
		                            <td class='alignment_css' width="80" align="right"><strong><? echo number_format($grand_total_sewing,0); ?></strong></td>
		                        <?
		                    }
							else if($head==8)
		                    {
								$grand_total_packing="";
		                        foreach ( $sql_floor_finishing as $rows )
		                        {
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><strong><? echo number_format($unit_wise_total_array[$head][$rows[csf("floor_id")]],0); ?></strong></td>
		                            <?
		                            $grand_total_packing+=$unit_wise_total_array[$head][$rows[csf("floor_id")]];
		                        }
								?>
		                            <td class='alignment_css' width="80" align="right"><strong><? echo number_format($grand_total_packing,0); ?></strong></td>
		                        <?
		                    }

						}
		                ?>
						<td class='alignment_css' width="80" align="right"><strong><? echo number_format($grand_total_ship,0); ?></strong></td>

		            </tr>



		            <tr bgcolor="#dddddd">
		                <td class='alignment_css' align="center" width="90"><strong>Avg : </strong></td>
		                <?
						foreach ( $sql_finish_fabric_receive as $rows )
						{
							//$avg=$unit_wise_total_array[$head][$rows[csf("uom")]]/$value_define[$head][$rows[csf("uom")]];
							$avg=($fab_value_define[$rows[csf("uom")]]>0) ? $unit_wise_fab_total_array[$rows[csf("uom")]]/ $fab_value_define[$rows[csf("uom")]] : 0;

							?>
								<td class='alignment_css' width="80" align="right"><strong><? echo number_format( $avg,2); ?></strong></td>
							<?
							$avg_finish_fabric_receive+=$avg;
						}
						$m=1;

		                foreach ( $head_arr as $head=>$headval )
		                {
		                    $avg='';
		                    $avg_tot='';
		                    if($head==1)
		                    {
		                        foreach ( $sql_floor_cutting as $rows )
		                        {
		                            $avg=($value_define[$head][$rows[csf("floor_id")]]>0) ? $unit_wise_total_array[$head][$rows[csf("floor_id")]]/ $value_define[$head][$rows[csf("floor_id")]] :0;
		    						//$avg=$unit_wise_total_array[$head][$rows[csf("id")]]/$value_define[$head][$rows[csf("id")]];
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><strong><? echo number_format( $avg,2); ?></strong></td>
		                            <?
		                            $avg_tot+=$avg;
		                        }
		                        ?>
		                            <td class='alignment_css' width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>
		                        <?
		                    }
							else if($head==4)
		                    {
		                        foreach ( $sql_floor_sewing_input as $rows )
		                        {
									$avg=($value_define[$head][$rows[csf("floor_id")]]>0) ? $unit_wise_total_array[$head][$rows[csf("floor_id")]]/ $value_define[$head][$rows[csf("floor_id")]] :0;
		                            //$avg=$unit_wise_total_array[$head][$rows[csf("floor_id")]]/ $value_define[$head][$rows[csf("floor_id")]];
		                            //$avg=$unit_wise_total_array[$head][$rows[csf("id")]]/$value_define[$head][$rows[csf("id")]];
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><strong><? echo number_format( $avg,2); ?></strong></td>
		                            <?
		                            $avg_tot+=$avg;
		                        }
								?>
		                    <td class='alignment_css' width="80" align="right"><strong><?  echo number_format($avg_tot,2); ?></strong></td>
		                <?
		                    }
		                    else if($head==5)
		                    {
		                        foreach ( $sql_floor_sewing as $rows )
		                        {
									$avg=($value_define[$head][$rows[csf("floor_id")]]>0) ? $unit_wise_total_array[$head][$rows[csf("floor_id")]]/ $value_define[$head][$rows[csf("floor_id")]] :0;
		                            //$avg=$unit_wise_total_array[$head][$rows[csf("floor_id")]]/ $value_define[$head][$rows[csf("floor_id")]];
		                            //$avg=$unit_wise_total_array[$head][$rows[csf("id")]]/$value_define[$head][$rows[csf("id")]];
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><strong><? echo number_format( $avg,2); ?></strong></td>
		                            <?
		                            $avg_tot+=$avg;
		                        }
								?>
		                    <td class='alignment_css' width="80" align="right"><strong><?  echo number_format($avg_tot,2); ?></strong></td>
		                <?
		                    }
							else if($head==8)
		                    {
		                        foreach ( $sql_floor_finishing as $rows )
		                        {
									$avg=($value_define[$head][$rows[csf("floor_id")]]>0) ? $unit_wise_total_array[$head][$rows[csf("floor_id")]]/ $value_define[$head][$rows[csf("floor_id")]] :0;
		                           // $avg=$unit_wise_total_array[$head][$rows[csf("floor_id")]]/ $value_define[$head][$rows[csf("floor_id")]];
		                            //$avg=$unit_wise_total_array[$head][$rows[csf("id")]]/$value_define[$head][$rows[csf("id")]];
		                            ?>
		                                <td class='alignment_css' width="80" align="right"><strong><? echo number_format( $avg,2); ?></strong></td>
		                            <?
		                            $avg_tot+=$avg;
		                        }
								?>
		                    <td class='alignment_css' width="80" align="right"><strong><?  echo number_format($avg_tot,2); ?></strong></td>
		                <?
		                    }

		                }
						?>
		                    <td class='alignment_css' width="80" align="right"><strong><?

							$avg=$grand_total_ship/$value_define[$head][$rows[csf("floor_id")]];
							 echo number_format($avg,2); ?></strong></td>
		                <?
		                ?>
		            </tr>

		        </table>
		    </div>
		    </div>
		    <?
		}
	}
	elseif ($type==6) // show4 button
	{
	    $floor_arr = return_library_array("select id,floor_name from  lib_prod_floor ","id","floor_name");
		$lineArr = return_library_array("select a.id,a.line_name from lib_sewing_line a","id","line_name");
		$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
		$cbo_company_id=str_replace("'","",$cbo_company_id);
		$cbo_location_id=str_replace("'","",$cbo_location_id);
		if($cbo_company==0) $cbo_company_cond=""; else $cbo_company_cond =" and a.serving_company in($cbo_company)";
		if($cbo_company==0) $cbo_company_cond2=""; else $cbo_company_cond2 =" and a.company_id in($cbo_company)";


		if($db_type==0)
		{
			if( $date_from==0 && $date_to==0 ) $production_date=""; else $production_date= " and a.production_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
		}
		else
		{
			if( $date_from==0 && $date_to==0 ) $production_date=""; else $production_date= " and a.production_date between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'";
		}

		//***************************************************************************************************************************
		$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
		order by sewing_line_serial");
		foreach($lineDataArr as $lRow)
		{
			$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
			$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
			$lastSlNo=$lRow[csf('sewing_line_serial')];
		}


		if($db_type==0)
		{
			$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id in ($comapny_id) and shift_id=1 and pr_date between $txt_date_from and $txt_date_to and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
		}
		else
		{
			$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id in($comapny_id) and shift_id=1 and pr_date between $txt_date_from and $txt_date_to and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
		}
		if($min_shif_start=="")
		{
			echo "<p style='font-size:20px', align='center'>No Line Engage for the selected Date.Please Check Actual Production Resource Entry.<p/>";die;

		}
		//echo $min_shif_start; die;


		//==============================shift time===================================================================================================
		$start_time_arr=array();
		if($db_type==0)
		{
			$start_time_data_arr=sql_select("SELECT company_name, shift_id, TIME_FORMAT( prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( lunch_start_time, '%H:%i' ) as lunch_start_time from variable_settings_production where company_name in($comapny_id) and shift_id=1  and variable_list=26 and status_active=1 and is_deleted=0");
		}
		else
		{
			$start_time_data_arr=sql_select("SELECT company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time,TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($comapny_id) and  shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");
		}
	  //  echo $start_time_data_arr; die;
		foreach($start_time_data_arr as $row)
		{
			$start_time_arr[$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
			$start_time_arr[$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];
		}

		$prod_start_hour=$start_time_arr[$row[csf('company_name')]][1]['pst'];
		$global_start_lanch=$start_time_arr[$row[csf('company_name')]][1]['lst'];
		if($prod_start_hour=="") $prod_start_hour="08:00";
		$start_time=explode(":",$prod_start_hour);
		$hour=substr($start_time[0],1,1); $minutes=$start_time[1]; $last_hour=23;
		$lineWiseProd_arr=array(); $prod_arr=array(); $start_hour_arr=array();
		$start_hour=$prod_start_hour;
		$start_hour_arr[$hour]=$start_hour;
		for($j=$hour;$j<$last_hour;$j++)
		{
			$start_hour=add_time($start_hour,60);
			$start_hour_arr[$j+1]=substr($start_hour,0,5);
		}
		//echo $pc_date_time;die;
		$start_hour_arr[$j+1]='23:59';
		if($prod_start_hour>$min_shif_start)  $prod_start_hour=$min_shif_start;
		$actual_date=date("Y-m-d");
		$actual_production_date=date("Y-m-d",strtotime(str_replace("'","",$txt_date_from)));
		$actual_time=substr(date("Y-m-d H:i:s",strtotime($pc_date_time)),11,2);
		$acturl_hour_minute=date("H:i",strtotime($pc_date_time));
		$generated_hourarr=array();
		$first_hour_time=explode(":",$min_shif_start);
		$hour_line=substr($first_hour_time[0],1,1); $minutes_one=$start_time[1];
		$line_start_hour_arr[$hour_line]=$min_shif_start;

		for($l=$hour_line;$l<$last_hour;$l++)
		{
			$min_shif_start=add_time($min_shif_start,60);
			$line_start_hour_arr[$l+1]=substr($min_shif_start,0,5);
		}

		$line_start_hour_arr[$j+1]='23:59';
		$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name in($cbo_company_id) and variable_list=23 and is_deleted=0
		and status_active=1");

		//print_r($prod_reso_allo);
		// echo $prod_reso_allo;


		//if(str_replace("'","",$cbo_company_id)==0) $company_name=""; else $company_name="and a.company_id=".str_replace("'","",$cbo_company_id)."";
		if(str_replace("'","",$cbo_company_id)==0) $company_name=""; else $company_name="and a.serving_company in(".str_replace("'","",$cbo_company_id).")";

		if(str_replace("'","",$cbo_location_id)=="")
		{
			$subcon_location="";
			$location="";
		}
		else
		{
			$location=" and a.location in(".str_replace("'","",$cbo_location_id).")";

			$subcon_location=" and a.location_id in(".str_replace("'","",$cbo_location_id).")";
		}
		//echo 	$subcon_location;
		//;die;
		$cbo_floor_id=str_replace("'","",$cbo_floor_id);
		if($cbo_floor_id=="") $floor=""; else $floor="and a.floor_id in(".$cbo_floor_id.")";
	    if(str_replace("'","",$hidden_line_id)==0)
		{
			$line="";
			$subcon_line="";
		}
		else
		{
			$subcon_line="and a.line_id in(".str_replace("'","",$hidden_line_id).")";
			$line="and a.sewing_line in(".str_replace("'","",$hidden_line_id).")";
		}
		$cbo_no_prod_type=str_replace("'","",$cbo_no_prod_type);
		$file_no=str_replace("'","",$txt_file_no);
		$ref_no=str_replace("'","",$txt_ref_no);
		if($file_no!="") $file_cond="and c.file_no=$file_no";else $file_cond="";
		if($ref_no!="") $ref_cond="and c.grouping='$ref_no'";else $ref_cond="";
		//echo $file_cond;

		// if(str_replace("'","",trim($txt_date_from))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date between $txt_date_from and $txt_date_to";
		// echo $txt_date_from; die;

		/* =============================================================================================/
		/									Prod Resource Data											/
		/============================================================================================= */

		if($prod_reso_allo==1)
		{
			$prod_resource_array=array();

			$dataArray_sql=("SELECT a.id,a.company_id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=b.mst_id and a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id in($comapny_id) and b.pr_date between $txt_date_from and $txt_date_to and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0");
			//echo $dataArray_sql; die;
			$sql_dataArray=sql_select($dataArray_sql);

			foreach($sql_dataArray as $val)
			{
				$prod_resource_array[$val[csf('id')]][$val[csf('company_id')]][$val[csf('pr_date')]]['man_power']=$val[csf('man_power')];
				$prod_resource_array[$val[csf('id')]][$val[csf('company_id')]][$val[csf('pr_date')]]['operator']=$val[csf('operator')];
				$prod_resource_array[$val[csf('id')]][$val[csf('company_id')]][$val[csf('pr_date')]]['helper']=$val[csf('helper')];
				$prod_resource_array[$val[csf('id')]][$val[csf('company_id')]][$val[csf('pr_date')]]['terget_hour']=$val[csf('target_per_hour')];
				$prod_resource_array[$val[csf('id')]][$val[csf('company_id')]][$val[csf('pr_date')]]['working_hour']=$val[csf('working_hour')];
				$prod_resource_array[$val[csf('id')]][$val[csf('company_id')]][$val[csf('pr_date')]]['tpd']=$val[csf('target_per_hour')]*$val[csf('working_hour')];
				$prod_resource_array[$val[csf('id')]][$val[csf('company_id')]][$val[csf('pr_date')]]['day_start']=$val[csf('from_date')];
				$prod_resource_array[$val[csf('id')]][$val[csf('company_id')]][$val[csf('pr_date')]]['day_end']=$val[csf('to_date')];
				$prod_resource_array[$val[csf('id')]][$val[csf('company_id')]][$val[csf('pr_date')]]['capacity']=$val[csf('capacity')];
				$prod_resource_array[$val[csf('id')]][$val[csf('company_id')]][$val[csf('pr_date')]]['smv_adjust']=$val[csf('smv_adjust')];
				$prod_resource_array[$val[csf('id')]][$val[csf('company_id')]][$val[csf('pr_date')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];
			}

			// print_r($prod_resource_array);die();
			if(str_replace("'","",trim($txt_date_from))==""){$pr_date_con="";}else{$pr_date_con=" and b.pr_date between $txt_date_from and $txt_date_to";}

			if($db_type==0)
			{
				$dataArray=sql_select("SELECT a.id,a.company_id;b.pr_date,d.shift_id,TIME_FORMAT( d.prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( d.lunch_start_time, '%H:%i' ) as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id in($comapny_id) and shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 $pr_date_con");
			}
			else
			{
				$dataArray=("SELECT a.id,a.company_id,b.pr_date,d.shift_id,TO_CHAR(d.prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR( d.lunch_start_time,'HH24:MI') as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id in($comapny_id) and shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 $pr_date_con");
			}
			//echo $dataArray;die;

			$line_number_arr=array();
			foreach($dataArray as $val)
			{
				$line_number_arr[$val[csf('id')]][$val[csf('company_id')]][$val[csf('pr_date')]]['shift_id']=$val[csf('shift_id')];
				$line_number_arr[$val[csf('id')]][$val[csf('company_id')]][$val[csf('pr_date')]]['prod_start_time']=$val[csf('prod_start_time')];
				$line_number_arr[$val[csf('id')]][$val[csf('company_id')]][$val[csf('pr_date')]]['lunch_start_time']=$val[csf('lunch_start_time')];
			}

			// print_r($line_number_arr);die();
		}
	 	//***************************************************************************************************
	  	if($db_type==0)
		{
			$manufacturing_company=return_field_value("group_concat(comp.id) as company_id","lib_company as comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
		}
		else
		{
			$manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
		}


		if($db_type==0) $prod_start_cond="prod_start_time";
		else if($db_type==2) $prod_start_cond="TO_CHAR(prod_start_time,'DD-MON-YYYY HH24:MI')";


		$variable_start_time_arr='';
		$prod_start_time=sql_select("SELECT $prod_start_cond as prod_start_time from variable_settings_production where company_name in($cbo_company_id) and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");
		//echo "select company_name, prod_start_time from variable_settings_production where company_name=$cbo_company_id and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1";
		foreach($prod_start_time as $row)
		{
			$ex_time=explode(" ",$row[csf('prod_start_time')]);
			if($db_type==0) $variable_start_time_arr=$row[csf('prod_start_time')];
			else if($db_type==2) $variable_start_time_arr=$ex_time[1];
		}//die;
		//echo $variable_start_time_arr;
		unset($prod_start_time);
		$current_date_time=date('d-m-Y H:i');
		$variable_date=change_date_format(str_replace("'","",$txt_date_from)).' '.$variable_start_time_arr;
		//echo $variable_date.'='.$current_date_time;
		$datediff_=datediff("n",$variable_date,$current_date_time);

		$ex_date_time=explode(" ",$current_date_time);
		$current_date=$ex_date_time[0];
		$current_time=$ex_date_time[1];
		$ex_time=explode(":",$current_time);

		$search_prod_date=change_date_format(str_replace("'","",$txt_date_from));
		$current_eff_min=($ex_time[0]*60)+$ex_time[1];
		//echo $current_date.'='.$search_prod_date;
		$variable_time= explode(":",$variable_start_time_arr);
		$vari_min=($variable_time[0]*60)+$variable_time[1];
		$difa_time=explode(".",number_format(($current_eff_min-$vari_min)/60,2));//datediff("",$ctime,$variable_start_time_arr);
		$dif_time=number_format($datediff_/60,2);
		$dif_hour_min=date("H", strtotime($dif_time));

	   	//$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($manufacturing_company) and variable_list=25 and   status_active=1 and is_deleted=0");



		if($db_type==2)
		{
			$pr_date= str_replace("'", "", $txt_date_from);
			$pr_date_old=explode("-",str_replace("'","",$txt_date_from));
			$month=strtoupper($pr_date_old[1]);
			$year=substr($pr_date_old[2],2);
			$pr_date=$pr_date_old[0]."-".$month."-".$year;
		}
		if($db_type==0)
		{
			$pr_date=str_replace("'","",$txt_date_from);
		}

		$i=1; $grand_total_good=0; $grand_alter_good=0; $grand_total_reject=0;
		$html="";
		$floor_html="";
	    $check_arr=array();
		/* =============================================================================================/
		/									Prod Inhouse Data											/
		/============================================================================================= */
		if($db_type==0)
		{
			$sql="SELECT  a.serving_company, a.location, a.floor_id,d.floor_serial_no,e.sewing_line_serial, a.prod_reso_allo, a.production_date, a.sewing_line,
			b.buyer_name  as buyer_name,b.style_ref_no,b.job_no,a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.unit_price,c.file_no,c.grouping as ref,sum(a.production_quantity) as good_qnty,";
			$first=1;
			for($h=$hour;$h<$last_hour;$h++)
			{
				$bg=$start_hour_arr[$h];
				$end=substr(add_time($start_hour_arr[$h],60),0,5);
				$prod_hour="prod_hour".substr($bg,0,2);
				if($first==1)
				{
					$sql.="sum(CASE WHEN   a.production_hour<='$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,";
				}
				else
				{
					$sql.="sum(CASE WHEN a.production_hour>'$bg' and  a.production_hour<='$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,";
				}
				$first=$first+1;
			}
			$sql.="sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour23 from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a left join lib_prod_floor d on a.floor_id=d.id and d.is_deleted=0 left join lib_sewing_line e on a.sewing_line=e.id and e.is_deleted=0  where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $company_name $location $floor $line $buyer_id_cond $production_date $file_cond $ref_cond group by b.job_no,a.serving_company, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,e.sewing_line_serial,b.buyer_name,a.item_number_id,c.po_number,c.file_no,c.unit_price,c.grouping,b.style_ref_no order by a.location, a.floor_id,e.sewing_line_serial,a.prod_reso_allo";
		}
		else if($db_type==2)
		{
			$sql="SELECT  a.serving_company, a.location, a.floor_id,a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name  as buyer_name,b.style_ref_no,b.job_no, a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.file_no,c.unit_price,c.grouping as ref,sum(d.production_qnty) as good_qnty,";
			$first=1;
			for($h=$hour;$h<$last_hour;$h++)
			{
				$bg=$start_hour_arr[$h];
				$end=substr(add_time($start_hour_arr[$h],60),0,5);
				$prod_hour="prod_hour".substr($bg,0,2);
				if($first==1)
				{
					$sql.="sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN production_qnty else 0 END) AS $prod_hour,";
				}
				else
				{
					$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5
					THEN production_qnty else 0 END) AS $prod_hour,";
				}
				$first++;
			}
			$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN production_qnty else 0 END) AS prod_hour23
			FROM  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a ,pro_garments_production_dtls d
			WHERE a.production_type=5 and a.po_break_down_id=c.id and c.job_id=b.id and a.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  $company_name $location $floor $line $buyer_id_cond  $production_date $file_cond $ref_cond
			GROUP BY b.job_no, a.serving_company , a.location, a.floor_id,a.po_break_down_id,a.item_number_id,a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name,b.style_ref_no,c.po_number,c.unit_price,c.file_no,c.grouping
			ORDER BY a.location,a.floor_id,a.sewing_line";
		}
	 	//echo $sql;die;
		$sql_resqlt=sql_select($sql);
		$production_data_arr=array();
		$production_po_data_arr=array();
		$production_serial_arr=array();
		$production_target_min_arr=array();
		$reso_line_ids='';
		$all_po_id="";
		$active_days_arr=array();
		$duplicate_date_arr=array();
		foreach($sql_resqlt as $val)
		{

			if($val[csf('prod_reso_allo')]==1)
			{
				$sewing_line_id=$prod_reso_arr[$val[csf('sewing_line')]];
				$reso_line_ids.=$val[csf('sewing_line')].',';
			}
			else
			{
				$sewing_line_id=$val[csf('sewing_line')];
			}

			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id]=$slNo;
			}
			else $slNo=$lineSerialArr[$sewing_line_id];
			$production_serial_arr[$val[csf('serving_company')]][$val[csf('production_date')]][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]]=$val[csf('sewing_line')];

			$line_start=$line_number_arr[csf('serving_company')][$val[csf('production_date')]][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]]=$val[csf('sewing_line')];
			[$val[csf('sewing_line')]][$val[csf('production_date')]]['prod_start_time'];
			if($line_start!="")
			{
				$line_start_hour=substr($line_start,0,2);
				if(substr($line_start_hour,0,1)==0)  $line_start_hour=substr($line_start_hour,1,1);
			}
			else
			{
				$line_start_hour=$hour;
			}

		 	for($h=$hour;$h<$last_hour;$h++)
			{
				$prod_hour="prod_hour".substr($start_hour_arr[$h],0,2)."";
				$production_data_arr[$val[csf('serving_company')]][$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$prod_hour]+=$val[csf($prod_hour)];

				if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
				{
					if( $h>=$line_start_hour && $h<=$actual_time)
					{
						$production_po_data_arr[$val[csf('serving_company')]][$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]] += $val[csf($prod_hour)];
					}
				}

				if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date))
				{
					$production_po_data_arr[$val[csf('serving_company')]][$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]] += $val[csf($prod_hour)];
				}
			}

			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
			{
				if( $h>=$line_start_hour && $h<=$actual_time)
				{
					$production_po_data_arr[$val[csf('serving_company')]][$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]] += $val[csf('prod_hour23')];
				}
			}
			else
			{
				$production_po_data_arr[$val[csf('serving_company')]][$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]] += $val[csf('prod_hour23')];
			}

		 	$production_data_arr[$val[csf('serving_company')]][$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['prod_hour23']+=$val[csf('prod_hour23')];
			$production_data_arr[$val[csf('serving_company')]][$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['prod_reso_allo']=$val[csf('prod_reso_allo')];

		 	if($production_data_arr[$val[csf('serving_company')]][$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']!="")
			{
				$production_data_arr[$val[csf('serving_company')]][$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name'].=",".$val[csf('buyer_name')];
			}
		 	else
			{
				$production_data_arr[$val[csf('serving_company')]][$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']=$val[csf('buyer_name')];
			}

		 	if($production_data_arr[$val[csf('serving_company')]][$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']!="")
			{
				$production_data_arr[$val[csf('serving_company')]][$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number'].=",".$val[csf('po_number')];
				$production_data_arr[$val[csf('serving_company')]][$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['job_no'].=",".$val[csf('job_no')];
				$production_data_arr[$val[csf('serving_company')]][$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_id'].=",".$val[csf('po_break_down_id')];
				$production_data_arr[$val[csf('serving_company')]][$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['style'].=",".$val[csf('style_ref_no')];
				$production_data_arr[$val[csf('serving_company')]][$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['file'].=",".$val[csf('file_no')];
				$production_data_arr[$val[csf('serving_company')]][$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['ref'].=",".$val[csf('ref')];
			}
		 	else
			{
				$production_data_arr[$val[csf('serving_company')]][$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']=$val[csf('po_number')];
				$production_data_arr[$val[csf('serving_company')]][$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['job_no']=$val[csf('job_no')];
				$production_data_arr[$val[csf('serving_company')]][$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_id']=$val[csf('po_break_down_id')];
				$production_data_arr[$val[csf('serving_company')]][$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['style']=$val[csf('style_ref_no')];
				$production_data_arr[$val[csf('serving_company')]][$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['file']=$val[csf('file_no')];
				$production_data_arr[$val[csf('serving_company')]][$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['ref']=$val[csf('ref')];
			}
			$fob_rate_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['rate']=$val[csf('unit_price')];

			if($production_data_arr[$val[csf('serving_company')]][$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']!="")
			{
				$production_data_arr[$val[csf('serving_company')]][$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')];
			}
			else
			{
				 $production_data_arr[$val[csf('serving_company')]][$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')];
			}
			$production_data_arr[$val[csf('serving_company')]][$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['quantity']+=$val[csf('good_qnty')];
			$production_data_arr_qty[$val[csf('serving_company')]][$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('serving_company')]][$val[csf('item_number_id')]]['quantity']+=$val[csf('good_qnty')];

			if($all_po_id=="") $all_po_id=$val[csf('po_break_down_id')]; else $all_po_id.=",".$val[csf('po_break_down_id')];
			$all_po_arr[$val["PO_BREAK_DOWN_ID"]]=$val["PO_BREAK_DOWN_ID"];

			$production_target_min_arr[$val[csf('serving_company')]][$val[csf('production_date')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]['quantity']+=$val[csf('good_qnty')];

			$all_style_arr[$val[csf('style_ref_no')]] = $val[csf('style_ref_no')];
			$style_wise_po_arr[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];

		}

	 //	echo"<pre>"; print_r($production_target__min_arr);die();
		$po_ids=count(array_unique(explode(",",$all_po_id)));
		$po_numIds=chop($all_po_id,',');
		$poIds_cond="";
		$poIds_cond2="";
		$empty_arr=array();
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from =1 and ENTRY_FORM=654");
		oci_commit($con);
        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 654, 1,$all_po_arr, $empty_arr);
		disconnect($con);
		// if($all_po_id!='' || $all_po_id!=0)
		// {
		// 	if($db_type==2 && $po_ids>1000)
		// 	{
		// 		$poIds_cond=" and (";
		// 		$poIds_cond2=" and (";
		// 		$poIdsArr=array_chunk(explode(",",$po_numIds),990);
		// 		foreach($poIdsArr as $ids)
		// 		{
		// 			$ids=implode(",",$ids);
		// 			$poIds_cond.=" b.id  in($ids) or ";
		// 			$poIds_cond2.=" c.id  in($ids) or ";
		// 		}
		// 		$poIds_cond=chop($poIds_cond,'or ');
		// 		$poIds_cond2=chop($poIds_cond,'or ');
		// 		$poIds_cond.=")";
		// 		$poIds_cond2.=")";
		// 	}
		// 	else
		// 	{
		// 		$poIds_cond=" and  b.id  in($all_po_id)";
		// 		$poIds_cond2=" and  c.id  in($all_po_id)";
		// 	}
		// }



		/* =============================================================================================/
		/											SMV Source											/
		/============================================================================================= */
		$smv_source=return_field_value("smv_source","variable_settings_production","company_name in($comapny_id) and variable_list=25 and   status_active=1 and is_deleted=0");


		// echo $smv_source;die;
	    if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;

	    if($smv_source==3) // from gsd enrty
		{
			$style_cond = where_con_using_array($all_style_arr,1,"a.style_ref");
			$sql_item="SELECT a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID  from PPL_GSD_ENTRY_MST a where  A.IS_DELETED=0 and A.STATUS_ACTIVE=1 $style_cond  group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID ORDER BY a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC";//a.APPLICABLE_PERIOD <= $txt_date_to and
			$gsdSqlResult=sql_select($sql_item);
			//echo $sql_item;die;

			foreach($gsdSqlResult as $rows)
			{
				foreach($style_wise_po_arr[$rows['STYLE_REF']] as $po_id)
				{
					if($item_smv_array[$po_id][$rows['GMTS_ITEM_ID']]=='')
					{
						$item_smv_array[$po_id][$rows['GMTS_ITEM_ID']]=$rows['TOTAL_SMV'];
					}
				}
			}
		}
		else
		{
			$sql_item="SELECT b.id, a.set_break_down,a.company_name, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost,c.smv_set from wo_po_details_master a,wo_po_break_down b, wo_po_details_mas_set_details c,  GBL_TEMP_ENGINE d where a.id=b.job_id and b.job_id=c.job_id and a.id=c.job_id and b.id=d.ref_val and d.user_id=$user_id and d.entry_form=654 and a.company_name in($manufacturing_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$resultItem=sql_select($sql_item);

			//echo $sql_item;die;


			foreach($resultItem as $itemData)
			{
				if($smv_source==1)
				{
					$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
				}
				if($smv_source==2)
				{
					$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs_precost')];
				}
			}

		}
		//echo "<pre>"; print_r($item_smv_array);die;

		/* =============================================================================================/
		/										Active PO Data											/
		/============================================================================================= */
	    $po_active_sql="SELECT a.serving_company,a.floor_id,a.sewing_line,a.production_date,a.po_break_down_id,a.item_number_id from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a,  GBL_TEMP_ENGINE d where  a.po_break_down_id=c.id and c.job_no_mst=b.job_no and c.id=d.ref_val and d.user_id=$user_id and d.entry_form=654 and a.production_type=5 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $company_name $location $floor $line $buyer_id_cond   $file_cond $ref_cond $poIds_cond2 group by a.serving_company, a.floor_id,a.sewing_line,a.production_date ,a.po_break_down_id,a.item_number_id";
	  // echo $po_active_sql;die;
		foreach(sql_select($po_active_sql) as $vals)
		{
			$prod_dates=$vals[csf('production_date')];
			if($duplicate_date_arr[$vals[csf('serving_company')]][$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]][$prod_dates]=="")
			{
				$active_days_arr[$vals[csf('serving_company')]][$vals[csf('floor_id')]][$vals[csf('sewing_line')]]+=1;
				$active_days_arr_powise[$vals[csf('serving_company')]][$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]]+=1;
				$duplicate_date_arr[$vals[csf('serving_company')]][$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]][$prod_dates]=$prod_dates;
			}

		}
		//print_r($active_days_arr);

		$sql_item_rate="SELECT b.id,a.company_name, c.item_number_id, c.order_quantity, c.order_total from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c ,  GBL_TEMP_ENGINE d where b.job_no_mst=a.job_no and b.id=c.po_break_down_id and b.job_no_mst=c.job_no_mst and b.id=d.ref_val and d.user_id=$user_id and d.entry_form=654 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1  $file_cond $ref_cond  $poIds_cond";
		$resultRate=sql_select($sql_item_rate);
		$item_po_array=array();
		foreach($resultRate as $row)
		{
			$item_po_array[$row[csf('id')]][$row[csf('company_name')]][$row[csf('item_number_id')]]['qty']+=$row[csf('order_quantity')];
			$item_po_array[$row[csf('id')]][$row[csf('company_name')]][$row[csf('item_number_id')]]['amt']+=$row[csf('order_total')];
		}
		// execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form=654");
        // oci_commit($con);
		/* =============================================================================================/
		/										Prod Subcon Data										/
		/============================================================================================= */

	    if($db_type==0)
	    {
			$sql_sub_contuct= "SELECT  a.company_id, a.location_id,d.floor_serial_no,e.sewing_line_serial,a.prod_reso_allo, a.floor_id, a.production_date, a.line_id,b.party_id  as buyer_name,a.order_id,c.order_no as po_number,c.cust_style_ref,b.subcon_job as job_no, max(c.smv) as smv,sum(a.production_qnty) as good_qnty,";

			$first=1;
			for($h=$hour;$h<$last_hour;$h++)
			{
				$bg=$start_hour_arr[$h];
				$end=substr(add_time($start_hour_arr[$h],60),0,5);
				$prod_hour="prod_hour".substr($bg,0,2);
				if($first==1)
				{
					$sql_sub_contuct.="sum(CASE WHEN  a.hour<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,";
				}
				else
				{
					$sql_sub_contuct.="sum(CASE WHEN a.hour>'$bg' and a.hour<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,";
				}
				$first=$first+1;
	   		}
	   		$sql_sub_contuct.="sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a left join lib_prod_floor d on a.floor_id=d.id and d.is_deleted=0 left join lib_sewing_line e on a.line_id=e.id and e.is_deleted=0 where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id in($comapny_id) $subcon_location $floor $subcon_line   $production_date group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date,a.prod_reso_allo,a.line_id,b.party_id,c.order_no,c.cust_style_ref,b.subcon_job ,e.sewing_line_serial order by a.location_id,d.floor_serial_no,e.sewing_line_serial,a.prod_reso_allo";
		}
		else
		{
			$sql_sub_contuct= "select  a.company_id, a.location_id, a.floor_id,d.floor_serial_no,e.sewing_line_serial, a.production_date, a.line_id,b.party_id  as buyer_name,a.prod_reso_allo,a.order_id,c.order_no as po_number,c.cust_style_ref, b.subcon_job as job_no,  max(c.smv) as smv,a.prod_reso_allo,a.GMTS_ITEM_ID,sum(a.production_qnty) as good_qnty,";
			$first=1;
			for($h=$hour;$h<$last_hour;$h++)
			{
				$bg=$start_hour_arr[$h];
				$end=substr(add_time($start_hour_arr[$h],60),0,5);
				$prod_hour="prod_hour".substr($bg,0,2);
				if($first==1)
				{
					$sql_sub_contuct.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,";
				}
				else
				{
					$sql_sub_contuct.="sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')>'$bg' and TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,";
				}
				$first++;
			}

		   	$sql_sub_contuct.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]'	and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23
			from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a left join lib_prod_floor d on a.floor_id=d.id  and d.is_deleted=0 left join lib_sewing_line e on a.line_id=e.id and e.is_deleted=0 where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id in($comapny_id) $subcon_location $floor $subcon_line $production_date
			group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date,  b.subcon_job,a.line_id,b.party_id,c.order_no,c.cust_style_ref,e.sewing_line_serial,a.prod_reso_allo order by a.location_id, a.floor_id,e.sewing_line_serial,a.prod_reso_allo,a.GMTS_ITEM_ID";

		}
		//echo $sql_sub_contuct;die;
		$sub_result=sql_select($sql_sub_contuct);
		$subcon_order_smv=array();
		foreach($sub_result as $subcon_val)
		{

			if($val[csf('prod_reso_allo')]==1)
			{
				$sewing_line_id=$prod_reso_arr[$subcon_val[csf('sewing_line')]];
			}
			else
			{
				$sewing_line_id=$subcon_val[csf('sewing_line')];
			}

			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id]=$slNo;
			}
			else $slNo=$lineSerialArr[$sewing_line_id];
			//$production_serial_arr[$subcon_val[csf('floor_id')]][$slNo][$subcon_val[csf('sewing_line')]]=$subcon_val[csf('sewing_line')];

			$production_po_data_arr[$subcon_val[csf('company_id')]][$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]][$subcon_val[csf('gmts_item_id')]]+=$subcon_val[csf('good_qnty')];
			if($production_data_arr[$subcon_val[csf('company_id')]][$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['buyer_name']!="")
			{
				$production_data_arr[$subcon_val[csf('company_id')]][$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['buyer_name'].=",".$subcon_val[csf('buyer_name')];
			}
			else
			{
				$production_data_arr[$subcon_val[csf('company_id')]][$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['buyer_name']=$subcon_val[csf('buyer_name')];
			}

			if($production_data_arr[$subcon_val[csf('company_id')]][$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number']!="")
			{
				$production_data_arr[$subcon_val[csf('company_id')]][$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number'].=",".$subcon_val[csf('po_number')];
				$production_data_arr[$subcon_val[csf('company_id')]][$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['job_no'].=",".$subcon_val[csf('job_no')];
				$production_data_arr[$subcon_val[csf('company_id')]][$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['style'].=",".$subcon_val[csf('cust_style_ref')];
			}
			else
			{
				$production_data_arr[$subcon_val[csf('company_id')]][$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number']=$subcon_val[csf('po_number')];
				$production_data_arr[$subcon_val[csf('company_id')]][$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['job_no']=$subcon_val[csf('job_no')];
				$production_data_arr[$subcon_val[csf('company_id')]][$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['style']=$subcon_val[csf('cust_style_ref')];
			}

			if($production_data_arr[$subcon_val[csf('company_id')]][$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['order_id']!="")
			{
				$production_data_arr[$subcon_val[csf('company_id')]][$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['order_id'].=",".$subcon_val[csf('order_id')]."**".$subcon_val[csf('gmts_item_id')];
			}
			else
			{
				$production_data_arr[$subcon_val[csf('company_id')]][$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['order_id'].=$subcon_val[csf('order_id')]."**".$subcon_val[csf('gmts_item_id')];
			}
			$subcon_order_smv[$subcon_val[csf('order_id')]]=$subcon_val[csf('smv')];
			$production_data_arr[$subcon_val[csf('company_id')]][$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['quantity']+=$subcon_val[csf('good_qnty')];

		 	$line_start=$line_number_arr[csf('company_id')][$val[csf('production_date')]][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]]=$val[csf('sewing_line')];
			 [$val[csf('line_id')]][$val[csf('production_date')]]['prod_start_time']	;
		 	if($line_start!="")
		 	{
				$line_start_hour=substr($line_start,0,2);
				if(substr($line_start_hour,0,1)==0)  $line_start_hour=substr($line_start_hour,1,1);
		 	}
			else
		 	{
				$line_start_hour=$hour;
		 	}
			for($h=$hour;$h<=$last_hour;$h++)
			{
				$prod_hour="prod_hour".substr($start_hour_arr[$h],0,2)."";
				$production_data_arr[$subcon_val[csf('company_id')]][$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$prod_hour]+=$subcon_val[csf($prod_hour)];
				if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
				{
					 if( $h>=$line_start_hour && $h<=$actual_time)
					 {
					 $production_po_data_arr[$subcon_val[csf('company_id')]][$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]][$subcon_val[csf('gmts_item_id')]]+=$val[csf($prod_hour)];	                 }
				}
				if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
				{
					$production_po_data_arr[$subcon_val[csf('company_id')]][$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]][$subcon_val[csf('gmts_item_id')]]+=$val[csf($prod_hour)];	            }
			 }
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
			{
				if( $h>=$line_start_hour && $h<=$actual_time)
				{
					$production_po_data_arr[$subcon_val[csf('company_id')]][$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]][$subcon_val[csf('gmts_item_id')]]+=$val[csf('prod_hour23')];
				}
			}
			else
			{
				$production_po_data_arr[$subcon_val[csf('company_id')]][$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]][$subcon_val[csf('gmts_item_id')]]+=$val[csf('prod_hour23')];
			}
			$production_data_arr[$subcon_val[csf('company_id')]][$subcon_val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('line_id')]]['prod_hour23']+=$val[csf('prod_hour23')];
		}
		//echo "<pre>"; print_r($production_data_arr);echo"</pre>";
		//For Summary Report New Add No Prodcut
		$cbo_no_prod_type=1;
		if($cbo_no_prod_type==1)
		{

		/* =============================================================================================/
		/									No Production line Start									/
		/============================================================================================= */
		$prod_reso_arr=return_library_array( "SELECT id, line_number from prod_resource_mst",'id','line_number');
		$sql_active_line=sql_select("SELECT sewing_line,sum(production_quantity) as total_production from pro_garments_production_mst  where production_date between $txt_date_from and $txt_date_to and production_type=5 and status_active=1 and is_deleted=0 and prod_reso_allo=1 group by  sewing_line");
		//$actual_line_arr=array();
		foreach($sql_active_line as $inf)
		{
			if(str_replace("","",$inf[csf('sewing_line')])!="")
			{
				//if(str_replace("","",$actual_line_arr)!="") $actual_line_arr.=",";
				//$actual_line_arr.="'".$inf[csf('sewing_line')]."'";
			}
		}
		//echo $actual_line_arr;die;
		$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name in($comapny_id) and variable_list=23 and is_deleted=0 and status_active=1");
		$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
		if(str_replace("'","",$cbo_location_id)==0)
		{
		$location_cond="";
		}
		else
		{
		$location=" and a.location_id in(".str_replace("'","",$cbo_location_id).")";
		}

		if(str_replace("'","",$cbo_floor_id)==0) $floor=""; else $floor="and a.floor_id=".str_replace("'","",$cbo_floor_id)."";
		$lin_ids=str_replace("'","",$hidden_line_id);
		$res_line_cond=rtrim($reso_line_ids,",");

			$dataArray_sum=("SELECT a.id,a.company_id, a.floor_id,1 as prod_reso_allo,2 as type_line, a.line_number as line_no, b.man_power, b.operator,b.pr_date, b.helper, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type,d.prod_start_time from  prod_resource_dtls b,prod_resource_dtls_time d,prod_resource_mst a  where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id in($comapny_id) and b.pr_date between $txt_date_from and $txt_date_to and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0  and a.line_number like '%$lin_ids%'  $location  $floor  group by a.id,a.company_id, a.floor_id, a.line_number, d.prod_start_time,b.man_power, b.operator, b.helper,b.pr_date, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type order by a.floor_id");
		//echo  $dataArray_sum;die;
			$no_prod_line_arr=array();
			$sql_data=sql_select($dataArray_sum);
			foreach( $sql_data as $row)
			{

			$sewing_line_id=$row[csf('line_no')];

			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id]=$slNo;
			}
			else $slNo=$lineSerialArr[$sewing_line_id];

				$production_serial_arr[$row[csf('company_id')]][$row[csf('pr_date')]][$row[csf('floor_id')]][$slNo][$row[csf('id')]]=$row[csf('id')];
				$production_data_arr[$row[csf('company_id')]][$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['type_line']=$row[csf('type_line')];
				$production_data_arr[$row[csf('company_id')]][$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['prod_reso_allo']=$row[csf('prod_reso_allo')];
				$production_data_arr[$row[csf('company_id')]][$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['man_power']=$row[csf('man_power')];
				$production_data_arr[$row[csf('company_id')]][$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['operator']=$row[csf('operator')];
				$production_data_arr[$row[csf('company_id')]][$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['helper']=$row[csf('helper')];
				$production_data_arr[$row[csf('company_id')]][$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['working_hour']=$row[csf('working_hour')];
				$production_data_arr[$row[csf('company_id')]][$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['terget_hour']=$row[csf('target_per_hour')];
				$production_data_arr[$row[csf('company_id')]][$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['total_line_hour']=$row[csf('man_power')]*$row[csf('working_hour')];
				$production_data_arr[$row[csf('company_id')]][$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['smv_adjust']=$row[csf('smv_adjust')];
				$production_data_arr[$row[csf('company_id')]][$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['smv_adjust_type']=$row[csf('smv_adjust_type')];
				$production_data_arr[$row[csf('company_id')]][$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['prod_start_time']=$row[csf('prod_start_time')];
			}
			$dataArray_sql_cap=sql_select("SELECT  a.floor_id, a.line_number as line_no,b.pr_date,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id  and a.company_id in($comapny_id) and b.pr_date between $txt_date_from and $txt_date_to  and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  group by a.id,a.line_number,b.pr_date, a.floor_id, a.line_number, b.man_power, b.operator,c.capacity, b.helper, b.working_hour,b.target_per_hour order by a.floor_id");

			//$prod_resource_array_summary=array();
			foreach( $dataArray_sql_cap as $row)
			{
				$production_data_arr[$row[csf('company_id')]][$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('line_no')]]['capacity']=$row[csf('capacity')];
			}

		} //End

		//echo "<pre>"; print_r($production_serial_arr);die;
		$produce_minit_arr=array();
		$newdate=date("d-M-Y",strtotime(str_replace("'","",$newdate)));
		foreach($production_target_min_arr as $company_key => $company_val)
		{
			foreach($company_val as $pdate => $date_data)
			{
				foreach($date_data as $po_id=>$po_val)
				{
					foreach($po_val as $item_id=> $row)
					{
						$pdate=date("d-M-Y",strtotime(str_replace("'","",$pdate)));
						$produce_minit_arr[$company_key][$pdate] +=$row['quantity']*$item_smv_array[$po_id][$item_id];

					}

				}


			}
		}
		// echo"<pre>" ;print_r($produce_minit_arr); die;

		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from =1 and ENTRY_FORM=654");
		oci_commit($con);
		disconnect($con);

		$avable_min=0;
		$today_product=0;
	    $floor_name="";
	    $floor_man_power=0;
		$floor_operator=$floor_produc_min=0;
		$floor_smv=$floor_row=$floor_helper=$floor_tgt_h=$floor_days_run=$floor_working_hour=$line_floor_production=$floor_today_product=$floor_avale_minute=0;
		$total_operator=$total_helper=$gnd_hit_rate=0;
	    $total_smv=$total_terget=$grand_total_product=$gnd_line_effi=0;
	    $total_man_power=$gnd_avable_min=$gnd_product_min=0;
		$item_smv=$item_smv_total=$line_efficiency=$days_run=$days_active=$total_working_hour=$gnd_total_tgt_h=$total_capacity=0;
		$j=1;

		$line_number_check_arr=array();
		$smv_for_item="";
		$total_production=array();
		$floor_production=array();
	    $line_floor_production=0;
	    $line_total_production=0; $gnd_total_fob_val=0; $gnd_final_total_fob_val=0;

	    foreach($production_serial_arr as $company_key=>$company_val)
		{
			foreach($company_val as $pdate=>$date_data)
			{
				foreach($date_data as $f_id=>$fname)
				{
					ksort($fname);
					foreach($fname as $sl=>$s_data)
					{
						foreach($s_data as $l_id=>$ldata)
						{
							$po_value=$production_data_arr[$company_key][$pdate][$f_id][$ldata]['po_number'];

							if($po_value)
							{
								//$item_ids=$production_data_arr[$f_id][$ldata]['item_number_id'];
								$germents_item=array_unique(explode('****',$production_data_arr[$company_key][$pdate][$f_id][$ldata]['item_number_id']));

								$buyer_neme_all=array_unique(explode(',',$production_data_arr[$company_key][$pdate][$f_id][$ldata]['buyer_name']));
								// $poIdsArr=array_unique(explode(',',$production_data_arr[$company_key][$pdate][$f_id][$ldata]['po_id']));
								$buyer_name="";
								foreach($buyer_neme_all as $buy)
								{
									if($buyer_name!='') $buyer_name.=',';
									$buyer_name.=$buyerArr[$buy];
								}
								// foreach($poIdsArr as $poid)
								// {

								// }
								$garment_itemname='';
								$active_days='';
								$item_smv="";$item_ids='';
								$smv_for_item="";
								$produce_minit="";
								$order_no_total="";
								$efficiency_min=0;
								$tot_po_qty=0;$fob_val=0;
								foreach($germents_item as $g_val)
								{
									$po_garment_item=explode('**',$g_val);
									if($garment_itemname!='') $garment_itemname.=',';
									$garment_itemname.=$garments_item[$po_garment_item[1]];
									if($item_ids=='') $item_ids=$po_garment_item[1];else $item_ids.=",".$po_garment_item[1];
									if($active_days=="")$active_days=$active_days_arr_powise[$company_key][$po_garment_item[0]][$po_garment_item[1]];
									else $active_days.=','.$active_days_arr_powise[$company_key][$po_garment_item[0]][$po_garment_item[1]];


									//echo $item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt'].'<br>';
									$tot_po_qty+=$item_po_array[$company_key][$po_garment_item[0]][$po_garment_item[1]]['qty'];
									$tot_po_amt+=$item_po_array[$company_key][$po_garment_item[0]][$po_garment_item[1]]['amt'];
									if($item_smv!='') $item_smv.='/';
									//echo $po_garment_item[0].'='.$po_garment_item[1];

									$item_smv.=$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
									if($order_no_total!="") $order_no_total.=",";
									$order_no_total.=$po_garment_item[0];
									if($smv_for_item!="") $smv_for_item.="****".$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
									else
									$smv_for_item=$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];

									$produce_minit+=$production_po_data_arr[$company_key][$pdate][$f_id][$l_id][$po_garment_item[0]][$po_garment_item[1]]*$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
                                    // echo $produce_minit;
									// echo $po_garment_item[0]."=".$pdate."=".$l_id."=".$production_po_data_arr[$company_key][$pdate][$f_id][$l_id][$po_garment_item[0]][$po_garment_item[1]]."*".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]]."<br>";
									// echo $l_id."=".$company_key.'==>'.$po_garment_item[0].'==>'.$po_garment_item[1].'<br>';
							$fob_rate=$item_po_array[$company_key][$po_garment_item[0]][$po_garment_item[1]]['amt']/$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];
									$prod_qty=$production_data_arr_qty[$company_key][$f_id][$l_id][$po_garment_item[0]][$po_garment_item[1]]['quantity'];
									//echo $prod_qty.'<br>';
									if(is_nan($fob_rate)){ $fob_rate=0; }
									$fob_val+=$prod_qty*$fob_rate;
								}
								//$fob_rate=$tot_po_amt/$tot_po_qty;

								$subcon_po_id=array_unique(explode(',',$production_data_arr[$company_key][$pdate][$f_id][$ldata]['order_id']));
								$subcon_order_id="";
								foreach($subcon_po_id as $sub_val)
								{
									list($poId,$itemId) = explode("**",$sub_val);
									$subcon_po_smv=explode(',',$sub_val);
									if($sub_val!=0)
									{
										if($item_smv!='') $item_smv.='/';
										if($item_smv!='') $item_smv.='/';
										$item_smv.=$subcon_order_smv[$sub_val];
									}
									$produce_minit+=$production_po_data_arr[$company_key][$pdate][$f_id][$l_id][$poId][$itemId]*$subcon_order_smv[$poId];
									if($subcon_order_id!="") $subcon_order_id.=",";
									$subcon_order_id.=$poId;
								}
								if($order_no_total!="")
								{
									$day_run_sql=sql_select("SELECT min(production_date) as min_date from pro_garments_production_mst
									where po_break_down_id in(".$order_no_total.")  and production_type=4");
									foreach($day_run_sql as $row_run)
									{
									$sewing_day=$row_run[csf('min_date')];
									}
									if($sewing_day!="")
									{
									$days_run=datediff("d",$sewing_day,$pdate);
									}
									else  $days_run=0;
								}
								$type_line=$production_data_arr[$company_key][$pdate][$f_id][$ldata]['type_line'];
								$prod_reso_allo=$production_data_arr[$company_key][$pdate][$f_id][$ldata]['prod_reso_allo'];
								/*if($type_line==2)
								{
									$sewing_line='';
									if($production_data_arr[$f_id][$ldata]['prod_reso_allo']==1)
									{
										$line_number='';
										$line_number=explode(",",$ldata);
										foreach($line_data as $lin_id)
										{
											//echo $lin_id.'dd';
											$line_number=explode(",",$prod_reso_arr[$lin_id]);
										}
										foreach($line_number as $val)
										{
											if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
										}
									}
									else $sewing_line=$lineArr[$lin_id];
								}
								else
								{*/
									$sewing_line='';
									if($production_data_arr[$company_key][$pdate][$f_id][$ldata]['prod_reso_allo']==1)
									{
										$line_number=explode(",",$prod_reso_arr[$ldata]);
										foreach($line_number as $val)
										{
											if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
										}
									}
									else $sewing_line=$lineArr[$ldata];
								//}

								/*$sewing_line='';
								if($production_data_arr[$f_id][$ldata]['prod_reso_allo']==1)
								{
								$line_number=explode(",",$prod_reso_arr[$ldata]);
								foreach($line_number as $val)
								{
								if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
								}
								}
								else $sewing_line=$lineArr[$ldata];*/


								$lunch_start="";
								$lunch_start=$line_number_arr[$company_key][$ldata][$pdate]['lunch_start_time'];
								$lunch_hour=$start_time_arr[$company_key][$row[1]]['lst'];
								if($lunch_start!="")
								{
								$lunch_start_hour=$lunch_start;
								}
								else
								{
								$lunch_start_hour=$lunch_hour;
								}

								$production_hour=array();
								for($h=$hour;$h<=$last_hour;$h++)
								{
									$prod_hour="prod_hour".substr($line_start_hour_arr[$h],0,2)."";
									$production_hour[$prod_hour]=$production_data_arr[$company_key][$pdate][$f_id][$ldata][$prod_hour];
									$floor_production[$prod_hour]+=$production_data_arr[$company_key][$pdate][$f_id][$ldata][$prod_hour];
									$total_production[$prod_hour]+=$production_data_arr[$company_key][$pdate][$f_id][$ldata][$prod_hour];
								}

								$floor_production['prod_hour24']+=$production_data_arr[$company_key][$pdate][$f_id][$ldata]['prod_hour23'];
								$total_production['prod_hour24']+=$production_data_arr[$company_key][$pdate][$f_id][$ldata]['prod_hour23'];
								$production_hour['prod_hour24']=$production_data_arr[$company_key][$pdate][$f_id][$ldata]['prod_hour23'];
								$line_production_hour=0;
								if(str_replace("'","",$actual_production_date)>str_replace("'","",$actual_date))
								{
									if($type_line==2) //No Profuction Line
									{
										$line_start=$production_data_arr[$company_key][$pdate][$f_id][$l_id]['prod_start_time'];
									}
									else
									{
										$line_start=$line_number_arr[$company_key][$ldata][$pdate]['prod_start_time'];
									}
									if($line_start!="")
									{
										$line_start_hour=substr($line_start,0,2);
										if(substr($line_start_hour,0,1)==0)  $line_start_hour=substr($line_start_hour,1,1);
									}
									else
									{
										$line_start_hour=$hour;
									}
									$actual_time_hour=0;
									$total_eff_hour=0;
									for($lh=$line_start_hour;$lh<=$last_hour;$lh++)
									{
										$bg=$start_hour_arr[$lh];
										if($lh<$actual_time)
										{
										$total_eff_hour=$total_eff_hour+1;;
										$line_hour="prod_hour".substr($bg,0,2)."";
										echo $line_production_hour+=$production_data_arr[$company_key][$pdate][$f_id][$ldata][$line_hour];
										$line_floor_production+=$production_data_arr[$company_key][$pdate][$f_id][$ldata][$line_hour];
										$line_total_production+=$production_data_arr[$company_key][$pdate][$f_id][$ldata][$line_hour];
										$actual_time_hour=$start_hour_arr[$lh+1];
										}
									}
									if($start_hour_arr[$actual_time]>$lunch_start_hour) $total_eff_hour=$total_eff_hour-1;

									if($type_line==2)
									{
										if($total_eff_hour>$production_data_arr[$company_key][$pdate][$f_id][$l_id]['working_hour'])
										{
											$total_eff_hour=$production_data_arr[$company_key][$pdate][$f_id][$l_id]['working_hour'];
										}
									}
									else
									{
										if($total_eff_hour>$prod_resource_array[$company_key][$ldata][$pdate]['working_hour'])
										{
											$total_eff_hour=$prod_resource_array[$company_key][$ldata][$pdate]['working_hour'];
										}
									}

								}
								if(str_replace("'","",$actual_production_date)<=str_replace("'","",$actual_date))
								{
									for($ah=$hour;$ah<=$last_hour;$ah++)
									{
										$prod_hour="prod_hour".substr($start_hour_arr[$ah],0,2)."";
										$line_production_hour+=$production_data_arr[$company_key][$pdate][$f_id][$ldata][$prod_hour];
										$line_floor_production+=$production_data_arr[$company_key][$pdate][$f_id][$ldata][$prod_hour];
										$line_total_production+=$production_data_arr[$company_key][$pdate][$f_id][$ldata][$prod_hour];
									}
									if($type_line==2)
									{
										$total_eff_hour=$production_data_arr[$company_key][$pdate][$f_id][$l_id]['working_hour'];
									}
									else
									{
										$total_eff_hour=$prod_resource_array[$company_key][$ldata][$pdate]['working_hour'];
									}
								}

								if($sewing_day!="")
								{
									$days_run= $diff=datediff("d",$sewing_day,$pdate);
									$days_active= $active_days_arr[$company_key][$f_id][$l_id];
								}
								else
								{
									$days_run=0;
									$days_active=0;
								}

								$current_wo_time=0;
								if($current_date==$search_prod_date)
								{
									$prod_wo_hour=$total_eff_hour;

									if ($dif_time<$prod_wo_hour)//
									{
										$current_wo_time=$dif_hour_min;
										$cla_cur_time=$dif_time;
									}
									else
									{
										$current_wo_time=$prod_wo_hour;
										$cla_cur_time=$prod_wo_hour;
									}
								}
								else
								{
									$current_wo_time=$total_eff_hour;
									$cla_cur_time=$total_eff_hour;
								}
								$total_adjustment=0;
								if($type_line==2) //No Production Line
								{
									$smv_adjustmet_type=$production_data_arr[$company_key][$pdate][$f_id][$l_id]['smv_adjust_type'];
									$eff_target=($production_data_arr[$company_key][$pdate][$f_id][$l_id]['terget_hour']*$total_eff_hour);

									if($total_eff_hour>=$production_data_arr[$company_key][$pdate][$f_id][$l_id]['working_hour'])
									{
										if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$production_data_arr[$company_key][$pdate][$f_id][$l_id]['smv_adjust'];
										if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($production_data_arr[$company_key][$pdate][$f_id][$l_id]['smv_adjust'])*(-1);
									}
									$efficiency_min+=$total_adjustment+($production_data_arr[$company_key][$pdate][$f_id][$l_id]['man_power'])*$cla_cur_time*60;
									$line_efficiency=($efficiency_min>0) ? (($produce_minit)*100)/$efficiency_min : 0;
								}
								else
								{
									$smv_adjustmet_type=$prod_resource_array[$company_key][$ldata][$pdate]['smv_adjust_type'];
									$eff_target=($prod_resource_array[$company_key][$ldata][$pdate]['terget_hour']*$total_eff_hour);

									if($total_eff_hour>=$prod_resource_array[$company_key][$ldata][$pdate]['working_hour'])
									{
									if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array[$company_key][$ldata][$pdate]['smv_adjust'];
									if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array[$company_key][$ldata][$pdate]['smv_adjust'])*(-1);
									}

									/*$actual_hours=date("H",time())-$line_start_hour; for metro
									$cur_time=date("H",time());
									if($cur_time>$line_start_hour){
										$actual_hours=$actual_hours-1;
									}
									else
									{
										$actual_hours=$actual_hours	;
									}
									$total_eff_hour_custom=($actual_hours>$total_eff_hour)?$total_eff_hour:$actual_hours;

									$producting_day=strtotime("Y-m-d",$txt_producting_day);

									if($producting_day>$today_date)
									{

										$efficiency_min+=$total_adjustment+($prod_resource_array[$ldata][$pdate]['man_power'])*$total_eff_hour*60;
										//echo $total_eff_hour."_";
									}
									else
									{
										$efficiency_min+=$total_adjustment+($prod_resource_array[$ldata][$pdate]['man_power'])*$total_eff_hour_custom*60;
										//echo $total_eff_hour_custom."_";
									}*/




									$efficiency_min+=$total_adjustment+($prod_resource_array[$company_key][$ldata][$pdate]['man_power'])*$cla_cur_time*60;
									$line_efficiency=($efficiency_min>0) ? (($produce_minit)*100)/$efficiency_min : 0;
								}




								if($type_line==2) //No Production Line
								{
									$man_power=$production_data_arr[$company_key][$pdate][$f_id][$l_id]['man_power'];
									$operator=$production_data_arr[$company_key][$pdate][$f_id][$l_id]['operator'];
									$helper=$production_data_arr[$company_key][$pdate][$f_id][$l_id]['helper'];
									$terget_hour=$production_data_arr[$company_key][$pdate][$f_id][$l_id]['target_hour'];
									$capacity=$production_data_arr[$company_key][$pdate][$f_id][$l_id]['capacity'];
									$working_hour=$production_data_arr[$company_key][$pdate][$f_id][$l_id]['working_hour'];

									$floor_working_hour+=$production_data_arr[$company_key][$pdate][$f_id][$l_id]['working_hour'];
									$eff_target_floor+=$eff_target;
									$floor_today_product+=$today_product;
									$floor_avale_minute+=$efficiency_min;
									$floor_produc_min+=$produce_minit;
									$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
									$floor_capacity+=$production_data_arr[$company_key][$pdate][$f_id][$l_id]['capacity'];
									$floor_helper+=$production_data_arr[$company_key][$pdate][$ldata][$pdate]['helper'];
									$floor_man_power+=$production_data_arr[$company_key][$pdate][$f_id][$l_id]['man_power'];
									$floor_operator+=$production_data_arr[$company_key][$pdate][$f_id][$l_id]['operator'];
									$total_operator+=$production_data_arr[$company_key][$pdate][$f_id][$l_id]['operator'];
									$total_man_power+=$production_data_arr[$company_key][$pdate][$f_id][$l_id]['man_power'];
									$total_helper+=$production_data_arr[$company_key][$pdate][$f_id][$l_id]['helper'];
									$total_capacity+=$production_data_arr[$company_key][$pdate][$f_id][$l_id]['capacity'];
									$floor_tgt_h+=$production_data_arr[$company_key][$pdate][$f_id][$l_id]['target_hour'];
									$total_working_hour+=$production_data_arr[$company_key][$pdate][$f_id][$l_id]['working_hour'];
									$gnd_total_tgt_h+=$production_data_arr[$company_key][$pdate][$f_id][$l_id]['target_hour'];
									$total_terget+=$eff_target;
									$grand_total_product+=$today_product;
									$gnd_avable_min+=$efficiency_min;
									$gnd_product_min+=$produce_minit;

									$gnd_total_fob_val+=$fob_val;
									$gnd_final_total_fob_val+=$fob_val;
								}
								else
								{
									$man_power=$prod_resource_array[$company_key][$ldata][$pdate]['man_power'];
									$operator=$prod_resource_array[$company_key][$ldata][$pdate]['operator'];
									$helper=$prod_resource_array[$company_key][$ldata][$pdate]['helper'];
									$terget_hour=$prod_resource_array[$company_key][$ldata][$pdate]['terget_hour'];
									$capacity=$prod_resource_array[$company_key][$ldata][$pdate]['capacity'];
									$working_hour=$prod_resource_array[$company_key][$ldata][$pdate]['working_hour'];

									$floor_capacity+=$prod_resource_array[$company_key][$ldata][$pdate]['capacity'];
									$floor_man_power+=$prod_resource_array[$company_key][$ldata][$pdate]['man_power'];
									$floor_operator+=$prod_resource_array[$company_key][$ldata][$pdate]['operator'];
									$floor_helper+=$prod_resource_array[$company_key][$ldata][$pdate]['helper'];
									$floor_tgt_h+=$prod_resource_array[$company_key][$ldata][$pdate]['terget_hour'];
									$floor_working_hour+=$prod_resource_array[$company_key][$ldata][$pdate]['working_hour'];
									$eff_target_floor+=$eff_target;
									$floor_today_product+=$today_product;
									$floor_avale_minute+=$efficiency_min;
									$floor_produc_min+=$produce_minit;
									$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;

									$total_operator+=$prod_resource_array[$company_key][$ldata][$pdate]['operator'];
									$total_man_power+=$prod_resource_array[$company_key][$ldata][$pdate]['man_power'];
									$total_helper+=$prod_resource_array[$company_key][$company_key][$ldata][$pdate]['helper'];
									$total_capacity+=$prod_resource_array[$company_key][$ldata][$pdate]['capacity'];
									$total_working_hour+=$prod_resource_array[$company_key][$ldata][$pdate]['working_hour'];
									$gnd_total_tgt_h+=$prod_resource_array[$company_key][$ldata][$pdate]['terget_hour'];
									$total_terget+=$eff_target;
									$grand_total_product+=$today_product;
									$gnd_avable_min+=$efficiency_min;
									$gnd_product_min+=$produce_minit;
									$gnd_total_fob_val+=$fob_val;
									$gnd_final_total_fob_val+=$fob_val;

								}
								$po_id=rtrim($production_data_arr[$company_key][$pdate][$f_id][$ldata]['po_id'],',');
								$po_id=array_unique(explode(",",$po_id));
								$style=rtrim($production_data_arr[$company_key][$pdate][$f_id][$ldata]['style']);
								$style=implode(",",array_unique(explode(",",$style)));

								$cbo_get_upto=str_replace("'","",$cbo_get_upto);
								$txt_parcentage=str_replace("'","",$txt_parcentage);

								$floor_name=$floorArr[$f_id];
								$floor_smv+=$item_smv;

								$floor_days_run+=$days_run;
								$floor_days_active+=$days_active;

								$po_id=$production_data_arr[$company_key][$pdate][$f_id][$ldata]['po_id'];//$item_ids//$subcon_order_id
								$styles=explode(",",$style);
								$style_button='';//

								$as_on_current_hour_target=0; $as_on_current_hour_variance=0;
								$as_on_current_hour_target=$terget_hour*$cla_cur_time;
								$as_on_current_hour_variance=$line_production_hour-$as_on_current_hour_target;


								// echo $line_efficiency;echo "<br>";

								// number_format($produce_minit,0)
								// ($line_production_hour/$eff_target)*100

								// number_format($line_efficiency,2)
								$resurce_data_array[$company_key][date('d-M-Y',strtotime($pdate))]['target'] += $eff_target;//$terget_hour;
								$resurce_data_array[$company_key][date('d-M-Y',strtotime($pdate))]['production'] += $line_production_hour;
								$resurce_data_array[$company_key][date('d-M-Y',strtotime($pdate))]['traget_qty_achieved'] += ($eff_target>0) ? ($line_production_hour/$eff_target)*100 : 0;

								$resurce_data_array[$company_key][date('d-M-Y',strtotime($pdate))]['man_power'] += $man_power;
								$resurce_data_array[$company_key][date('d-M-Y',strtotime($pdate))]['prod_hours'] += $prod_hours;
								$resurce_data_array[$company_key][date('d-M-Y',strtotime($pdate))]['available_minit'] += $efficiency_min;
								$resurce_data_array[$company_key][date('d-M-Y',strtotime($pdate))]['target_min'] += $target_min;
								$resurce_data_array[$company_key][date('d-M-Y',strtotime($pdate))]['produce_minit'] += $produce_minit;
								//echo $produce_minit;

								$resurce_data_array[$company_key][date('d-M-Y',strtotime($pdate))]['line_efficiency'] += $line_efficiency;

								$resurce_data_array[$company_key][date('d-M-Y',strtotime($pdate))]['style_change'] += $style_change;
								$resurce_data_array[$company_key][date('d-M-Y',strtotime($pdate))]['target_effi'] += $target_effi;
								$resurce_data_array[$company_key][date('d-M-Y',strtotime($pdate))]['achive_effi'] += $achive_effi;
								$resurce_data_array[$company_key][date('d-M-Y',strtotime($pdate))]['cm_earn'] += $ttl_cm;
								$resurce_data_array[$company_key][date('d-M-Y',strtotime($pdate))]['fob_earn'] += $ttl_fob_val;

							}
						}
					}
				}
					//echo "ovi882";	die;

			}

		}



		// echo "<pre>";print_r($resurce_data_array);die();

		/* ==========================================================================================/
		/										production  data									 /
		/ ========================================================================================= */

		$sql="SELECT a.id,a.serving_company,a.production_type,a.floor_id, a.production_date,a.embel_name, b.production_qnty as production_qnty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.production_type in (1,2,3,4,5,8,9,11,80) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.floor_id!=0 $cbo_company_cond  $production_date order by a.production_date";


		//echo $sql; die();
		$sql_dtls = sql_select($sql);
		$floor_qnty = array();
		$floor_wise_total = array();
		$prod_floor_array = array();
		$wash_qty_array = array();

		$sewing_line_array = array();

		foreach ( $sql_dtls as $row )
		{
			if($row[csf('prod_reso_allo')]==1)
			{
				$resource_line= explode(",", $prod_reso_arr[$row[csf('sewing_line')]]);
				$line_name='';
    			foreach($resource_line as $actual_line)
    			{
    				$line_name.= ($line_name=='') ? $lineArr[$actual_line] : ", ".$lineArr[$actual_line];
    			}
			}
			else
			{
				$line_name=$lineArr[$row[csf('sewing_line')]];
			}

			// $floor_qnty[change_date_format($row[csf("production_date")],'','',1)][$row[csf("floor_id")]][$row[csf("production_type")]] += $row[csf("production_qnty")];
			$wash_qty_array[$row['SERVING_COMPANY']][change_date_format($row[csf("production_date")],'','',1)][$row[csf("production_type")]][$row[csf("embel_name")]] +=$row[csf("production_qnty")];


			// echo"<pre>";
			// print_r($wash_qty_array);die;
			if($row[csf("production_type")]==11)
			{
				$floor_id = 0;
				$prod_type = 0;
				if($row[csf("production_type")]==11)
				{
					if($row[csf("floor_id")]==624)
					{
						$floor_id= 669;
					}
					else
					{
						$floor_id= 331;
					}
					$prod_type = 80;
				}
				$prod_floor_array[$row['SERVING_COMPANY']][$prod_type][$floor_id] = $floor_id;
				$floor_wise_total[$row['SERVING_COMPANY']][$floor_id][$prod_type] += $row[csf("production_qnty")];
				$floor_qnty[$row['SERVING_COMPANY']][change_date_format($row[csf("production_date")],'','',1)][$floor_id][$prod_type] += $row[csf("production_qnty")];
			}
			else
			{
				$prod_floor_array[$row['SERVING_COMPANY']][$row[csf("production_type")]][$row[csf("floor_id")]] = $row[csf("floor_id")];
				$floor_wise_total[$row['SERVING_COMPANY']][$row[csf("floor_id")]][$row[csf("production_type")]] += $row[csf("production_qnty")];
				$floor_qnty[$row['SERVING_COMPANY']][change_date_format($row[csf("production_date")],'','',1)][$row[csf("floor_id")]][$row[csf("production_type")]] += $row[csf("production_qnty")];
			}

			$sewing_line_array[$row['SERVING_COMPANY']][change_date_format($row[csf("production_date")],'','',1)][$row[csf("production_type")]] .= $line_name.",";


		}
		//echo "<pre>"; print_r($prod_floor_array);die();

		// ================================ cutting delivery data ============================
		$delivery_date = str_replace("a.production_date", "b.cut_delivery_date", $production_date);
		$delivery_location = str_replace("a.location", "a.location_id", $location_id);
		$sqlDelv = "SELECT a.COMPANY_ID,b.CUT_DELIVERY_DATE,b.CUT_DELIVERY_QNTY from pro_cut_delivery_mst a,pro_cut_delivery_order_dtls b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $cbo_company_cond  $delivery_date";
		//echo $sqlDelv;die();
		$delvRes = sql_select($sqlDelv);
		$delivery_to_input_qty = array();
		foreach ($delvRes as $val)
		{
			$delivery_to_input_qty[$val['COMPANY_ID']][date('d-M-Y',strtotime($val['CUT_DELIVERY_DATE']))] += $val['CUT_DELIVERY_QNTY'];
		}
		//echo "<pre>"; print_r($delivery_to_input_qty);die();


		// ================================ ex-factory data ============================
		$delivery_date = str_replace("a.production_date", "a.delivery_date", $production_date);
		$delivery_location = str_replace("a.location", "a.delivery_location_id", $location_id);
		$sqlEx = "SELECT a.COMPANY_ID, a.DELIVERY_FLOOR_ID,a.DELIVERY_DATE,b.EX_FACTORY_QNTY from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $cbo_company_cond2  $delivery_date";
		// echo $sqlEx;die();
		$exRes = sql_select($sqlEx);
		$ex_factory_qty = array();
		foreach ($exRes as $val)
		{
			$ex_factory_qty[$val['COMPANY_ID']][date('d-M-Y',strtotime($val['DELIVERY_DATE']))] += $val['EX_FACTORY_QNTY'];
		}
		// echo "<pre>"; print_r($ex_factory_qty);die();

		// ================================ buyer inspection data ============================
		$inspection_date = str_replace("a.production_date", "a.inspection_date", $production_date);
		$sql_cond="";
		 if($cbo_company)
		 {
			$sql_cond.=" and a.inspection_company in ($cbo_company)";

		 }
		 if($cbo_location)
		 {
			$sql_cond.=" and a.working_location in ($cbo_location)";

		 }
		$sql = " SELECT a.INSPECTION_COMPANY,a.INSPECTION_STATUS,a.INSPECTION_DATE,a.INSPECTION_QNTY from pro_buyer_inspection a where a.status_active=1 and a.is_deleted=0 and a.inspection_level=3 $working_company  $working_location $inspection_date and a.inspection_status in(1,3)";;
		 //echo $sql;die();
		$res = sql_select($sql);
		$buyer_insp_qty = array();
		foreach ($res as $val)
		{
			$buyer_insp_qty['INSPECTION_COMPANY'][date('d-M-Y',strtotime($val['INSPECTION_DATE']))][$val['INSPECTION_STATUS']] += $val['INSPECTION_QNTY'];
		}
		// echo "<pre>"; print_r($buyer_insp_qty);die();


		ob_start();

		?>
		<fieldset>

					<?
					$company_wise_sum_array=array();
					$compnay_name_arr=explode(",",str_replace("'","",$cbo_company_id));
					foreach($compnay_name_arr as $company_key=> $company_val)
					{
						$count_data=count($prod_floor_array[$company_val][1])+count($prod_floor_array[$company_val][5])+count($prod_floor_array[$company_val][4])+count($prod_floor_array[$company_val][8])+count($prod_floor_array[$company_val][80]);
						// echo $count_data;die;

						 //$count_hd=count($sql_floor)+1;
						$count_hd=count($prod_floor_array[$company_val][1])+count($prod_floor_array[$company_val][5])+count($prod_floor_array[$company_val][4])+count($prod_floor_array[$company_val][8])+count($prod_floor_array[$company_val][80]);

						$width_hd=$count_hd*80;
						$count_cutting=count($prod_floor_array[$company_val][1])+1;
						$width_cutting=$count_cutting*80;

						$count_sewing_in=count($prod_floor_array[$company_val][4])+1;
						$width_sewing_in=$count_sewing*80;

						$count_sewing=count($prod_floor_array[$company_val][5])+1;
						$width_sewing=$count_sewing*80;




						$count_finishing=count($prod_floor_array[$company_val][8])+1;
						$width_finishing=$count_finishing*80;

						$count_poly=count($prod_floor_array[$company_val][80])+1;
						$width_poly=$count_poly*80;

						$table_width=1200+(($count_data+4)*80);
                       ?>
							<table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="caption" align="center">
					<tr>
					<td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:18px">

					Company Name: <?= $company_library[ $company_val]; ?>

						</strong>
					</td>
					</tr>
					<tr>
					<td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:14px"><? echo $report_title; ?></strong></td>
					</tr>
					<tr>
					<td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:14px"> <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
					</tr>
				</table>

				<div style="height:auto;">

					<table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left">
						<thead>
							<tr>
								<th width="80" rowspan="2" >Date</th>
								<th width="<? echo $width_cutting; ?>" colspan="<? echo $count_cutting; ?>">Cutting Output</th>
								<th width="<? echo $width_sewing_in; ?>" colspan="<? echo $count_sewing_in; ?>">Sewing Input</th>
								<th width="<? echo $width_sewing; ?>" colspan="<? echo $count_sewing; ?>">Sewing Output</th>
								<th width="160" colspan="2">Print</th>
								<th width="160" colspan="2">Embroidery</th>
								<th width="160" colspan="2">Wash</th>
								<th width="<? echo $width_poly; ?>" colspan="<? echo $count_poly; ?>">Gmts. Finishing</th>
								<th width="<? echo $width_finishing; ?>" colspan="<? echo $count_finishing; ?>">Packing</th>
								<th width="80" rowspan="2" title="Produce min/Available min*100">Avg. Efficiency (%)</th>
								<th width="80" rowspan="2">Produced Minute</th>
								<th width="80" rowspan="2">Available Minutes</th>
								<th width="80" rowspan="2" title="Production/Target*100">Target Qty Achieved (%)</th>
								<th width="80" rowspan="2">Buyer Insp. Passed Qty</th>
								<th width="80" rowspan="2">Buyer Insp. Failed Qty</th>
								<th width="80" rowspan="2">Ex-Factory Qty</th>
							</tr>
							<tr>
								<?
								foreach ( $prod_floor_array[$company_val][1] as $rows)
								{

										?>
										<th width="80" title="<?=$rows;?>"><? echo $floor_arr[$rows]; ?></th>
										<?

								}
								?>
								<th width="80" colspan="">Total</th>

								<?
								foreach ($prod_floor_array[$company_val][4] as $rows)
								{

										?>
										<th width="80" title="<?=$rows;?>"><? echo $floor_arr[$rows]; ?></th>
										<?

								}
								?>
								<th width="80" colspan="">Total</th>
								<?
								foreach ($prod_floor_array[$company_val][5] as $rows)
								{

										?>
										<th width="80" title="<?=$rows;?>"><? echo $floor_arr[$rows]; ?></th>
										<?

								}
								?>

								<th width="80" colspan="">Total</th>
								<th width="80">Sent</th>
								<th width="80">Receive</th>

								<th width="80">Sent</th>
								<th width="80">Receive</th>

								<th width="80">Sent</th>
								<th width="80">Receive</th>
								<?
								foreach ( $prod_floor_array[$company_val][80] as $rows)
								{

										?>
										<th width="80" title="<?=$rows;?>"><? echo $floor_arr[$rows]; ?></th>
										<?

								}
								?>

								<th width="80" colspan="">Total</th>
								<?

									foreach ($prod_floor_array[$company_val][8] as $rows)
									{

											?>
											<th width="80" title="<?=$rows;?>"><? echo $floor_arr[$rows]; ?></th>
											<?

									}
									?>

								<th width="80" colspan="">Total</th>

							</tr>
						</thead>
					</table>
				</div>
				<? //echo $datediff; die('kakku');?>
				<div align="left" style="max-height:300px;width: <?= $table_width+20;?>px" id="scroll_body">
					<table align="left" cellspacing="0" cellpadding="0" width="<? echo $table_width; ?>"  border="1" rules="all" class="rpt_table" >
						<?

							$r=0;
							$n=0;

							$unit_wise_total_array=array();
							$value_define=array();
							$value_define2=array();
							$value_define3=array();
							$value_define4=array();
							$value_define_grand_arr=array();


							$wash_issue_total = 0;
							$wash_rcv_total = 0;
							$ex_qty_total = 0;
							$produce_minit_total = 0;
							$tot_efficiency = 0;
							$tot_efficiency_count = 0;
							$tot_target_achive = 0;
							//$tot_target_achive_count = 0;
							$gr_tot_avg_efficiency=0;
							$gr_tot_pro_min = 0;
							$gr_tot_avlable_min = 0;
							$gr_tot_target_aciv=0;

							$gr_tot_target = 0;
							$gr_tot_production = 0;

							for($j=0;$j<$datediff;$j++)
							{
								if ($j%2==0)
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";

								$date_data_array=array();
								$newdate =add_date(str_replace("'","",$txt_date_from),$j);
								$newdate=date("d-M-Y",strtotime(str_replace("'","",$newdate)));
								$date_data_array[$j]=$newdate;
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>">
									<td width="80" align="center"><? echo change_date_format($newdate); ?></td>
									<?
									$site_tot_qnty=0;
									foreach ( $prod_floor_array[$company_val][1]  as $rows)
									{

											// echo $newdate."==".$rows."<br>";
											?>
												<td width="80" align="right"><p><?  echo number_format($floor_qnty[$company_val][$date_data_array[$j]][$rows][1],0); ?></p></td>
											<?
											$company_wise_sum_array[$company_val]['cutting_qty']+=$floor_qnty[$company_val][$date_data_array[$j]][$rows][1];

											$site_tot_qnty+=$floor_qnty[$company_val][$date_data_array[$j]][$rows][1];
											if($floor_qnty[$company_val][$date_data_array[$j]][$rows][1]!="")
											{
												$value_define[$company_val][1][$rows] +=1;
											}


								}

									?>
									<td width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>
									<?
									$site_tot_qnty=0;
									foreach ( $prod_floor_array [$company_val][4] as $rows)
									{


											?>
												<td width="80" align="right"><?  echo number_format($floor_qnty[$company_val][$date_data_array[$j]][$rows][4],0); ?></td>
											<?
											$company_wise_sum_array[$company_val]['sewing_input']+=$floor_qnty[$company_val][$date_data_array[$j]][$rows][4];
											$site_tot_qnty+=$floor_qnty[$company_val][$date_data_array[$j]][$rows][4];
											if($floor_qnty[$company_val][$date_data_array[$j]][$rows][4]!="")
											{
												$value_define[$company_val][4][$rows] +=1;
											}
											if($site_tot_qnty!="")
											{
												$value_define_grand_arr[$company_val][4][$rows] +=1;

											}
											// echo $value_define_grand_arr[$company_val][4][$rows];

									}
									?>
									<td width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>


									<?
									$site_tot_qnty=0;
									foreach ( $prod_floor_array [$company_val][5] as $rows)
									{


											?>

												<td width="80" align="right"><?  echo number_format($floor_qnty[$company_val][$date_data_array[$j]][$rows][5],0); ?></td>
											<?
											$company_wise_sum_array[$company_val]['sewing_output']+=$floor_qnty[$company_val][$date_data_array[$j]][$rows][5];

											$site_tot_qnty+=$floor_qnty[$company_val][$date_data_array[$j]][$rows][5];
											if($floor_qnty[$company_val][$date_data_array[$j]][$rows][5]!="")
											{
												$value_define[$company_val][5][$rows] +=1;
											}
											if($site_tot_qnty!="")
											{
												$value_define_grand_arr[$company_val][5][$rows] +=1;

											}

									}
									?>
									<td width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>
									<td width="80" align="right">
									<?
									if($wash_qty_array[$company_val][$date_data_array[$j]][2][1]!="")
									{
										$value_define3[$company_val]['print_iss'] +=1;
									}
									$company_wise_sum_array[$company_val]['print_iss']+=$wash_qty_array[$company_val][$date_data_array[$j]][2][1];
									echo number_format($wash_qty_array[$company_val][$date_data_array[$j]][2][1],0);
									?>

									</td>
									<td width="80" align="right">
									<?
									if($wash_qty_array[$company_val][$date_data_array[$j]][3][1]!="")
									{
										$value_define3[$company_val]['print_rcv'] +=1;
									}
									$company_wise_sum_array[$company_val]['print_rcv']+=$wash_qty_array[$company_val][$date_data_array[$j]][3][1];
									echo number_format($wash_qty_array[$company_val][$date_data_array[$j]][3][1],0);
									?>

									</td>
									<td width="80" align="right">
									<?
									if($wash_qty_array[$company_val][$date_data_array[$j]][2][2]!="")
									{
										$value_define4[$company_val]['emb_iss'] +=1;
									}
									$company_wise_sum_array[$company_val]['emb_iss']+=$wash_qty_array[$company_val][$date_data_array[$j]][2][2];
									echo number_format($wash_qty_array[$company_val][$date_data_array[$j]][2][2],0);
									?>

									</td>
									<td width="80" align="right">
									<?
									if($wash_qty_array[$company_val][$date_data_array[$j]][3][2]!="")
									{
										$value_define4[$company_val]['emb_rcv'] +=1;
									}
									$company_wise_sum_array[$company_val]['emb_rcv']+=$wash_qty_array[$company_val][$date_data_array[$j]][3][2];
									echo number_format($wash_qty_array[$company_val][$date_data_array[$j]][3][2],0);
									?>

									</td>

									<td width="80" align="right">
									<?
									if($wash_qty_array[$company_val][$date_data_array[$j]][2][3]!="")
									{
										$value_define2[$company_val]['wash_iss'] +=1;
									}
									$company_wise_sum_array[$company_val]['wash_iss']+=$wash_qty_array[$company_val][$date_data_array[$j]][2][3];
									echo number_format($wash_qty_array[$date_data_array[$j]][2][3],0);
									?>

									</td>
									<td width="80" align="right">
									<?
									if($wash_qty_array[$company_val][$date_data_array[$j]][3][3]!="")
									{
										$value_define2[$company_val]['wash_rcv'] +=1;
									}
									$company_wise_sum_array[$company_val]['wash_rcv']+=$wash_qty_array[$company_val][$date_data_array[$j]][3][3];
									echo number_format($wash_qty_array[$company_val][$date_data_array[$j]][3][3],0);
									?>

									</td>

									<?
									$woven_finish_site_tot_qnty=0;
									foreach ( $prod_floor_array [$company_val] [80] as $rows )
									{

												if ($rows == 669)
												{
													?>

														<td width="80" align="right">
															<?
																$company_wise_sum_array[$company_val]['gmts_finishing']+=$floor_qnty [$company_val][$date_data_array[$j]][$rows][80]+$floor_qnty[$date_data_array[$j]][624][11];

															echo number_format(($floor_qnty [$company_val][$date_data_array[$j]][$rows][80]+$floor_qnty [$company_val][$date_data_array[$j]][624][11]),0); ?>
														</td>
													<?

													$woven_finish_site_tot_qnty+=$floor_qnty [$company_val][$date_data_array[$j]][$rows][80]+$floor_qnty[$date_data_array[$j]][624][11];
												}
												else if($rows == 331)
												{
													?>
													<td width="80" align="right">
														<?
															$company_wise_sum_array[$company_val]['gmts_finishing']+=$floor_qnty [$company_val][$date_data_array[$j]][$rows][80]+$floor_qnty [$company_val][$date_data_array[$j]][676][11];
														echo number_format(($floor_qnty [$company_val][$date_data_array[$j]][$rows][80]+$floor_qnty [$company_val][$date_data_array[$j]][676][11]),0); ?>
													</td>
													<?

													$woven_finish_site_tot_qnty+=$floor_qnty [$company_val][$date_data_array[$j]][$rows][80]+$floor_qnty [$company_val][$date_data_array[$j]][676][11];
												}
											else
											{
												?>
													<td width="80" align="right">

														<?
														$company_wise_sum_array[$company_val]['gmts_finishing']+=$floor_qnty [$company_val][$date_data_array[$j]][$rows][80];

														echo number_format($floor_qnty [$company_val][$date_data_array[$j]][$rows][80],0); ?>
													</td>
												<?

											}

											if($floor_qnty [$company_val][$date_data_array[$j]][$rows][80]!="")
											{
												$value_define[$company_val][80][$rows] +=1;
											}

									}
									?>
									<td width="80" align="right">
										<strong><? echo number_format($woven_finish_site_tot_qnty,0); ?></strong>
									</td>

									<?
									$site_tot_qnty=0;
									foreach ( $prod_floor_array [$company_val][8]as $rows )
									{

											?>
												<td width="80" align="right">
													<?

													 echo number_format($floor_qnty[$company_val][$date_data_array[$j]][$rows][8],0); ?>
												</td>
											<?
											$company_wise_sum_array[$company_val]['packing']+=$floor_qnty [$company_val][$date_data_array[$j]][$rows][8];
											$site_tot_qnty+=$floor_qnty[$company_val][$date_data_array[$j]][$rows][8];
											if($floor_qnty[$company_val][$date_data_array[$j]][$rows][8]!="")
											{
												$value_define[$company_val][8][$rows] +=1;
											}
									}
									?>
									<td width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>

									<td width="80" align="right">
										<?

										echo $tot_eff=($resurce_data_array[$company_val][$date_data_array[$j]]['available_minit']>0) ? number_format(($resurce_data_array[$company_val][$date_data_array[$j]]['produce_minit']/$resurce_data_array[$company_val][$date_data_array[$j]]['available_minit']*100),2) : 0;

										if($resurce_data_array[$company_val][$date_data_array[$j]]['available_minit']>0)
										{
											$tot_efficiency += $produce_minit_arr[$company_val][$date_data_array[$j]]/$resurce_data_array[$company_val][$date_data_array[$j]]['available_minit']*100;
											$tot_efficiency_count++;
										}
										//  $company_wise_sum_array[$company_val]['avg_efficiency']+=($produce_minit_arr[$company_val][$date_data_array[$j]]/$resurce_data_array[$company_val][$date_data_array[$j]]['available_minit']*100);
									//	echo"<pre>"; print_r($company_wise_sum_array[$company_val]['avg_efficiency']) ;die;
								     	$company_wise_sum_array[$company_val]['avg_efficiency']=$tot_eff;

										 if($tot_eff>0)
									 {
                                        $r+=1;
								 	}

										?>%
									</td>
									<td width="80" align="right"><?

									echo number_format($resurce_data_array[$company_val][$date_data_array[$j]]['produce_minit'],0);

									 $company_wise_sum_array[$company_val]['produce_minit']+=$resurce_data_array[$company_val][$date_data_array[$j]]['produce_minit']

									 ?>

									</td>
									<td width="80" align="right">
										<?
										 $company_wise_sum_array[$company_val]['available_minit']+=$resurce_data_array[$company_val][$date_data_array[$j]]['available_minit'];

									 echo number_format($resurce_data_array[$company_val][$date_data_array[$j]]['available_minit'],0);
									 ?>
									 </td>
									 <td width="80" align="right">
										<?

										echo $tot_target=($resurce_data_array[$company_val][$date_data_array[$j]]['target']>0) ? number_format(($resurce_data_array[$company_val][$date_data_array[$j]]['production']/$resurce_data_array[$company_val][$date_data_array[$j]]['target']*100),2) : 0;
										//echo $resurce_data_array[$company_val][$date_data_array[$j]]['production'];


										$company_wise_sum_array[$company_val]['target'] =$tot_target;
										// $resurce_data_array[$company_val][$date_data_array[$j]]['production']/$resurce_data_array[$company_val][$date_data_array[$j]]['target']*100;

										if($resurce_data_array[$company_val][$date_data_array[$j]]['target']>0)
										{
											$tot_target_achive +=  $resurce_data_array[$company_val][$date_data_array[$j]]['production']/$resurce_data_array[$company_val][$date_data_array[$j]]['target']*100;
											$tot_target_achive_count++;
										}
										if($tot_target>0)
										{
										   $n+=1;
										}
										?>%

									</td>
									<td width="80" align="right">
										<a href="javascript:void(0)" onclick="open_popup('<?=$cbo_company;?>','<?=$cbo_location;?>','<?=$newdate;?>',1,'buyer-insp-popup')">
											<?
											   $company_wise_sum_array[$company_val]['buyer_insp_passed_qty']+=$buyer_insp_qty[$company_val][$date_data_array[$j]][1];
											echo number_format($buyer_insp_qty[$company_val][$date_data_array[$j]][1],0);
											?>
										</a>
									</td>
									<?
									if($buyer_insp_qty[$company_val][$date_data_array[$j]][1]!="")
									{
										$value_define2[$company_val]['bi-pass'] +=1;
									}
									?>


									<td width="80" align="right">
										<a href="javascript:void(0)" onclick="open_popup('<?=$cbo_company;?>','<?=$cbo_location;?>','<?=$newdate;?>',3,'buyer-insp-popup')">
											<?
											$company_wise_sum_array[$company_val]['buyer_insp_failed_qty']+=$buyer_insp_qty[$company_val][$date_data_array[$j]][1];

											echo number_format($buyer_insp_qty[$company_val][$date_data_array[$j]][3],0); ?>
										</a>
									</td>
									<?
									if($buyer_insp_qty[$company_val][$date_data_array[$j]][3]!="")
									{
										$value_define2[$company_val]['bi-faill'] +=1;
									}
									?>

									<td width="80" align="right">
										<a href="javascript:void(0)" onclick="open_popup('<?=$cbo_company;?>','<?=$cbo_location;?>','<?=$newdate;?>',0,'ex-factory-popup')">
											<?
											$company_wise_sum_array[$company_val]['ex_factory']+=$ex_factory_qty[$company_val][$date_data_array[$j]];
											echo number_format($ex_factory_qty[$company_val][$date_data_array[$j]],0); ?>
										</a>
									</td>
									<?
									if($ex_factory_qty[$company_val][$date_data_array[$j]]!="")
									{
										$value_define2[$company_val]['ex'] +=1;
									}
									?>

								</tr>
								<?
								$print_issue_total += $wash_qty_array[$company_val][$date_data_array[$j]][2][1];
								$print_rcv_total += $wash_qty_array[$company_val][$date_data_array[$j]][3][1];
								$emb_issue_total += $wash_qty_array[$company_val][$date_data_array[$j]][2][2];
								$emb_rcv_total += $wash_qty_array[$company_val][$date_data_array[$j]][3][2];
								$wash_issue_total += $wash_qty_array[$company_val][$date_data_array[$j]][2][3];
								$wash_rcv_total += $wash_qty_array[$company_val][$date_data_array[$j]][3][3];
								$cutting_delv_total += $delivery_to_input_qty[$company_val][$date_data_array[$j]];
								$buyer_insp_pass_total += $buyer_insp_qty[$company_val][$date_data_array[$j]][1];
								$buyer_insp_faill_total += $buyer_insp_qty[$date_data_array[$j]][3];
								$ex_qty_total += $ex_factory_qty[$company_val][$date_data_array[$j]];
								//$produce_minit_total += $produce_minit_arr[$company_val][$date_data_array[$j]];
								$available_minit_total += $resurce_data_array[$company_val][$date_data_array[$j]]['available_minit'];

								$gr_tot_pro_min += $resurce_data_array[$company_val][$date_data_array[$j]]['produce_minit'] ;
								$gr_tot_avlable_min += $resurce_data_array[$company_val][$date_data_array[$j]]['available_minit'];
								$gr_tot_avg_efficiency +=$tot_eff;
								$gr_tot_target_aciv += $tot_target;


							}
							?>
							<tfoot>
								<tr bgcolor="#dddddd">
									<th align="center" width="60"><strong>Total : </strong></th>
									<?
									$grand_total_tot=0;

									foreach ( $prod_floor_array[$company_val][1] as $rows )
									{
										?>
											<th width="80" align="right"><strong><? echo number_format($floor_wise_total[$company_val][$rows][1],0); ?></strong></th>
										<?
										$grand_total_tot+=$floor_wise_total[$company_val][$rows][1];
									}

									?>
									<th width="80" align="right"><strong><? echo number_format($grand_total_tot,0); ?></strong></th>

									<?
									$grand_total_tot_sew_in=0;

									foreach ( $prod_floor_array[$company_val][4] as $rows )
									{
										?>
											<th width="80" align="right"><strong><? echo number_format($floor_wise_total[$company_val][$rows][4],0); ?></strong></th>
										<?
										$grand_total_tot_sew_in+=$floor_wise_total[$company_val][$rows][4];
									}

									?>
									<th width="80" align="right"><strong><? echo number_format($grand_total_tot_sew_in,0); ?></strong></th>

									<?
									$grand_total_tot=0;

									foreach ( $prod_floor_array[$company_val][5] as $rows )
									{
										?>
											<th width="80" align="right"><strong><? echo number_format($floor_wise_total[$company_val][$rows][5],0); ?></strong></th>
										<?
										$grand_total_tot_sew_out+=$floor_wise_total[$company_val][$rows][5];
									}

									?>
									<th width="80" align="right"><strong><? echo number_format($grand_total_tot_sew_out,0); ?></strong></th>





									<th width="80" align="right"><strong><? echo number_format($print_issue_total,0); ?></strong></th>
									<th width="80" align="right"><strong><? echo number_format($print_rcv_total,0); ?></strong></th>
									<th width="80" align="right"><strong><? echo number_format($emb_issue_total,0); ?></strong></th>
									<th width="80" align="right"><strong><? echo number_format($emb_rcv_total,0); ?></strong></th>

									<th width="80" align="right"><strong><? echo number_format($wash_issue_total,0); ?></strong></th>
									<th width="80" align="right"><strong><? echo number_format($wash_rcv_total,0); ?></strong></th>

									 <?
									$grand_total_tot=0;
									foreach ( $prod_floor_array[$company_val][80] as $rows )
									{
										if ($rows == 669)
										{
											?>
												<th width="80" align="right"><strong><? echo number_format(($floor_wise_total[$company_val][$rows][80]+$floor_wise_total[$company_val][624][11]),0); ?></strong></th>
											<?
											$grand_total_tot+=$floor_wise_total[$company_val][$rows][80]+$floor_wise_total[624][11];
										}
										else if ($rows == 331)
										{
											?>
												<th width="80" align="right"><strong><? echo number_format(($floor_wise_total[$company_val][$rows][80]+$floor_wise_total[$company_val][676][11]),0); ?></strong></th>
											<?
											$grand_total_tot+=$floor_wise_total[$company_val][$rows][80]+$floor_wise_total[$company_val][676][11];
										}
										else
										{
											?>
												<th width="80" align="right"><strong><? echo number_format($floor_wise_total[$company_val][$rows][80],0); ?></strong></th>
											<?
											$grand_total_tot+=$floor_wise_total[$company_val][$rows][80];
										}
									}

									?>
									<th width="80" align="right"><strong><? echo number_format($grand_total_tot,0); ?></strong></th>

									<?
									$grand_total_tot=0;

									foreach ( $prod_floor_array[$company_val][8] as $rows )
									{
										?>
											<th width="80" align="right"><strong><? echo number_format($floor_wise_total[$company_val][$rows][8],0); ?></strong></th>
										<?
										$grand_total_tot+=$floor_wise_total[$company_val][$rows][8];
									}

									?>
									<th width="80" align="right"><strong><? echo number_format($grand_total_tot,0); ?></strong></th>

									 <th width="80" align="right"><strong><??></strong></th>

									<th width="80" align="right"><strong><?=number_format($gr_tot_pro_min ,2)?></strong></th>
									<th width="80" align="right"><strong><?=number_format($available_minit_total,2)?></strong></th>
									<th width="80" align="right"><strong></strong></th>
									<th width="80" align="right"><strong><? echo number_format($buyer_insp_pass_total,0); ?></strong></th>
									<th width="80" align="right"><strong><? echo number_format($buyer_insp_faill_total,0); ?></strong></th>
									<th width="80" align="right"><strong><? echo number_format($ex_qty_total,0); ?></strong></th>

								</tr>

								<tr bgcolor="#dddddd">
									<th align="center" width="60"><strong>Avg : </strong></th>
									<?
									$avg='';
									$avg_tot=0;
									foreach ( $prod_floor_array[$company_val][1] as $rows )
									{
										$avg=$floor_wise_total[$company_val][$rows][1]/ $value_define[$company_val][1][$rows];
										?>
										<th width="80" align="right"><strong><? echo number_format( $avg,0); ?></strong></th>
										<?
										$avg_tot+=$avg;
									}
									?>
									<th width="80" align="right"><strong><?  echo number_format($avg_tot,0); ?></strong></th>

									<?
									$avg='';
									$avg_tot=0;
									foreach ( $prod_floor_array[$company_val][4] as $rows )
									{
										$avg=$floor_wise_total[$company_val][$rows][4]/ $value_define[$company_val][4][$rows];
										?>
										<th width="80" align="right"><strong><? echo number_format( $avg,0); ?></strong></th>
										<?
										// $avg_tot+=$avg;
									}
									?>

									<th width="80" align="right"><strong><? $avg_sew_in=$grand_total_tot_sew_in/$value_define_grand_arr[$company_val][4][$rows]; echo number_format($avg_sew_in,0); ?></strong></th>

									<?
									$avg='';
									$avg_tot=0;
									foreach ( $prod_floor_array[$company_val][5] as $rows )
									{
										$avg=$floor_wise_total[$company_val][$rows][5]/ $value_define[$company_val][5][$rows];
										?>
										<th width="80" align="right"><strong><? echo number_format( $avg,0); ?></strong></th>
										<?
										$avg_tot+=$avg;
									}
									?>
									<th width="80" align="right"><strong><? $avg_sew_out=$grand_total_tot_sew_out/$value_define_grand_arr[$company_val][5][$rows]; echo number_format($avg_sew_out,0); ?></strong></th>
                                    <th width="80" align="right"><strong>
									<?
									$avg_tot_print_issue = 0;
									$avg_tot_print_issue = $print_issue_total/ $value_define3[$company_val]['print_iss'];
									echo number_format($avg_tot_print_issue,0);

									?>
									</strong></th>
									<th width="80" align="right"><strong>
									<?
									$avg_tot_print_rcv = 0;
									$avg_tot_print_rcv =  $print_rcv_total/ $value_define3[$company_val]['print_rcv'] ;
									echo number_format($avg_tot_print_rcv,0);
									?>
									</strong></th>
									<th width="80" align="right"><strong>
									<?
									$avg_tot_emb_issue = 0;
									$avg_tot_emb_issue =  $emb_issue_total/ $value_define4[$company_val]['emb_iss'];
									echo number_format($avg_tot_emb_issue,0);

									?>

									</strong></th>
									<th width="80" align="right"><strong>
									<?
									$avg_tot_emb_rcv = 0;
									$avg_tot_emb_rcv =  $emb_rcv_total/ $value_define4[$company_val]['emb_rcv'];
									echo number_format($avg_tot_emb_rcv,0);

									?>

									</strong></th>


									<th width="80" align="right"><strong>
									<?
									$avg_tot_wash_issue = 0;
									$avg_tot_wash_issue = ($value_define2[$company_val]['wash_iss']>0) ? $wash_issue_total/ $value_define2[$company_val]['wash_iss'] : 0;
									echo number_format($avg_tot_wash_issue,0);

									?>

									</strong></th>
									<th width="80" align="right"><strong>
									<?
									$avg_tot_wash_rcv = 0;
									$avg_tot_wash_rcv = ($value_define2[$company_val]['wash_rcv']>0) ? $wash_rcv_total / $value_define2[$company_val]['wash_rcv'] : 0;
									echo number_format($avg_tot_wash_rcv,0);

									?>

									</strong></th>

									<?
									$avg='';
									$avg_tot=0;
									foreach ( $prod_floor_array[$company_val][80] as $rows )
									{
										if($rows == 669)
											{
												$avg=($floor_wise_total[$company_val][$rows][80]+$floor_wise_total[$company_val][624][11])/ $value_define[$company_val][80][$rows];
												?>
													<th width="80" align="right"><strong><? echo number_format( $avg,0); ?></strong></th>
												<?
												$avg_tot+=$avg;
											}
										else if( $rows == 331)
											{
												$avg=($floor_wise_total[$company_val][$rows][80]+$floor_wise_total[$company_val][676][11])/ $value_define[$company_val][80][$rows];
												?>
													<th width="80" align="right"><strong><? echo number_format( $avg,0); ?></strong></th>
												<?
												$avg_tot+=$avg;
											}
										else
											{
												$avg=$floor_wise_total[$company_val][$rows][80]/ $value_define[$company_val][80][$rows];
												?>
													<th width="80" align="right"><strong><? echo number_format( $avg,0); ?></strong></th>
												<?
												$avg_tot+=$avg;
											}
									}
									?>
									<th width="80" align="right"><?  echo number_format($avg_tot,0); ?></th>

									<?
									$avg='';
									$avg_tot=0;
									foreach ( $prod_floor_array[$company_val][8] as $rows )
									{
										$avg=$floor_wise_total[$company_val][$rows][8]/ $value_define[$company_val][8][$rows];
										?>
											<th width="80" align="right"><strong><? echo number_format( $avg,0); ?></strong></th>
										<?
										$avg_tot+=$avg;
									}
									?>
									<th width="80" align="right"><?  echo number_format($avg_tot,0); ?></th>

									<th width="80" align="right" title="<?=$gr_tot_pro_min."/".$gr_tot_avlable_min;?>">
										<?  echo fn_number_format(($gr_tot_avg_efficiency/$r),2); ?>%
										<?
										$company_wise_sum_array[$company_val]['avg_efficiency']=number_format(($gr_tot_avg_efficiency/$r),2);
										?>
									</th>
									<th width="80" align="right"><?  //echo number_format($avg_tot,0); ?></th>
									<th width="80" align="right"><?  //echo number_format($avg_tot,0); ?></th>
									<th width="80" align="right" >
										<?  echo number_format(($gr_tot_target_aciv / $n),2); ?>%
										<?
										$company_wise_sum_array[$company_val]['target']=number_format(($gr_tot_target_aciv / $n),2);
										?>
									</th>
									<th width="80" align="right">
									<?
									$avg_tot = 0;
									$avg_tot = ($value_define2[$company_val]['bi-pass']>0) ? $buyer_insp_pass_total / $value_define2[$company_val]['bi-pass'] : 0;
									echo number_format($avg_tot,0);
									?>
									</th>
									<th width="80" align="right">
									<?
									$avg_tot = 0;
									$avg_tot = ($value_define2[$company_val]['bi-faill']>0) ? $buyer_insp_faill_total / $value_define2[$company_val]['bi-faill'] : 0;
									echo number_format($avg_tot,0);
									?>
									</th>
									<th width="80" align="right">
									<?
									$avg_tot = 0;
									$avg_tot = $ex_qty_total/ $value_define2[$company_val]['ex'];
									echo number_format($avg_tot,0);
									?>
									</th>

			                    </tr>


							</tfoot>
						</table>
					<?
				}
				?>
		</div>
		<br>
		<br>

		<div width="1650px" align="center">
				<table width="1800" cellpadding="0" cellspacing="0" id="caption" align="center">

						<tr>
						<td align="center" width="100%" class="form_caption" ><strong style="font-size:20px">Group Summary Report</strong></td>
						</tr>
						<tr>
						<td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:18px"> <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
						</tr>
	             </table>

				<table width="1640" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" >
		            <thead>
		                <tr>
		                    <th width="100" rowspan="2">Company Name</th>
		                    <th width="100" rowspan="2">Cutting Output</th>
							<th width="100" rowspan="2">Sewing Input</th>
		                    <th width="100" rowspan="2">Sewing Output</th>
							<th  colspan="2">Print</th>
							<th colspan="2">Embroidery</th>
		                    <th colspan="2">Wash</th>
		                    <th width="100" rowspan="2">Gmts. Finishing</th>
		                    <th width="100" rowspan="2">Packing</th>
		                    <th width="80" rowspan="2" title="Produce min/Available min*100">Avg. Efficiency (%)</th>
		                    <th width="80" rowspan="2">Produced Minute</th>
		                    <th width="80" rowspan="2">Available Minutes</th>
		                    <th width="80" rowspan="2" title="Production/Target*100">Target Qty Achieved (%)</th>
		                    <th width="80" rowspan="2">Buyer Insp. Passed Qty</th>
		                    <th width="80" rowspan="2">Buyer Insp. Failed Qty</th>
		                    <th width="80" rowspan="2">Ex-Factory Qty</th>
		                </tr>
		               <tr>

							<th width="80" >Print Sent</th>
							<th width="80" >Print Receive</th>
							<th width="80" >Emb.  Sent</th>
							<th width="80" >Emb. Receive</th>
							<th width="80" >Wash Sent</th>
							<th width="80" >Wash Receive</th>
					   </tr>
		            </thead>
		           </table>

				   <div align="center" style="max-height:300px;width:1650px" id="scroll_body">
		          <table align="center" cellspacing="0" cellpadding="0" width="1640"  border="1" rules="all" class="rpt_table">
				   <tbody>
					                <?
									  $company_count=0;
					                 foreach( $company_wise_sum_array as $company_val=>$rows)
									 {
							           $company_count +=1;

											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>">
													<td width="100" align="right"><?=$company_library[$company_val]?></td>
													<td width="100" align="right"><?=number_format($rows['cutting_qty'],2);?></td>
													<td width="100" align="right"><?=number_format($rows['sewing_input'],2);?></td>
													<td width="100" align="right"><?=number_format($rows['sewing_output'],2);?></td>
													<td width="80" align="right"><?=number_format($rows['print_iss'],2);?></td>
													<td width="80" align="right"><?=number_format($rows['print_rcv'],2);?></td>
													<td width="80" align="right"><?=number_format($rows['emb_iss'],2);?></td>
													<td width="80" align="right"><?=number_format($rows['emb_rcv'],2);?></td>
													<td width="80" align="right"><?=number_format($rows['wash_iss'],2);?></td>
													<td width="80" align="right"><?=number_format($rows['wash_rcv'],2);?></td>
													<td width="100" align="right"><?=number_format($rows['gmts_finishing'],2);?></td>
													<td width="100" align="right"><?=number_format($rows['packing'],2)?></td>
													<td width="80" align="right"><?=number_format($rows['avg_efficiency'],2)?>%</td>
													<td width="80" align="right"><?=number_format($rows['produce_minit'],2)?></td>
													<td width="80" align="right"><?=number_format($rows['available_minit'],2);?></td>
													<td width="80"  align="right"><?=number_format($rows['target'],2);?></td>
													<td width="80" align="right"><?= number_format($rows['buyer_insp_passed_qty'],2) ?></td>
													<td width="80" align="right"><?= number_format($rows['buyer_insp_failed_qty'],2)?></td>
													<td width="80" align="right"><?= number_format($rows['ex_factory'],2)?></td></td>


												</tr>

											<?
											     $total_cutting_qty+=$rows['cutting_qty'];
												 $total_sewing_input+=$rows['sewing_input'];
												 $total_sewing_output+=$rows['sewing_output'];
												 $total_print_iss+=$rows['print_iss'];
												 $total_print_rcv+=$rows['print_rcv'];
												 $total_emb_iss+=$rows['emb_iss'];
												 $total_emb_rcv+=$rows['emb_rcv'];
												 $total_wash_iss+=$rows['wash_iss'];
												 $total_wash_rcv+=$rows['wash_rcv'];
												 $total_gmts_finishing+=$rows['gmts_finishing'];
												 $total_packing+=$rows['packing'];
												 $total_avg_efficiency+=$rows['avg_efficiency'];
												 $total_produce_minit+=$rows['produce_minit'];
												 $total_available_minit+=$rows['available_minit'];
												 $total_target+=$rows['target'];
												 $total_buyer_insp_passed_qty+=$rows['buyer_insp_passed_qty'];
												 $total_buyer_insp_failed_qty+=$rows['buyer_insp_failed_qty'];
												 $total_ex_factory+=$rows['ex_factory'];

												}
											?>






		         </tbody>
		     </table>
			        <table align="center" cellspacing="0" cellpadding="0" width="1640"  border="1" rules="all" class="rpt_table">
						<tfoot>
							<tr>
								<th width="100" >Total</th>
								<th width="100"align="right"><?= number_format($total_cutting_qty,0)?></th>
								<th width="100"align="right"><?= number_format($total_sewing_input,0)?></th>
								<th width="100" align="right"><?= number_format( $total_sewing_output,0)?></th>
								<th width="80" align="right"><?= number_format($total_print_iss,0)?></th>
								<th width="80" align="right"><?= number_format($total_print_rcv,0)?></th>
								<th width="80" align="right"><?= number_format( $total_emb_iss,0)?></th>
								<th width="80" align="right"><?= number_format( $total_emb_rcv,0)?></th>
								<th width="80" align="right"><?= number_format( $total_wash_iss,0)?></th>
								<th width="80" align="right"><?= number_format( $total_wash_rcv,0)?></th>
								<th width="100" align="right"><?= number_format( $total_gmts_finishing,0)?></th>
								<th width="100" align="right"><?= number_format( $total_packing,0)?></th>
								<th width="80" align="right"></th>
								<th width="80" align="right"><?= number_format( $total_produce_minit,0)?></th>
								<th width="80" align="right"><?= number_format($total_available_minit,0)?></th>
								<th width="80"  align="right"></th>
								<th width="80" align="right"><?= number_format($total_buyer_insp_passed_qty,0)?></th>
								<th width="80" align="right"><?= number_format($total_buyer_insp_failed_qty,0)?></th>
								<th width="80"align="right"><?= number_format($total_ex_factory,0)?></th>







							</tr>

							<tr bgcolor="#dddddd">
								<th width="100" >Avg.</th>
								<th width="100"align="right"><?= number_format($total_cutting_qty / $company_count,0)?></th>
								<th width="100"align="right"><?= number_format($total_sewing_input / $company_count,0)?></th>
								<th width="100" align="right"><?= number_format( $total_sewing_output / $company_count,0)?></th>
								<th width="80" align="right"><?= number_format($total_print_iss / $company_count,0)?></th>
								<th width="80" align="right"><?= number_format($total_print_rcv / $company_count,0)?></th>
								<th width="80" align="right"><?= number_format( $total_emb_iss / $company_count,0)?></th>
								<th width="80" align="right"><?= number_format( $total_emb_rcv / $company_count,0)?></th>
								<th width="80" align="right"><?= number_format( $total_wash_iss / $company_count,0)?></th>
								<th width="80" align="right"><?= number_format( $total_wash_rcv / $company_count,0)?></th>
								<th width="100" align="right"><?= number_format( $total_gmts_finishing / $company_count,0)?></th>
								<th width="100" align="right"><?= number_format( $total_packing / $company_count,0)?></th>
								<th width="80" align="right"></th>
								<th width="80" align="right"><?= number_format( $total_produce_minit / $company_count,0)?></th>
								<th width="80" align="right"><?= number_format($total_available_minit / $company_count,0)?></th>
								<th width="80"  align="right"></th>
								<th width="80" align="right"><?= number_format($total_buyer_insp_passed_qty / $company_count,0)?></th>
								<th width="80" align="right"><?= number_format($total_buyer_insp_failed_qty / $company_count,0)?></th>
								<th width="80"align="right"><?= number_format($total_ex_factory / $company_count,0)?></th>



							</tr>
						</tfoot>



					</table>


		        </div>
				</div>
	    </fieldset>

	    <?
	}

	elseif ($type==7) // show5 button
	{
		$floor_arr = return_library_array("select id,floor_name from  lib_prod_floor ","id","floor_name");
		$location_library =return_library_array("select id,location_name from  lib_location ","id","location_name");
		$lineArr = return_library_array("select id,line_name from lib_sewing_line where company_name in($cbo_company)","id","line_name");


		$lineDataArr = sql_select("SELECT id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1 and company_name in($cbo_company) order by sewing_line_serial");
		foreach($lineDataArr as $lRow)
		{
			$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
			$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
			$lastSlNo=$lRow[csf('sewing_line_serial')];
		}

		if($cbo_company==0) $cbo_company_cond_lay=""; else $cbo_company_cond_lay =" and a.WORKING_COMPANY_ID in($cbo_company)";
		if($cbo_location=="") $cbo_location_cond_lay=""; else $cbo_location_cond_lay =" and a.LOCATION_ID in($cbo_location)";

		if($cbo_company==0) $cbo_company_cond_pro=""; else $cbo_company_cond_pro =" and a.SERVING_COMPANY in($cbo_company)";
		if($cbo_location=="") $cbo_location_cond_pro=""; else $cbo_location_cond_pro =" and a.LOCATION in($cbo_location)";

		if($cbo_company==0) $cbo_company_cond_print=""; else $cbo_company_cond_print =" and a.company_id in($cbo_company)";
		if($cbo_location=="") $cbo_location_cond_print=""; else $cbo_location_cond_print =" and a.LOCATION_ID in($cbo_location)";

		if( $date_from==0 && $date_to==0 ) $entry_date=""; else $entry_date= " and a.entry_date between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'";

		if( $date_from==0 && $date_to==0 ) $PRODUCTION_DATE=""; else $PRODUCTION_DATE= " and a.PRODUCTION_DATE between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'";

		if( $date_from==0 && $date_to==0 ) $production_date_print=""; else $production_date_print= " and b.PRODUCTION_DATE between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'";

		if($cbo_company==0) $cbo_company_cond=""; else $cbo_company_cond =" and a.company_id in($cbo_company)";
		if($cbo_location=="") $cbo_location_cond_=""; else $cbo_location_cond =" and a.LOCATION_ID in($cbo_location)";

		if( $date_from==0 && $date_to==0 ) $pr_date=""; else $pr_date= " and b.pr_date between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'";

		if($cbo_company==0) $cbo_company_cond_ship=""; else $cbo_company_cond_ship =" and a.DELIVERY_COMPANY_ID in($cbo_company)";
		if($cbo_location=="") $cbo_location_cond_ship=""; else $cbo_location_cond_ship =" and a.DELIVERY_LOCATION_ID in($cbo_location)";

		if( $date_from==0 && $date_to==0 ) $delivery_date=""; else $delivery_date= " and a.delivery_date between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'";

		if($cbo_company==0) $cbo_company_cond_left_over=""; else $cbo_company_cond_left_over =" and a.WORKING_COMPANY_ID in($cbo_company)";
		if($cbo_location=="") $cbo_location_cond_left_over=""; else $cbo_location_cond_left_over =" and a.WORKING_LOCATION_ID in($cbo_location)";

		if( $date_from==0 && $date_to==0 ) $leftover_date=""; else $leftover_date= " and a.LEFTOVER_DATE between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'";

		$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id in($cbo_company) and shift_id=1 and pr_date between '$date_from' and '$date_to' and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
		// echo $min_shif_start;die;
		$start_time_arr=array();
		$start_time_data_arr=sql_select("select company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time,TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($cbo_company) and  shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");
		// print_r($start_time_data_arr);
		$lunch_start_time = "";
		foreach($start_time_data_arr as $row)
		{
			$start_time_arr[$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
			$start_time_arr[$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];
			$exp = explode(":",$row[csf('lunch_start_time')]);
			$lunch_start_time = $exp[0]*1;
		}
		// echo $lunch_start_time;die;
		$prod_start_hour=$start_time_arr[1]['pst'];
		$global_start_lanch=$start_time_arr[1]['lst'];
		if($prod_start_hour=="") $prod_start_hour="08:00";
		$start_time=explode(":",$prod_start_hour);
		// $hour=substr($start_time[0],1,1);
		$hour = $start_time[0]*1;
		$minutes=$start_time[1];
		$last_hour=23;
		$lineWiseProd_arr=array(); $prod_arr=array(); $start_hour_arr=array();
		$start_hour=$prod_start_hour;
		$start_hour_arr[$hour]=$start_hour;
		for($j=$hour;$j<$last_hour;$j++)
		{
			$start_hour=add_time($start_hour,60);
			$start_hour_arr[$j+1]=substr($start_hour,0,5);
		}
		//echo $prod_start_hour;die;
		//echo"<pre>";print_r($prod_start_hour);die;
		$start_hour_arr[$j+1]='23:59';
		if($prod_start_hour>$min_shif_start)  $prod_start_hour=$min_shif_start;
		$actual_date=date("Y-m-d");
		$actual_production_date=date("Y-m-d",strtotime(str_replace("'","",$txt_date)));
		$actual_time=substr(date("Y-m-d H:i:s",strtotime($pc_date_time)),11,2);
		$acturl_hour_minute=date("H:i",strtotime($pc_date_time));
		$generated_hourarr=array();
		$first_hour_time=explode(":",$min_shif_start);
		$hour_line=substr($first_hour_time[0],1,1); $minutes_one=$start_time[1];
		$line_start_hour_arr[$hour_line]=$min_shif_start;
		//echo $actual_production_date;die;
		for($l=$hour_line;$l<$last_hour;$l++)
		{
			$min_shif_start=add_time($min_shif_start,60);
			$line_start_hour_arr[$l+1]=substr($min_shif_start,0,5);
		}

		$line_start_hour_arr[$j+1]='23:59';

		// echo "<pre>";print_r($start_hour_arr);echo "</pre>";

		//====================================== $cut_and_lay ======================================//

		$cut_lay_sql="SELECT a.WORKING_COMPANY_ID,a.LOCATION_ID,a.FLOOR_ID,a.ENTRY_DATE,b.SIZE_QTY from PPL_CUT_LAY_MST a,PPL_CUT_LAY_BUNDLE b where  a.id=b.mst_id   and a.STATUS_ACTIVE=1  and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 $cbo_company_cond_lay $cbo_location_cond_lay  $entry_date order by a.ENTRY_DATE asc";
		//echo $cut_lay_sql;die;

		$cut_lay_floor_array=array();
		$cut_lay_floor_wise_total = array();
		$data_array=array();
		//$location_array==array();
		foreach(sql_select($cut_lay_sql) as $row)
		{
		$data_array[$row['WORKING_COMPANY_ID']][$row['LOCATION_ID']][strtotime($row['ENTRY_DATE'])][$row['FLOOR_ID']][0]+=$row['SIZE_QTY'];
		$cut_lay_floor_wise_total[$row['WORKING_COMPANY_ID']][$row['FLOOR_ID']][0]+=$row['SIZE_QTY'];
		$cut_lay_floor_array[$row['WORKING_COMPANY_ID']][$row['FLOOR_ID']] =$row['FLOOR_ID'];
		}

		// echo "<pre>";print_r($cut_lay_floor_wise_total);die;

		//===============================$production_query=================================================//

		$production_sql="SELECT a.id, a.SERVING_COMPANY,a.LOCATION,a.PO_BREAK_DOWN_ID,a.PRODUCTION_TYPE,a.FLOOR_ID,a.PROD_RESO_ALLO,a.EMBEL_NAME,a.PRODUCTION_DATE,b.PRODUCTION_QNTY from PRO_GARMENTS_PRODUCTION_MST a,PRO_GARMENTS_PRODUCTION_DTLS b where a.id=b.mst_id  and a.production_type in (1,2,3,4,5,6,7,8,9,11,15,80)  and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 $cbo_company_cond_pro $cbo_location_cond_pro  $PRODUCTION_DATE order by a.PRODUCTION_DATE asc";
		//echo $production_sql;die;
		$prod_floor_arr=array();
		$prod_floor_wise_total = array();
		$production_id_arr=array();
		foreach(sql_select($production_sql) as $val)
		{
			if($val['PRODUCTION_TYPE']==5)
			{
				$production_id_arr[$val['ID']]=$val['ID'];
			}

			$prod_floor_arr[$val['SERVING_COMPANY']][$val['PRODUCTION_TYPE']][$val['FLOOR_ID']]=$val['FLOOR_ID'];

			$prod_floor_wise_total[$val['SERVING_COMPANY']][$val['FLOOR_ID']][$val['PRODUCTION_TYPE']]+=$val['PRODUCTION_QNTY'];

			$data_array[$val['SERVING_COMPANY']][$val['LOCATION']][strtotime($val['PRODUCTION_DATE'])][$val['FLOOR_ID']][$val['PRODUCTION_TYPE']]+=$val['PRODUCTION_QNTY'];

		}
		//echo "<pre>";print_r($data_array);die;

		$printing_production_sql = "SELECT a.company_id, b.production_date, a.location_id, b.qcpass_qty,a.entry_form,a.floor_id FROM subcon_embel_production_mst a, subcon_embel_production_dtls b WHERE   a.id =b.mst_id
		and a.entry_form in(222,315) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   $cbo_company_cond_print $cbo_location_cond_print $production_date_print  order by  b.production_date asc";
		// echo $printing_production_sql; die;
		$print_floor_arr=array();
		$print_floor_wise_total=array();

		foreach(sql_select($printing_production_sql) as $v)
		{
			$data_array[$v['COMPANY_ID']][$v['LOCATION_ID']][strtotime($v['PRODUCTION_DATE'])][$v['FLOOR_ID']][$v['ENTRY_FORM']]+=$v['QCPASS_QTY'];

			$print_floor_arr[$v['COMPANY_ID']][$v['ENTRY_FORM']][$v['FLOOR_ID']]=$v['FLOOR_ID'];
			$print_floor_wise_total[$v['COMPANY_ID']][$v['FLOOR_ID']][$v['ENTRY_FORM']]+=$v['QCPASS_QTY'];
		}
		//echo"<pre>"; print_r($data_array);die;

		$prod_source_sql="SELECT a.company_id,a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.line_chief, b.target_per_hour, b.working_hour,c.capacity,c.target_efficiency,2 as type_line from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=b.mst_id and a.id=c.mst_id  $cbo_company_cond $cbo_location_cond $pr_date
		and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 order by b.pr_date asc";
		//echo $prod_source_sql ;die;


		$prod_source_arr=array();
		$prod_source_total=array();
		//echo"<pre>";print_r($prod_source_arr);die;
		foreach (sql_select($prod_source_sql)  as $value)
		{
			$prod_source_arr[$value['COMPANY_ID']][$value['LOCATION_ID']][strtotime($value['PR_DATE'])][$value['ID']]['helper']=$value['HELPER'];
			$prod_source_arr[$value['COMPANY_ID']][$value['LOCATION_ID']][strtotime($value['PR_DATE'])][$value['ID']]['operator']=$value['OPERATOR'];
			$prod_source_arr[$value['COMPANY_ID']][$value['LOCATION_ID']][strtotime($value['PR_DATE'])][$value['ID']]['man_power']=$value['MAN_POWER'];
			$prod_source_arr[$value['COMPANY_ID']][$value['LOCATION_ID']][strtotime($value['PR_DATE'])][$value['ID']]['terget_hour']=$value[csf('target_per_hour')];
			$prod_source_arr[$value['COMPANY_ID']][$value['LOCATION_ID']][strtotime($value['PR_DATE'])][$value['ID']]['working_hour']=$value[csf('working_hour')];
		}
		//echo"<pre>";print_r($prod_source_arr);die;
		$prod_rce_arr=array();
		foreach ($prod_source_arr as $com_key => $com_val)
		{
			foreach ($com_val as $loc_key => $loc_val)
			{
				foreach ($loc_val as $date_key => $date_val)
				{
					foreach ($date_val as $key => $v)
					{
						$prod_rce_arr[$com_key][$loc_key][$date_key]['helper'] += $v['helper'];
						$prod_rce_arr[$com_key][$loc_key][$date_key]['operator'] += $v['operator'];
						$prod_rce_arr[$com_key][$loc_key][$date_key]['man_power'] += $v['man_power'];
						$prod_rce_arr[$com_key][$loc_key][$date_key]['terget_hour'] = $v['terget_hour'];
						$prod_rce_arr[$com_key][$loc_key][$date_key]['working_hour'] = $v['working_hour'];
						$prod_rce_arr[$com_key][$loc_key][$date_key]['tpd']+=$v['terget_hour']*$v['working_hour'];


					}
				}
			}

		}
		// echo"<pre>";print_r($prod_rce_arr);die;
				//=========================================== Ex-Factory =================================//
		$gmt_shipment_sql="SELECT  a.DELIVERY_COMPANY_ID, a.DELIVERY_LOCATION_ID,b.invoice_no, a.delivery_date, b.po_break_down_id, a.entry_form,
		sum(case when a.entry_form<>85 then b.ex_factory_qnty else 0 end) as ship_qty,
		sum(case when a.entry_form<>85 then b.ex_factory_qnty*(c.unit_price/d.total_set_qnty) else 0 end) as ship_value from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b, wo_po_break_down c,  wo_po_details_master d
		where a.id=b.delivery_mst_id and  b.po_break_down_id=c.id and c.job_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $cbo_company_cond_ship $cbo_location_cond_ship $delivery_date
		group by a.DELIVERY_COMPANY_ID, a.DELIVERY_LOCATION_ID,b.invoice_no, a.delivery_date, b.po_break_down_id,c.unit_price, a.entry_form, d.total_set_qnty order by a.delivery_date asc";
		//echo $gmt_shipment_sql;die();

		$gmt_shipment_arr=array();
		foreach (sql_select($gmt_shipment_sql)  as  $r)
		{
			$gmt_shipment_arr[$r['DELIVERY_COMPANY_ID']][$r['DELIVERY_LOCATION_ID']][strtotime($r['DELIVERY_DATE'])]['ship_qty']+=$r['SHIP_QTY'];
			$gmt_shipment_arr[$r['DELIVERY_COMPANY_ID']][$r['DELIVERY_LOCATION_ID']][strtotime($r['DELIVERY_DATE'])]['ship_value']+=$r['SHIP_VALUE'];

		}
		//echo"<pre>";print_r($gmt_shipment_arr);die;

			//==================================================== Left Over Garments ===============================//
			$gmt_left_over_sql="SELECT a.WORKING_COMPANY_ID,a.WORKING_LOCATION_ID,a.LEFTOVER_DATE,a.GOODS_TYPE,b.TOTAL_LEFT_OVER_RECEIVE from PRO_LEFTOVER_GMTS_RCV_MST a,PRO_LEFTOVER_GMTS_RCV_DTLS b where a.id =b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $cbo_company_cond_left_over $cbo_location_cond_left_over $leftover_date order by a.LEFTOVER_DATE asc ";
		//echo $gmt_left_over_sql;die();

		$gmt_left_over_arr=array();
		foreach (sql_select($gmt_left_over_sql)  as $v)
		{
			$gmt_left_over_arr[$v['WORKING_COMPANY_ID']][$v['WORKING_LOCATION_ID']][strtotime($v['LEFTOVER_DATE'])][$v['GOODS_TYPE']]+=$v['TOTAL_LEFT_OVER_RECEIVE'];
		}
		// echo"<pre>";print_r($gmt_left_over_arr);die;

		$production_id_cond=where_con_using_array($production_id_arr,0,"a.id");

		$sql="SELECT a.serving_company, a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line,c.style_ref_no, a.po_break_down_id, a.item_number_id,sum(b.production_qnty) as good_qnty,";
			$first=1;
			for($h=$hour;$h<$last_hour;$h++)
			{
				$bg=$start_hour_arr[$h];
				$end=substr(add_time($start_hour_arr[$h],60),0,5);
				$prod_hour="prod_hour".substr($bg,0,2);
				if($first==1)
				{
					$sql.="sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN b.production_qnty else 0 END) AS $prod_hour,";
				}
				else
				{
					$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5
					THEN b.production_qnty else 0 END) AS $prod_hour,";
				}
				$first++;
			}
			$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN b.production_qnty else 0 END) AS prod_hour23

			FROM  pro_garments_production_mst a ,pro_garments_production_dtls b, wo_po_details_master c, wo_po_break_down d,wo_po_color_size_breakdown e
			WHERE a.id=b.mst_id and a.po_break_down_id=d.id and d.job_id=c.id and d.job_id=e.job_id and d.id=e.po_break_down_id and b.color_size_break_down_id=e.id and a.production_type=5 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $cbo_company_cond_pro $cbo_location_cond_pro  $PRODUCTION_DATE $production_id_cond
			GROUP BY a.serving_company, a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,c.style_ref_no,a.item_number_id
			ORDER BY a.production_date";
			$sql="SELECT a.serving_company, a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line,c.id as job_id,c.buyer_name,c.style_ref_no,c.job_no, a.po_break_down_id, a.item_number_id,d.po_number,d.file_no,d.unit_price,d.grouping as ref,b.color_type_id,a.remarks,sum(b.production_qnty) as good_qnty,";
				$first=1;
				for($h=$hour;$h<$last_hour;$h++)
				{
					$bg=$start_hour_arr[$h];
					$end=substr(add_time($start_hour_arr[$h],60),0,5);
					$prod_hour="prod_hour".substr($bg,0,2);
					if($first==1)
					{
						$sql.="sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN b.production_qnty else 0 END) AS $prod_hour,";
					}
					else
					{
						$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5
						THEN b.production_qnty else 0 END) AS $prod_hour,";
					}
					$first++;
				}
				$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN b.production_qnty else 0 END) AS prod_hour23

				FROM  pro_garments_production_mst a ,pro_garments_production_dtls b, wo_po_details_master c, wo_po_break_down d,wo_po_color_size_breakdown e
				WHERE a.id=b.mst_id and a.po_break_down_id=d.id and d.job_id=c.id and d.job_id=e.job_id and d.id=e.po_break_down_id and b.color_size_break_down_id=e.id and a.production_type=5 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0  $cbo_company_cond_pro $cbo_location_cond_pro $PRODUCTION_DATE  $production_id_cond
				GROUP BY a.serving_company,c.job_no, a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,c.id,c.buyer_name,c.style_ref_no,a.item_number_id,d.po_number,d.unit_price,d.file_no,d.grouping ,b.color_type_id,a.remarks
				ORDER BY a.production_date";
		//echo $sql;die;
		$production_data_arr=array();
		$poIdArr=array();
		$all_style_arr=array();
		$lc_com_array=array();
		$style_wise_po_arr=array();
		$po_item_wise_prod_qty_arr=array();
		foreach (sql_select($sql) as $val)
		{
			$poIdArr[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
			$all_style_arr[$val[csf('style_ref_no')]] = $val[csf('style_ref_no')];
			$lc_com_array[$val[csf('company_id')]] = $val[csf('company_id')];
			$style_wise_po_arr[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
			if($val[csf('prod_reso_allo')]==1)
				{
					$sewing_line_id=$prod_reso_arr[$val[csf('sewing_line')]];
					$reso_line_ids.=$val[csf('sewing_line')].',';
				}
				else
				{
					$sewing_line_id=$val[csf('sewing_line')];
				}

				if($lineSerialArr[$sewing_line_id]=="")
				{
					$lastSlNo++;
					$slNo=$lastSlNo;
					$lineSerialArr[$sewing_line_id]=$slNo;
				}
				else $slNo=$lineSerialArr[$sewing_line_id];

				for($h=$hour;$h<$last_hour;$h++)
				{
					$prod_hour="prod_hour".substr($start_hour_arr[$h],0,2)."";
					$production_data_arr[$val[csf('serving_company')]][$val[csf('location')]][strtotime($val[csf('production_date')])][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]]['qty'][$prod_hour]+=$val[csf($prod_hour)];
					$production_data_arr[$val[csf('serving_company')]][$val[csf('location')]][strtotime($val[csf('production_date')])][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]]['po_item'] .= $val[csf('po_break_down_id')]."__".$val[csf('item_number_id')]."**";

				}

		}
		//echo $prod_hour;
		// echo"<pre>";print_r($production_data_arr);die;
				// ==================================================== SMV Source ================================== //
		$lc_com_ids = implode(",",$lc_com_array);
		$poIds_cond = where_con_using_array($poIdArr,0,"b.id");
		$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($lc_com_ids) and variable_list=25 and   status_active=1 and is_deleted=0");
		// echo $smv_source;

		if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
		if($smv_source==3) // from gsd enrty
		{
			$style_cond = where_con_using_array($all_style_arr,1,"a.style_ref");
			$sql_item="SELECT a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID  from PPL_GSD_ENTRY_MST a where a.APPLICABLE_PERIOD  between '$date_from' and '$date_to' and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 $style_cond  group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID ORDER BY a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC";
			$gsdSqlResult=sql_select($sql_item);
			//echo $sql_item;die;
			foreach($gsdSqlResult as $rows)
			{
				foreach($style_wise_po_arr[$rows['STYLE_REF']] as $po_id)
				{
					if($item_smv_array[$po_id][$rows['GMTS_ITEM_ID']]=='')
					{
						$item_smv_array[$po_id][$rows['GMTS_ITEM_ID']]=$rows['TOTAL_SMV'];
					}
				}
			}
		}
		else
		{
			$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost,c.smv_set from wo_po_details_master a,wo_po_break_down b, wo_po_details_mas_set_details c where b.job_id=a.id and b.job_id=c.job_id and a.company_name in($lc_com_ids) $poIds_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			//echo $sql_item;
			$resultItem=sql_select($sql_item);

			foreach($resultItem as $itemData)
			{
				if($smv_source==1)
				{
					$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
				}
				if($smv_source==2)
				{
					$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs_precost')];
				}
			}
		}
		//echo "<pre>";print_r($item_smv_array);echo "</pre>";

		$time1 = $hour;
		$time2 = date('H');

		if(substr($global_start_lanch,0,2) < $time2)
		{
			$cur_difference_hour = (int) $time2 - $time1;
			$cur_difference_hour = $cur_difference_hour - 1;
			// echo $cur_difference_hour."==SSSSSSSS";
		}
		else
		{
			$cur_difference_hour = (int) $time2 - $time1;
		}
		//echo $cur_difference_hour;die;

		$resource_data_array = array(); // wo-com, date
		foreach ($production_data_arr as $com_key => $com_data)
		{
			foreach ($com_data as $lc_key => $lc_data)
			{
				foreach ($lc_data as $date_key => $date_data)
				{
					foreach ($date_data as $flr_id => $flr_data)
					{
						foreach ($flr_data as $li_sl => $sl_data)
						{
							foreach ($sl_data as $l_id => $l_data)
							{
								$tot_prod_qty = 0;
								$produce_min = 0;
								$general_prod_qty = 0;
								$ot_prod_qty = 0;
								$general_prod_min = 0;
								$ot_prod_min = 0;
								$po_item_arr = array_unique(array_filter(explode("**",$l_data['po_item'])));
								foreach ($po_item_arr as $po_item_data)
								{
									list($po_id,$item_id) = explode("__",$po_item_data);
									if($po_chk_arr[$po_id.$item_id]=="")
									{
										$po_chk_arr[$po_id.$item_id] = "AA";
										$smv=$item_smv_array[$po_id][$item_id];
										$gen_last_prod_hour = "";
										$ot_last_prod_hour = "";
										$m=1;
										for($k=$hour; $k<=$last_hour; $k++)
										{
											$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
											if($m<=9)
											{
												$general_prod_qty += $l_data['qty'][$prod_hour];
												if($l_data['qty'][$prod_hour]>0)
												{
													$gen_last_prod_hour=substr($start_hour_arr[$k],0,2);
												}
											}
											else
											{
												$ot_prod_qty += $l_data['qty'][$prod_hour];
												if($l_data['qty'][$prod_hour]>0)
												{
													$ot_last_prod_hour=substr($start_hour_arr[$k],0,2);
												}
											}
											$m++;
											$tot_prod_qty += $l_data['qty'][$prod_hour];
										}
										// echo $tot_prod_qty;die;

										$po_count++;

										// ============================
										$current_hour = 0;

										$line_prod_hour_array = array();
										if(strtotime(date('d-M-Y')) != $date_key)
										{
											$working_hour = $prod_source_arr[$com_key][$lc_key][$date_key][$l_id]['working_hour']-1;
											//echo $difference_hour = $line_prod_hour_array[$l_id];die;
											for($k=$hour; $k<=$last_hour; $k++)
											{
												$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
												// echo $r['qty'][$prod_hour]."<br>";
												if($r['qty'][$prod_hour]>0)
												{
													$line_prod_hour_array[$l_id]++;
												}

											}
											$difference_hour = $line_prod_hour_array[$l_id];


											if($ot_last_prod_hour!="")
											{
												$current_hour = $ot_last_prod_hour - $hour;
												// echo $lc_key."=".$ot_last_prod_hour." - ".$hour;echo "<br>";
											}
											else
											{
												if(substr($global_start_lanch,0,2) < substr(date('H:i:s'),0,2))
												{
													// $current_hour = $prod_rce_arr[$com_key][$lc_key][$date_key]['working_hour']-1;
													$current_hour = $prod_source_arr[$com_key][$lc_key][$date_key][$l_id]['working_hour']-1;
												}
												else
												{
													// $current_hour = $prod_rce_arr[$com_key][$lc_key][$date_key]['working_hour'];
													$current_hour = $prod_source_arr[$com_key][$lc_key][$date_key][$l_id]['working_hour'];
												}
											}
										}
										else // for current date
										{
											for($k=$hour; $k<=$last_hour; $k++)
											{
												$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
												// echo $r['qty'][$prod_hour]."<br>";
												if($l_data['qty'][$prod_hour]>0)
												{
													$line_prod_hour_array[$l_id]++;
												}

											}

											$difference_hour = $line_prod_hour_array[$l_id];
											if($ot_last_prod_hour!="")
											{
												$current_hour = $ot_last_prod_hour - $hour;
											}
											else
											{
												$current_hour = $cur_difference_hour;
											}

											// chk lunch hour
											if(substr($global_start_lanch,0,2) < substr(date('H:i:s'),0,2))
											{
												$working_hour = $prod_source_arr[$com_key][[$lc_key]][$date_key][$l_id]['working_hour']-1;
											}
											else
											{
												$working_hour = $prod_source_arr[$com_key][[$lc_key]][$date_key][$l_id]['working_hour'];
											}

										}
										// $efficiency_min+=($prod_rce_arr[$com_key][$lc_key][$date_key]['man_power'])*$current_hour*60;
										//echo $current_hour."<br>";
										// echo $current_hour."**".$l_id;

										$resource_data_array[$com_key][$lc_key][$date_key]['day_tr']=$current_hour*$prod_rce_arr[$com_key][$lc_key][$date_key]['terget_hour'];
										// echo $current_hour."**".$prod_rce_arr[$com_key][$lc_key][$date_key]['terget_hour'];die;
										// $resource_data_array[$com_key][$lc_key][$date_key]['achv'] += ( $tot_prod_qty) / ($current_hour*$prod_rce_arr[$com_key][$lc_key][$date_key]['terget_hour'])*100;
										// echo "$l_id==(". $tot_prod_qty.") / (".$current_hour."*".$prod_rce_arr[$com_key][$lc_key][$date_key]['terget_hour'].")*100<br>";
										// echo $tot_prod_qty."<br>";

										// $resource_data_array[$com_key][$lc_key][$date_key]['eff'] +=(($tot_prod_qty*$smv)/($prod_rce_arr[$com_key][$lc_key][$date_key]['man_power']*60*$current_hour))*100;
										$produce_min += ($tot_prod_qty*$smv);

										// echo "(". $tot_prod_qty*$smv.")<br>";
										// echo $current_hour."<br>";
									}


								}
								// =======================
								$resource_data_array[$com_key][$lc_key][$date_key]['achv'] += ( $tot_prod_qty) / ($current_hour*$prod_source_arr[$com_key][$lc_key][$date_key][$l_id]['terget_hour'])*100;
								$resource_data_array[$com_key][$lc_key][$date_key]['line_count']++;
								// echo "$l_id==(". $tot_prod_qty.") / (".$current_hour."*".$prod_source_arr[$com_key][$lc_key][$date_key][$l_id]['terget_hour'].")*100<br>";
								$resource_data_array[$com_key][$lc_key][$date_key]['eff'] +=(($tot_prod_qty*$smv)/($prod_source_arr[$com_key][$lc_key][$date_key][$l_id]['man_power']*60*$current_hour))*100;
								// echo "((".$produce_min.")/(".$prod_source_arr[$com_key][$lc_key][$date_key][$l_id]['man_power']."*60*".$current_hour."))*100<br>";
							}
						}
					}
				}
			}

		}
		//echo ($smv);die;
		//echo "<pre>"; print_r($production_data_arr);die;
		ob_start();

		?>
			<fieldset style="margin-top: 50px">
			<?
				$compnay_name_arr=explode(",",str_replace("'","",$cbo_company_id));
				// echo "<pre>";print_r($compnay_name_arr);die;
				$grand_total_cut_lay=0;
				foreach($compnay_name_arr as $company_key=> $company_val)
				{
					$j=1;
					$count_data=count($prod_floor_arr[$company_val][1])+count($prod_floor_arr[$company_val][5])+count($prod_floor_arr[$company_val][4])+count($prod_floor_arr[$company_val][8])+count($prod_floor_arr[$company_val][6])+count($prod_floor_arr[$company_val][7])+count($prod_floor_arr[$company_val][15])+count($prod_floor_arr[$company_val][11])+count($print_floor_arr[$company_val][222])+count($print_floor_arr[$company_val][315]);;
					// echo $count_data;die;
					//$count_hd=count($sql_floor)+1;
					$count_hd=count($prod_floor_arr[$company_val][1])+count($prod_floor_arr[$company_val][5])+count($prod_floor_arr[$company_val][4])+count($prod_floor_arr[$company_val][8])+count($prod_floor_arr[$company_val][6])+count($prod_floor_arr[$company_val][7])+count($prod_floor_arr[$company_val][15])+count($prod_floor_arr[$company_val][11])+count($print_floor_arr[$company_val][222])+count($print_floor_arr[$company_val][315]);
					// echo $count_hd;die;
					// echo count($prod_floor_arr[$company_val][4]);die;
					$count_data_lay=count($cut_lay_floor_array[$company_val]);
					$count_hd_lay=count($cut_lay_floor_array[$company_val]);


					$width_hd_lay=$count_hd_lay*80;
					$count_cutting_lay=count($cut_lay_floor_array[$company_val])+1;
					$width_cutting_lay=$count_cutting_lay*80;

					$count_cutting_qc=count($prod_floor_arr[$company_val][1])+1;
					$width_cutting_qc=$count_cutting_qc*80;
					//echo $width_cutting_qc;die;

					$count_print=count($print_floor_arr[$company_val][222])+1;
					$width_print=$count_print*80;
					//echo $count_print;die;
					$count_emb=count($print_floor_arr[$company_val][315])+1;
					$width_emb=$count_emb*80;

					$count_iron=count($prod_floor_arr[$company_val][7])+1;
					$width_iron=$count_iron*80;

					$count_hang_tag=count($prod_floor_arr[$company_val][15])+1;
					$width_hang_tag=$count_hang_tag*80;

					$count_poly=count($prod_floor_arr[$company_val][11])+1;
					$width_poly=$count_poly*80;

					$count_sewing_in=count($prod_floor_arr[$company_val][4])+1;
					$width_sewing_in=$count_sewing_in*80;

					$count_sewing_out=count($prod_floor_arr[$company_val][5])+7;
					$width_sewing_out=$count_sewing_out*80;

					$count_packing=count($prod_floor_arr[$company_val][8])+1;
							$width_packing=$count_packing*80;

					$table_width=1500+(($count_data+4)*80);
					?>

					<table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="caption" align="center">
						<tr>
							<td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:18px">

							Company Name: <?= $company_library[ $company_val]; ?>

								</strong>
							</td>
						</tr>
						<tr>
							<td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:14px"><? echo $report_title; ?></strong></td>
						</tr>
						<tr>
							<td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:14px"> <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
						</tr>
					</table>

					<table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left">
						<thead>
							<tr>
								<th width="150" rowspan="2" ><p>Working Company</p></th>
								<th width="100" rowspan="2"><p>Location</p></th>
								<th width="100" rowspan="2" ><p>Date</p></th>
								<th width="<? echo $width_cutting_lay; ?>" colspan="<? echo $count_cutting_lay; ?>"><p>Cut and Lay Qty</p></th>
								<th width="<? echo $width_cutting_qc; ?>" colspan="<? echo $count_cutting_qc; ?>" ><p>Cutting QC</p></th>

								<th width="<? echo $width_print; ?>" colspan="<? echo $count_print; ?>" ><p>Printing</p></th>
								<th width="<? echo $width_emb; ?>" colspan="<? echo $count_emb; ?>"><p>Embroidery</p></th>

								<th  width="<? echo $width_sewing_in; ?>" colspan="<? echo $count_sewing_in; ?>"><p>Sewing Input</p></th>
								<th width="<? echo $width_sewing_out; ?>" colspan="<? echo $count_sewing_out; ?>"><p>Sewing Output</p></th>
								<th width="<? echo $width_iron; ?>" colspan="<? echo $count_iron; ?>"><p>Iron</p></th>
								<th width="<? echo $width_hang_tag; ?>" colspan="<? echo $count_hang_tag; ?>"><p>Hang Tag</p></th>
								<th  width="<? echo $width_poly; ?>" colspan="<? echo $count_poly; ?>"><p>Poly</p></th>

								<th width="<? echo $width_packing; ?>" colspan="<? echo $count_packing; ?>"><p>Packing</p></th>


								<th colspan="3"><p>Ex-Factory Qty</p></th>
								<th colspan="2"><p>Left Over Garments</p></th>
							</tr>
							<tr>
								<?
									foreach ($cut_lay_floor_array[$company_val] as $rows)
									{
										?>
										<th width="80" title="<?=$rows;?>"><? echo $floor_arr[$rows]; ?></th>
										<?

									}
								?>
								<th width="80" >Total</th>
								<?
									foreach ($prod_floor_arr[$company_val][1] as $rows)
									{
										?>
										<th width="80" title="<?=$rows;?>"><? echo $floor_arr[$rows]; ?></th>
										<?

									}
								?>
								<th width="80" >Total</th>
								<?
									foreach ($print_floor_arr[$company_val][222] as $rows)
									{
										?>
										<th width="80" title="<?=$rows;?>"><? echo $floor_arr[$rows]; ?></th>
										<?

									}
								?>
								<th width="80" >Total</th>
								<?
									foreach ($print_floor_arr[$company_val][315] as $rows)
									{
										?>
										<th width="80" title="<?=$rows;?>"><? echo $floor_arr[$rows]; ?></th>
										<?
									}
								?>
								<th width="80" >Total</th>
								<?
									foreach ($prod_floor_arr[$company_val][4] as $rows)
									{
										?>
										<th width="80" title="<?=$rows;?>"><? echo $floor_arr[$rows]; ?></th>
										<?

									}
								?>
								<th width="80" >Total</th>
								<?
									foreach ($prod_floor_arr[$company_val][5] as $rows)
									{

											?>
											<th width="80" title="<?=$rows;?>"><? echo $floor_arr[$rows]; ?></th>
											<?
									}
								?>
								<th width="80">Total</th>
								<th width="80" >Helper</th>
								<th width="80">Operator</th>
								<th width="80">Total Man Power</th>
								<th width="80" >Day Target</th>
								<th width="80" >Achv%</th>
								<th width="80">Eff%</th>
								<?
									foreach ($prod_floor_arr[$company_val][7] as $rows)
									{
										?>
											<th width="80" title="<?=$rows;?>"><? echo $floor_arr[$rows]; ?></th>
										<?
									}
								?>
								<th width="80" >Total</th>
								<?
									foreach ($prod_floor_arr[$company_val][15] as $rows)
									{
										?>
											<th width="80" title="<?=$rows;?>"><? echo $floor_arr[$rows]; ?></th>
										<?
									}
								?>
								<th width="80" >Total</th>
								<?
									foreach ($prod_floor_arr[$company_val][11] as $rows)
									{
										?>
											<th width="80" title="<?=$rows;?>"><? echo $floor_arr[$rows]; ?></th>
										<?
									}
								?>
								<th width="80" >Total</th>
								<?
									foreach ($prod_floor_arr[$company_val][8] as $rows)
									{
										?>
											<th width="80" title="<?=$rows;?>"><? echo $floor_arr[$rows]; ?></th>
										<?
									}
								?>
								<th width="80" >Total</th>

								<th width="80" >Total Goods</th>
								<th width="80" >Total</th>
								<th width="80" >Total Value$</th>

								<th width="80" >Total Goods</th>
								<th width="80" >Total Damage</th>

							</tr>
						</thead>
						<tbody>
							<?
								ksort($data_array[$company_val]);
								$grad_toal_helper=0;
								$grad_toal_operator=0;
								$grad_toal_man_power=0;
								$grad_toal_day_tr=0;
								$grad_toal_achv=0;
								$grad_toal_eff=0;
								$grad_toal_goods=0;
								$grad_total_values=0;
								$grad_toal_left_over_goods=0;
								$grad_toal_left_over_damage=0;

								foreach ($data_array[$company_val]  as $location_key=>$location_val)
								{
									ksort($location_val);
									foreach ($location_val as $date_key => $r)
									{
									// 	$total_achv += $resource_data_array[$com_key][$lc_key][$date_key]['achv'];
									// 	echo $date_key."uuu";die;
									// print_r()
										if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										?>
												<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>">

													<td width="150" align="left"><p><?=$company_library[$company_val]?></p></td>

													<td width="130" align="left"><?=$location_library[$location_key]?></td>

													<td  width="100" align="left"><?=date('d-M-Y',($date_key))?></td>
													<?
														$total_lay=0;

														foreach ($cut_lay_floor_array[$company_val] as $rows)
														{

															?>
																<td width="80" align="right"><?=number_format($r[$rows][0],0); ?></td>

															<?

															$total_lay+=$r[$rows][0];

														}
													?>
													<td width="80" align="right"><strong><? echo number_format($total_lay,0); ?></strong></td>

													<?
														$total_qc=0;
														foreach ($prod_floor_arr[$company_val][1] as $rows)
														{
															?>
															<td width="80" align="right"><?=number_format($r[$rows][1],0); ?></td>

															<?
															$total_qc+=$r[$rows][1];

														}
													?>

													<td width="80" align="right"><strong><? echo number_format($total_qc,0); ?></strong></td>

													<?
														$total_printing=0;
														foreach ($print_floor_arr[$company_val][222] as $rows)
														{
															?>
																<td width="80" align="right"><?=number_format($r[$rows][222],0); ?></td>

															<?
															$total_printing+=$r[$rows][222];

														}
													?>

													<td width="80" align="right"><strong><? echo number_format($total_printing,0); ?></strong></td>
													<?
														$total_emb=0;
														foreach ($print_floor_arr[$company_val][315] as $rows)
														{
															?>
																<td width="80" align="right"><?=number_format($r[$rows][315],0); ?></td>

															<?
															$total_emb+=$r[$rows][315];

														}
													?>

													<td width="80" align="right"><strong><? echo number_format($total_emb,0); ?></strong></td>

													<?
														$total_sew_in=0;
														foreach ($prod_floor_arr[$company_val][4] as $rows)
														{
															?>
																<td width="80" align="right"><?=number_format($r[$rows][4],0); ?></td>

															<?
															$total_sew_in+=$r[$rows][4];

														}
													?>

													<td width="80" align="right"><strong><? echo number_format($total_sew_in,0); ?></strong></td>
													<?
														$total_sew_out=0;
														foreach ($prod_floor_arr[$company_val][5] as $rows)
														{
															?>
																<td width="80" align="right"><?=number_format($r[$rows][5],0); ?></td>

															<?
															$total_sew_out+=$r[$rows][5];

														}
													?>

													<td width="80" align="right"><strong><? echo number_format($total_sew_out,0); ?></strong></td>
													<td width="80" align="right"><p><?=$prod_rce_arr[$company_val][$location_key][$date_key]['helper']?></p></td>
													<td width="80" align="right"><p><?=$prod_rce_arr[$company_val][$location_key][$date_key]['operator']?></p></td>
													<td width="80" align="right"><p><?=$prod_rce_arr[$company_val][$location_key][$date_key]['man_power']?></p></td>
													<td width="80" align="right"><p><?=$prod_rce_arr[$company_val][$location_key][$date_key]['tpd'];?></p></td>
													<td width="80" align="right"><p><p><?=number_format (($resource_data_array[$company_val][$location_key][$date_key]['achv']/$resource_data_array[$company_val][$location_key][$date_key]['line_count']),2);?>%</p></td>
													<td width="80" align="right"><p><?=number_format(($resource_data_array[$company_val][$location_key][$date_key]['eff']/$resource_data_array[$company_val][$location_key][$date_key]['line_count']),2);?>%</p></td>

													<?
														$total_iron=0;
														foreach ($prod_floor_arr[$company_val][7] as $rows)
														{
															?>
																<td width="80" align="right"><?=number_format($r[$rows][7],0); ?></td>

															<?
															$total_iron+=$r[$rows][7];

														}
													?>

													<td width="80" align="right"><strong><? echo number_format($total_iron,0); ?></strong></td>

													<?
															$total_hang_tag=0;
															foreach ($prod_floor_arr[$company_val][15] as $rows)
															{
																?>
																	<td width="80" align="right"><?=number_format($r[$rows][15],0); ?></td>

																<?
																$total_hang_tag+=$r[$rows][15];

															}
														?>

													<td width="80" align="right"><strong><? echo number_format($total_hang_tag,0); ?></strong></td>
													<?
															$total_poly=0;
															foreach ($prod_floor_arr[$company_val][11] as $rows)
															{
																?>
																	<td width="80" align="right"><?=number_format($r[$rows][11],0); ?></td>

																<?
																$total_poly+=$r[$rows][11];

															}
														?>

													<td width="80" align="right"><strong><? echo number_format($total_poly,0); ?></strong></td>

													<?
															$total_packing=0;
															foreach ($prod_floor_arr[$company_val][8] as $rows)
															{
																?>
																	<td width="80" align="right"><?=number_format($r[$rows][8],0); ?></td>

																<?
																$total_packing+=$r[$rows][8];

															}
														?>

													<td width="80" align="right"><strong><? echo number_format($total_packing,0); ?></strong></td>

													<td width="80" align="right"><p><?=number_format($gmt_shipment_arr[$company_val][$location_key][$date_key]['ship_qty'],0)?></p></td>
													<td width="80" align="right"><p><?=number_format($gmt_shipment_arr[$company_val][$location_key][$date_key]['ship_qty'],0)?></p></td>
													<td width="80" align="right"><p><?= number_format($gmt_shipment_arr[$company_val][$location_key][$date_key]['ship_value'],0)?></p></td>

													<td width="80" align="right"><p><?=$gmt_left_over_arr[$company_val][$location_key][$date_key][1];?></p></td>
													<td width="80" align="right"><p><?=$gmt_left_over_arr[$company_val][$location_key][$date_key][2];?></p></td>

												</tr>
												<?
												$j++;
												$grad_toal_helper+=$prod_rce_arr[$company_val][$location_key][$date_key]['helper'];

												$grad_toal_operator+=$prod_rce_arr[$company_val][$location_key][$date_key]['operator'];
												$grad_toal_man_power+=$prod_rce_arr[$company_val][$location_key][$date_key]['man_power'];
												$grad_toal_day_tr+=$prod_rce_arr[$company_val][$location_key][$date_key]['tpd'];
												$grad_toal_achv+=($resource_data_array[$company_val][$location_key][$date_key]['achv']/$resource_data_array[$company_val][$location_key][$date_key]['line_count']);
												$grad_toal_eff+=($resource_data_array[$company_val][$location_key][$date_key]['eff']/$resource_data_array[$company_val][$location_key][$date_key]['line_count']);
												$grad_toal_goods+=($gmt_shipment_arr[$company_val][$location_key][$date_key]['ship_qty']);
												$grad_total_values +=($gmt_shipment_arr[$company_val][$location_key][$date_key]['ship_value']);
												$grad_toal_left_over_goods +=$gmt_left_over_arr[$company_val][$location_key][$date_key][1];
												$grad_toal_left_over_damage +=$gmt_left_over_arr[$company_val][$location_key][$date_key][2];


											?>
										<?
									}
								}
							?>
						</tbody>

						<tfoot>
							<tr>
								<th style="text-align:center" colspan="3"><strong>Company Total</strong> </th>

									<?
										$grand_total_cut_lay=0;
										foreach ($cut_lay_floor_array[$company_val] as $rows)
										{
											?>
												<th width="80" align="right"><b><?=number_format($cut_lay_floor_wise_total[$company_val][$rows][0]); ?></th></b>

											<?
											$grand_total_cut_lay+=$cut_lay_floor_wise_total[$company_val][$rows][0];

										}

									?>
									<th width="80" align="right"><b><?=number_format($grand_total_cut_lay)?></b></th>
									<?

										$grand_total_cut_qc=0;
										foreach ($prod_floor_arr[$company_val][1] as $rows )
										{
											?>
												<th width="80" align="right"><b><?=number_format($prod_floor_wise_total[$company_val][$rows][1]); ?></b></th>

											<?
											$grand_total_cut_qc+=$prod_floor_wise_total[$company_val][$rows][1];

										}
									?>
									<th width="80" align="right"><b><?=number_format($grand_total_cut_qc)?></b></th>

									<?
										$grand_total_cut_print=0;
										foreach ($print_floor_arr[$company_val][222] as $rows )
										{
											?>
												<th width="80" align="right"><b><?=number_format($print_floor_wise_total[$company_val][$rows][222]); ?></b></th>

											<?
											$grand_total_cut_print+=$print_floor_wise_total[$company_val][$rows][222];

										}
									?>
									<th width="80" align="right"><b><?=number_format($grand_total_cut_print)?></b></th>

									<?
										$grand_total_cut_emb=0;
										foreach ($print_floor_arr[$company_val][315] as $rows )
										{
											?>
												<th width="80" align="right"><b><?=number_format($print_floor_wise_total[$company_val][$rows][315]); ?></b></th>

											<?
											$grand_total_cut_emb+=$print_floor_wise_total[$company_val][$rows][315];

										}
									?>
									<th width="80" align="right"><b><?=number_format($grand_total_cut_emb)?></b></th>

									<?
										$grand_total_cut_sew_in=0;
										foreach ($prod_floor_arr[$company_val][4] as $rows )
										{
											?>
												<th width="80" align="right"><b><?=number_format($prod_floor_wise_total[$company_val][$rows][4]); ?></b></th>

											<?
											$grand_total_cut_sew_in+=$prod_floor_wise_total[$company_val][$rows][4];

										}
									?>
									<th width="80" align="right"><b><?=number_format($grand_total_cut_sew_in)?></b></th>

									<?
									$grand_total_cut_sew_out=0;
										foreach ($prod_floor_arr[$company_val][5] as $rows )
										{
											?>
												<th width="80" align="right"><b><?=number_format($prod_floor_wise_total[$company_val][$rows][5]); ?></b></th>

											<?
											$grand_total_cut_sew_out+=$prod_floor_wise_total[$company_val][$rows][5];

										}
									?>
									<th width="80" align="right"><b><?=number_format($grand_total_cut_sew_out)?></b></th>

									<th width="80" align="right"><b><?=$grad_toal_helper;?></b></th>

									<th width="80" align="right"><b><?=$grad_toal_operator;?></b></th>

									<th width="80" align="right"><b><?=$grad_toal_man_power;?></b></th>

									<th width="80" align="right"><b><?=$grad_toal_day_tr;?></b></th>

									<th width="80" align="right"><b><?=number_format($grad_toal_achv,2);?>%</b></th>
									<th width="80" align="right"><?=number_format($grad_toal_eff,2)?>%</th>

									<?
										$grand_total_iron=0;
										foreach ($prod_floor_arr[$company_val][7] as $rows )
										{
											?>
												<th width="80" align="right"><b><?=number_format($prod_floor_wise_total[$company_val][$rows][7]); ?></b></th>

											<?
											$grand_total_iron+=$prod_floor_wise_total[$company_val][$rows][7];

										}
									?>

									<th width="80" align="right"><b><?=number_format($grand_total_iron)?></b></th>

									<?
										$grand_total_hang_tag=0;
										foreach ($prod_floor_arr[$company_val][15] as $rows )
										{
											?>
												<th width="80" align="right"><b><?=number_format($prod_floor_wise_total[$company_val][$rows][15]); ?></b></th>

											<?
											$grand_total_hang_tag+=$prod_floor_wise_total[$company_val][$rows][15];

										}
									?>
									<th width="80" align="right"><b><?=number_format($grand_total_hang_tag)?></b></th>

									<?

										$grand_total_poly=0;
										foreach ($prod_floor_arr[$company_val][11] as $rows )
										{
											?>
												<th width="80" align="right"><b><?=number_format($prod_floor_wise_total[$company_val][$rows][11]); ?></b></th>

											<?
											$grand_total_poly+=$prod_floor_wise_total[$company_val][$rows][11];

										}
									?>

									<th width="80" align="right"><b><?=number_format($grand_total_poly)?></b></th>

									<?

										$grand_total_packing=0;
										foreach ($prod_floor_arr[$company_val][8] as $rows )
										{
											?>
												<th width="80" align="right"><b><?=number_format($prod_floor_wise_total[$company_val][$rows][8]); ?></th>

											<?
											$grand_total_packing+=$prod_floor_wise_total[$company_val][$rows][8];

										}
									?>
									<th width="80" align="right"><b><?=number_format($grand_total_packing,0)?></b></th>

									<th width="80" align="right"><b><?= number_format($grad_toal_goods,0)?></b></th>

									<th width="80" align="right"><b><?= number_format($grad_toal_goods,0)?></b></th>

									<th width="80" align="right"><b><?=number_format($grad_total_values,0)?><b></td>

									<th width="80" align="right"><b><?=number_format($grad_toal_left_over_goods);?></b></th>

									<th width="80" align="right"><b><?=number_format($grad_toal_left_over_damage);?></b></th>

							</tr>
						</tfoot>
					</table>


					<?

				}
								?>
			</fieldset>
		<?
	}
	elseif ($type==8) // show6 button   ###GBL **REF FROM: 1**
	{
	    $floor_arr = return_library_array("select id,floor_name from  lib_prod_floor ","id","floor_name");
		$lineArr = return_library_array("select a.id,a.line_name from lib_sewing_line a","id","line_name");
		$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

		if ($cbo_location==0) $location_id =""; else $location_id =" and a.location=$cbo_location ";
		if($db_type==0)
		{
			if( $date_from==0 && $date_to==0 ) $production_date=""; else $production_date= " and a.production_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
		}
		else
		{
			if( $date_from==0 && $date_to==0 ) $production_date=""; else $production_date= " and a.production_date between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'";
		}

		//***************************************************************************************************************************
		$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
		order by sewing_line_serial");
		foreach($lineDataArr as $lRow)
		{
			$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
			$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
			$lastSlNo=$lRow[csf('sewing_line_serial')];
		}


		$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$comapny_id and shift_id=1 and pr_date between $txt_date_from and $txt_date_to and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");

		if($min_shif_start=="")
		{
			echo "<p style='font-size:20px', align='center'>No Line Engage for the selected Date.Please Check Actual Production Resource Entry.<p/>";die;

		}


		//==============================shift time===================================================================================================
		$start_time_arr=array();

		$start_time_data_arr=sql_select("SELECT company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time,TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($comapny_id) and  shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");


		foreach($start_time_data_arr as $row)
		{
			$start_time_arr[$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
			$start_time_arr[$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];
		}

		$prod_start_hour=$start_time_arr[1]['pst'];
		$global_start_lanch=$start_time_arr[1]['lst'];
		if($prod_start_hour=="") $prod_start_hour="08:00";
		$start_time=explode(":",$prod_start_hour);
		$hour=substr($start_time[0],1,1); $minutes=$start_time[1]; $last_hour=23;
		$lineWiseProd_arr=array(); $prod_arr=array(); $start_hour_arr=array();
		$start_hour=$prod_start_hour;
		$start_hour_arr[$hour]=$start_hour;
		for($j=$hour;$j<$last_hour;$j++)
		{
			$start_hour=add_time($start_hour,60);
			$start_hour_arr[$j+1]=substr($start_hour,0,5);
		}
		//echo $pc_date_time;die;
		$start_hour_arr[$j+1]='23:59';
		if($prod_start_hour>$min_shif_start)  $prod_start_hour=$min_shif_start;
		$actual_date=date("Y-m-d");
		$actual_production_date=date("Y-m-d",strtotime(str_replace("'","",$txt_date_from)));
		$actual_time=substr(date("Y-m-d H:i:s",strtotime($pc_date_time)),11,2);
		$acturl_hour_minute=date("H:i",strtotime($pc_date_time));
		$generated_hourarr=array();
		$first_hour_time=explode(":",$min_shif_start);
		$hour_line=substr($first_hour_time[0],1,1); $minutes_one=$start_time[1];
		$line_start_hour_arr[$hour_line]=$min_shif_start;

		for($l=$hour_line;$l<$last_hour;$l++)
		{
			$min_shif_start=add_time($min_shif_start,60);
			$line_start_hour_arr[$l+1]=substr($min_shif_start,0,5);
		}

		$line_start_hour_arr[$j+1]='23:59';
		$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_id and variable_list=23 and is_deleted=0
		and status_active=1");

		//if(str_replace("'","",$cbo_company_id)==0) $company_name=""; else $company_name="and a.company_id=".str_replace("'","",$cbo_company_id)."";
		if(str_replace("'","",$cbo_company_id)==0) $company_name=""; else $company_name="and a.serving_company=".str_replace("'","",$cbo_company_id)."";

		if(str_replace("'","",$cbo_location_id)==0)
		{
			$subcon_location="";
			$location="";
		}
		else
		{
			$location=" and a.location=".str_replace("'","",$cbo_location_id)."";
			$subcon_location=" and a.location_id=".str_replace("'","",$cbo_location_id)."";
		}
		$cbo_floor_id=str_replace("'","",$cbo_floor_id);
		if($cbo_floor_id=="") $floor=""; else $floor="and a.floor_id in(".$cbo_floor_id.")";
	    if(str_replace("'","",$hidden_line_id)==0)
		{
			$line="";
			$subcon_line="";
		}
		else
		{
			$subcon_line="and a.line_id in(".str_replace("'","",$hidden_line_id).")";
			$line="and a.sewing_line in(".str_replace("'","",$hidden_line_id).")";
		}
		$cbo_no_prod_type=str_replace("'","",$cbo_no_prod_type);
		$file_no=str_replace("'","",$txt_file_no);
		$ref_no=str_replace("'","",$txt_ref_no);
		if($file_no!="") $file_cond="and c.file_no=$file_no";else $file_cond="";
		if($ref_no!="") $ref_cond="and c.grouping='$ref_no'";else $ref_cond="";
		//echo $file_cond;


		/* =============================================================================================/
		/									Prod Resource Data											/
		/============================================================================================= */
		if($prod_reso_allo[0]==1)
		{
			$prod_resource_array=array();

			$dataArray_sql=sql_select("SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id=$comapny_id and b.pr_date between $txt_date_from and $txt_date_to and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0");
			foreach($dataArray_sql as $val)
			{
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['man_power']=$val[csf('man_power')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['operator']=$val[csf('operator')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['helper']=$val[csf('helper')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['terget_hour']=$val[csf('target_per_hour')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['working_hour']=$val[csf('working_hour')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['tpd']=$val[csf('target_per_hour')]*$val[csf('working_hour')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_start']=$val[csf('from_date')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_end']=$val[csf('to_date')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['capacity']=$val[csf('capacity')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust']=$val[csf('smv_adjust')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];
			}
			// print_r($prod_resource_array);die();
			if(str_replace("'","",trim($txt_date_from))==""){$pr_date_con="";}else{$pr_date_con=" and b.pr_date between $txt_date_from and $txt_date_to";}


			$dataArray=sql_select("SELECT a.id,b.pr_date,d.shift_id,TO_CHAR(d.prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR( d.lunch_start_time,'HH24:MI') as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 $pr_date_con");


			$line_number_arr=array();
			foreach($dataArray as $val)
			{
				$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['shift_id']=$val[csf('shift_id')];
				$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['prod_start_time']=$val[csf('prod_start_time')];
				$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['lunch_start_time']=$val[csf('lunch_start_time')];
			}
		}
	 	//***************************************************************************************************

		$manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");



		$prod_start_cond="TO_CHAR(prod_start_time,'DD-MON-YYYY HH24:MI')";

		$variable_start_time_arr='';
		$prod_start_time=sql_select("SELECT $prod_start_cond as prod_start_time from variable_settings_production where company_name=$cbo_company_id and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");
		foreach($prod_start_time as $row)
		{
			$ex_time=explode(" ",$row[csf('prod_start_time')]);
			if($db_type==0) $variable_start_time_arr=$row[csf('prod_start_time')];
			else if($db_type==2) $variable_start_time_arr=$ex_time[1];
		}//die;
		//echo $variable_start_time_arr;
		unset($prod_start_time);
		$current_date_time=date('d-m-Y H:i');
		$variable_date=change_date_format(str_replace("'","",$txt_date_from)).' '.$variable_start_time_arr;
		//echo $variable_date.'='.$current_date_time;
		$datediff_=datediff("n",$variable_date,$current_date_time);

		$ex_date_time=explode(" ",$current_date_time);
		$current_date=$ex_date_time[0];
		$current_time=$ex_date_time[1];
		$ex_time=explode(":",$current_time);

		$search_prod_date=change_date_format(str_replace("'","",$txt_date_from));
		$current_eff_min=($ex_time[0]*60)+$ex_time[1];
		//echo $current_date.'='.$search_prod_date;
		$variable_time= explode(":",$variable_start_time_arr);
		$vari_min=($variable_time[0]*60)+$variable_time[1];
		$difa_time=explode(".",number_format(($current_eff_min-$vari_min)/60,2));//datediff("",$ctime,$variable_start_time_arr);
		$dif_time=number_format($datediff_/60,2);
		$dif_hour_min=date("H", strtotime($dif_time));


		/* =============================================================================================/
		/											SMV Source											/
		/============================================================================================= */
	   	$smv_source=return_field_value("smv_source","variable_settings_production","company_name =$comapny_id and variable_list=25 and   status_active=1 and is_deleted=0");


		 //echo $smv_source;die;
	    if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;

	    if($smv_source==3)
		{
			$sql_item="SELECT b.id, a.sam_style, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0
		and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$resultItem=sql_select($sql_item);

			foreach($resultItem as $itemData)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
			}
		}
		else
		{
			$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost,c.smv_set from wo_po_details_master a,wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.company_name in($manufacturing_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$resultItem=sql_select($sql_item);

			foreach($resultItem as $itemData)
			{
				if($smv_source==1)
				{
					$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
				}
				if($smv_source==2)
				{
					$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs_precost')];
				}
			}
		}
		// echo $sql_item;


		$pr_date= str_replace("'", "", $txt_date_from);
		$pr_date_old=explode("-",str_replace("'","",$txt_date_from));
		$month=strtoupper($pr_date_old[1]);
		$year=substr($pr_date_old[2],2);
		$pr_date=$pr_date_old[0]."-".$month."-".$year;


		$i=1; $grand_total_good=0; $grand_alter_good=0; $grand_total_reject=0;
		$html="";
		$floor_html="";
	    $check_arr=array();
		/* =============================================================================================/
		/									Prod Inhouse Data											/
		/============================================================================================= */

		$sql="SELECT  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name  as buyer_name,b.style_ref_no,b.job_no, a.po_break_down_id as po_id, a.item_number_id,c.po_number as po_number,c.file_no,c.unit_price,c.grouping as ref,sum(d.production_qnty) as good_qnty,";
		$first=1;
		for($h=$hour;$h<$last_hour;$h++)
		{
			$bg=$start_hour_arr[$h];
			$end=substr(add_time($start_hour_arr[$h],60),0,5);
			$prod_hour="prod_hour".substr($bg,0,2);
			if($first==1)
			{
				$sql.="sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN production_qnty else 0 END) AS $prod_hour,";
			}
			else
			{
				$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5
				THEN production_qnty else 0 END) AS $prod_hour,";
			}
			$first++;
		}
		$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN production_qnty else 0 END) AS prod_hour23
		FROM  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a ,pro_garments_production_dtls d
		WHERE a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_name $location $floor $line $buyer_id_cond  $production_date $file_cond $ref_cond
		GROUP BY b.job_no, a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name,b.style_ref_no,a.item_number_id,c.po_number,c.unit_price,c.file_no,c.grouping
		ORDER BY a.location,a.floor_id,a.sewing_line";

		//echo $sql;die;
		$sql_resqlt=sql_select($sql);
		$production_data_arr=array();
		$production_po_data_arr=array();
		$production_serial_arr=array(); $reso_line_ids=''; $all_po_id="";
		$active_days_arr=array();
		$duplicate_date_arr=array();
		$po_id_array=array();
		foreach($sql_resqlt as $val)
		{
			$po_id_array[$val['PO_ID']] = $val['PO_ID'];
			if($val['PROD_RESO_ALLO']==1)
			{
				$sewing_line_id=$prod_reso_arr[$val['SEWING_LINE']];
				$reso_line_ids.=$val['SEWING_LINE'].',';
			}
			else
			{
				$sewing_line_id=$val['SEWING_LINE'];
			}

			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id]=$slNo;
			}
			else $slNo=$lineSerialArr[$sewing_line_id];
			$production_serial_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$slNo][$val['SEWING_LINE']]=$val['SEWING_LINE'];

			$line_start=$line_number_arr[$val['SEWING_LINE']][$val['PRODUCTION_DATE']]['prod_start_time'];
			if($line_start!="")
			{
				$line_start_hour=substr($line_start,0,2);
				if(substr($line_start_hour,0,1)==0)  $line_start_hour=substr($line_start_hour,1,1);
			}
			else
			{
				$line_start_hour=$hour;
			}

		 	for($h=$hour;$h<$last_hour;$h++)
			{
				$prod_hour="prod_hour".substr($start_hour_arr[$h],0,2)."";
				$production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']][$prod_hour]+=$val[csf($prod_hour)];

				if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
				{
					if( $h>=$line_start_hour && $h<=$actual_time)
					{
						$production_po_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']][$val['PO_ID']]+=$val[csf($prod_hour)];
					}
				}

				if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date))
				{
					$production_po_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']][$val['PO_ID']]+=$val[csf($prod_hour)];
				}
			}

			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
			{
				if( $h>=$line_start_hour && $h<=$actual_time)
				{
					$production_po_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']][$val['PO_ID']]+=$val['PROD_HOUR23'];
				}
			}
			else
			{
				$production_po_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']][$val['PO_ID']]+=$val['PROD_HOUR23'];
			}

		 	$production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']]['prod_hour23']+=$val['PROD_HOUR23'];
			$production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']]['prod_reso_allo']=$val['PROD_RESO_ALLO'];

		 	if($production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']]['buyer_name']!="")
			{
				$production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']]['buyer_name'].=",".$val['BUYER_NAME'];
			}
		 	else
			{
				$production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']]['buyer_name']=$val['BUYER_NAME'];
			}

		 	if($production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']]['po_number']!="")
			{
				$production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']]['po_number'].=",".$val['PO_NUMBER'];
				$production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']]['job_no'].=",".$val['JOB_NO'];
				$production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']]['po_id'].=",".$val['PO_ID'];
				$production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']]['style'].=",".$val['STYLE_REF_NO'];
				$production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']]['file'].=",".$val['FILE_NO'];
				$production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']]['ref'].=",".$val['REF'];
			}
		 	else
			{
				$production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']]['po_number']=$val['PO_NUMBER'];
				$production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']]['job_no']=$val['JOB_NO'];
				$production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']]['po_id']=$val['PO_ID'];
				$production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']]['style']=$val['STYLE_REF_NO'];
				$production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']]['file']=$val['FILE_NO'];
				$production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']]['ref']=$val['REF'];
			}
			$fob_rate_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']][$val['PO_ID']]['rate']=$val['UNIT_PRICE'];

			if($production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']]['item_number_id']!="")
			{
				$production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']]['item_number_id'].="****".$val['PO_ID']."**".$val['ITEM_NUMBER_ID'];
			}
			else
			{
				 $production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']]['item_number_id']=$val['PO_ID']."**".$val['ITEM_NUMBER_ID'];
			}
			$production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']]['quantity']+=$val['GOOD_QNTY'];
			$production_data_arr_qty[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']][$val['PO_ID']][$val['ITEM_NUMBER_ID']]['quantity']+=$val['GOOD_QNTY'];

			if($all_po_id=="") $all_po_id=$val['PO_ID']; else $all_po_id.=",".$val['PO_ID'];
		}
		// ============================================================================================================
		//												DELETE ORDER ID FROM TEMP ENGINE
		// ============================================================================================================
		$con = connect();
		execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 125 and ref_from =1");
		oci_commit($con);

		// ============================================================================================================
		//												INSERT ORDER_ID INTO TEMP ENGINE
		// ============================================================================================================
		fnc_tempengine("gbl_temp_engine", $user_id, 125, 1,$po_id_array, $empty_arr);

		// ============================================================================================================
		//												TEMP ENGINE COMMON CONDITION
		// ============================================================================================================
		$tmp_table = ", gbl_temp_engine tmp ";
		$tmp_po_common_cond = " and tmp.entry_form=125 and tmp.ref_from=1 and tmp.user_id=$user_id ";

		$tmp_tbl_join_cnd_0 = " and a.po_break_down_id = tmp.ref_val";
		$day_run_sql=sql_select("SELECT a.po_break_down_id, min(a.production_date) as min_date from pro_garments_production_mst a $tmp_table
								where $tmp_tbl_join_cnd_0  $tmp_po_common_cond a.production_type=4 status=1 group by a.po_break_down_id");
		$mindateArr=array();
		foreach($day_run_sql as $row)
		{
			$mindateArr[$row[csf('po_break_down_id')]]=strtotime($row[csf('min_date')]);
		}
		unset($day_run_sql);

		/* =============================================================================================/
		/										Active PO Data											/
		/============================================================================================= */
		$tmp_tbl_join_cnd_1 = " and c.id = tmp.ref_val";
	    $po_active_sql="SELECT a.floor_id,a.sewing_line,a.production_date,a.po_break_down_id,a.item_number_id from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a $tmp_table where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no $tmp_tbl_join_cnd_1  and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $company_name $location $floor $line $buyer_id_cond  $file_cond $ref_cond $tmp_po_common_cond  group by  a.floor_id,a.sewing_line,a.production_date ,a.po_break_down_id,a.item_number_id";
	    // echo $po_active_sql;die;
		foreach(sql_select($po_active_sql) as $vals)
		{
			$prod_dates=$vals['PRODUCTION_DATE'];
			if($duplicate_date_arr[$vals['PO_BREAK_DOWN_ID']][$vals['ITEM_NUMBER_ID']][$prod_dates]=="")
			{
				$active_days_arr[$vals['FLOOR_ID']][$vals['SEWING_LINE']]+=1;
				$active_days_arr_powise[$vals['PO_BREAK_DOWN_ID']][$vals['ITEM_NUMBER_ID']]+=1;
				$duplicate_date_arr[$vals['PO_BREAK_DOWN_ID']][$vals['ITEM_NUMBER_ID']][$prod_dates]=$prod_dates;
			}

		}
		//print_r($duplicate_date_arr);
		$tmp_tbl_join_cnd_2 = " and b.id = tmp.ref_val";
		$sql_item_rate="SELECT b.id ,c.item_number_id, c.order_quantity, c.order_total from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c $tmp_table  where b.job_no_mst=a.job_no $tmp_tbl_join_cnd_2 and b.id=c.po_break_down_id and b.job_no_mst=c.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1  $file_cond $ref_cond  $tmp_po_common_cond";
		// echo $sql_item_rate; die;
		$resultRate=sql_select($sql_item_rate);
		$item_po_array=array();
		foreach($resultRate as $row)
		{
			$item_po_array[$row['ID']][$row['ITEM_NUMBER_ID']]['qty']+=$row['ORDER_QUANTITY'];
			$item_po_array[$row['ID']][$row['ITEM_NUMBER_ID']]['amt']+=$row['ORDER_TOTAL'];
		}

		/* =============================================================================================/
		/										Prod Subcon Data										/
		/============================================================================================= */


		$sql_sub_contuct= "select  a.company_id, a.location_id, a.floor_id,d.floor_serial_no,e.sewing_line_serial, a.production_date, a.line_id,b.party_id  as buyer_name,a.prod_reso_allo,a.order_id,c.order_no as po_number,c.cust_style_ref, b.subcon_job as job_no,  max(c.smv) as smv,a.prod_reso_allo,sum(a.production_qnty) as good_qnty,";
		$first=1;
		for($h=$hour;$h<$last_hour;$h++)
		{
			$bg=$start_hour_arr[$h];
			$end=substr(add_time($start_hour_arr[$h],60),0,5);
			$prod_hour="prod_hour".substr($bg,0,2);
			if($first==1)
			{
				$sql_sub_contuct.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,";
			}
			else
			{
				$sql_sub_contuct.="sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')>'$bg' and TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,";
			}
			$first++;
		}

		$sql_sub_contuct.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]'	and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a left join lib_prod_floor d on a.floor_id=d.id  and d.is_deleted=0 left join lib_sewing_line e on a.line_id=e.id and e.is_deleted=0 where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id=$comapny_id $subcon_location $floor $subcon_line $production_date group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date,  b.subcon_job,a.line_id,b.party_id,c.order_no,c.cust_style_ref,e.sewing_line_serial,a.prod_reso_allo order by a.location_id, a.floor_id,e.sewing_line_serial,a.prod_reso_allo";


		//echo $sql_sub_contuct;die;
		$sub_result=sql_select($sql_sub_contuct);
		$subcon_order_smv=array();
		foreach($sub_result as $subcon_val)
		{

			if($val['PROD_RESO_ALLO']==1)
			{
				$sewing_line_id=$prod_reso_arr[$subcon_val['SEWING_LINE']];
			}
			else
			{
				$sewing_line_id=$subcon_val['SEWING_LINE'];
			}

			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id]=$slNo;
			}
			else $slNo=$lineSerialArr[$sewing_line_id];

			$production_po_data_arr[$subcon_val['PRODUCTION_DATE']][$subcon_val['FLOOR_ID']][$subcon_val['LINE_ID']][$subcon_val['ORDER_ID']]+=$subcon_val['GOOD_QNTY'];
			if($production_data_arr[$subcon_val['PRODUCTION_DATE']][$subcon_val['FLOOR_ID']][$subcon_val['LINE_ID']]['buyer_name']!="")
			{
				$production_data_arr[$subcon_val['PRODUCTION_DATE']][$subcon_val['FLOOR_ID']][$subcon_val['LINE_ID']]['buyer_name'].=",".$subcon_val['BUYER_NAME'];
			}
			else
			{
				$production_data_arr[$subcon_val['PRODUCTION_DATE']][$subcon_val['FLOOR_ID']][$subcon_val['LINE_ID']]['buyer_name']=$subcon_val['BUYER_NAME'];
			}

			if($production_data_arr[$subcon_val['PRODUCTION_DATE']][$subcon_val['FLOOR_ID']][$subcon_val['LINE_ID']]['po_number']!="")
			{
				$production_data_arr[$subcon_val['PRODUCTION_DATE']][$subcon_val['FLOOR_ID']][$subcon_val['LINE_ID']]['po_number'].=",".$subcon_val['PO_NUMBER'];
				$production_data_arr[$subcon_val['PRODUCTION_DATE']][$subcon_val['FLOOR_ID']][$subcon_val['LINE_ID']]['job_no'].=",".$subcon_val['JOB_NO'];
				$production_data_arr[$subcon_val['PRODUCTION_DATE']][$subcon_val['FLOOR_ID']][$subcon_val['LINE_ID']]['style'].=",".$subcon_val['CUST_STYLE_REF'];
			}
			else
			{
				$production_data_arr[$subcon_val['PRODUCTION_DATE']][$subcon_val['FLOOR_ID']][$subcon_val['LINE_ID']]['po_number']=$subcon_val['PO_NUMBER'];
				$production_data_arr[$subcon_val['PRODUCTION_DATE']][$subcon_val['FLOOR_ID']][$subcon_val['LINE_ID']]['job_no']=$subcon_val['JOB_NO'];
				$production_data_arr[$subcon_val['PRODUCTION_DATE']][$subcon_val['FLOOR_ID']][$subcon_val['LINE_ID']]['style']=$subcon_val['CUST_STYLE_REF'];
			}

			if($production_data_arr[$subcon_val['PRODUCTION_DATE']][$subcon_val['FLOOR_ID']][$subcon_val['LINE_ID']]['order_id']!="")
			{
				$production_data_arr[$subcon_val['PRODUCTION_DATE']][$subcon_val['FLOOR_ID']][$subcon_val['LINE_ID']]['order_id'].=",".$subcon_val['ORDER_ID'];
			}
			else
			{
				$production_data_arr[$subcon_val['PRODUCTION_DATE']][$subcon_val['FLOOR_ID']][$subcon_val['LINE_ID']]['order_id'].=$subcon_val['ORDER_ID'];
			}
			$subcon_order_smv[$subcon_val['ORDER_ID']]=$subcon_val['SMV'];
			$production_data_arr[$subcon_val['PRODUCTION_DATE']][$subcon_val['FLOOR_ID']][$subcon_val['LINE_ID']]['quantity']+=$subcon_val['GOOD_QNTY'];

		 	$line_start=$line_number_arr[$val['LINE_ID']][$val['PRODUCTION_DATE']]['prod_start_time']	;
		 	if($line_start!="")
		 	{
				$line_start_hour=substr($line_start,0,2);
				if(substr($line_start_hour,0,1)==0)  $line_start_hour=substr($line_start_hour,1,1);
		 	}
			else
		 	{
				$line_start_hour=$hour;
		 	}
			for($h=$hour;$h<=$last_hour;$h++)
			{
				$prod_hour="prod_hour".substr($start_hour_arr[$h],0,2)."";
				$production_data_arr[$subcon_val['PRODUCTION_DATE']][$subcon_val['FLOOR_ID']][$subcon_val['LINE_ID']][$prod_hour]+=$subcon_val[csf($prod_hour)];
				if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
				{
					 if( $h>=$line_start_hour && $h<=$actual_time)
					 {
					 $production_po_data_arr[$subcon_val['PRODUCTION_DATE']][$subcon_val['FLOOR_ID']][$subcon_val['LINE_ID']][$subcon_val['ORDER_ID']]+=$val[csf($prod_hour)];	                 }
				}
				if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
				{
					$production_po_data_arr[$subcon_val['PRODUCTION_DATE']][$subcon_val['FLOOR_ID']][$subcon_val['LINE_ID']][$subcon_val['ORDER_ID']]+=$val[csf($prod_hour)];	            }
			 }
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
			{
				if( $h>=$line_start_hour && $h<=$actual_time)
				{
					$production_po_data_arr[$subcon_val['PRODUCTION_DATE']][$subcon_val['FLOOR_ID']][$subcon_val['LINE_ID']][$subcon_val['ORDER_ID']]+=$val['PROD_HOUR23'];
				}
			}
			else
			{
				$production_po_data_arr[$subcon_val['PRODUCTION_DATE']][$subcon_val['FLOOR_ID']][$subcon_val['LINE_ID']][$subcon_val['ORDER_ID']]+=$val['PROD_HOUR23'];
			}
			$production_data_arr[$subcon_val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['LINE_ID']]['prod_hour23']+=$val['PROD_HOUR23'];
		}
		//For Summary Report New Add No Prodcut
		$cbo_no_prod_type=1;
		if($cbo_no_prod_type==1)
		{

		/* =============================================================================================/
		/									No Production line Start									/
		/============================================================================================= */
		$prod_reso_arr=return_library_array( "SELECT id, line_number from prod_resource_mst",'id','line_number');
		$sql_active_line=sql_select("SELECT sewing_line,sum(production_quantity) as total_production from pro_garments_production_mst  where production_date between $txt_date_from and $txt_date_to and production_type=5 and status_active=1 and is_deleted=0 and prod_reso_allo=1 group by  sewing_line");
		//$actual_line_arr=array();
		foreach($sql_active_line as $inf)
		{
			if(str_replace("","",$inf[csf('sewing_line')])!="")
			{
				//if(str_replace("","",$actual_line_arr)!="") $actual_line_arr.=",";
				//$actual_line_arr.="'".$inf[csf('sewing_line')]."'";
			}
		}
					//echo $actual_line_arr;die;
		$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$comapny_id and variable_list=23 and is_deleted=0 and status_active=1");
		$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
		if(str_replace("'","",$cbo_location_id)==0)
		{
		$location_cond="";
		}
		else
		{
		$location=" and a.location_id=".str_replace("'","",$cbo_location_id)."";
		}

		if(str_replace("'","",$cbo_floor_id)==0) $floor=""; else $floor="and a.floor_id=".str_replace("'","",$cbo_floor_id)."";
		$lin_ids=str_replace("'","",$hidden_line_id);
		$res_line_cond=rtrim($reso_line_ids,",");

			$dataArray_sum=sql_select("SELECT a.id, a.floor_id,1 as prod_reso_allo,2 as type_line, a.line_number as line_no, b.man_power, b.operator,b.pr_date, b.helper, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type,d.prod_start_time from  prod_resource_dtls b,prod_resource_dtls_time d,prod_resource_mst a  where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and b.pr_date between $txt_date_from and $txt_date_to and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 and a.id not in($res_line_cond) and a.line_number like '%$lin_ids%'  $location  $floor  group by a.id, a.floor_id, a.line_number, d.prod_start_time,b.man_power, b.operator, b.helper,b.pr_date, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type order by a.floor_id");
			$no_prod_line_arr=array();
			foreach( $dataArray_sum as $row)
			{

			$sewing_line_id=$row[csf('line_no')];

			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id]=$slNo;
			}
			else $slNo=$lineSerialArr[$sewing_line_id];

				$production_serial_arr[$row[csf('pr_date')]][$row[csf('floor_id')]][$slNo][$row[csf('id')]]=$row[csf('id')];
				$production_data_arr[$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['type_line']=$row[csf('type_line')];
				$production_data_arr[$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['prod_reso_allo']=$row[csf('prod_reso_allo')];
				$production_data_arr[$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['man_power']=$row[csf('man_power')];
				$production_data_arr[$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['operator']=$row[csf('operator')];
				$production_data_arr[$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['helper']=$row[csf('helper')];
				$production_data_arr[$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['working_hour']=$row[csf('working_hour')];
				$production_data_arr[$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['terget_hour']=$row[csf('target_per_hour')];
				$production_data_arr[$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['total_line_hour']=$row[csf('man_power')]*$row[csf('working_hour')];
				$production_data_arr[$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['smv_adjust']=$row[csf('smv_adjust')];
				$production_data_arr[$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['smv_adjust_type']=$row[csf('smv_adjust_type')];
				$production_data_arr[$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('id')]]['prod_start_time']=$row[csf('prod_start_time')];
			}
			$dataArray_sql_cap=sql_select("SELECT  a.floor_id, a.line_number as line_no,b.pr_date,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id  and a.company_id=$comapny_id and b.pr_date between $txt_date_from and $txt_date_to  and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  group by a.id,a.line_number,b.pr_date, a.floor_id, a.line_number, b.man_power, b.operator,c.capacity, b.helper, b.working_hour,b.target_per_hour order by a.floor_id");

			//$prod_resource_array_summary=array();
			foreach( $dataArray_sql_cap as $row)
			{
				$production_data_arr[$row[csf('pr_date')]][$row[csf('floor_id')]][$row[csf('line_no')]]['capacity']=$row[csf('capacity')];
			}

		} //End

		// echo "<pre>"; print_r($production_serial_arr);die;

		$avable_min=0;
		$today_product=0;
	    $floor_name="";
	    $floor_man_power=0;
		$floor_operator=$floor_produc_min=0;
		$floor_smv=$floor_row=$floor_helper=$floor_tgt_h=$floor_days_run=$floor_working_hour=$line_floor_production=$floor_today_product=$floor_avale_minute=0;
		$total_operator=$total_helper=$gnd_hit_rate=0;
	    $total_smv=$total_terget=$grand_total_product=$gnd_line_effi=0;
	    $total_man_power=$gnd_avable_min=$gnd_product_min=0;
		$item_smv=$item_smv_total=$line_efficiency=$days_run=$days_active=$total_working_hour=$gnd_total_tgt_h=$total_capacity=0;
		$j=1;

		$line_number_check_arr=array();
		$smv_for_item="";
		$total_production=array();
		$floor_production=array();
	    $line_floor_production=0;
	    $line_total_production=0; $gnd_total_fob_val=0; $gnd_final_total_fob_val=0;

	    foreach($production_serial_arr as $pdate=>$date_data)
	    {
			foreach($date_data as $f_id=>$fname)
			{
				ksort($fname);
				foreach($fname as $sl=>$s_data)
				{
					foreach($s_data as $l_id=>$ldata)
					{
					  	$po_value=$production_data_arr[$pdate][$f_id][$ldata]['po_number'];
					  	if($po_value)
					  	{
							//$item_ids=$production_data_arr[$f_id][$ldata]['item_number_id'];
							$germents_item=array_unique(explode('****',$production_data_arr[$pdate][$f_id][$ldata]['item_number_id']));

							$buyer_neme_all=array_unique(explode(',',$production_data_arr[$pdate][$f_id][$ldata]['buyer_name']));
							$buyer_name="";
							foreach($buyer_neme_all as $buy)
							{
								if($buyer_name!='') $buyer_name.=',';
								$buyer_name.=$buyerArr[$buy];
							}
							$garment_itemname='';
							$active_days='';
							$item_smv="";$item_ids='';
							$smv_for_item="";
							$produce_minit="";
							$order_no_total="";
							$efficiency_min=0;
							$tot_po_qty=0;$fob_val=0; $po_mindate="";
							foreach($germents_item as $g_val)
							{
								$po_garment_item=explode('**',$g_val);
								if($garment_itemname!='') $garment_itemname.=',';
								$garment_itemname.=$garments_item[$po_garment_item[1]];
								if($item_ids=='') $item_ids=$po_garment_item[1];else $item_ids.=",".$po_garment_item[1];
								if($active_days=="")$active_days=$active_days_arr_powise[$po_garment_item[0]][$po_garment_item[1]];
								else $active_days.=','.$active_days_arr_powise[$po_garment_item[0]][$po_garment_item[1]];


								if($po_mindate=="")
                                {
                                	$po_mindate=$mindateArr[$po_garment_item[0]];
                                }
                                else
                                {
                                	if($po_mindate>$mindateArr[$po_garment_item[0]])
                                    {
                                    	$po_mindate=$mindateArr[$po_garment_item[0]];
                                    }
                                }

								//echo $item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt'].'<br>';
								$tot_po_qty+=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];
								$tot_po_amt+=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt'];
								if($item_smv!='') $item_smv.='/';
								//echo $po_garment_item[0].'='.$po_garment_item[1];

								$item_smv.=$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
								if($order_no_total!="") $order_no_total.=",";
								$order_no_total.=$po_garment_item[0];
								if($smv_for_item!="") $smv_for_item.="****".$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
								else
								$smv_for_item=$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
								$produce_minit+=$production_po_data_arr[$pdate][$f_id][$l_id][$po_garment_item[0]]*$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
								$fob_rate=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt']/$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];
								$prod_qty=$production_data_arr_qty[$f_id][$l_id][$po_garment_item[0]][$po_garment_item[1]]['quantity'];
								//echo $prod_qty.'<br>';
								if(is_nan($fob_rate)){ $fob_rate=0; }
								$fob_val+=$prod_qty*$fob_rate;
							}
							//$fob_rate=$tot_po_amt/$tot_po_qty;

							$subcon_po_id=array_unique(explode(',',$production_data_arr[$pdate][$f_id][$ldata]['order_id']));
							$subcon_order_id="";
							foreach($subcon_po_id as $sub_val)
							{
								$subcon_po_smv=explode(',',$sub_val);
								if($sub_val!=0)
								{
								if($item_smv!='') $item_smv.='/';
								if($item_smv!='') $item_smv.='/';
								$item_smv.=$subcon_order_smv[$sub_val];
								}
								$produce_minit+=$production_po_data_arr[$pdate][$f_id][$l_id][$sub_val]*$subcon_order_smv[$sub_val];
								if($subcon_order_id!="") $subcon_order_id.=",";
								$subcon_order_id.=$sub_val;
							}
							if($order_no_total!="")
							{
								/*$day_run_sql=sql_select("SELECT min(production_date) as min_date from pro_garments_production_mst
								where po_break_down_id in(".$order_no_total.")  and production_type=4");
								foreach($day_run_sql as $row_run)
								{
								$sewing_day=$row_run[csf('min_date')];
								}*/
								$sewing_day=change_date_format($po_mindate);
								// echo $sewing_day."<br>";
								if($sewing_day!="")
								{
								$days_run=datediff("d",$sewing_day,$pdate);
								}
								else  $days_run=0;
							}
							$type_line=$production_data_arr[$pdate][$f_id][$ldata]['type_line'];
							$prod_reso_allo=$production_data_arr[$pdate][$f_id][$ldata]['prod_reso_allo'];

							$sewing_line='';
							if($production_data_arr[$pdate][$f_id][$ldata]['prod_reso_allo']==1)
							{
								$line_number=explode(",",$prod_reso_arr[$ldata]);
								foreach($line_number as $val)
								{
									if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
								}
							}
							else $sewing_line=$lineArr[$ldata];



							$lunch_start="";
							$lunch_start=$line_number_arr[$ldata][$pdate]['lunch_start_time'];
							$lunch_hour=$start_time_arr[$row[1]]['lst'];
							if($lunch_start!="")
							{
							$lunch_start_hour=$lunch_start;
							}
							else
							{
							$lunch_start_hour=$lunch_hour;
							}

							$production_hour=array();
							for($h=$hour;$h<=$last_hour;$h++)
							{
								 $prod_hour="prod_hour".substr($line_start_hour_arr[$h],0,2)."";
								 $production_hour[$prod_hour]=$production_data_arr[$pdate][$f_id][$ldata][$prod_hour];
								 $floor_production[$prod_hour]+=$production_data_arr[$pdate][$f_id][$ldata][$prod_hour];
								 $total_production[$prod_hour]+=$production_data_arr[$pdate][$f_id][$ldata][$prod_hour];
							}

			 				$floor_production['prod_hour24']+=$production_data_arr[$pdate][$f_id][$ldata]['prod_hour23'];
							$total_production['prod_hour24']+=$production_data_arr[$pdate][$f_id][$ldata]['prod_hour23'];
							$production_hour['prod_hour24']=$production_data_arr[$pdate][$f_id][$ldata]['prod_hour23'];
							$line_production_hour=0;
							if(str_replace("'","",$actual_production_date)>str_replace("'","",$actual_date))
							{
								if($type_line==2) //No Profuction Line
								{
									$line_start=$production_data_arr[$pdate][$f_id][$l_id]['prod_start_time'];
								}
								else
								{
									$line_start=$line_number_arr[$ldata][$pdate]['prod_start_time'];
								}
								if($line_start!="")
								{
									$line_start_hour=substr($line_start,0,2);
									if(substr($line_start_hour,0,1)==0)  $line_start_hour=substr($line_start_hour,1,1);
								}
								else
								{
									$line_start_hour=$hour;
								}
								$actual_time_hour=0;
								$total_eff_hour=0;
								for($lh=$line_start_hour;$lh<=$last_hour;$lh++)
								{
									$bg=$start_hour_arr[$lh];
									if($lh<$actual_time)
									{
									$total_eff_hour=$total_eff_hour+1;;
									$line_hour="prod_hour".substr($bg,0,2)."";
									echo $line_production_hour+=$production_data_arr[$pdate][$f_id][$ldata][$line_hour];
									$line_floor_production+=$production_data_arr[$pdate][$f_id][$ldata][$line_hour];
									$line_total_production+=$production_data_arr[$pdate][$f_id][$ldata][$line_hour];
									$actual_time_hour=$start_hour_arr[$lh+1];
									}
								}
			 					if($start_hour_arr[$actual_time]>$lunch_start_hour) $total_eff_hour=$total_eff_hour-1;

								if($type_line==2)
								{
									if($total_eff_hour>$production_data_arr[$pdate][$f_id][$l_id]['working_hour'])
									{
										 $total_eff_hour=$production_data_arr[$pdate][$f_id][$l_id]['working_hour'];
									}
								}
								else
								{
									if($total_eff_hour>$prod_resource_array[$ldata][$pdate]['working_hour'])
									{
										$total_eff_hour=$prod_resource_array[$ldata][$pdate]['working_hour'];
									}
								}

							}
							if(str_replace("'","",$actual_production_date)<=str_replace("'","",$actual_date))
							{
								for($ah=$hour;$ah<=$last_hour;$ah++)
								{
									$prod_hour="prod_hour".substr($start_hour_arr[$ah],0,2)."";
									$line_production_hour+=$production_data_arr[$pdate][$f_id][$ldata][$prod_hour];
									$line_floor_production+=$production_data_arr[$pdate][$f_id][$ldata][$prod_hour];
									$line_total_production+=$production_data_arr[$pdate][$f_id][$ldata][$prod_hour];
								}
								if($type_line==2)
								{
									$total_eff_hour=$production_data_arr[$pdate][$f_id][$l_id]['working_hour'];
								}
								else
								{
									$total_eff_hour=$prod_resource_array[$ldata][$pdate]['working_hour'];
								}
							}

							if($sewing_day!="")
							{
								$days_run= $diff=datediff("d",$sewing_day,$pdate);
								$days_active= $active_days_arr[$f_id][$l_id];
							}
							else
							{
								 $days_run=0;
								 $days_active=0;
							}

							$current_wo_time=0;
							if($current_date==$search_prod_date)
							{
								$prod_wo_hour=$total_eff_hour;

								if ($dif_time<$prod_wo_hour)//
								{
									$current_wo_time=$dif_hour_min;
									$cla_cur_time=$dif_time;
								}
								else
								{
									$current_wo_time=$prod_wo_hour;
									$cla_cur_time=$prod_wo_hour;
								}
							}
							else
							{
								$current_wo_time=$total_eff_hour;
								$cla_cur_time=$total_eff_hour;
							}
							$total_adjustment=0;
							if($type_line==2) //No Production Line
							{
								$smv_adjustmet_type=$production_data_arr[$pdate][$f_id][$l_id]['smv_adjust_type'];
								$eff_target=($production_data_arr[$pdate][$f_id][$l_id]['terget_hour']*$total_eff_hour);

								if($total_eff_hour>=$production_data_arr[$pdate][$f_id][$l_id]['working_hour'])
								{
									if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$production_data_arr[$pdate][$f_id][$l_id]['smv_adjust'];
									if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($production_data_arr[$pdate][$f_id][$l_id]['smv_adjust'])*(-1);
								}
								$efficiency_min+=$total_adjustment+($production_data_arr[$pdate][$f_id][$l_id]['man_power'])*$cla_cur_time*60;
								$line_efficiency=($efficiency_min>0) ? (($produce_minit)*100)/$efficiency_min : 0;
							}
							else
							{
								$smv_adjustmet_type=$prod_resource_array[$ldata][$pdate]['smv_adjust_type'];
								$eff_target=($prod_resource_array[$ldata][$pdate]['terget_hour']*$total_eff_hour);

								if($total_eff_hour>=$prod_resource_array[$ldata][$pdate]['working_hour'])
								{
								if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array[$ldata][$pdate]['smv_adjust'];
								if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array[$ldata][$pdate]['smv_adjust'])*(-1);
								}



								$efficiency_min+=$total_adjustment+($prod_resource_array[$ldata][$pdate]['man_power'])*$cla_cur_time*60;
								$line_efficiency=($efficiency_min>0) ? (($produce_minit)*100)/$efficiency_min : 0;
							}

							if($type_line==2) //No Production Line
							{
								$man_power=$production_data_arr[$pdate][$f_id][$l_id]['man_power'];
								$operator=$production_data_arr[$pdate][$f_id][$l_id]['operator'];
								$helper=$production_data_arr[$pdate][$f_id][$l_id]['helper'];
								$terget_hour=$production_data_arr[$pdate][$f_id][$l_id]['target_hour'];
								$capacity=$production_data_arr[$pdate][$f_id][$l_id]['capacity'];
								$working_hour=$production_data_arr[$pdate][$f_id][$l_id]['working_hour'];

								$floor_working_hour+=$production_data_arr[$pdate][$f_id][$l_id]['working_hour'];
								$eff_target_floor+=$eff_target;
								$floor_today_product+=$today_product;
								$floor_avale_minute+=$efficiency_min;
								$floor_produc_min+=$produce_minit;
								$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
								$floor_capacity+=$production_data_arr[$pdate][$f_id][$l_id]['capacity'];
								$floor_helper+=$production_data_arr[$pdate][$ldata][$pdate]['helper'];
								$floor_man_power+=$production_data_arr[$pdate][$f_id][$l_id]['man_power'];
								$floor_operator+=$production_data_arr[$pdate][$f_id][$l_id]['operator'];
								$total_operator+=$production_data_arr[$pdate][$f_id][$l_id]['operator'];
								$total_man_power+=$production_data_arr[$pdate][$f_id][$l_id]['man_power'];
								$total_helper+=$production_data_arr[$pdate][$f_id][$l_id]['helper'];
								$total_capacity+=$production_data_arr[$pdate][$f_id][$l_id]['capacity'];
								$floor_tgt_h+=$production_data_arr[$pdate][$f_id][$l_id]['target_hour'];
								$total_working_hour+=$production_data_arr[$pdate][$f_id][$l_id]['working_hour'];
								$gnd_total_tgt_h+=$production_data_arr[$pdate][$f_id][$l_id]['target_hour'];
								$total_terget+=$eff_target;
								$grand_total_product+=$today_product;
								$gnd_avable_min+=$efficiency_min;
								$gnd_product_min+=$produce_minit;

								$gnd_total_fob_val+=$fob_val;
								$gnd_final_total_fob_val+=$fob_val;
							}
							else
							{
								$man_power=$prod_resource_array[$ldata][$pdate]['man_power'];
								$operator=$prod_resource_array[$ldata][$pdate]['operator'];
								$helper=$prod_resource_array[$ldata][$pdate]['helper'];
								$terget_hour=$prod_resource_array[$ldata][$pdate]['terget_hour'];
								$capacity=$prod_resource_array[$ldata][$pdate]['capacity'];
								$working_hour=$prod_resource_array[$ldata][$pdate]['working_hour'];

								$floor_capacity+=$prod_resource_array[$ldata][$pdate]['capacity'];
								$floor_man_power+=$prod_resource_array[$ldata][$pdate]['man_power'];
								$floor_operator+=$prod_resource_array[$ldata][$pdate]['operator'];
								$floor_helper+=$prod_resource_array[$ldata][$pdate]['helper'];
								$floor_tgt_h+=$prod_resource_array[$ldata][$pdate]['terget_hour'];
								$floor_working_hour+=$prod_resource_array[$ldata][$pdate]['working_hour'];
								$eff_target_floor+=$eff_target;
								$floor_today_product+=$today_product;
								$floor_avale_minute+=$efficiency_min;
								$floor_produc_min+=$produce_minit;
								$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;

								$total_operator+=$prod_resource_array[$ldata][$pdate]['operator'];
								$total_man_power+=$prod_resource_array[$ldata][$pdate]['man_power'];
								$total_helper+=$prod_resource_array[$ldata][$pdate]['helper'];
								$total_capacity+=$prod_resource_array[$ldata][$pdate]['capacity'];
								$total_working_hour+=$prod_resource_array[$ldata][$pdate]['working_hour'];
								$gnd_total_tgt_h+=$prod_resource_array[$ldata][$pdate]['terget_hour'];
								$total_terget+=$eff_target;
								$grand_total_product+=$today_product;
								$gnd_avable_min+=$efficiency_min;
								$gnd_product_min+=$produce_minit;
								$gnd_total_fob_val+=$fob_val;
								$gnd_final_total_fob_val+=$fob_val;

							}
							$po_id=rtrim($production_data_arr[$pdate][$f_id][$ldata]['po_id'],',');
							$po_id=array_unique(explode(",",$po_id));
							$style=rtrim($production_data_arr[$pdate][$f_id][$ldata]['style']);
							$style=implode(",",array_unique(explode(",",$style)));

							$cbo_get_upto=str_replace("'","",$cbo_get_upto);
							$txt_parcentage=str_replace("'","",$txt_parcentage);

							$floor_name=$floorArr[$f_id];
							$floor_smv+=$item_smv;

							$floor_days_run+=$days_run;
							$floor_days_active+=$days_active;

							$po_id=$production_data_arr[$pdate][$f_id][$ldata]['po_id'];//$item_ids//$subcon_order_id
							$styles=explode(",",$style);
							 $style_button='';//

							$as_on_current_hour_target=0; $as_on_current_hour_variance=0;
							$as_on_current_hour_target=$terget_hour*$cla_cur_time;
							$as_on_current_hour_variance=$line_production_hour-$as_on_current_hour_target;


							// echo $line_efficiency;echo "<br>";

							// number_format($produce_minit,0)
							// ($line_production_hour/$eff_target)*100

							// number_format($line_efficiency,2)
							$resurce_data_array[date('d-M-Y',strtotime($pdate))]['target'] += $eff_target;//$terget_hour;
							$resurce_data_array[date('d-M-Y',strtotime($pdate))]['production'] += $line_production_hour;
							$resurce_data_array[date('d-M-Y',strtotime($pdate))]['traget_qty_achieved'] += ($eff_target>0) ? ($line_production_hour/$eff_target)*100 : 0;

							$resurce_data_array[date('d-M-Y',strtotime($pdate))]['man_power'] += $man_power;
							$resurce_data_array[date('d-M-Y',strtotime($pdate))]['prod_hours'] += $prod_hours;
							$resurce_data_array[date('d-M-Y',strtotime($pdate))]['available_minit'] += $efficiency_min;
							$resurce_data_array[date('d-M-Y',strtotime($pdate))]['target_min'] += $target_min;
							$resurce_data_array[date('d-M-Y',strtotime($pdate))]['produce_minit'] += $produce_minit;
							$resurce_data_array[date('d-M-Y',strtotime($pdate))]['line_efficiency'] += $line_efficiency;

							$resurce_data_array[date('d-M-Y',strtotime($pdate))]['style_change'] += $style_change;
							$resurce_data_array[date('d-M-Y',strtotime($pdate))]['target_effi'] += $target_effi;
							$resurce_data_array[date('d-M-Y',strtotime($pdate))]['achive_effi'] += $achive_effi;
							$resurce_data_array[date('d-M-Y',strtotime($pdate))]['cm_earn'] += $ttl_cm;
							$resurce_data_array[date('d-M-Y',strtotime($pdate))]['fob_earn'] += $ttl_fob_val;

						}
					}
				}
			}
		}



		// echo "<pre>";print_r($resurce_data_array);die();

		/* ==========================================================================================/
		/										production  data									 /
		/ ========================================================================================= */

		$sql="SELECT a.id,a.sewing_line,a.prod_reso_allo, a.production_type, a.floor_id, a.production_date, b.production_qnty as production_qnty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.production_type in (1,2,3,4,5,8,9,11,80) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.floor_id!=0 $cbo_company_cond  $production_date order by a.production_date";
		// echo $sql; die();
		$sql_dtls = sql_select($sql);
		$floor_qnty = array();
		$floor_wise_total = array();
		$prod_floor_array = array();
		$wash_qty_array = array();
		$sewing_line_array = array();

		foreach ( $sql_dtls as $row )
		{
			if($row['PROD_RESO_ALLO']==1)
			{
				$resource_line= explode(",", $prod_reso_arr[$row['SEWING_LINE']]);
				$line_name='';
    			foreach($resource_line as $actual_line)
    			{
    				$line_name.= ($line_name=='') ? $lineArr[$actual_line] : ", ".$lineArr[$actual_line];
    			}
			}
			else
			{
				$line_name=$lineArr[$row['SEWING_LINE']];
			}

			// $floor_qnty[change_date_format($row["PRODUCTION_DATE"],'','',1)][$row["FLOOR_ID"]][$row["PRODUCTION_TYPE"]] += $row["PRODUCTION_QNTY"];
			$wash_qty_array[change_date_format($row["PRODUCTION_DATE"],'','',1)][$row["PRODUCTION_TYPE"]] +=$row["PRODUCTION_QNTY"];
			if($row["PRODUCTION_TYPE"]==11)
			{
				$floor_id = 0;
				$prod_type = 0;
				if($row["PRODUCTION_TYPE"]==11)
				{
					if($row["FLOOR_ID"]==624)
					{
						$floor_id= 669;
					}
					else
					{
						$floor_id= 331;
					}
					$prod_type = 80;
				}
				$prod_floor_array[$prod_type][$floor_id] = $floor_id;
				$floor_wise_total[$floor_id][$prod_type] += $row["PRODUCTION_QNTY"];
				$floor_qnty[change_date_format($row["PRODUCTION_DATE"],'','',1)][$floor_id][$prod_type] += $row["PRODUCTION_QNTY"];
			}
			else
			{
				$prod_floor_array[$row["PRODUCTION_TYPE"]][$row["FLOOR_ID"]] = $row["FLOOR_ID"];
				$floor_wise_total[$row["FLOOR_ID"]][$row["PRODUCTION_TYPE"]] += $row["PRODUCTION_QNTY"];
				$floor_qnty[change_date_format($row["PRODUCTION_DATE"],'','',1)][$row["FLOOR_ID"]][$row["PRODUCTION_TYPE"]] += $row["PRODUCTION_QNTY"];
			}

			$sewing_line_array[change_date_format($row["PRODUCTION_DATE"],'','',1)][$row["PRODUCTION_TYPE"]] .= $line_name.",";


		}
		// echo "<pre>"; print_r($floor_wise_total);die();

		// ================================ cutting delivery data ============================
		$delivery_date = str_replace("a.production_date", "b.cut_delivery_date", $production_date);
		$delivery_location = str_replace("a.location", "a.location_id", $location_id);
		$sqlDelv = "SELECT b.CUT_DELIVERY_DATE,b.CUT_DELIVERY_QNTY from pro_cut_delivery_mst a,pro_cut_delivery_order_dtls b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $cbo_company_cond  $delivery_date";
		// echo $sqlDelv;die();
		$delvRes = sql_select($sqlDelv);
		$delivery_to_input_qty = array();
		foreach ($delvRes as $val)
		{
			$delivery_to_input_qty[date('d-M-Y',strtotime($val['CUT_DELIVERY_DATE']))] += $val['CUT_DELIVERY_QNTY'];
		}
		// echo "<pre>"; print_r($delivery_to_input_qty);die();


		// ================================ ex-factory data ============================
		$delivery_date = str_replace("a.production_date", "a.delivery_date", $production_date);
		$delivery_location = str_replace("a.location", "a.delivery_location_id", $location_id);
		$sqlEx = "SELECT a.DELIVERY_FLOOR_ID,a.DELIVERY_DATE,b.EX_FACTORY_QNTY from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $cbo_company_cond  $delivery_date";
		// echo $sqlEx;die();
		$exRes = sql_select($sqlEx);
		$ex_factory_qty = array();
		foreach ($exRes as $val)
		{
			$ex_factory_qty[date('d-M-Y',strtotime($val['DELIVERY_DATE']))] += $val['EX_FACTORY_QNTY'];
		}
		// echo "<pre>"; print_r($ex_factory_qty);die();

		// ================================ buyer inspection data ============================
		$inspection_date = str_replace("a.production_date", "a.inspection_date", $production_date);
		$working_location = str_replace("a.location", "a.working_location", $location_id);
		$working_company = str_replace("a.company_id", "a.working_company", $cbo_company_cond);
		$sql = "SELECT a.INSPECTION_STATUS,a.INSPECTION_DATE,a.INSPECTION_QNTY from pro_buyer_inspection a where a.status_active=1 and a.is_deleted=0 and a.inspection_level=3 $working_company  $working_location $inspection_date and a.inspection_status in(1,3)";
		//echo $sql;die();
		$res = sql_select($sql);
		$buyer_insp_qty = array();
		foreach ($res as $val)
		{
			$buyer_insp_qty[date('d-M-Y',strtotime($val['INSPECTION_DATE']))][$val['INSPECTION_STATUS']] += $val['INSPECTION_QNTY'];
		}
		// echo "<pre>"; print_r($buyer_insp_qty);die();

        $count_data=count($prod_floor_array[1])+count($prod_floor_array[4])+count($prod_floor_array[5])+count($prod_floor_array[8])+count($prod_floor_array[80]);
        // echo $count_data;die;

         //$count_hd=count($sql_floor)+1;
        $count_hd=count($prod_floor_array[1])+count($prod_floor_array[4])+count($prod_floor_array[5])+count($prod_floor_array[8])+count($prod_floor_array[80]);

        $width_hd=$count_hd*80;
        $count_cutting=count($prod_floor_array[1])+1;
        $width_cutting=$count_cutting*80;

        $count_sewing=count($prod_floor_array[5])+1;
        $width_sewing=$count_sewing*80;
        $count_sewing_in=count($prod_floor_array[4])+1;
        $width_sewing_in=$count_sewing_in*80;

        $count_finishing=count($prod_floor_array[8])+1;
        $width_finishing=$count_finishing*80;

		$count_poly=count($prod_floor_array[80])+1;
        $width_poly=$count_poly*80;

        $table_width=940+(($count_data+5)*80);
		ob_start();

		?>
		<fieldset>
	        <table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="caption" align="center">
	            <tr>
	               <td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:18px">

				   Company Name: <?= $company_library[str_replace("'","",$cbo_company_id)]; ?>

					</strong>
	               </td>
	            </tr>
	            <tr>
	               <td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:14px"><? echo $report_title; ?></strong></td>
	            </tr>
	            <tr>
	               <td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:14px"> <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
	            </tr>
	        </table>

	        <div style="height:auto;">

		        <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left">
		            <thead>
		                <tr>
		                    <th width="60" rowspan="2" >Date</th>
		                    <th width="<? echo $width_cutting; ?>" colspan="<? echo $count_cutting; ?>">Cutting Output</th>
		                    <th width="80" rowspan="2">Cutting Issue</th>
		                    <th width="80" rowspan="2">Sewing Line</th>
		                    <th width="<? echo $width_sewing_in; ?>" colspan="<? echo $count_sewing_in; ?>">Sewing Input</th>
		                    <th width="<? echo $width_sewing; ?>" colspan="<? echo $count_sewing; ?>">Sewing Output</th>
		                    <th width="160" colspan="2">Wash</th>
		                    <th width="<? echo $width_poly; ?>" colspan="<? echo $count_poly; ?>">Gmts. Finishing</th>
		                    <th width="<? echo $width_finishing; ?>" colspan="<? echo $count_finishing; ?>">Packing</th>
		                    <th width="80" rowspan="2" title="Produce min/Available min*100">Avg. Efficiency (%)</th>
		                    <th width="80" rowspan="2">Produced Minute</th>
		                    <th width="80" rowspan="2">Available Minutes</th>
		                    <th width="80" rowspan="2" title="Production/Target*100">Target Qty Achieved (%)</th>
		                    <th width="80" rowspan="2">Buyer Insp. Passed Qty</th>
		                    <th width="80" rowspan="2">Buyer Insp. Failed Qty</th>
		                    <th width="80" rowspan="2">Ex-Factory Qty</th>
		                </tr>
		               	<tr>
							<?
							foreach ( $prod_floor_array[1] as $rows )
							{
								?>
	                            <th width="80" title="<?=$rows;?>"><? echo $floor_arr[$rows]; ?></th>
								<?
							}
							?>
							<th width="80" colspan="">Total</th>
							<?

	                        foreach ( $prod_floor_array[4] as $rows )
	                        {
	                            ?>
	                            <th width="80"><? echo $floor_arr[$rows]; ?></th>
	                            <?
	                        }
	                        ?>
							<th width="80" colspan="">Total</th>
							<?

	                        foreach ( $prod_floor_array[5] as $rows )
	                        {
	                            ?>
	                            <th width="80"><? echo $floor_arr[$rows]; ?></th>
	                            <?
	                        }
	                        ?>
							<th width="80" colspan="">Total</th>
	                        <th width="80">Sent</th>
	                        <th width="80">Receive</th>
	                        <?
	                        foreach ( $prod_floor_array[80] as $rows )
	                        {
	                            ?>
	                            <th width="80"><? echo $floor_arr[$rows]; ?></th>
	                            <?
	                        }
							?>
							<th width="80" colspan="">Total</th>
							<?

	                        foreach ( $prod_floor_array[8] as $rows )
	                        {
	                            ?>
	                            <th width="80"><? echo $floor_arr[$rows]; ?></th>
	                            <?
	                        }
							?>
							<th width="80" colspan="">Total</th>

		               	</tr>
		            </thead>
		        </table>
	        </div>
	        <? //echo $datediff; die('kakku');?>
	        <div align="left" style="max-height:300px;width: <?= $table_width+20;?>px" id="scroll_body">
		        <table align="left" cellspacing="0" cellpadding="0" width="<? echo $table_width; ?>"  border="1" rules="all" class="rpt_table" >
					<?
					$unit_wise_total_array=array();
					$value_define=array();
					$value_define2=array();
					$wash_issue_total = 0;
					$wash_rcv_total = 0;
					$ex_qty_total = 0;
					$produce_minit_total = 0;
					$tot_efficiency = 0;
					$tot_efficiency_count = 0;
					$tot_target_achive = 0;
					$tot_target_achive_count = 0;

					$gr_tot_pro_min = 0;
					$gr_tot_avlable_min = 0;
					$gr_tot_target = 0;
					$gr_tot_production = 0;

		            for($j=0;$j<$datediff;$j++)
		            {
		                if ($j%2==0)
		                    $bgcolor="#E9F3FF";
		                else
		                    $bgcolor="#FFFFFF";

		                $date_data_array=array();
						$newdate =add_date(str_replace("'","",$txt_date_from),$j);
						$newdate=date("d-M-Y",strtotime(str_replace("'","",$newdate)));
		                $date_data_array[$j]=$newdate;
			            ?>
			            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>">
			                <td width="60" align="center">&nbsp;<? echo change_date_format($newdate); ?></td>
			                <?
		                    $site_tot_qnty=0;
	                        foreach ( $prod_floor_array[1] as $rows )
	                        {
	                        	// echo $newdate."==".$rows."<br>";
	                            ?>
	                                <td width="80" align="right"><?  echo number_format($floor_qnty[$date_data_array[$j]][$rows][1],0); ?></td>
	                            <?
	                            $site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows][1];
	                            if($floor_qnty[$date_data_array[$j]][$rows][1]!="")
	                            {
	                            	$value_define[1][$rows] +=1;
	                            }
	                        }
	                        ?>
	                        <td width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>

	                        <td width="80" align="right">
	                        	<?
	                        	if($delivery_to_input_qty[$date_data_array[$j]]!="")
		                        {
		                        	$value_define2['cut_delv'] +=1;
		                        }
	                        	echo number_format($delivery_to_input_qty[$date_data_array[$j]],0);
	                        	?>

	                        </td>
	                        <td width="80" align="left"><? echo implode(", ",array_unique(array_filter(explode(",", $sewing_line_array[$date_data_array[$j]][5])))); ?></td>
							<?
		                    $site_tot_qnty=0;
	                        foreach ( $prod_floor_array[4] as $rows )
	                        {
	                            ?>
	                                <td width="80" align="right"><?  echo number_format($floor_qnty[$date_data_array[$j]][$rows][4],0); ?></td>
	                            <?
	                            $site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows][4];
	                            if($floor_qnty[$date_data_array[$j]][$rows][4]!="")
	                            {
	                            	$value_define[4][$rows] +=1;
	                            }
	                        }
	                        ?>
	                        <td width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>
	                        <?
		                    $site_tot_qnty=0;
	                        foreach ( $prod_floor_array[5] as $rows )
	                        {
	                            ?>
	                                <td width="80" align="right"><?  echo number_format($floor_qnty[$date_data_array[$j]][$rows][5],0); ?></td>
	                            <?
	                            $site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows][5];
	                            if($floor_qnty[$date_data_array[$j]][$rows][5]!="")
	                            {
	                            	$value_define[5][$rows] +=1;
	                            }
	                        }
	                        ?>
	                        <td width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>

	                        <td width="80" align="right">
	                        <?
	                        if($wash_qty_array[$date_data_array[$j]][2]!="")
	                        {
	                        	$value_define2['wash_iss'] +=1;
	                        }
	                        echo number_format($wash_qty_array[$date_data_array[$j]][2],0);
	                        ?>

	                        </td>
	                        <td width="80" align="right">
	                        <?
	                        if($wash_qty_array[$date_data_array[$j]][3]!="")
	                        {
	                        	$value_define2['wash_rcv'] +=1;
	                        }
	                        echo number_format($wash_qty_array[$date_data_array[$j]][3],0);
	                        ?>

	                        </td>

							<?
	                        $woven_finish_site_tot_qnty=0;
	                        foreach ( $prod_floor_array[80] as $rows )
	                        {
								if ($rows == 669)
								{
									?>
										<td width="80" align="right">
											<? echo number_format(($floor_qnty[$date_data_array[$j]][$rows][80]+$floor_qnty[$date_data_array[$j]][624][11]),0); ?>
										</td>
									<?
									$woven_finish_site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows][80]+$floor_qnty[$date_data_array[$j]][624][11];
								}
								else if($rows == 331)
								{
									?>
									<td width="80" align="right">
										<? echo number_format(($floor_qnty[$date_data_array[$j]][$rows][80]+$floor_qnty[$date_data_array[$j]][676][11]),0); ?>
									</td>
								    <?
									$woven_finish_site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows][80]+$floor_qnty[$date_data_array[$j]][676][11];
								}
								else
								{
									?>
										<td width="80" align="right">
											<? echo number_format($floor_qnty[$date_data_array[$j]][$rows][80],0); ?>
										</td>
									<?
									$woven_finish_site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows][80];
								}

	                            if($floor_qnty[$date_data_array[$j]][$rows][80]!="")
	                            {
	                            	$value_define[80][$rows] +=1;
	                            }
	                        }
	                        ?>
							<td width="80" align="right">
								<strong><? echo number_format($woven_finish_site_tot_qnty,0); ?></strong>
						    </td>

	                        <?
	                        $site_tot_qnty=0;
	                        foreach ( $prod_floor_array[8] as $rows )
	                        {
	                            ?>
	                                <td width="80" align="right">
	                                	<? echo number_format($floor_qnty[$date_data_array[$j]][$rows][8],0); ?>
	                                </td>
	                            <?
	                            $site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows][8];
	                            if($floor_qnty[$date_data_array[$j]][$rows][8]!="")
	                            {
	                            	$value_define[8][$rows] +=1;
	                            }
	                        }
	                        ?>
	                        <td width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>

	                        <td width="80" align="right">
	                        	<?
	                        	echo ($resurce_data_array[$date_data_array[$j]]['available_minit']>0) ? number_format(($resurce_data_array[$date_data_array[$j]]['produce_minit']/$resurce_data_array[$date_data_array[$j]]['available_minit']*100),2) : 0;

	                        	if($resurce_data_array[$date_data_array[$j]]['available_minit']>0)
	                        	{
	                        		$tot_efficiency += $resurce_data_array[$date_data_array[$j]]['produce_minit']/$resurce_data_array[$date_data_array[$j]]['available_minit']*100;
	                        		$tot_efficiency_count++;
	                        	}

	                        	?>%
	                        </td>
	                        <td width="80" align="right"><? echo number_format($resurce_data_array[$date_data_array[$j]]['produce_minit'],0); ?></td>
	                        <td width="80" align="right"><? echo number_format($resurce_data_array[$date_data_array[$j]]['available_minit'],0); ?></td>
	                        <td width="80" align="right">
	                        	<?
	                        	echo ($resurce_data_array[$date_data_array[$j]]['target']>0) ? number_format(($resurce_data_array[$date_data_array[$j]]['production']/$resurce_data_array[$date_data_array[$j]]['target']*100),2) : 0;

	                        	if($resurce_data_array[$date_data_array[$j]]['target']>0)
	                        	{
	                        		$tot_target_achive +=  $resurce_data_array[$date_data_array[$j]]['production']/$resurce_data_array[$date_data_array[$j]]['target']*100;
	                        		$tot_target_achive_count++;
	                        	}
	                        	?>%
	                        </td>
	                        <td width="80" align="right">
	                        	<a href="javascript:void(0)" onclick="open_popup('<?=$cbo_company;?>','<?=$cbo_location;?>','<?=$newdate;?>',1,'buyer-insp-popup')">
	                        		<? echo number_format($buyer_insp_qty[$date_data_array[$j]][1],0); ?>
	                        	</a>
	                        </td>
	                        <?
	                        if($buyer_insp_qty[$date_data_array[$j]][1]!="")
	                        {
	                        	$value_define2['bi-pass'] +=1;
	                        }
	                        ?>


	                        <td width="80" align="right">
	                        	<a href="javascript:void(0)" onclick="open_popup('<?=$cbo_company;?>','<?=$cbo_location;?>','<?=$newdate;?>',3,'buyer-insp-popup')">
	                        		<? echo number_format($buyer_insp_qty[$date_data_array[$j]][3],0); ?>
	                        	</a>
	                        </td>
	                        <?
	                        if($buyer_insp_qty[$date_data_array[$j]][3]!="")
	                        {
	                        	$value_define2['bi-faill'] +=1;
	                        }
	                        ?>

	                        <td width="80" align="right">
	                        	<a href="javascript:void(0)" onclick="open_popup('<?=$cbo_company;?>','<?=$cbo_location;?>','<?=$newdate;?>',0,'ex-factory-popup')">
	                        		<? echo number_format($ex_factory_qty[$date_data_array[$j]],0); ?>
	                        	</a>
	                        </td>
	                        <?
	                        if($ex_factory_qty[$date_data_array[$j]]!="")
	                        {
	                        	$value_define2['ex'] +=1;
	                        }
	                        ?>

			            </tr>
			            <?
			            $wash_issue_total += $wash_qty_array[$date_data_array[$j]][2];
						$wash_rcv_total += $wash_qty_array[$date_data_array[$j]][3];
						$cutting_delv_total += $delivery_to_input_qty[$date_data_array[$j]];
						$buyer_insp_pass_total += $buyer_insp_qty[$date_data_array[$j]][1];
						$buyer_insp_faill_total += $buyer_insp_qty[$date_data_array[$j]][3];
						$ex_qty_total += $ex_factory_qty[$date_data_array[$j]];
						$produce_minit_total += $resurce_data_array[$date_data_array[$j]]['produce_minit'];
						$available_minit_total += $resurce_data_array[$date_data_array[$j]]['available_minit'];

						$gr_tot_pro_min += $resurce_data_array[$date_data_array[$j]]['produce_minit'];
						$gr_tot_avlable_min += $resurce_data_array[$date_data_array[$j]]['available_minit'];
						$gr_tot_target += $resurce_data_array[$date_data_array[$j]]['target'];
						$gr_tot_production += $resurce_data_array[$date_data_array[$j]]['production'];


			        }
			        ?>
			        <tfoot>
			            <tr bgcolor="#dddddd">
			                <th align="center" width="60"><strong>Total : </strong></th>
			                <?
		                    $grand_total_tot=0;

	                        foreach ( $prod_floor_array[1] as $rows )
	                        {
	                            ?>
	                                <th width="80" align="right"><strong><? echo number_format($floor_wise_total[$rows][1],0); ?></strong></th>
	                            <?
	                            $grand_total_tot+=$floor_wise_total[$rows][1];
	                        }

			                ?>
			                <th width="80" align="right"><strong><? echo number_format($grand_total_tot,0); ?></strong></th>

			                <th width="80" align="right"><? echo number_format($cutting_delv_total,0); ?></th>
			                <th width="80" align="right"></th>
							<?
		                    $grand_total_tot=0;

	                        foreach ( $prod_floor_array[4] as $rows )
	                        {
	                            ?>
	                                <th width="80" align="right"><strong><? echo number_format($floor_wise_total[$rows][4],0); ?></strong></th>
	                            <?
	                            $grand_total_tot+=$floor_wise_total[$rows][4];
	                        }

			                ?>
			                <th width="80" align="right"><strong><? echo number_format($grand_total_tot,0); ?></strong></th>
			                <?
		                    $grand_total_tot=0;

	                        foreach ( $prod_floor_array[5] as $rows )
	                        {
	                            ?>
	                                <th width="80" align="right"><strong><? echo number_format($floor_wise_total[$rows][5],0); ?></strong></th>
	                            <?
	                            $grand_total_tot+=$floor_wise_total[$rows][5];
	                        }

			                ?>
			                <th width="80" align="right"><strong><? echo number_format($grand_total_tot,0); ?></strong></th>

			                <th width="80" align="right"><strong><? echo number_format($wash_issue_total,0); ?></strong></th>
			                <th width="80" align="right"><strong><? echo number_format($wash_rcv_total,0); ?></strong></th>

			                <?
							$grand_total_tot=0;
							foreach ( $prod_floor_array[80] as $rows )
	                        {
								if ($rows == 669)
								{
									?>
										<th width="80" align="right"><strong><? echo number_format(($floor_wise_total[$rows][80]+$floor_wise_total[624][11]),0); ?></strong></th>
									<?
									$grand_total_tot+=$floor_wise_total[$rows][80]+$floor_wise_total[624][11];
								}
								else if ($rows == 331)
								{
									?>
										<th width="80" align="right"><strong><? echo number_format(($floor_wise_total[$rows][80]+$floor_wise_total[676][11]),0); ?></strong></th>
									<?
									$grand_total_tot+=$floor_wise_total[$rows][80]+$floor_wise_total[676][11];
								}
								else
								{
									?>
										<th width="80" align="right"><strong><? echo number_format($floor_wise_total[$rows][80],0); ?></strong></th>
									<?
									$grand_total_tot+=$floor_wise_total[$rows][80];
								}
	                        }

			                ?>
							<th width="80" align="right"><strong><? echo number_format($grand_total_tot,0); ?></strong></th>

			                <?
		                    $grand_total_tot=0;

	                        foreach ( $prod_floor_array[8] as $rows )
	                        {
	                            ?>
	                                <th width="80" align="right"><strong><? echo number_format($floor_wise_total[$rows][8],0); ?></strong></th>
	                            <?
	                            $grand_total_tot+=$floor_wise_total[$rows][8];
	                        }

			                ?>
			                <th width="80" align="right"><strong><? echo number_format($grand_total_tot,0); ?></strong></th>

			                <th width="80" align="right"><strong><? //echo number_format($grand_total_tot,0); ?></strong></th>
			                <th width="80" align="right"><strong><? echo number_format($produce_minit_total,0); ?></strong></th>
			                <th width="80" align="right"><strong><? echo number_format($available_minit_total,0); ?></strong></th>
			                <th width="80" align="right"><strong><? //echo number_format($grand_total_tot,0); ?></strong></th>
			                <th width="80" align="right"><strong><? echo number_format($buyer_insp_pass_total,0); ?></strong></th>
			                <th width="80" align="right"><strong><? echo number_format($buyer_insp_faill_total,0); ?></strong></th>
			                <th width="80" align="right"><strong><? echo number_format($ex_qty_total,0); ?></strong></th>

			            </tr>

			            <tr bgcolor="#dddddd">
			                <th align="center" width="60"><strong>Avg : </strong></th>
			                <?
		                    $avg='';
		                    $avg_tot=0;
	                        foreach ( $prod_floor_array[1] as $rows )
	                        {
	                            $avg=$floor_wise_total[$rows][1]/ $value_define[1][$rows];
	                            ?>
	                            <th width="80" align="right"><strong><? echo number_format( $avg,0); ?></strong></th>
	                            <?
	                            $avg_tot+=$avg;
	                        }
		                    ?>
		                    <th width="80" align="right"><strong><?  echo number_format($avg_tot,0); ?></strong></th>
		                    <th width="80" align="right"><?
		                    $avg_tot = 0;
	                    	$avg_tot = ($value_define2['cut_delv']>0) ? $cutting_delv_total/ $value_define2['cut_delv'] : 0;
		                    echo number_format($avg_tot,0);
		                    ?></th>
		                    <th width="80" align="right"></th>
							<?
		                    $avg_in='';
		                    $avg_tot_in=0;
	                        foreach ( $prod_floor_array[4] as $rows )
	                        {
	                            $avg_in=$floor_wise_total[$rows][4]/ $value_define[4][$rows];
	                            ?>
	                            <th width="80" align="right"><strong><? echo number_format( $avg_in,0); ?></strong></th>
	                            <?
	                            $avg_tot_in+=$avg_in;
	                        }
		                    ?>
		                    <th width="80" align="right"><strong><?  echo number_format($avg_tot_in,0); ?></strong></th>
		                    <?
		                    $avg='';
		                    $avg_tot=0;
	                        foreach ( $prod_floor_array[5] as $rows )
	                        {
	                            $avg=$floor_wise_total[$rows][5]/ $value_define[5][$rows];
	                            ?>
	                            <th width="80" align="right"><strong><? echo number_format( $avg,0); ?></strong></th>
	                            <?
	                            $avg_tot+=$avg;
	                        }
		                    ?>
		                    <th width="80" align="right"><strong><?  echo number_format($avg_tot,0); ?></strong></th>
		                    <th width="80" align="right"><strong>
	                    	<?
	                    	$avg_tot = 0;
	                    	$avg_tot = ($value_define2['wash_iss']>0) ? $wash_issue_total/ $value_define2['wash_iss'] : 0;
	                    	echo number_format($avg_tot,0);

	                    	?>

		                    </strong></th>
		                    <th width="80" align="right"><strong>
		                    <?
	                    	$avg_tot = 0;
	                    	$avg_tot = ($value_define2['wash_rcv']>0) ? $wash_rcv_total / $value_define2['wash_rcv'] : 0;
	                    	echo number_format($avg_tot,0);

	                    	?>

		                    </strong></th>


							<?
							$avg='';
		                    $avg_tot=0;
	                        foreach ( $prod_floor_array[80] as $rows )
	                        {
								if($rows == 669)
								    {
										$avg=($floor_wise_total[$rows][80]+$floor_wise_total[624][11])/ $value_define[80][$rows];
										?>
											<th width="80" align="right"><strong><? echo number_format( $avg,0); ?></strong></th>
										<?
										$avg_tot+=$avg;
								    }
								else if( $rows == 331)
								    {
										$avg=($floor_wise_total[$rows][80]+$floor_wise_total[676][11])/ $value_define[80][$rows];
										?>
											<th width="80" align="right"><strong><? echo number_format( $avg,0); ?></strong></th>
										<?
										$avg_tot+=$avg;
								    }
								else
								    {
										$avg=$floor_wise_total[$rows][80]/ $value_define[80][$rows];
										?>
											<th width="80" align="right"><strong><? echo number_format( $avg,0); ?></strong></th>
										<?
										$avg_tot+=$avg;
								    }
	                        }
		                    ?>
							<th width="80" align="right"><?  echo number_format($avg_tot,0); ?></th>

		                    <?
		                    $avg='';
		                    $avg_tot=0;
	                        foreach ( $prod_floor_array[8] as $rows )
	                        {
	                            $avg=$floor_wise_total[$rows][8]/ $value_define[8][$rows];
	                            ?>
	                                <th width="80" align="right"><strong><? echo number_format( $avg,0); ?></strong></th>
	                            <?
	                            $avg_tot+=$avg;
	                        }
		                    ?>
		                    <th width="80" align="right"><?  echo number_format($avg_tot,0); ?></th>

		                    <th width="80" align="right" title="<?=$gr_tot_pro_min."/".$gr_tot_avlable_min;?>">
		                    	<?  echo number_format(($gr_tot_pro_min/$gr_tot_avlable_min)*100,2); ?>%
		                    </th>
		                    <th width="80" align="right"><?  //echo number_format($avg_tot,0); ?></th>
		                    <th width="80" align="right"><?  //echo number_format($avg_tot,0); ?></th>
		                    <th width="80" align="right" title="<?=$gr_tot_production."/".$gr_tot_target;?>">
		                    	<?  echo number_format(($gr_tot_production/$gr_tot_target)*100,2); ?>%
		                    </th>
		                    <th width="80" align="right">
		                    <?
	                    	$avg_tot = 0;
	                    	$avg_tot = ($value_define2['bi-pass']>0) ? $buyer_insp_pass_total / $value_define2['bi-pass'] : 0;
	                    	echo number_format($avg_tot,0);
	                    	?>
		                    </th>
		                    <th width="80" align="right">
		                    <?
	                    	$avg_tot = 0;
	                    	$avg_tot = ($value_define2['bi-faill']>0) ? $buyer_insp_faill_total / $value_define2['bi-faill'] : 0;
	                    	echo number_format($avg_tot,0);
	                    	?>
		                    </th>
		                    <th width="80" align="right">
	                    	<?
	                    	$avg_tot = 0;
	                    	$avg_tot = $ex_qty_total/ $value_define2['ex'];
	                    	echo number_format($avg_tot,0);
	                    	?>
		                    </th>

			            </tr>
		            </tfoot>
		        </table>
		    </div>
	    </fieldset>
	    <?
	}
	?>

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
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}

if($action=='buyer-insp-popup')
{
	extract($_REQUEST);
 	echo load_html_head_contents("Remarks", "../../../", 1, 1,$unicode,'','');
 	$lib_buyer=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
 	$location_cond = ($location!="") ? " and b.location_name=$location" : "";
 	$sql = "SELECT b.JOB_NO,b.STYLE_REF_NO,b.BUYER_NAME,c.PO_NUMBER, a.INSPECTED_BY,sum(a.inspection_qnty) as QTY from pro_buyer_inspection a,wo_po_details_master b,wo_po_break_down c where a.po_break_down_id=c.id and c.job_id=b.id and a.status_active=1 and a.is_deleted=0 and a.inspection_level=3 and a.inspection_date='$date' and a.inspection_status=$type and b.company_name=$company $location_cond group by b.job_no,b.style_ref_no,b.buyer_name,c.po_number,a.inspected_by";
 	// echo $sql;
 	$result = sql_select($sql);

    ?>
	<fieldset>
		<table width="530" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
			<caption><h2><?= ($type==1) ? "Buyer Insp. Passed Qty" : "Buyer Insp. Failed Qty";?>,Date: <?=$date;?></h2></caption>
			<thead>
				<th width="20">SL No</th>
				<th width="70">Job</th>
				<th width="85">Style</th>
				<th width="85">Buyer</th>
				<th width="85">Order No</th>
				<th width="85">Inspected By</th>
				<th width="60">Inspec. Qty</th>
				<th width="40">Inspec. Status</th>
			</thead>
			<tbody>
			<?
			$i = 1;
			$total = 0;
			foreach($result as $row)
			{
				?>
				<tr>
					<td><? echo $i; ?></td>
					<td><p><?= $row['JOB_NO'];?></p></td>
					<td><p><?= $row['STYLE_REF_NO'];?></p></td>
					<td><p><?= $lib_buyer[$row['BUYER_NAME']];?></p></td>
					<td><p><?= $row['PO_NUMBER'];?></p></td>
					<td><p><?= $inspected_by_arr[$row['INSPECTED_BY']];?></p></td>
					<td><p align="right"><?= $row['QTY'];?></p></td>
					<td><?=($type==1) ? "Passed" : "Failed";?></td>
				</tr>
				 <?
				 $i++;
				 $total += $row['QTY'];
			}
			?>
			</tbody>
			<tfoot>
				<th align="right" colspan="6">Total</th>
				<th align=""><? echo $total; ?></th>
				<th align=""></th>
			</tfoot>
		</table>
    </fieldset>
    <?
}//end if

if($action=='ex-factory-popup')
{
	extract($_REQUEST);
 	echo load_html_head_contents("Remarks", "../../../", 1, 1,$unicode,'','');
	$lib_country=return_library_array( "select id, country_name from lib_country",'id','country_name');
 	$lib_buyer=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$location_cond = ($location!="") ? " and b.delivery_location_id=$location" : "";
 	$sql = "SELECT c.JOB_NO,c.STYLE_REF_NO,c.BUYER_NAME,d.PO_NUMBER, a.CHALLAN_NO,a.COUNTRY_ID,sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as FACTORY_QNTY,sum(CASE WHEN a.entry_form=85 THEN a.ex_factory_qnty ELSE 0 END) as RETURN_QNTY from pro_ex_factory_mst a,pro_ex_factory_delivery_mst b,wo_po_details_master c,wo_po_break_down d where c.id=d.job_id and d.id=a.po_break_down_id and a.delivery_mst_id=b.id and a.status_active=1 and a.is_deleted=0 and b.delivery_date='$date' and b.company_id=$company group by c.job_no,c.style_ref_no,c.buyer_name,d.po_number,a.challan_no,a.country_id";
 	// echo $sql;
 	$result = sql_select($sql);

    ?>
	<fieldset>
		<table width="530" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
			<caption><h2>Ex-Factory Details,Date: <?=$date;?></h2></caption>
			<thead>
				<th width="20">SL No</th>
				<th width="60">Challan</th>
				<th width="60">Job</th>
				<th width="80">Style</th>
				<th width="80">Buyer</th>
				<th width="80">Order No</th>
				<th width="80">Country</th>
				<th width="40">Delv. Qty</th>
				<th width="40">Return Qty</th>
			</thead>
			<tbody>
			<?
			$i = 1;
			$ex_total = 0;
			$rtn_total = 0;
			foreach($result as $row)
			{
				?>
				<tr>
					<td><? echo $i; ?></td>
					<td><? echo $row['CHALLAN_NO']; ?></td>
					<td><?= $row['JOB_NO'];?></td>
					<td><?= $row['STYLE_REF_NO'];?></td>
					<td><?= $lib_buyer[$row['BUYER_NAME']];?></td>
					<td><?= $row['PO_NUMBER'];?></td>
					<td><?= $lib_country[$row['COUNTRY_ID']];?></td>
					<td><p align="right"><?= $row['FACTORY_QNTY'];?></p></td>
					<td><p align="right"><?= $row['RETURN_QNTY'];?></p></td>
				</tr>
				 <?
				 $i++;
				 $ex_total += $row['FACTORY_QNTY'];
				 $rtn_total += $row['RETURN_QNTY'];
			}
			?>
			</tbody>
			<tfoot>
				<th align="right" colspan="7">Total</th>
				<th align=""><? echo $ex_total; ?></th>
				<th align=""><? echo $rtn_total; ?></th>
			</tfoot>
		</table>
    </fieldset>
    <?
}//end if
?>
