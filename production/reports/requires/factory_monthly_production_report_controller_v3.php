<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.others.php');
//require_once('../../../includes/class4/class.commisions.php');
//require_once('../../../includes/class4/class.fabrics.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="load_report_format")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id in(60) and is_deleted=0 and status_active=1");
 	echo trim($print_report_format);	
	exit();

}

if ($action=="load_drop_down_location")
{
	extract($_REQUEST);
    //$choosenCompany = $choosenCompany; 
	$choosenCompany = $data; 
	echo create_drop_down( "cbo_location", 130, "select distinct id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in($choosenCompany) order by location_name","id,location_name", 1, "-- Select --", "", "",0 );     
	exit();	 
}

if($action=="party_popup")
{
	echo load_html_head_contents("Company Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array; var selected_name = new Array;
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			
			if (str!="") str=str.split("_");
			 
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_party_id').val( id );
			$('#hide_party_name').val( name );
		}
    </script>
        <input type="hidden" name="hide_party_name" id="hide_party_name" value="" />
        <input type="hidden" name="hide_party_id" id="hide_party_id" value="" />
        <input type="hidden" name="hidd_type" id="hidd_type" value="<?=$type; ?>" />
	<?

	$sql="select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name";
	echo create_list_view("tbl_list_search", "Company Name", "380","380","270",0, $sql , "js_set_value", "id,company_name", "", 1, "0", $arr , "company_name", "",'setFilterGrid("tbl_list_search",-1);','0','',1) ;
	
   exit(); 
} 

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$cbo_company=str_replace("'","",$cbo_company_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$report_cat=str_replace("'","",$cbo_report_category);
	$cbo_working_company=str_replace("'","",$cbo_working_company_id);
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	if(str_replace("'","",$cbo_location)==0){$location_con="";}else{$location_con=" and a.location=$cbo_location";}
	
	if($cbo_company==0) $cbo_company_cond=""; else $cbo_company_cond=" and a.company_id in($cbo_company)";
	if($cbo_working_company==0) $company_working_cond=""; else $company_working_cond=" and a.knitting_company in($cbo_working_company)";
	$comp=str_replace("'", "", $cbo_company_id);
	$work_comp=$cbo_working_company;
	$_SESSION["comp"]=""; 
	$_SESSION["comp"]=$comp;
	$_SESSION["work_comp"]=""; 
	$_SESSION["work_comp"]=$work_comp;
	ob_start();	
	
	if($report_cat==1)
	{		
		?>
        <table width="2410px" cellpadding="0" cellspacing="0" id="caption" align="center">
            <tr>
            <td align="center" width="100%" colspan="23" class="form_caption"><strong style="font-size:18px">
			<? if($cbo_company==0){ echo "Working Company Name:". $company_library[$cbo_working_company];} else{ 
			$com_arr=explode(",",str_replace("'","",$cbo_company));
			$comName="";
			foreach($com_arr as $comID)
			{
				$comName.=$company_library[$comID].',';
			}
			//echo chop($comName,",");
			echo "Company Name:". chop($comName,",");} 
			?></strong></td>
            </tr> 
            <tr>  
               <td align="center" width="100%" colspan="23" class="form_caption" ><strong style="font-size:14px"><? echo $report_title; ?></strong></td>
            </tr>
            <tr>  
               <td align="center" width="100%" colspan="23" class="form_caption" ><strong style="font-size:14px"> <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
            </tr>  
        </table>
        
      
      	<?
	  	if($cbo_company==0) $cbo_company_cond_1=""; else $cbo_company_cond_1="and company_name in($cbo_company)";
		//$smv_source=return_field_value("smv_source","variable_settings_production","$cbo_company_cond_1 and variable_list=25 and status_active=1 and is_deleted=0");
		$smv_source=return_field_value("smv_source","variable_settings_production","variable_list=25 $cbo_company_cond_1 and status_active=1 and is_deleted=0");
		//echo $smv_source;die;
		if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
		$item_smv_array=array();
		//$smv_source=2;
		if($smv_source==3)
		{
			$sql_item="SELECT b.id, a.sam_style, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,3)";
			$resultItem=sql_select($sql_item);
			foreach($resultItem as $itemData)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
			}
		}
		else
		{
			if($cbo_company==0) $cbo_company_cond_2=""; else $cbo_company_cond_2=" and a.company_name in($cbo_company)";
			$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no $cbo_company_cond_2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,3)";
			$resultItem=sql_select($sql_item);
			foreach($resultItem as $itemData)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]['smv_pcs']=$itemData[csf('smv_pcs')];
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]['smv_pcs_precost']=$itemData[csf('smv_pcs_precost')];
			}
		}
		
		$tpdArr=array(); $tsmvArr=array();
		//$cbo_working_company
		if($cbo_company==0) $cbo_company_cond_3=""; else $cbo_company_cond_3=" and b.company_id in($cbo_company)";
		if($cbo_working_company==0) $cboWorkingComCondManpower=""; else $cboWorkingComCondManpower=" and b.company_id in($cbo_working_company)";
		if(str_replace("'","",$cbo_location)==0){$cboWorkingComCondLocation="";}else{$cboWorkingComCondLocation=" and b.location_id=$cbo_location";}

        $tpd_data_arr=sql_select( "SELECT a.pr_date, sum(a.target_per_hour*a.working_hour) tpd, sum(a.man_power*a.working_hour) tsmv from prod_resource_dtls a, prod_resource_mst b where b.id=a.mst_id $cbo_company_cond_3 $cboWorkingComCondManpower $cboWorkingComCondLocation and a.pr_date between '$date_from' and '$date_to' and a.is_deleted=0 and b.is_deleted=0 group by  a.pr_date");
		
        foreach($tpd_data_arr as $row)
        {
			$production_date=date("Y-m-d", strtotime($row[csf('pr_date')])); 
           // $tpdArr[$production_date]+=$row[csf('tpd')];
			 $tsmvArr[change_date_format($production_date)]['smv']=$row[csf('tsmv')]*60;
        } //var_dump($tsmvArr[$production_date]['smv']);
		
		$job_array=array(); 
		$job_id_array=array(); 
		/*$job_sql="SELECT a.id, a.unit_price,b.buyer_name,b.company_name,a.po_quantity, b.job_no, b.total_set_qnty,b.set_smv from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.is_deleted=0 and a.status_active in(1,3) and b.is_deleted=0 and b.status_active=1 ";*/
		if($cbo_company){$company_cond = " and a.company_name in($cbo_company)";}else{$company_cond ="";}
		$job_sql="SELECT a.id as job_id, a.job_no, a.total_set_qnty,b.id, a.buyer_name,a.company_name,b.po_quantity,b.unit_price,c.smv_pcs,c.set_item_ratio,a.set_smv from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_id=a.id and b.job_id=c.job_id $company_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		
		
		
		$job_sql_result=sql_select($job_sql);
		foreach($job_sql_result as $row)
		{
			// $job_array[$row[csf("id")]]['unit_price']=number_format(($row[csf("unit_price")]/$row[csf("total_set_qnty")]),2);
			$job_array[$row[csf("id")]]['unit_price']=($row[csf("unit_price")]/$row[csf("total_set_qnty")]);
			$job_array[$row[csf("id")]]['job_no']=$row[csf("job_no")];
			$job_array[$row[csf("id")]]['total_set_qnty']=$row[csf("total_set_qnty")];
			$job_array[$row[csf("id")]]['set_smv']=$row[csf("set_smv")];
			$job_array_summary[$row[csf("company_name")]][$row[csf("buyer_name")]]['po_qty']+=$row[csf("po_quantity")];
			//$job_array[$row[csf("id")]][$row[csf("set_item_ratio")]]['smv_pcs']=$row[csf("smv_pcs")];
			$job_id_array[$row[csf("job_id")]]=$row[csf("job_id")];
		}

		$job_id_cond = where_con_using_array('job_id',0,$job_id_array);
		$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst where status_active=1 $job_id_cond","job_no","costing_per"); 
		$tot_cost_arr = return_library_array("select job_no, cm_cost from wo_pre_cost_dtls where status_active=1 $job_id_cond","job_no","cm_cost"); 	  
	  
		  $total_knit=0;
		  $total_finishing=0;
		  $total_print=0;
		  $total_emb=0;
		  $total_cutting=0;
		  $total_cutting_inhouse=0;
		  $total_cutting_subcontract=0;
		  $total_sew=0;
		  $total_sew_inhouse=0;
		  $total_sew_subcontract=0;
		  $total_finishg=0;
		  $total_finish_inhouse=0;
		  $total_finish_subcontract=0;
		  $total_carton=0;
		 if($cbo_company==0) $cbo_company_cond_4=""; else $cbo_company_cond_4=" and a.company_id in($cbo_company)";
		 if($cbo_working_company==0) $company_working_cond_1=""; else $company_working_cond_1=" and a.serving_company in($cbo_working_company)";
		 $dtls_sql="SELECT a.production_date, a.po_break_down_id as po_breakdown_id, a.item_number_id,c.buyer_name,a.company_id,
					sum(CASE WHEN a.production_type =1 THEN a.production_quantity END) AS cutting_qnty,
					sum(CASE WHEN a.production_type =1 and a.production_source=1 THEN a.production_quantity END) AS cutting_qnty_inhouse,
					sum(CASE WHEN a.production_type =1 and a.production_source=3 THEN a.production_quantity END) AS cutting_qnty_outbound, 
					
					sum(CASE WHEN a.production_type =3 and a.embel_name=1 THEN a.production_quantity END) AS printing_qnty,
					sum(CASE WHEN a.production_type =3 and a.embel_name=1 and a.production_source=1 THEN a.production_quantity END) AS printing_qnty_inhouse,
					sum(CASE WHEN a.production_type =3 and a.embel_name=1 and a.production_source=3 THEN a.production_quantity END) AS printing_qnty_outbound, 
					
					sum(CASE WHEN a.production_type =3 and a.embel_name=2 THEN a.production_quantity END) AS emb_qnty,
					sum(CASE WHEN a.production_type =3 and a.embel_name=2 and a.production_source=1 THEN a.production_quantity END) AS emb_qnty_inhouse,
					sum(CASE WHEN a.production_type =3 and a.embel_name=2 and a.production_source=3 THEN a.production_quantity END) AS emb_qnty_outbound,
					 
					sum(CASE WHEN a.production_type =5 THEN a.production_quantity END) AS sewing_qnty,
					sum(CASE WHEN a.production_type =5 and a.production_source=1 THEN a.production_quantity END) AS sewingout_qnty_inhouse,
					sum(CASE WHEN a.production_type =5 and a.production_source=3 THEN a.production_quantity END) AS sewingout_qnty_outbound, 
					
					 
					sum(CASE WHEN a.production_type =4 THEN a.production_quantity END) AS sewing_input_qnty,
					sum(CASE WHEN a.production_type =4 and a.production_source=1 THEN a.production_quantity END) AS sewing_input_qnty_inhouse,
					sum(CASE WHEN a.production_type =4 and a.production_source=3 THEN a.production_quantity END) AS sewing_input_qnty_outbound, 
					
					sum(CASE WHEN a.production_type =8 THEN a.production_quantity END) AS finish_qnty,
					sum(CASE WHEN a.production_type =8 and a.production_source=1 THEN a.production_quantity END) AS finish_qnty_inhouse, 
					sum(CASE WHEN a.production_type =8 and a.production_source=3 THEN a.production_quantity END) AS finish_qnty_outbound,
					sum(CASE WHEN a.production_type =8  THEN a.carton_qty END) AS carton_qty 
					from pro_garments_production_mst a, wo_po_break_down b,wo_po_details_master c 
					where a.po_break_down_id=b.id and c.job_no=b.job_no_mst  $location_con $cbo_company_cond_4 $company_working_cond_1 and a.production_date between '$date_from' and '$date_to' and a.is_deleted=0 and a.status_active=1 and b.status_active in(1,3) and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.production_date, a.po_break_down_id, a.item_number_id,c.buyer_name,a.company_id  order by a.production_date asc";
			//and a.production_date between '$date_from' and '$date_to'
			//and b.id in(29714,29715) 
			 //echo $dtls_sql;
			 $dtls_sql_result=sql_select($dtls_sql);
			 $prod_date=array();$po_id=""; $po_sewing_qty=array();
			 foreach($dtls_sql_result as $row)
			 {
				//if($po_id=="")$po_id=$row[csf("po_breakdown_id")]; else $po_id=$po_id.",".$row[csf("po_breakdown_id")];
				 
				 $production_date=date("Y-m-d", strtotime($row[csf('production_date')])); 
				 $prod_date[change_date_format($row[csf("production_date")])]['po_breakdown_id'].=$row[csf("po_breakdown_id")].",";
				 $prod_date[change_date_format($row[csf("production_date")])]['production_date']=$row[csf("production_date")];
				 $prod_date[change_date_format($row[csf("production_date")])]['printing_qnty']+=$row[csf("printing_qnty")];
				 $prod_date[change_date_format($row[csf("production_date")])]['emb_qnty']+=$row[csf("emb_qnty")];
				 $prod_date[change_date_format($row[csf("production_date")])]['cutting_qnty_inhouse']+=$row[csf("cutting_qnty_inhouse")];
				 $prod_date[change_date_format($row[csf("production_date")])]['cutting_qnty_outbound']+=$row[csf("cutting_qnty_outbound")];
				 $prod_date[change_date_format($row[csf("production_date")])]['cutting_qnty']+=$row[csf("cutting_qnty")];
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_qnty_inhouse']+=$row[csf("sewingout_qnty_inhouse")];
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_qnty_outbound']+=$row[csf("sewingout_qnty_outbound")];
				 $prod_date[change_date_format($row[csf("production_date")])]['sewing_qnty']+=$row[csf("sewing_qnty")];
				 
				 // array for Buyer wise summary part
				// $prod_date_buyer_wise[$row[csf("company_id")]][$row[csf("buyer_name")]]['po_quantity']+=$row[csf("po_quantity")];
				 $prod_date_buyer_wise[$row[csf("company_id")]][$row[csf("buyer_name")]]['cutting_qnty']+=$row[csf("cutting_qnty")];
				 $prod_date_buyer_wise[$row[csf("company_id")]][$row[csf("buyer_name")]]['sewing_input_qnty']+=$row[csf("sewing_input_qnty")];
				 $prod_date_buyer_wise[$row[csf("company_id")]][$row[csf("buyer_name")]]['sewing_qnty']+=$row[csf("sewing_qnty")]; //sew output
				 //$prod_date_buyer_wise[$row[csf("company_id")]][$row[csf("buyer_name")]]['sewing_qnty']+=$row[csf("sewing_qnty")]; //sew output
				 $prod_date_buyer_wise[$row[csf("company_id")]][$row[csf("buyer_name")]]['finish_qnty']+=$row[csf("finish_qnty")];
				 $prod_date_buyer_wise[$row[csf("company_id")]][$row[csf("buyer_name")]]['buyer_name']=$row[csf("buyer_name")];
				 /*
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_qnty_outbound_pcs']+=($row[csf("sewingout_qnty_outbound")]*$job_array[$row[csf("po_breakdown_id")]]['set_smv']);
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_qnty_inhouse_pcs']+=($row[csf("sewingout_qnty_inhouse")]*$job_array[$row[csf("po_breakdown_id")]]['set_smv']);
				 */
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_qnty_outbound_pcs']+=($row[csf("sewingout_qnty_outbound")]*$item_smv_array[$row[csf("po_breakdown_id")]][$row[csf("item_number_id")]]['smv_pcs']);
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_qnty_inhouse_pcs']+=($row[csf("sewingout_qnty_inhouse")]*$item_smv_array[$row[csf("po_breakdown_id")]][$row[csf("item_number_id")]]['smv_pcs']);
				 
				 $item_smv=0;
				if($smv_source==2)
				{
					$item_smv=$item_smv_array[$row[csf("po_breakdown_id")]][$row[csf("item_number_id")]]['smv_pcs_precost'];
				}
				else if($smv_source==3)
				{
					$item_smv=$item_smv_array[$row[csf("po_breakdown_id")]][$row[csf("item_number_id")]];	
				}
				else
				{
					$item_smv=$item_smv_array[$row[csf("po_breakdown_id")]][$row[csf("item_number_id")]]['smv_pcs'];	
				}
				 
				 $prod_date[change_date_format($row[csf("production_date")])]['sewing_qnty_smv']+=$row[csf("sewing_qnty")]*$item_smv;
				 
				 $prod_date[change_date_format($row[csf("production_date")])]['finish_qnty_inhouse']+=$row[csf("finish_qnty_inhouse")];
				 $prod_date[change_date_format($row[csf("production_date")])]['finish_qnty_outbound']+=$row[csf("finish_qnty_outbound")];
				 $prod_date[change_date_format($row[csf("production_date")])]['finish_qnty']+=$row[csf("finish_qnty")];
				 $prod_date[change_date_format($row[csf("production_date")])]['carton_qty']+=$row[csf("carton_qty")];
				 
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_value_inhouse']+=$row[csf("sewingout_qnty_inhouse")]*$job_array[$row[csf("po_breakdown_id")]]['unit_price'];
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_value_outbound']+=$row[csf("sewingout_qnty_outbound")]*$job_array[$row[csf("po_breakdown_id")]]['unit_price'];
				  //$prod_date_buyer_wise[$row[csf("company_id")]][$row[csf("buyer_name")]]['sewing_fob_val_input_qnty']+=$row[csf("sewingout_qnty_inhouse")]*$job_array[$row[csf("po_breakdown_id")]]['unit_price'];
				  $prod_date_buyer_wise[$row[csf("company_id")]][$row[csf("buyer_name")]]['sewingUnitprice_fob']+=($row[csf("sewingout_qnty_inhouse")]*$job_array[$row[csf("po_breakdown_id")]]['unit_price']) + ($row[csf("sewingout_qnty_outbound")]*$job_array[$row[csf("po_breakdown_id")]]['unit_price']);
				 
				 $cm_value=0; $cm_value_in=0; $cm_value_out=0; $sewing_qty_in=0; $sewing_qty_out=0;
				 //$sewing_qnty=$row[csf("sewing_qnty")];
				 $sewing_qty_in=$row[csf("sewingout_qnty_inhouse")];
				 $sewing_qty_out=$row[csf("sewingout_qnty_outbound")];
				 $job_no=$job_array[$row[csf("po_breakdown_id")]]['job_no'];
				 $total_set_qnty=$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty'];
				 $costing_per=$costing_per_arr[$job_no];
				 
				 if($costing_per==1) $dzn_qnty=12;
				 else if($costing_per==3) $dzn_qnty=12*2;
				 else if($costing_per==4) $dzn_qnty=12*3;
				 else if($costing_per==5) $dzn_qnty=12*4;
				 else $dzn_qnty=1;
							
				 $dzn_qnty=$dzn_qnty*$total_set_qnty;
				 //$cm_value=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewing_qnty;
				 $cm_value_in=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewing_qty_in;
				 $cm_value_out=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewing_qty_out;
				
				 //$prod_date[change_date_format($row[csf("production_date")])]['cm_value']+=$cm_value;
				 $prod_date[change_date_format($row[csf("production_date")])]['cm_value_in']+=$cm_value_in;
				 $prod_date[change_date_format($row[csf("production_date")])]['cm_value_out']+=$cm_value_out;
				 $prod_date_buyer_wise[$row[csf("company_id")]][$row[csf("buyer_name")]]['cm_value_in']+=$cm_value_in+$cm_value_out;
				 $prod_date_buyer_wise[$row[csf("company_id")]][$row[csf("buyer_name")]]['sewOut_qnty_inhouse_pcs']+=($row[csf("sewingout_qnty_inhouse")]*$item_smv_array[$row[csf("po_breakdown_id")]][$row[csf("item_number_id")]]['smv_pcs']);
			}
			//print_r($prod_date_buyer_wise);
			// echo "<pre>";  print_r($prod_date);  echo "</pre>";
			
			if(str_replace("'","",$cbo_location)==0){$location_con_rcv="";}else{$location_con_rcv=" and a.location_id=$cbo_location";}
			if($cbo_company==0) $cbo_company_cond_5=""; else $cbo_company_cond_5=" and a.company_id in($cbo_company)";
			if($cbo_working_company==0) $company_working_cond_2=""; else $company_working_cond_2=" and a.knitting_company in($cbo_working_company)";
			$knited_query="select a.receive_date as production_date, sum(b.grey_receive_qnty) as kniting_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id $cbo_company_cond_5  $company_working_cond_2 $location_con_rcv and a.receive_date between '$date_from' and '$date_to' and a.entry_form=2 and a.item_category=13 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.receive_date";
			
			
			$knited_query_result=sql_select($knited_query);
			$count_knit=count($knited_query_result);
			foreach( $knited_query_result as $knit_row)
			{
				$prod_date[change_date_format($knit_row[csf("production_date")])]['kniting_qnty']=$knit_row[csf("kniting_qnty")];
			}
			//var_dump($prod_datek);
			$finish_query="select a.receive_date as production_date, sum(b.receive_qnty) as finishing_qnty from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id $cbo_company_cond_5 $company_working_cond_2 $location_con_rcv and a.receive_date between '$date_from' and '$date_to' and a.entry_form=7 and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.receive_date";
			//echo $finish_query;
			$finish_query_result=sql_select($finish_query);
			$count_finish=count($finish_query_result);
			foreach( $finish_query_result as $finish_row)
			{
				$prod_date[change_date_format($finish_row[csf("production_date")])]['finishing_qnty']=$finish_row[csf("finishing_qnty")];
			}
			//var_dump($prod_date);
			
			if($cbo_company==0) $cbo_company_cond_6=""; else $cbo_company_cond_6=" and c.company_name in($cbo_company)";
			if( str_replace("'", "",$cbo_working_company_id)==0) $cbo_delivery_com_cond=""; else $cbo_delivery_com_cond=" and d.delivery_company_id in($cbo_working_company)";	
			if( str_replace("'", "",$cbo_working_company_id)>0 &&  str_replace("'", "",$cbo_location)>0 )
			{
				
				$delv_location_con=" and d.delivery_location_id=$cbo_location ";
				$location_con="";
			}
			else
			{
				$delv_location_con="";
				
			}

			$exfactory_res = sql_select("SELECT a.ex_factory_date, a.po_break_down_id,c.company_name,c.buyer_name,   
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c,pro_ex_factory_delivery_mst d
			where a.delivery_mst_id=d.id and  a.po_break_down_id=b.id and b.job_no_mst=c.job_no $cbo_company_cond_6 $cbo_delivery_com_cond $location_con $delv_location_con and a.is_deleted=0 and a.status_active=1 and b.status_active in(1,3) and a.ex_factory_date between '$date_from' and '$date_to' group by a.ex_factory_date, a.po_break_down_id,c.company_name,c.buyer_name");
			
			/*echo "select a.ex_factory_date, a.po_break_down_id,c.company_name,c.buyer_name,   
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no $cbo_company_cond_6 $location_con and a.is_deleted=0 and a.status_active=1 and a.ex_factory_date between '$date_from' and '$date_to' group by a.ex_factory_date, a.po_break_down_id,c.company_name,c.buyer_name";*/
			
			foreach($exfactory_res as $ex_row)
			{
				$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['po_breakdown_id'].=$ex_row[csf("po_break_down_id")].",";
				$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_factory']+=$ex_row[csf("ex_factory_qnty")];
				$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_factory_val']+=$ex_row[csf("ex_factory_qnty")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']);

  				//for summery part
				 $ex_cm_value_in=0; $ex_sewing_qty_in=0;
				 $ex_sewing_qty_in=$ex_row[csf("ex_factory_qnty")];
				 $job_no_ex=$job_array[$ex_row[csf("po_break_down_id")]]['job_no'];
				 $total_ex_set_qnty=$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty'];
				 $costing_per_ex=$costing_per_arr[$job_no_ex];
				 
				 if($costing_per_ex==1) $dzn_qnty_ex=12;
				 else if($costing_per_ex==3) $dzn_qnty_ex=12*2;
				 else if($costing_per_ex==4) $dzn_qnty_ex=12*3;
				 else if($costing_per_ex==5) $dzn_qdzn_qnty_exnty=12*4;
				 else $dzn_qnty_ex=1;
							
				 $dzn_qnty_ex=$dzn_qnty_ex*$total_ex_set_qnty;
				 $ex_cm_value_in=($tot_cost_arr[$job_no_ex]/$dzn_qnty_ex)*$ex_sewing_qty_in;
				 $prod_date_buyer_wise[$ex_row[csf("company_name")]][$ex_row[csf("buyer_name")]]['buyer_name']=$ex_row[csf("buyer_name")];
				 $prod_date_buyer_wise[$ex_row[csf("company_name")]][$ex_row[csf("buyer_name")]]['ex_cm_value_in']+=$ex_cm_value_in;
				 $prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_cm_value_in']+=$ex_cm_value_in;
				 $prod_date_buyer_wise[$ex_row[csf("company_name")]][$ex_row[csf("buyer_name")]]['ex_factory_smry']+=$ex_row[csf("ex_factory_qnty")];		
				 $prod_date_buyer_wise[$ex_row[csf("company_name")]][$ex_row[csf("buyer_name")]]['ex_factory_smry_unitPrice']=$job_array[$ex_row[csf("po_break_down_id")]]['unit_price'];
				 
				$prod_date_buyer_wise[$ex_row[csf("company_name")]][$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal']+=$ex_row[csf("ex_factory_qnty")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']);
				 
				// end for summary part
			}
			
			 ksort($prod_date);
			 $i=1;
			 $printing=0; $embing=0; $cuting_in=0; $cuting_out=0; $cuting=0; $sewing_in=0; $sewing_out=0; $sewing=0; $finish_in=0; $finish_out=0; $finish=0; $carton=0; $ord_in=0; $ord_out=0; $ord_tot=0;
			 
			if($cbo_company==0) $cbo_company_yes=$cbo_working_company.'_'.'workingComp'; else $cbo_company_yes=$cbo_company.'_'.'mainComp';
		?>
        <table width="1300px " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
         <p style="float:left"><strong>Buyer Wise Summary Part</strong></p>
            <thead>
            	<tr>
                    <th width="30">SL</th>
                    <th width="150">Buyer Name</th>
                    <th width="100">Order Qty</th>
                    <th width="100">Cut Qty</th>
                    <!--<th width="100">Sew Input</th>-->
                   <!-- <th width="100">Sew Output Target</th>-->
                    <th width="100">Sew Output</th>
                    <th width="100">Produced Minute</th>
                    <th width="100">Total Finish</th>
                    <th width="100">Sewing CM Cost</th>
                    <th width="100">Sewing FOB Value</th>
                    <th width="100">Ex Factory</th>
                    <th width="100">Ex Factory CM Value</th>
                    <th width="100">Ex Factory FOB Value</th>
                    <th>Sew Out to Ship Bal</th>
                </tr>
            </thead>
        </table>
        <div style="max-height:420px; overflow-y:scroll; width:1320px" id="scroll_body_summery">
       	<table cellspacing="0" border="1" class="rpt_table"  width="1300px" rules="all" id="scroll_body_summery" >
        <?
        // echo "<pre>";
        // print_r($prod_date);
        // echo "</pre>";
		$inc=1;
		$all_po_ids="";
		/*for($jk=0;$jk<$datediff;$jk++)
			{*/
			foreach($prod_date_buyer_wise as $companyKey =>$comp_value)
			{
				foreach($comp_value as $buyerKey =>$buyer_value)
				{
				
				if ($inc%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				/*$date_all=add_date(str_replace("'","",$txt_date_from),$jk);
				$newdate =change_date_format($date_all);
				$po_id=$prod_date[$newdate]['po_breakdown_id'];
				//echo $all_po_id=chop($po_id,',');
				
				$all_po_ids.=$prod_date[$newdate]['po_breakdown_id'];
				//echo $sql_select_buyer="select b.buyer_name from a.wo_po_break_down,b.wo_po_details_master where a.id in($all_po_id) and a.job_no_mst=b.job_no";
				
				$produce_qty=$prod_date[$newdate]['sewingout_qnty_inhouse_pcs']/60;
				$effiecy_aff_perc=$produce_qty/($tsmvArr[$newdate]['smv']/60)*100;*/
				$poQTY=$job_array_summary[$companyKey][$buyerKey]['po_qty'];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $inc; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $inc; ?>">
                    <td width="30" ><? echo $inc; ?></td>
					<td width="150"><? echo $buyer_short_library[$prod_date_buyer_wise[$companyKey][$buyerKey]['buyer_name']]; ?></td>
                    <td width="100" align="right"><? echo number_format($poQTY,2); $smry_tot_po_qnty+=$poQTY; ?></td>
					<td width="100" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['cutting_qnty'],2); $smry_tot_cutting_qnty+=$prod_date_buyer_wise[$companyKey][$buyerKey]['cutting_qnty']; ?></td>
                    <?php /*?><td width="100" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_input_qnty'],2); $smry_tot_sewing_input_qnty+=$prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_input_qnty']; ?></td><?php */?>
					<?php /*?><td width="100" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_qnty'],2); $smry_tot_sewing_qnty+=$prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_qnty'];  ?></td><?php */?>
                    <td width="100" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_qnty'],2); $smry_tot_sewing_qnty+=$prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_qnty']; ?></td>
                    <td width="100" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['sewOut_qnty_inhouse_pcs'],2); $tot_sewOut_qnty_inhouse_pcs+=$prod_date_buyer_wise[$companyKey][$buyerKey]['sewOut_qnty_inhouse_pcs']; ?></td>
                    <td width="100" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['finish_qnty'],2); $smry_tot_finish_qnty+=$prod_date_buyer_wise[$companyKey][$buyerKey]['finish_qnty']; ?></td>
                    
					<td width="100" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['cm_value_in'],2); $smry_tot_cm_value_in+=$prod_date_buyer_wise[$companyKey][$buyerKey]['cm_value_in']; ?></td>

                    <td width="100" align="right"><? $swingUnitPrice= $prod_date_buyer_wise[$companyKey][$buyerKey]['sewingUnitprice_fob']; echo number_format($swingUnitPrice,2); $smry_tot_sewing_fob_val_input_qnty+=$swingUnitPrice; ?></td>
                    <td width="100" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_smry'],2); $smry_tot_ex_factory+=$prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_smry']; ?></td>
                    <td width="100" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['ex_cm_value_in'],2); $smry_tot_ex_factory_val+=$prod_date_buyer_wise[$companyKey][$buyerKey]['ex_cm_value_in']; ?></td>
                   <?php /*?> <td width="100" align="right"><? $exfactory_unitPrice= $prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_smry_unitPrice']*$prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_smry']; echo number_format($exfactory_unitPrice,2); $smry_tot_ex_factory_fob_val+=$exfactory_unitPrice; ?></td><?php */?>
                    
                     <td width="100" align="right"><? $exfactory_unitPrice= $prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_smry_fobVal']; echo number_format($exfactory_unitPrice,2); $smry_tot_ex_factory_fob_val+=$exfactory_unitPrice; ?></td>
                    
                    
                    
					<td  align="right"><? $ship_balance=$prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_qnty']-$prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_smry']; echo number_format($ship_balance,2); $smry_tot_ship_balance+=$ship_balance; ?></td>
                   
				</tr>
			<?
			//number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_fob_val_input_qnty'],2).'_'.
		$inc++;
			}
		}
			//echo $all_po_ids.jahid;
		?>
         <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                <th width="150">Total</th>
                <th width="100"><? echo number_format($smry_tot_po_qnty,2); ?></th>
                <th width="100"><? echo number_format($smry_tot_cutting_qnty,2); ?></th>
                <?php /*?><th width="100"><? echo number_format($smry_tot_sewing_input_qnty,2); ?></th><?php */?>
               <?php /*?> <th width="100"><? echo number_format($smry_tot_sewing_qnty,2); ?></th><?php */?>
                <th width="100"><? echo number_format($smry_tot_sewing_qnty,2); ?></th>
                <th width="100"><? echo number_format($tot_sewOut_qnty_inhouse_pcs,2); ?></th>
                <th width="100"><? echo number_format($smry_tot_finish_qnty,2); ?></th>
                <th width="100"><? echo number_format($smry_tot_cm_value_in,2); ?></th>
                <th width="100"><? echo number_format($smry_tot_sewing_fob_val_input_qnty,2); ?></th>
                <th width="100"><? echo number_format($smry_tot_ex_factory,2); ?></th>
                <th width="100"><? echo number_format($smry_tot_ex_factory_val,2); ?></th>
                <th width="100"><? echo number_format($smry_tot_ex_factory_fob_val,2); ?></th>
                <th><? echo number_format($smry_tot_ship_balance,2); ?></th>
             </tr>
        </table>
        </div>
         <br/>
         
         
        <table width="2410px " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
        <p style="float:left"><strong>Details Part</strong></p>
            <thead>
            	<tr>
                    <th width="35" rowspan="4" valign="middle">SL</th>
                    <th width="90" rowspan="4" valign="middle">Production Date</th>
                    <th colspan="14">Shipping Status </th>
                    <th colspan="2">CM Cost (On Sewing Qty)</th>
                    <th colspan="7">Order value and SMV</th>
                    <th colspan="3">Ex-Factory Dtls</th>
                    
                </tr>
            	<tr>
                    <th width="80" rowspan="3" valign="middle">Finish Fabric</th>
                    <th width="80" rowspan="3" valign="middle">Printing</th>
                    <th width="80" rowspan="3" valign="middle">Emb.</th>
                    <th colspan="3">Cutting</th>
                    <th colspan="3">Sewing</th>
                    <th colspan="3">Finish</th>
                    <th width="80" rowspan="3" valign="middle">Carton</th>
                    
                    <th width="100" rowspan="3">In House</th>
                    <th width="100" rowspan="3">Outbound Sub Con</th>
                    
                    <th colspan="3">Order FOB Value (On Sewing Qty)</th>
                    <th colspan="4">SMV (On Sewing Qty)</th>
                    <th rowspan="3" width="100">Ex-Factory Qty</th>
                    <th rowspan="3" width="100" >Ex-Factory Value</th>
                    <th rowspan="3" width="100" >Ex-Factory CM</th>
                    
                </tr>
            	<tr>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Outbound Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Outbound Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Outbound Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="100">In House</th>
                    <th rowspan="2" width="80">Outbound Sub Contact</th>
                    <th rowspan="2" width="100">Total</th>
                    <th rowspan="2" width="100">SAH Available</th>
                    <th colspan="2">SAH Produced</th>
                    <th rowspan="2" width="100">Efficiency</th>
                </tr>
                <tr>
                    <th width="100">In House</th>
                    <th width="100">Outbound Sub Con</th>
                </tr>
            </thead>
        </table>

		 <div style="max-height:420px; overflow-y:scroll; width:2430px" id="scroll_body">
       	 <table cellspacing="0" border="1" class="rpt_table"  width="2410px" rules="all" id="scroll_body" >
         <?
			for($j=0;$j<$datediff;$j++)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				$date_all=add_date(str_replace("'","",$txt_date_from),$j);
				$newdate =change_date_format($date_all);
				$po_id=$prod_date[$newdate]['po_breakdown_id'];
				$produce_qty=$prod_date[$newdate]['sewingout_qnty_inhouse_pcs']/60;
				$effiecy_aff_perc=$tsmvArr[$newdate]['smv']/60 !=0 ? $produce_qty/($tsmvArr[$newdate]['smv']/60)*100 : 0;
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="35" ><? echo $i; ?></td>
					<td width="90"><? echo $newdate; $date=$date_all; //change_date_format($date_all); ?>&nbsp;</td>
                    <!-- <td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"; ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'grey_receive_qnty')"><?  echo number_format($prod_date[$newdate]['kniting_qnty'],2); ?></a><? $total_knit+=$prod_date[$newdate]['kniting_qnty']; //echo $po_id;  ?></td> -->
					<td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'finish_receive_qnty')"> <? echo number_format($prod_date[$newdate]['finishing_qnty'],2); ?></a><? $total_finishing+=$prod_date[$newdate]['finishing_qnty'];?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'printreceived')"><? echo number_format($prod_date[$newdate]['printing_qnty'],2); ?></a> <? $total_print+=$prod_date[$newdate]['printing_qnty']; if($val['printing_qnty']>0) $printing++;  ?></td>
					<td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'emb_qnty')"><? echo number_format($prod_date[$newdate]['emb_qnty'],2); ?></a><? $total_emb+=$prod_date[$newdate]['emb_qnty']; if($prod_date[$newdate]['emb_qnty']>0) $embing++; ?></td>
                    
					<td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'cutting_inhouse')"><? echo number_format($prod_date[$newdate]['cutting_qnty_inhouse'],2); ?></a><? $total_cutting_inhouse+=$prod_date[$newdate]['cutting_qnty_inhouse']; if($prod_date[$newdate]['cutting_qnty_inhouse']>0) $cuting_in++; ?></td>
                   
                   
                    <td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'cutting_subcontract')"><? echo number_format($prod_date[$newdate]['cutting_qnty_outbound'],2); ?></a><? $total_cutting_outbound+=$prod_date[$newdate]['cutting_qnty_outbound']; if($prod_date[$newdate]['cutting_qnty_outbound']>0) $cuting_out++; ?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'cutting')"><? echo number_format($prod_date[$newdate]['cutting_qnty'],2); ?></a><? $total_cutting+=$prod_date[$newdate]['cutting_qnty']; if($prod_date[$newdate]['cutting_qnty']>0) $cuting++; ?></td>
                    
                    
                    <td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'sewingout_inhouse')"><? echo number_format($prod_date[$newdate]['sewingout_qnty_inhouse'],2); ?></a><?  $total_sew_inhouse+=$prod_date[$newdate]['sewingout_qnty_inhouse']; if($prod_date[$newdate]['sewingout_qnty_inhouse']>0) $sewing_in++; ?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'sewingout_subcontract')"><? echo number_format($prod_date[$newdate]['sewingout_qnty_outbound'],2); ?></a><? $total_sew_outbound+=$prod_date[$newdate]['sewingout_qnty_outbound']; if($prod_date[$newdate]['sewingout_qnty_outbound']>0) $sewing_out++; ?></td>
					<td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'sewingout')"><? echo number_format($prod_date[$newdate]['sewing_qnty'],2); ?></a><? $total_sew+=$prod_date[$newdate]['sewing_qnty']; if($prod_date[$newdate]['sewing_qnty']>0) $sewing++; ?></td>
					
                    <td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'finish_inhouse')"><? echo number_format($prod_date[$newdate]['finish_qnty_inhouse'],2); ?></a><? $total_finishg_inhouse+=$prod_date[$newdate]['finish_qnty_inhouse']; if($prod_date[$newdate]['finish_qnty_inhouse']>0) $finish_in++; ?></td>
					<td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'finish_subcontract')"><? echo number_format($prod_date[$newdate]['finish_qnty_outbound'],2); ?></a><? $total_finish_outbound+=$prod_date[$newdate]['finish_qnty_outbound']; if($prod_date[$newdate]['finish_qnty_outbound']>0) $finish_out++; ?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'finish')"><? echo number_format($prod_date[$newdate]['finish_qnty'],2); ?></a><? $total_finish+=$prod_date[$newdate]['finish_qnty']; if($prod_date[$newdate]['finish_qnty']>0) $finish++; ?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'carton')"><? echo number_format($prod_date[$newdate]['carton_qty'],2); ?></a><? $total_carton+=$prod_date[$newdate]['carton_qty']; if($prod_date[$newdate]['carton_qty']>0) $carton++; ?></td>
                    
                    
                     <td width="100" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'cm_value_popup','1')"><? $cm_value_in=$prod_date[$newdate]['cm_value_in']; echo number_format($cm_value_in,2); $total_cm_value_in+=$cm_value_in; if($cm_value_in>0) $cm_val_in++; ?></a>
                    </td>
                    <td  width="100" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'cm_value_popup','3')"><? $cm_value_out=$prod_date[$newdate]['cm_value_out']; echo number_format($cm_value_out,2); $total_cm_value_out+=$cm_value_out; if($cm_value_out>0) $cm_val_out++; ?></a>
                    </td>                
                    
                    
                    
                    
                    <td width="100" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'ord_sew_inhouse')"><? $swing_val_inhouse=$prod_date[$newdate]['sewingout_value_inhouse']; echo number_format($swing_val_inhouse,2);  $total_sewin_order_value+=$swing_val_inhouse; if($swing_val_inhouse>0) $ord_in++; ?></a></td>
                    
                    
                    
                    <td width="80" align="right"><?
					$swing_val_outbound=$prod_date[$newdate]['sewingout_value_outbound']; echo number_format($swing_val_outbound,2);  $total_sewout_order_value+=$swing_val_outbound; if($swing_val_outbound>0) $ord_out++; ?></td>
                    
                    <td width="100" align="right"><? echo number_format(($swing_val_inhouse+$swing_val_outbound),2);  $total_sew_order_value+=($swing_val_inhouse+$swing_val_outbound); if(($swing_val_inhouse+$swing_val_outbound)>0) $ord_tot++; ?></td>
                     <td width="100" align="right"  title="<? echo $swing_val_availble; ?>"><? $swing_val_availble=$tsmvArr[$newdate]['smv']/60; echo number_format($swing_val_availble,2);  $total_swing_val_availble_value+=$swing_val_availble; ?></td>
                     
                    <td width="100" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'smv_sewing_popup',1)"><? $swing_val_produced=$produce_qty; echo number_format($swing_val_produced,2);  $total_sewout_order_value_produced+=$swing_val_produced; ?></a></td>
                    
                    <td width="100" align="right"><? $sewing_out_bound=($prod_date[$newdate]['sewingout_qnty_outbound_pcs'])/60; $total_sewing_out_bound+=$sewing_out_bound; ?><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'smv_sewing_popup',3)"><? echo number_format($sewing_out_bound,2);?></a></td>
                    
                    <td width="100" align="right">
						<? 
						   echo number_format(($effiecy_aff_perc),2);  $total_effiecy_aff_perc+=$effiecy_aff_perc; 
						   if($effiecy_aff_perc>0)
						   {
                             $efficiency_count++;
						   }
						   // echo $efficiency_count."_";
						?>
					</td>

                    <td width="100" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'ex_factory_qty_popup')"><? $ex_factory_qty=$prod_date[$newdate]['ex_factory']; echo number_format($ex_factory_qty,2);  $total_ex_factory_qty+=$ex_factory_qty; if($ex_factory_qty>0) $ex_fac++; ?></a></td>
                    <td width="100"  align="right"><? $ex_factory_value=$prod_date[$newdate]['ex_factory_val']; echo number_format($ex_factory_value,2);  $total_ex_factory_value+=$ex_factory_value; if($ex_factory_value>0) $ex_val++; ?></td>

                    <td width="100" align="right">
                    	<a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'ex_cm_value_popup','1')">
                    		<? 
	                    		$ex_cm_value_in=$prod_date[$newdate]['ex_cm_value_in'];
	                    	 	echo number_format($ex_cm_value_in,2);
	                    	  	$total_ex_cm_value_in+=$ex_cm_value_in;
	                    	   if($ex_cm_value_in>0) $ex_cm_val_in++; 
                    	   ?>                    	   	
                    	</a>
                   
				</tr>
			<?
		$i++;
		}
		?>
	    </table>
	    <table width="2410px " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
	        <tfoot>
	            <tr>
	                <th width="35">&nbsp;</th>
	                <th width="90">Total</th>
	                
	                <th width="80"><? echo number_format($total_finishing,2); ?></th>
	                <th width="80"><? echo number_format($total_print,2); ?></th>
	                <th width="80"><? echo number_format($total_emb,2); ?></th>
	                <th width="80"><? echo number_format($total_cutting_inhouse,2); ?></th>
	                <th width="80"><? echo number_format($total_cutting_outbound,2); ?></th>
	                <th width="80"><? echo number_format($total_cutting,2); ?></th>
	                <th width="80"><? echo number_format($total_sew_inhouse,2); ?></th>
	                <th width="80"><? echo number_format($total_sew_outbound,2); ?></th>
	                <th width="80"><? echo number_format($total_sew,2); ?></th>
	                <th width="80"><? echo number_format($total_finishg_inhouse,2); ?></th>
	                <th width="80"><? echo number_format($total_finish_outbound,2); ?></th>
	                <th width="80"><? echo number_format($total_finish,2); ?></th>
	                <th width="80"><? echo number_format($total_carton,2); ?></th>
	                
	                <th width="100"><? echo number_format($total_cm_value_in,2); ?></th>
	                <th width="100"><? echo number_format($total_cm_value_out,2); ?></th>
	                
	                <th width="100"><? echo number_format($total_sewin_order_value,2); ?></th>
	                <th width="80"><? echo number_format($total_sewout_order_value,2); ?></th>
	                <th width="100"><? echo number_format($total_sew_order_value,2); ?></th>
	                
	                <th width="100"><? echo number_format($total_swing_val_availble_value,2); ?></th>
	                <th width="100"><? echo number_format($total_sewout_order_value_produced,2); ?></th>
	                <th width="100"><?  echo number_format($total_sewing_out_bound,2); ?></th>
	                <th width="100"><?  echo number_format($total_effiecy_aff_perc,2); ?></th>
	                
	                <th width="100"><? echo number_format($total_ex_factory_qty,2); ?></th>
	                <th width="100"><? echo number_format($total_ex_factory_value,2); ?></th>
	                <th width="100"><? echo number_format($total_ex_cm_value_in,2); ?></th>
	               
	            </tr>
	            <tr>
	                <th width="35">&nbsp;</th>
	                <th width="90">Avg.</th>
	                <th width="80"><? 
					//echo number_format($total_finishing/$count_finish,2); 
						$cv=$total_finishing/$count_finish;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					?></th>
	                <th width="80"><? 
						$cv=$total_print/$printing;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					?></th>
	                <th width="80"><? 
					//echo number_format($total_emb/$embing,2); 
						$cv=$total_emb/$embing;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					
					?></th>
	                <th width="80"><? 
					//echo number_format($total_cutting_inhouse/$cuting_in,2); 
						$cv=$total_cutting_inhouse/$cuting_in;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					?></th>
	                <th width="80"><? 
					//echo number_format($total_cutting_outbound/$cuting_out,2); 
						$cv=$total_cutting_outbound/$cuting_out;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					?></th>
	                <th width="80"><? 
					//echo number_format($total_cutting/ $cuting,2); 
						$cv=$total_cutting/$cuting;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					?></th>
	                <th width="80"><? 
					//echo number_format($total_sew_inhouse/$sewing_in,2); 
						$cv=$total_sew_inhouse/$sewing_in;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					?></th>
	                <th width="80"><? 
					//echo number_format($total_sew_outbound/$sewing_out,2); 
						$cv=$total_sew_outbound/$sewing_out;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					?></th>
	                <th width="80"><? 
					//echo number_format($total_sew/$sewing,2); 
						$cv=$total_sew/$sewing;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					?></th>
	                <th width="80"><? 
					//echo number_format($total_finishg_inhouse/$finish_in,2); 
						$cv=$total_finishg_inhouse/$finish_in;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					?></th>
	                <th width="80"><? 
					//echo number_format($total_finish_outbound/$finish_out,2); 
						$cv=$total_finish_outbound/$finish_out;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					?></th>
	                <th width="80"><? 
					//echo number_format($total_finish/$finish,2); 
						$cv=$total_finish/$finish;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					?></th>
	                <th width="80"><? 
					//echo number_format($total_carton/$carton,2); 
						$cv=$total_carton/$carton;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					?></th>
	               
	                <th width="100"><? 
					//echo number_format($total_cm_value_in/$cm_val_in,2); 
						$cv=$total_cm_value_in/$cm_val_in;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					?></th>
	                <th width="100"><? 
					//echo number_format($total_cm_value_out/$cm_val_out,2); 
						$cv=$total_cm_value_out/$cm_val_out;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					?></th>
	               
	                <th width="100"><? 
					//echo number_format($total_sewin_order_value/$ord_in,2); 
						$cv=$total_sewin_order_value/$ord_in;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					?></th>
	                <th width="80"><? 
					//echo number_format($total_sewout_order_value/$ord_out,2); 
						$cv=$total_sewout_order_value/$ord_out;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					?></th>
	                <th width="100"><? //echo number_format($total_sew_order_value/$ord_tot,2);
						$cv=$total_sew_order_value/$ord_tot;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					 ?></th>
	                <th width="100"><? //echo number_format($total_sewin_order_value/$ord_in,2); ?></th>
	                <th width="100"><? //echo number_format($total_sewout_order_value/$ord_out,2); ?></th>
	                <th width="100"><? //echo number_format($total_sew_order_value/$ord_tot,2); ?></th>
	                <th width="100"><? echo number_format($total_effiecy_aff_perc/$efficiency_count,2); ?></th>
	                <th width="100"><? //echo number_format($total_ex_factory_qty/$ex_fac,2); 
						$cv=$total_ex_factory_qty/$ex_fac;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					?></th>
	                <th width="100"><? //echo number_format($total_ex_factory_value/$ex_val,2); 
						$cv=$total_ex_factory_value/$ex_val;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					?></th>
					 <th width="100">
					 	<? 
						$cv=$total_ex_cm_value_in/$ex_cm_val_in;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
						?>
					</th>
	               
	            </tr>
	        </tfoot>
	    </table>
	    </div>
	    <? 
	}
	else if($report_cat==2)//On Finishing Qty
	{
		
		?>
        <table width="2310px" cellpadding="0" cellspacing="0" id="caption" align="center">
            <tr>
            <td align="center" width="100%" colspan="22" class="form_caption"><strong style="font-size:18px"><? if($cbo_company==0){ echo "Working Company Name:". $company_library[$cbo_working_company];} else{ 
			$com_arr=explode(",",str_replace("'","",$cbo_company));
			$comName="";
			foreach($com_arr as $comID)
			{
				$comName.=$company_library[$comID].',';
			}
			//echo chop($comName,",");
			
			echo "Company Name:". chop($comName,",");
			//echo "Company Name:". $company_library[$cbo_company];
			} ?></strong></td>
            </tr> 
            <tr>  
               <td align="center" width="100%" colspan="22" class="form_caption" ><strong style="font-size:14px"><? echo $report_title; ?></strong></td>
            </tr>
            <tr>  
               <td align="center" width="100%" colspan="22" class="form_caption" ><strong style="font-size:14px"> <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
            </tr>  
            <tr>  
               <td align="center" width="100%" colspan="22" class="form_caption" ><strong style="font-size:14px">On Finishing Qty</strong></td>
            </tr>
        </table>
        <table width="2310px " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
            <thead>
            	<tr>
                    <th width="35" rowspan="4" valign="middle">SL</th>
                    <th width="90" rowspan="4" valign="middle">Production Date</th>
                    <th colspan="14">Shipping Status </th>
                    <th colspan="7">Order value and SMV</th>
                    <th colspan="2">Ex-Factory Dtls</th>
                    <th colspan="2">CM Value (On Finishing Qty)</th>
                </tr>
            	<tr>
                   
                    <th width="80" rowspan="3" valign="middle">Finish Fabric</th>
                    <th width="80" rowspan="3" valign="middle">Printing</th>
                    <th width="80" rowspan="3" valign="middle">Emb.</th>
                    <th colspan="3">Cutting</th>
                    <th colspan="3">Sewing</th>
                    <th colspan="3">Finish</th>
                    <th width="80" rowspan="3" valign="middle">Carton</th>
                    <th colspan="3">Order Value (On Finishing Qty)</th>
                    <th colspan="4">SMV (On Finishing Qty)</th>
                    <th rowspan="3" width="100">Ex-Factory Qty</th>
                    <th rowspan="3" width="80">Ex-Factory Value</th>
                    <th width="100" rowspan="3">In House</th>
                    <th rowspan="3">Sub Con</th>
                </tr>
            	<tr>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="100">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="100">Total</th>
                    <th rowspan="2" width="100">SAH Available</th>
                    <th colspan="2">SAH Produced</th>
                    <th rowspan="2" width="100">Efficiency</th>
                </tr>
                <tr>
                    <th width="100">In House</th>
                    <th width="100">Sub Con</th>
                </tr>
            </thead>
        </table>
        <div style="max-height:420px; overflow-y:scroll; width:2330px" id="scroll_body">
        <table cellspacing="0" border="1" class="rpt_table"  width="2310px" rules="all" id="scroll_body" >
      <?	

		$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per"); 
		$tot_cost_arr = return_library_array("select job_no, cm_cost from wo_pre_cost_dtls","job_no","cm_cost"); 
	  	if($cbo_company==0) $cbo_company_cond_1=""; else $cbo_company_cond_1="and company_name in($cbo_company)";
		//$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($cbo_company) and variable_list=25 and status_active=1 and is_deleted=0");
		$smv_source=return_field_value("smv_source","variable_settings_production","variable_list=25 $cbo_company_cond_1 and status_active=1 and is_deleted=0");
		//echo $smv_source;die;
		if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
		$item_smv_array=array();
		//$smv_source=2;
		if($smv_source==3)
		{
			$sql_item="select b.id, a.sam_style, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,3)";
			$resultItem=sql_select($sql_item);
			foreach($resultItem as $itemData)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
			}
		}
		else
		{
			if($cbo_company==0) $cbo_company_cond_2=""; else $cbo_company_cond_2=" and a.company_name in($cbo_company)";
			$sql_item="select b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no $cbo_company_cond_2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,3)";
			$resultItem=sql_select($sql_item);
			foreach($resultItem as $itemData)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]['smv_pcs']=$itemData[csf('smv_pcs')];
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]['smv_pcs_precost']=$itemData[csf('smv_pcs_precost')];
			}
		}
		
		$tpdArr=array(); $tsmvArr=array();
		
		if($cbo_company==0) $cbo_company_cond_3=""; else $cbo_company_cond_3=" and b.company_id in($cbo_company)";
		if($cbo_working_company==0) $cboWorkingComCondManpower=""; else $cboWorkingComCondManpower=" and b.company_id in($cbo_working_company)";
		if(str_replace("'","",$cbo_location)==0){$cboWorkingComCondLocation="";}else{$cboWorkingComCondLocation=" and b.location_id=$cbo_location";}

        $tpd_data_arr=sql_select( "select a.pr_date, sum(a.target_per_hour*a.working_hour) tpd, sum(a.man_power*a.working_hour) tsmv from prod_resource_dtls a, prod_resource_mst b where b.id=a.mst_id $cbo_company_cond_3 $cboWorkingComCondManpower $cboWorkingComCondLocation and a.pr_date between '$date_from' and '$date_to' and a.is_deleted=0 and b.is_deleted=0 group by  a.pr_date");
        foreach($tpd_data_arr as $row)
        {
			$production_date=date("Y-m-d", strtotime($row[csf('pr_date')])); 
           // $tpdArr[$production_date]+=$row[csf('tpd')];
			 $tsmvArr[change_date_format($production_date)]['smv']=$row[csf('tsmv')]*60;
        } //var_dump($tsmvArr[$production_date]['smv']);
		
		$job_array=array(); 
		$job_sql="SELECT a.id, a.unit_price , b.job_no, b.total_set_qnty,b.set_smv from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no   and a.is_deleted=0 and a.status_active in(1,3) and b.is_deleted=0 and b.status_active=1 group by a.id, a.unit_price , b.job_no, b.total_set_qnty,b.set_smv ";
			
		//$job_sql="select a.job_no, a.total_set_qnty,b.id, b.unit_price,c.smv_pcs,c.set_item_ratio from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.company_name in($cbo_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		
		
		
		$job_sql_result=sql_select($job_sql);
		foreach($job_sql_result as $row)
		{
			$job_array[$row[csf("id")]]['unit_price']=$row[csf("unit_price")];
			$job_array[$row[csf("id")]]['job_no']=$row[csf("job_no")];
			$job_array[$row[csf("id")]]['total_set_qnty']=$row[csf("total_set_qnty")];
			$job_array[$row[csf("id")]]['set_smv']=$row[csf("set_smv")];
			//$job_array[$row[csf("id")]][$row[csf("set_item_ratio")]]['smv_pcs']=$row[csf("smv_pcs")];
		}	  
	  
	  
		  $total_knit=0;
		  $total_finishing=0;
		  $total_print=0;
		  $total_emb=0;
		  $total_cutting=0;
		  $total_cutting_inhouse=0;
		  $total_cutting_subcontract=0;
		  $total_sew=0;
		  $total_sew_inhouse=0;
		  $total_sew_subcontract=0;
		  $total_finishg=0;
		  $total_finish_inhouse=0;
		  $total_finish_subcontract=0;
		  $total_carton=0;
		  $comp_cond=str_replace("'","",$cbo_company_id);
		  if($comp_cond==0) $cbo_company_cond_5=""; else $cbo_company_cond_5=" and company_id in($comp_cond)";
		  if($cbo_company==0) $cbo_company_cond_4=""; else $cbo_company_cond_4=" and company_id like '$cbo_company'";
		  if($cbo_working_company==0) $company_working_cond_1=""; else $company_working_cond_1=" and serving_company in($cbo_working_company)";
		  $dtls_sql="SELECT production_date, po_break_down_id as po_breakdown_id, item_number_id,
					sum(CASE WHEN production_type =1 THEN production_quantity END) AS cutting_qnty,
					sum(CASE WHEN production_type =1 and production_source=1 THEN production_quantity END) AS cutting_qnty_inhouse,
					sum(CASE WHEN production_type =1 and production_source=3 THEN production_quantity END) AS cutting_qnty_outbound, 
					
					sum(CASE WHEN production_type =3 and embel_name=1 THEN production_quantity END) AS printing_qnty,
					sum(CASE WHEN production_type =3 and embel_name=1 and production_source=1 THEN production_quantity END) AS printing_qnty_inhouse,
					sum(CASE WHEN production_type =3 and embel_name=1 and production_source=3 THEN production_quantity END) AS printing_qnty_outbound, 
					
					sum(CASE WHEN production_type =3 and embel_name=2 THEN production_quantity END) AS emb_qnty,
					sum(CASE WHEN production_type =3 and embel_name=2 and production_source=1 THEN production_quantity END) AS emb_qnty_inhouse,
					sum(CASE WHEN production_type =3 and embel_name=2 and production_source=3 THEN production_quantity END) AS emb_qnty_outbound,
					 
					sum(CASE WHEN production_type =5 THEN production_quantity END) AS sewing_qnty,
					sum(CASE WHEN production_type =5 and production_source=1 THEN production_quantity END) AS sewingout_qnty_inhouse,
					sum(CASE WHEN production_type =5 and production_source=3 THEN production_quantity END) AS sewingout_qnty_outbound, 
					
					sum(CASE WHEN production_type =8 THEN production_quantity END) AS finish_qnty,
					sum(CASE WHEN production_type =8 and production_source=1 THEN production_quantity END) AS finish_qnty_inhouse, 
					sum(CASE WHEN production_type =8 and production_source=3 THEN production_quantity END) AS finish_qnty_outbound,
					sum(CASE WHEN production_type =8  THEN carton_qty END) AS carton_qty 
					from pro_garments_production_mst a, wo_po_break_down b
					where a.po_break_down_id=b.id $location_con $cbo_company_cond_5 $company_working_cond_1 and production_date between '$date_from' and '$date_to' and a.is_deleted=0 and a.status_active=1 and b.status_active in(1,3) group by production_date, po_break_down_id, item_number_id order by production_date asc";
			
			 //echo $dtls_sql;
			 $dtls_sql_result=sql_select($dtls_sql);
			 $prod_date=array();$po_id=""; $po_sewing_qty=array();
			 foreach($dtls_sql_result as $row)
			 {
				 //if($po_id=="")$po_id=$row[csf("po_breakdown_id")]; else $po_id=$po_id.",".$row[csf("po_breakdown_id")];
				 
				 $production_date=date("Y-m-d", strtotime($row[csf('production_date')])); 
				 $prod_date[change_date_format($row[csf("production_date")])]['po_breakdown_id'].=$row[csf("po_breakdown_id")].",";
				 $prod_date[change_date_format($row[csf("production_date")])]['production_date']=$row[csf("production_date")];
				 $prod_date[change_date_format($row[csf("production_date")])]['printing_qnty']+=$row[csf("printing_qnty")];
				 $prod_date[change_date_format($row[csf("production_date")])]['emb_qnty']+=$row[csf("emb_qnty")];
				 $prod_date[change_date_format($row[csf("production_date")])]['cutting_qnty_inhouse']+=$row[csf("cutting_qnty_inhouse")];
				 $prod_date[change_date_format($row[csf("production_date")])]['cutting_qnty_outbound']+=$row[csf("cutting_qnty_outbound")];
				 $prod_date[change_date_format($row[csf("production_date")])]['cutting_qnty']+=$row[csf("cutting_qnty")];
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_qnty_inhouse']+=$row[csf("sewingout_qnty_inhouse")];
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_qnty_outbound']+=$row[csf("sewingout_qnty_outbound")];
				 $prod_date[change_date_format($row[csf("production_date")])]['sewing_qnty']+=$row[csf("sewing_qnty")];
				 /*
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_qnty_outbound_pcs']+=($row[csf("sewingout_qnty_outbound")]*$job_array[$row[csf("po_breakdown_id")]]['set_smv']);
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_qnty_inhouse_pcs']+=($row[csf("sewingout_qnty_inhouse")]*$job_array[$row[csf("po_breakdown_id")]]['set_smv']);
				 */
				 $prod_date[change_date_format($row[csf("production_date")])]['finish_qnty_out_bound']+=($row[csf("finish_qnty_outbound")]*$item_smv_array[$row[csf("po_breakdown_id")]][$row[csf("item_number_id")]]['smv_pcs']);
				 $prod_date[change_date_format($row[csf("production_date")])]['finish_qnty_in_house']+=($row[csf("finish_qnty_inhouse")]*$item_smv_array[$row[csf("po_breakdown_id")]][$row[csf("item_number_id")]]['smv_pcs']);
				 
				 $item_smv=0;
				if($smv_source==2)
				{
					$item_smv=$item_smv_array[$row[csf("po_breakdown_id")]][$row[csf("item_number_id")]]['smv_pcs_precost'];
				}
				else if($smv_source==3)
				{
					$item_smv=$item_smv_array[$row[csf("po_breakdown_id")]][$row[csf("item_number_id")]];	
				}
				else
				{
					$item_smv=$item_smv_array[$row[csf("po_breakdown_id")]][$row[csf("item_number_id")]]['smv_pcs'];	
				}
				 
				 $prod_date[change_date_format($row[csf("production_date")])]['sewing_qnty_smv']+=$row[csf("sewing_qnty")]*$item_smv;
				 
				 $prod_date[change_date_format($row[csf("production_date")])]['finish_qnty_inhouse']+=$row[csf("finish_qnty_inhouse")];
				 $prod_date[change_date_format($row[csf("production_date")])]['finish_qnty_outbound']+=$row[csf("finish_qnty_outbound")];
				 $prod_date[change_date_format($row[csf("production_date")])]['finish_qnty']+=$row[csf("finish_qnty")];
				 $prod_date[change_date_format($row[csf("production_date")])]['carton_qty']+=$row[csf("carton_qty")];
				 
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_value_inhouse']+=$row[csf("finish_qnty_inhouse")]*($job_array[$row[csf("po_breakdown_id")]]['unit_price']/$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty']);
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_value_outbound']+=$row[csf("finish_qnty_outbound")]*($job_array[$row[csf("po_breakdown_id")]]['unit_price']/$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty']);
				 
				 $cm_value=0; $cm_value_in=0; $cm_value_out=0; $sewing_qty_in=0; $sewing_qty_out=0;
				 //$sewing_qnty=$row[csf("sewing_qnty")];
				 $fin_qty_in=$row[csf("finish_qnty_inhouse")];
				 $fin_qty_out=$row[csf("finish_qnty_outbound")];
				 $job_no=$job_array[$row[csf("po_breakdown_id")]]['job_no'];
				 $total_set_qnty=$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty'];
				 $costing_per=$costing_per_arr[$job_no];
				 
				 if($costing_per==1) $dzn_qnty=12;
				 else if($costing_per==3) $dzn_qnty=12*2;
				 else if($costing_per==4) $dzn_qnty=12*3;
				 else if($costing_per==5) $dzn_qnty=12*4;
				 else $dzn_qnty=1;
							
				 $dzn_qnty=$dzn_qnty*$total_set_qnty;
				 //$cm_value=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewing_qnty;
				 $cm_value_in=($tot_cost_arr[$job_no]/$dzn_qnty)*$fin_qty_in;
				 $cm_value_out=($tot_cost_arr[$job_no]/$dzn_qnty)*$fin_qty_out;
					
				 //$prod_date[change_date_format($row[csf("production_date")])]['cm_value']+=$cm_value;
				 $prod_date[change_date_format($row[csf("production_date")])]['cm_value_in']+=$cm_value_in;
				 $prod_date[change_date_format($row[csf("production_date")])]['cm_value_out']+=$cm_value_out;
			}
			
			if(str_replace("'","",$cbo_location)==0){$location_con_rcv="";}else{$location_con_rcv=" and a.location_id=$cbo_location";}
			
			
				
			if($cbo_company==0) $cbo_company_cond_5=""; else $cbo_company_cond_5=" and a.company_id in(".str_replace("'","", $cbo_company).")";

			if($cbo_working_company==0) $company_working_cond_2=""; else $company_working_cond_2=" and a.knitting_company in($cbo_working_company)";
			$knited_query="select a.receive_date as production_date, sum(b.grey_receive_qnty) as kniting_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id $cbo_company_cond_5  $company_working_cond_2 $location_con_rcv and a.receive_date between '$date_from' and '$date_to' and a.entry_form=2 and a.item_category=13 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.receive_date";
			
			
			$knited_query_result=sql_select($knited_query);
			$count_knit=count($knited_query_result);
			foreach( $knited_query_result as $knit_row)
			{
				$prod_date[change_date_format($knit_row[csf("production_date")])]['kniting_qnty']=$knit_row[csf("kniting_qnty")];
			}
			//var_dump($prod_datek);

			$finish_query="select a.receive_date as production_date, sum(b.receive_qnty) as finishing_qnty from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id $cbo_company_cond_5  $company_working_cond_2 $location_con_rcv and a.receive_date between '$date_from' and '$date_to' and a.entry_form=7 and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.receive_date";
			//echo $finish_query;
			$finish_query_result=sql_select($finish_query);
			$count_finish=count($finish_query_result);
			foreach( $finish_query_result as $finish_row)
			{
				$prod_date[change_date_format($finish_row[csf("production_date")])]['finishing_qnty']=$finish_row[csf("finishing_qnty")];
			}
			//var_dump($prod_date);
			
			
			if($cbo_company==0) $cbo_company_cond_6=""; else $cbo_company_cond_6=" and c.company_name in($cbo_company)";
			$working_comp=str_replace("'", "", $cbo_working_company_id);
			if($working_comp)
			{
				$working_comp_cond=" and d.delivery_company_id in($working_comp) ";
			}
			$location_con2=str_replace("a.location", "d.delivery_location_id", $location_con);
			$exfactory_res = sql_select("SELECT a.ex_factory_date, a.po_break_down_id, 
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c,pro_ex_factory_delivery_mst d
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.delivery_mst_id=d.id and d.status_active=1 and d.is_deleted=0 $cbo_company_cond_6 $location_con2 and a.is_deleted=0 and a.status_active=1 and b.status_active in(1,2,3) and a.ex_factory_date between '$date_from' and '$date_to' $working_comp_cond group by a.ex_factory_date, a.po_break_down_id");

			$_SESSION["location_session"]="";
			if($location_con2)
			{
				$_SESSION["location_session"]=$location_con2;
			}
			foreach($exfactory_res as $ex_row)
			{
				$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_factory']+=$ex_row[csf("ex_factory_qnty")];
				$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_factory_val']+=$ex_row[csf("ex_factory_qnty")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
			}
			
			 ksort($prod_date);
			 $i=1;
			 $printing=0; $embing=0; $cuting_in=0; $cuting_out=0; $cuting=0; $sewing_in=0; $sewing_out=0; $sewing=0; $finish_in=0; $finish_out=0; $finish=0; $carton=0; $ord_in=0; $ord_out=0; $ord_tot=0;
			 
			if($cbo_company==0) $cbo_company_yes=$cbo_working_company.'_'.'workingComp'; else $cbo_company_yes=$cbo_company.'_'.'mainComp';

			for($j=0;$j<$datediff;$j++)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				$date_all=add_date(str_replace("'","",$txt_date_from),$j);
				$newdate =change_date_format($date_all);
				$po_id=$prod_date[$newdate]['po_breakdown_id'];
				$produce_qty=$prod_date[$newdate]['finish_qnty_in_house']/60;
				// $effiecy_aff_perc=$produce_qty/($tsmvArr[$newdate]['smv']/60)*100;
				$effiecy_aff_perc=$tsmvArr[$newdate]['smv']/60 !=0 ? $produce_qty/($tsmvArr[$newdate]['smv']/60)*100 : 0;
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="35" ><? echo $i; ?></td>
					<td width="90"><? echo $newdate; $date=$date_all; //change_date_format($date_all); ?>&nbsp;</td>
					<td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'finish_receive_qnty')"> <? echo number_format($prod_date[$newdate]['finishing_qnty'],2); ?></a><? $total_finishing+=$prod_date[$newdate]['finishing_qnty'];?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'printreceived')"><? echo number_format($prod_date[$newdate]['printing_qnty'],2); ?></a> <? $total_print+=$prod_date[$newdate]['printing_qnty']; if($val['printing_qnty']>0) $printing++;  ?></td>
					<td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'emb_qnty')"><? echo number_format($prod_date[$newdate]['emb_qnty'],2); ?></a><? $total_emb+=$prod_date[$newdate]['emb_qnty']; if($prod_date[$newdate]['emb_qnty']>0) $embing++; ?></td>
                    
					<td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'cutting_inhouse')"><? echo number_format($prod_date[$newdate]['cutting_qnty_inhouse'],2); ?></a><? $total_cutting_inhouse+=$prod_date[$newdate]['cutting_qnty_inhouse']; if($prod_date[$newdate]['cutting_qnty_inhouse']>0) $cuting_in++; ?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'cutting_subcontract')"><? echo number_format($prod_date[$newdate]['cutting_qnty_outbound'],2); ?></a><? $total_cutting_outbound+=$prod_date[$newdate]['cutting_qnty_outbound']; if($prod_date[$newdate]['cutting_qnty_outbound']>0) $cuting_out++; ?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'cutting')"><? echo number_format($prod_date[$newdate]['cutting_qnty'],2); ?></a><? $total_cutting+=$prod_date[$newdate]['cutting_qnty']; if($prod_date[$newdate]['cutting_qnty']>0) $cuting++; ?></td>
                    
                    <td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'sewingout_inhouse')"><? echo number_format($prod_date[$newdate]['sewingout_qnty_inhouse'],2); ?></a><?  $total_sew_inhouse+=$prod_date[$newdate]['sewingout_qnty_inhouse']; if($prod_date[$newdate]['sewingout_qnty_inhouse']>0) $sewing_in++; ?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'sewingout_subcontract')"><? echo number_format($prod_date[$newdate]['sewingout_qnty_outbound'],2); ?></a><? $total_sew_outbound+=$prod_date[$newdate]['sewingout_qnty_outbound']; if($prod_date[$newdate]['sewingout_qnty_outbound']>0) $sewing_out++; ?></td>
					<td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'sewingout')"><? echo number_format($prod_date[$newdate]['sewing_qnty'],2); ?></a><? $total_sew+=$prod_date[$newdate]['sewing_qnty']; if($prod_date[$newdate]['sewing_qnty']>0) $sewing++; ?></td>
					
                    <td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'finish_inhouse')"><? echo number_format($prod_date[$newdate]['finish_qnty_inhouse'],2); ?></a><? $total_finishg_inhouse+=$prod_date[$newdate]['finish_qnty_inhouse']; if($prod_date[$newdate]['finish_qnty_inhouse']>0) $finish_in++; ?></td>
					<td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'finish_subcontract')"><? echo number_format($prod_date[$newdate]['finish_qnty_outbound'],2); ?></a><? $total_finish_outbound+=$prod_date[$newdate]['finish_qnty_outbound']; if($prod_date[$newdate]['finish_qnty_outbound']>0) $finish_out++; ?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'finish')"><? echo number_format($prod_date[$newdate]['finish_qnty'],2); ?></a><? $total_finish+=$prod_date[$newdate]['finish_qnty']; if($prod_date[$newdate]['finish_qnty']>0) $finish++; ?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'carton')"><? echo number_format($prod_date[$newdate]['carton_qty'],2); ?></a><? $total_carton+=$prod_date[$newdate]['carton_qty']; if($prod_date[$newdate]['carton_qty']>0) $carton++; ?></td>
                    
                    <td width="100" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'finish_inhouse')"><? $swing_val_inhouse=$prod_date[$newdate]['sewingout_value_inhouse']; echo number_format($swing_val_inhouse,2);  $total_sewin_order_value+=$swing_val_inhouse; if($swing_val_inhouse>0) $ord_in++; ?></a></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'finish_subcontract')"><?
					$swing_val_outbound=$prod_date[$newdate]['sewingout_value_outbound']; echo number_format($swing_val_outbound,2);  $total_sewout_order_value+=$swing_val_outbound; if($swing_val_outbound>0) $ord_out++; ?></a></td>
                    
                    <td width="100" align="right"><? echo number_format(($swing_val_inhouse+$swing_val_outbound),2);  $total_sew_order_value+=($swing_val_inhouse+$swing_val_outbound); if(($swing_val_inhouse+$swing_val_outbound)>0) $ord_tot++; ?></td>
                     <td width="100" align="right"><? $swing_val_availble=$tsmvArr[$newdate]['smv']/60; echo number_format($swing_val_availble,2);  $total_swing_val_availble_value+=$swing_val_availble; ?></td>
                     
                    <td width="100" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'smv_finishing_popup',1)"><? $finishing_val_produced=$produce_qty; echo number_format($finishing_val_produced,2);  $total_sewout_order_value_produced+=$finishing_val_produced; ?></a></td>
                    
                    <td width="100" align="right"><? $finishinging_out_bound=($prod_date[$newdate]['finish_qnty_out_bound'])/60; $total_sewing_out_bound+=$finishinging_out_bound; ?><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'smv_finishing_popup',3)"><? echo number_format($finishinging_out_bound,2);?></a></td>
                    
                    <td width="100" align="right"><? echo number_format(($effiecy_aff_perc),2);  $total_effiecy_aff_perc+=$effiecy_aff_perc; ?></td>

                    <td width="100" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'ex_factory_qty_popup')"><? $ex_factory_qty=$prod_date[$newdate]['ex_factory']; echo number_format($ex_factory_qty,2);  $total_ex_factory_qty+=$ex_factory_qty; if($ex_factory_qty>0) $ex_fac++; ?></a></td>
                    <td width="80" align="right"><? $ex_factory_value=$prod_date[$newdate]['ex_factory_val']; echo number_format($ex_factory_value,2);  $total_ex_factory_value+=$ex_factory_value; if($ex_factory_value>0) $ex_val++; ?></td>
                    <td width="100" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'cm_value_finish_popup',1)"><? $cm_value_in=$prod_date[$newdate]['cm_value_in']; echo number_format($cm_value_in,2); $total_cm_value_in+=$cm_value_in; if($cm_value_in>0) $cm_val_in++; ?></a>
                    </td>
                    <td align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company_yes."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'cm_value_finish_popup',3)"><? $cm_value_out=$prod_date[$newdate]['cm_value_out']; echo number_format($cm_value_out,2); $total_cm_value_out+=$cm_value_out; if($cm_value_out>0) $cm_val_out++; ?></a>
                    </td>
				</tr>
			<?
		$i++;
		}
		?>
    	</table>
    	<table width="2310px " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
	        <tfoot>
	            <tr>
	                <th width="35">&nbsp;</th>
	                <th width="90">Total</th>
	                <th width="80"><? echo number_format($total_finishing,2); ?></th>
	                <th width="80"><? echo number_format($total_print,2); ?></th>
	                <th width="80"><? echo number_format($total_emb,2); ?></th>
	                <th width="80"><? echo number_format($total_cutting_inhouse,2); ?></th>
	                <th width="80"><? echo number_format($total_cutting_outbound,2); ?></th>
	                <th width="80"><? echo number_format($total_cutting,2); ?></th>
	                <th width="80"><? echo number_format($total_sew_inhouse,2); ?></th>
	                <th width="80"><? echo number_format($total_sew_outbound,2); ?></th>
	                <th width="80"><? echo number_format($total_sew,2); ?></th>
	                <th width="80"><? echo number_format($total_finishg_inhouse,2); ?></th>
	                <th width="80"><? echo number_format($total_finish_outbound,2); ?></th>
	                <th width="80"><? echo number_format($total_finish,2); ?></th>
	                <th width="80"><? echo number_format($total_carton,2); ?></th>
	                <th width="100"><? echo number_format($total_sewin_order_value,2); ?></th>
	                <th width="80"><? echo number_format($total_sewout_order_value,2); ?></th>
	                <th width="100"><? echo number_format($total_sew_order_value,2); ?></th>
	                
	                <th width="100"><? echo number_format($total_swing_val_availble_value,2); ?></th>
	                <th width="100"><? echo number_format($total_sewout_order_value_produced,2); ?></th>
	                <th width="100"><?  echo number_format($total_sewing_out_bound,2); ?></th>
	                <th width="100"><?  //echo number_format($total_effiecy_aff_perc,2); ?></th>
	                
	                <th width="100"><? echo number_format($total_ex_factory_qty,2); ?></th>
	                <th width="80"><? echo number_format($total_ex_factory_value,2); ?></th>
	                <th width="100"><? echo number_format($total_cm_value_in,2); ?></th>
	                <th><? echo number_format($total_cm_value_out,2); ?></th>
	            </tr>
	            <tr>
	                <th width="35" >&nbsp;</th>
	                <th width="90" >Avg.</th>
	                
	                <th width="80" ><? //echo number_format($total_finishing/$count_finish,2); 
						$cv=$total_finishing/$count_finish;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					?></th>
	                <th width="80" ><? //echo number_format($total_print/$printing,2); 
						$cv=$total_print/$printing;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					?></th>
	                <th width="80" ><? //echo number_format($total_emb/$embing,2); 
						$cv=$total_emb/$embing;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					?></th>
	                <th width="80"><? 
					//echo number_format($total_cutting_inhouse/$cuting_in,2);
						$cv=$total_cutting_inhouse/$cuting_in;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					 ?></th>
	                <th width="80"><? //echo number_format($total_cutting_outbound/$cuting_out,2); 
						$cv=$total_cutting_outbound/$cuting_out;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					?></th>
	                <th width="80"><? //echo number_format($total_cutting/ $cuting,2);
						$cv=$total_cutting/$cuting;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					 ?></th>
	                <th width="80"><? //echo number_format($total_sew_inhouse/$sewing_in,2);
						$cv=$total_sew_inhouse/$sewing_in;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					 ?></th>
	                <th width="80"><? //echo number_format($total_sew_outbound/$sewing_out,2);
						$cv=$total_sew_outbound/$sewing_out;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					 ?></th>
	                <th width="80"><? //echo number_format($total_sew/$sewing,2);
						$cv=$total_sew/$sewing;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					 ?></th>
	                <th width="80"><? //echo number_format($total_finishg_inhouse/$finish_in,2); 
						$cv=$total_finishg_inhouse/$finish_in;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					?></th>
	                <th width="80"><? //echo number_format($total_finish_outbound/$finish_out,2);
						$cv=$total_finish_outbound/$finish_out;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					?></th>
	                <th width="80"><? //echo number_format($total_finish/$finish,2);
						$cv=$total_finish/$finish;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					 ?></th>
	                <th width="80"><? //echo number_format($total_carton/$carton,2); 
						$cv=$total_carton/$carton;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					?></th>
	                <th width="100"><? //echo number_format($total_sewin_order_value/$ord_in,2); 
						$cv=$total_sewin_order_value/$ord_in;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					?></th>
	                <th width="80"><? //echo number_format($total_sewout_order_value/$ord_out,2); 
						$cv=$total_sewout_order_value/$ord_out;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					?></th>
	                <th width="100"><? //echo number_format($total_sew_order_value/$ord_tot,2);
						$cv=$total_sew_order_value/$ord_tot;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					 ?></th>
	                <th width="100"><? //echo number_format($total_sewin_order_value/$ord_in,2); ?></th>
	                <th width="100"><? //echo number_format($total_sewout_order_value/$ord_out,2); ?></th>
	                <th width="100"><? //echo number_format($total_sew_order_value/$ord_tot,2); ?></th>
	                <th width="100"><? //echo number_format($total_sew_order_value/$ord_tot,2); ?></th>
	                <th width="100"><? //echo number_format($total_ex_factory_qty/$ex_fac,2);
						$cv=$total_ex_factory_qty/$ex_fac;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					 ?></th>
	                <th width="80"><? //echo number_format($total_ex_factory_value/$ex_val,2);
						$cv=$total_ex_factory_value/$ex_val;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					 ?></th>
	                <th width="100"><? //echo number_format($total_cm_value_in/$cm_val_in,2); 
						$cv=$total_cm_value_in/$cm_val_in;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					?></th>
	                <th><? //echo number_format($total_cm_value_out/$cm_val_out,2);
						$cv=$total_cm_value_out/$cm_val_out;
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					 ?></th>
	            </tr>
	        </tfoot>
	    </table>
	    </div>
	    <? 
	}
	
	foreach (glob("$user_id*.xls") as $filename) 
	{
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

if($action=="grey_receive_qnty")
{
	extract($_REQUEST);
	$comp_expl=explode('_',$company_id);
	$comp_ids_arr=explode(',',$comp_expl[0]);
	$comp_ids="";
	foreach($comp_ids_arr as $comp)
	{
		$comp_ids.=$comp.',';
	}
	$comp_id=chop($comp_ids,',');
	 
	$comp_type=$comp_expl[1];
	echo load_html_head_contents("Production Report", "../../../", 1, 1,$unicode,'','');	
	$companys=$_SESSION["comp"];
	$work_comp=$_SESSION["work_comp"];


	if($companys)
	{
		$companys_cond=" and a.company_id in ($companys) ";
	}
	if($work_comp)
	{
		$serving_company_cond=" and a.knitting_company in($work_comp) ";
	}

	$order_array=array();
	$po_sql="SELECT b.id, a.buyer_name, a.job_no_prefix_num, a.style_ref_no, b.po_number,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active in(1,3) and a.is_deleted=0 ";
	$po_sql_result=sql_select($po_sql);
	foreach ($po_sql_result as $row)
	{
		$order_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
		$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		$order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$order_array[$row[csf('id')]]['grouping']=$row[csf('grouping')];
	}

	$po_sql_sub="SELECT b.id,  a.job_no_prefix_num, b.cust_style_ref as style_ref_no, b.order_no as po_number,a.party_id as buyer_name  from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 ";
	$po_sql_result_sub=sql_select($po_sql_sub);
	$order_array_subcon=array();
	foreach ($po_sql_result as $row)
	{
		$order_array_subcon[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
		$order_array_subcon[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		$order_array_subcon[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$order_array_subcon[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
	}

	
	if(str_replace("'","",$location_id)==0){$location_con_rcv="";}else{$location_con_rcv=" and a.location_id=$location_id";}
	
	$total_grey_receive_qnty=0;
	if($db_type==0)
	{
		$date2=change_date_format($date,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{	$date2=change_date_format($date,'','',1);
	}
	//if($comp_type=="workingComp") $cbo_company_cond_inv="and a.knitting_company='$comp_id'"; else $cbo_company_cond_inv=" and a.company_id in($comp_id)";

  
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");


	$sql_sample_sam="
		SELECT
			a.buyer_id, a.booking_no,
			sum(case when a.booking_without_order=1 and b.machine_no_id>0 $floor_id then b.grey_receive_qnty end ) as sample_qty,
			sum(case when a.booking_without_order=1 and b.machine_no_id>0 $floor_id then b.no_of_roll end ) as no_of_roll
		FROM
			inv_receive_master a,
			pro_grey_prod_entry_dtls b
		WHERE
			a.id = b.mst_id 
			and a.entry_form = 2 
			and a.item_category = 13 
			and a.receive_basis != 4 
			".$cbo_company_cond_inv." 
			".$location_con_rcv." 
			and a.receive_date = '".$date2."' 
			and a.status_active = 1 
			and a.is_deleted = 0 
			and b.status_active = 1 
			and b.is_deleted = 0 
			and a.booking_without_order IN(1) 
			".$companys_cond." 
			".$serving_company_cond." 
		GROUP BY
			a.buyer_id,
			a.booking_no
	";
	//echo $sql_sample_sam;
	$sql_sample_samary=sql_select( $sql_sample_sam);
	$subcon_buyer_samary=array(); 
	foreach($sql_sample_samary as $inf)
	{
		$booking_no=explode("-",$inf[csf('booking_no')]);
		$without_booking_no=$booking_no[1];
		if($without_booking_no == 'SMN')
		{
			$knit_buyer_samary[4][$inf[csf('booking_no')]][$inf[csf('buyer_id')]]['qnty']+= $inf[csf('sample_qty')];
			$knit_buyer_samary[4][$inf[csf('booking_no')]][$inf[csf('buyer_id')]]['no_of_roll']+= $inf[csf('no_of_roll')];
		}
		
	}
	unset($sql_sample_samary);
	
	$sql_sample_sam_with="
		SELECT
			a.buyer_id, a.booking_no,e.id as po_id,
			sum(case when a.booking_without_order=0 and b.machine_no_id>0 $floor_id  then b.grey_receive_qnty else 0 end ) as with_ord_sample_qty,
			sum(case when a.booking_without_order=0 and b.machine_no_id>0 $floor_id then b.no_of_roll end ) as no_of_roll
		FROM
			inv_receive_master a,
			pro_grey_prod_entry_dtls b,
			order_wise_pro_details c,
			lib_machine_name d,
			wo_po_break_down e,
			wo_po_details_master f
		WHERE
			c.po_breakdown_id=e.id 
			and a.entry_form=2 
			and a.item_category=13 
			and a.id=b.mst_id 
			and b.id=c.dtls_id 
			and e.job_no_mst=f.job_no 
			and c.entry_form=2 
			and c.trans_type=1 
			and a.knitting_source=1 
			and a.receive_basis!=4 
			".$cbo_company_cond_inv." 
			".$location_con_rcv." 
			and a.receive_date='".$date2."' 
			and a.status_active=1 
			and e.status_active in(1,3) 
			and a.is_deleted=0 
			and b.status_active=1 
			and b.is_deleted=0 
			and a.booking_without_order =0
			".$companys_cond." 
			".$serving_company_cond."
		GROUP BY
			a.buyer_id,
			a.booking_no,
			e.id
	";
	//echo $sql_sample_sam_with;
	$sql_sample_samary_with=sql_select( $sql_sample_sam_with);
	foreach($sql_sample_samary_with as $row)
	{
		$booking_no=explode("-",$row[csf('booking_no')]);
		$without_booking_no=$booking_no[1];
		if($without_booking_no=='SM')
		{
			$knit_buyer_samary[3][$row[csf('po_id')]][$row[csf('buyer_id')]]['qnty']+= $row[csf('with_ord_sample_qty')];
			$knit_buyer_samary[3][$row[csf('po_id')]][$row[csf('buyer_id')]]['no_of_roll']+= $row[csf('no_of_roll')];
		}
		
	}
	unset($sql_sample_samary_with);

	$sql_service_samary=sql_select("
		SELECT
			a.buyer_id,
			sum(b.grey_receive_qnty) as service_qty
		FROM
			inv_receive_master a,
			pro_grey_prod_entry_dtls b
		WHERE
			a.entry_form=22 
			and a.receive_basis=11 
			and a.item_category=13 
			and a.id=b.mst_id 
			".$cbo_company_cond_inv." 
			".$location_con_rcv." 
			and a.receive_date='".$date2."' 
			and a.status_active=1 
			and a.is_deleted=0 
			and b.status_active=1 
			and b.is_deleted=0 
			".$companys_cond." 
			".$serving_company_cond." 
		GROUP BY
			a.buyer_id
	");
	$service_buyer_data=array();
	foreach($sql_service_samary as $row)
	{
		$service_buyer_data[$row[csf("buyer_id")]]=$row[csf("service_qty")];
	}
	unset($sql_service_samary);




	$sql_qty="
		SELECT
			a.buyer_id,a.booking_no,c.po_breakdown_id,
   			sum(case when a.knitting_source=1 and b.machine_no_id>0 then c.quantity end ) as qtyinhouse,
   			sum(case when a.knitting_source=1 and b.machine_no_id>0 then b.no_of_roll end ) as noofroll_inhouse,
   			sum(case when a.knitting_source=3 and b.machine_no_id>0 then b.no_of_roll end ) as noofroll_outhouse,
   			sum(case when a.knitting_source=3 then c.quantity end ) as qtyoutbound 
   		FROM
			inv_receive_master a,
			pro_grey_prod_entry_dtls b,
			order_wise_pro_details c
		WHERE
			a.item_category=13 
			and a.id=b.mst_id 
			and b.id=c.dtls_id
			and c.entry_form=2 
			and c.trans_type=1 
			and a.receive_basis!=4  
			".$cbo_company_cond_inv." 
			".$location_con_rcv."
			and a.receive_date='".$date2."' 
			and a.status_active=1 
			and a.is_deleted=0 
			and b.status_active=1 
			and b.is_deleted=0 
			and c.status_active=1 
			and c.is_deleted=0 
			".$companys_cond." 
			".$serving_company_cond."
		GROUP BY
			a.buyer_id,
			a.booking_no,
			c.po_breakdown_id
	";
   //echo $sql_qty;
	$k=1;
	$sql_result=sql_select( $sql_qty);
	foreach($sql_result as $row)
	{
		$booking_no=explode("-",$row[csf('booking_no')]);
		$without_booking_no=$booking_no[1];
		if($without_booking_no!='SMN' || $without_booking_no!='SM')
		{
			if($row[csf('qtyinhouse')])
			{
				$knit_buyer_samary[1][$row[csf('po_breakdown_id')]][$row[csf('buyer_id')]]['qnty']+= $row[csf('qtyinhouse')];
			}
			if($row[csf('qtyoutbound')])
			{
				$knit_buyer_samary[2][$row[csf('po_breakdown_id')]][$row[csf('buyer_id')]]['qnty']+= $row[csf('qtyoutbound')];
			}
			
			if($row[csf('noofroll_inhouse')])
			{
				$knit_buyer_samary[1][$row[csf('po_breakdown_id')]][$row[csf('buyer_id')]]['no_of_roll']+= $row[csf('noofroll_inhouse')];
			}
			if($row[csf('noofroll_outhouse')])
			{
				$knit_buyer_samary[2][$row[csf('po_breakdown_id')]][$row[csf('buyer_id')]]['no_of_roll']+= $row[csf('noofroll_outhouse')];
			}
		}
	}

	 $sql_qty_sales="
	 	SELECT
			a.buyer_id,a.booking_no,c.po_breakdown_id,
			sum(case when a.knitting_source=1 and b.machine_no_id>0 then c.quantity end ) as qtyinhouse,
			sum(case when a.knitting_source=1 and b.machine_no_id>0 then b.no_of_roll end ) as noofroll_inhouse,
			sum(case when a.knitting_source=3 and b.machine_no_id>0 then b.no_of_roll end ) as noofroll_outhouse,
			sum(case when a.knitting_source=3 then c.quantity end ) as qtyoutbound
	 	FROM
			inv_receive_master a,
			pro_grey_prod_entry_dtls b,
			order_wise_pro_details c
	 	WHERE
			a.item_category=13 
			and a.id=b.mst_id 
			and b.id=c.dtls_id
			and c.entry_form=2 
			and c.trans_type=1 
			and a.receive_basis=4 
			".$cbo_company_cond_inv." 
			".$location_con_rcv."
			".$companys_cond." 
			".$serving_company_cond." 
			and a.receive_date='".$date2."' 
			and a.status_active=1 
			and a.is_deleted=0 
			and b.status_active=1 
			and b.is_deleted=0 
			and c.status_active=1 
			and c.is_deleted=0
		GROUP BY
			a.buyer_id,a.booking_no,
			c.po_breakdown_id
	";
	$k=1;
	$sql_result_sales=sql_select( $sql_qty_sales);
	foreach($sql_result_sales as $rows)
	{
		
		$knit_buyer_samary[6][$rows[csf('booking_no')]][$rows[csf('buyer_id')]]['qnty']+= $rows[csf('qtyinhouse')]+$rows[csf('qtyoutbound')];		 
		$knit_buyer_samary[6][$rows[csf('booking_no')]][$rows[csf('buyer_id')]]['no_of_roll']+= $rows[csf('noofroll_inhouse')];		 
	}

	if(!$companys && $work_comp)
	{
		$knited_query_subcon="
			SELECT
				a.party_id,b.order_id,
				sum(b.product_qnty) as subcon_qnty,
				sum(b.no_of_roll) as no_of_roll 
			FROM
				subcon_production_mst a,
				subcon_production_dtls b
			WHERE
				a.id=b.mst_id 
				".$cbo_company_cond_inv." 
				".$location_con_rcv." 
				and a.product_date = '".$date2."'
				and a.is_deleted=0 
				and a.status_active=1 
			GROUP BY
				a.party_id,b.order_id
		";

		$knited_query_result_sc=sql_select($knited_query_subcon);
		foreach( $knited_query_result_sc as $knit_row)
		{
			$knit_buyer_samary[5][$knit_row[csf("order_id")]][$knit_row[csf("party_id")]]['qnty']+=$knit_row[csf("subcon_qnty")];
			$knit_buyer_samary[5][$knit_row[csf("order_id")]][$knit_row[csf("party_id")]]['no_of_roll']+=$knit_row[csf("no_of_roll")];
		}
	}
	
	//print_r($knit_buyer_samary);
	$total_production_sammary=array(1=>'Inhouse (Self Order)',2=>'Outbound-Subcon',3=>'Sample With Order',4=>'Sample Without Order',5=>'Inbound Subcontract',6=>'Fabric Sales Order');
	$gr_qnty=0;
	$grandTotal = array();
	foreach($knit_buyer_samary as $type_id=>$po_data)
	{
		$i=1;
		?>
	  	<table style="margin:0px auto;" cellspacing="0" cellpadding="0" border="1" rules="all" width="680px" class="rpt_table">
			<br>
			<caption><strong><? echo $total_production_sammary[$type_id];?></strong></caption>
			<thead>
				<tr>					 
					<th colspan="7"><b>Knitting <? echo change_date_format($date);  ?></b></th>            
				</tr>
				<tr>
					<th width="40">SL</th>
					<?
					if($type_id != 4 && $type_id != 6)
					{
						?>
						<th width="50">Job</th>
						<?
						if($type_id != 5)
						{
							?>
							<th width="100">Int. Ref. No</th> 
							<?
						}
						else
						{
							?>
							<th width="100">Po No</th> 
							<?
						}
					}
					else
					{
						if($type_id==4)
						{
							?>
							<th width="150"  colspan="2">Booking No</th>
							<?
						}
						else
						{
							?>
							<th width="150" colspan="2">FSO No</th>
							<?
						}
					}
					?>
					<th width="120">Buyer</th> 
					<th width="100">Style</th> 
					<th width="120">Knitting Qty.</th> 
					<th>No of Roll</th> 
				</tr>
			</thead>
			<tbody>
				<?
				$subTotal = array();
				foreach($po_data as $po_id=>$buyer_data)
				{
					foreach($buyer_data as $buyer_id=>$rows)
					{
						?>
						<tr>
                            <td align="center"><? echo $i;$i++;?></td>
                            <?
                            if($type_id!=4 && $type_id!=6)
                            {
                                ?>
                                <td align="center">
                                <? 
                                if($type_id==5)
                                {
                                    echo $order_array_subcon[$po_id]['job'];
                                }
                                else
                                {
                                    echo $order_array[$po_id]['job'];
                                }
                                ?>
                                </td>
                                <td align="center">
                                <? 
                                if($type_id==5)
                                {
                                    echo $order_array_subcon[$po_id]['po_number'];
                                }
                                else
                                {
                                    echo $order_array[$po_id]['grouping'];
                                }
                                ?>
                                </td> 
                                <?
                            }
                            else
                            {
                                if($type_id==4)
                                {
                                    ?>
                                    <td align="center" colspan="2"><? echo $po_id; ?></td>
                                    <?
                                }
                                else
                                {
                                    ?>
                                    <td align="center" colspan="2"><? echo $po_id; ?></td>  
                                    <?
                                }
                            }
                            ?>
                            <td align="center">
                            <?
                            if($type_id==5)
                            {
                                echo $buyerArr[$order_array_subcon[$po_id]['buyer_name']];
                            }
                            else
                            {
                                echo $buyerArr[$order_array[$po_id]['buyer_name']];
                            }
                            ?>
                            </td> 
                            <td align="center">
                            <?
                            if($type_id==5)
                            {
                                echo $order_array_subcon[$po_id]['style_ref_no'];
                            }
                            else
                            {
                                echo $order_array[$po_id]['style_ref_no'];
                            }
                            ?>
                            </td> 
                            <td align="center"><? echo $rows['qnty']; ?></td> 
                            <td align="center"><? echo $rows['no_of_roll'];?></td> 
						</tr>
						<?
						$subTotal['qnty'] += $rows['qnty'];
						$subTotal['no_of_roll'] += $rows['no_of_roll'];
						$grandTotal['qnty'] += $rows['qnty'];
						$grandTotal['no_of_roll'] += $rows['no_of_roll'];
					}
				}
				?>
				<tr style="background-color: #E4E4E4;">
					<td colspan="5" align="right"><b>Total</b></td>
					<td align="center"><b><? echo $subTotal['qnty']; ?></b></td>
					<td align="center"><b><? echo $subTotal['no_of_roll']; ?></b></td>
				</tr>
				<?php
			}
			if(	count($knit_buyer_samary) >1)
			{
			?>
			<tr style="background-color: #E4E4E4;">
				<td colspan="5" align="right"><b>Grand Total</b></td>
				<td align="center"><b><? echo $grandTotal['qnty']; ?></b></td>
                <td align="center"><b><? echo $grandTotal['no_of_roll']; ?></b></td>
			</tr>
            <?php
			}
			?>
		</tbody>
	</table>
	<?
    exit();	
}

if($action=="dyeing_receive_qnty")
{
	extract($_REQUEST);
	$comp_expl=explode('_',$company_id);
	$comp_ids_arr=explode(',',$comp_expl[0]);
	$comp_ids="";
	foreach($comp_ids_arr as $comp)
	{
		$comp_ids.=$comp.',';
	}
	$comp_id=chop($comp_ids,',');
	 
	$comp_type=$comp_expl[1];
	echo load_html_head_contents("Production Report", "../../../", 1, 1,$unicode,'','');
	$companys=$_SESSION["comp"];
	$work_comp=$_SESSION["work_comp"];


	if($companys)
	{
		$companys_cond=" and a.company_id in ($companys) ";
	}
	if($work_comp)
	{
		$serving_company_cond=" and a.service_company in($work_comp) ";
	}

	
	
 	?>
    
    <?
	 /*
		$order_array=array();
		$po_sql="SELECT b.id, a.buyer_name, a.job_no_prefix_num, a.style_ref_no, b.po_number,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 ";
		$po_sql_result=sql_select($po_sql);
		foreach ($po_sql_result as $row)
		{
			$order_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
			$order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
			$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$order_array[$row[csf('id')]]['grouping']=$row[csf('grouping')];
		}*/


		$booking_array=array();
		$po_sql="SELECT b.job_no_prefix_num,a.booking_no,a.buyer_id,b.style_ref_no from wo_booking_mst a, wo_po_details_master b where a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		union 
		select null as job_no,booking_no,buyer_id,null as style_ref_no from wo_non_ord_samp_booking_mst where status_active=1 and is_deleted=0   ";
		$po_sql_result=sql_select($po_sql);
		foreach ($po_sql_result as $row)
		{
			$booking_array[$row[csf('booking_no')]]['job']=$row[csf('job_no_prefix_num')];
			//$booking_array[$row[csf('booking_no')]]['po_number']=$row[csf('po_number')];
			$booking_array[$row[csf('booking_no')]]['buyer_name']=$row[csf('buyer_id')];
			$booking_array[$row[csf('booking_no')]]['style_ref_no']=$row[csf('style_ref_no')];
			//$booking_array[$row[csf('booking_no')]]['grouping']=$row[csf('grouping')];
		}



		$po_sql_sub="SELECT b.id,  a.job_no_prefix_num, b.cust_style_ref as style_ref_no, b.order_no as po_number,a.party_id as buyer_name  from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 ";
		$po_sql_result_sub=sql_select($po_sql_sub);
		$order_array_subcon=array();
		foreach ($po_sql_result as $row)
		{
			$order_array_subcon[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$order_array_subcon[$row[csf('id')]]['po_number']=$row[csf('po_number')];
			$order_array_subcon[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
			$order_array_subcon[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
 		}

		
		if(str_replace("'","",$location_id)==0){$location_con_rcv="";}else{$location_con_rcv=" and a.location_id=$location_id";}
		
        $total_grey_receive_qnty=0;
		if($db_type==0)
		{
			$date2=change_date_format($date,'yyyy-mm-dd');
		}
		else if($db_type==2)
		{	$date2=change_date_format($date,'','',1);
		}
		if($comp_type=="workingComp") $cbo_company_cond_inv="and a.service_company='$comp_id'"; else $cbo_company_cond_inv=" and a.company_id in($comp_id)";

	  
		$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
		$cbo_company_cond_inv2=str_replace("a.service_company", "a.knitting_company", $cbo_company_cond_inv2);

		$dyeing_query_subcon="SELECT a.production_date as production_date, 0 as dyeing_qnty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id $cbo_company_cond_inv2  $location_con_rcv  and a.production_date = '$date_2'    and a.is_deleted=0 and a.status_active=1 and a.entry_form=38 group by a.production_date";


			 $dyeing_query_result_sc=sql_select($dyeing_query_subcon);
 			 foreach( $dyeing_query_result_sc as $dyeing_row)
			 {
			 	$knit_buyer_samary[5][$value[csf('sales_order_no')]][$value[csf('batch_no')]]['qnty']+=$dyeing_row[csf("dyeing_qnty")];
			 }




 	   $dyeing_query="SELECT a.service_source, c.booking_no ,c.sales_order_no,c.batch_no,c.is_sales,b.production_qty as qnty from pro_fab_subprocess a, pro_fab_subprocess_dtls b,pro_batch_create_mst c where a.id=b.mst_id and a.batch_id=c.id  $cbo_company_cond_inv $location_con_rcv $companys_cond $serving_company_cond   and a.process_end_date = '$date2' and a.entry_form=35  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.load_unload_id in(2)";
 	    foreach(sql_select($dyeing_query) as $key=>$value)
 	    {
 	    	$is_sales=$value[csf("is_sales")];
 	    	$booking_no=explode("-",$value[csf("is_sales")]);
 	    	if($is_sales)
 	    	{
 	    		$knit_buyer_samary[6][$value[csf('sales_order_no')]][$value[csf('batch_no')]]['qnty']+= $value[csf('qnty')];	

 	    	}
 	    	else
 	    	{
 	    		$without_booking_no=$booking_no[1];
				if($without_booking_no=='SMN')
				{
	 				$knit_buyer_samary[4][$value[csf('booking_no')]][$value[csf('batch_no')]]['qnty']+= $value[csf('qnty')];
				}

				else if( $without_booking_no=='SM')
				{
	 				$knit_buyer_samary[3][$value[csf('booking_no')]][$value[csf('batch_no')]]['qnty']+= $value[csf('qnty')];
				}
				else
				{
					if($value[csf('service_source')]==1)
					{
						$knit_buyer_samary[1][$value[csf('booking_no')]][$value[csf('batch_no')]]['qnty']+= $value[csf('qnty')];
					}
					else
					{
						$knit_buyer_samary[3][$value[csf('booking_no')]][$value[csf('batch_no')]]['qnty']+= $value[csf('qnty')];
					}

				}

 	    	}

 	    }
 	   


		$total_production_sammary=array(1=>'Inhouse (Self Order)',2=>'Outbound-Subcon',3=>'Sample With Order',4=>'Sample Without Order',5=>'Inbound Subcontract
			',6=>'Fabric Sales Order');

		
		$gr_qnty=0;
		foreach($knit_buyer_samary as $type_id=>$po_data)
		{
			 
			$i=1;
			?>
		  <table style="margin:0px auto;" cellspacing="0" cellpadding="0" border="1" rules="all" width="560px" class="rpt_table" >
		  		<br>
				<caption><strong><? echo $total_production_sammary[$type_id];?></strong></caption>
				<thead>
					<tr>					 
						<th colspan="6"><b>Knitting <? echo change_date_format($date);  ?></b></th>            
					</tr>
					<tr>
						<th width="40">SL</th>
						<?
						if($type_id!=4 &&  $type_id!=6)
						{
							?>
							<th width="50">Job</th>
							<?
							if($type_id!=5)
							{
								?>
								<th width="100">Int. Ref. No</th> 

								<?

							}
							else
							{
								?>
								<th width="100">Po No</th> 

								<?
							}
							

						}
						else
						{
							if($type_id==4)
							{
								?>
								<th width="150"  colspan="2">Booking No</th>
								<?
								 

							}
							else
							{
								?>
								<th width="150" colspan="2">FSO No</th>
								<?
							}
							
						}

						?>
						
						<th width="120">Buyer</th> 
						<th width="100">Style</th> 
						<th>Knitting Qty</th> 
					</tr>
					
				</thead>
				<tbody>
					<?
					foreach($po_data as $po_id=>$buyer_data)
					{
						foreach($buyer_data as $buyer_id=>$rows)
						{
							?>

							<tr>
								<td align="center"><? echo $i;$i++;?></td>
								<?
								if($type_id!=4 &&  $type_id!=6)
								{
									 

									?>
									<td align="center"><?  if($type_id==5){ echo $order_array_subcon[$po_id]['job'];} else{echo  $booking_array[$po_id]['job'];} ?></td>
									<td align="center"><?  if($type_id==5){ echo $order_array_subcon[$po_id]['po_number'];} else{echo  $booking_array[$po_id]['grouping'];} ?></td> 

									<?

								}
								else
								{
									if($type_id==4)
									{
										?>
										<td align="center" colspan="2"><? echo $po_id; ?></td>
										<?


									}
									else
									{
										?>
										<td align="center" colspan="2"><? echo $po_id; ?></td>  
										<?
									}

								}

								?>

								<td align="center"><? if($type_id==5){ echo $buyerArr[$order_array_subcon[$po_id]['buyer_name']];} else{echo  $buyerArr[$booking_array[$po_id]['buyer_name']];} ?></td> 
								<td align="center"><? if($type_id==5){ echo $order_array_subcon[$po_id]['style_ref_no'];} else { echo $booking_array[$po_id]['style_ref_no']; } ?></td> 
								<td align="center"><? echo $rows['qnty']; $gr_qnty+=$rows['qnty'];?></td> 
							</tr>



							<?

						}
					}



				
			 
		}

					?>
					<tr style="background-color: #E4E4E4;">
						<td colspan="5" align="right"><b>Grand Total</b></td>
						<td align="center"><b><? echo $gr_qnty;?></b></td>
					</tr>


			    </tbody>
		  </table>
				<?


 
    exit();	
}

if($action=="finish_receive_qnty")
{
	extract($_REQUEST);
	$comp_expl=explode('_',$company_id);
	
	$comp_ids_arr=explode(',',$comp_expl[0]);
	$comp_ids="";
	foreach($comp_ids_arr as $comp)
	{
		$comp_ids.=$comp.',';
	}
	$comp_id=chop($comp_ids,',');
	//$comp_id=$comp_expl[0];
	$comp_type=$comp_expl[1];
	echo load_html_head_contents("Production Report", "../../../", 1, 1,$unicode,'','');	
	//echo $date." ".$po_id;die;//echo $date;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="720">
        <thead>
            <tr>
                <th colspan="6"><b>Finishing <? echo change_date_format($date);  ?></b></th>
            </tr>
            <tr>
            	<th width="30">SL</th>
                <th width="70">Job</th>
                <th width="200">Po No</th>
                <th width="120">Buyer</th>
                <th width="120">Style</th>
                <th>Finishing Qty</th>
           </tr>
        </thead>
    <?
		$order_array=array();
		$po_sql="select b.id, a.buyer_name, a.job_no_prefix_num, a.style_ref_no, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0";
		$po_sql_result=sql_select($po_sql);
		foreach ($po_sql_result as $row)
		{
			$order_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
			$order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
			$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		}        
		if(str_replace("'","",$location_id)==0){$location_con_rcv="";}else{$location_con_rcv=" and a.location_id=$location_id";}
		$total_finish_qnty=0;
		if($db_type==0)
		{
			$date2=change_date_format($date,'yyyy-mm-dd');
		}
		else if($db_type==2)
		{
			$date2=change_date_format($date,'','',1);
		}
		if($comp_type=="workingComp") $cbo_company_cond_finish="and a.knitting_company='$comp_id'"; else $cbo_company_cond_finish=" and a.company_id in($comp_id)";
		$finish_query="select a.receive_date as production_date, sum(c.quantity) as finishing_qnty, c.po_breakdown_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id $cbo_company_cond_finish $location_con_rcv and a.receive_date='$date2' and a.entry_form=7 and a.item_category=2 and c.entry_form=7 and c.trans_type=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.receive_date, c.po_breakdown_id";
		
		$finish_query_nonOrder="select a.id, a.recv_number, a.buyer_id, a.receive_date as production_date, sum(b.cons_quantity) as finishing_qnty 
		from  inv_receive_master a,  inv_transaction b, pro_batch_create_mst c 
		where a.id=b.mst_id and b.pi_wo_batch_no=c.id $cbo_company_cond_finish $location_con_rcv and a.receive_date='$date2' and a.entry_form=7 and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.booking_without_order=1  and a.receive_basis=5
		group by a.id, a.recv_number, a.buyer_id, a.receive_date";
		
		
		$finish_query_result=sql_select($finish_query);
		$finish_query_nonOrder_result=sql_select($finish_query_nonOrder);
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
        $i=1;
        foreach ($finish_query_result as $row)  
        {
			if($order_array[$row[csf('po_breakdown_id')]]['po_number']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td><? echo $i; ?></td>
                <td><p><? echo $order_array[$row[csf('po_breakdown_id')]]['job']; ?>&nbsp;</p></td>
                <td><p><? echo $order_array[$row[csf('po_breakdown_id')]]['po_number']; ?>&nbsp;</p></td>
                <td><p><? echo $buyerArr[$order_array[$row[csf('po_breakdown_id')]]['buyer_name']]; ?>&nbsp;</p></td>
                <td><p><? echo $order_array[$row[csf('po_breakdown_id')]]['style_ref_no']; ?>&nbsp;</p></td>
                <td align="right"><? echo number_format($row[csf('finishing_qnty')],2); $total_finish_qnty+=$row[csf('finishing_qnty')]; ?></td>
			</tr>
			<?
			$i++;
			}
        }
		
		foreach ($finish_query_nonOrder_result as $row)  
        {
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td><? echo $i; ?></td>
                <td><p>&nbsp;</p></td>
                <td><p>Non Order&nbsp;</p></td>
                <td><p><? echo $buyerArr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
                <td><p><? echo $row[csf('recv_number')]; ?>&nbsp;</p></td>
                <td align="right"><? echo number_format($row[csf('finishing_qnty')],2); $total_finish_qnty+=$row[csf('finishing_qnty')]; ?></td>
			</tr>
			<?
			$i++;
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_finish_qnty,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="printreceived")
{
	extract($_REQUEST);
	$comp_expl=explode('_',$company_id);
	
	$comp_ids_arr=explode(',',$comp_expl[0]);
	$comp_ids="";
	foreach($comp_ids_arr as $comp)
	{
		$comp_ids.=$comp.',';
	}
	$comp_id=chop($comp_ids,',');
	//$comp_id=$comp_expl[0];
	$comp_type=$comp_expl[1];
	echo load_html_head_contents("Production Report", "../../../", 1, 1,$unicode,'','');	
	//echo $date;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="720">
        <thead>
            <tr>
                <th colspan="6"><b>Printing (<? echo change_date_format($date);  ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="100px">Po No</th>
                <th width="100px">Buyer</th>
                <th width="120px">Style</th>
                <th width="90px">Printing Qty</th>
           </tr>
        </thead>
    <?
		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0");
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		}
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
		if($comp_type=="workingComp") $cbo_company_cond_finish="serving_company like '$comp_id'"; else $cbo_company_cond_finish="company_id in($comp_id)";

        $total_printing_qnty=0;
		if($db_type==0)
		{
			$printing_query="select sum(production_quantity) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where $cbo_company_cond_finish $location_con and production_date='$date' and production_type=3 and embel_name=1 and status_active=1 and is_deleted=0 group by  production_date, po_break_down_id";
		}
		else if($db_type==2)
		{
			$date2=change_date_format($date,'','',1);
			$printing_query="select sum(production_quantity) as production_quantity, po_break_down_id from pro_garments_production_mst where $cbo_company_cond_finish $location_con and production_date='$date2' and production_type=3 and embel_name=1 and status_active=1 and is_deleted=0  group by po_break_down_id";
		}
	
		$printing_query_result=sql_select($printing_query);
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
        $i=1;
        foreach ($printing_query_result as $row)  
        {
			if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="100px"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="100px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_printing_qnty+=$row[csf('production_quantity')]; ?></td>
			</tr>
			<?
			$i++;
			}
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_printing_qnty,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="emb_qnty")
{
	extract($_REQUEST);
	$comp_expl=explode('_',$company_id);
	
	$comp_ids_arr=explode(',',$comp_expl[0]);
	$comp_ids="";
	foreach($comp_ids_arr as $comp)
	{
		$comp_ids.=$comp.',';
	}
	$comp_id=chop($comp_ids,',');
	
	//$comp_id=$comp_expl[0];
	$comp_type=$comp_expl[1];
	echo load_html_head_contents("Production Report", "../../../", 1, 1,$unicode,'','');	
	
	?>
    <table border="1" class="rpt_table" rules="all" width="720">
        <thead>
            <tr>
                <th colspan="6"><b>Embellishment (<? echo change_date_format($date);  ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="100px">Po No</th>
                <th width="100px">Buyer</th>
                <th width="120px">Style</th>
                <th width="90px">Embellishment Qty</th>
           </tr>
        </thead>
    <?
		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0");
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		}
		
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
		if($comp_type=="workingComp") $cbo_company_cond_finish="serving_company like '$comp_id'"; else $cbo_company_cond_finish="company_id in($comp_id)";

        $total_embellishment_qnty=0;
		if($db_type==0)
		{
			$embellishment_query="select sum(production_quantity) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where $cbo_company_cond_finish $location_con and production_date='$date' and production_type=3 and embel_name=2 and status_active=1 and is_deleted=0 group by production_date, po_break_down_id ";
		}
		else if($db_type==2)
		{
			$date2=change_date_format($date,'','',1);
			$embellishment_query="select  sum(production_quantity) as production_quantity, po_break_down_id from pro_garments_production_mst where $cbo_company_cond_finish and production_date='$date2' $location_con and production_type=3 and embel_name=2 and status_active=1 and is_deleted=0  group by po_break_down_id";
		}
		
		$embellishment_query_result=sql_select($embellishment_query);
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
        $i=1;
        foreach ($embellishment_query_result as $row)  
        {
			if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="100px"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="100px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_embellishment_qnty+=$row[csf('production_quantity')]; ?></td>
			</tr>
			<?
			$i++;
		 	}
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_embellishment_qnty,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="cutting_inbound")
{
  	extract($_REQUEST);
	$comp_expl=explode('_',$company_id);
	
	$comp_ids_arr=explode(',',$comp_expl[0]);
	$comp_ids="";
	foreach($comp_ids_arr as $comp)
	{
		$comp_ids.=$comp.',';
	}
	$comp_id=chop($comp_ids,',');
	
	//$comp_id=$comp_expl[0];
	$comp_type=$comp_expl[1];
	echo load_html_head_contents("Production Report", "../../../", 1, 1,$unicode,'','');	
	//echo $date;//company_id
	?>	
    
     <table border="1" class="rpt_table" rules="all" width="720">
        <thead>
            <tr>
                <th colspan="6"><b>Cutting In Bound-Subcontract (<? echo change_date_format($date);  ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="100px">Po Number</th>
                <th width="100px">Buyer</th>
                <th width="120px">Style</th>
                <th width="90px">Cutting Qty</th>
           </tr>
        </thead>
    <?
		$po_array=array();
		//$query_po_break_down=sql_select("select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		
		
		
		$query_po_break_down=sql_select("select  a.subcon_job, a.job_no_prefix_num, a.party_id, b.id, b.order_no, b.cust_style_ref from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and a.id=c.mst_id and b.id=c.order_id  order by a.id DESC");
		
		
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job']=$row[csf('subcon_job')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('party_id')];
			$po_array[$row[csf('id')]]['style']=$row[csf('cust_style_ref')];
			$po_array[$row[csf('id')]]['po']=$row[csf('order_no')];
		}
		
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location_id=$location_id";}
		if($comp_type=="workingComp") $cbo_company_condition="company_id like '$comp_id'"; else $cbo_company_condition="company_id in($comp_id)";
        $total_cutting_qnty=0;
		if($db_type==0)
		{
			$cutting_query="select sum(production_qnty) as production_quantity, production_date,order_id as po_break_down_id from subcon_gmts_prod_dtls where company_id in($comp_id) $location_con and production_date='$date' and production_type=1  and status_active=1 and is_deleted=0 group by  production_date,order_id";
		}
		else if($db_type==2)
		{
			$date2=change_date_format($date,'','',1);
			
			$cutting_query="select sum(production_qnty) as production_quantity, order_id as po_break_down_id from subcon_gmts_prod_dtls where company_id in($comp_id) $location_con and production_date='$date2' and production_type=1  and status_active=1 and is_deleted=0 group by order_id";
		}
		//echo $printing_query;
		$cutting_query_result=sql_select($cutting_query);
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
        $i=1;
        foreach ($cutting_query_result as $row)  
        {
			if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="100px"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="100px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_cutting_qnty+=$row[csf('production_quantity')]; ?></td>
			</tr>
			<?
			$i++;
			}
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_cutting_qnty,2) ?></th>
        </tfoot>
    </table>
    
	 <?  
     exit();	   
}

if($action=="sewing_inbound")
{
	extract($_REQUEST);
	$comp_expl=explode('_',$company_id);
	
	$comp_ids_arr=explode(',',$comp_expl[0]);
	$comp_ids="";
	foreach($comp_ids_arr as $comp)
	{
		$comp_ids.=$comp.',';
	}
	$comp_id=chop($comp_ids,',');
	
	//$comp_id=$comp_expl[0];
	$comp_type=$comp_expl[1];
	echo load_html_head_contents("Production Report", "../../../", 1, 1,$unicode,'','');	
	//echo $date;//company_id
	?>
      <table border="1" class="rpt_table" rules="all" width="720">
        <thead>
            <tr>
                <th colspan="6"><b>Sewing In Bound-Subcontract (<? echo change_date_format($date);  ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="100px">Po Number</th>
                <th width="100px">Buyer</th>
                <th width="120px">Style</th>
                <th width="90px">Sewing Qty</th>
           </tr>
        </thead>
    <?
		$po_array=array();
		//$query_po_break_down=sql_select("select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		
		$query_po_break_down=sql_select("select  a.subcon_job, a.job_no_prefix_num, a.party_id, b.id, b.order_no, b.cust_style_ref from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and a.id=c.mst_id and b.id=c.order_id  order by a.id DESC");
		
		
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job']=$row[csf('subcon_job')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('party_id')];
			$po_array[$row[csf('id')]]['style']=$row[csf('cust_style_ref')];
			$po_array[$row[csf('id')]]['po']=$row[csf('order_no')];
		}
		
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location_id=$location_id";}
		if($comp_type=="workingComp") $cbo_company_condition="company_id like '$comp_id'"; else $cbo_company_condition="company_id in($comp_id)";
        $total_cutting_qnty=0;
		if($db_type==0)
		{
			$sweing_query="select sum(production_qnty) as production_quantity, production_date,order_id as po_break_down_id from subcon_gmts_prod_dtls where company_id in($comp_id) $location_con and production_date='$date' and production_type=2  and status_active=1 and is_deleted=0 group by production_date,order_id";
		}
		else if($db_type==2)
		{
			$date2=change_date_format($date,'','',1);
			
			$sweing_query="select sum(production_qnty) as production_quantity, order_id as po_break_down_id from subcon_gmts_prod_dtls where company_id in($comp_id) $location_con and production_date='$date2' and production_type=2  and status_active=1 and is_deleted=0 group by order_id";
		}
		//echo $printing_query;
		$sewing_query_result=sql_select($sweing_query);
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
        $i=1;
        foreach ($sewing_query_result as $row)  
        {
			if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="100px"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="100px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_cutting_qnty+=$row[csf('production_quantity')]; ?></td>
			</tr>
			<?
			$i++;
			}
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_cutting_qnty,2) ?></th>
        </tfoot>
    </table>    
    <?
	exit();	
}

if($action=="finish_inbound")
{
	extract($_REQUEST);
	$comp_expl=explode('_',$company_id);
	
	$comp_ids_arr=explode(',',$comp_expl[0]);
	$comp_ids="";
	foreach($comp_ids_arr as $comp)
	{
		$comp_ids.=$comp.',';
	}
	$comp_id=chop($comp_ids,',');
	
	//$comp_id=$comp_expl[0];
	$comp_type=$comp_expl[1];
	echo load_html_head_contents("Production Report", "../../../", 1, 1,$unicode,'','');	
	//echo $date;//company_id
	?>
     <table border="1" class="rpt_table" rules="all" width="720">
        <thead>
            <tr>
                <th colspan="6"><b>Finishing In Bound-Subcontract (<? echo change_date_format($date);  ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="100px">Po Number</th>
                <th width="100px">Buyer</th>
                <th width="120px">Style</th>
                <th width="90px">Finishing Qty</th>
           </tr>
        </thead>
    <?
		$po_array=array();
		//$query_po_break_down=sql_select("select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		
		$query_po_break_down=sql_select("select  a.subcon_job, a.job_no_prefix_num, a.party_id, b.id, b.order_no, b.cust_style_ref from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and a.id=c.mst_id and b.id=c.order_id  order by a.id DESC");
		
		
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job']=$row[csf('subcon_job')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('party_id')];
			$po_array[$row[csf('id')]]['style']=$row[csf('cust_style_ref')];
			$po_array[$row[csf('id')]]['po']=$row[csf('order_no')];
		}
		
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location_id=$location_id";}
		if($comp_type=="workingComp") $cbo_company_condition="company_id like '$comp_id'"; else $cbo_company_condition="company_id in($comp_id)";
        $total_finishing_qnty=0;
		if($db_type==0)
		{
			$finishing_query="select sum(production_qnty) as production_quantity, production_date,order_id as po_break_down_id from subcon_gmts_prod_dtls where company_id in($comp_id) $location_con and production_date='$date' and production_type=4  and status_active=1 and is_deleted=0 group by production_date,order_id ";
		}
		else if($db_type==2)
		{
			$date2=change_date_format($date,'','',1);
			
			$finishing_query="select sum(production_qnty) as production_quantity, order_id as po_break_down_id from subcon_gmts_prod_dtls where company_id in($comp_id)  $location_con and production_date='$date2' and production_type=4  and status_active=1 and is_deleted=0 group by order_id";
		}
		//echo $printing_query;
		$finishing_query_result=sql_select($finishing_query);
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
        $i=1;
        foreach ($finishing_query_result as $row)  
        {
			if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="100px"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="100px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_finishing_qnty+=$row[csf('production_quantity')]; ?></td>
			</tr>
			<?
			$i++;
			}
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_finishing_qnty,2) ?></th>
        </tfoot>
    </table>
    <?	
	exit();	
}

if($action=="cutting_inhouse") 
{
	extract($_REQUEST);
	$comp_expl=explode('_',$company_id);
	
	$comp_ids_arr=explode(',',$comp_expl[0]);
	$comp_ids="";
	foreach($comp_ids_arr as $comp)
	{
		$comp_ids.=$comp.',';
	}
	$comp_id=chop($comp_ids,',');
	$companys=$_SESSION["comp"];
	$work_comp=$_SESSION["work_comp"];
	
	//$comp_id=$comp_expl[0];
	$comp_type=$comp_expl[1];
	echo load_html_head_contents("Production Report", "../../../", 1, 1,$unicode,'','');	
	//echo $date;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="720">
        <thead>
            <tr>
                <th colspan="6"><b>Cutting In House (<? echo change_date_format($date);  ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="100px">Po Number</th>
                <th width="100px">Buyer</th>
                <th width="120px">Style</th>
                <th width="90px">Cutting Qty</th>
           </tr>
        </thead>
    <?
		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0");
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		}
		
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
		if($comp_type=="workingComp") $cbo_company_condition="serving_company like '$comp_id'"; else $cbo_company_condition="company_id in($comp_id)";
        $total_cutting_qnty=0;
        if($companys)
        {
        	$companys_cond=" and company_id in ($companys) ";
        }
        if($work_comp)
        {
        	$serving_company_cond="and serving_company in($work_comp) ";
        }
		if($db_type==0)
		{
			$cutting_query="select sum(production_quantity) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where $cbo_company_condition $location_con $companys_cond $serving_company_cond  and production_date='$date' and production_type=1 and production_source=1 and status_active=1 and is_deleted=0 group by production_date, po_break_down_id ";
		}
		else if($db_type==2)
		{
			$date2=change_date_format($date,'','',1);
			$cutting_query="select sum(production_quantity) as production_quantity, po_break_down_id from pro_garments_production_mst where $cbo_company_condition $location_con  $companys_cond $serving_company_cond  and production_date='$date2' and production_type=1 and production_source=1 and status_active=1 and is_deleted=0 group by po_break_down_id";
		}
		//echo $printing_query;
		$cutting_query_result=sql_select($cutting_query);
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
        $i=1;
        foreach ($cutting_query_result as $row)  
        {
			if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="100px"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="100px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_cutting_qnty+=$row[csf('production_quantity')]; ?></td>
			</tr>
			<?
			$i++;
			}
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_cutting_qnty,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="cutting_subcontract") 
{
	extract($_REQUEST);
	$comp_expl=explode('_',$company_id);
	$companys=$_SESSION["comp"];
	$work_comp=$_SESSION["work_comp"];
	$comp_ids_arr=explode(',',$comp_expl[0]);
	$comp_ids="";
	foreach($comp_ids_arr as $comp)
	{
		$comp_ids.=$comp.',';
	}
	$comp_id=chop($comp_ids,',');
	
	//$comp_id=$comp_expl[0];
	$comp_type=$comp_expl[1];
	echo load_html_head_contents("Production Report", "../../../", 1, 1,$unicode,'','');	
	//echo $date;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="720">
        <thead>
            <tr>
                <th colspan="6"><b>Cutting Sub-Contract (<? echo change_date_format($date);  ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="100px">Po Number</th>
                <th width="100px">Buyer</th>
                <th width="120px">Style</th>
                <th width="90px">Cutting Qty</th>
           </tr>
        </thead>
    <?
    if($companys && !$work_comp)
		{

			$po_array=array();
			$query_po_break_down=sql_select("select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0");
			foreach ($query_po_break_down as $row)
			{
				$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
				$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
				$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
				$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
			}
			
			if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
			if($comp_type=="workingComp") $cbo_company_condition="serving_company like '$comp_id'"; else $cbo_company_condition="company_id in($comp_id)";

	        $total_cutting_qnty=0;
			if($db_type==0)
			{
				$cutting_query="select sum(production_quantity) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where $cbo_company_condition $location_con and production_date='$date' and production_type=1 and production_source=3 and status_active=1 and is_deleted=0 group by production_date, po_break_down_id";
			}
			else if($db_type==2)
			{
				$date2=change_date_format($date,'','',1);
				$cutting_query="select sum(production_quantity) as production_quantity,po_break_down_id from pro_garments_production_mst where $cbo_company_condition $location_con and production_date='$date2' and production_type=1 and production_source=3 and status_active=1 and is_deleted=0 group by po_break_down_id";
			}
			//echo $cutting_query;
			$cutting_query_result=sql_select($cutting_query);
		}
		else if(!$companys && $work_comp)
		{

			$po_array=array();
			$query_po_break_down=sql_select("SELECT a.job_no_prefix_num, a.party_id as buyer_name, b.cust_style_ref as style_ref_no, b.id, b.order_no as po_number,b.rate as unit_price from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0");
			foreach ($query_po_break_down as $row)
			{
				$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
				$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
				$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
				$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
				$po_array[$row[csf('id')]]['unit_price']=$row[csf('unit_price')];

			}

			if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
		 
			$total_finishing_qnty=0;
			if($db_type==0)
			{
				$cutting_query="SELECT id, sum(production_qnty) as production_quantity, production_date,order_id as  po_break_down_id from subcon_gmts_prod_dtls where company_id in($work_comp) $location_con and production_date='$date' and production_type=1   and status_active=1 and is_deleted=0  group by order_id";
			}
			elseif($db_type==2)
			{
				$date2=change_date_format($date,'','',1);
				$cutting_query="SELECT  sum(production_qnty) as production_quantity, order_id as po_break_down_id from subcon_gmts_prod_dtls where company_id in($work_comp)  $location_con and production_date='$date2' and production_type=1 and status_active=1 and is_deleted=0  group by order_id";
			}
		 
			$cutting_query_result=sql_select($cutting_query);
			//$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
		}

        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
        $i=1;
        foreach ($cutting_query_result as $row)  
        {
			if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="100px"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="100px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_cutting_qnty+=$row[csf('production_quantity')]; ?></td>
			</tr>
			<?
			$i++;
			}
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_cutting_qnty,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}
 
if($action=="cutting") 
{
	extract($_REQUEST);
	$comp_expl=explode('_',$company_id);
	$companys=$_SESSION["comp"];
	$work_comp=$_SESSION["work_comp"];
	$comp_ids_arr=explode(',',$comp_expl[0]);
	$comp_ids="";
	foreach($comp_ids_arr as $comp)
	{
		$comp_ids.=$comp.',';
	}
	$comp_id=chop($comp_ids,',');
	
	//$comp_id=$comp_expl[0];
	$comp_type=$comp_expl[1];

	echo load_html_head_contents("Production Report", "../../../", 1, 1,$unicode,'','');	
	if($companys)
        {
        	$companys_cond=" and company_id in ($companys) ";
        }
        if($work_comp)
        {
        	$serving_company_cond="and serving_company in($work_comp) ";
        }

       
	?>
    <table border="1" class="rpt_table" rules="all" width="720">
        <thead>
            <tr>
                <th colspan="6"><b>Cutting Total (<? echo change_date_format($date);  ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="100px">Po Number</th>
                <th width="100px">Buyer</th>
                <th width="120px">Style</th>
                <th width="90px">Cutting Qty</th>
           </tr>
        </thead>
    <?
		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0");
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		}
		
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
		if($comp_type=="workingComp") $cbo_company_condition="serving_company like '$comp_id'"; else $cbo_company_condition="company_id in($comp_id)";

        $total_cutting_qnty=0;
        if($companys && !$work_comp)
        {
        	if($db_type==0)
        	{
        		$cutting_query="select sum(production_quantity) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where $cbo_company_condition $location_con and production_date='$date'  $companys_cond $serving_company_cond and production_type=1 and status_active=1 and is_deleted=0 group by production_date, po_break_down_id";
        	}
        	else if($db_type==2)
        	{
        		$date2=change_date_format($date,'','',1);
        		$cutting_query="select sum(production_quantity) as production_quantity, po_break_down_id from pro_garments_production_mst where $cbo_company_condition $location_con and production_date='$date2'  $companys_cond $serving_company_cond and production_type=1 and status_active=1 and is_deleted=0  group by po_break_down_id";
        	}

        }
        else if(!$companys && $work_comp)
        {
        	if($db_type==0)
        	{
        		$cutting_query="select sum(production_quantity) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where $cbo_company_condition $location_con and production_date='$date'  $companys_cond $serving_company_cond and production_source=1 and production_type=1 and status_active=1 and is_deleted=0 group by production_date, po_break_down_id ";
        	}
        	else if($db_type==2)
        	{
        		$date2=change_date_format($date,'','',1);
        		$cutting_query="select sum(production_quantity) as production_quantity, po_break_down_id from pro_garments_production_mst where $cbo_company_condition $location_con and production_date='$date2' and production_type=1  $companys_cond $serving_company_cond and production_source=1 and status_active=1 and is_deleted=0  group by po_break_down_id";
        	}

        	$po_array2=array();
			$query_po_break_down2=sql_select("SELECT a.job_no_prefix_num, a.party_id as buyer_name, b.cust_style_ref as style_ref_no, b.id, b.order_no as po_number,b.rate as unit_price from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0");
			foreach ($query_po_break_down2 as $row)
			{
				$po_array2[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
				$po_array2[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
				$po_array2[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
				$po_array2[$row[csf('id')]]['po']=$row[csf('po_number')];
				$po_array2[$row[csf('id')]]['unit_price']=$row[csf('unit_price')];

			}

			if($db_type==0)
			{
				$cutting_query2="SELECT sum(production_qnty) as production_quantity, production_date,order_id as  po_break_down_id from subcon_gmts_prod_dtls where company_id in($work_comp) $location_con and production_date='$date' and production_type=1   and status_active=1 and is_deleted=0  group by order_id";
			}
			elseif($db_type==2)
			{
				$date2=change_date_format($date,'','',1);
				$cutting_query2="SELECT  sum(production_qnty) as production_quantity, order_id as po_break_down_id from subcon_gmts_prod_dtls where company_id in($work_comp)  $location_con and production_date='$date2' and production_type=1 and status_active=1 and is_deleted=0  group by order_id";
			}
		 
			$cutting_query_result2=sql_select($cutting_query2);



        }
        else
        {
        	if($db_type==0)
        	{
        		$cutting_query="select sum(production_quantity) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where $cbo_company_condition $location_con and production_date='$date'  $companys_cond $serving_company_cond  and production_source=1 and production_type=1 and status_active=1 and is_deleted=0 group by production_date, po_break_down_id";
        	}
        	else if($db_type==2)
        	{
        		$date2=change_date_format($date,'','',1);
        		$cutting_query="select sum(production_quantity) as production_quantity, po_break_down_id from pro_garments_production_mst where $cbo_company_condition $location_con and production_date='$date2' and production_type=1  $companys_cond $serving_company_cond and production_source=1 and status_active=1 and is_deleted=0  group by po_break_down_id";
        	}
        }
		
		
		$cutting_query_result=sql_select($cutting_query);
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
        $i=1;
        foreach ($cutting_query_result as $row)  
        {
			if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="100px"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="100px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['style'] ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_cutting_qnty+=$row[csf('production_quantity')]; ?></td>
			</tr>
			<?
			$i++;
			}
        }

        foreach ($cutting_query_result2 as $row)  
        {
			if($po_array2[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array2[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="100px"><? echo $po_array2[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="100px"><? echo $buyerArr[$po_array2[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array2[$row[csf('po_break_down_id')]]['style'] ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_cutting_qnty+=$row[csf('production_quantity')]; ?></td>
			</tr>
			<?
			$i++;
			}
        }

        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_cutting_qnty,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="sewingout_subcontract") 
{
	extract($_REQUEST);
	$comp_expl=explode('_',$company_id);
	$companys=$_SESSION["comp"];
	$work_comp=$_SESSION["work_comp"];
	$comp_ids_arr=explode(',',$comp_expl[0]);
	$comp_ids="";
	foreach($comp_ids_arr as $comp)
	{
		$comp_ids.=$comp.',';
	}
	$comp_id=chop($comp_ids,',');
	
	//$comp_id=$comp_expl[0];
	$comp_type=$comp_expl[1];

	echo load_html_head_contents("Production Report", "../../../", 1, 1,$unicode,'','');	
	
	?>
    <table border="1" class="rpt_table" rules="all" width="720">
        <thead>
            <tr>
                <th colspan="6"><b>Sewing Sub-Contract (<? echo change_date_format($date);  ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="100px">Po No</th>
                <th width="100px">Buyer</th>
                <th width="120px">Style</th>
                <th width="90px">Sewing Qty</th>
           </tr>
        </thead>
    <?
    if($companys && !$work_comp)
		{

			$po_array=array();
			$query_po_break_down=sql_select("select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0");
			foreach ($query_po_break_down as $row)
			{
				$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
				$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
				$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
				$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
			}
			
			if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
			if($comp_type=="workingComp") $cbo_company_condition="serving_company like '$comp_id'"; else $cbo_company_condition="company_id in($comp_id)";

	        $total_sewing_qnty=0;
			if($db_type==0)
			{
				$sewing_query="select sum(production_quantity) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where  $cbo_company_condition $location_con and production_date='$date' and production_type=5 and production_source=3 and status_active=1 and is_deleted=0 group by production_date, po_break_down_id";
			}
			else if($db_type==2)
			{
				$date2=change_date_format($date,'','',1);
				$sewing_query="select sum(production_quantity) as production_quantity, po_break_down_id from pro_garments_production_mst where $cbo_company_condition $location_con and production_date='$date2' and production_type=5 and production_source=3 and status_active=1 and is_deleted=0 group by po_break_down_id";
			}
			//echo $sewing_query;
			$sewing_query_result=sql_select($sewing_query);
		}
		else if(!$companys && $work_comp)
		{

			$po_array=array();
			$query_po_break_down=sql_select("SELECT a.job_no_prefix_num, a.party_id as buyer_name, b.cust_style_ref as style_ref_no, b.id, b.order_no as po_number,b.rate as unit_price from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0");
			foreach ($query_po_break_down as $row)
			{
				$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
				$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
				$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
				$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
				$po_array[$row[csf('id')]]['unit_price']=$row[csf('unit_price')];

			}

			if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
		 
			$total_finishing_qnty=0;
			if($db_type==0)
			{
				$sewing_query="SELECT id, sum(production_qnty) as production_quantity, production_date,order_id as  po_break_down_id from subcon_gmts_prod_dtls where company_id in($work_comp) $location_con and production_date='$date' and production_type=2   and status_active=1 and is_deleted=0  group by order_id";
			}
			elseif($db_type==2)
			{
				$date2=change_date_format($date,'','',1);
				$sewing_query="SELECT  sum(production_qnty) as production_quantity, order_id as po_break_down_id from subcon_gmts_prod_dtls where company_id in($work_comp)  $location_con and production_date='$date2' and production_type=2 and status_active=1 and is_deleted=0  group by order_id";
			}
		 
			$sewing_query_result=sql_select($sewing_query);
 		}

        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
        $i=1;
        foreach ($sewing_query_result as $row)  
        {
			if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="100px"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="100px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_sewing_qnty+=$row[csf('production_quantity')]; ?></td>
			</tr>
			<?
			$i++;
			}
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_sewing_qnty,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="sewingout_inhouse")
{
	extract($_REQUEST);
	$comp_expl=explode('_',$company_id);
	
	$comp_ids_arr=explode(',',$comp_expl[0]);
	$comp_ids="";
	foreach($comp_ids_arr as $comp)
	{
		$comp_ids.=$comp.',';
	}
	$comp_id=chop($comp_ids,',');
	
	//$comp_id=$comp_expl[0];
	$comp_type=$comp_expl[1];
	$companys=$_SESSION["comp"];
	$work_comp=$_SESSION["work_comp"];
	if($companys)
	{
		$companys_cond=" and company_id in ($companys) ";
	}
	if($work_comp)
	{
		$serving_company_cond="and serving_company in($work_comp) ";
	}


	echo load_html_head_contents("Production Report", "../../../", 1, 1,$unicode,'','');	
	//echo $date;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="720">
        <thead>
            <tr>
                <th colspan="6"><b>Sewing In House (<? echo change_date_format($date);  ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="100px">Po No</th>
                <th width="100px">Buyer</th>
                <th width="120px">Style</th>
                <th width="90px">Sewing Qty</th>
           </tr>
        </thead>
    <?
		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0");
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		}
		
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
		if($comp_type=="workingComp") $cbo_company_condition="serving_company like '$comp_id'"; else $cbo_company_condition="company_id in($comp_id)";

        $total_sewing_qnty=0;
		if($db_type==0)
		{
			$sewing_query="select sum(production_quantity) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where  $cbo_company_condition $location_con and production_date='$date' and production_type=5 and production_source=1 $companys_cond $serving_company_cond and status_active=1 and is_deleted=0 group by production_date, po_break_down_id";
		}
		elseif($db_type==2)
		{
			 $date2=change_date_format($date,'','',1);
			$sewing_query="select  sum(production_quantity) as production_quantity,  po_break_down_id from pro_garments_production_mst where $cbo_company_condition $location_con and production_date='$date2' and production_type=5 and production_source=1 and status_active=1 $companys_cond $serving_company_cond and is_deleted=0  group by po_break_down_id";
		}
		
		$sewing_query_result=sql_select($sewing_query);
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
        $i=1;
        foreach ($sewing_query_result as $row)  
        {
			if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="100px"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="100px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_sewing_qnty+=$row[csf('production_quantity')]; ?></td>
			</tr>
			<?
			$i++;
			}
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_sewing_qnty,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="sewingout") 
{
	extract($_REQUEST);
	$comp_expl=explode('_',$company_id);
	$comp_ids_arr=explode(',',$comp_expl[0]);
	$comp_ids="";
	$companys=$_SESSION["comp"];
	$work_comp=$_SESSION["work_comp"];
	foreach($comp_ids_arr as $comp)
	{
		$comp_ids.=$comp.',';
	}
	$comp_id=chop($comp_ids,',');
	
	//$comp_id=$comp_expl[0];
	$comp_type=$comp_expl[1];
	echo load_html_head_contents("Production Report", "../../../", 1, 1,$unicode,'','');	
	//echo $date;//company_id
	if($companys)
        {
        	$companys_cond=" and company_id in ($companys) ";
        }
        if($work_comp)
        {
        	$serving_company_cond="and serving_company in($work_comp) ";
        }

        
	?>
    <table border="1" class="rpt_table" rules="all" width="100%">
        <thead>
            <tr>
                <th colspan="6"><b>Sewing Total (<? echo change_date_format($date);  ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="100px">Po No</th>
                <th width="100px">Buyer</th>
                <th width="120px">Style</th>
                <th width="90px">Sewing Qty</th>
           </tr>
        </thead>
    <?
		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0");
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		}
		
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
		if($comp_type=="workingComp") $cbo_company_condition="serving_company like '$comp_id'"; else $cbo_company_condition="company_id in($comp_id)";

        $total_sewing_qnty=0;
        if($companys && !$work_comp)
        {
        	if($db_type==0)
        	{
        		$sewing_query="select sum(production_quantity) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where $cbo_company_condition $location_con and production_date='$date' and production_type=5 $companys_cond $serving_company_cond and status_active=1 and is_deleted=0 group by production_date, po_break_down_id ";
        	}
        	else if($db_type==2)
        	{
        		$date2=change_date_format($date,'','',1);
        		$sewing_query="select sum(production_quantity) as production_quantity, po_break_down_id from pro_garments_production_mst where $cbo_company_condition $location_con and production_date='$date2' and production_type=5 and status_active=1 $companys_cond $serving_company_cond and is_deleted=0  group by po_break_down_id";
        	}

        }

        else if(!$companys && $work_comp)
        {
        	if($db_type==0)
        	{
        		$sewing_query="select sum(production_quantity) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where $cbo_company_condition $location_con and production_date='$date' and production_source=1 $companys_cond $serving_company_cond  and production_type=5 and status_active=1 and is_deleted=0 group by production_date, po_break_down_id";
        	}
        	else if($db_type==2)
        	{
        		$date2=change_date_format($date,'','',1);
        		$sewing_query="select sum(production_quantity) as production_quantity, po_break_down_id from pro_garments_production_mst where $cbo_company_condition $location_con and production_date='$date2' and production_type=5 and production_source=1  $companys_cond $serving_company_cond and status_active=1 and is_deleted=0  group by po_break_down_id";
        	}

        	$po_array2=array();
			$query_po_break_down2=sql_select("SELECT a.job_no_prefix_num, a.party_id as buyer_name, b.cust_style_ref as style_ref_no, b.id, b.order_no as po_number,b.rate as unit_price from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0");
			foreach ($query_po_break_down2 as $row)
			{
				$po_array2[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
				$po_array2[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
				$po_array2[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
				$po_array2[$row[csf('id')]]['po']=$row[csf('po_number')];
				$po_array2[$row[csf('id')]]['unit_price']=$row[csf('unit_price')];

			}

			if($db_type==0)
			{
				$sewing_query2="SELECT id, sum(production_qnty) as production_quantity, production_date,order_id as  po_break_down_id from subcon_gmts_prod_dtls where company_id in($work_comp) $location_con and production_date='$date' and production_type=2   and status_active=1 and is_deleted=0  group by order_id";
			}
			elseif($db_type==2)
			{
				$date2=change_date_format($date,'','',1);
				$sewing_query2="SELECT  sum(production_qnty) as production_quantity, order_id as po_break_down_id from subcon_gmts_prod_dtls where company_id in($work_comp)  $location_con and production_date='$date2' and production_type=2 and status_active=1 and is_deleted=0  group by order_id";
			}
		 
			$sewing_query_result2=sql_select($sewing_query2);



        }
        else
        {
        	if($db_type==0)
        	{
        		$sewing_query="select sum(production_quantity) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where $cbo_company_condition $location_con and production_date='$date' $companys_cond $serving_company_cond and production_source=1 and production_type=5 and status_active=1 and is_deleted=0 group by production_date, po_break_down_id";
        	}
        	else if($db_type==2)
        	{
        		$date2=change_date_format($date,'','',1);
        		$sewing_query="select sum(production_quantity) as production_quantity, po_break_down_id from pro_garments_production_mst where $cbo_company_condition $location_con $companys_cond $serving_company_cond and production_date='$date2' and production_type=5 and production_source=1 and status_active=1 and is_deleted=0  group by po_break_down_id";
        	}
        }

 
		
		//echo $sewing_query;
		$sewing_query_result=sql_select($sewing_query);
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
        $i=1;
        foreach ($sewing_query_result as $row)  
        {	if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="100px"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="100px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_sewing_qnty+=$row[csf('production_quantity')]; ?></td>
			</tr>
			<?
			$i++;
			}
        }

        foreach ($sewing_query_result2 as $row)  
        {	if($po_array2[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array2[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="100px"><? echo $po_array2[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="100px"><? echo $buyerArr[$po_array2[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array2[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_sewing_qnty+=$row[csf('production_quantity')]; ?></td>
			</tr>
			<?
			$i++;
			}
        }

        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_sewing_qnty,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="finish_inhouse") 
{
	extract($_REQUEST);
	$comp_expl=explode('_',$company_id);
	$comp_ids_arr=explode(',',$comp_expl[0]);
	$comp_ids="";
	foreach($comp_ids_arr as $comp)
	{
		$comp_ids.=$comp.',';
	}
	$comp_id=chop($comp_ids,',');
	
	//$comp_id=$comp_expl[0];
	$comp_type=$comp_expl[1];
	echo load_html_head_contents("Production Report", "../../../", 1, 1,$unicode,'','');
	$companys=$_SESSION["comp"];
	$work_comp=$_SESSION["work_comp"];


	if($companys)
	{
		$companys_cond=" and company_id in ($companys) ";
	}
	if($work_comp)
	{
		$serving_company_cond="and serving_company in($work_comp) ";
	}	
	//echo $date;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="797">
        <thead>
            <tr>
                <th colspan="8"><b>Finishing In House (<? echo change_date_format($date);  ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="70px">Po No</th>
                <th width="70px">Buyer</th>
                <th width="70px">Style</th>
                <th width="80px">Finishing Qty</th> 	
                <th width="50px">Unit Price</th>
                <th >Finishing Ord. Value</th>
           </tr>
        </thead>
    <?
		$po_array=array();
		$query_po_break_down=sql_select("SELECT a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number,c.order_rate as unit_price1, b.unit_price,a.total_set_qnty   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0 group by a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number,c.order_rate, b.unit_price,a.total_set_qnty");
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['unit_price']=$row[csf('unit_price')]/$row[csf('total_set_qnty')];
		}
		
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
		if($comp_type=="workingComp") $cbo_company_condition="serving_company like '$comp_id'"; else $cbo_company_condition="company_id in($comp_id)";

        $total_finishing_qnty=0;
		if($db_type==0)
		{
			$finishing_query="select sum(production_quantity) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where   $cbo_company_condition $location_con and production_date='$date' $companys_cond $serving_company_cond  and production_type=8 and production_source=1 and status_active=1 and is_deleted=0 group by production_date, po_break_down_id";
		}
		elseif($db_type==2)
		{
			$date2=change_date_format($date,'','',1);
			$finishing_query="select id,sum(production_quantity) as production_quantity, po_break_down_id from pro_garments_production_mst where  $cbo_company_condition  $location_con and production_date='$date2' $companys_cond $serving_company_cond and production_type=8 and production_source=1 and status_active=1 and is_deleted=0 group by id,po_break_down_id";
		}
		
		$finishing_query_result=sql_select($finishing_query);
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
        $i=1;
        foreach ($finishing_query_result as $row)  
        {	if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="70px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_finishing_qnty+=$row[csf('production_quantity')]; ?></td>
                <td width="50px" align="center"><? echo number_format($po_array[$row[csf('po_break_down_id')]]['unit_price'],2); ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')]*$po_array[$row[csf('po_break_down_id')]]['unit_price'],2); $total_fin_value+=$row[csf('production_quantity')]*$po_array[$row[csf('po_break_down_id')]]['unit_price']; ?></td>
			</tr>
			<?
			$i++;
			}
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_finishing_qnty,2) ?></th>
            <th></th>
            <th><? echo number_format($total_fin_value,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="finish_subcontract")  
{
	extract($_REQUEST);
	$companys=$_SESSION["comp"];
	$work_comp=$_SESSION["work_comp"];
	$comp_expl=explode('_',$company_id);
	$comp_ids_arr=explode(',',$comp_expl[0]);
	$comp_ids="";

	foreach($comp_ids_arr as $comp)
	{
		$comp_ids.=$comp.',';
	}
	$comp_id=chop($comp_ids,',');
	
	//$comp_id=$comp_expl[0];
	$comp_type=$comp_expl[1];
	echo load_html_head_contents("Production Report", "../../../", 1, 1,$unicode,'','');	
	//echo $date;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="100%">
        <thead>
            <tr>
                <th colspan="8"><b>Finishing Sub-Contract(<? echo change_date_format($date);  ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="70px">Po No</th>
                <th width="70px">Buyer</th>
                <th width="70px">Style</th>
                <th width="80px">Finishing Qty</th>
                <th width="50px">Unite Price</th>
                <th >Finishing Ord. Value</th>
           </tr>
        </thead>
    <?
    	//echo $comp;
	    $po_array=array();
	    $query_po_break_down=sql_select("SELECT a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number,b.unit_price,a.total_set_qnty from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0");
	    foreach ($query_po_break_down as $row)
	    {
	    	$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
	    	$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
	    	$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
	    	$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
	    	$po_array[$row[csf('id')]]['unit_price']=$row[csf('unit_price')]/$row[csf('total_set_qnty')];

	    }

		if($companys && !$work_comp)
		{

			

			if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
			if($comp_type=="workingComp") $cbo_company_condition="serving_company like '$comp_id'"; else $cbo_company_condition="company_id in($comp_id)";

			$total_finishing_qnty=0;
			if($db_type==0)
			{
				$finishing_query="SELECT sum(production_quantity) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where $cbo_company_condition $location_con and production_date='$date' and production_type=8 and production_source=3 and status_active=1 and is_deleted=0 group by production_date, po_break_down_id";
			}
			elseif($db_type==2)
			{
				$date2=change_date_format($date,'','',1);
				 $finishing_query="SELECT  sum(production_quantity) as production_quantity, po_break_down_id from pro_garments_production_mst where $cbo_company_condition $location_con and production_date='$date2' and production_type=8 and production_source=3 and status_active=1 and is_deleted=0  group by po_break_down_id";
			}
		//echo $sewing_query;
			//$finishing_query_result=sql_select($finishing_query);
			$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
		}
		else if(!$companys && $work_comp)
		{
 

			if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
		 
			$total_finishing_qnty=0;
			if($db_type==0)
			{
				$finishing_query="SELECT id, sum(production_qnty) as production_quantity, production_date,order_id as  po_break_down_id from subcon_gmts_prod_dtls where company_id in($work_comp) $location_con and production_date='$date' and production_type=4   and status_active=1 and is_deleted=0  group by order_id";
			}
			elseif($db_type==2)
			{
				$date2=change_date_format($date,'','',1);
				$finishing_query="SELECT  sum(production_qnty) as production_quantity, order_id as po_break_down_id from subcon_gmts_prod_dtls where company_id in($work_comp)  $location_con and production_date='$date2' and production_type=4 and status_active=1 and is_deleted=0  group by order_id";
			}
		 
			//$finishing_query_result=sql_select($finishing_query);
			$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
		}
		else
		{ 

			if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
			if($comp_type=="workingComp") $cbo_company_condition="serving_company like '$comp_id'"; else $cbo_company_condition="company_id in($comp_id)";

			$total_finishing_qnty=0;
			if($db_type==0)
			{
				$finishing_query="SELECT id, sum(production_quantity) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where $cbo_company_condition $location_con and production_date='$date' and production_type=8 and production_source=3 and status_active=1 and is_deleted=0 ";
			}
			elseif($db_type==2)
			{
				$date2=change_date_format($date,'','',1);
				 $finishing_query="SELECT  sum(production_quantity) as production_quantity, po_break_down_id from pro_garments_production_mst where $cbo_company_condition $location_con and production_date='$date2' and production_type=8 and production_source=3 and status_active=1 and is_deleted=0  group by po_break_down_id";
			}
		}
        $i=1;
        $finishing_query_result=sql_select($finishing_query);
        foreach ($finishing_query_result as $row)  
        {	if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="70px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="80px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_finishing_qnty+=$row[csf('production_quantity')]; ?></td>
                 <td width="50px"><? echo $po_array[$row[csf('po_break_down_id')]]['unit_price']; ?></td>
                <td width="90px" align="right"><? echo $row[csf('production_quantity')]*$po_array[$row[csf('po_break_down_id')]]['unit_price']; $total_fin_value+=$row[csf('production_quantity')]*$po_array[$row[csf('po_break_down_id')]]['unit_price']; ?></td>
			</tr>
			<?
			$i++;
			}
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_finishing_qnty,2) ?></th>
            <th></th>
            <th><? echo number_format($total_fin_value,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="finish_subcontract_new_btn") 
{
	extract($_REQUEST);
	echo load_html_head_contents("Production Report", "../../../", 1, 1,$unicode,'','');	
	//echo $date;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="100%">
        <thead>
            <tr>
                <th colspan="8"><b>Finishing Sub-Contract (<? echo change_date_format($date);  ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="100px">Po No</th>
                <th width="100px">Buyer</th>
                <th width="120px">Style</th>
                <th width="90px">Finishing Qty</th>
                <th width="90px">Unit</th>
                <th width="90px">Total Finishing value</th>
                
           </tr>
        </thead>
    <?
		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number,b.unit_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0");
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['unit_price']=$row[csf('unit_price')];
		}
		
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
        $total_finishing_qnty=0;
		if($db_type==0)
		{
			$finishing_query="select sum(production_quantity) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where company_id like '$company_id' $location_con and production_date='$date' and production_type=8 and production_source=3 and status_active=1 and is_deleted=0 group by production_date, po_break_down_id";
		}
		elseif($db_type==2)
		{
			$date2=change_date_format($date,'','',1);
			$finishing_query="select  sum(production_quantity) as production_quantity, po_break_down_id from pro_garments_production_mst where company_id like '$company_id' $location_con and production_date='$date2' and production_type=8 and production_source=3 and status_active=1 and is_deleted=0  group by po_break_down_id";
		}
		//echo $sewing_query;
		$finishing_query_result=sql_select($finishing_query);
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
        $i=1;
        foreach ($finishing_query_result as $row)  
        {	if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="100px"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="100px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_finishing_qnty+=$row[csf('production_quantity')]; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['unit_price']; $subtotal_fin_qty+=$row[csf('production_quantity')]*$po_array[$row[csf('po_break_down_id')]]['unit_price']; ?></td>
                <td width="120px"><? echo $subtotal_fin_qty; $total_fin_qty+=$subtotal_fin_qty ?></td>
			</tr>
			<?
			$i++;
			}
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_finishing_qnty,2) ?></th>
            <th></th>
            <th><? echo number_format($total_fin_qty,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="finish") 
{
	extract($_REQUEST);
	$comp_expl=explode('_',$company_id);
	$comp_ids_arr=explode(',',$comp_expl[0]);
	$comp_ids="";
	$companys=$_SESSION["comp"];
	$work_comp=$_SESSION["work_comp"];
	foreach($comp_ids_arr as $comp)
	{
		$comp_ids.=$comp.',';
	}
	$comp_id=chop($comp_ids,',');
	
	//$comp_id=$comp_expl[0];
	$comp_type=$comp_expl[1];
	echo load_html_head_contents("Production Report", "../../../", 1, 1,$unicode,'','');	
	if($companys)
        {
        	$companys_cond=" and company_id in ($companys) ";
        }
        if($work_comp)
        {
        	$serving_company_cond="and serving_company in($work_comp) ";
        }

        
	?>
    <table border="1" class="rpt_table" rules="all" width="720">
        <thead>
            <tr>
                <th colspan="6"><b>Finishing (<? echo change_date_format($date);  ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="100px">Po Number</th>
                <th width="100px">Buyer</th>
                <th width="120px">Style</th>
                <th width="90px">Finishing Qty</th>
           </tr>
        </thead>
    <?
		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0");
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		}
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
		if($comp_type=="workingComp") $cbo_company_condition="serving_company like '$comp_id'"; else $cbo_company_condition="company_id in($comp_id)";

        $total_finishing_qnty=0;
		if($companys && !$work_comp)
        {
        	 
        	if($db_type==0)
        	{
        		$finishing_query="select sum(production_quantity) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where $cbo_company_condition $location_con and production_date='$date' $companys_cond $serving_company_cond  and production_type=8 and status_active=1 and is_deleted=0 group by production_date, po_break_down_id";
        	}
        	else if($db_type==2)
        	{
        		$date2=change_date_format($date,'','',1);
        		$finishing_query="select sum(production_quantity) as production_quantity, po_break_down_id from pro_garments_production_mst where $cbo_company_condition $location_con and production_date='$date2'  $companys_cond $serving_company_cond and production_type=8 and status_active=1 and is_deleted=0  group by po_break_down_id";
        	}

        }
        else if(!$companys && $work_comp)
        {

        	if($db_type==0)
        	{
        		$finishing_query="select sum(production_quantity) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where $cbo_company_condition $location_con and production_date='$date' $companys_cond $serving_company_cond and production_source=1 and production_type=8 and status_active=1 and is_deleted=0 group by production_date, po_break_down_id";
        	}
        	else if($db_type==2)
        	{
        		$date2=change_date_format($date,'','',1);
        		$finishing_query="select sum(production_quantity) as production_quantity, po_break_down_id from pro_garments_production_mst where $cbo_company_condition $location_con and production_date='$date2' $companys_cond $serving_company_cond and production_type=8 and production_source=1 and status_active=1 and is_deleted=0  group by po_break_down_id";
        	}

        	$po_array2=array();
			$query_po_break_down2=sql_select("SELECT a.job_no_prefix_num, a.party_id as buyer_name, b.cust_style_ref as style_ref_no, b.id, b.order_no as po_number,b.rate as unit_price from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0");
			foreach ($query_po_break_down2 as $row)
			{
				$po_array2[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
				$po_array2[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
				$po_array2[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
				$po_array2[$row[csf('id')]]['po']=$row[csf('po_number')];
				$po_array2[$row[csf('id')]]['unit_price']=$row[csf('unit_price')];

			}

			if($db_type==0)
			{
				$finishing_query2="SELECT sum(production_qnty) as production_quantity, production_date,order_id as  po_break_down_id from subcon_gmts_prod_dtls where company_id in($work_comp) $location_con and production_date='$date' and production_type=4   and status_active=1 and is_deleted=0  group by order_id";
			}
			elseif($db_type==2)
			{
				$date2=change_date_format($date,'','',1);
				$finishing_query2="SELECT  sum(production_qnty) as production_quantity, order_id as po_break_down_id from subcon_gmts_prod_dtls where company_id in($work_comp)  $location_con and production_date='$date2' and production_type=4 and status_active=1 and is_deleted=0  group by order_id";
			}
		 
			$finishing_query_result2=sql_select($finishing_query2);



        }
        else
        {


        	if($db_type==0)
        	{
        		$finishing_query="select sum(production_quantity) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where $cbo_company_condition $location_con and production_date='$date' $companys_cond $serving_company_cond and production_source=1 and production_type=8 and status_active=1 and is_deleted=0 group by production_date, po_break_down_id";
        	}
        	else if($db_type==2)
        	{
        		$date2=change_date_format($date,'','',1);
        		$finishing_query="select sum(production_quantity) as production_quantity, po_break_down_id from pro_garments_production_mst where $cbo_company_condition $location_con and production_date='$date2' and production_type=8 $companys_cond $serving_company_cond and production_source=1 and status_active=1 and is_deleted=0  group by po_break_down_id";
        	}
        }
		//echo $sewing_query;
		$finishing_query_result=sql_select($finishing_query);
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
        $i=1;
        foreach ($finishing_query_result as $row)  
        {
			if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="100px"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="100px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_finishing_qnty+=$row[csf('production_quantity')]; ?></td>
			</tr>
			<?
			$i++;
			}
        }

        foreach ($finishing_query_result2 as $row)  
        {
			if($po_array2[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array2[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="100px"><? echo $po_array2[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="100px"><? echo $buyerArr[$po_array2[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array2[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_finishing_qnty+=$row[csf('production_quantity')]; ?></td>
			</tr>
			<?
			$i++;
			}
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_finishing_qnty,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="carton")
{
	extract($_REQUEST);
	$comp_expl=explode('_',$company_id);
	$comp_ids_arr=explode(',',$comp_expl[0]);
	$comp_ids="";
	foreach($comp_ids_arr as $comp)
	{
		$comp_ids.=$comp.',';
	}
	$comp_id=chop($comp_ids,',');
	
	//$comp_id=$comp_expl[0];
	$comp_type=$comp_expl[1];
	echo load_html_head_contents("Production Report", "../../../", 1, 1,$unicode,'','');	
	//echo $date;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="720px">
        <thead>
            <tr>
                <th colspan="6"><b>Carton (<? echo change_date_format($date);  ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="100px">Po Number</th>
                <th width="100px">Buyer</th>
                <th width="120px">Style</th>
                <th width="90px">Carton Qnty</th>
           </tr>
        </thead>
    <?
		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0");
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		}
		
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
		if($comp_type=="workingComp") $cbo_company_condition="serving_company like '$comp_id'"; else $cbo_company_condition="company_id in($comp_id)";

        $total_carton_qnty=0;
		if($db_type==0)
		{
			$carton_query="select sum(carton_qty) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where $cbo_company_condition  $location_con and production_date='$date' and production_type=8 and status_active=1 and is_deleted=0 group by production_date, po_break_down_id";
		}
		elseif($db_type==2)
		{
			$date2=change_date_format($date,'','',1);
			$carton_query="select  sum(carton_qty) as production_quantity, po_break_down_id from pro_garments_production_mst where $cbo_company_condition $location_con and production_date='$date2' and production_type=8 and status_active=1 and is_deleted=0 group by po_break_down_id";
		}
		//echo $carton_query;
		$carton_query_result=sql_select($carton_query);
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
        $i=1;
        foreach ($carton_query_result as $row)  
        {
			if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="100px"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="100px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_carton_qnty+=$row[csf('production_quantity')]; ?></td>
               
			</tr>
			<?
			$i++;
			}
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_carton_qnty,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="ex_factory_qty_popup")  
{
	extract($_REQUEST);
	$comp_expl=explode('_',$company_id);
	$comp_ids_arr=explode(',',str_replace("'","",  $comp_expl[0]));
	$comp_ids="";
	foreach($comp_ids_arr as $comp)
	{
		$comp_ids.=$comp.',';
	}
	$comp_id=chop($comp_ids,',');
	$po_id=chop($po_id,',');
	$location_session=$_SESSION["location_session"];
	$location_session=str_replace("d.", "b.", $location_session);
	//echo "aa $location_session";;
	//$comp_id=$comp_expl[0];
	$comp_type=$comp_expl[1];
	echo load_html_head_contents("Ex-Factory Qty Popup", "../../../", 1, 1,$unicode,'','');	
	//echo $company_id;//company_id
	$location_cond = "";
	if($location_id !=0){$location_cond = " and a.location=$location_id";}

	$po_id_cond = "";
	if($po_id !=""){$po_id_cond = " and b.id in($po_id)";}
	
	?>
    <table border="1" class="rpt_table" rules="all" width="720px">
        <thead>
            <tr>
                <th colspan="8"><b>Ex-Factory Date (<? echo change_date_format($date); ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="120px">Buyer</th>
                <th width="120px">Style</th>
                <th width="120px">Po Number</th>
                <th width="100px">Ex-Factory Qty</th>
                <th width="100px">Return Qty</th>
                <th>Ex-Factory Value</th>
           </tr>
        </thead>
    <?
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
		
		$po_array=array();
		$query_po_break_down=sql_select("SELECT a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.total_set_qnty, b.id, b.po_number, b.unit_price from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0 $po_id_cond");
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['unit_price']=$row[csf('unit_price')];
			$po_array[$row[csf('id')]]['set_qnty']=$row[csf('total_set_qnty')];
		}
		
		if($db_type==0)
		{
			$date_cond=$date;
		}
		elseif($db_type==2)
		{
			$date_cond=change_date_format($date,'','',1);
		}
		
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and a.location=$location_id";}
		if($comp_type=="workingComp") $cbo_company_condition="and b.delivery_company_id in($comp_id)"; else $cbo_company_condition="and b.company_id in($comp_id)";
		 

		if($comp_type=="workingComp" && str_replace("'","",$location_id)>0) 
			{
				$location_con="";
				$cbo_delivery_loc_cond="and b.delivery_location_id='$location_id'";
			} 
			else
			{
				$cbo_delivery_loc_cond="";
			} 


		$po_id_cond = str_replace("b.id", "a.po_break_down_id", $po_id_cond);
	   	$exfactory_query="SELECT a.ex_factory_date, a.po_break_down_id, 
		sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN a.entry_form=85 THEN a.ex_factory_qnty ELSE 0 END) as return_qnty 
		 from pro_ex_factory_mst a,pro_ex_factory_delivery_mst b where a.delivery_mst_id=b.id and a.ex_factory_date='$date_cond' $location_con  $cbo_company_condition   $location_session $po_id_cond and a.status_active=1 and a.is_deleted=0 group by a.ex_factory_date, a.po_break_down_id"; //and b.delivery_location_id=$location_id
		 // echo $exfactory_query; 

		$exfactory_query_result=sql_select($exfactory_query); $i=1; //$total_exfactory_qnty=0;
        foreach ($exfactory_query_result as $row)  
        {
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$ex_factory_val=($row[csf("ex_factory_qnty")]-$row[csf("return_qnty")])*($po_array[$row[csf("po_break_down_id")]]['unit_price']/$po_array[$row[csf("po_break_down_id")]]['set_qnty']);
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="120px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['po'];; ?></td>
                <td width="100px" align="right"><? echo number_format($row[csf('ex_factory_qnty')],2); $total_ex_factory_qnty+=$row[csf('ex_factory_qnty')]; ?></td>
                  <td width="100px" align="right"><? echo number_format($row[csf('return_qnty')],2); $total_return_ex_factory_qnty+=$row[csf('return_qnty')]; ?></td>
                <td align="right"><? echo number_format($ex_factory_val,2); $total_exfactory_value+=$ex_factory_val; ?></td>
			</tr>
			<?
			$i++;
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th><? echo number_format($total_ex_factory_qnty,2) ?></th>
             <th><? echo number_format($total_return_ex_factory_qnty,2) ?></th>
            <th><? echo number_format($total_exfactory_value,2) ?></th>
           
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="cm_value_popup") 
{
	extract($_REQUEST);
	//echo "string $sewing_source";
	$comp_expl=explode('_',$company_id);
	$comp_ids_arr=explode(',',str_replace("'", "", $comp_expl[0]));
	$comp_ids="";
	foreach($comp_ids_arr as $comp)
	{
		$comp_ids.=$comp.',';
	}
	$comp_id=chop($comp_ids,',');
	
	//$comp_id=$comp_expl[0];
	$comp_type=$comp_expl[1];


	$companys=$_SESSION["comp"];
	$work_comp=$_SESSION["work_comp"];


	if($companys)
	{
		$companys_cond=" and company_id in ($companys) ";
	}
	if($work_comp)
	{
		$serving_company_cond="and serving_company in($work_comp) ";
	}

	echo load_html_head_contents("CM Value Popup", "../../../", 1, 1,$unicode,'','');	
	//echo $date;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="720px">
        <thead>
            <tr>
                <th colspan="8"><b>CM Value Popup (<? echo change_date_format($date); ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="110px">Buyer</th>
                <th width="110px">Style</th>
                <th width="110px">Po Number</th>
                <th width="90px">Sewing Qty</th>
                <th width="90px">CM Per Pcs</th>
                <th>CM Value</th>
           </tr>
        </thead>
    <?
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
		$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per"); 
		//$tot_cost_arr = return_library_array("select job_no, cm_for_sipment_sche from wo_pre_cost_dtls","job_no","cm_for_sipment_sche"); 
		$tot_cost_arr = return_library_array("select job_no, cm_cost from wo_pre_cost_dtls","job_no","cm_cost"); 
		//$tot_cost_arr = return_library_array("select job_no, cm_for_sipment_sche from wo_pre_cost_dtls","job_no","cm_for_sipment_sche"); 
		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.total_set_qnty, b.id, b.po_number, b.unit_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0");
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['unit_price']=$row[csf('unit_price')];
			$po_array[$row[csf('id')]]['set_qnty']=$row[csf('total_set_qnty')];
		}
		
		if($db_type==0)
		{
			$date_cond=$date;
		}
		elseif($db_type==2)
		{
			$date_cond=change_date_format($date,'','',1);
		}
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
		if($comp_type=="workingComp") $cbo_company_condition="serving_company in($comp_id)"; else $cbo_company_condition="company_id in($comp_id)";
		if($sewing_source) $sewing_source_cond=" and  production_source=$sewing_source";

		$cm_query="SELECT production_date, po_break_down_id, sum(production_quantity) AS sewing_qnty from pro_garments_production_mst where $cbo_company_condition $location_con and production_date='$date_cond' $companys_cond $serving_company_cond and production_type =5 and is_deleted=0 and status_active=1 $sewing_source_cond group by production_date, po_break_down_id";

		$cm_query_result=sql_select($cm_query); $i=1; //$total_exfactory_qnty=0;
        foreach ($cm_query_result as $row)  
        {
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$cm_val=$row[csf("sewing_qnty")]*($po_array[$row[csf("po_break_down_id")]]['unit_price']/$po_array[$row[csf("po_break_down_id")]]['set_qnty']);
			
			$costing_per=$costing_per_arr[$po_array[$row[csf('po_break_down_id')]]['job_no']];
			if($costing_per==1) $dzn_qnty=12;
			else if($costing_per==3) $dzn_qnty=12*2;
			else if($costing_per==4) $dzn_qnty=12*3;
			else if($costing_per==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;
						
			$dzn_qnty=$dzn_qnty*$po_array[$row[csf("po_break_down_id")]]['set_qnty'];
			$cm_per_pcs=$tot_cost_arr[$po_array[$row[csf('po_break_down_id')]]['job_no']]/$dzn_qnty;
			$cm_value=($tot_cost_arr[$po_array[$row[csf('po_break_down_id')]]['job_no']]/$dzn_qnty)*$row[csf('sewing_qnty')];
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="110px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="110px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="110px"><? echo $po_array[$row[csf('po_break_down_id')]]['po'];; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('sewing_qnty')],2); $total_cm_qnty+=$row[csf('sewing_qnty')]; ?></td>
                <td width="90px" align="right"><? echo number_format($cm_per_pcs,2); $total_cm_per_pcs+=$cm_per_pcs; ?></td>
                <td align="right"><? echo number_format($cm_value,2); $total_cm_value+=$cm_value; ?></td>
			</tr>
			<?
			$i++;
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th><? echo number_format($total_cm_qnty,2) ?></th>
            <th><? //echo number_format($total_cm_per_pcs,2) ?></th>
            <th><? echo number_format($total_cm_value,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="ex_cm_value_popup") 
{
	extract($_REQUEST);
	//echo "string $sewing_source";
	$comp_expl=explode('_',$company_id);
	$comp_ids_arr=explode(',',str_replace("'", "", $comp_expl[0]));
	$comp_ids="";
	foreach($comp_ids_arr as $comp)
	{
		$comp_ids.=$comp.',';
	}
	$comp_id=chop($comp_ids,',');
	$po_id=chop($po_id,',');
	
	//$comp_id=$comp_expl[0];
	$comp_type=$comp_expl[1];


	$companys=$_SESSION["comp"];
	$work_comp=$_SESSION["work_comp"];


	if($companys)
	{
		$companys_cond=" and company_id in ($companys) ";
	}
	if($work_comp)
	{
		$serving_company_cond="and serving_company in($work_comp) ";
	}

	$po_id_cond = "";
	if($po_id !=""){$po_id_cond = " and b.id in($po_id)";}

	echo load_html_head_contents("CM Value Popup", "../../../", 1, 1,$unicode,'','');	
	//echo $date;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="720px">
        <thead>
            <tr>
                <th colspan="8"><b>Ex-fact CM Value Popup (<? echo change_date_format($date); ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="110px">Buyer</th>
                <th width="110px">Style</th>
                <th width="110px">Po Number</th>
                <th width="90px">Ex-fact Qty</th>
                <th width="90px">CM Per Pcs</th>
                <th>CM Value</th>
           </tr>
        </thead>
    <?
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
		$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per"); 
		//$tot_cost_arr = return_library_array("select job_no, cm_for_sipment_sche from wo_pre_cost_dtls","job_no","cm_for_sipment_sche"); 
		$tot_cost_arr = return_library_array("select job_no, cm_cost from wo_pre_cost_dtls","job_no","cm_cost"); 
		//$tot_cost_arr = return_library_array("select job_no, cm_for_sipment_sche from wo_pre_cost_dtls","job_no","cm_for_sipment_sche"); 
		$po_array=array();
		$query_po_break_down=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.total_set_qnty, b.id, b.po_number, b.unit_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0 $po_id_cond");
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['unit_price']=$row[csf('unit_price')];
			$po_array[$row[csf('id')]]['set_qnty']=$row[csf('total_set_qnty')];
		}
		
		if($db_type==0)
		{
			$date_cond=$date;
		}
		elseif($db_type==2)
		{
			$date_cond=change_date_format($date,'','',1);
		}
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
		if($comp_type=="workingComp") $cbo_company_condition="serving_company in($comp_id)"; else $cbo_company_condition="company_id in($comp_id)";
		if($sewing_source) $sewing_source_cond=" and  production_source=$sewing_source";
		$po_id_cond = str_replace("b.id", "a.po_break_down_id", $po_id_cond);
		$cm_query="SELECT a.ex_factory_date, a.po_break_down_id, SUM (CASE WHEN a.entry_form != 85 THEN a.ex_factory_qnty ELSE 0 END)-SUM (CASE WHEN a.entry_form = 85 THEN a.ex_factory_qnty ELSE 0 END) AS exfact_qnty from pro_ex_factory_mst a,pro_ex_factory_delivery_mst b where b.id=a.delivery_mst_id and a.ex_factory_date='$date_cond' and a.is_deleted=0 and a.status_active=1 $po_id_cond group by a.ex_factory_date, a.po_break_down_id";
		// echo $cm_query;
		$cm_query_result=sql_select($cm_query); $i=1; //$total_exfactory_qnty=0;
        foreach ($cm_query_result as $row)  
        {
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$cm_val=$row[csf("exfact_qnty")]*($po_array[$row[csf("po_break_down_id")]]['unit_price']/$po_array[$row[csf("po_break_down_id")]]['set_qnty']);
			
			$costing_per=$costing_per_arr[$po_array[$row[csf('po_break_down_id')]]['job_no']];
			if($costing_per==1) $dzn_qnty=12;
			else if($costing_per==3) $dzn_qnty=12*2;
			else if($costing_per==4) $dzn_qnty=12*3;
			else if($costing_per==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;
						
			$dzn_qnty=$dzn_qnty*$po_array[$row[csf("po_break_down_id")]]['set_qnty'];
			$cm_per_pcs=$tot_cost_arr[$po_array[$row[csf('po_break_down_id')]]['job_no']]/$dzn_qnty;
			$cm_value=($tot_cost_arr[$po_array[$row[csf('po_break_down_id')]]['job_no']]/$dzn_qnty)*$row[csf('exfact_qnty')];
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="110px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="110px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="110px"><? echo $po_array[$row[csf('po_break_down_id')]]['po'];; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('exfact_qnty')],2); $total_cm_qnty+=$row[csf('exfact_qnty')]; ?></td>
                <td width="90px" align="right"><? echo number_format($cm_per_pcs,2); $total_cm_per_pcs+=$cm_per_pcs; ?></td>
                <td align="right"><? echo number_format($cm_value,2); $total_cm_value+=$cm_value; ?></td>
			</tr>
			<?
			$i++;
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th><? echo number_format($total_cm_qnty,2) ?></th>
            <th><? //echo number_format($total_cm_per_pcs,2) ?></th>
            <th><? echo number_format($total_cm_value,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="cm_value_finish_popup") 
{
	extract($_REQUEST);
	$comp_expl=explode('_',$company_id);
	$comp_ids_arr=explode(',',$comp_expl[0]);
	$comp_ids="";
	foreach($comp_ids_arr as $comp)
	{
		$comp_ids.=$comp.',';
	}
	$comp_id=chop($comp_ids,',');
	
	//$comp_id=$comp_expl[0];
	$comp_type=$comp_expl[1];
	$source = ($sewing_source !="") ? " and production_source=$sewing_source" : "";
	echo load_html_head_contents("CM Value Popup", "../../../", 1, 1,$unicode,'','');	
	//echo $date;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="720px">
        <thead>
            <tr>
                <th colspan="8"><b>CM Value Popup (<? echo change_date_format($date); ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="110px">Buyer</th>
                <th width="110px">Style</th>
                <th width="110px">Po Number</th>
                <th width="90px">Sewing Qty</th>
                <th width="90px">CM Per Pcs</th>
                <th>CM Value</th>
           </tr>
        </thead>
    <?
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
		$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per"); 
		$tot_cost_arr = return_library_array("select job_no, cm_cost from wo_pre_cost_dtls","job_no","cm_cost"); 
		
		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.total_set_qnty, b.id, b.po_number, b.unit_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0");
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['unit_price']=$row[csf('unit_price')];
			$po_array[$row[csf('id')]]['set_qnty']=$row[csf('total_set_qnty')];
		}
		
		if($db_type==0)
		{
			$date_cond=$date;
		}
		elseif($db_type==2)
		{
			$date_cond=change_date_format($date,'','',1);
		}
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
		if($comp_type=="workingComp") $cbo_company_condition="serving_company='$comp_id'"; else $cbo_company_condition="company_id in($comp_id)";

		$cm_query="SELECT production_date, po_break_down_id, sum(production_quantity) AS finishing_qnty from pro_garments_production_mst where $cbo_company_condition $location_con and production_date='$date_cond' and production_type=8 and is_deleted=0 and status_active=1 $source group by production_date, po_break_down_id";

		$cm_query_result=sql_select($cm_query); $i=1; //$total_exfactory_qnty=0;
        foreach ($cm_query_result as $row)  
        {
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$cm_val=$row[csf("finishing_qnty")]*($po_array[$row[csf("po_break_down_id")]]['unit_price']/$po_array[$row[csf("po_break_down_id")]]['set_qnty']);
			
			$costing_per=$costing_per_arr[$po_array[$row[csf('po_break_down_id')]]['job_no']];
			if($costing_per==1) $dzn_qnty=12;
			else if($costing_per==3) $dzn_qnty=12*2;
			else if($costing_per==4) $dzn_qnty=12*3;
			else if($costing_per==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;
						
			$dzn_qnty=$dzn_qnty*$po_array[$row[csf("po_break_down_id")]]['set_qnty'];
			$cm_per_pcs=$tot_cost_arr[$po_array[$row[csf('po_break_down_id')]]['job_no']]/$dzn_qnty;
			$cm_value=($tot_cost_arr[$po_array[$row[csf('po_break_down_id')]]['job_no']]/$dzn_qnty)*$row[csf('finishing_qnty')];
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="110px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="110px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="110px"><? echo $po_array[$row[csf('po_break_down_id')]]['po'];; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('finishing_qnty')],2); $total_cm_qnty+=$row[csf('finishing_qnty')]; ?></td>
                <td width="90px" align="right"><? echo number_format($cm_per_pcs,2); $total_cm_per_pcs+=$cm_per_pcs; ?></td>
                <td align="right"><? echo number_format($cm_value,2); $total_cm_value+=$cm_value; ?></td>
			</tr>
			<?
			$i++;
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th><? echo number_format($total_cm_qnty,2) ?></th>
            <th><? //echo number_format($total_cm_per_pcs,2) ?></th>
            <th><? echo number_format($total_cm_value,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="ord_sew_inhouse") 
{
	extract($_REQUEST);
	$comp_expl=explode('_',$company_id);
	$comp_ids_arr=explode(',',$comp_expl[0]);
	$comp_ids="";
	foreach($comp_ids_arr as $comp)
	{
		$comp_ids.=$comp.',';
	}
	$comp_id=chop($comp_ids,',');
	
	//$comp_id=$comp_expl[0];
	$comp_type=$comp_expl[1];
	echo load_html_head_contents("Inhouse Order Value (On Sewing Qty) Popup", "../../../", 1, 1,$unicode,'','');	
	//echo $date;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="720px">
        <thead>
            <tr>
                <th colspan="8"><b>Inhouse Order Value (On Sewing Qty) Popup (<? echo change_date_format($date); ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="110px">Buyer</th>
                <th width="110px">Style</th>
                <th width="110px">Po Number</th>
                <th width="90px">Sewing Qty</th>
                <th width="90px">Unite Price</th>
                <th>Sewing Ord. Value</th>
           </tr>
        </thead>
    <?
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
		$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per"); 
		$tot_cost_arr = return_library_array("select job_no, cm_cost from wo_pre_cost_dtls","job_no","cm_cost"); 
		
		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.total_set_qnty, b.id, b.po_number,(b.unit_price/a.total_set_qnty) as unit_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0");
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['unit_price']=number_format($row[csf('unit_price')],2);
			$po_array[$row[csf('id')]]['set_qnty']=$row[csf('total_set_qnty')];
		}
		
		if($db_type==0)
		{
			$date_cond=$date;
		}
		elseif($db_type==2)
		{
			$date_cond=change_date_format($date,'','',1);
		}
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
		if($comp_type=="workingComp") $cbo_company_condition="serving_company like '$comp_id'"; else $cbo_company_condition="company_id in($comp_id)";

		$ord_sew_inhouse_sql="SELECT production_date, po_break_down_id, sum(production_quantity) AS sewing_qnty from pro_garments_production_mst where $cbo_company_condition  $location_con and production_date='$date_cond' and is_deleted=0 and production_type=5 and production_source=1 and status_active=1 group by production_date, po_break_down_id order by production_date asc";
			
		$ord_sew_inhouse_sql_result=sql_select($ord_sew_inhouse_sql); $i=1; //$total_exfactory_qnty=0;
        foreach ($ord_sew_inhouse_sql_result as $row)  
        {
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$ord_sew_val=$row[csf("sewing_qnty")]*$po_array[$row[csf("po_break_down_id")]]['unit_price'];
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="110px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="110px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="110px"><? echo $po_array[$row[csf('po_break_down_id')]]['po'];; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf("sewing_qnty")],2); $total_sewing_qnty+=$row[csf("sewing_qnty")]; ?></td>
                <td width="90px" align="right"><? echo number_format($po_array[$row[csf("po_break_down_id")]]['unit_price'],2); $total_unit_price+=$po_array[$row[csf("po_break_down_id")]]['unit_price']; ?></td>
                <td align="right"><? echo number_format($ord_sew_val,2); $total_ord_sew_val+=$ord_sew_val; ?></td>
			</tr>
			<?
			$i++;
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th><? echo number_format($total_sewing_qnty,2) ?></th>
            <th><? //echo number_format($total_cm_per_pcs,2) ?></th>
            <th><? echo number_format($total_ord_sew_val,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="smv_sewing_popup") 
{
	extract($_REQUEST);
	$comp_expl=explode('_',$company_id);
	$comp_ids_arr=explode(',',str_replace("'", "", $comp_expl[0]));
	$comp_ids="";
	foreach($comp_ids_arr as $comp)
	{
		$comp_ids.=$comp.',';
	}
	$comp_id=chop($comp_ids,',');
	
	//$comp_id=$comp_expl[0];
	$comp_type=$comp_expl[1];
	echo load_html_head_contents("SMV (On Sewing Qty) Popup", "../../../", 1, 1,$unicode,'','');	
	//echo $sewing_source;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="720px">
        <thead>
            <tr>
                <th colspan="10"><b>SMV (On Sewing Qty) Popup (<? echo change_date_format($date); ?>)</b></th>
            </tr>
            <tr>
            	<th width="30">SL</th>
                <th width="60">Job</th>
                <th width="50">SMV</th>
                <th width="110">Buyer</th>
                <th width="110">Style</th>
                <th width="100">Item</th>
                <th width="110">Po Number</th>
                <th width="90">Sewing Qty</th>
                <th width="90">SAH Produced</th>
                <th>Efficiency</th>
           </tr>
        </thead>
    <?
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
		
		$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($comp_id) and variable_list=25 and status_active=1 and is_deleted=0");
		//echo $smv_source;die;
		if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
		$item_smv_array=array();
		//$smv_source=2;
		if($smv_source==3)
		{
			$sql_item="select b.id, a.sam_style, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,3)";
			$resultItem=sql_select($sql_item);
			foreach($resultItem as $itemData)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
			}
		}
		else
		{
			if($comp_type=="workingComp") $cbo_company_condition=""; else $cbo_company_condition="and a.company_name in($comp_id)";
			$sql_item="select b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no $cbo_company_condition and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,3)";
			$resultItem=sql_select($sql_item);
			foreach($resultItem as $itemData)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]['smv_pcs']=$itemData[csf('smv_pcs')];
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]['smv_pcs_precost']=$itemData[csf('smv_pcs_precost')];
			}
		}
			
		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no,a.set_smv, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.total_set_qnty, b.id, b.po_number, b.unit_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0");
		
		
		
		
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$po_array[$row[csf('id')]]['set_smv']=$row[csf('set_smv')];
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['unit_price']=$row[csf('unit_price')];
			$po_array[$row[csf('id')]]['set_qnty']=$row[csf('total_set_qnty')];
		}
		
		if($db_type==0) { $date_cond=$date; } elseif($db_type==2) { $date_cond=change_date_format($date,'','',1); }
		if($comp_type=="workingComp") $cbo_company_condition=""; else $cbo_company_condition="and b.company_id in($comp_id)";
		if(str_replace("'","",$location_id)==0){$location_con_smv="";}else{$location_con_smv=" and b.location_id=$location_id";}
		$smv_array=array();
		$tpd_data_arr=sql_select( "select a.pr_date, sum(a.target_per_hour*a.working_hour) tpd, sum(a.man_power*a.working_hour) tsmv from prod_resource_dtls a, prod_resource_mst b where b.id=a.mst_id $cbo_company_condition $location_con_smv and a.pr_date='$date_cond' and a.is_deleted=0 and b.is_deleted=0 group by  a.pr_date");
        foreach($tpd_data_arr as $row)
        {
			$production_date=date("Y-m-d", strtotime($row[csf('pr_date')])); 
           // $tpdArr[$production_date]+=$row[csf('tpd')];
			$smv_array[change_date_format($production_date)]['smv']=$row[csf('tsmv')]*60;
        }
		//var_dump ($smv_array);
		
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
		if($comp_type=="workingComp") $cbo_company_condition="serving_company in($comp_id)"; else $cbo_company_condition="company_id in($comp_id)";

		 $ord_sew_inhouse_sql="SELECT production_date, po_break_down_id, item_number_id, sum(production_quantity) AS sewing_qnty from pro_garments_production_mst where  $cbo_company_condition $location_con and production_date='$date_cond' and is_deleted=0 and production_type=5 and production_source=$sewing_source and status_active=1 group by production_date, po_break_down_id, item_number_id order by production_date asc";
					
		$ord_sew_inhouse_sql_result=sql_select($ord_sew_inhouse_sql); $i=1;
        foreach ($ord_sew_inhouse_sql_result as $row)  
        {
			if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$production_date=change_date_format(date("Y-m-d", strtotime($row[csf('production_date')]))); 
			$item_smv=0;
			if($smv_source==2)
			{
				$item_smv=$item_smv_array[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]['smv_pcs_precost'];
			}
			else if($smv_source==3)
			{
				$item_smv=$item_smv_array[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]];	
			}
			else
			{
				$item_smv=$item_smv_array[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]['smv_pcs'];	
			}
			//$ord_sew_smv=$row[csf("sewing_qnty")]*$item_smv;
			//$ord_sew_smv=($row[csf("sewing_qnty")]*$po_array[$row[csf('po_break_down_id')]]['set_smv'])/60;
			$ord_sew_smv=($row[csf("sewing_qnty")]*$item_smv_array[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]]['smv_pcs'])/60;
			//$effiecy_aff_perc=$ord_sew_smv/$smv_array[$production_date]['smv']*100;
			$effiecy_aff_perc=$ord_sew_smv/($smv_array[$production_date]['smv']/60)*100;
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30"><? echo $i; ?></td>
                <td width="60"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="50" align="right"><? echo $item_smv_array[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]]['smv_pcs']; ?></td>
                <td width="110"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="110"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="100"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
                <td width="110"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="90" align="right"><? echo number_format($row[csf("sewing_qnty")],2); $total_sewing_qnty+=$row[csf("sewing_qnty")]; ?></td>
                <td width="90" align="right"><? echo number_format($ord_sew_smv,2); $total_ord_sew_smv+=$ord_sew_smv; ?></td>
                <td align="right"><? echo number_format($effiecy_aff_perc,2); $total_effiecy_aff_perc+=$effiecy_aff_perc; ?></td>
			</tr>
			<?
			$i++; 
			}
        }
        ?>
        <tfoot>
            <th colspan="7">Total</th>
            <th><? echo number_format($total_sewing_qnty,2); ?></th>
            <th><? echo number_format($total_ord_sew_smv,2); ?></th>
            <th><? echo number_format($total_effiecy_aff_perc,2); ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="smv_finishing_popup") 
{
	extract($_REQUEST);
	$comp_expl=explode('_',$company_id);
	$comp_ids_arr=explode(',',$comp_expl[0]);
	$comp_ids="";
	foreach($comp_ids_arr as $comp)
	{
		$comp_ids.=$comp.',';
	}
	$comp_id=chop($comp_ids,',');
	
	//$comp_id=$comp_expl[0];
	$comp_type=$comp_expl[1];
	echo load_html_head_contents("SMV (On Finishing Qty) Popup", "../../../", 1, 1,$unicode,'','');	
	//echo $sewing_source;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="797px">
        <thead>
            <tr>
                <th colspan="10"><b>SMV (On Finishing Qty) Popup (<? echo change_date_format($date); ?>)</b></th>
            </tr>
            <tr>
            	<th width="30">SL</th>
                <th width="60">Job</th>
                <th width="50">SMV</th>
                <th width="110">Buyer</th>
                <th width="110">Style</th>
                <th width="100">Item</th>
                <th width="110">Po Number</th>
                <th width="90">Finishing Qty</th>
                <th width="90">SAH Produced</th>
                <th>Efficiency</th>
           </tr>
        </thead>
    <?
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
		if($comp_type=="workingComp") $cbo_company_cond_1=""; else $cbo_company_cond_1="and company_name in($comp_id)";
		$smv_source=return_field_value("smv_source","variable_settings_production","variable_list=25 $cbo_company_cond_1 and status_active=1 and is_deleted=0");
		//echo $smv_source;die;
		if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
		$item_smv_array=array();
		//$smv_source=2;
		if($smv_source==3)
		{
			$sql_item="select b.id, a.sam_style, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,3)";
			$resultItem=sql_select($sql_item);
			foreach($resultItem as $itemData)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
			}
		}
		else
		{
			
			if($comp_type=="mainComp") $cbo_company_condition="and a.company_name in($comp_id)"; else $cbo_company_condition="";
			$sql_item="select b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no $cbo_company_condition and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,3)";
			$resultItem=sql_select($sql_item);
			foreach($resultItem as $itemData)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]['smv_pcs']=$itemData[csf('smv_pcs')];
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]['smv_pcs_precost']=$itemData[csf('smv_pcs_precost')];
			}
		}
			
		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no,a.set_smv, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.total_set_qnty, b.id, b.po_number, b.unit_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0");
		
		
		
		
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$po_array[$row[csf('id')]]['set_smv']=$row[csf('set_smv')];
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['unit_price']=$row[csf('unit_price')];
			$po_array[$row[csf('id')]]['set_qnty']=$row[csf('total_set_qnty')];
		}
		
		if($db_type==0) { $date_cond=$date; } elseif($db_type==2) { $date_cond=change_date_format($date,'','',1); }
		
		if($comp_type=="mainComp") $cbo_company_condition="and b.company_id in($comp_id)"; else $cbo_company_condition="";

		$smv_array=array();
		$tpd_data_arr=sql_select( "select a.pr_date, sum(a.target_per_hour*a.working_hour) tpd, sum(a.man_power*a.working_hour) tsmv from prod_resource_dtls a, prod_resource_mst b where b.id=a.mst_id and b.company_id='$company_id' and a.pr_date='$date_cond' and a.is_deleted=0 and b.is_deleted=0 group by  a.pr_date");
        foreach($tpd_data_arr as $row)
        {
			$production_date=date("Y-m-d", strtotime($row[csf('pr_date')])); 
           // $tpdArr[$production_date]+=$row[csf('tpd')];
			$smv_array[change_date_format($production_date)]['smv']=$row[csf('tsmv')]*60;
        }
		//var_dump ($smv_array);
		
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
		if($comp_type=="workingComp") $cbo_company_condition="serving_company='$comp_id'"; else $cbo_company_condition="company_id in($comp_id)";
		$ord_sew_inhouse_sql="SELECT production_date, po_break_down_id, item_number_id, sum(production_quantity) AS finishing_qnty from pro_garments_production_mst where   $cbo_company_condition $location_con and production_date='$date_cond' and is_deleted=0 and production_type=8 and production_source=$sewing_source and status_active=1 group by production_date, po_break_down_id, item_number_id order by production_date asc";
					
		$ord_sew_inhouse_sql_result=sql_select($ord_sew_inhouse_sql); $i=1;
        foreach ($ord_sew_inhouse_sql_result as $row)  
        {
			if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$production_date=change_date_format(date("Y-m-d", strtotime($row[csf('production_date')]))); 
			$item_smv=0;
			if($smv_source==2)
			{
				$item_smv=$item_smv_array[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]['smv_pcs_precost'];
			}
			else if($smv_source==3)
			{
				$item_smv=$item_smv_array[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]];	
			}
			else
			{
				$item_smv=$item_smv_array[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]['smv_pcs'];	
			}
			//$ord_sew_smv=$row[csf("sewing_qnty")]*$item_smv;
			//$ord_sew_smv=($row[csf("sewing_qnty")]*$po_array[$row[csf('po_break_down_id')]]['set_smv'])/60;
			$ord_sew_smv=($row[csf("finishing_qnty")]*$item_smv_array[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]]['smv_pcs'])/60;
			//$effiecy_aff_perc=$ord_sew_smv/$smv_array[$production_date]['smv']*100;
			$effiecy_aff_perc=$smv_array[$production_date]['smv']/60 !=0 ? $ord_sew_smv/($smv_array[$production_date]['smv']/60)*100 : 0;
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30"><? echo $i; ?></td>
                <td width="60"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="50" align="right"><? echo $item_smv_array[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]]['smv_pcs']; ?></td>
                <td width="110"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="110"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="100"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
                <td width="110"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="90" align="right"><? echo number_format($row[csf("finishing_qnty")],2); $total_sewing_qnty+=$row[csf("finishing_qnty")]; ?></td>
                <td width="90" align="right"><? echo number_format($ord_sew_smv,2); $total_ord_sew_smv+=$ord_sew_smv; ?></td>
                <td align="right"><? echo number_format($effiecy_aff_perc,2); $total_effiecy_aff_perc+=$effiecy_aff_perc; ?></td>
			</tr>
			<?
			$i++; 
			}
        }
        ?>
        <tfoot>
            <th colspan="7">Total</th>
            <th><? echo number_format($total_sewing_qnty,2); ?></th>
            <th><? echo number_format($total_ord_sew_smv,2); ?></th>
            <th><? echo number_format($total_effiecy_aff_perc,2); ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="total_cm_cost_popup_type_7") 
{
	extract($_REQUEST);
	$comp_expl=explode('_',$company_id);
	$comp_ids_arr=explode(',',str_replace("'","",$comp_expl[0]));
	$comp_ids="";
	foreach($comp_ids_arr as $comp)
	{
		$comp_ids.=$comp.',';
	}
	$comp_id=chop($comp_ids,',');
	
	//$comp_id=$comp_expl[0];
	$comp_type=$comp_expl[1];
	$companys=$_SESSION["comp"];
	$work_comp=$_SESSION["work_comp"];


	if($companys)
	{
		$companys_cond=" and a.company_id in ($companys) ";
	}
	if($work_comp)
	{
		$serving_company_cond="and a.serving_company in($work_comp) ";
	}

	echo load_html_head_contents("Total CM Cost Popup", "../../../", 1, 1,$unicode,'','');	
	//echo $date;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="720px">
        <thead>
            <tr>
                <th colspan="8"><b>CM Cost (On Sewing Qty) Popup (<? echo change_date_format($date); ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="110px">Buyer</th>
                <th width="110px">Style</th>
                <th width="110px">Po Number</th>
                <th width="90px">Sewing Qty</th>
                <th width="90px">CM Cost Pcs</th>
                <th>Total CM Cost</th>
           </tr>
        </thead>
    <?
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
		$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per"); 
		$tot_cost_arr = return_library_array("select job_no, cm_cost from wo_pre_cost_dtls","job_no","cm_cost"); 
		$cm_cost_arr = return_library_array("select job_no, cm_cost from wo_pre_cost_dtls","job_no","cm_cost"); 

		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.total_set_qnty, b.id, b.po_number, b.unit_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0");
		
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['unit_price']=$row[csf('unit_price')];
			$po_array[$row[csf('id')]]['set_qnty']=$row[csf('total_set_qnty')];
		}
				
		if($db_type==0)
		{
			$date_cond=$date;
		}
		elseif($db_type==2)
		{
			$date_cond=change_date_format($date,'','',1);
		}
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and a.location=$location_id";}
		if($comp_type=="workingComp") $cbo_company_condition="a.serving_company in($comp_id)"; else $cbo_company_condition="a.company_id in($comp_id)";

		//$cm_query="SELECT a.production_date, a.po_break_down_id, sum(a.production_quantity) AS finishing_qnty from pro_garments_production_mst a where $cbo_company_condition $location_con and a.production_date='$date_cond' and a.production_type=8 and a.is_deleted=0 and a.status_active=1 group by a.production_date, a.po_break_down_id";
		$poIds_all=chop($po_id,",");
		

		$cm_query=" select a.po_break_down_id as po_break_down_id, sum(a.production_quantity) as sewing_qnty from pro_garments_production_mst a 
		where $cbo_company_condition and  a.production_type =5 and a.production_source=1   $location_con and a.production_date='$date_cond' and a.is_deleted=0 and a.status_active=1   $companys_cond $serving_company_cond
		group by a.po_break_down_id";



		$cm_query_result=sql_select($cm_query); $i=1; //$total_exfactory_qnty=0;
        foreach ($cm_query_result as $row)  
        {
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$cm_val=$row[csf("sewing_qnty")]*($po_array[$row[csf("po_break_down_id")]]['unit_price']/$po_array[$row[csf("po_break_down_id")]]['set_qnty']);
			
			$costing_per=$costing_per_arr[$po_array[$row[csf('po_break_down_id')]]['job_no']];
			$costing_cm=$cm_cost_arr[$po_array[$row[csf('po_break_down_id')]]['job_no']];

			
			if($costing_per==1) $dzn_qnty=12;
			else if($costing_per==3) $dzn_qnty=12*2;
			else if($costing_per==4) $dzn_qnty=12*3;
			else if($costing_per==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;
			
			
			if($costing_per==1) $cm_cost_pcs=$costing_cm/12;
			else if($costing_per==3) $cm_cost_pcs=$costing_cm/(12*2);
			else if($costing_per==4) $cm_cost_pcs=$costing_cm/(12*3);
			else if($costing_per==5) $cm_cost_pcs=$costing_cm/(12*4);
			else $cm_cost_pcs=1*$costing_cm;
			
						
			$dzn_qnty=$dzn_qnty*$po_array[$row[csf("po_break_down_id")]]['set_qnty'];
			$cm_per_pcs=$tot_cost_arr[$po_array[$row[csf('po_break_down_id')]]['job_no']]/$dzn_qnty;
			$cm_value=($tot_cost_arr[$po_array[$row[csf('po_break_down_id')]]['job_no']]/$dzn_qnty)*$row[csf('sewing_qnty')];
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="110px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="110px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="110px"><? echo $po_array[$row[csf('po_break_down_id')]]['po'];; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('sewing_qnty')],2); $total_cm_qnty+=$row[csf('sewing_qnty')]; ?></td>
                <td width="90px" align="right"><? echo $cm_cost_pcs=number_format($cm_cost_pcs,2); $total_cm_per_pcs+=$cm_cost_pcs; ?></td>
                <td align="right"><? echo number_format($row[csf('sewing_qnty')]*$cm_cost_pcs,2); $total_cm_value+=$row[csf('sewing_qnty')]*$cm_cost_pcs; ?></td>
			</tr>
			<?
			$i++;
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th><? echo number_format($total_cm_qnty,2) ?></th>
            <th><? //echo number_format($total_cm_per_pcs,2) ?></th>
            <th><? echo number_format($total_cm_value,2) ?></th>
        </tfoot>
    </table>
    <br/>
    <table border="1" class="rpt_table" rules="all" width="720px">
        <thead>
            <tr>
                <th colspan="8"><b>For In Bound Sub-Contract: (<? echo change_date_format($date); ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="110px">Buyer</th>
                <th width="110px">Style</th>
                <th width="110px">Po Number</th>
                <th width="90px">Sewing Qty</th>
                <th width="90px">CM Cost Pcs</th>
                <th>Total CM Cost</th>
           </tr>
        </thead>
    <?
			

	 	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
		$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per"); 
		$tot_cost_arr = return_library_array("select job_no, cm_cost from wo_pre_cost_dtls","job_no","cm_cost"); 
		$cm_cost_arr = return_library_array("select job_no, cm_cost from wo_pre_cost_dtls","job_no","cm_cost"); 

		$po_array=array();
		
		if($comp_type=="workingComp") $cbo_company_condition="and a.company_id in($comp_id)" ; else $cbo_company_condition="";
		$sql_sub_con_dtls = sql_select("select a.currency_id, a.subcon_job,a.job_no_prefix_num, b.id, b.order_no, b.order_uom,b.rate, b.cust_buyer, b.cust_style_ref,a.party_id from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and a.id=c.mst_id and b.id=c.order_id  $cbo_company_condition order by a.id DESC ");
		
		/*$query_po_break_down=sql_select("select a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.total_set_qnty, b.id, b.po_number, b.unit_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");*/
		
		foreach ($sql_sub_con_dtls as $row)
		{
			$po_array[$row[csf('id')]]['job_no']=$row[csf('subcon_job')];
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('party_id')];
			$po_array[$row[csf('id')]]['style']=$row[csf('cust_style_ref')];
			$po_array[$row[csf('id')]]['po']=$row[csf('order_no')];
			$po_array[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
			$po_array[$row[csf('id')]]['rate']=$row[csf('rate')];
			$po_array[$row[csf('id')]]['currency_id']=$row[csf('currency_id')];
		}
				
		if($db_type==0)
		{
			$date_cond=$date;
		}
		elseif($db_type==2)
		{
			$date_cond=change_date_format($date,'','',1);
		}


		$cm_query_subcon ="SELECT   c.order_id as po_break_down_id,   sum(b.prod_qnty) as production_qnty 
		from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz b,subcon_ord_breakdown c 
		where a.id=b.dtls_id and a.production_date=b.production_date and a.production_date='$date_cond' $cbo_company_condition and a.status_active=1 and a.is_deleted=0  and c.id=b.ord_color_size_id and a.production_type=2 
		group by c.order_id ";
       
		$cm_query_result_subcon=sql_select($cm_query_subcon); $inc=1; //$total_exfactory_qnty=0;
		$currdate=date('d-m-Y');
		if($db_type==0)
		{
			$conversion_date=change_date_format($currdate, "Y-m-d", "-",1);
		}
		else
		{
			$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
		}
		$currency_rate=set_conversion_rate( 2, $conversion_date );

        foreach ($cm_query_result_subcon as $row)  
        {
			if ($inc%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
			$order_uom=$po_array[$row[csf('po_break_down_id')]]['order_uom'];
			$rate=$po_array[$row[csf('po_break_down_id')]]['rate'];
		    $currency_id=$po_array[$row[csf('po_break_down_id')]]['currency_id'];
			if($currency_id==1)$rate=$rate/$currency_rate;
			if($order_uom==2 || $order_uom==12)
			{
				$cm_cost_rate_pcs=$rate/12;
				
			}
			else
			{
				$cm_cost_rate_pcs=$rate;
				//if($currency_id==1)$cm_cost_rate_pcs=$cm_cost_rate_pcs/$currency_rate;
			}
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $inc; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $inc; ?>">
            	<td width="30px"><? echo $inc; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="110px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="110px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="110px"><? echo $po_array[$row[csf('po_break_down_id')]]['po'];; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_qnty')],2); $total_cm_qnty_subcontact_outbound+=$row[csf('production_qnty')]; ?></td>
                <td width="90px" align="right"><? echo $cm_cost_rate_pcs=number_format($cm_cost_rate_pcs,2); $total_cm_cost_rate_pcs+=$cm_cost_rate_pcs; ?></td>
                <td align="right"><? echo number_format($row[csf('production_qnty')]*($cm_cost_rate_pcs),2); $total_cm_value_subcon_outbound+=($row[csf('production_qnty')]*($cm_cost_rate_pcs)); ?></td>
			</tr>
			<?
			$inc++;
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th><? echo number_format($total_cm_qnty_subcontact_outbound,2) ?></th>
            <th><? //echo number_format($total_cm_per_pcs_outbound,2) ?></th>
            <th><? echo number_format($total_cm_value_subcon_outbound,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}
?>