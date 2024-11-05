<?
//phpinfo();
require_once('../common.php');


include('class.conditions.php');
include('class.reports.php');
include('class.fabrics.php');
include('class.yarns.php');
include('class.conversions.php');
include('class.trims.php');
include('class.emblishments.php');
include('class.washes.php');
include('class.commercials.php');
include('class.commisions.php');
include('class.others.php');
 

class ProfitMargin{

    protected $cond = '';
    protected $condition ='';
    private $_query = "";
    private $_dataArray = array();
    private $costHeadDataArr = array();

    private $yarnCostDataArr = array();
    private $fabricCostDataArr = array();
    private $conversionCsotProcessDataArr = array();
    private $trimsCsotProcessDataArr = array();
    private $emblishmentNameCostDataArr = array();
    private $emblishmentWashDataArr = array();
    private $commercialCostDataArr = array();
    private $commissionCostDataArr = array();
    private $otherCostDataArr = array();
    private $convDataArr = array();
    
    


    function __construct($dataArr){
        $this->yarnCostDataArr = $dataArr['yarn_costing'];
        $this->fabricCostDataArr = $dataArr['fabric_costing'];
        $this->conversionCsotProcessDataArr = $dataArr['conversion_costing_process'];
        $this->trimsCsotProcessDataArr = $dataArr['trims_costing'];
        $this->otherCostDataArr = $dataArr['other_costing'];
        $this->emblishmentNameCostDataArr = $dataArr['emblishment_name_costing'];
        $this->emblishmentWashDataArr = $dataArr['emblishment_wash_costing'];
        $this->commercialCostDataArr = $dataArr['commercial_costing'];
        $this->commissionCostDataArr = $dataArr['commission_costing'];
        $this->costHeadDataArr = $dataArr['cost_head_arr'];
        
        $this->condition = $dataArr['condition'];
        $this->cond=$this->condition->getCond();
        $this->_setQuery();
        $this->_setData();


    }

    private function _setQuery(){
        $this->_query = 'select a.id as JOB_ID, a.total_set_qnty as TOTAL_SET_QNTY,b.id as PO_ID, (b.plan_cut) as PLAN_CUT, (b.po_quantity) as ORD_QTY, b.po_total_price as PO_TOTAL_PRICE
        from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.id=b.job_id and b.job_id=c.job_id and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 '.$this->cond ;
    }
    
    
    public function getQuery(){
        return $this->_query;
    }

    private function _setData() {
        $this->_dataArray=sql_select($this->_query,'','');
        return $this;
    }

    public function getData() {
        return $this->_dataArray;
    }




    function getMargin(){
         //return $this->cond;

         $knit_cost_arr=array(1,2,3,4);
         $fabric_dyeingCost_arr=array(25,31,26,32,60,61,62,63,72,80,81,84,85,86,87,38,39,74,78,79,101,133,137,138,139,146,147,149);
         $aop_cost_arr=array(35,36,37,40);
         $fab_finish_cost_arr=array(33,34,38,63,65,66,67,68,69,70,71,72,73,75,76,77,88,82,89,90,91,92,93,94,128,129,135,136,141,143,150,151,155,156,157,145,82,89,132,144);
         $washing_cost_arr=array(140,142,148,64);
         $knit_cost_arr=array(1,2,3,4);


        $total_po_value_arr=array();
        $total_po_qty_arr=array();
        $po_id_arr=array();

        foreach($this->_dataArray as $row)
        {
          $total_po_value_arr[$row['PO_ID']]+=$row["PO_TOTAL_PRICE"];
          $total_po_qty_arr[$row['PO_ID']]+=$row['ORD_QTY'];
          $po_id_arr[$row['PO_ID']]=$row['JOB_ID'];
        }


        $po_cost_arr=array();

         foreach ($po_id_arr as $po_id => $job_id) {

            foreach($knit_cost_arr as $knit_process_id)
						{
							$po_cost_arr['knit_cost'][$po_id]+=array_sum($this->conversionCsotProcessDataArr[$po_id][$knit_process_id]);
						}
            
            foreach($fab_finish_cost_arr as $fin_process_id)
            {
              $po_cost_arr['fabric_finish'][$po_id]+=array_sum($this->conversionCsotProcessDataArr[$po_id][$fin_process_id]);
            }   
          
            foreach($washing_cost_arr as $w_process_id)
            {
              $po_cost_arr['washing_cost'][$po_id]+=array_sum($this->conversionCsotProcessDataArr[$po_id][$w_process_id]);
            }

						foreach($aop_cost_arr as $aop_process_id)
						{
							$po_cost_arr['all_over_cost'][$po_id]+=array_sum($this->conversionCsotProcessDataArr[$po_id][$aop_process_id]);
						}

            foreach($fabric_dyeingCost_arr as $fab_process_id)
						{
							$po_cost_arr['fabric_dyeing_cost'][$po_id]+=array_sum($this->conversionCsotProcessDataArr[$po_id][$fab_process_id]);
						}

            foreach($fab_finish_cost_arr as $fin_process_id)
						{
              $po_cost_arr['fabric_finish'][$po_id]+=array_sum($this->conversionCsotProcessDataArr[$po_id][$fin_process_id]);
						}

 
            $po_cost_arr['yarn_dyeing_cost'][$po_id]=array_sum($this->conversionCsotProcessDataArr[$po_id][30]);
            $po_cost_arr['heat_setting_cost'][$po_id]=array_sum($this->conversionCsotProcessDataArr[$po_id][33]);

            $po_cost_arr['test_cost'][$po_id]=$this->otherCostDataArr[$po_id]['lab_test'];
						$po_cost_arr['freight_cost'][$po_id]= $this->otherCostDataArr[$po_id]['freight'];
						$po_cost_arr['inspection'][$po_id]=$this->otherCostDataArr[$po_id]['inspection'];
						$po_cost_arr['certificate_cost'][$po_id]=$this->otherCostDataArr[$po_id]['certificate_pre_cost'];
						$po_cost_arr['common_oh'][$po_id]=$this->otherCostDataArr[$po_id]['common_oh'];
						$po_cost_arr['currier_cost'][$po_id]=$this->otherCostDataArr[$po_id]['currier_pre_cost'];
						$po_cost_arr['cm_cost'][$po_id]=$this->otherCostDataArr[$po_id]['cm_cost'];

            $po_cost_arr['print_amount'][$po_id]=$this->emblishmentNameCostDataArr[$po_id][1];
						$po_cost_arr['special_amount'][$po_id]=$this->emblishmentNameCostDataArr[$po_id][4];
						$po_cost_arr['embroidery_amount'][$po_id]=$this->emblishmentNameCostDataArr[$po_id][2];
						$po_cost_arr['other_amount'][$po_id]=$this->emblishmentNameCostDataArr[$po_id][5];

            $po_cost_arr['wash_cost'][$po_id]=$this->emblishmentWashDataArr[$po_id][3];

            $po_cost_arr['foreign'][$po_id]=$this->commissionCostDataArr[$po_id][1];
						$po_cost_arr['local'][$po_id]=$this->commissionCostDataArr[$po_id][2];

            $fab_purchase_knit=array_sum($this->fabricCostDataArr['knit']['grey'][$po_id]);
            $fab_purchase_woven=array_sum($this->fabricCostDataArr['woven']['grey'][$po_id]);
            $po_cost_arr['fab_purchase'][$po_id]=$fab_purchase_knit+$fab_purchase_woven;

    
            $cost_head_total=$this->yarnCostDataArr[$po_id]+$this->trimsCsotProcessDataArr[$po_id]+$this->commercialCostDataArr[$po_id];
            foreach ($this->costHeadDataArr as $cost_head) {
              $cost_head_total += $po_cost_arr[$cost_head][$po_id]*1;


			  $cost_head_wise[$cost_head]+= $po_cost_arr[$cost_head][$po_id]*1;
            }

            
            // $profit_arr['po_value'][$po_id] = $total_po_value_arr[$po_id];
            // $profit_arr['po_cost'][$po_id] = $cost_head_total;
            // $profit_arr['po_profit'][$po_id] = $total_po_value_arr[$po_id]-$cost_head_total;
            // $profit_arr['job_profit'][$job_id] += $total_po_value_arr[$po_id]-$cost_head_total;

			return $cost_head_wise;

         }

         return $profit_arr;

        
    }



}//class;



$company_id = 17;
$po_id = "71302,71303";
 


$condition = new condition;
$condition->company_name("= $company_id")->po_id_in($po_id);
//->jobid_in('1,2,3,455')

$condition->init();

$commercial = new commercial($condition);
$commercial_data_arr = $commercial->getAmountArray_by_orderAndPrecostdtlsid();

$yarn = new yarn($condition);
$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();

$fabric = new fabric($condition);
$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();

$conversion = new conversion($condition);
$conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
 
$trims = new trims($condition);
$trims_costing_arr=$trims->getAmountArray_by_order();
 
$emblishment = new emblishment($condition);
$emblishment_name_costing_arr=$emblishment->getAmountArray_by_orderAndEmbname();
  
$wash = new wash($condition);
$emblishment_wash_costing_arr=$wash->getAmountArray_by_orderAndEmbname();

$commercial= new commercial($condition);
$commercial_costing_arr=$commercial->getAmountArray_by_order();

$commission = new commision($condition);
$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
 
$other= new other($condition);
$other_costing_arr=$other->getAmountArray_by_order(); 



$dataArr = array(
  'condition' => $condition,
  'yarn_costing' => $yarn_costing_arr,
  'fabric_costing' => $fabric_costing_arr,
  'conversion_costing_process' => $conversion_costing_arr_process,
  'trims_costing' => $trims_costing_arr,
  'other_costing' => $other_costing_arr,
  'emblishment_name_costing' => $emblishment_name_costing_arr,
  'emblishment_wash_costing' => $emblishment_wash_costing_arr,
  'commercial_costing' => $commercial_costing_arr,
  'commission_costing' => $commission_costing_arr,
  'cost_head_arr'=>['fab_purchase','knit_cost','washing_cost','all_over_cost','yarn_dyeing_cost','fabric_dyeing_cost','heat_setting_cost','fabric_finish','test_cost','print_amount','embroidery_amount','special_amount','other_amount','wash_cost','foreign','local','freight_cost','inspection','certificate_cost','common_oh','currier_cost','cm_cost']
  
);

$bfObj = new ProfitMargin($dataArr);

$dataArr = $bfObj->getMargin();
 var_dump($dataArr);

 
// $conv_data_amt=$conversion->getAmountArray_by_conversionid();
// print_r($conv_data_amt);die;
// $conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();

//Note: need to check conversion cost



$precost_data_arr=get_precost_data(array('company_id'=>$company_id,'job_no'=>'RpC-23-00032','po_id'=>$po_id));


print_r($precost_data_arr);


function get_precost_data($dataArr=array()){
		  
	// include('../../includes/class4/class.conditions.php');
	// include('../../includes/class4/class.reports.php');
	// include('../../includes/class4/class.fabrics.php');
	// include('../../includes/class4/class.yarns.php');
	// include('../../includes/class4/class.conversions.php');
	// include('../../includes/class4/class.trims.php');
	// include('../../includes/class4/class.emblishments.php');
	// include('../../includes/class4/class.washes.php');
	// include('../../includes/class4/class.commercials.php');
	// include('../../includes/class4/class.commisions.php');
	// include('../../includes/class4/class.others.php');
   
   
   $zero_value	="1";
   $supplier_check	="0";
   $txt_job_no= "'".$dataArr[job_no]."'";
   $cbo_company_name=	$dataArr[company_id];
   $txt_po_breack_down_id=	$dataArr[po_id];
	   
   //print_r($dataAr);die;
   
   $precostSql="select RATE,COSTING_DATE,COSTING_PER from WO_PRE_COST_MST where JOB_NO=$txt_job_no";
   $precostSqlResult = sql_select($precostSql);

   foreach($precostSqlResult as $rows)
   {
	   $txt_costing_date=$rows[COSTING_DATE];
	   $cbo_costing_per=$rows[COSTING_PER];
	   $rate_amt=$rows[RATE];

   }

   

   ///extract($_REQUEST);
   $txt_costing_date=change_date_format(str_replace("'","",$txt_costing_date),'yyyy-mm-dd','-');
   if($txt_job_no=="") $job_no=''; else $job_no=" and a.job_no=".$txt_job_no."";
   if($cbo_company_name=="") $company_name=''; else $company_name=" and a.company_name=".$cbo_company_name."";
   if($cbo_buyer_name=="") $cbo_buyer_name=''; else $cbo_buyer_name=" and a.buyer_name=".$cbo_buyer_name."";
   if($txt_style_ref=="") $txt_style_ref=''; else $txt_style_ref=" and a.style_ref_no=".$txt_style_ref."";
   if($txt_costing_date=="") $txt_costing_date=''; else $txt_costing_date=" and c.costing_date='".$txt_costing_date."'";
   $txt_po_breack_down_id=str_replace("'",'',$txt_po_breack_down_id);
   if(str_replace("'",'',$txt_po_breack_down_id)=="") 
   {
	   $txt_po_breack_down_id_cond='';  $txt_po_breack_down_id_cond1='';  $txt_po_breack_down_id_cond2='';  $txt_po_breack_down_id_cond3=''; 
   }
   else
   {
	   $txt_po_breack_down_id_cond=" and b.id in(".$txt_po_breack_down_id.")";
	   $txt_po_breack_down_id_cond1=" and id in(".$txt_po_breack_down_id.")";
	   $txt_po_breack_down_id_cond2=" and po_break_down_id in(".$txt_po_breack_down_id.")";
	   $txt_po_breack_down_id_cond3=" and b.id in(".$txt_po_breack_down_id.")";
   }
   
   //array for display name
   $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
   $comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
   $imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
   $color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
   
   if($db_type==0) $group_gsm="group_concat( distinct b.gsm_weight) AS gsm_weight";
   if($db_type==2) $group_gsm="listagg(b.gsm_weight ,',') within group (order by b.gsm_weight) AS gsm_weight";
   
   $gsm_weight_top=return_field_value("$group_gsm", "lib_body_part a,wo_pre_cost_fabric_cost_dtls b", "a.id=b.body_part_id and b.job_no=$txt_job_no and b.status_active=1 and b.is_deleted=0 and a.body_part_type in(1,20)","gsm_weight");
   //$gsm_weight_bottom=return_field_value("$group_gsm", "lib_body_part a,wo_pre_cost_fabric_cost_dtls b", "a.id=b.body_part_id and b.job_no=$txt_job_no and a.body_part_type=20 ","gsm_weight");
   //echo $gsm_weight_bottom.'DD';
   $gmtsitem_ratio_array=array();
   $grmnt_items = "";
   $grmts_sql = sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where job_no=$txt_job_no");
   

   foreach($grmts_sql as $key=>$val)
   {
	   $grmnt_items .=$garments_item[$val[csf("gmts_item_id")]].",";
	   $gmtsitem_ratio_array[$val[csf('job_no')]][$val[csf('gmts_item_id')]]=$val[csf('set_item_ratio')];	
   }
   
   $grmnt_items = rtrim($grmnt_items,","); 

   $set_order=0;
   if(count($grmts_sql)>1)
   {
	   $set_order=1;
   }
   
   if($db_type==0) ///fab_knit_fin_req_kg,fab_knit_req_kg
   {	
	  $sql = "SELECT a.job_no,a.company_name, a.buyer_name,a.style_ref_no,a.ship_mode, a.gmts_item_id,a.order_uom, a.avg_unit_price,sum(b.plan_cut) as job_quantity,sum(b.po_quantity) as ord_qty,  c.costing_per,c.budget_minute,c.costing_date,c.approved,c.exchange_rate ,a.quotation_id,c.incoterm,c.sew_effi_percent,group_concat(b.sc_lc) as sc_lc, d.fab_knit_req_kg,d.fab_knit_fin_req_kg, d.fab_woven_req_yds,d.fab_woven_fin_req_yds, d.fab_yarn_req_kg,c.sew_smv
		   from wo_po_details_master a,wo_po_break_down b, wo_pre_cost_mst c left join wo_pre_cost_sum_dtls d on   c.job_no=d.job_no and d.status_active=1 and d.is_deleted=0
		   where a.job_no=b.job_no_mst and b.job_no_mst=c.job_no  and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $job_no $txt_po_breack_down_id_cond $company_name $cbo_buyer_name $txt_style_ref $txt_costing_date group by a.job_no,a.company_name, a.buyer_name,a.style_ref_no, a.gmts_item_id,a.order_uom, a.avg_unit_price, c.costing_per,c.approved,c.budget_minute,c.incoterm,c.sew_effi_percent, d.fab_knit_req_kg, d.fab_woven_req_yds,d.fab_knit_fin_req_kg,d.fab_woven_fin_req_yds, d.fab_yarn_req_kg,c.sew_smv order by a.job_no"; 
   }
   else if($db_type==2)
   {	
	   $sql = "SELECT a.job_no,a.company_name, a.buyer_name,a.ship_mode,a.style_ref_no, a.gmts_item_id,a.order_uom, a.avg_unit_price,sum(b.plan_cut) as job_quantity, sum(b.po_quantity) as ord_qty, listagg(cast(b.sc_lc as varchar2(4000)),',') within group (order by b.sc_lc) as sc_lc, c.costing_per,c.costing_date,c.budget_minute,c.approved,a.quotation_id,c.exchange_rate ,c.incoterm,c.sew_effi_percent,d.fab_knit_fin_req_kg, d.fab_knit_req_kg, d.fab_woven_req_yds,d.fab_woven_fin_req_yds, d.fab_yarn_req_kg,c.sew_smv
	   from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c left join  wo_pre_cost_sum_dtls d on  c.job_no=d.job_no and d.status_active=1 and d.is_deleted=0
	   where a.job_no=b.job_no_mst and b.job_no_mst=c.job_no  and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $job_no $txt_po_breack_down_id_cond $company_name $cbo_buyer_name $txt_style_ref group by a.job_no,a.company_name, a.buyer_name,a.style_ref_no, a.gmts_item_id,a.order_uom,a.ship_mode, a.avg_unit_price, c.incoterm,c.costing_date,c.exchange_rate ,a.quotation_id,c.costing_per,c.sew_effi_percent,c.approved,c.budget_minute, d.fab_knit_req_kg, d.fab_woven_req_yds,d.fab_knit_fin_req_kg,d.fab_woven_fin_req_yds, d.fab_yarn_req_kg,c.sew_smv  order by a.job_no"; //a.job_quantity as job_quantity,
   }
   
   //echo $sql;die;
   $data_array=sql_select($sql);
   $plan_cut_qty=$data_array[0][csf('job_quantity')];
   $exchange_rate=$data_array[0][csf('exchange_rate')];
   $pre_costing_smv=$data_array[0][csf('sew_smv')];
   
	
	$preCost_histry=sql_select( "select a.id as mst_id,b.id as dtls_id,a.sew_smv as sew_smv,a.sew_effi_percent as sew_effi_percent,b.margin_dzn_percent as margin_dzn_percent ,b.fabric_cost_percent as fabric_cost_percent,
	b.trims_cost_percent  as trims_cost_percent,b.embel_cost_percent as embel_cost_percent,b.wash_cost_percent as wash_cost_percent ,b.comm_cost_percent as comm_cost_percent ,
	b.commission_percent as commission_percent,b.lab_test_percent as lab_test_percent,b.inspection_percent as inspection_percent,b.cm_cost_percent as cm_cost_percent,
	b.freight_percent as freight_percent,b.currier_percent as currier_percent,b.certificate_percent as certificate_percent,b.common_oh_percent as common_oh_percent
	from wo_pre_cost_mst_histry a,wo_pre_cost_dtls_histry b where a.job_no=b.job_no and a.job_no=$txt_job_no  order by  a.id,b.id  asc");
   
   list($preCost_histry_row)=$preCost_histry;
   $opert_profitloss_percent=$preCost_histry_row[csf('margin_dzn_percent')];
   $fabric_cost_percent=$preCost_histry_row[csf('fabric_cost_percent')];
   $trims_cost_percent=$preCost_histry_row[csf('trims_cost_percent')];
   $embel_cost_percent=$preCost_histry_row[csf('embel_cost_percent')];
   $wash_cost_percent=$preCost_histry_row[csf('wash_cost_percent')];
   $comm_cost_percent=$preCost_histry_row[csf('comm_cost_percent')];
   $commission_percent=$preCost_histry_row[csf('commission_percent')];
   $common_oh_percent=$preCost_histry_row[csf('common_oh_percent')];
   
   $lab_test_percent=$preCost_histry_row[csf('lab_test_percent')];
   $inspection_percent=$preCost_histry_row[csf('inspection_percent')];
   $cm_cost_percent=$preCost_histry_row[csf('cm_cost_percent')];
   $freight_percent=$preCost_histry_row[csf('freight_percent')];
   $currier_percent=$preCost_histry_row[csf('currier_percent')];
   $certificate_percent=$preCost_histry_row[csf('certificate_percent')];
   //$currier_percent=$preCost_histry_row[csf('currier_percent')];
   $sew_effi_percent=$data_array[0][csf('sew_effi_percent')];//
   $hissew_effi_percent=$preCost_histry_row[csf('sew_effi_percent')];
   $sew_smv=$preCost_histry_row[csf('sew_smv')];
   $first_app_date="";
   $last_app_date="";
   $preCost_approved=sql_select( "select max(b.approved_no) as approved_no, min(b.approved_date) as first_app_date, max(b.approved_date) as last_app_date,a.id from wo_pre_cost_mst a, approval_history b where b.full_approved=1 and  a.id=b.mst_id and a.job_no=$txt_job_no and b.entry_form=15 group by a.id"); 
	
   if(count($preCost_approved)>0)
   {
	   foreach($preCost_approved as $preCost_approved_row)
	   {
		   $approved_no_row=$preCost_approved_row[csf('approved_no')];
		   $fst_date=$preCost_approved_row[csf('first_app_date')];
		   $fstapp_date=$fst_date[0];
		   
		   $last_date=$preCost_approved_row[csf('last_app_date')];
		   $lstapp_date=$last_date[0];
		   $precost_id=$preCost_approved_row[csf('id')];
		
		   
		   if($approved_no_row>1){
			   $revised_no=$approved_no_row-1;
		   }
	   }
   }

 
   $preCost_approved_component=sql_select( "select   max(approved_date) as last_app_date,count(approved_date) as approve_no,cost_component_id from co_com_pre_costing_app_his where mst_id=$precost_id and job_no=$txt_job_no and entry_form=15 group by cost_component_id  ");
   $higher_autorize_app_date=$preCost_approved_component[0][csf('last_app_date')];
   $higher_autorize_approve_no=$preCost_approved_component[0][csf('approve_no')];
   if($higher_autorize_app_date!="") $last_date=$higher_autorize_app_date;
   if($higher_autorize_approve_no!="") $revised_no=$revised_no+$higher_autorize_approve_no;

	$company_id=str_replace("'","",$cbo_company_name);
   // echo $fstapp_date.'dddddddddd';die;
	if($fstapp_date=="" || $fstapp_date=="00-00-0000") $first_app_dateD="";else $first_app_dateD=$fstapp_date;
	if($lstapp_date=="" || $lstapp_date=="00-00-0000") $lstapp_dateD="";else $lstapp_dateD=$lstapp_date;
   //echo $higher_autorize_app_date."**".$lstapp_date;die;
   $img_path = ($img_path)?$img_path:'../../';
   
   //Fabric ,Trims,Emblishment,Wash Synchronize check//If color size breakdown updated found
   $sql_trim=sql_select("select Max(b.is_apply_last_update) as is_apply_last_update  from  wo_pre_cost_fabric_cost_dtls b where  b.job_no=$txt_job_no and b.is_deleted=0 and b.status_active=1");
	
   $trim_msg="";
   foreach($sql_trim as $row)
   {
	   if($row[csf("is_apply_last_update")]==2)
	   {
		   $trim_msg=" Trims,";
		   $chk_msg=1; 
	   }
   }
   
   $sql_trim=sql_select("select Max(b.is_apply_last_update) as is_apply_last_update  from  wo_pre_cost_trim_cost_dtls b where  b.job_no=$txt_job_no and b.is_deleted=0 and b.status_active=1");
	
   $trim_msg="";
   foreach($sql_trim as $row)
   {
	   if($row[csf("is_apply_last_update")]==2)
	   {
		   $trim_msg=" Trims,";
		   $chk_msg=1; 
	   }
   }
   $sql_conv=sql_select("select Max(b.is_apply_last_update) as is_apply_last_update  from  wo_pre_cost_fab_conv_cost_dtls b where  b.job_no=$txt_job_no and b.is_deleted=0 and b.status_active=1");
   
   $conv_msg="";
   foreach($sql_conv as $row)
   {
	   if($row[csf("is_apply_last_update")]==2)
	   {
		   $conv_msg=" Conversion,";
		   $chk_msg=1; 
	   }
   }
   
   $sql_embl=sql_select("select Max(b.is_apply_last_update) as is_apply_last_update,emb_name  from wo_pre_cost_embe_cost_dtls b where  b.job_no=$txt_job_no and b.is_deleted=0 and b.status_active=1 and cons_dzn_gmts>0 group by emb_name");
	
   $emb_msg="";
   foreach($sql_embl as $row)
   {
	   if($row[csf("is_apply_last_update")]==2 && $row[csf("emb_name")]==3) //Wash
	   {
		   $wash_msg=" Wash,";
		   $chk_msg=1; 
	   }
	   else if($row[csf("is_apply_last_update")]==2 && $row[csf("emb_name")]!=3) 
	   {
		   $emb_msg=" Emblishment";
		   $chk_msg=1; 
	   }
   }
	if($chk_msg==1)
	{
   $check_msg="Color size breakdown is updated. please Synchronize following heads: ";
   $check_msg.=$fabric_msg.$conv_msg.$trim_msg.$emb_msg.$wash_msg;
	   //echo "document.getElementById('check_sms2').innerHTML = '".$check_msg."';\n";
	}
	else
	{
	   $check_msg="";
		//echo "document.getElementById('check_sms2').innerHTML = '".$check_msg."';\n";
	}

	
   
   ob_start();
   ?>
   <div style="width:972px; margin:0 auto">
	
   <div style="width:970px; font-size:20px; font-weight:bold" align="center"><b style="float:left"> <img  src='<? echo $img_path.$imge_arr[$company_id]; ?>' height='40px' width='100px' /></b><? echo $comp[str_replace("'","",$cbo_company_name)]; ?><b style="float:right; font-size:14px; font-weight:bold"> <?  echo '&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;';?>  </b></div>
   <div style="width:970px; font-size:18px; font-weight:bold" align="center"><b style="float:left"></b>Bill Of Materials (BOM) Report<b style="float:right; font-size:18px; font-weight:bold"> <? if($revised_no!=0) echo 'Revised No &nbsp;:'.$revised_no; else echo " &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;"; ?>  &nbsp;  </b> </div>
   <div style="width:970px; font-size:14px; font-weight:bold" align="center"><b style="float:right; font-size:14px; font-weight:bold; color:#F00"> <? if($fst_date!="") echo "First Approval Date:".$fst_date; if($last_date!="") echo ";<br>  Last Approval Date:".$last_date; else echo " &nbsp;"; ?>  &nbsp;  </b>
   <p style="color:red"> <? echo $check_msg;?></p>
   
	</div>
   <?
   
   foreach ($data_array as $row)
   {	
	   $order_price_per_dzn=0;
	   $order_job_qnty=0;
	   $ord_qty=0;
	   $avg_unit_price=0;
	   $order_values = $row[csf("ord_qty")]*$row[csf("avg_unit_price")];
	   $result =sql_select("select po_number,pub_shipment_date,file_no,excess_cut,grouping from wo_po_break_down where job_no_mst=$txt_job_no $txt_po_breack_down_id_cond1 and status_active=1 and is_deleted=0 order by pub_shipment_date DESC");
	   //$tot_row=count($result);
	   $job_in_orders = '';$pulich_ship_date='';$job_in_ref = '';$job_in_file = '';
	   $tot_excess_cut=0;$tot_row=0;
	   foreach ($result as $val)
	   {
		   $job_in_orders .= $val[csf('po_number')].", ";
		   $pulich_ship_date = $val[csf('pub_shipment_date')];
		   if($val[csf('excess_cut')]>0)
		   {
			   $tot_row++;	
		   }
		   $tot_excess_cut+= $val[csf('excess_cut')];
		   
		   //$job_in_ref .= $val[csf('grouping')].", ";
		   //$job_in_file .= $val[csf('file_no')].", ";
	   //	if($job_in_ref=='') $job_in_ref= $val[csf('grouping')]; else $job_in_ref.=",". $val[csf('grouping')];
	   //	if($job_in_file=='') $job_in_file= $val[csf('file_no')]; else $job_in_file.=",". $val[csf('file_no')];
	   }
	   $job_in_orders = substr(trim($job_in_orders),0,-1);
	   //$sew_effi_percent=$row[csf("sew_effi_percent")];
	   
	   ?>
			   <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px" rules="all">
			   <caption><? if($row[csf("approved")]==1) echo "<font color='red' style=' font-size:15px; font-weight:bold'>THIS JOB IS APPROVED</font>"; else "";?> </caption>
				   <tr>
					   <td>Job No</td>
					   <td><b><? echo $row[csf("job_no")]; ?></b></td>
					   <td>Buyer</td>
					   <td><b><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></b></td>
					   <td>Style Ref. No</td>
					   <td><b><? echo $row[csf("style_ref_no")]; ?></b></td>
					   <td>Garments Item</td>
					   <td><b><? echo $grmnt_items; ?></b></td>
				   </tr>
				   <tr>
					   <td>Incoterm</td>
					   <td><b><? echo $incoterm[$row[csf("incoterm")]]; ?></b></td>
					   <td>Costing for</td>
					   <td><b><? echo $costing_per[$row[csf("costing_per")]]; ?></b></td>
					 
					   <td>Sew. Effi. %</td>
					   <td><b><? echo $row[csf("sew_effi_percent")]; ?></b></td>
					   <td>Order UOM</td>
					   <td><? echo $unit_of_measurement[$row[csf("order_uom")]]; ?></td>
				   </tr>
				   <tr>
					   <td>LC/SC No</td>
					   <td colspan="7"><b><? echo $row[csf("sc_lc")]; ?></b></td>
				   </tr>
			   </table>

		   <?	
		   if($row[csf("costing_per")]==1){$order_price_per_dzn=12;$costing_for="1 DZN";}
		   else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;$costing_for="1 PCS";}
		   else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;$costing_for="2 DZN";}
		   else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;$costing_for="3 DZN";}
		   else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;$costing_for="4 DZN";}
		   else {$order_price_per_dzn=0;$costing_for="DZN";}
		   $order_job_qnty=$row[csf("job_quantity")];
		   $avg_unit_price=$row[csf("avg_unit_price")];
		   $ord_qty=$row[csf("ord_qty")];
   }//end first foearch
	 $sql_po = "select a.job_no, a.company_name, a.set_smv, a.total_set_qnty, b.id as po_id, (e.plan_cut_qnty) as plan_cut, (b.po_quantity) as ord_qty, b.excess_cut, b.unit_price, b.po_total_price, b.po_number, b.shiping_status, b.pub_shipment_date, b.is_confirmed, b.insert_date, c.exchange_rate, c.incoterm, d.price_dzn, e.order_quantity as color_size_qty
		   from wo_po_details_master a,wo_po_break_down b, wo_pre_cost_mst c, wo_pre_cost_dtls d, wo_po_color_size_breakdown e
		   where a.job_no=b.job_no_mst and b.job_no_mst=c.job_no and c.job_no=d.job_no and b.id=e.po_break_down_id and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $job_no $txt_po_breack_down_id_cond $company_name $cbo_buyer_name $txt_style_ref order by b.insert_date ASC"; 
		   $po_data_array=sql_select($sql_po); $set_ratio=0; $exchange_rate=0;
		   foreach($po_data_array as $row)
		   {
			   $insert_date=explode(" ",$row[csf("insert_date")]);
			   $insert_date=$insert_date[0];
			   $po_wise_arr[$row[csf("po_id")]]['ord_qty']=$row[csf("ord_qty")];
			   $po_wise_arr[$row[csf("po_id")]]['plan_cut']+=$row[csf("plan_cut")];
			   $po_wise_arr[$row[csf("po_id")]]['excess_cut']=$row[csf("excess_cut")];
			   $po_wise_arr[$row[csf("po_id")]]['unit_price']=$row[csf("unit_price")];
			   $po_wise_arr[$row[csf("po_id")]]['po_total_price']=$row[csf("po_total_price")];
			   $po_wise_arr[$row[csf("po_id")]]['total_set_qnty']=$row[csf("total_set_qnty")];
			   $po_wise_arr[$row[csf("po_id")]]['shiping_status']=$row[csf("shiping_status")];
			   $po_wise_arr[$row[csf("po_id")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
			   $po_wise_arr[$row[csf("po_id")]]['is_confirmed']=$row[csf("is_confirmed")];
			   $po_wise_arr[$row[csf("po_id")]]['insert_date']=$insert_date;
			   $po_wise_arr[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
			   $po_wise_arr[$row[csf("po_id")]]['set_smv']=$row[csf("set_smv")];
			   $po_wise_arr[$row[csf("po_id")]]['color_size_qty']+=$row[csf("color_size_qty")];
			   
			   $incoterm_id=$row[csf("incoterm")];
			   $set_ratio=$row[csf("total_set_qnty")];
			   $price_dzn=$row[csf("price_dzn")];
			   $exchange_rate=$row[csf("exchange_rate")];
		   }
		   //echo $price_dzn; die;
	   
		   ?>
		   <br/>
			   <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px; font-size:14px" rules="all">
			   <caption> <b>Order Info </b></caption>
				   <thead>
					   <th width="20">SL</th>
					   <th width="90">Order No</th>
					   <th width="70">Order Qty(Pcs)</th>
					   <th width="70">Breakdown Qty(Pcs)</th>
					   <th width="60">Excess Cut %</th>
					   <th width="70">Plan Cut Qty(Pcs)</th>
					   <th width="60">Avg. Unit Price (Pcs)</td>
					   <th width="70">Order Value</th>
					   <th width="60">SMV</th>
					   <th width="70">TTL SAH</th>
					   <th width="60">Ship date</th>
					   <th width="60">Insert date</th>
					   <th width="70">Order Status</th>
					   <th>Ship Status</th>
				   </tr>
				   <?
				   $p=1;$total_po_qty=$total_plan_cut_qty=$total_po_value=$total_ttl_sah=0;
					
				   foreach($po_wise_arr as $poKey=>$val) 
				   {     
					   $ord_qty=$val["ord_qty"]*$val["total_set_qnty"];
					   
					   $plan_qty=$val["plan_cut"];
					   $unit_price=($val["unit_price"]/$val["total_set_qnty"]);
					   if(is_infinite($unit_price) || is_nan($unit_price)){$unit_price=0;}
					   $po_price=$ord_qty*$unit_price;
					   if($set_order>0)
					   {
						   $ttl_sah=($val["plan_cut"]*$val["set_smv"])/60;
						   $ttl_title="Plan Cut Qty(Set=".$val["plan_cut"].")*SMV/60";
					   }
					   
					   else
					   {
						   $ttl_sah=($plan_qty*$val["set_smv"])/60;
						   $ttl_title="Plan Cut Qty(Pcs)*SMV/60";
					   }
					   
				   ?>
			   
				   <tr>
					   <td><? echo $p; ?></td>
					   <td style="word-break:break-all"><? echo $val["po_number"]; ?></td>
					   <td align="right" title="<?=$val["ord_qty"].'='.$val["total_set_qnty"];?>"><? echo fn_number_format($ord_qty,0); ?></td>
					   <td align="right"><? echo fn_number_format($val["color_size_qty"],0); ?></td>
					   <td align="right"><? echo fn_number_format($val["excess_cut"],2); ?></td>
					   <td align="right"><? echo fn_number_format($plan_qty,0); ?></td>
					   <td align="right"><? echo fn_number_format($unit_price,3); ?></td>
					   <td align="right"><? echo fn_number_format($po_price,2); ?></td>
					   <td align="right"><? echo fn_number_format($val["set_smv"],3); ?></td>
					   <td align="right" title="<?=$ttl_title;?>"><? echo fn_number_format($ttl_sah,2); ?></td>
					   <td><? echo date('d-m-y',strtotime($val["pub_shipment_date"])); //change_date_format?></td>
					   <td><? echo date('d-m-y',strtotime($val["insert_date"])); ?></td>
					 
					   <td><? echo $order_status[$val["is_confirmed"]]; ?></td>
					   <td style="word-break:break-all"><? echo $shipment_status[$val["shiping_status"]]; ?></td>
				   </tr>
				   <?
				   $p++;
				   $total_po_qty+=$val["ord_qty"];
				   $total_plan_cut_qty+=$val["plan_cut"];
				   $total_po_value+=$val["po_total_price"];
				   $total_color_size_qty+=$val["color_size_qty"];
				   
				   $tot_po_qty+=$ord_qty;
				   $tot_plan_cut_qty+=$plan_qty;
				   $tot_po_value+=$po_price;
				   
				   $total_ttl_sah+=$ttl_sah;
				   //$total_ttl_sah+=$ttl_sah;
				   //$total_ttl_sah+=$ttl_sah;
				   }
				   
				   $job_po_rate_set=$total_po_value/$total_po_qty;
				   
				   if(is_infinite($job_po_rate_set) || is_nan($job_po_rate_set)){$job_po_rate_set=0;}
				   
				   $td_qty_pcs_color="";
				   if($tot_po_qty!=$total_color_size_qty) $td_qty_pcs_color="#FF0000";
				   ?>
				   <tfoot>
				   <th colspan="2">Job Total</th>
				   <th align="right" bgcolor="<? echo $td_qty_pcs_color; ?>"><? echo fn_number_format($tot_po_qty,0); ?></th>
				   <th align="right" bgcolor="<? echo $td_qty_pcs_color; ?>"><? echo fn_number_format($total_color_size_qty,0); ?></th>
				   <th align="right" title="Total ((Plan Cut-PO Qty)/Po Qty)*100"><? 
				   $excec_per=(($tot_plan_cut_qty-$tot_po_qty)/$tot_po_qty)*100;
				   if(is_infinite($excec_per) || is_nan($excec_per)){$excec_per=0;}
				   echo  fn_number_format($excec_per,0); 
				   ?></th>
				   
				   <th align="right"><? echo  fn_number_format($tot_plan_cut_qty,0); ?></th>
				   <th align="right"><? $job_po_rate=$tot_po_value/$tot_po_qty;echo  fn_number_format($job_po_rate,3); ?></th>
				   <th align="right"><? echo  fn_number_format($tot_po_value,2); ?></th>
				   <th align="right" title="Total TTL SAH*60/Plan Cuts"><?  
				   $tot_smv=($total_ttl_sah*60)/$tot_plan_cut_qty;
				   if(is_infinite($tot_smv) || is_nan($tot_smv)){$tot_smv=0;}
				   echo  fn_number_format($tot_smv,3); 
				   ?></th>
				   <th align="right"><?  echo  fn_number_format($total_ttl_sah,2); ?></th>
				   <th align="right"><? //echo  fn_number_format($total_po_qty,0); ?></th>
				   <th align="right"><? //echo  fn_number_format($total_po_qty,0); ?></th>
				   <th><? //echo  fn_number_format($total_po_qty,0); ?></th>
				   <th><? 
				   $tot_perDznValue=($tot_po_value/$tot_po_qty)*12;
				   if(is_infinite($tot_perDznValue) || is_nan($tot_perDznValue)){$tot_perDznValue=0;}
				   //echo  fn_number_format($total_po_qty,0); ?></th>
				   </tfoot>
			   </table>
			   <br/>
			   <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px" rules="all">
			   <?
			   //echo $txt_po_breack_down_id;die;
			   $po_no=str_replace("'","",$txt_po_breack_down_id);
		   $condition= new condition();
		   if(str_replace("'","",$txt_job_no) !=''){
				 $condition->job_no("=$txt_job_no");
			}
		   
			 if(str_replace("'","",$txt_po_breack_down_id)!='')
			{
			   $condition->po_id("in($po_no)"); 
			}
		   
			 $condition->init();
		   //$yarn= new yarn($condition);
		  $fabric= new fabric($condition);
		  //echo $fabric->getQuery(); die;
		 //$fabric_costing_arr=$fabric->getQtyArray_by_job_knitAndwoven_greyAndfinish_production();
		  $yarn= new yarn($condition);
		   $yarn_costing_arr=$yarn->getJobWiseYarnAmountArray();
		   
		   $fabric= new fabric($condition);
		   $fabric_costing_arr2=$fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
		   
		   $fabric_qty_arr=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
		   $fabric_amount_arr=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();

   
			   
		   $conversion= new conversion($condition);
		   
		   $conversion_costing_arr_process=$conversion->getAmountArray_by_job();
		   
		   $trims= new trims($condition);
		   
		   $trims_costing_arr=$trims->getAmountArray_by_job();
		   
		   $emblishment= new emblishment($condition);
		   $emblishment_costing_arr=$emblishment->getAmountArray_by_job();
		   $wash= new wash($condition);
		   //echo $wash->getQuery(); die;
		   $emblishment_costing_arr_wash=$wash->getAmountArray_by_job();
	   
		   $commercial= new commercial($condition);
		   
		   $commercial_costing_arr=$commercial->getAmountArray_by_job();
		   
		   $commission= new commision($condition);
		   
		   $commission_costing_arr=$commission->getAmountArray_by_job();
	   
		   $other= new other($condition);
		   
		   $other_costing_arr=$other->getAmountArray_by_job();
			
		   $job_no=str_replace("'","",$txt_job_no);
		   //All Cost start here...
	   
		   ///----------------AAA---------
		   $ttl_conversion_cost=array_sum($conversion_costing_arr_process[$job_no]);
	   
		   $ttl_fab_cost=array_sum($fabric_costing_arr2['knit']['grey'][$job_no])+array_sum($fabric_costing_arr2['woven']['grey'][$job_no]);
		   $total_fab_cost=$yarn_costing_arr[$job_no]+$ttl_fab_cost+$ttl_conversion_cost;
		   
		   $ttl_trims_cost=$trims_costing_arr[$job_no];
		   $ttl_emblishment_cost=$emblishment_costing_arr[$job_no]+$emblishment_costing_arr_wash[$job_no];
		   $ttl_commercial_cost=$commercial_costing_arr[$job_no];
		   $ttl_commission_cost=$commission_costing_arr[$job_no];
		   
		   $ttl_cm_cost=$other_costing_arr[$job_no]['cm_cost'];
		   
		   if(is_infinite($ttl_cm_cost) || is_nan($ttl_cm_cost)){$ttl_cm_cost=0;}
		   $ttl_freight_cost=$other_costing_arr[$job_no]['freight'];
		   if(is_infinite($ttl_freight_cost) || is_nan($ttl_freight_cost)){$ttl_freight_cost=0;}
		   $ttl_inspection_cost=$other_costing_arr[$job_no]['inspection'];
		   if(is_infinite($ttl_inspection_cost) || is_nan($ttl_inspection_cost)){$ttl_inspection_cost=0;}
		   $ttl_certificate_cost=$other_costing_arr[$job_no]['certificate_pre_cost'];
		   if(is_infinite($ttl_certificate_cost) || is_nan($ttl_certificate_cost)){$ttl_certificate_cost=0;}
		   $ttl_common_oh=$other_costing_arr[$job_no]['common_oh'];
		   if(is_infinite($ttl_common_oh) || is_nan($ttl_common_oh)){$ttl_common_oh=0;}
		   $ttl_currier_cost=$other_costing_arr[$job_no]['currier_pre_cost'];
		   if(is_infinite($ttl_currier_cost) || is_nan($ttl_currier_cost)){$ttl_currier_cost=0;}
		   $ttl_lab_test_cost=$other_costing_arr[$job_no]['lab_test'];
		   if(is_infinite($ttl_lab_test_cost) || is_nan($ttl_lab_test_cost)){$ttl_lab_test_cost=0;}
		   $ttl_depr_amor_pre_cost=$other_costing_arr[$job_no]['depr_amor_pre_cost'];
		   if(is_infinite($ttl_depr_amor_pre_cost) || is_nan($ttl_depr_amor_pre_cost)){$ttl_depr_amor_pre_cost=0;}
		   $ttl_deffdlc_cost=$other_costing_arr[$job_no]['deffdlc_cost'];
		   if(is_infinite($ttl_deffdlc_cost) || is_nan($ttl_deffdlc_cost)){$ttl_deffdlc_cost=0;}			
		   $ttl_other_cost=$ttl_cm_cost+$ttl_freight_cost+$ttl_inspection_cost+$ttl_certificate_cost+$ttl_common_oh+$ttl_currier_cost+$ttl_lab_test_cost+$ttl_depr_amor_pre_cost+$ttl_deffdlc_cost;
		   //echo $ttl_conversion_cost.'='.$ttl_trims_cost.'='.$ttl_emblishment_cost.'='.$ttl_commercial_cost.'='.$ttl_commission_cost.'='.$ttl_other_cost;
		   $ttl_total_cost=$total_fab_cost+$ttl_trims_cost+$ttl_emblishment_cost+$ttl_commercial_cost+$ttl_commission_cost+$ttl_other_cost;
		   $costing_per_unit_budget=$ttl_total_cost/$tot_po_qty;
		   if(is_infinite($costing_per_unit_budget) || is_nan($costing_per_unit_budget)){$costing_per_unit_budget=0;}
			   
		   $fab_knit_req_kg=$data_array[0][csf('fab_knit_req_kg')];
		   if(is_infinite($fab_knit_req_kg) || is_nan($fab_knit_req_kg)){$fab_knit_req_kg=0;}
		   $fab_knit_fin_req_kg=$data_array[0][csf('fab_knit_fin_req_kg')];
		   if(is_infinite($fab_knit_fin_req_kg) || is_nan($fab_knit_fin_req_kg)){$fab_knit_fin_req_kg=0;}
		   $fab_woven_req_yds=$data_array[0][csf('fab_woven_req_yds')];
		   if(is_infinite($fab_woven_req_yds) || is_nan($fab_woven_req_yds)){$fab_woven_req_yds=0;}
		   $fab_woven_fin_req_yds=$data_array[0][csf('fab_woven_fin_req_yds')];
		   if(is_infinite($fab_woven_fin_req_yds) || is_nan($fab_woven_fin_req_yds)){$fab_woven_fin_req_yds=0;}
		   
		   $fab_yarn_req_kg=$data_array[0][csf('fab_yarn_req_kg')];
		   if(is_infinite($fab_yarn_req_kg) || is_nan($fab_yarn_req_kg)){$fab_yarn_req_kg=0;}
		   $exchange_rate=$data_array[0][csf('exchange_rate')];
		   if(is_infinite($exchange_rate) || is_nan($exchange_rate)){$exchange_rate=0;}
		   //$avg_unit_price=$data_array[0][csf('avg_unit_price')];\
		   $avg_unit_price=$job_po_rate;
		   if(is_infinite($avg_unit_price) || is_nan($avg_unit_price)){$avg_unit_price=0;}
		   $ship_mode=$data_array[0][csf('ship_mode')];
		   if(is_infinite($ship_mode) || is_nan($ship_mode)){$ship_mode=0;}
		   $quotation_id=$data_array[0][csf('quotation_id')];
		   if(is_infinite($quotation_id) || is_nan($quotation_id)){$quotation_id=0;}
		   $costing_date=$data_array[0][csf('costing_date')];
		   if(is_infinite($costing_date) || is_nan($costing_date)){$costing_date=0;}
		   $gsm_weights_top=implode(",",array_unique(explode(",",$gsm_weight_top)));
		   if(is_infinite($gsm_weights_top) || is_nan($gsm_weights_top)){$gsm_weights_top=0;}
		   //$gsm_weight_bottom=implode(",",array_unique(explode(",",$gsm_weight_bottom)));
		   //if($gsm_weights_top!='') $gsm_weightTop=$gsm_weights_top;else $gsm_weightTop='';
	   
					   //echo $gsm_weightTop .$gsm_weightBottom;
			   ?>
				   <tr>
					   <td>Knit Fabric Cons</td>
					   <td align="right" ><b><? echo $fab_knit_req_kg; ?></b></td>
					   <td>Woven Fab. Cons	</td>
					   <td align="right" ><b><? echo $fab_woven_req_yds; ?></b></td>
					   <td>Quotation Id</td>
					   <td><b><? echo $quotation_id; ?></b></td>
					   <td>Costing Date</td>
					   <td><b><? echo  date('d-M-y',strtotime($costing_date)); ?></b></td>
				   </tr>
				   <tr>
					   <td>Avg Yarn Req</td>
					   <td align="right"><b><? echo $fab_yarn_req_kg; ?></b></td>
					   <td>Woven Fin Fabric Cons</td>
					   <td align="right" ><b><? echo $fab_woven_fin_req_yds; ?></b></td>
					 
					   <td>Exchange Rate</td>
					   <td align="right" ><b><? echo $exchange_rate; ?></b></td>
					   <td>Avg Unit Price</td>
					   <td align="right" ><? echo fn_number_format($avg_unit_price,3); ?></td>
				   </tr>
					<tr>
					   <td>Knit Fin Fab. Cons </td>
					   <td align="right"><b><? echo $fab_knit_fin_req_kg;  ?></b></td>
					   <td>Incoterm</td>
					   <td><b><? echo $incoterm[$incoterm_id]; ?></b></td>
					 
					   <td>Ship Mode</td>
					   <td><b><? echo $shipment_mode[$ship_mode]; ?></b></td>
					   <td>Cost Per Unit As Budget</td>
					   <td align="right"  title="Total Cost<? echo $ttl_total_cost;?>/Total PO Qty Pcs"><? echo fn_number_format($costing_per_unit_budget,3); ?></td>
				   </tr>
				   <tr style="background:#F9F">
					   <td><b> SMV % (As 1st App) : &nbsp;<? echo $sew_smv;?></b></td>
					   <td><b> Efficiency %(As 1st App) :&nbsp;<? echo $hissew_effi_percent;?> </b></td>
					   <td align="right" colspan="3">Net Pft/Loss %(As 1st App)</td>
					   <td align="right" ><b><? echo $opert_profitloss_percent;?></b></td>
					   <td title="Total Cost/Total PO Qty">Net Profit/Loss %</td>
					   <td align="right" ><b>
					   <? 
					   $operatin_profit_loss_per=(($avg_unit_price-$costing_per_unit_budget)/$avg_unit_price)*100;
					   if(is_infinite($operatin_profit_loss_per) || is_nan($operatin_profit_loss_per)){$operatin_profit_loss_per=0;}
					   echo fn_number_format($operatin_profit_loss_per,2);
						?>
					   </b>
					   </td>
				   </tr>
			   </table>
			   
		   <?
		   
   //$ttl_commercial_cost=$commercial_costing_arr[$job_no];
	   //2 Fabric Cost part here------------------------------------------- 	   	
	   $sql = "select id, job_no, item_number_id, gsm_weight, uom, body_part_id, fab_nature_id, color_type_id, fabric_description, avg_cons, fabric_source, rate, amount, avg_finish_cons, status_active from wo_pre_cost_fabric_cost_dtls 
		   where job_no=".$txt_job_no." and status_active=1 and is_deleted=0";
		   
	   $data_array=sql_select($sql);
	   
	   $knit_fab="";$woven_fab=""; 
	   $knit_subtotal_amount=0;
	   $woven_subtotal_amount=0;$knit_subtotal_amount_dzn=0;$knit_subtotal_amount_kg=0;$woven_subtotal_amount_dzn=0;$woven_subtotal_amount_kg=$knit_subtotal_avg_cons_dzn=$knit_subtotal_avg_finish_cons_dzn=0;
	   $i=2;$j=2;
	   foreach( $data_array as $row )
	   {
			   $set_item_ratio=return_field_value("set_item_ratio"," wo_po_details_mas_set_details", "job_no='".$row[csf('job_no')]."' and gmts_item_id='".$row[csf('item_number_id')]."'");
			
			  $fincons=0;
			  $greycons=0;
			  $order_qty_fab=0;
			  $fab_dtls_data=sql_select("select po_break_down_id,color_number_id,gmts_sizes,cons,requirment from wo_pre_cos_fab_co_avg_con_dtls where pre_cost_fabric_cost_dtls_id=".$row[csf("id")]." $txt_po_breack_down_id_cond2 and cons !=0");
			  foreach($fab_dtls_data as $fab_dtls_data_row )
			  {
					$sql_po_qty_fab=sql_select("select sum(c.plan_cut_qnty) as order_quantity  from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id=".$fab_dtls_data_row[csf('po_break_down_id')]." and c.item_number_id='".$row[csf('item_number_id')]."' and size_number_id='".$fab_dtls_data_row[csf('gmts_sizes')]."' and  color_number_id= '".$fab_dtls_data_row[csf('color_number_id')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
					list($sql_po_qty_row_fab)=$sql_po_qty_fab;
					$po_qty_fab=$sql_po_qty_row_fab[csf('order_quantity')];
					$order_qty_fab+=$po_qty_fab;
			  }
		   $knit_cost_dzn=$row[csf("amount")];$woven_cost_dzn=$row[csf("amount")];
		   if($row[csf("fab_nature_id")]==2)//knit fabrics
		   {
			   $item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")];
			   $fincons=$fabric_qty_arr['knit']['finish'][$row[csf("id")]][$row[csf("uom")]];
			   $greycons=$fabric_qty_arr['knit']['grey'][$row[csf("id")]][$row[csf("uom")]];
			   $row[csf("amount")]=$fabric_amount_arr['knit']['grey'][$row[csf("id")]][$row[csf("uom")]];
				  $i++;	
			   $knit_fab .= '<tr>
				   <td align="left">'.$item_descrition.'</td>
				   <td align="left">'.$fabric_source[$row[csf("fabric_source")]].'</td>
				   <td align="right">'.$row[csf("gsm_weight")].'</td>
				   <td align="right">'.fn_number_format($row[csf("avg_cons")],3).'</td>
				   <td align="right">'.fn_number_format($greycons,2).'</td>
					<td align="right">'.fn_number_format($row[csf("avg_finish_cons")],3).'</td>
				   <td align="right">'.fn_number_format($fincons,2).'</td>
				   <td align="right">'.$unit_of_measurement[$row[csf("uom")]].'</td>
				   <td align="right">'.fn_number_format($row[csf("rate")],3).'</td>
				   <td align="right">'.fn_number_format($knit_cost_dzn,4).'</td>
				   <td align="right">'.fn_number_format($row[csf("amount")],2).'</td> 
				   <td align="right">'.fn_number_format((($knit_cost_dzn/$price_dzn)*100),2).'</td> 
			   </tr>';	
			   $knit_subtotal_avg_cons_dzn+=$row[csf("avg_cons")];
			   $knit_subtotal_avg_cons+=$greycons;
			   $knit_subtotal_avg_finish_cons+=$fincons;
			   $knit_subtotal_avg_finish_cons_dzn+=$row[csf("avg_finish_cons")];
			   $knit_subtotal_amount+=$row[csf("amount")];
			   $knit_subtotal_amount_dzn+=$knit_cost_dzn;
			   $knit_subtotal_amount_kg=$knit_subtotal_amount/$knit_subtotal_amount_dzn;
		   }	
       
      
       

		   if($row[csf("fab_nature_id")]==3)//woven fabrics
		   {
			   $fincons=$fabric_qty_arr['woven']['finish'][$row[csf("id")]][$row[csf("uom")]];
			   $greycons=$fabric_qty_arr['woven']['grey'][$row[csf("id")]][$row[csf("uom")]];
			   $row[csf("amount")]=$fabric_amount_arr['woven']['grey'][$row[csf("id")]][$row[csf("uom")]];
			   $item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")];
			   $j++;
				$woven_fab .= '<tr>
				   <td align="left">'.$item_descrition.'</td>
				   <td align="left">'.$fabric_source[$row[csf("fabric_source")]].'</td>
				   <td align="center">'.$row[csf("gsm_weight")].'</td>
				   <td align="right">'.fn_number_format($row[csf("avg_cons")],3).'</td>
				   <td align="right">'.fn_number_format($greycons,2).'</td>
				   <td align="right">'.fn_number_format($row[csf("avg_finish_cons")],3).'</td>
					<td align="right">'.fn_number_format($fincons,2).'</td>
				   <td align="right">'.$unit_of_measurement[$row[csf("uom")]].'</td>
				   <td align="right">'.fn_number_format($row[csf("rate")],3).'</td>
				   <td align="right">'.fn_number_format($woven_cost_dzn,4).'</td>
				   <td align="right">'.fn_number_format($row[csf("amount")],2).'</td> 
				   <td align="right">'.fn_number_format((($woven_cost_dzn/$price_dzn)*100),2).'</td> 
			   </tr>';	
			   $woven_subtotal_avg_cons+=$greycons;
			   $woven_subtotal_avg_cons_dzn+=$row[csf("avg_cons")];
			   $woven_subtotal_avg_finish_cons+=$fincons;
			   $woven_subtotal_avg_finish_cons_dzn+=$row[csf("avg_finish_cons")];
			   $woven_subtotal_amount+=$row[csf("amount")];
			   $woven_subtotal_amount_dzn+=$woven_cost_dzn;
			   $woven_subtotal_amount_kg=$woven_subtotal_amount/$woven_subtotal_amount_dzn;
		   }
	   }	
   
	   $knit_fab= '<div style="margin-top:15px">
			   <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px;text-align:center;" rules="all">		
				   
				   
				   <label  style="float:left;background:#CCCCCC;font-size:larger;"><b>All Fabric Cost </b> </label>	
						   
					   <tr style="font-weight:bold"  align="center">
						   <td width="80" rowspan="'.$i.'" ><div class="verticalText"><b>Knit Fabric</b></div></td>
						   <td width="300">Description</td>
						   <td width="100">Source</td>
						   <td width="50">GSM</td>
						   <td width="100">Fab. Cons/'.$costing_for.'</td>
						   <td width="100">Gray Fabric Qty</td>
						   <td width="100">Finish Cons/ '.$costing_for.'</td>
						   <td width="100">Finish Fab Qty</td>
						   <td width="50">UOM</td>
							<td width="50">Rate</td>
						   <td width="50">Amount/ '.$costing_for.'</td>
						   <td width="50">TTL Amount</td>
						   <td width="50">% to Ord. Value</td>
					   </tr>'.$knit_fab;
	   $woven_fab = '<tr>
					   <td width="80" rowspan="'.$j.'"><div class="verticalText"><b>Woven Fabric</b></div></td></tr>'.$woven_fab;	
					   
	   //knit fabrics table here 
	   $KnitFabricTotalParcentToOrdValue =(($knit_subtotal_amount_dzn/$price_dzn)*100);
	   if(is_infinite($KnitFabricTotalParcentToOrdValue) || is_nan($KnitFabricTotalParcentToOrdValue)){$KnitFabricTotalParcentToOrdValue=0;}
	   
	   $knit_fab .='<tr class="rpt_bottom" style="font-weight:bold">
					   <td colspan="3">Knit Total</td>
					   <td align="right">'.fn_number_format($knit_subtotal_avg_cons_dzn,3).'</td>
					   <td align="right">'.fn_number_format($knit_subtotal_avg_cons,2).'</td>
					   <td align="right">'.fn_number_format($knit_subtotal_avg_finish_cons_dzn,3).'</td>
					   <td align="right">'.fn_number_format($knit_subtotal_avg_finish_cons,2).'</td>
					   <td align="right"></td>
					   <td align="right"></td>
					   <td align="right">'.fn_number_format($knit_subtotal_amount_dzn,4).'</td>
					   <td align="right">'.fn_number_format($knit_subtotal_amount,2).'</td>
					   <td align="right">'.fn_number_format($KnitFabricTotalParcentToOrdValue,2).'</td> 
				   </tr>';
		 echo $knit_fab;
	   //woven fabrics table here 
	   $WovenFabricTotalParcentToOrdValue=(($woven_subtotal_amount_dzn/$price_dzn)*100);
	   if(is_infinite($WovenFabricTotalParcentToOrdValue) || is_nan($WovenFabricTotalParcentToOrdValue)){$WovenFabricTotalParcentToOrdValue=0;}

	   $fabricTotalParcentToOrdValue=((($knit_subtotal_amount_dzn+$woven_subtotal_amount_dzn)/$price_dzn)*100);
	   if(is_infinite($fabricTotalParcentToOrdValue) || is_nan($fabricTotalParcentToOrdValue)){$fabricTotalParcentToOrdValue=0;}
	   
	   $woven_fab .='<tr class="rpt_bottom" style="font-weight:bold">
					   <td colspan="3">Woven Total</td>
					   <td align="right">'.fn_number_format($woven_subtotal_avg_cons_dzn,3).'</td>
					   <td align="right">'.fn_number_format($woven_subtotal_avg_cons,2).'</td>
					   <td align="right">'.fn_number_format($woven_subtotal_avg_finish_cons_dzn,3).'</td>
					   <td align="right">'.fn_number_format($woven_subtotal_avg_finish_cons,2).'</td>
					   <td align="right"></td>
					   <td align="right"></td>
					   <td align="right">'.fn_number_format($woven_subtotal_amount_dzn,4).'</td>
					   <td align="right">'.fn_number_format($woven_subtotal_amount,2).'</td>
					   <td align="right">'.fn_number_format($WovenFabricTotalParcentToOrdValue,2).'</td> 
				   </tr>
				   <tr class="rpt_bottom" style="font-weight:bold">
					   <td colspan="3">Fabric Total</td>
					   <td align="right"></td>
					   <td align="right">'.fn_number_format($knit_subtotal_avg_cons_dzn+$woven_subtotal_avg_cons_dzn,3).'</td>
					   <td align="right">'.fn_number_format($knit_subtotal_avg_cons+$woven_subtotal_avg_cons,2).'</td>
					   <td align="right">'.fn_number_format($knit_subtotal_avg_finish_cons_dzn+$woven_subtotal_avg_finish_cons_dzn,3).'</td>
					   <td align="right">'.fn_number_format($finish_fab_req_qty=$knit_subtotal_avg_finish_cons+$woven_subtotal_avg_finish_cons,2).'</td>
					   <td align="right"></td>
					   <td align="right"></td>
					   <td align="right">'.fn_number_format($knit_subtotal_amount_dzn+$woven_subtotal_amount_dzn,4).'</td>
					   <td align="right">'.fn_number_format($knit_subtotal_amount+$woven_subtotal_amount,2).'</td>
					   <td align="right">'.fn_number_format($fabricTotalParcentToOrdValue,2).'</td> 
				   </tr>
					  </table></div>';
				   $precostData['FinishFabQty']=$finish_fab_req_qty;
		  echo $woven_fab;           		
			   //end 	All Fabric Cost part report-------------------------------------------
		   $lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
				$sql = "select min(id) as id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, color,type_id, min(cons_ratio) as cons_ratio, sum(cons_qnty) as cons_qnty, rate, sum(amount) as amount from wo_pre_cost_fab_yarn_cost_dtls where job_no=".$txt_job_no."  and status_active=1 and is_deleted=0 group by count_id, copm_one_id, percent_one, copm_two_id, percent_two, color,type_id, rate";
				
			   $data_array=sql_select($sql); 
			   $yarn_data_array=$yarn->getCountCompositionPercentTypeColorAndRateWiseYarnQtyAndAmountArray();
			   //print_r($yarn_data_array);
		   
	   ?>
	   <div style="margin-top:15px">
		   <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px;text-align:center;" rules="all">
	   
		   <label  style="float:left;background:#CCCCCC; font-size:larger;"><b>Yarn Cost </b> </label>	
			   <tr style="font-weight:bold">
				  
				   <td width="350">Yarn Desc</td>
				   <td width="80">Yarn Qty/<?=$costing_for; ?></td> 
				   <td width="80">TTL Yarn Qty</td>
				
				   <td width="80">Rate</td>
				   <td width="80">Amount/<?=$costing_for; ?></td>
				   <td width="80">TTL Amount</td>
				   <td width="">% to Ord. Value</td>
			   </tr>
		   <?
		   $total_yarn_qty = 0;
		   $total_yarn_amount = 0; $total_yarn_cost_dzn=$total_yarn_qty_dzn=0; $total_yarn_cost_kg=0; $total_yarn_avg_cons_qty=0;
		   foreach( $data_array as $row )
		   { 
			   if($row[csf("percent_one")]==100)
				   $item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$color_library[$row[csf("color")]]." ".$yarn_type[$row[csf("type_id")]];
			   else
				   $item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$composition[$row[csf("copm_two_id")]]." ".$row[csf("percent_two")]."% ".$color_library[$row[csf("color")]]." ".$yarn_type[$row[csf("type_id")]];
				$rowcons_qnty = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['qty'];
			   $rowavgcons_qnty = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['qty'];
			   $rowamount = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['amount'];
			   if(is_infinite($rowamount) || is_nan($rowamount)){$rowamount=0;}
		   ?>	 
			   <tr>
				   <td align="left"><? echo $item_descrition; ?></td>
				   <td align="right"><? echo fn_number_format($row[csf("cons_qnty")],3); ?></td>
				   <td align="right"><? echo fn_number_format($rowcons_qnty,2); ?></td>
				  
				   <td align="right"><? echo fn_number_format($row[csf("rate")],3); ?></td>
				   <td align="right"><? echo fn_number_format($row[csf("amount")],4); ?></td>
				   <td align="right"><? echo fn_number_format($rowamount,2); ?></td>
				   <td align="right"><? 
				   $cv=($row[csf("amount")]/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   ?></td>
			   </tr>
		   <?  
				 $total_yarn_qty+=$rowcons_qnty;
				 $total_yarn_qty_dzn+=$row[csf("cons_qnty")];
				 $total_avg_yarn_qty+=$rowavgcons_qnty;
				 $total_yarn_amount +=$rowamount;
				 $total_yarn_cost_dzn+=$row[csf("amount")];
				 $total_yarn_avg_cons_qty+=$rowavgcons_qnty;
				 $total_yarn_cost_kg=$total_yarn_amount/$total_yarn_qty;
				 if(is_infinite($total_yarn_cost_kg) || is_nan($total_yarn_cost_kg)){$total_yarn_cost_kg=0;}
		   }
		   ?>
			   <tr class="rpt_bottom" style="font-weight:bold">
				   <td>Yarn Total</td>
				   <td align="right"><? echo fn_number_format($total_yarn_qty_dzn,4); ?></td>
				   <td align="right"><? echo fn_number_format($total_yarn_qty,2); ?></td>
				   
				   <td></td>
				   <td align="right"><? echo fn_number_format($total_yarn_cost_dzn,4); ?></td>
				   <td align="right"><? echo fn_number_format($total_yarn_amount,2); ?></td>
				   <td align="right"><? 
				   $cv=($total_yarn_cost_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format(($total_yarn_cost_dzn/$price_dzn)*100,2); 
				   ?></td>
			   </tr>
		   </table>
	 </div>
	 <?

 
	   //End Yarn Cost part report here -------------------------------------------
	   
	   //start	Conversion Cost to Fabric report here -------------------------------------------
	   $sql_count = "select a.cons_process as cons_process from wo_pre_cost_fab_conv_cost_dtls a left join wo_pre_cost_fabric_cost_dtls b on a.job_no=b.job_no and a.fabric_description=b.id and b.status_active=1 and b.is_deleted=0 where a.job_no=".$txt_job_no." and a.status_active=1 and a.is_deleted=0 group by a.cons_process order by  a.cons_process";
	   $tot_data_array=sql_select($sql_count);
	   foreach( $tot_data_array as $row ){
				$process_id=$row[csf("cons_process")];
				$process_row+=count($row[csf("cons_process")]);
		}
		$sql = "select a.id,a.fabric_description as pre_cost_fabric_cost_dtls_id, a.job_no, a.cons_process, a.req_qnty, a.charge_unit,a.amount,a.color_break_down, a.status_active,b.body_part_id,b.uom ,b.fab_nature_id,b.color_type_id,b.fabric_description,b.item_number_id from wo_pre_cost_fab_conv_cost_dtls a left join wo_pre_cost_fabric_cost_dtls b on a.job_no=b.job_no and a.fabric_description=b.id and b.status_active=1 and b.is_deleted=0 where a.job_no=".$txt_job_no." and a.status_active=1 and a.is_deleted=0 order by  a.cons_process";
		$data_array=sql_select($sql);
	?>
	   <div style="margin-top:15px">
		   <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px;text-align:center;" rules="all">
		   
		   <label style="float:left;background:#CCCCCC; font-size:larger"><b>Conversion Cost to Fabric </b> </label>	
			   <tr style="font-weight:bold; font-size:12px">
				   <td width="230">Particulars</td>
				   <td width="110">Process</td>
				   <td width="60">Cons/<?=$costing_for; ?></td>
				   <td width="80">TTL Required</td>
				   <td width="60">Uom</td>
				  
				   <td width="60">Rate</td>
				   <td width="60">Rate(Tk)</td>
				   <td width="70">Amount/<?=$costing_for; ?></td>
				   <td width="80">TTL Amount</td>
				   <td>% to Ord. Value</td>
			   </tr>
		   <?
		   $conv_data_qty=$conversion->getQtyArray_by_conversionid();
		   $conv_data_amt=$conversion->getAmountArray_by_conversionid();
   
		   $total_conversion_cost=$total_convsion_qty_dzn=$total_conversion_cost_dzn=0;$total_conversion_cost_dzn=0;$total_conversion_cost_kg=0;
		   $total_convsion_qty=0;
		   $total_avg_convsion_qty=0;$grand_total_conv_qnty=0;$grand_total_avg_convsion_qty=$grand_total_conversion_cost_dzn=$grand_total_avg_convsion_qty_dzn=0;$grand_total_conversion_cost=0;
		   $process_array_check=array();$k=1;
		   foreach( $data_array as $row )
		   { 
			   $convsion_qty=$conv_data_qty[$row[csf('id')]][$row[csf('uom')]];
			   $conversion_cost=$conv_data_amt[$row[csf('id')]][$row[csf('uom')]];
			   
			   if($row[csf("pre_cost_fabric_cost_dtls_id")] ==0) $item_descrition = "All Fabrics";
			   else
			   {
				   $item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")];
			   }
			   
			   $process_id=$row[csf("cons_process")];
			   if (!in_array($process_id,$process_array_check) )
			   {
				   if($k!=1)
				   {
					   ?>
					  <tr>
						   <td>&nbsp;</td>
						   <td><strong>Process Total </strong></td>
						   <td align="right"><strong><? echo fn_number_format($total_convsion_qty_dzn,3); ?></strong></td>
						   <td align="right"><strong><? echo fn_number_format($total_convsion_qty,2); $precostData['knitting_day'][$process_id]=$total_convsion_qty;?></strong></td>
						   <td align="right"><strong><? //echo fn_number_format($total_convsion_qty,4); ?></strong></td>
						   <td>&nbsp;</td>
						   <td>&nbsp;</td>
						   <td align="right"><strong><? echo fn_number_format($total_conversion_cost_dzn,3); ?></strong></td>
						   <td align="right"><strong><? echo fn_number_format($total_conversion_cost,2); ?></strong></td>
						   <td align="right"><? 
						   $cv=($total_conversion_cost_dzn/$price_dzn)*100;
						   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						   echo fn_number_format($cv,3); 
						   //echo fn_number_format(($total_conversion_cost_dzn/$price_dzn)*100,3); 
						   
						   ?></td>
					   </tr>
					   <?
				   }
				   ?>
				   <?
				   unset($total_convsion_qty_dzn);unset($total_conversion_cost_dzn);
				   unset($total_convsion_qty);
				   unset($total_avg_convsion_qty);
				   unset($total_conversion_cost);
				   unset($total_convsion_qty_dzn);
				   $process_array_check[]=$process_id; 
				   $k++;    
			   }
		   ?>	 
			   <tr>
				   <td align="left" style="font-size:14px; word-break:break-all"><? echo $item_descrition; ?></td>
				   <td align="left"><? echo $conversion_cost_head_array[$row[csf("cons_process")]]; ?></td>
				   <td align="right"><? echo fn_number_format($row[csf("req_qnty")],3); ?></td>
				   <td align="right"><? echo fn_number_format($convsion_qty,2); ?></td>
				   <td align="right"><? echo  $unit_of_measurement[$row[csf('uom')]]; ?></td>
				 
				   <td align="right"><? echo fn_number_format($row[csf("charge_unit")],3); ?></td>
				   <td align="right"><? echo fn_number_format($row[csf("charge_unit")]*$exchange_rate,3); ?></td>
				   
				   <td align="right"><? echo fn_number_format($row[csf('amount')],4); ?></td>
				   <td align="right"><? echo fn_number_format($conversion_cost,2); ?></td>
				   <td align="right" title="<? echo $row[csf('amount')].'='.$price_dzn;?>">
				   <? 
				   $cv=($row[csf('amount')]/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format(($row[csf('amount')]/$price_dzn)*100,2); 
				   ?>
				   </td>
			   </tr>
		   <?
			   $total_convsion_qty+=$convsion_qty;
			   $total_convsion_qty_dzn+=$row[csf("req_qnty")];
			   $total_avg_convsion_qty+=$row[csf("req_qnty")];
			   $total_conversion_cost += $conversion_cost;
		   //	$total_conversion_cost_dzn += $row[csf('amount')];
			   $grand_total_conv_qnty+=$convsion_qty;
			   $grand_total_avg_convsion_qty+=$convsion_qty;
			   $grand_total_avg_convsion_qty_dzn+=$row[csf("req_qnty")];
			   $grand_total_conversion_cost+= $conversion_cost;
			   $grand_total_conversion_cost_dzn+= $row[csf('amount')];
			   $total_conversion_cost_dzn+=$row[csf('amount')];
			   $total_conversion_cost_kg=$grand_total_conversion_cost/$total_avg_yarn_qty;
			   if(is_infinite($total_conversion_cost_kg) || is_nan($total_conversion_cost_kg)){$total_conversion_cost_kg=0;}
			   //$grand_total_avg_convsion_qty;
			   }
		   ?>
		   <tr class="rpt_bottom" style="font-weight:bold">
			   <td>&nbsp;</td>
			   <td align="right">Process Total</td>
			   <td align="right"><? echo fn_number_format($total_convsion_qty_dzn,3); ?></td>
				<td align="right"><? echo fn_number_format($total_convsion_qty,2); ?></td>
				<td align="right"><? //echo fn_number_format($total_convsion_qty,4); ?></td>
			   <td>&nbsp;</td>
			   <td>&nbsp;</td> 
			   <td align="right"><? echo fn_number_format($total_conversion_cost_dzn,4); ?></td>                   
			   <td align="right"><? echo fn_number_format($total_conversion_cost,2); ?></td>
			   <td align="right"><? 
				   $cv=($total_conversion_cost_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
			   //echo fn_number_format(($total_conversion_cost_dzn/$price_dzn)*100,2); 
			   
			   ?></td>
		   </tr>   
		   <tr class="rpt_bottom" style="font-weight:bold">
			   <td colspan="2" align="right">Conversion Total</td>
			   <td align="right"><? echo fn_number_format($grand_total_avg_convsion_qty_dzn,3); ?></td>
			   <td align="right"><? echo fn_number_format($grand_total_conv_qnty,2); ?></td>
			   <td align="right"><? //echo fn_number_format($grand_total_conv_qnty,4); ?></td>
			   <td>&nbsp;</td>
			   <td>&nbsp;</td> 
			   <td align="right"><? echo fn_number_format($grand_total_conversion_cost_dzn,4); ?></td>                
			   <td align="right"><? echo fn_number_format($grand_total_conversion_cost,2); ?></td>
			   <td align="right"  title="<? echo $grand_total_conversion_cost_dzn.'='.$price_dzn;?>">
			   <? 
				   $cv=($grand_total_conversion_cost_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
			   //echo fn_number_format(($grand_total_conversion_cost_dzn/$price_dzn)*100,3); 
			   ?>
			   </td>
		   </tr>     
			 <tr class="rpt_bottom" style="font-weight:bold">
				 <td>Total Fabric Cost</td>
			   <td align="right" colspan="6" style="background:#F9F">Previous Fabric cost % (As 1st Approval) : <? echo fn_number_format($fabric_cost_percent,4); ?> </td>
			   <td align="right" title="Fabric Cost+Yarn+Conversion(Dzn)"><? $total_fab_cost_dzn=$knit_subtotal_amount_dzn+$woven_subtotal_amount_dzn+$total_yarn_cost_dzn+$grand_total_conversion_cost_dzn;echo fn_number_format($total_fab_cost_dzn,4); ?></td>
			   <td align="right"><? $all_fab_cost=$knit_subtotal_amount+$woven_subtotal_amount+$total_yarn_amount+$grand_total_conversion_cost; echo fn_number_format($all_fab_cost,2); ?></td>
			   <td align="right"><? 
				   $cv=($total_fab_cost_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
			   //echo fn_number_format(($total_fab_cost_dzn/$price_dzn)*100,2); 
			   ?></td>
			 </tr>           
		   </table>
	 </div>
	 <?
   //End Conversion Cost to Fabric report here -------------------------------------------
  // echo $knit_subtotal_amount;die;
  // echo $knit_subtotal_amount.'+'.$woven_subtotal_amount.'+'.$total_yarn_amount.'+'.$grand_total_conversion_cost;;die;
   
   //start	Trims Cost part report here -------------------------------------------
   $supplier_library_fabric=return_library_array( "select a.id, a.supplier_name from lib_supplier a where a.is_deleted=0  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");
   
	   $sql = "select id, job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp_multi, status_active from wo_pre_cost_trim_cost_dtls  where job_no=".$txt_job_no." and status_active=1 and is_deleted=0";
	   $data_array=sql_select($sql);
	?>
	   <div style="margin-top:15px">
		   <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px;text-align:center;" rules="all">
		  
		   <label  style="float:left;background:#CCCCCC; font-size:larger"><b>Trims Cost</b> </label>	
			   <tr style="font-weight:bold">
				   <td width="110">Item Group</td>
				   <td width="150">Description</td>
				   <td width="100">Nominated Supp</td>
				   <td width="100">Brand/Supp Ref</td>
				   <td width="60">UOM</td>
				   <td width="80">Cons/<?=$costing_for; ?></td>
				   <td width="100">TTL Required</td>
				   <td width="80">Rate</td>
				   <td width="80">Amount/<?=$costing_for; ?></td>
				   <td width="80">Amount</td>
				   <td width="60">% to Ord. Value</td>
			   </tr>
		   <?
		   $trim_qty_arr=$trims->getQtyArray_by_precostdtlsid();
		   //print_r($trim_qty);
		   $trim_amount_arr=$trims->getAmountArray_precostdtlsid();
       //print_r( $trim_amount_arr);die;
		   $total_trims_cost=0;  $total_trims_qty=$total_trims_cost_dzn=0;$total_trims_cost_dzn=0;$total_trims_cost_kg=0;
		   foreach( $data_array as $row ){ 
			   
			   $trim_group=return_library_array( "select item_name,id from  lib_item_group where id=".$row[csf("trim_group")], "id", "item_name" ); 
			   $cons_dzn_gmts= $row[csf("cons_dzn_gmts")];
			   $amount_dzn= $row[csf("amount")];
			   $pre_trims_qty=$trim_qty_arr[$row[csf("id")]];
			   $pre_trims_amount=$trim_amount_arr[$row[csf("id")]];  
			   
			   $nominated_supp_str="";
			   $exsupp=explode(",",$row[csf("nominated_supp_multi")]);
			   foreach($exsupp as $sid)
			   {
				   if($nominated_supp_str=="") $nominated_supp_str=$supplier_library_fabric[$sid]; else $nominated_supp_str.=','.$supplier_library_fabric[$sid];
			   }         	 
		   ?>	 
			   <tr>
				   <td align="left"><? echo $trim_group[$row[csf("trim_group")]]; ?></td>
				   <td align="left"><? echo $row[csf("description")]; ?></td>
				   <td align="left"><?=$nominated_supp_str; ?></td>
				   <td align="left"><? echo $row[csf("brand_sup_ref")]; ?></td>
				   <td align="left"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
				   <td align="right"><? echo fn_number_format($cons_dzn_gmts,3); ?></td>
				   <td align="right"><? echo fn_number_format($pre_trims_qty,4); ?></td>
				   <td align="right"><? echo fn_number_format($row[csf("rate")],3); ?></td>
					<td align="right"><? echo fn_number_format($amount_dzn,4); ?></td>
				   <td align="right"><? echo fn_number_format($pre_trims_amount,2); ?></td>
				   <td align="right"  title="<? echo $amount_dzn.'='.$price_dzn;?>">
				   <? 
				   $cv=($amount_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format(($amount_dzn/$price_dzn)*100,2); 
				   ?></td>
			   </tr>
		   <?
				$total_trims_cost += $pre_trims_amount;
				$total_trims_cost_dzn += $amount_dzn;
				 $total_trims_qty += $pre_trims_qty;
		   }
		   ?>
			   <tr class="rpt_bottom" style="font-weight:bold" >
				   <td>Trims Total</td>
				   <td colspan="5" style="background:#F9F">Previous Trims cost % (As 1st Approval)=<? echo fn_number_format($trims_cost_percent,4);?></td>
				   <td align="right"><? echo fn_number_format($total_trims_qty,4); ?></td>
				   <td align="right"><? //echo fn_number_format($total_trims_cost_dzn,4); ?></td>                   
				   
				   <td align="right"><? echo fn_number_format($total_trims_cost_dzn,4); ?></td>
				   <td align="right"><? echo fn_number_format($total_trims_cost,2); ?></td>
				   <td align="right" title="<? echo $total_trims_cost_dzn.'='.$price_dzn;?>">
				   <? 
				   $cv=($total_trims_cost_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format(($total_trims_cost_dzn/$price_dzn)*100,2); 
				   ?>
				   </td>
			   </tr>                
		   </table>
	 </div>
	 <?
	//End Trims Cost Part report here -------------------------------------------	
	
	//start	Embellishment Details part report here -------------------------------------------
	
	   $sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from wo_pre_cost_embe_cost_dtls where job_no=".$txt_job_no." and status_active=1 and is_deleted=0";
	   $data_array=sql_select($sql);
   ?> 
	   <div style="margin-top:15px">
		   <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px;text-align:center;" rules="all">
		
		   <label  style="float:left;background:#CCCCCC; font-size:larger"><b>Embellishment Cost</b> </label>	
			   <tr style="font-weight:bold">
				   <td width="170">Particulars</td>
				   <td width="170">Type</td>
				   <td width="100">Cons/<?=$costing_for; ?></td>
				   <td width="120">TTL Gmts. Qty</td>
				   <td width="80">Rate</td>
				   <td width="100">Amount/<?=$costing_for; ?></td>
				   <td width="120">TTL Amount</td>
				   <td width="100">% to Ord. Value</td>
				</tr>
		   <?
		   //echo $emblishment->getQuery(); die;
		   $emblishment_qty_arr=$emblishment->getQtyArray_by_jobAndEmblishmentid();
			
		   $emblishment_amount_arr=$emblishment->getAmountArray_by_jobAndEmblishmentid();
		   $wash_qty_arr=$wash->getQtyArray_by_jobAndEmblishmentid();
		   $wash_amount_arr=$wash->getAmountArray_by_jobAndEmblishmentid();
		   $cost_per_qty_arr=$condition->getCostingPerArr();
		   
		   
		   $total_embellishment_amt=0;$total_embellishment_amt_dzn=0;  
		   foreach( $data_array as $row )
		   {
			   $em_type =""; 
			   //$total_embellishment_amt_dzn += $row[csf("amount")];
			   //$emblishment_name_array=array(1=>"Printing",2=>"Embroidery",3=>"Wash",4=>"Special Works",5=>"Others");
			   $cost_per_qty=$cost_per_qty_arr[$row[csf("job_no")]];
				if($row[csf("emb_name")]==1)$em_type = $emblishment_print_type[$row[csf("emb_type")]];
			   else if($row[csf("emb_name")]==2)$em_type = $emblishment_embroy_type[$row[csf("emb_type")]];
			   else if($row[csf("emb_name")]==3)$em_type = $emblishment_wash_type[$row[csf("emb_type")]];
			   else if($row[csf("emb_name")]==4)$em_type = $emblishment_spwork_type[$row[csf("emb_type")]];
			   else if($row[csf("emb_name")]==5)$em_type = $emblishment_gmts_type[$row[csf("emb_type")]];
			   //$row[csf("cons_dzn_gmts")] = $row[csf("cons_dzn_gmts")]/$order_price_per_dzn*$order_job_qnty;
			   //$row[csf("amount")] = $row[csf("amount")]/$order_price_per_dzn*$order_job_qnty;
			   if($row[csf("emb_name")] !=3){
				   $embl_cons_gmts=$emblishment_qty_arr[$row[csf("job_no")]][$row[csf("id")]];
				   $embl_amount=$emblishment_amount_arr[$row[csf("job_no")]][$row[csf("id")]];
			   }
			   else if($row[csf("emb_name")] ==3){
				   $embl_cons_gmts=$wash_qty_arr[$row[csf("job_no")]][$row[csf("id")]];
				   $embl_amount=$wash_amount_arr[$row[csf("job_no")]][$row[csf("id")]];
			   }
			   $embl_cons_gmts=$embl_cons_gmts;//(($embl_cons_gmts*$cost_per_qty)/$tot_plan_cut_qty)*$set_ratio;//
			   $row[csf("amount")]=($row[csf("amount")]/$cost_per_qty)*$set_ratio;//*$set_ratio
			   if(is_infinite($row[csf("amount")]) || is_nan($row[csf("amount")])){$row[csf("amount")]=0;}
			   
			   $embl_amt=$row[csf("cons_dzn_gmts")]*$row[csf("rate")];
		   ?>	 
			   <tr>
				   <td align="left"><? echo $emblishment_name_array[$row[csf("emb_name")]]; ?></td>
				   <td align="left"><? echo $em_type; ?></td>
				   <td align="right"><? echo fn_number_format($row[csf("cons_dzn_gmts")],3); ?></td>
				   <td align="right"><? echo fn_number_format($embl_cons_gmts,0); ?></td>
				   <td align="right"><? echo fn_number_format($row[csf("rate")],3); ?></td>
				   <td align="right"><? echo fn_number_format($embl_amt,4); ?></td>
				   <td align="right"><? echo fn_number_format($embl_amount,2); ?></td>
				   <td align="right" title="<? echo $embl_amt.'='.$price_dzn;?>">
				   <?
				   $cv=($embl_amt/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format(($row[csf("amount")]/$price_dzn)*100,2); 
				   ?>
				   </td>
			   </tr>
		   <?
				$total_embellishment_amt += $embl_amount;
				$total_embellishment_amt_dzn += $embl_amt;
				$total_embellishment_qty += $embl_cons_gmts;
				 $total_embellishment_qty_dzn += $row[csf("cons_dzn_gmts")];
		   }
		   ?>
			   <tr class="rpt_bottom" style="font-weight:bold">
				   <td>Embellishment Total</td> 
				   <td colspan="2"  style="background:#F9F">Previous Embel.cost % (As 1st Approval)=<? echo fn_number_format($embel_cost_percent,4);?></td>                    
				   <td align="right"><? echo fn_number_format($total_embellishment_qty,0); ?></td>
				   <td align="right"><? //echo fn_number_format($total_embellishment_qty,4); ?></td>
				   <td align="right"><? echo fn_number_format($total_embellishment_amt_dzn,4); ?></td>
				   <td align="right"><? echo fn_number_format($total_embellishment_amt,2); ?></td>
				   <td align="right" title="<? echo $total_embellishment_amt_dzn.'='.$price_dzn;?>">
				   <? 
				   $cv=($total_embellishment_amt_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format(($total_embellishment_amt_dzn/$price_dzn)*100,2); 
				   ?></td>
			   </tr>                
		   </table>
	 </div>
	 <?
	//End Embellishment Details Part report here -------------------------------------------	
	//start	Commercial Cost part report here -------------------------------------------
	  $sql = "select id, job_no, item_id, rate, amount, status_active from  wo_pre_cost_comarci_cost_dtls  where job_no=".$txt_job_no." and status_active=1 and is_deleted=0";
   $data_array=sql_select($sql);
   ?> 
	   <div style="margin-top:15px">
		   <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px;text-align:center;" rules="all">
		  
		   <label  style="float:left;background:#CCCCCC; font-size:larger"><b>Commercial Cost</b> </label>	

			   <tr style="font-weight:bold">
				   <td width="250">Particulars</td>
				   
				   <td width="200">Rate In %</td>
				   <td width="100">Amount/<?=$costing_for; ?></td>
				   <td width="100">TTL Amount</td>
				   <td width="100">% to Ord. Value</td>
				</tr>
		   <?
		   $commarcial_amount=$commercial->getAmountArray_by_jobAndPrecostdtlsid();
		   $total_commercial_cost=0;$total_commercial_cost_dzn=0;
		   foreach( $data_array as $row )
		   { 
			   //$total_commercial_cost_dzn+= $row[csf("amount")];
			   $amount = $commarcial_amount[$row[csf("job_no")]][$row[csf("id")]];
			   if(is_infinite($amount) || is_nan($amount)){$amount=0;}
			 ?>	 
			   <tr>
				   <td align="left"><? echo $camarcial_items[$row[csf("item_id")]]; ?></td>
				   <td align="right"><? echo fn_number_format($row[csf("rate")],3); ?></td>
				   <td align="right"><? echo fn_number_format($row[csf("amount")],4); ?></td>
				   <td align="right"><? echo fn_number_format($amount,2); ?></td>
				   <td align="right"  title="<? echo $$row[csf("amount")].'='.$price_dzn;?>">
				   <? 
				   $cv=($row[csf("amount")]/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format(($row[csf("amount")]/$price_dzn)*100,2); 
				   ?>
				   </td>
			   </tr>
		   <?
				$total_commercial_cost_dzn += $row[csf("amount")];
				 $total_commercial_cost += $amount;
		   }
		   ?>
			   <tr class="rpt_bottom" style="font-weight:bold">
				   <td>Commercial Total</td>                    
				   <td align="right"  style="background:#F9F">Previous Commercial % (As 1st Approval)=<?  echo fn_number_format($comm_cost_percent,4); ?></td>
				   <td align="right"><? echo fn_number_format($total_commercial_cost_dzn,4); ?></td>
				   <td align="right"><? echo fn_number_format($total_commercial_cost,2); ?></td>
				   <td align="right" title="<? echo $total_commercial_cost_dzn.'='.$price_dzn;?>">
				   <?
				   $cv=(($total_commercial_cost_dzn/$price_dzn)*100);
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format((($total_commercial_cost_dzn/$price_dzn)*100),2); 
				   ?>
				   </td>
			   </tr>                
		   </table>
	 </div>
	 <?
	//End Commercial Cost Part report here -------------------------------------------	
 
	 //start	Commission Cost part report here -------------------------------------------
	  $sql = "select id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount, status_active from  wo_pre_cost_commiss_cost_dtls  where job_no=".$txt_job_no." and status_active=1 and is_deleted=0";
   $data_array=sql_select($sql);
   ?> 
	   <div style="margin-top:15px">
		   <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px;text-align:center;" rules="all">
		 
		   <label  style="float:left;background:#CCCCCC; font-size:larger"><b>Commission Cost</b> </label>	
			   <tr style="font-weight:bold">
				   <td width="250">Particulars</td>
				   <td width="250">Commission Basis</td>
				   <td width="100">Rate</td>
				   <td width="100">Amount/<?=$costing_for; ?></td>
				   <td width="100">TTL Amount</td>
				   <td width="100">% to Ord. Value</td>
				</tr>
		   <?
		   $commission_amount_arr=$commission->getAmountArray_by_jobAndPrecostdtlsid();
		   $total_commission_cost=0;   $total_commission_cost_dzn=0;
		   foreach( $data_array as $row )
		   { 
			   $commission_amount = $commission_amount_arr[$row[csf("job_no")]][$row[csf("id")]];
			   if(is_infinite($commission_amount) || is_nan($commission_amount)){$commission_amount=0;}
			 ?>	 
			   <tr>
				   <td align="left"><? echo $commission_particulars[$row[csf("particulars_id")]]; ?></td>
				   <td align="left"><? echo $commission_base_array[$row[csf("commission_base_id")]]; ?></td>
				   <td align="right"><? echo fn_number_format($row[csf("commision_rate")],3); ?></td>
					<td align="right"><? echo fn_number_format($row[csf("commission_amount")],4); ?></td>
				   <td align="right"><? echo fn_number_format($commission_amount,2); ?></td>
				   <td align="right" title="<? echo $row[csf("commission_amount")].'='.$price_dzn;?>">
				   <? 
				   $cv=($row[csf("commission_amount")]/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format(($row[csf("commission_amount")]/$price_dzn)*100,2); 
				   ?>
				   </td>
			   </tr>
		   <?
				$total_commission_cost += $commission_amount;
				$total_commission_cost_dzn+=$row[csf("commission_amount")];
			   // $total_commission_cost_dzn+=$row[csf("commission_amount")];
		   }
		   ?>
			   <tr class="rpt_bottom" style="font-weight:bold">
				   <td>Commission Total</td>
				   <td colspan="2"  style="background:#F9F">Previous Commission % (As 1st Approval)=<? echo fn_number_format($commission_percent,4);?></td>                    
				   <td align="right"><? echo fn_number_format($total_commission_cost_dzn,4); ?></td>
				   <td align="right"><? echo fn_number_format($total_commission_cost,2); ?></td>
				   <td align="right"><? 
					//echo fn_number_format(($total_commission_cost_dzn/$price_dzn)*100,2); 
				   
				   ?></td>
			   </tr>                
		   </table>
	 </div>
	   <br/>
		 <?
   //start	Other Components part report here -------------------------------------------
	  $sql = "select id,job_no,costing_per_id,order_uom_id,fabric_cost,fabric_cost_percent,trims_cost,depr_amor_pre_cost,deffdlc_cost,studio_cost,design_cost,trims_cost_percent,embel_cost,embel_cost_percent,comm_cost,comm_cost_percent,commission,incometax_cost,interest_cost,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,common_oh,common_oh_percent,design_percent,studio_percent,currier_pre_cost, currier_percent,certificate_pre_cost, certificate_percent, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche  from wo_pre_cost_dtls where job_no=".$txt_job_no." and status_active=1 and is_deleted=0";
   $data_array=sql_select($sql);
   ?> 
		<div style="margin-top:15px">
		<table>
		<tr>
		<td>
		   <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:620px;text-align:center;" rules="all">
		   
		   <label  style="background:#CCCCCC; font-size:larger"><b style="float:left;background:#CCCCCC;">Others Cost</b> </label>	
			   <tr style="font-weight:bold">
				   <td width="220">Particulars</td>
				   <td width="100"> Cost/<?=$costing_for; ?> (As 1st Approval)</td>
				   <td width="100">Amount/<?=$costing_for; ?></td>
				   <td width="100">TTL Amount</td>
				   <td width="100">% to Ord. Value</td>
				</tr>
		   <?
		   $total_other_components=0; $lab_test_dzn=$interest_cost=$incometax_cost=0; $lab_test = 0; $inspection = 0; $cm_cost = 0; $freight = 0; $common_oh = 0; $price_dzn=0;
		  foreach( $data_array as $row )
		  { 
			   $job_no=$row[csf("job_no")];
			   $cm_cost=$other_costing_arr[$row[csf("job_no")]]['cm_cost'];
			   $freight_cost=$other_costing_arr[$job_no]['freight'];
			   $inspection_cost=$other_costing_arr[$job_no]['inspection'];
			   $certificate_cost=$other_costing_arr[$job_no]['certificate_pre_cost'];
			   $common_oh=$other_costing_arr[$job_no]['common_oh'];
			   $currier_cost=$other_costing_arr[$job_no]['currier_pre_cost'];
			   $lab_test_cost=$other_costing_arr[$job_no]['lab_test'];
			   $depr_amor_pre_cost=$other_costing_arr[$job_no]['depr_amor_pre_cost'];
			   $deffdlc_cost=$other_costing_arr[$job_no]['deffdlc_cost'];
			   $studio_cost=$other_costing_arr[$job_no]['studio_cost'];
			   $design_cost=$other_costing_arr[$job_no]['design_cost']; 
			   $interest_cost=$other_costing_arr[$job_no]['interest_cost'];
			   $incometax_cost=$other_costing_arr[$job_no]['incometax_cost'];
			   $price_dzn=$row[csf("price_dzn")];
			   //$freight = $freight_cost;
			   //$common_oh = $common_oh;
			   //$currier_pre_cost = $currier_cost;
			   //$certificate_pre_cost = $certificate_cost;
			   $lab_test_dzn=$row[csf("lab_test")];
			   $inspection_dzn=$row[csf("inspection")];
			   $cm_cost_dzn =$row[csf("cm_cost")];
			   $common_oh_dzn =$row[csf("common_oh")];
			   $freight_dzn =$row[csf("freight")];
			   $currier_pre_cost_dzn = $row[csf("currier_pre_cost")];
			   $certificate_pre_cost_dzn = $row[csf("certificate_pre_cost")];
			   $deffdlc_cost_dzn = $row[csf("deffdlc_cost")];
			   $depr_amor_pre_cost_dzn = $row[csf("depr_amor_pre_cost")];
			   $interest_cost_dzn=$row[csf("interest_cost")];
			   $incometax_cost_dzn=$row[csf("incometax_cost")];
			   $studio_cost_dzn=$row[csf("studio_cost")];
			   $design_cost_dzn=$row[csf("design_cost")];

			   
			   $studio_cost_percent=$row[csf("studio_percent")];
			   $design_cost_percent=$row[csf("design_percent")];
			  ?>	 
			   <tr>
				   <td align="left">Lab Test </td>
				   <td align="right"  style="background:#F9F"><? echo fn_number_format($lab_test_percent,3); ?></td>
				   <td align="right"><? echo fn_number_format($lab_test_dzn,4); ?></td>
				   <td align="right"><? echo fn_number_format($lab_test_cost,2); ?></td>
				   <td align="right"  title="<? echo $lab_test_dzn.'='.$price_dzn;?>">
				   <? 
				   $cv=($lab_test_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format(($lab_test_dzn/$price_dzn)*100,2); 
				   
				   ?></td>
			   </tr>
			   <tr>
				   <td align="left">Inspection Cost</td>
				   <td align="right"  style="background:#F9F"><? echo fn_number_format($inspection_percent,3); ?></td>
				   <td align="right"><? echo fn_number_format($inspection_dzn,4); ?></td>
				   <td align="right"><? echo fn_number_format($inspection_cost,2); ?></td>
				   <td align="right" title="<? echo $inspection_dzn.'='.$price_dzn;?>">
				   <? 
				   $cv=($inspection_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format(($inspection_dzn/$price_dzn)*100,2);
					?></td>
			   </tr>
			   <tr>
				   <td align="left">CM Cost</td>
				   <td align="right"  style="background:#F9F"><? echo fn_number_format($cm_cost_percent,3); ?></td>
				   <td align="right"><? echo fn_number_format($cm_cost_dzn,4); ?></td>
				   <td align="right"><? echo fn_number_format($cm_cost,2); ?></td>
				   <td align="right"><? 
				   $cv=($cm_cost_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format(($cm_cost_dzn/$price_dzn)*100,2); ?></td>
			   </tr>
			   
				<tr>
				   <td align="left">Gmts Freight Cost</td>
				   <td align="right"  style="background:#F9F"><? echo fn_number_format($freight_percent,3); ?></td>
				   <td align="right"><? echo fn_number_format($freight_dzn,4); ?></td>
				   <td align="right"><? echo fn_number_format($freight_cost,2); ?></td>
				   <td align="right"><? 
				   $cv=($freight_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format(($freight_dzn/$price_dzn)*100,2); ?></td>
			   </tr>
				<tr>
				   <td align="left">Currier Cost </td>
				   <td align="right"  style="background:#F9F"><? echo fn_number_format($currier_percent,3); ?></td>
				   <td align="right"><? echo fn_number_format($currier_pre_cost_dzn,4); ?></td>
				   <td align="right"><? echo fn_number_format($currier_cost,2); ?></td>
				   <td align="right"><? 
				   $cv=($currier_pre_cost_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format(($currier_pre_cost_dzn/$price_dzn)*100,2); ?></td>
			   </tr>
				<tr>
				   <td align="left">Certificate Cost </td>
				   <td align="right"  style="background:#F9F"><? echo fn_number_format($certificate_percent,3); ?></td>
				   <td align="right"><? echo fn_number_format($certificate_pre_cost_dzn,4); ?></td>
					<td align="right"><? echo fn_number_format($certificate_cost,2); ?></td>
				   <td align="right"><? 
				   $cv=($certificate_pre_cost_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format(($certificate_pre_cost_dzn/$price_dzn)*100,2); ?></td>
			   </tr>
			   <tr>
				   <td align="left">Deffd. LC Cost </td>
				   <td align="right"  style="background:#F9F"><? $deffdlc_cost_percent=0; echo fn_number_format($deffdlc_cost_percent,3); ?></td>
					<td align="right"><? echo fn_number_format($deffdlc_cost_dzn,4); ?></td>
					<td align="right"><? echo fn_number_format($deffdlc_cost,2); ?></td>
					<td align="right"><? 
				   $cv=($deffdlc_cost_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
					//echo fn_number_format(($deffdlc_cost_dzn/$price_dzn)*100,2); ?></td>
			   </tr>
			   <tr>
				   <td align="left">Studio Cost </td>
				   <td align="right"  style="background:#F9F"><? $studio_cost_percent=0; echo fn_number_format($studio_cost_percent,3); ?></td>
					<td align="right"><? echo fn_number_format($studio_cost_dzn,4); ?></td>
					<td align="right"><? echo fn_number_format($studio_cost,2); ?></td>
					<td align="right"><? 
				   $cv=($studio_cost_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
					//echo fn_number_format(($deffdlc_cost_dzn/$price_dzn)*100,2); ?></td>
			   </tr>
			   <tr>
				   <td align="left">Design  Cost </td>
				   <td align="right"  style="background:#F9F"><? $design_cost_percent=0; echo fn_number_format($design_cost_percent,3); ?></td>
					<td align="right"><? echo fn_number_format($design_cost_dzn,4); ?></td>
					<td align="right"><? echo fn_number_format($design_cost,2); ?></td>
					<td align="right"><? 
				   $cv=($design_cost_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
					//echo fn_number_format(($deffdlc_cost_dzn/$price_dzn)*100,2); ?></td>
			   </tr>
				<tr>
				   <td align="left">Interest </td>
				   <td align="right"  style="background:#F9F"><? echo fn_number_format($interest_cost,3); ?></td>
				   <td align="right"><? echo fn_number_format($interest_cost_dzn,4); ?></td>
				   <td align="right"><? echo fn_number_format($interest_cost,2); ?></td>
				   <td align="right"><? 
				   $cv=($interest_cost_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format(($interest_cost_dzn/$price_dzn)*100,2); ?></td>
			   </tr>
				<tr>
				   <td align="left">Income Tax </td>
				   <td align="right"  style="background:#F9F"><? echo fn_number_format($incometax_cost,3); ?></td>
				   <td align="right"><? echo fn_number_format($incometax_cost_dzn,4); ?></td>
				   <td align="right"><? echo fn_number_format($incometax_cost,2); ?></td>
				   <td align="right"><? 
				   $cv=($incometax_cost_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format(($incometax_cost_dzn/$price_dzn)*100,2); ?></td>
			   </tr>
			   <tr>
				   <td align="left">Operating Expensees</td>
				   <td align="right"  style="background:#F9F"><? echo fn_number_format($common_oh_percent,3); ?></td>
				   <td align="right"><? echo fn_number_format($common_oh_dzn,4); ?></td>
				   <td align="right"><? echo fn_number_format($common_oh,2); ?></td>
				   <td align="right"><? 
				   $cv=($common_oh_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format(($common_oh_dzn/$price_dzn)*100,2); ?></td>
			   </tr>
				<tr>
				   <td align="left">Depreciation & Amortization </td>
				   <td align="right"><? //echo fn_number_format($common_oh,4); ?></td>
				   <td align="right"><? echo fn_number_format($depr_amor_pre_cost_dzn,4); ?></td>
				   <td align="right"><? echo fn_number_format($depr_amor_pre_cost,2); ?></td>
				   <td align="right"><? 
				   $cv=($depr_amor_pre_cost_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format(($depr_amor_pre_cost_dzn/$price_dzn)*100,2); ?></td>
			   </tr>
		   <?
				$total_other_components_dzn = $lab_test_dzn+$inspection_dzn+$cm_cost_dzn+$freight_dzn+$currier_pre_cost_dzn+$certificate_pre_cost_dzn+$deffdlc_cost_dzn+$interest_cost_dzn+$incometax_cost_dzn+$common_oh_dzn+$depr_amor_pre_cost_dzn+$design_cost_dzn+$studio_cost_dzn;
				  $total_other_components =$lab_test_cost+$inspection_cost+$cm_cost+$freight_cost+$currier_cost+$certificate_cost+$deffdlc_cost+$interest_cost+$incometax_cost+$common_oh+$depr_amor_pre_cost+$design_cost+$studio_cost;
		  }
		   ?>
			   <tr class="rpt_bottom" style="font-weight:bold">
				   <td>Others Total</td>                    
				   <td align="right"><? // echo fn_number_format($total_other_components,4); ?></td>
				   <td align="right"><? echo fn_number_format($total_other_components_dzn,4); ?></td>
				   <td align="right"><? echo fn_number_format($total_other_components,2); ?></td>
				   <td align="right"><? 
				   $cv=($total_other_components_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format(($total_other_components_dzn/$price_dzn)*100,2); ?></td>
			   </tr> 
		   </table>
		   </td>
				  <td colspan="5"   valign="top">
					 <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:260px; margin-top:20px" rules="all">
					<?
					   // image show here  -------------------------------------------
						$sql = "select id,master_tble_id,image_location from common_photo_library   where master_tble_id=$txt_job_no and file_type=1  and rownum=1 ";
					   $photo_data_array=sql_select($sql);
					?> 
				   <? foreach($photo_data_array AS $inf){ ?>
						 <tr class="rpt_bottom" style="font-weight:bold">
						   <td align="center"><img  src='../../<? echo $inf[csf("image_location")]; ?>' height='250px' width='250px' /></td>
						</tr>
				   <?  } ?>			
	   
				 </table>
				  </td>
		 </tr>
		 </table>
		<br/>
	 <?
		   //End Commission Cost Part report here -------------------------------------------	
		 
		   //start	all summary report here -------------------------------------------
		   //job_no $txt_po_breack_down_id_cond $company_name $cbo_buyer_name $txt_style_ref
			$sql = "select fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent,certificate_pre_cost,certificate_percent,common_oh,common_oh_percent,total_cost,deffdlc_cost,deffdlc_percent,interest_cost,interest_percent,incometax_cost,incometax_percent,total_cost_percent,price_dzn,price_dzn_percent,margin_dzn,margin_dzn_percent,price_pcs_or_set,depr_amor_pre_cost,depr_amor_po_price,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche
				   from wo_pre_cost_dtls
				   where job_no=".$txt_job_no." and status_active=1 and is_deleted=0";
				   
		   $data_array=sql_select($sql);
		   $others_cost_value=0; $fabric_cost=0; $trims_cost=0; $embel_cost=0; $comm_cost=0; $commission=0; $lab_test=0; $inspection=0; $cm_cost=0; $freight=0; $currier_pre_cost=0; $certificate_pre_cost=0; $common_oh=0;
	?>

	   <div style="margin-top:15px">
		<table>
		<tr>
		<td>
		   <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:650px;text-align:center;" rules="all">
		   <caption><label  style="background:#CCCCCC; font-size:larger"><b style="float:left;background:#CCCCCC;">Order Profitability Summary</b> </label> </caption>	
			   <tr style="font-weight:bold">
				   <td width="80">SL</td>
				   <td width="250">Particulars</td>
				   <td width="100">Amount/<?=$costing_for; ?></td>
				   <td width="100">TTL Amount</td>
				   <td width="100" title="(Net FOB Value DZN/ Per DZN Order Value)*100">% to Ord. Value</td>                     
			   </tr>
		   <?
		   $cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=22 and status_active=1 and is_deleted=0");
		   //echo $cm_cost_method_based_on.'AAAAAAAAAA';
		   if($cm_cost_method_based_on=="") $cm_cost_method_based_on=0;else $cm_cost_method_based_on=$cm_cost_method_based_on;
		   if($cm_cost_method_based_on==1)
		   {
			   $based_on_date=return_field_value("costing_date", "wo_pre_cost_mst", "job_no=".$txt_job_no." and status_active=1 and is_deleted=0 ");
		   }
		   else if($cm_cost_method_based_on==2)
		   {
				   $based_on_date=return_field_value("min(shipment_date) as shipment_date", "wo_po_break_down", "job_no_mst=".$txt_job_no." and status_active=1 and is_deleted=0 ","shipment_date");
				   //echo $based_on_date.'GGGGGGG';
		   }
		   else if($cm_cost_method_based_on==3)
		   {
				   $based_on_date=return_field_value("max(shipment_date) as shipment_date", "wo_po_break_down", "job_no_mst=".$txt_job_no." and status_active=1 and is_deleted=0 ","shipment_date");
		   }
		   else if($cm_cost_method_based_on==4)
		   {
				   $based_on_date=return_field_value("min(pub_shipment_date) as pub_shipment_date", "wo_po_break_down", "job_no_mst=".$txt_job_no." and status_active=1 and is_deleted=0 ","pub_shipment_date");
		   }
		   else if($cm_cost_method_based_on==5)
		   {
				   $based_on_date=return_field_value("max(pub_shipment_date) as pub_shipment_date", "wo_po_break_down", "job_no_mst=".$txt_job_no." and status_active=1 and is_deleted=0 ","pub_shipment_date");
		   }
		   else
		   {
			   $based_on_date=return_field_value("costing_date", "wo_pre_cost_mst", "job_no=".$txt_job_no." and status_active=1 and is_deleted=0 ");
		   }
		   
		   $based_on_date_new =change_date_format($based_on_date,'','',1);
		   //echo $cm_cost_method_based_on.'='.$based_on_date_new.'fddf';
		   
		   $financial_para=array();
		   $sql_std_para=sql_select("select cost_per_minute,interest_expense,income_tax,applying_period_date as from_period_date,applying_period_to_date from lib_standard_cm_entry where company_id=$cbo_company_name and status_active=1 and is_deleted=0  order by id desc");	
		   foreach($sql_std_para as $row)
		   {
			   $applying_period_date=change_date_format($row[csf('from_period_date')],'','',1);
			   $applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			   $diff=datediff('d',$applying_period_date,$applying_period_to_date);
			   for($j=0;$j<$diff;$j++)
			   {
				   $newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
				   if($based_on_date_new==$newdate)
				   {
					   //echo $row[csf('cost_per_minute')].',';
					   $financial_para[$newdate]['cost_per_minute']=$row[csf('cost_per_minute')];
					   $financial_para[$newdate]['interest_expense']=$row[csf('interest_expense')];
					   $financial_para[$newdate]['income_tax']=$row[csf('income_tax')];
				   }
			   }
		   }
		   //$row[csf("sew_effi_percent")]
		   $cpm_budget=(($financial_para[$based_on_date_new]['cost_per_minute']/$exchange_rate)/$sew_effi_percent)*100;
		   if(is_infinite($cpm_budget) || is_nan($cpm_budget)){$cpm_budget=0;}
		   //	echo $based_on_date_new.'='.$financial_para[$based_on_date_new]['cost_per_minute'].'='.$exchange_rate.'='.$sew_effi_percent;
		   $price_dzn=0;
		   $sl=0;
		   foreach( $data_array as $row )
		   { 
		   //$fab_production_knit=$fabric_costing_arr['knit']['grey'][$job_no];
		   //$fab_production_finish=$fabric_costing_arr['finish']['grey'][$job_no];
		   
		   $fab_purchase_knit2=array_sum($fabric_costing_arr2['knit']['grey'][$job_no]);
		   if(is_infinite($fab_purchase_knit2) || is_nan($fab_purchase_knit2)){$fab_purchase_knit2=0;}
		   $fab_purchase_woven2=array_sum($fabric_costing_arr2['woven']['grey'][$job_no]);
		   if(is_infinite($fab_purchase_woven2) || is_nan($fab_purchase_woven2)){$fab_purchase_woven2=0;}
	   
		   $yarn_costing=$yarn_costing_arr[$job_no];
		   $tot_fabric_cost=$fab_purchase_knit2+$fab_purchase_woven2;
		   $conversion_cost=array_sum($conversion_costing_arr_process[$job_no]);
		   if(is_infinite($conversion_cost) || is_nan($conversion_cost)){$conversion_cost=0;}
		   //$testing_cost=$other_costing_arr[$job_no]['lab_test'];
		   $freight_cost=$other_costing_arr[$job_no]['freight'];
		   if(is_infinite($freight_cost) || is_nan($freight_cost)){$freight_cost=0;}
		   $inspection_cost=$other_costing_arr[$job_no]['inspection'];
		   if(is_infinite($inspection_cost) || is_nan($inspection_cost)){$inspection_cost=0;}
		   $certificate_cost=$other_costing_arr[$job_no]['certificate_pre_cost'];
		   if(is_infinite($certificate_cost) || is_nan($certificate_cost)){$certificate_cost=0;}
		   $common_oh=$other_costing_arr[$job_no]['common_oh'];
		   if(is_infinite($common_oh) || is_nan($common_oh)){$common_oh=0;}
		   $currier_cost=$other_costing_arr[$job_no]['currier_pre_cost'];
		   if(is_infinite($currier_cost) || is_nan($currier_cost)){$currier_cost=0;}
		   $cm_cost=$other_costing_arr[$job_no]['cm_cost'];
		   if(is_infinite($cm_cost) || is_nan($cm_cost)){$cm_cost=0;}
		   $lab_test_cost=$other_costing_arr[$job_no]['lab_test'];
		   if(is_infinite($lab_test_cost) || is_nan($lab_test_cost)){$lab_test_cost=0;}
		   $depr_amor_pre_cost=$other_costing_arr[$job_no]['depr_amor_pre_cost'];
		   if(is_infinite($depr_amor_pre_cost) || is_nan($depr_amor_pre_cost)){$depr_amor_pre_cost=0;}
		   $deffdlc_cost=$other_costing_arr[$job_no]['deffdlc_cost'];
		   if(is_infinite($deffdlc_cost) || is_nan($deffdlc_cost)){$deffdlc_cost=0;}
		   $fabric_cost=$tot_fabric_cost;
		   $trims_cost=$trims_costing_arr[$job_no];
		   $embel_cost=$emblishment_costing_arr[$job_no];
		   $wash=$emblishment_costing_arr_wash[$job_no];
		   $commercial_cost=$commercial_costing_arr[$job_no];
		   $comm_cost_dzn=$row[csf("comm_cost")];
		   $cm_cost_dzn=$row[csf("cm_cost")];
		   $price_dzn=$row[csf("price_dzn")];
		   $total_cost_dzn=$row[csf("total_cost")];
		   $deffdlc_cost_dzn=$row[csf("deffdlc_cost")];
		   $interest_cost_dzn=$row[csf("interest_cost")];
		   $incometax_cost_dzn=$row[csf("incometax_cost")];
		   $commission_dzn=$row[csf("commission")];
		   $operatin_expense_dzn=$row[csf("common_oh")];
		   //deffdlc_cost,deffdlc_percent,interest_cost,interest_percent,incometax_cost,incometax_percent
		   $deffdlc_percent=$row[csf("deffdlc_percent")];
		   $interest_percent=$row[csf("interest_percent")];
		   $incometax_percent=$row[csf("incometax_percent")];
		   $depr_amor_po_price=$row[csf("depr_amor_po_price")];
		   $depr_amor_pre_cost_dzn=$row[csf("depr_amor_pre_cost")];
		   //interest_cost,interest_percent,incometax_cost
		   $tot_commission=$commission_costing_arr[$job_no];
		   $lab_test=$lab_test_cost;
		   $inspection=$inspection_cost;
		   $cm_cost=$cm_cost;
		   $freight=$freight_cost;
		   $currier_pre_cost=$currier_cost;
		   $certificate_pre_cost=$certificate_cost;
		   $sl=$sl+1;
			 $others_cost_value=$all_total_cost-$cm_cost-$freight-$commercial_cost-$commission;
		   $price_dzn=0;
		   
		   $price_dzn=($total_po_value/$total_po_qty)*$order_price_per_dzn;
   ?>	 
			   <tr> 
				   <td><? echo $sl; ?></td>
				   <td align="left"><b>Gross FOB Value/ <?=$costing_for; ?></b></td>
					 <td align="right" title="Order value/Order Qty*Costing Per"><b><? echo fn_number_format($price_dzn,4); ?></b></td>
				   <td align="right"><b><? echo fn_number_format($total_po_value,2);//$price_dzn=$tot_order_value; ?></b></td>
				   <td align="center"><? echo "100.00%"; ?></td>
				   
			   </tr>
			   <tr>
				   <td><? echo ++$sl; ?></td>
				   <td align="left">Less Commission</td>
				   <td align="right"><? echo fn_number_format($commission_dzn,4); ?></td>
				   <td align="right"><? echo fn_number_format($tot_commission,2); ?></td>
				   <td align="center"><? echo fn_number_format(($commission_dzn/$price_dzn)*100,2); ?>%</td>
			   </tr>
			 
			   <tr>
				   <td><? echo ++$sl; ?></td>
				   <td align="left">Net FOB Value (1-2)</td>
				   <td align="right">
				   <? 
					   $net_fob_value_dzn=$price_dzn-$commission_dzn;  echo fn_number_format($net_fob_value_dzn,4);
						
				   ?>
				   </td>
					<td align="right"><? $net_fob_value=$total_po_value-$tot_commission;echo fn_number_format($net_fob_value,2); ?></td>
				   <td align="center" ><? 
				   $cv=($net_fob_value_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format(($net_fob_value_dzn/$price_dzn)*100,2); ?>%</td>
			   </tr>
			   <tr>
				   <td><? echo ++$sl; ?></td>
				   <td align="left">Less: Material & Services Cost</td>
				   <td align="right"><? $total_material_services_dzn=$total_fab_cost_dzn+$total_trims_cost_dzn+$total_embellishment_amt_dzn+$lab_test_dzn+$inspection_dzn+$freight_dzn+$currier_pre_cost_dzn+$certificate_pre_cost_dzn+$deffdlc_cost_dzn;
								   $total_material_services=$total_trims_cost+$all_fab_cost+$total_embellishment_amt+$lab_test_cost+$inspection_cost+$freight_cost+$currier_cost+$certificate_cost+$deffdlc_cost;

					   echo fn_number_format($total_material_services_dzn,4); ?></td>
					<td align="right"><? echo fn_number_format($total_material_services,2); ?></td>
				   <td align="center"><? 
				   $cv=($total_material_services_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format(($total_material_services_dzn/$price_dzn)*100,2);//fn_number_format($row[csf("embel_cost_percent")],2); ?>%</td>
			   </tr>
				<tr>
				   <td><? echo ++$sl; ?></td>
				   <td align="left">Less: Commercial Cost</td>
				   <td align="right"><? echo fn_number_format($comm_cost_dzn,4); ?></td>
					<td align="right"><? echo fn_number_format($commercial_cost,2); ?></td>
				   <td align="center"><? 
				   $cv=($comm_cost_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format(($comm_cost_dzn/$price_dzn)*100,2); ?>%</td>
			   </tr>
			   
			   <tr>
				   <td><? echo ++$sl; ?></td>
				   <td align="left">Contribution Margin/CM Value (3-4-5)</td>
				   <td align="right">
				   <? 
					   $contributions_value_dzn=$net_fob_value_dzn-$total_material_services_dzn-$comm_cost_dzn;
						   $contributions_value=$net_fob_value-$total_material_services-$commercial_cost;
						   echo fn_number_format($contributions_value_dzn,4); 
						   $precostData['CMValue']=$contributions_value;
						   
				   ?>
				   </td>
				   <td align="right"><? echo fn_number_format($contributions_value,2); ?></td>
				   <td align="center"><? 
				   $cv=($contributions_value_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format(($contributions_value_dzn/$price_dzn)*100,2); ?>%</td>
			   </tr>
			   <tr>
				   <td><? echo ++$sl; ?></td>
				   <td align="left">Less: CM Cost </td>
				   <td align="right"><? echo fn_number_format($cm_cost_dzn,4); ?></td>
					<td align="right"><? echo fn_number_format($cm_cost,2); ?></td>
				   <td align="center"><? 
				   $cv=($cm_cost_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format(($cm_cost_dzn/$price_dzn)*100,2); ?>%</td>
			   </tr>
			   <tr>
				   <td><? echo ++$sl; ?></td>
				   <td align="left">Gross Profit(6-7)</td>
				   <td align="right"><? $gross_profit_dzn=$contributions_value_dzn-$cm_cost_dzn;
				   $gross_profit_loss=$contributions_value-$cm_cost;
				   
				   echo fn_number_format($gross_profit_dzn,4); ?></td>
					<td align="right"><? echo fn_number_format($gross_profit_loss,2); ?></td>
				   <td align="center"><? 
				   $cv=($gross_profit_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format(($gross_profit_dzn/$price_dzn)*100,2); ?>%</td>
			   </tr>
			  
			   <tr>
				   <td><? echo ++$sl; ?></td>
				   <td align="left">Less: Operating Expensees</td>
				   <td align="right"><? echo fn_number_format($operatin_expense_dzn,4); ?></td>
				   <td align="right"><? echo fn_number_format($common_oh,2); ?></td>
				   <td align="center"><? 
				   $cv=($operatin_expense_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format(($operatin_expense_dzn/$price_dzn)*100,2); ?>%</td>
				</tr>
			   <tr>
				   <td><? echo ++$sl; ?></td>
				   <td align="left">Operating Profit/Loss [8-(9)]</td>
				   <td align="right">
				   <? 
				   $operating_profit_dzn=$gross_profit_dzn-($operatin_expense_dzn);
				   $operating_profit_loss=$gross_profit_loss-($operatin_expense_dzn);
				   echo fn_number_format($operating_profit_dzn,4); 
				   ?>
				   </td>
				   <td align="right"><? echo fn_number_format($operating_profit_loss,2); ?></td>
				   <td align="center"><? 
				   $cv=($operating_profit_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format(($operating_profit_dzn/$price_dzn)*100,2); ?>%</td>
				</tr>
			   <tr> 
				   <td><? echo ++$sl; ?></td>
				   <td align="left">Less: Depreciation & Amortization </td>
				   <td align="right"><? echo fn_number_format($depr_amor_pre_cost_dzn,4); ?></td>
				   <td align="right"><? echo fn_number_format($depr_amor_pre_cost,2); ?></td>
				   <td align="center"><? 
				   $cv=($depr_amor_pre_cost_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format(($depr_amor_pre_cost_dzn/$price_dzn)*100,2); ?>%</td>
				</tr>
			   <tr>
				   <td><? echo ++$sl; ?></td>
				   <td align="left">Less: Interest</td>
				   <td align="right"><? echo fn_number_format($interest_cost_dzn,4); ?></td>
				   <td align="right"><? //echo fn_number_format($interest_cost_dzn,4); ?></td>
				   <td align="center"><? 
				   $cv=($interest_cost_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format(($interest_cost_dzn/$price_dzn)*100,2); ?>%</td>
				</tr>
				<tr>
				   <td><? echo ++$sl; ?></td>
				   <td align="left">Less: Income Tax</td>
				   <td align="right"><? echo fn_number_format($incometax_cost_dzn,4); ?></td>
					<td align="right"><? //echo fn_number_format($incometax_cost_dzn,4); ?></td>
				   <td align="center"><? 
				   $cv=($incometax_cost_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
				   //echo fn_number_format(($incometax_cost_dzn/$price_dzn)*100,2); ?>%</td>
				</tr>
				 <tr>
				   <td><? echo ++$sl; ?></td>
				   <td align="left">Net Profit [10-(11+12+13)]</td>
				   <td align="right"><? $net_profit_dzn=$operating_profit_dzn-($depr_amor_pre_cost_dzn+$interest_cost_dzn+$incometax_cost_dzn);
				   $net_profit=$operating_profit_loss-($depr_amor_pre_cost);
				   echo fn_number_format($net_profit_dzn,4); ?></td>
					<td align="center"><? echo fn_number_format($net_profit,2); ?></td>
					<td align="center" title="Net Profit Dzn/Tot Fob Dzn*100">
					<? 
				   $cv=($net_profit_dzn/$price_dzn)*100;
				   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				   echo fn_number_format($cv,2); 
					//echo fn_number_format(($net_profit_dzn/$price_dzn)*100,2); ?>%</td>
				</tr>
			   <tr>
				   <td><? echo ++$sl; ?></td>
				   <td align="left"  title="Contribution Mergin /<?=$order_price_per_dzn;?>/Tot SMV(<?  echo $pre_costing_smv;?>)">EPM (Per Pcs CM/SMV)</td>
					<td align="right" colspan="3"><? 
					$epm_per_pcs=($contributions_value_dzn/$order_price_per_dzn)/$pre_costing_smv;
					if(is_infinite($epm_per_pcs) || is_nan($epm_per_pcs)){$epm_per_pcs=0;}
					echo fn_number_format($epm_per_pcs,4); ?></td>
				</tr>
				 <tr>
				   <td><? echo ++$sl; ?></td>
				   <td align="left">CPM </td>
				   <td align="right" colspan="3" title="CPM/Exchange Rate/Efficiency %"><?  echo fn_number_format($cpm_budget,4); ?></td>
				</tr>
		   <?
		   }
		   $total_job_cost=$total_trims_cost+$all_fab_cost+$total_embellishment_amt+$total_other_components+$total_commercial_cost+$total_commission_cost;


       $total_job_cost222='Trim:'.$total_trims_cost."<br>all Fab: ".$all_fab_cost."<br>emb: ".$total_embellishment_amt."<br>Other: ".$total_other_components."<br>commercial: ".$total_commercial_cost."<br>commi: ".$total_commission_cost;

 

		   ?>
		   </table>
		   </td>
		   <td valign="top"> 
			<div style="margin-top:0px;">
			   <table class="rpt_table" border="1" cellpadding="1" cellspacing="1"style="width:320px;text-align:center;" rules="all">
			   <label><b>Cost Summary on Total Order Qty</b></label>
				   <tr style="font-weight:bold">
					   <td width="130">Particulars</td>
					   <td width="80">Amount (USD)</td>
					   <td width="80">%</td>
				   </tr>            
				   <tr>
					   <td align="left">Total Order Value </td>
					   <td align="right"><? 
						echo fn_number_format($total_po_value,2); ?></td>
					   <td align="center"><? 
					   $cv=$total_po_value/$total_po_value*100;
					   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					   echo fn_number_format($cv,2); 
					   //echo fn_number_format($total_po_value/$total_po_value*100,2); ?></td>
				   </tr> 
					<tr>
					   <td align="left">Total Cost </td>
					   <td align="right" title="Total Trims+Total All Fabric Cost+Emblish+Other+Commercail+Commission "><?  //$total_cost = $total_job_cost;//$all_total_cost; 
					   echo fn_number_format($total_job_cost,2);//$total_cost = $row[csf("total_cost")]/$order_price_per_dzn*$order_job_qnty; echo fn_number_format($total_cost,4); ?></td>
					   <td align="center"><? 
					   $cv=$total_job_cost/$total_po_value*100;
					   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					   echo fn_number_format($cv,2); 
					   //echo fn_number_format($total_job_cost/$total_po_value*100,2); ?></td>
				   </tr>
					<tr>
					   <td align="left">Total Margin </td>
					   <td align="right" title="Total Order Value-Total Cost=(<?=$total_po_value.'-'.$total_job_cost; ?>)">
					   
					   
					   <? 
					   $total_margin_val = $total_po_value-$total_job_cost; 
					   echo fn_number_format($total_margin_val,2); 
					   
					 
             $precostData['xxxxxxxxxxx']= $total_job_cost222;
            // $precostData['po_value']=$total_po_value;
             $precostData['job_cost']=$total_job_cost;
            // $precostData['TotalMargin']=$total_margin_val;
					    
					   ?>
					   
					   
					   </td>
					   <td align="center"><? 
					   $cv=($total_margin_val/$total_po_value*100);
					   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					   echo fn_number_format($cv,2); 
					   //echo fn_number_format(($total_margin_val/$total_po_value*100),2); 
					   
						   
					   ?></td>
				   </tr>
					<tr>
					   <td align="left">Margin /<?=$costing_for; ?> </td>
					   <td align="right" title="Tot Margin/PO Qty*12"><?
						   $ord_val_dzn_per=($job_po_rate_set*12);
						   
						$margin_dzn=($total_margin_val/$total_po_qty)*12; 
						if(is_infinite($margin_dzn) || is_nan($margin_dzn)){$margin_dzn=0;}
						echo fn_number_format($margin_dzn,4); ?></td>
					   <td align="center" title="<? echo $ord_val_dzn_per.'=Unit Rate*12'; ?>">
					   <? 
					   $cv=($margin_dzn/$ord_val_dzn_per)*100;
					   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					   echo fn_number_format($cv,2); 
					   //echo fn_number_format(($margin_dzn/$ord_val_dzn_per)*100,2);
					   
						 $margin_pcs=$margin_dzn/12;
						 if(is_infinite($margin_pcs) || is_nan($margin_pcs)){$margin_pcs=0;}
						?></td>
				   </tr>
				   <tr>
					   <td align="left">Margin /Pcs </td>
					   <td align="right" title="Margin Pcs=<? echo $margin_dzn/12;?>"><? echo fn_number_format($margin_pcs,4); ?></td>
					   <td align="center" title="Rate=<? echo $job_po_rate;?>"><? 
					   $cv=($margin_pcs/$job_po_rate_set)*100;
					   if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					   echo fn_number_format($cv,2); 
					   //echo fn_number_format(($margin_pcs/$job_po_rate_set)*100,2); ?></td>
				   </tr>
				  </table>
				</div>
		   </td>
		   </tr>
	   </table>
	 </div>
	<br/>
	<? // echo signature_table(109, $cbo_company_name, "970px");?>
	</div>
	<?
	disconnect($con);
	//ob_get_contents();
	ob_clean();
	
	 return $precostData;						

}

?>