<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 150, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "",0 );
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	//echo $datediff;
	$cbo_company=str_replace("'","",$cbo_company_id);
	//$cbo_working_company=str_replace("'","",$cbo_working_company_id);
	$cbo_location=str_replace("'","",$cbo_location_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$cbo_production_type=str_replace("'","",$cbo_production_type);

	if($cbo_company==0) $cbo_company_cond=""; else $cbo_company_cond=" and a.company_id=$cbo_company";
	//if($cbo_working_company==0) $company_working_cond=""; else $company_working_cond=" and a.serving_company=$cbo_working_company";

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );

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

        $sql_floor_sewing=sql_select("SELECT a.floor_id from  pro_garments_production_mst a, lib_prod_floor b where a.location=$cbo_location $cbo_company_cond and b.id=a.floor_id and b.status_active=1 and a.production_type=5 and a.status_active=1 and a.is_deleted=0 and a.floor_id !=0  group by a.floor_id order by  a.floor_id ");

        $sql_floor_finishing=sql_select("SELECT a.floor_id from  pro_garments_production_mst a, lib_prod_floor b where a.location=$cbo_location $cbo_company_cond and b.id=a.floor_id and b.status_active=1 and a.production_type=8 and a.status_active=1 and a.is_deleted=0 and a.floor_id !=0  group by a.floor_id order by  a.floor_id ");

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

			$head_arr=array(2=>"Knitting Production",35=>"Dyeing Production",1=>"Cutting Production",5=>"Sewing Production",8=>"Packing");//103=>"Iron",11=>"Poly",7=>"Fabric Finishing",
			$floor_arr = return_library_array("select id,floor_name from  lib_prod_floor ","id","floor_name");
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
	        <div align="center" style="height:auto;">
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
	                            $grand_total_tot+=$unit_wise_total_array2[$head][$rows[csf("floor_id")]][1]+$unit_wise_total_array2[$head][$rows[csf("floor_id")]][3];
	                            $grand_total_tot_sub+=$unit_wise_total_array2[$head][$rows[csf("floor_id")]][3];
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

			$head_arr=array(2=>"Knitting Production",35=>"Dyeing Production"); //,7=>"Fabric Finishing"
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

if($action=="report_generate_050323")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	//echo $datediff;
	$cbo_company=str_replace("'","",$cbo_company_id);
	//$cbo_working_company=str_replace("'","",$cbo_working_company_id);
	$cbo_location=str_replace("'","",$cbo_location_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$cbo_production_type=str_replace("'","",$cbo_production_type);

	if($cbo_company==0) $cbo_company_cond=""; else $cbo_company_cond=" and a.company_id=$cbo_company";
	//if($cbo_working_company==0) $company_working_cond=""; else $company_working_cond=" and a.serving_company=$cbo_working_company";

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );

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

        $sql_floor_sewing=sql_select("SELECT a.floor_id from  pro_garments_production_mst a, lib_prod_floor b where a.location=$cbo_location $cbo_company_cond and b.id=a.floor_id and b.status_active=1 and a.production_type=5 and a.status_active=1 and a.is_deleted=0 and a.floor_id !=0  group by a.floor_id order by  a.floor_id ");

        $sql_floor_finishing=sql_select("SELECT a.floor_id from  pro_garments_production_mst a, lib_prod_floor b where a.location=$cbo_location $cbo_company_cond and b.id=a.floor_id and b.status_active=1 and a.production_type=8 and a.status_active=1 and a.is_deleted=0 and a.floor_id !=0  group by a.floor_id order by  a.floor_id ");

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

			$head_arr=array(2=>"Knitting Production",35=>"Dyeing Production",7=>"Fabric Finishing" ,1=>"Cutting Production",5=>"Sewing Production",8=>"Packing");//103=>"Iron",11=>"Poly",
			$floor_arr = return_library_array("select id,floor_name from  lib_prod_floor ","id","floor_name");
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
	        <div align="center" style="height:auto;">
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
	                            $grand_total_tot+=$unit_wise_total_array2[$head][$rows[csf("floor_id")]][1]+$unit_wise_total_array2[$head][$rows[csf("floor_id")]][3];
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

			$head_arr=array(2=>"Knitting Production",35=>"Dyeing Production"); //,7=>"Fabric Finishing"
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
?>
