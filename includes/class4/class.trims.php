<?
class trims extends report{
	private $_By_JobAndPrecostdtlsid='By_JobAndPrecostdtlsid';
	private $_By_JobAndItemid='By_JobAndItemid';
	private $_By_JobSourcing='By_JobSourcing';
	private $_By_JobItemidAndDescription='By_JobItemidAndDescription';
	private $_By_JobItemidDescriptionGmtcolorAndSizeid='By_JobItemidDescriptionGmtcolorAndSizeid';

	private $_By_OrderAndPrecostdtlsidSourcing='By_OrderAndPrecostdtlsidSourcing';
	private $_By_OrderAndPrecostdtlsid='By_OrderAndPrecostdtlsid';
	private $_By_OrderAndItemid='By_OrderAndItemid';
	private $_By_OrderSourceidAndItemid='By_OrderSourceidAndItemid';
	private $_By_OrderSourceidAndItemidType='By_OrderSourceidAndItemidType';
	private $_By_OrderAndSourceid='By_OrderAndSourceid';
	private $_By_OrderAndItemidTypeid='By_OrderAndItemidTypeid';
	private $_By_OrderAndTrimsTypeid='By_OrderAndTrimsTypeid';
	private $_By_OrderItemidAndDescription='By_OrderItemidAndDescription';

	private $_By_OrderCountryAndPrecostdtlsid='By_OrderCountryAndPrecostdtlsid';
	private $_By_OrderCountryAndItemid='By_OrderCountryAndItemid';
	private $_By_OrderCountryItemidAndDescription='By_OrderCountryItemidAndDescription';

	private $_By_Itemid='By_Itemid';
	private $_By_ItemidAndDescription='By_ItemidAndDescription';
	private $_By_Precostdtlsid='By_Precostdtlsid';
	private $_By_PrecostdtlsidSourcing='By_PrecostdtlsidSourcing';
	private $_By_PrecostdtlsidAndCountry='By_PrecostdtlsidAndCountry';
	private $_By_PrecostdtlsidAndCountrySourcing='By_PrecostdtlsidAndCountrySourcing';
	private $_By_Precostdtlsid_consAndTotcons='By_Precostdtlsid_consAndTotcons';
	private $_By_PrecostdtlsidAndGmtssize_consAndTotcons='By_PrecostdtlsidAndGmtssize_consAndTotcons';

	private $_By_JobAndPrecostdtlsid_consAndTotcons='By_JobAndPrecostdtlsid_consAndTotcons';

	protected $_By_orderCountryGmtsitemAndPrecostdtlsid='By_orderCountryGmtsitemAndPrecostdtlsid';
	protected $_By_orderCountryGmtsitemAndPrecostdtlsidSourcing='By_orderCountryGmtsitemAndPrecostdtlsidSourcing';

	private $_By_OrderPrecostdtlsidAndGmtscolor='By_OrderPrecostdtlsidAndGmtscolor';
	private $_By_OrderCountryPrecostdtlsidAndGmtscolor='By_OrderCountryPrecostdtlsidAndGmtscolor';
	private $_By_OrderCountryPrecostdtlsidAndGmtscolorSourcing='By_OrderCountryPrecostdtlsidAndGmtscolorSourcing';
	
	private $_By_OrderPrecostdtlsidAndGmtssize='By_OrderPrecostdtlsidAndGmtssize';
	private $_By_OrderCountryPrecostdtlsidAndGmtssize='By_OrderCountryPrecostdtlsidAndGmtssize';
	private $_By_OrderCountryPrecostdtlsidAndGmtssizeSourcing='By_OrderCountryPrecostdtlsidAndGmtssizeSourcing';
	private $_By_OrderPrecostdtlsidGmtscolorAndGmtssize='By_OrderPrecostdtlsidGmtscolorAndGmtssize';
	private $_By_PrecostdtlsidGmtscolorAndItemSize='By_PrecostdtlsidGmtscolorAndItemSize';
	private $_By_OrderCountryPrecostdtlsidGmtscolorAndGmtssize='By_OrderCountryPrecostdtlsidGmtscolorAndGmtssize';
		private $_By_OrderCountryPrecostdtlsidGmtscolorAndGmtssizeSourcing='By_OrderCountryPrecostdtlsidGmtscolorAndGmtssizeSourcing';

	protected $_By_orderCountryGmtsitemGmtscolorAndPrecostdtlsid='By_orderCountryGmtsitemGmtscolorAndPrecostdtlsid';
	protected $_By_orderCountryGmtsitemGmtssizeAndPrecostdtlsid='By_orderCountryGmtsitemGmtssizeAndPrecostdtlsid';
	protected $_By_orderCountryGmtsitemGmtscolorGmtssizeAndPrecostdtlsid='By_orderCountryGmtsitemGmtscolorGmtssizeAndPrecostdtlsid';

	private $_By_OrderPrecostdtlsidGmtscolorGmtssizeAndArticle='By_OrderPrecostdtlsidGmtscolorGmtssizeAndArticle';
	private $_By_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColor='By_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColor';
	private $_By_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColorItemSize='By_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColorItemSize';
	
	
	private $_By_OrderPrecostdtlsidGmtssizeAndArticle='By_OrderPrecostdtlsidGmtssizeAndArticle';
	private $_By_OrderPrecostdtlsidGmtssizeItemsizeAndArticle='By_OrderPrecostdtlsidGmtssizeItemsizeAndArticle';
	private $_By_OrderItemidGmtscolorAndrGmtssize='By_OrderItemidGmtscolorAndrGmtssize';


	private $_query="";
	private $_dataArray=array();
	private $_is_sweater=0;
	private $_is_approval_histry=0;
	// class constructor
	function __construct(condition $condition,$is_sweater=0,$is_approval_histry=0){
		parent::__construct($condition);
		$this->_is_sweater=$is_sweater;
		$this->_is_approval_histry=$is_approval_histry;
		$this->_setQuery();
		$this->_setData();
	}// end class constructor

	private function _setQuery(){
		//$jobcond=$this->_setJobsString($this->_jobs,'a.job_no');
		//$pocond=$this->_setPoIdsString($this->_poIds, 'b.id');
		/*$this->_query='select a.job_no AS "job_no",a.total_set_qnty AS "total_set_qnty",b.id AS "id",c.item_number_id AS "item_number_id",c.country_id AS "country_id",c.color_number_id AS "color_number_id",c.size_number_id "size_number_id",c.article_number AS "article_number", c.order_quantity AS "order_quantity" ,c.plan_cut_qnty AS "plan_cut_qnty" ,c.size_order AS "size_order",d.id AS "pre_cost_dtls_id",d.trim_group AS "trim_group",d.description AS "description" ,d.cons_uom AS "cons_uom",d.cons_dzn_gmts "cons_dzn_gmts",d.rate AS "rateMst",d.amount AS "amount", e.cons AS "cons",e.tot_cons AS "tot_cons",e.country_id AS "country_id_trims" ,e.rate AS "rate" from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where 1=1 '.$this->cond.' and a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by c.size_order';//order by b.id,d.id*/
		if($this->_is_approval_histry==2 && $this->_is_sweater==0 ){
			$this->_query='select a.job_no AS "job_no",a.total_set_qnty AS "total_set_qnty",b.po_id AS "id",c.item_number_id AS "item_number_id",c.country_id AS "country_id",c.color_number_id AS "color_number_id",c.size_number_id "size_number_id",c.article_number AS "article_number", c.order_quantity AS "order_quantity" ,c.plan_cut_qnty AS "plan_cut_qnty" ,c.size_order AS "size_order",d.pre_cost_trim_cost_dtls_id AS "pre_cost_dtls_id",d.trim_group AS "trim_group",d.source_id AS "source_id",d.description AS "description" ,d.cons_uom AS "cons_uom",d.cons_dzn_gmts "cons_dzn_gmts",d.rate AS "rateMst",d.amount AS "amount", e.cons AS "cons",e.tot_cons AS "tot_cons",e.country_id AS "country_id_trims" ,e.rate AS "rate",e.sourcing_rate AS "sourcing_rate",e.item_color_number_id as "item_color",e.item_size as "item_size"
			from wo_po_dtls_mst_his a, wo_po_break_down_his b,wo_po_color_size_his c, wo_pre_cost_trim_cost_dtls_his d, wo_pre_cost_trim_co_cons_dtl_h e
			where 1=1 '.$this->cond.' and a.job_id=b.job_id and b.po_id=c.po_break_down_id and a.job_id=d.job_id and d.job_id = e.job_id AND c.job_id = d.job_id AND a.job_id = e.job_id and d.pre_cost_trim_cost_dtls_id=e.wo_pre_cost_trim_cost_dtls_id  and a.approved_no=b.approved_no and a.approval_page=b.approval_page and a.approved_no=c.approved_no and a.approval_page=c.approval_page and a.approved_no = d.approved_no and d.approved_no=e.approved_no and d.approval_page=e.approval_page   and b.po_id=e.po_break_down_id  and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and e.cons>0  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1) and c.is_deleted=0 and c.status_active in(1) and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 ';//order by c.size_order
		}
		else{
			$this->_query='select a.job_no AS "job_no",a.total_set_qnty AS "total_set_qnty",b.id AS "id",c.item_number_id AS "item_number_id",c.country_id AS "country_id",c.color_number_id AS "color_number_id",c.size_number_id "size_number_id",c.article_number AS "article_number", c.order_quantity AS "order_quantity" ,c.plan_cut_qnty AS "plan_cut_qnty" ,c.size_order AS "size_order",d.id AS "pre_cost_dtls_id",d.trim_group AS "trim_group",d.source_id AS "source_id",d.description AS "description" ,d.cons_uom AS "cons_uom",d.cons_dzn_gmts "cons_dzn_gmts",d.rate AS "rateMst",d.amount AS "amount", e.cons AS "cons",e.tot_cons AS "tot_cons",e.country_id AS "country_id_trims" ,e.rate AS "rate",e.sourcing_rate AS "sourcing_rate",e.item_color_number_id as "item_color",e.item_size as "item_size"
			from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e
			where 1=1 '.$this->cond.' and a.id=b.job_id and b.id=c.po_break_down_id and a.id=d.job_id and d.id=e.wo_pre_cost_trim_cost_dtls_id and b.id=e.po_break_down_id  and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and e.cons>0  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1) and c.is_deleted=0 and c.status_active in(1) and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 ';
		}

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
	private function _calculateQty($plan_cut_qnty,$costingPerQty,$total_set_qnty,$cons_qnty){
		if($total_set_qnty !=0 && $costingPerQty != 0){
			return $reqyarnqnty =($plan_cut_qnty/$total_set_qnty)*($cons_qnty/$costingPerQty);
		}
		else{
			return $reqyarnqnty = 0;
		}

	}

	private function _calculateAmount($reqyarnqnty,$rate){
	 return $amount=$reqyarnqnty*$rate;
	}

	private function _setQty($level){
		$jobNo='';
		$poId='';
		$itemNumberId='';$source_id='';
		$countryId='';
		$colorId='';
		$sizeId='';
		$item_color='';
		$item_size='';
		$articleNumber='';
		$planPutQnty='';
		$orderQuantity='';
		$total_set_qnty='';
		$preCostDtlsId='';
		$trimGroup='';
		$description='';
		$cons_dzn_gmts='';
		$rate='';

		$cons='';
		$tot_cons='';
		$country_id_trims='';

		//$gmtsitemRatioArray=$this->_gmtsitemRatioArray;
		//$costingPerArray=$this->_costingPerArr;
		$Qty=array();
		foreach($this->_dataArray as $row)
		{
			/*$jobNo=$row[csf('job_no')];
			$poId=$row[csf('id')];
			$itemNumberId=$row[csf('item_number_id')];
			$countryId=$row[csf('country_id')];
			$colorId=$row[csf('color_number_id')];
			$sizeId=$row[csf('size_number_id')];
			$planPutQnty=$row[csf('plan_cut_qnty')];
			$orderQuantity=$row[csf('order_quantity')];
			$total_set_qnty=$row[csf('total_set_qnty')];
			$preCostDtlsId=$row[csf('pre_cost_dtls_id')];
			$trimGroup=$row[csf('trim_group')];
			$description=$row[csf('description')];
			$cons_dzn_gmts=$row[csf('cons_dzn_gmts')];
			$rate=$row[csf('rate')];
			$cons=$row[csf('cons')];
			$tot_cons=$row[csf('tot_cons')];
			$country_id_trims=$row[csf('country_id_trims')];*/

			$jobNo=$row['job_no'];
			$poId=$row['id'];
			$itemNumberId=$row['item_number_id'];
			$source_id=$row['source_id'];
			$countryId=$row['country_id'];
			$colorId=$row['color_number_id'];
			$sizeId=$row['size_number_id'];
			$articleNumber=$row['article_number'];
			$planPutQnty=$row['plan_cut_qnty'];
			$orderQuantity=$row['order_quantity'];
			$total_set_qnty=$row['total_set_qnty'];
			$preCostDtlsId=$row['pre_cost_dtls_id'];
			$trimGroup=$row['trim_group'];
			$description=$row['description'];
			$cons_dzn_gmts=$row['cons_dzn_gmts'];
			$rate=$row['rate'];
			$cons=$row['cons'];
			$item_color=$row['item_color'];
			$item_size=$row['item_size'];
			$tot_cons=$row['tot_cons'];
			$country_id_trims=$row['country_id_trims'];
			//if($item_color=='0' || $item_color=='') $item_color=$colorId;


			//$costingPerQty=$this->_costingPer($costingPerArray[$jobNo]);
			//$set_item_ratio=$gmtsitemRatioArray[$jobNo][$itemNumberId];
			//$costingPerQty=$this->_costingPer($this->_costingPerArr[$jobNo]);

			$costingPerQty=$this->_costingPerQtyArr[$jobNo];
			$set_item_ratio=$this->_gmtsitemRatioArray[$jobNo][$itemNumberId];
			$trims_type_id=$this->_trimTypeArray[$trimGroup];
			if($trims_type_id>0) $trims_type_id=$trims_type_id;else $trims_type_id=0;
			//echo $trimGroup.'x, ';

			$reqqnty=0;
			$req=0;
			if($country_id_trims==0 || $country_id_trims==''){
			    $reqqnty=$this->_calculateQty($orderQuantity,$costingPerQty,$set_item_ratio,$cons);
				$req=$this->_calculateQty($orderQuantity,$costingPerQty,$set_item_ratio,$tot_cons);
			}
			else{
				$country_id_array=explode(",",$country_id_trims);
				if (in_array($countryId, $country_id_array)) {
					$reqqnty=$this->_calculateQty($orderQuantity,$costingPerQty,$set_item_ratio,$cons);
					$req=$this->_calculateQty($orderQuantity,$costingPerQty,$set_item_ratio,$tot_cons);
				}
			}

			if($level==$this->_By_Job){
				if(isset($Qty[$jobNo])){
					$Qty[$jobNo]+=$reqqnty;
				}
				else{
					$Qty[$jobNo]=$reqqnty;
				}
			}
			elseif($level==$this->_By_JobAndPrecostdtlsid){
				if(isset($Qty[$jobNo][$preCostDtlsId])){
					$Qty[$jobNo][$preCostDtlsId]+=$reqqnty;
				}
				else{
					$Qty[$jobNo][$preCostDtlsId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_JobAndItemid){
				if(isset($Qty[$jobNo][$trimGroup])){
					$Qty[$jobNo][$trimGroup]+=$reqqnty;
				}
				else{
					$Qty[$jobNo][$trimGroup]=$reqqnty;
				}
			}

			elseif($level==$this->_By_JobItemidAndDescription){
				if(isset($Qty[$jobNo][$trimGroup][$description])){
					$Qty[$jobNo][$trimGroup][$description]+=$reqqnty;
				}
				else{
					$Qty[$jobNo][$trimGroup][$description]=$reqqnty;
				}
			}
			elseif($level==$this->_By_JobItemidDescriptionGmtcolorAndSizeid){
				if(isset($Qty[$jobNo][$trimGroup][$description][$colorId][$sizeId])){
					$Qty[$jobNo][$trimGroup][$description][$colorId][$sizeId]+=$reqqnty;
				}
				else{
					$Qty[$jobNo][$trimGroup][$description][$colorId][$sizeId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_Order){
				if(isset($Qty[$poId])){
					$Qty[$poId]+=$reqqnty;
				}
				else{
					$Qty[$poId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderAndPrecostdtlsid){
				if(isset($Qty[$poId][$preCostDtlsId])){
					$Qty[$poId][$preCostDtlsId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$preCostDtlsId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderSourceidAndItemid){
				if(isset($Qty[$poId][$source_id][$trimGroup])){
					$Qty[$poId][$source_id][$trimGroup]+=$reqqnty;
				}
				else{
					$Qty[$poId][$source_id][$trimGroup]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderSourceidAndItemidType){
				if(isset($Qty[$poId][$source_id][$trimGroup][$trims_type_id])){
					$Qty[$poId][$source_id][$trimGroup][$trims_type_id]+=$reqqnty;
				}
				else{
					$Qty[$poId][$source_id][$trimGroup][$trims_type_id]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderAndItemid){
				if(isset($Qty[$poId][$trimGroup])){
					$Qty[$poId][$trimGroup]+=$reqqnty;
				}
				else{
					$Qty[$poId][$trimGroup]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderAndSourceid){
				if(isset($Qty[$poId][$source_id])){
					$Qty[$poId][$source_id]+=$reqqnty;
				}
				else{
					$Qty[$poId][$source_id]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderAndTrimsTypeid){
				if(isset($Qty[$poId][$trims_type_id])){
					$Qty[$poId][$trims_type_id]+=$reqqnty;
				}
				else{
					$Qty[$poId][$trims_type_id]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderAndItemidTypeid){
				if(isset($Qty[$poId][$trimGroup][$trims_type_id])){
					$Qty[$poId][$trimGroup][$trims_type_id]+=$reqqnty;
				}
				else{
					$Qty[$poId][$trimGroup][$trims_type_id]=$reqqnty;
				}
			}

			elseif($level==$this->_By_OrderItemidAndDescription){
				if(isset($Qty[$poId][$trimGroup][$description])){
					$Qty[$poId][$trimGroup][$description]+=$reqqnty;
				}
				else{
					$Qty[$poId][$trimGroup][$description]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderAndCountry){
				if(isset($Qty[$poId][$countryId])){
					$Qty[$poId][$countryId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderCountryAndPrecostdtlsid){
				if(isset($Qty[$poId][$countryId][$preCostDtlsId])){
					$Qty[$poId][$countryId][$preCostDtlsId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$preCostDtlsId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderCountryAndItemid){
				if(isset($Qty[$poId][$countryId][$trimGroup])){
					$Qty[$poId][$countryId][$trimGroup]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$trimGroup]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderCountryItemidAndDescription){
				if(isset($Qty[$poId][$countryId][$trimGroup][$description])){
					$Qty[$poId][$countryId][$trimGroup][$description]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$trimGroup][$description]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderAndGmtsitem){
				if(isset($Qty[$poId][$itemNumberId])){
					$Qty[$poId][$itemNumberId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$itemNumberId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderAndGmtscolor){
				if(isset($Qty[$poId][$colorId])){
					$Qty[$poId][$colorId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$colorId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderAndGmtssize){
				if(isset($Qty[$poId][$sizeId])){
					$Qty[$poId][$sizeId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$sizeId]=$reqqnty;
				}

			}
			elseif($level==$this->_By_orderCountryAndGmtsitem){
				if(isset($Qty[$poId][$countryId][$itemNumberId])){
					$Qty[$poId][$countryId][$itemNumberId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$itemNumberId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderCountryAndGmtscolor){
				if(isset($Qty[$poId][$countryId][$colorId])){
					$Qty[$poId][$countryId][$colorId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$colorId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderCountryAndGmtssize){
				if(isset($Qty[$poId][$countryId][$sizeId])){
					$Qty[$poId][$countryId][$sizeId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$sizeId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderGmtsitemAndGmtscolor){
				if(isset($Qty[$poId][$itemNumberId][$colorId])){
					$Qty[$poId][$itemNumberId][$colorId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$itemNumberId][$colorId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderGmtsitemAndGmtssize){
				if(isset($Qty[$poId][$itemNumberId][$sizeId])){
					$Qty[$poId][$itemNumberId][$sizeId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$itemNumberId][$sizeId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderGmtscolorAndGmtssize){
				if(isset($Qty[$poId][$colorId][$sizeId])){
					$Qty[$poId][$colorId][$sizeId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$colorId][$sizeId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemAndGmtscolor){
				if(isset($Qty[$poId][$countryId][$itemNumberId][$colorId])){
					$Qty[$poId][$countryId][$itemNumberId][$colorId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$itemNumberId][$colorId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemAndGmtssize){
				if(isset($Qty[$poId][$countryId][$itemNumberId][$sizeId])){
					$Qty[$poId][$countryId][$itemNumberId][$sizeId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$itemNumberId][$sizeId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_orderCountryGmtscolorAndGmtssize){
				if(isset($Qty[$poId][$countryId][$colorId][$sizeId])){
					$Qty[$poId][$countryId][$colorId][$sizeId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$colorId][$sizeId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_orderGmtsitemGmtscolorAndGmtssize){
				if(isset($Qty[$poId][$itemNumberId][$colorId][$sizeId])){
					$Qty[$poId][$itemNumberId][$colorId][$sizeId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$itemNumberId][$colorId][$sizeId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemGmtscolorAndGmtssize){
					if(isset($Qty[$poId][$countryId][$itemNumberId][$colorId][$sizeId])){
						$Qty[$poId][$countryId][$itemNumberId][$colorId][$sizeId]+=$reqqnty;
					}
					else{
						$Qty[$poId][$countryId][$itemNumberId][$colorId][$sizeId]=$reqqnty;
					}
			}
			elseif($level==$this->_By_Itemid){
				if(isset($Qty[$trimGroup])){
					$Qty[$trimGroup]+=$reqqnty;
				}
				else{
					$Qty[$trimGroup]=$reqqnty;
				}
			}
			elseif($level==$this->_By_ItemidAndDescription){
				if(isset($Qty[$trimGroup][$description])){
					$Qty[$trimGroup][$description]+=$reqqnty;
				}
				else{
					$Qty[$trimGroup][$description]=$reqqnty;
				}
			}
			elseif($level==$this->_By_Precostdtlsid){
				if(isset($Qty[$preCostDtlsId])){
					$Qty[$preCostDtlsId]+=$reqqnty;
				}
				else{
					$Qty[$preCostDtlsId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_PrecostdtlsidAndCountry){
				if(isset($Qty[$preCostDtlsId][$countryId])){
					$Qty[$preCostDtlsId][$countryId]+=$reqqnty;
				}
				else{
					$Qty[$preCostDtlsId][$countryId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_Precostdtlsid_consAndTotcons){
				if(isset($Qty[$preCostDtlsId])){
					$Qty[$preCostDtlsId]['cons']+=$reqqnty;
					$Qty[$preCostDtlsId]['totcons']+=$req;
				}
				else{
					$Qty[$preCostDtlsId]['cons']=$reqqnty;
					$Qty[$preCostDtlsId]['totcons']+=$req;
				}
			}
			elseif($level==$this->_By_PrecostdtlsidAndGmtssize_consAndTotcons){
				if(isset($Qty[$preCostDtlsId][$sizeId])){
					$Qty[$preCostDtlsId][$sizeId]['cons']+=$reqqnty;
					$Qty[$preCostDtlsId][$sizeId]['totcons']+=$req;
				}
				else{
					$Qty[$preCostDtlsId][$sizeId]['cons']=$reqqnty;
					$Qty[$preCostDtlsId][$sizeId]['totcons']+=$req;
				}
			}
			elseif($level==$this->_By_JobAndPrecostdtlsid_consAndTotcons){
				if(isset($Qty[$jobNo][$preCostDtlsId])){
					$Qty[$jobNo][$preCostDtlsId]['cons']+=$reqqnty;
					$Qty[$jobNo][$preCostDtlsId]['totcons']+=$req;
				}
				else{
					$Qty[$jobNo][$preCostDtlsId]['cons']=$reqqnty;
					$Qty[$jobNo][$preCostDtlsId]['totcons']+=$req;
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemAndPrecostdtlsid){
				if(isset($Qty[$poId][$countryId][$itemNumberId][$preCostDtlsId])){
					$Qty[$poId][$countryId][$itemNumberId][$preCostDtlsId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$itemNumberId][$preCostDtlsId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderPrecostdtlsidAndGmtscolor){
				if(isset($Qty[$poId][$preCostDtlsId][$colorId])){
					$Qty[$poId][$preCostDtlsId][$colorId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$preCostDtlsId][$colorId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderCountryPrecostdtlsidAndGmtscolor){
				if(isset($Qty[$poId][$countryId][$preCostDtlsId][$colorId])){
					$Qty[$poId][$countryId][$preCostDtlsId][$colorId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$preCostDtlsId][$colorId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderPrecostdtlsidAndGmtssize){
				if(isset($Qty[$poId][$preCostDtlsId][$sizeId])){
					$Qty[$poId][$preCostDtlsId][$sizeId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$preCostDtlsId][$sizeId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderCountryPrecostdtlsidAndGmtssize){
				if(isset($Qty[$poId][$countryId][$preCostDtlsId][$sizeId])){
					$Qty[$poId][$countryId][$preCostDtlsId][$sizeId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$preCostDtlsId][$sizeId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderPrecostdtlsidGmtssizeAndArticle){
				if(isset($Qty[$poId][$preCostDtlsId][$sizeId][$articleNumber])){
					$Qty[$poId][$preCostDtlsId][$sizeId][$articleNumber]+=$reqqnty;
				}
				else{
					$Qty[$poId][$preCostDtlsId][$sizeId][$articleNumber]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderPrecostdtlsidGmtssizeItemsizeAndArticle){
				if(isset($Qty[$poId][$preCostDtlsId][$sizeId][$item_size][$articleNumber])){
					$Qty[$poId][$preCostDtlsId][$sizeId][$item_size][$articleNumber]+=$reqqnty;
				}
				else{
					$Qty[$poId][$preCostDtlsId][$sizeId][$item_size][$articleNumber]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderItemidGmtscolorAndrGmtssize){
				if(isset($Qty[$poId][$trimGroup][$colorId][$sizeId])){
					$Qty[$poId][$trimGroup][$colorId][$sizeId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$trimGroup][$colorId][$sizeId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderPrecostdtlsidGmtscolorAndGmtssize){
				if(isset($Qty[$poId][$preCostDtlsId][$colorId][$sizeId])){
					$Qty[$poId][$preCostDtlsId][$colorId][$sizeId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$preCostDtlsId][$colorId][$sizeId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_PrecostdtlsidGmtscolorAndItemSize){
				if(isset($Qty[$preCostDtlsId][$colorId][$item_size])){
					$Qty[$preCostDtlsId][$colorId][$item_size]+=$reqqnty;
				}
				else{
					$Qty[$preCostDtlsId][$colorId][$item_size]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderCountryPrecostdtlsidGmtscolorAndGmtssize){
				if(isset($Qty[$poId][$countryId][$preCostDtlsId][$colorId][$sizeId])){
					$Qty[$poId][$countryId][$preCostDtlsId][$colorId][$sizeId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$preCostDtlsId][$colorId][$sizeId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderPrecostdtlsidGmtscolorGmtssizeAndArticle){
				if(isset($Qty[$poId][$preCostDtlsId][$colorId][$sizeId][$articleNumber])){
					$Qty[$poId][$preCostDtlsId][$colorId][$sizeId][$articleNumber]+=$reqqnty;
				}
				else{
					$Qty[$poId][$preCostDtlsId][$colorId][$sizeId][$articleNumber]=$reqqnty;
				}
			} 
			elseif($level==$this->_By_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColor){
				if(isset($Qty[$poId][$preCostDtlsId][$colorId][$sizeId][$articleNumber][$item_color])){
					$Qty[$poId][$preCostDtlsId][$colorId][$sizeId][$articleNumber][$item_color]+=$reqqnty;
				}
				else{
					$Qty[$poId][$preCostDtlsId][$colorId][$sizeId][$articleNumber][$item_color]=$reqqnty;
				}
			}
			  
			elseif($level==$this->_By_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColorItemSize){
				if(isset($Qty[$poId][$preCostDtlsId][$colorId][$sizeId][$articleNumber][$item_color][$item_size])){
					$Qty[$poId][$preCostDtlsId][$colorId][$sizeId][$articleNumber][$item_color][$item_size]+=$reqqnty;
				}
				else{
					$Qty[$poId][$preCostDtlsId][$colorId][$sizeId][$articleNumber][$item_color][$item_size]=$reqqnty;
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemGmtscolorAndPrecostdtlsid){
				if(isset($Qty[$poId][$countryId][$itemNumberId][$colorId][$preCostDtlsId])){
					$Qty[$poId][$countryId][$itemNumberId][$colorId][$preCostDtlsId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$itemNumberId][$colorId][$preCostDtlsId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemGmtssizeAndPrecostdtlsid){
				if(isset($Qty[$poId][$countryId][$itemNumberId][$sizeId][$preCostDtlsId])){
					$Qty[$poId][$countryId][$itemNumberId][$sizeId][$preCostDtlsId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$itemNumberId][$sizeId][$preCostDtlsId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemGmtscolorGmtssizeAndPrecostdtlsid){
				if(isset($Qty[$poId][$countryId][$itemNumberId][$colorId][$sizeId][$preCostDtlsId])){
					$Qty[$poId][$countryId][$itemNumberId][$colorId][$sizeId][$preCostDtlsId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$itemNumberId][$colorId][$sizeId][$preCostDtlsId]=$reqqnty;
				}
			}
			else{
				return null;
			}
		}
		return $Qty;
	}

	private function _setAmount($level){
		$jobNo='';
		$poId='';
		$itemNumberId='';
		$countryId='';
		$colorId='';
		$sizeId='';$item_size='';$item_color='';
		$planPutQnty='';
		$orderQuantity='';
		$total_set_qnty='';
		$preCostDtlsId='';
		$trimGroup='';
		$description='';
		$cons_dzn_gmts='';
		$rate='';$sourcing_rate='';

		$cons='';
		$tot_cons='';
		$country_id_trims='';$source_id='';
		//$gmtsitemRatioArray=$this->_gmtsitemRatioArray;
		//$costingPerArray=$this->_costingPerArr;
		$Amount=array();

		foreach($this->_dataArray as $row)
		{
			/*$jobNo=$row[csf('job_no')];
			$poId=$row[csf('id')];
			$itemNumberId=$row[csf('item_number_id')];
			$countryId=$row[csf('country_id')];
			$colorId=$row[csf('color_number_id')];
			$sizeId=$row[csf('size_number_id')];
			$planPutQnty=$row[csf('plan_cut_qnty')];
			$orderQuantity=$row[csf('order_quantity')];
			$total_set_qnty=$row[csf('total_set_qnty')];

			$preCostDtlsId=$row[csf('pre_cost_dtls_id')];
			$trimGroup=$row[csf('trim_group')];
			$description=$row[csf('description')];
			$cons_dzn_gmts=$row[csf('cons_dzn_gmts')];
			$rate=$row[csf('rate')];

			$cons=$row[csf('cons')];
			$tot_cons=$row[csf('tot_cons')];
			$country_id_trims=$row[csf('country_id_trims')];*/

			$jobNo=$row['job_no'];
			$poId=$row['id'];
			$itemNumberId=$row['item_number_id'];
			$source_id=$row['source_id'];
			$countryId=$row['country_id'];
			$colorId=$row['color_number_id'];
			$sizeId=$row['size_number_id'];
			$articleNumber=$row['article_number'];
			$planPutQnty=$row['plan_cut_qnty'];
			$orderQuantity=$row['order_quantity'];
			$total_set_qnty=$row['total_set_qnty'];
			$item_color=$row['item_color'];
			$item_size=$row['item_size'];
		

			$preCostDtlsId=$row['pre_cost_dtls_id'];
			$trimGroup=$row['trim_group'];
			$description=$row['description'];
			$cons_dzn_gmts=$row['cons_dzn_gmts'];
			$rate=$row['rate'];
			$sourcing_rate=$row['sourcing_rate'];
			$cons=$row['cons'];
			$tot_cons=$row['tot_cons'];
			$country_id_trims=$row['country_id_trims'];

			//$costingPerQty=$this->_costingPer($costingPerArray[$jobNo]);
			//$set_item_ratio=$gmtsitemRatioArray[$jobNo][$itemNumberId];

			//$costingPerQty=$this->_costingPer($this->_costingPerArr[$jobNo]);
			$costingPerQty=$this->_costingPerQtyArr[$jobNo];
			$set_item_ratio=$this->_gmtsitemRatioArray[$jobNo][$itemNumberId];
			$trims_type_id=$this->_trimTypeArray[$trimGroup];
			if($trims_type_id>0) $trims_type_id=$trims_type_id;else $trims_type_id=0;

			$reqqnty=0;
			$req=0;
			$amount=0;
			$req_amount=0;$sourcing_amount=0;
			if($country_id_trims==0 || $country_id_trims==''){
			$reqqnty=$this->_calculateQty($orderQuantity,$costingPerQty,$set_item_ratio,$cons);
			$req=$this->_calculateQty($orderQuantity,$costingPerQty,$set_item_ratio,$tot_cons);
			$amount=$this->_calculateAmount($reqqnty,$rate);
			$sourcing_amount=$this->_calculateAmount($reqqnty,$sourcing_rate);
			$req_amount=$this->_calculateAmount($req,$rate);
			}
			else{
				$country_id_array=explode(",",$country_id_trims);
				if (in_array($countryId, $country_id_array)) {
					$reqqnty=$this->_calculateQty($orderQuantity,$costingPerQty,$set_item_ratio,$cons);
					$req=$this->_calculateQty($orderQuantity,$costingPerQty,$set_item_ratio,$tot_cons);
					$amount=$this->_calculateAmount($reqqnty,$rate);
					$sourcing_amount=$this->_calculateAmount($reqqnty,$sourcing_rate);
					$req_amount=$this->_calculateAmount($req,$rate);
				}
			}
			if($level==$this->_By_Job){
				if(isset($Amount[$jobNo])){
					$Amount[$jobNo]+=$amount;
				}
				else{
					$Amount[$jobNo]=$amount;
				}
			}
			elseif($level==$this->_By_JobSourcing){
				if(isset($Amount[$jobNo])){
					$Amount[$jobNo]+=$sourcing_amount;
				}
				else{
					$Amount[$jobNo]=$sourcing_amount;
				}
			}
			elseif($level==$this->_By_JobAndPrecostdtlsid){
				if(isset($Amount[$jobNo][$preCostDtlsId])){
					$Amount[$jobNo][$preCostDtlsId]+=$amount;
				}
				else{
					$Amount[$jobNo][$preCostDtlsId]=$amount;
				}
			}
			elseif($level==$this->_By_JobAndItemid){
				if(isset($Amount[$jobNo][$trimGroup])){
					$Amount[$jobNo][$trimGroup]+=$amount;
				}
				else{
					$Amount[$jobNo][$trimGroup]=$amount;
				}
			}

			elseif($level==$this->_By_JobItemidAndDescription){
				if(isset($Amount[$jobNo][$trimGroup][$description])){
					$Amount[$jobNo][$trimGroup][$description]+=$amount;
				}
				else{
					$Amount[$jobNo][$trimGroup][$description]=$amount;
				}
			}
			elseif($level==$this->_By_JobItemidDescriptionGmtcolorAndSizeid){
				if(isset($Amount[$jobNo][$trimGroup][$description][$colorId][$sizeId])){
					$Amount[$jobNo][$trimGroup][$description][$colorId][$sizeId]+=$amount;
				}
				else{
					$Amount[$jobNo][$trimGroup][$description][$colorId][$sizeId]=$amount;
				}
			}

			elseif($level==$this->_By_Order){
				if(isset($Amount[$poId])){
					$Amount[$poId]+=$amount;
				}
				else{
					$Amount[$poId]=$amount;
				}
			}
			
			elseif($level==$this->_By_OrderAndPrecostdtlsidSourcing){
				if(isset($Amount[$poId][$preCostDtlsId])){
					$Amount[$poId][$preCostDtlsId]+=$sourcing_amount;
				}
				else{
					$Amount[$poId][$preCostDtlsId]=$sourcing_amount;
				}
			}
			elseif($level==$this->_By_OrderAndPrecostdtlsid){
				if(isset($Amount[$poId][$preCostDtlsId])){
					$Amount[$poId][$preCostDtlsId]+=$amount;
				}
				else{
					$Amount[$poId][$preCostDtlsId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderAndItemid){
				if(isset($Amount[$poId][$trimGroup])){
					$Amount[$poId][$trimGroup]+=$amount;
				}
				else{
					$Amount[$poId][$trimGroup]=$amount;
				}
			}
			elseif($level==$this->_By_OrderSourceidAndItemid){ 
				if(isset($Amount[$poId][$source_id][$trimGroup])){
					$Amount[$poId][$source_id][$trimGroup]+=$amount;
				}
				else{
					$Amount[$poId][$source_id][$trimGroup]=$amount;
				}
			}
			elseif($level==$this->_By_OrderSourceidAndItemidType){ //trims_type_id
				if(isset($Amount[$poId][$source_id][$trimGroup][$trims_type_id])){
					$Amount[$poId][$source_id][$trimGroup][$trims_type_id]+=$amount;
				}
				else{
					$Amount[$poId][$source_id][$trimGroup][$trims_type_id]=$amount;
				}
			}
			elseif($level==$this->_By_OrderAndSourceid){
				if(isset($Amount[$poId][$source_id])){
					$Amount[$poId][$source_id]+=$amount;
				}
				else{
					$Amount[$poId][$source_id]=$amount;
				}
			}
			elseif($level==$this->_By_OrderAndTrimsTypeid){ 
				if(isset($Amount[$poId][$trims_type_id])){
					$Amount[$poId][$trims_type_id]+=$amount;
				}
				else{
					$Amount[$poId][$trims_type_id]=$amount;
				}
			}
			elseif($level==$this->_By_OrderAndItemidTypeid){
				if(isset($Amount[$poId][$trimGroup][$trims_type_id])){
					$Amount[$poId][$trimGroup][$trims_type_id]+=$amount;
				}
				else{
					$Amount[$poId][$trimGroup][$trims_type_id]=$amount;
				}
			}

			elseif($level==$this->_By_OrderItemidAndDescription){
				if(isset($Amount[$poId][$trimGroup][$description])){
					$Amount[$poId][$trimGroup][$description]+=$amount;
				}
				else{
					$Amount[$poId][$trimGroup][$description]=$amount;
				}
			}
			elseif($level==$this->_By_OrderAndCountry){
				if(isset($Amount[$poId][$countryId])){
					$Amount[$poId][$countryId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderCountryAndPrecostdtlsid){
				if(isset($Amount[$poId][$countryId][$preCostDtlsId])){
					$Amount[$poId][$countryId][$preCostDtlsId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$preCostDtlsId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderCountryAndItemid){
				if(isset($Amount[$poId][$countryId][$trimGroup])){
					$Amount[$poId][$countryId][$trimGroup]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$trimGroup]=$amount;
				}
			}

			elseif($level==$this->_By_OrderCountryItemidAndDescription){
				if(isset($Amount[$poId][$countryId][$trimGroup][$description])){
					$Amount[$poId][$countryId][$trimGroup][$description]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$trimGroup][$description]=$amount;
				}
			}
			elseif($level==$this->_By_OrderAndGmtsitem){
				if(isset($Amount[$poId][$itemNumberId])){
					$Amount[$poId][$itemNumberId]+=$amount;
				}
				else{
					$Amount[$poId][$itemNumberId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderAndGmtscolor){
				if(isset($Amount[$poId][$colorId])){
					$Amount[$poId][$colorId]+=$amount;
				}
				else{
					$Amount[$poId][$colorId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderAndGmtssize){
				if(isset($Amount[$poId][$sizeId])){
					$Amount[$poId][$sizeId]+=$amount;
				}
				else{
					$Amount[$poId][$sizeId]=$amount;
				}

			}
			elseif($level==$this->_By_orderCountryAndGmtsitem){
				if(isset($Amount[$poId][$countryId][$itemNumberId])){
					$Amount[$poId][$countryId][$itemNumberId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderCountryAndGmtscolor){
				if(isset($Amount[$poId][$countryId][$colorId])){
					$Amount[$poId][$countryId][$colorId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$colorId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderCountryAndGmtssize){
				if(isset($Amount[$poId][$countryId][$sizeId])){
					$Amount[$poId][$countryId][$sizeId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$sizeId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderGmtsitemAndGmtscolor){
				if(isset($Amount[$poId][$itemNumberId][$colorId])){
					$Amount[$poId][$itemNumberId][$colorId]+=$amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$colorId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderGmtsitemAndGmtssize){
				if(isset($Amount[$poId][$itemNumberId][$sizeId])){
					$Amount[$poId][$itemNumberId][$sizeId]+=$amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$sizeId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderGmtscolorAndGmtssize){
				if(isset($Amount[$poId][$colorId][$sizeId])){
					$Amount[$poId][$colorId][$sizeId]+=$amount;
				}
				else{
					$Amount[$poId][$colorId][$sizeId]=$amount;
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemAndGmtscolor){
				if(isset($Amount[$poId][$countryId][$itemNumberId][$colorId])){
					$Amount[$poId][$countryId][$itemNumberId][$colorId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$colorId]=$amount;
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemAndGmtssize){
				if(isset($Amount[$poId][$countryId][$itemNumberId][$sizeId])){
					$Amount[$poId][$countryId][$itemNumberId][$sizeId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$sizeId]=$amount;
				}
			}
			elseif($level==$this->_By_orderCountryGmtscolorAndGmtssize){
				if(isset($Amount[$poId][$countryId][$colorId][$sizeId])){
					$Amount[$poId][$countryId][$colorId][$sizeId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$colorId][$sizeId]=$amount;
				}
			}
			elseif($level==$this->_By_orderGmtsitemGmtscolorAndGmtssize){
				if(isset($Amount[$poId][$itemNumberId][$colorId][$sizeId])){
					$Amount[$poId][$itemNumberId][$colorId][$sizeId]+=$amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$colorId][$sizeId]=$amount;
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemGmtscolorAndGmtssize){
					if(isset($Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId])){
						$Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId]+=$amount;
					}
					else{
						$Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId]=$amount;
					}
			}
			elseif($level==$this->_By_Itemid){
				if(isset($Amount[$trimGroup])){
					$Amount[$trimGroup]+=$amount;
				}
				else{
					$Amount[$trimGroup]=$amount;
				}
			}
			elseif($level==$this->_By_ItemidAndDescription){
				if(isset($Amount[$trimGroup][$description])){
					$Amount[$trimGroup][$description]+=$amount;
				}
				else{
					$Amount[$trimGroup][$description]=$amount;
				}
			}
			elseif($level==$this->_By_Precostdtlsid){
				if(isset($Amount[$preCostDtlsId])){
					$Amount[$preCostDtlsId]+=$amount;
				}
				else{
					$Amount[$preCostDtlsId]=$amount;
				}
			}
			elseif($level==$this->_By_PrecostdtlsidSourcing){
				if(isset($Amount[$preCostDtlsId])){
					$Amount[$preCostDtlsId]+=$sourcing_amount;
				}
				else{
					$Amount[$preCostDtlsId]=$sourcing_amount;
				}
			}
			elseif($level==$this->_By_PrecostdtlsidAndCountry){
				if(isset($Amount[$preCostDtlsId][$countryId])){
					$Amount[$preCostDtlsId][$countryId]+=$amount;
				}
				else{
					$Amount[$preCostDtlsId][$countryId]=$amount;
				}
			}
			elseif($level==$this->_By_PrecostdtlsidAndCountrySourcing){
				if(isset($Amount[$preCostDtlsId][$countryId])){
					$Amount[$preCostDtlsId][$countryId]+=$sourcing_amount;
				}
				else{
					$Amount[$preCostDtlsId][$countryId]=$sourcing_amount;
				}
			}

			elseif($level==$this->_By_PrecostdtlsidAndGmtssize_consAndTotcons){
				if(isset($Amount[$preCostDtlsId][$sizeId])){
					$Amount[$preCostDtlsId][$sizeId]['cons']+=$amount;
					$Amount[$preCostDtlsId][$sizeId]['totcons']+=$req_amount; 
				}
				else{
					$Amount[$preCostDtlsId][$sizeId]['cons']=$amount;
					$Amount[$preCostDtlsId][$sizeId]['totcons']+=$req_amount;
				}
			}
			elseif($level==$this->_By_JobAndPrecostdtlsid_consAndTotcons){
				if(isset($Amount[$jobNo][$preCostDtlsId])){
					$Amount[$jobNo][$preCostDtlsId]['cons']+=$amount;
					$Amount[$jobNo][$preCostDtlsId]['totcons']+=$req_amount;
				}
				else{
					$Amount[$jobNo][$preCostDtlsId]['cons']=$amount;
					$Amount[$jobNo][$preCostDtlsId]['totcons']+=$req_amount;
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemAndPrecostdtlsid){
				if(isset($Amount[$poId][$countryId][$itemNumberId][$preCostDtlsId])){
					$Amount[$poId][$countryId][$itemNumberId][$preCostDtlsId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$preCostDtlsId]=$amount;
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemAndPrecostdtlsidSourcing){
				if(isset($Amount[$poId][$countryId][$itemNumberId][$preCostDtlsId])){
					$Amount[$poId][$countryId][$itemNumberId][$preCostDtlsId]+=$sourcing_amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$preCostDtlsId]=$sourcing_amount;
				}
			}
			elseif($level==$this->_By_OrderPrecostdtlsidAndGmtscolor){
				if(isset($Amount[$poId][$preCostDtlsId][$colorId])){
					$Amount[$poId][$preCostDtlsId][$colorId]+=$amount;
				}
				else{
					$Amount[$poId][$preCostDtlsId][$colorId]=$amount;
				}
			}//Sourcing
			elseif($level==$this->_By_OrderCountryPrecostdtlsidAndGmtscolorSourcing){
				if(isset($Amount[$poId][$countryId][$preCostDtlsId][$colorId])){
					$Amount[$poId][$countryId][$preCostDtlsId][$colorId]+=$sourcing_amount;
				}
				else{
					$Amount[$poId][$countryId][$preCostDtlsId][$colorId]=$sourcing_amount;
				}
			}
			elseif($level==$this->_By_OrderCountryPrecostdtlsidAndGmtscolor){
				if(isset($Amount[$poId][$countryId][$preCostDtlsId][$colorId])){
					$Amount[$poId][$countryId][$preCostDtlsId][$colorId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$preCostDtlsId][$colorId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderPrecostdtlsidAndGmtssize){
				if(isset($Amount[$poId][$preCostDtlsId][$sizeId])){
					$Amount[$poId][$preCostDtlsId][$sizeId]+=$amount;
				}
				else{
					$Amount[$poId][$preCostDtlsId][$sizeId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderCountryPrecostdtlsidAndGmtssize){
				if(isset($Amount[$poId][$countryId][$preCostDtlsId][$sizeId])){
					$Amount[$poId][$countryId][$preCostDtlsId][$sizeId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$preCostDtlsId][$sizeId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderPrecostdtlsidGmtssizeAndArticle){
				if(isset($Amount[$poId][$preCostDtlsId][$sizeId][$articleNumber])){
					$Amount[$poId][$preCostDtlsId][$sizeId][$articleNumber]+=$amount;
				}
				else{
					$Amount[$poId][$preCostDtlsId][$sizeId][$articleNumber]=$amount;
				}
			}
			elseif($level==$this->_By_OrderPrecostdtlsidGmtssizeItemsizeAndArticle){
				if(isset($Amount[$poId][$preCostDtlsId][$sizeId][$item_size][$articleNumber])){
					$Amount[$poId][$preCostDtlsId][$sizeId][$item_size][$articleNumber]+=$amount;
				}
				else{
					$Amount[$poId][$preCostDtlsId][$sizeId][$item_size][$articleNumber]=$amount;
				}
			}
			elseif($level==$this->_By_OrderItemidGmtscolorAndrGmtssize){
				if(isset($Amount[$poId][$trimGroup][$colorId][$sizeId])){
					$Amount[$poId][$trimGroup][$colorId][$sizeId]+=$amount;
				}
				else{
					$Amount[$poId][$trimGroup][$colorId][$sizeId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderPrecostdtlsidGmtscolorAndGmtssize){
				if(isset($Amount[$poId][$preCostDtlsId][$colorId][$sizeId])){
					$Amount[$poId][$preCostDtlsId][$colorId][$sizeId]+=$amount;
				}
				else{
					$Amount[$poId][$preCostDtlsId][$colorId][$sizeId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderPrecostdtlsidGmtscolorAndGmtssizeSourcing){
				if(isset($Amount[$poId][$preCostDtlsId][$colorId][$sizeId])){
					$Amount[$poId][$preCostDtlsId][$colorId][$sizeId]+=$sourcing_amount;
				}
				else{
					$Amount[$poId][$preCostDtlsId][$colorId][$sizeId]=$sourcing_amount;
				}
			}
			elseif($level==$this->_By_OrderCountryPrecostdtlsidGmtscolorAndGmtssize){
				if(isset($Amount[$poId][$countryId][$preCostDtlsId][$colorId][$sizeId])){
					$Amount[$poId][$countryId][$preCostDtlsId][$colorId][$sizeId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$preCostDtlsId][$colorId][$sizeId]=$amount;
				}
			} 
			elseif($level==$this->_By_OrderCountryPrecostdtlsidGmtscolorAndGmtssizeSourcing){
				if(isset($Amount[$poId][$countryId][$preCostDtlsId][$colorId][$sizeId])){
					$Amount[$poId][$countryId][$preCostDtlsId][$colorId][$sizeId]+=$sourcing_amount;
				}
				else{
					$Amount[$poId][$countryId][$preCostDtlsId][$colorId][$sizeId]=$sourcing_amount;
				}
			} 
			elseif($level==$this->_By_OrderPrecostdtlsidGmtscolorGmtssizeAndArticle){
				if(isset($Amount[$poId][$preCostDtlsId][$colorId][$sizeId][$articleNumber])){
					$Amount[$poId][$preCostDtlsId][$colorId][$sizeId][$articleNumber]+=$amount;
				}
				else{
					$Amount[$poId][$preCostDtlsId][$colorId][$sizeId][$articleNumber]=$amount;
				}
			} //item_size
			elseif($level==$this->_By_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColor){
				if(isset($Amount[$poId][$preCostDtlsId][$colorId][$sizeId][$articleNumber][$item_color])){
					$Amount[$poId][$preCostDtlsId][$colorId][$sizeId][$articleNumber][$item_color]+=$amount;
				}
				else{
					$Amount[$poId][$preCostDtlsId][$colorId][$sizeId][$articleNumber][$item_color]=$amount;
				}
			}
			elseif($level==$this->_By_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColorItemSize){
				if(isset($Amount[$poId][$preCostDtlsId][$colorId][$sizeId][$articleNumber][$item_color][$item_size])){
					$Amount[$poId][$preCostDtlsId][$colorId][$sizeId][$articleNumber][$item_color][$item_size]+=$amount;
				}
				else{
					$Amount[$poId][$preCostDtlsId][$colorId][$sizeId][$articleNumber][$item_color][$item_size]=$amount;
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemGmtscolorAndPrecostdtlsid){
				if(isset($Amount[$poId][$countryId][$itemNumberId][$colorId][$preCostDtlsId])){
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$preCostDtlsId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$preCostDtlsId]=$amount;
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemGmtssizeAndPrecostdtlsid){
				if(isset($Amount[$poId][$countryId][$itemNumberId][$sizeId][$preCostDtlsId])){
					$Amount[$poId][$countryId][$itemNumberId][$sizeId][$preCostDtlsId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$sizeId][$preCostDtlsId]=$amount;
				}
			}
			elseif($level==$this->_By_orderCountryGmtsitemGmtscolorGmtssizeAndPrecostdtlsid){
				if(isset($Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId][$preCostDtlsId])){
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId][$preCostDtlsId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$sizeId][$preCostDtlsId]=$amount;
				}
			}
			else{
				return null;
			}

		}
		return $Amount;
	}

	public function unsetDataArray(){
		$this->_dataArray=array();
	}

	//Job wise
	//Qty
	public function getQty_by_job($jobNo){
		$Qty=$this->_setQty($this->_By_Job);
		return $Qty[$jobNo];
	}

	public function getQtyArray_by_job(){
		$Qty=$this->_setQty($this->_By_Job);
		return $Qty;

	}
	//Amount
	public function getAmount_by_job($jobNo){
		$Amount=$this->_setAmount($this->_By_Job);
		return $Amount[$jobNo];
	}

	public function getAmountArray_by_job(){
		$Amount=$this->_setAmount($this->_By_Job);
		return $Amount;
	}
	//Job and Conversion Id wise
	//Qty
	public function getQty_by_jobAndPrecostdtlsid($jobNo,$preCostDtlsId){
		$Qty=$this->_setQty($this->_By_JobAndPrecostdtlsid);
		return $Qty[$jobNo][$preCostDtlsId];
	}

	public function getQtyArray_by_jobAndPrecostdtlsid(){
		$Qty=$this->_setQty($this->_By_JobAndPrecostdtlsid);
		return $Qty;

	}
	//Amount
	public function getAmount_by_jobAndPrecostdtlsid($jobNo,$preCostDtlsId){
		$Amount=$this->_setAmount($this->_By_JobAndPrecostdtlsid);
		return $Amount[$jobNo][$preCostDtlsId];
	}

	public function getAmountArray_by_jobAndPrecostdtlsid(){
		$Amount=$this->_setAmount($this->_By_JobAndPrecostdtlsid);
		return $Amount;
	}
	//Amount Sourcing
	public function getAmount_by_jobSourcing($jobNo){
		$Amount=$this->_setAmount($this->_By_JobSourcing);
		return $Amount[$jobNo];
	}

	public function getAmountArray_by_jobSourcing(){
		$Amount=$this->_setAmount($this->_By_JobSourcing);
		return $Amount;
	}


	//Job and Fabric wise
	//Qty
	public function getQty_by_jobAndItemid($jobNo,$trimGroup){
		$Qty=$this->_setQty($this->_By_JobAndItemid);
		return $Qty[$jobNo][$trimGroup];
	}

	public function getQtyArray_by_jobAndItemid(){
		$Qty=$this->_setQty($this->_By_JobAndItemid);
		return $Qty;

	}
	//Amount
	public function getAmount_by_jobAndItemid($jobNo,$trimGroup){
		$Amount=$this->_setAmount($this->_By_JobAndItemid);
		return $Amount[$jobNo][$trimGroup];
	}

	public function getAmountArray_by_jobAndItemid(){
		$Amount=$this->_setAmount($this->_By_JobAndItemid);
		return $Amount;
	}



	//Job, Fabric and Process wise
	//Qty
	public function getQty_by_jobItemidAndDescription($jobNo,$trimGroup,$description){
		$Qty=$this->_setQty($this->_By_JobItemidAndDescription);
		return $Qty[$jobNo][$trimGroup][$description];
	}

	public function getQtyArray_by_jobItemidAndDescription(){
		$Qty=$this->_setQty($this->_By_JobItemidAndDescription);
		return $Qty;

	}
	//Amount
	public function getAmount_by_jobItemidAndDescription($jobNo,$trimGroup,$description){
		$Amount=$this->_setAmount($this->_By_JobItemidAndDescription);
		return $Amount[$jobNo][$trimGroup][$description];
	}

	public function getAmountArray_by_jobItemidAndDescription(){
		$Amount=$this->_setAmount($this->_By_JobItemidAndDescription);
		return $Amount;
	}

	//Job, TrimGroup Gmt Color and Gmt Size wise //Aziz
	//Qty
	public function getQty_by_jobItemidDescriptionGmtcolorAndSizeid($jobNo,$trimGroup,$description,$colorId,$sizeId){
		$Qty=$this->_setQty($this->_By_JobItemidDescriptionGmtcolorAndSizeid);
		return $Qty[$jobNo][$trimGroup][$description][$colorId][$sizeId];
	}

	public function getQtyArray_by_jobItemidDescriptionGmtcolorAndSizeid(){
		$Qty=$this->_setQty($this->_By_JobItemidDescriptionGmtcolorAndSizeid);
		return $Qty;

	}
	//Amount
	public function getAmount_by_jobItemidDescriptionGmtcolorAndSizeid($jobNo,$trimGroup,$description,$colorId,$sizeId){
		$Amount=$this->_setAmount($this->_By_JobItemidDescriptionGmtcolorAndSizeid);
		return $Amount[$jobNo][$trimGroup][$description][$colorId][$sizeId];
	}

	public function getAmountArray_by_jobItemidDescriptionGmtcolorAndSizeid(){
		$Amount=$this->_setAmount($this->_By_JobItemidDescriptionGmtcolorAndSizeid);
		return $Amount;
	}


	// Order wise
	//Qty
	public function getQty_by_order($poId){
		$Qty=$this->_setQty($this->_By_Order);
		return $Qty[$poId];
	}

	public function getQtyArray_by_order(){
		$Qty=$this->_setQty($this->_By_Order);
		return $Qty;
	}

	//Amount
	public function getAmount_by_order($poId){
		$Amount=$this->_setAmount($this->_By_Order);
		return $Amount[$poId];
	}

	public function getAmountArray_by_order(){
		$Amount=$this->_setAmount($this->_By_Order);
		return $Amount;
	}


	//Order and Conversion Id wise
	//Qty
	public function getQty_by_orderAndPrecostdtlsid($poId,$preCostDtlsId){
		$Qty=$this->_setQty($this->_By_OrderAndPrecostdtlsid);
		return $Qty[$poId][$preCostDtlsId];
	}

	public function getQtyArray_by_orderAndPrecostdtlsid(){
		$Qty=$this->_setQty($this->_By_OrderAndPrecostdtlsid);
		return $Qty;

	}
	//Amount
	public function getAmount_by_orderAndPrecostdtlsid($poId,$preCostDtlsId){
		$Amount=$this->_setAmount($this->_By_OrderAndPrecostdtlsid);
		return $Amount[$poId][$preCostDtlsId];
	}
	
	public function getAmountArray_by_orderAndPrecostdtlsid(){
		$Amount=$this->_setAmount($this->_By_OrderAndPrecostdtlsid);
		return $Amount;
	}
	//Amount sourcing
	public function getAmount_by_orderAndPrecostdtlsidSourcing($poId,$preCostDtlsId){
		$Amount=$this->_setAmount($this->_By_OrderAndPrecostdtlsidSourcing);
		return $Amount[$poId][$preCostDtlsId];
	}

	public function getAmountArray_by_orderAndPrecostdtlsidSourcing(){
		$Amount=$this->_setAmount($this->_By_OrderAndPrecostdtlsidSourcing);
		return $Amount;
	}


	//Order and Fabric wise
	//Qty
	public function getQty_by_orderAndItemid($poId,$trimGroup){
		$Qty=$this->_setQty($this->_By_OrderAndItemid);
		return $Qty[$poId][$trimGroup];
	}

	public function getQtyArray_by_orderAndItemid(){
		$Qty=$this->_setQty($this->_By_OrderAndItemid);
		return $Qty;

	}
	//Order Source id and Item Group 
	//Qty
	public function getQty_by_orderSourceidAndItemid($poId,$source_id,$trimGroup){
		$Qty=$this->_setQty($this->_By_OrderSourceidAndItemid);
		return $Qty[$poId][$source_id][$trimGroup];
	}

	public function getQtyArray_by_orderSourceidAndItemid(){
		$Qty=$this->_setQty($this->_By_OrderSourceidAndItemid);
		return $Qty;

	}
	//Order Source id and Item Group ,Trim Type
	//Qty
	public function getQty_by_orderSourceidAndItemidType($poId,$source_id,$trimGroup,$trims_type_id){
		$Qty=$this->_setQty($this->_By_OrderSourceidAndItemidType);
		return $Qty[$poId][$source_id][$trimGroup][$trims_type_id];
	}

	public function getQtyArray_by_orderSourceidAndItemidType(){
		$Qty=$this->_setQty($this->_By_OrderSourceidAndItemidType);
		return $Qty;

	}
	
	//Order and Source Id wise
	//Qty
	public function getQty_by_orderAndSourceid($poId,$source_id){
		$Qty=$this->_setQty($this->_By_OrderAndSourceid);
		return $Qty[$poId][$source_id];
	}

	public function getQtyArray_by_orderAndSourceid(){
		$Qty=$this->_setQty($this->_By_OrderAndSourceid);
		return $Qty;

	}
	
	public function getQty_by_orderAndTrimsTypeid($poId,$trims_type_id){
		$Qty=$this->_setQty($this->_By_OrderAndTrimsTypeid);
		return $Qty[$poId][$trims_type_id];
	}

	public function getQtyArray_by_orderAndTrimsTypeid(){
		$Qty=$this->_setQty($this->_By_OrderAndTrimsTypeid);
		return $Qty;

	}
	public function getQty_by_orderAndItemidTypeid($poId,$trimGroup,$trims_type_id){
		$Qty=$this->_setQty($this->_By_OrderAndItemidTypeid);
		return $Qty[$poId][$trimGroup][$trims_type_id];
	}

	public function getQtyArray_by_orderAndItemidTypeid(){
		$Qty=$this->_setQty($this->_By_OrderAndItemidTypeid);
		return $Qty;

	}
	//Amount
	public function getAmount_by_orderAndItemid($poId,$trimGroup){
		$Amount=$this->_setAmount($this->_By_OrderAndItemid);
		return $Amount[$poId][$trimGroup];
	}

	public function getAmountArray_by_orderAndItemid(){
		$Amount=$this->_setAmount($this->_By_OrderAndItemid);
		return $Amount;
	}
	//Amount po Source,Item Group
	public function getAmount_by_orderSourceidAndItemid($poId,$source_id,$trimGroup){
		$Amount=$this->_setAmount($this->_By_OrderSourceidAndItemid);
		return $Amount[$poId][$source_id][$trimGroup];
	}

	public function getAmountArray_by_orderSourceidAndItemid(){
		$Amount=$this->_setAmount($this->_By_OrderSourceidAndItemid);
		return $Amount;
	}
	//Amount po Source,Item Group,Trim Type
	public function getAmount_by_orderSourceidAndItemidType($poId,$source_id,$trimGroup,$trims_type_id){
		$Amount=$this->_setAmount($this->_By_OrderSourceidAndItemidType);
		return $Amount[$poId][$source_id][$trimGroup][$trims_type_id];
	}

	public function getAmountArray_by_orderSourceidAndItemidType(){
		$Amount=$this->_setAmount($this->_By_OrderSourceidAndItemidType);
		return $Amount;
	}
	//Amount PO Id Source id
	public function getAmount_by_orderAndSourceid($poId,$source_id){
		$Amount=$this->_setAmount($this->_By_OrderAndSourceid);
		return $Amount[$poId][$source_id];
	}

	public function getAmountArray_by_orderAndSourceid(){
		$Amount=$this->_setAmount($this->_By_OrderAndSourceid);
		return $Amount;
	}
	
	public function getAmount_by_orderAndTrimsTypeid($poId,$trims_type_id){
		$Amount=$this->_setAmount($this->_By_OrderAndTrimsTypeid);
		return $Amount[$poId][$trims_type_id];
	}

	public function getAmountArray_by_orderAndTrimsTypeid(){
		$Amount=$this->_setAmount($this->_By_OrderAndTrimsTypeid);
		return $Amount;
	}
	
	public function getAmount_by_orderAndItemidTypeid($poId,$trimGroup,$trims_type_id){
		$Amount=$this->_setAmount($this->_By_OrderAndItemidTypeid);
		return $Amount[$poId][$trimGroup][$trims_type_id];
	}

	public function getAmountArray_by_orderAndItemidTypeid(){
		$Amount=$this->_setAmount($this->_By_OrderAndItemidTypeid);
		return $Amount;
	}
	
	//Order Fabric and Process wise
	//Qty
	public function getQty_by_orderItemidAndDescription($poId,$trimGroup,$description){
		$Qty=$this->_setQty($this->_By_OrderItemidAndDescription);
		return $Qty[$poId][$trimGroup][$description];
	}

	public function getQtyArray_by_orderItemidAndDescription(){
		$Qty=$this->_setQty($this->_By_OrderItemidAndDescription);
		return $Qty;

	}
	//Amount
	public function getAmount_by_orderItemidAndDescription($poId,$trimGroup,$description){
		$Amount=$this->_setAmount($this->_By_OrderItemidAndDescription);
		return $Amount[$poId][$trimGroup][$description];
	}

	public function getAmountArray_by_orderItemidAndDescription(){
		$Amount=$this->_setAmount($this->_By_OrderItemidAndDescription);
		return $Amount;
	}



	// Order and Country wise
	//Qty
	public function getQty_by_orderAndCountry($poId,$countryId){
		$Qty=$this->_setQty($this->_By_OrderAndCountry);
		return $Qty[$poId][$countryId];
	}

	public function getQtyArray_by_orderAndCountry(){
		$Qty=$this->_setQty($this->_By_OrderAndCountry);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderAndCountry($poId,$countryId){
		$Amount=$this->_setAmount($this->_By_OrderAndCountry);
		return $Amount[$poId][$countryId];
	}
	public function getAmountArray_by_orderAndCountry(){
		$Amount=$this->_setAmount($this->_By_OrderAndCountry);
		return $Amount;
	}





	//Order,Country and Conversion Id wise
	//Qty
	public function getQty_by_orderCountryAndPrecostdtlsid($poId,$countryId,$preCostDtlsId){
		$Qty=$this->_setQty($this->_By_OrderCountryAndPrecostdtlsid);
		return $Qty[$poId][$countryId][$preCostDtlsId];
	}

	public function getQtyArray_by_orderCountryAndPrecostdtlsid(){
		$Qty=$this->_setQty($this->_By_OrderCountryAndPrecostdtlsid);
		return $Qty;

	}
	//Amount
	public function getAmount_by_orderCountryAndPrecostdtlsid($poId,$countryId,$preCostDtlsId){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndPrecostdtlsid);
		return $Amount[$poId][$countryId][$preCostDtlsId];
	}

	public function getAmountArray_by_orderCountryAndPrecostdtlsid(){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndPrecostdtlsid);
		return $Amount;
	}


	//Order,Country and Fabric wise
	//Qty
	public function getQty_by_orderCountryAndItemid($poId,$countryId,$trimGroup){
		$Qty=$this->_setQty($this->_By_OrderCountryAndItemid);
		return $Qty[$poId][$countryId][$trimGroup];
	}

	public function getQtyArray_by_orderCountryAndItemid(){
		$Qty=$this->_setQty($this->_By_OrderCountryAndItemid);
		return $Qty;

	}
	//Amount
	public function getAmount_by_orderCountryAndItemid($poId,$countryId,$trimGroup){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndItemid);
		return $Amount[$poId][$countryId][$trimGroup];
	}

	public function getAmountArray_by_orderCountryAndItemid(){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndItemid);
		return $Amount;
	}



	//Order,Country Fabric and Process wise
	//Qty
	public function getQty_by_orderCountryItemidAndDescription($poId,$countryId,$trimGroup,$description){
		$Qty=$this->_setQty($this->_By_OrderCountryItemidAndDescription);
		return $Qty[$poId][$countryId][$trimGroup][$description];
	}

	public function getQtyArray_by_orderCountryItemidAndDescription(){
		$Qty=$this->_setQty($this->_By_OrderCountryItemidAndDescription);
		return $Qty;

	}
	//Amount
	public function getAmount_by_orderCountryItemidAndDescription($poId,$countryId,$trimGroup,$description){
		$Amount=$this->_setAmount($this->_By_OrderCountryItemidAndDescription);
		return $Amount[$poId][$countryId][$trimGroup][$description];
	}

	public function getAmountArray_by_orderCountryItemidAndDescription(){
		$Amount=$this->_setAmount($this->_By_OrderCountryItemidAndDescription);
		return $Amount;
	}
	// Order and Gmts Item wise
	//Qty
	public function getQty_by_orderAndGmtsitem($poId,$gmtsItem){
		$Qty=$this->_setQty($this->_By_OrderAndGmtsitem);
		return $Qty[$poId][$gmtsItem];
	}

	public function getQtyArray_by_orderAndGmtsitem(){
		$Qty=$this->_setQty($this->_By_OrderAndGmtsitem);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderAndGmtsitem($poId,$gmtsItem){
		$Amount=$this->_setAmount($this->_By_OrderAndGmtsitem);
		return $Amount[$poId][$gmtsItem];
	}

	public function getAmountArray_by_orderAndGmtsitem(){
		$Amount=$this->_setAmount($this->_By_OrderAndGmtsitem);
		return $Amount;
	}

	// Order and Gmts Color wise
	//Qty
	public function getQty_by_orderAndGmtscolor($poId,$colorId){
		$Qty=$this->_setQty($this->_By_OrderAndGmtscolor);
		return $Qty[$poId][$colorId];
	}

	public function getQtyArray_by_orderAndGmtscolor(){
		$Qty=$this->_setQty($this->_By_OrderAndGmtscolor);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderAndGmtscolor($poId,$colorId){
		$Amount=$this->_setAmount($this->By_OrderAndGmtscolor);
		return $Amount[$poId][$colorId];
	}

	public function getAmountArray_by_orderAndGmtscolor(){
		$Amount=$this->_setAmount($this->_By_OrderAndGmtscolor);
		return $Amount;
	}

	// Order and Gmts Size wise
	//Qty
	public function getQty_by_orderAndGmtssize($poId,$sizeId){
		$Qty=$this->_setQty($this->_By_OrderAndGmtssize);
		return $Qty[$poId][$sizeId];
	}

	public function getQtyArray_by_orderAndGmtssize(){
		$Qty=$this->_setQty($this->_By_OrderAndGmtssize);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderAndGmtssize($poId,$sizeId){
		$Amount=$this->_setAmount($this->_By_OrderAndGmtssize);
		return $Amount[$poId][$sizeId];
	}

	public function getAmountArray_by_orderAndGmtssize(){
		$Amount=$this->_setAmount($this->_By_OrderAndGmtssize);
		return $Amount;
	}



	// Order,Country and Gmts Item wise
	//Qty
	public function getQty_by_orderCountryAndGmtsitem($poId,$countryId,$gmtsItem){
		$Qty=$this->_setQty($this->_By_orderCountryAndGmtsitem);
		return $Qty[$poId][$countryId][$gmtsItem];
	}

	public function getQtyArray_by_orderCountryAndGmtsitem(){
		$Qty=$this->_setQty($this->_By_orderCountryAndGmtsitem);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderCountryAndGmtsitem($poId,$countryId,$gmtsItem){
		$Amount=$this->_setAmount($this->_By_orderCountryAndGmtsitem);
		return $Amount[$poId][$countryId][$gmtsItem];
	}
	public function getAmountArray_by_orderCountryAndGmtsitem(){
		$Amount=$this->_setAmount($this->_By_orderCountryAndGmtsitem);
		return $Amount;
	}

	// Order and Country And Color Wise
	//Qty
	public function getQty_by_orderCountryAndGmtscolor($poId,$countryId,$colorId){
		$Qty=$this->_setQty($this->_By_OrderCountryAndGmtscolor);
		return $Qty[$poId][$countryId][$colorId];
	}

	public function getQtyArray_by_orderCountryAndGmtscolor(){
		$Qty=$this->_setQty($this->_By_OrderCountryAndGmtscolor);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderCountryAndGmtscolor($poId,$countryId,$colorId){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndGmtscolor);
		return $Amount[$poId][$countryId][$colorId];
	}
	public function getAmountArray_by_orderCountryAndGmtscolor(){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndGmtscolor);
		return $Amount;
	}

	// Order and Country And Size Wise
	//Qty
	public function getQty_by_orderCountryAndGmtssize($poId,$countryId,$sizeId){
		$Qty=$this->_setQty($this->_By_OrderCountryAndGmtssize);
		return $Qty[$poId][$countryId][$sizeId];
	}

	public function getQtyArray_by_orderCountryAndGmtssize(){
		$Qty=$this->_setQty($this->_By_OrderCountryAndGmtssize);
		return $Qty;
	}
	//Amount
	public function getAmount_by_orderCountryAndGmtssize($poId,$countryId,$sizeId){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndGmtssize);
		return $Amount[$poId][$countryId][$sizeId];
	}
	public function getAmountArray_by_orderCountryAndGmtssize(){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndGmtssize);
		return $Amount;
	}

	// Order and Gmts Item And Color Wise
	//Qty
	public function getQty_by_orderGmtsitemAndGmtscolor($poId,$gmtsItem,$colorId){
		$Qty=$this->_setQty($this->_By_OrderGmtsitemAndGmtscolor);
		return $Qty[$poId][$gmtsItem][$colorId];
	}

	public function getQtyArray_by_orderGmtsitemAndGmtscolor(){
		$Qty=$this->_setQty($this->_By_OrderGmtsitemAndGmtscolor);
		return $Qty;
	}


	//Amount
	public function getAmount_by_orderGmtsitemAndGmtscolor($poId,$gmtsItem,$colorId){
		$Amount=$this->_setAmount($this->_By_OrderGmtsitemAndGmtscolor);
		return $Amount[$poId][$gmtsItem][$colorId];
	}
	public function getAmountArray_by_orderGmtsitemAndGmtscolor(){
		$Amount=$this->_setAmount($this->_By_OrderGmtsitemAndGmtscolor);
		return $Amount;
	}

	// Order and Gmts Item And Size Wise=============================================================================
	//Qty
	public function getQty_by_orderGmtsitemAndGmtssize($poId,$gmtsItem,$sizeId){
		$Qty=$this->_setQty($this->_By_OrderGmtsitemAndGmtssize);
		return $Qty[$poId][$gmtsItem][$sizeId];
	}

	public function getQtyArray_by_orderGmtsitemAndGmtssize(){
		$Qty=$this->_setQty($this->_By_OrderGmtsitemAndGmtssize);
		return $Qty;
	}


	//Amount
	public function getAmount_by_orderGmtsitemAndGmtssize($poId,$gmtsItem,$sizeId){
		$Amount=$this->_setAmount($this->_By_OrderGmtsitemAndGmtssize);
		return $Amount[$poId][$gmtsItem][$sizeId];
	}
	public function getAmountArray_by_orderGmtsitemAndGmtssize(){
		$Amount=$this->_setAmount($this->_By_OrderGmtsitemAndGmtssize);
		return $Amount;
	}

	// Order and Gmts Color And Size Wise=============================================================================
	//Qty
	public function getQty_by_orderGmtscolorAndGmtssize($poId,$colorId,$sizeId){
		$Qty=$this->_setQty($this->_By_OrderGmtscolorAndGmtssize);
		return $Qty[$poId][$colorId][$sizeId];
	}

	public function getQtyArray_by_orderGmtscolorAndGmtssize(){
		$Qty=$this->_setQty($this->_By_OrderGmtscolorAndGmtssize);
		return $Qty;
	}


	//Amount
	public function getAmount_by_orderGmtscolorAndGmtssize($poId,$colorId,$sizeId){
		$Amount=$this->_setAmount($this->_By_OrderGmtscolorAndGmtssize);
		return $Amount[$poId][$colorId][$sizeId];
	}
	public function getAmountArray_by_orderGmtscolorAndGmtssize(){
		$Amount=$this->_setAmount($this->_By_OrderGmtscolorAndGmtssize);
		return $Amount;
	}

	// Order,Country,Gmts Item  and Gmts Color wise
	//Qty
	public function getQty_by_orderCountryGmtsitemAndGmtscolor($poId,$countryId,$gmtsItem,$colorId){
		$Qty=$this->_setQty($this->_By_orderCountryGmtsitemAndGmtscolor);
		return $Qty[$poId][$countryId][$gmtsItem][$colorId];
	}

	public function getQtyArray_by_orderCountryGmtsitemAndGmtscolor(){
		$Qty=$this->_setQty($this->_By_orderCountryGmtsitemAndGmtscolor);
		return $Qty;
	}


	//Amount
	public function getAmount_by_orderCountryGmtsitemAndGmtscolor($poId,$countryId,$gmtsItem,$colorId){
		$Amount=$this->_setAmount($this->_By_orderCountryGmtsitemAndGmtscolor);
		return $Amount[$poId][$countryId][$gmtsItem][$colorId];
	}
	public function getAmountArray_by_orderCountryGmtsitemAndGmtscolor(){
		$Amount=$this->_setAmount($this->_By_orderCountryGmtsitemAndGmtscolor);
		return $Amount;
	}

	// Order,Country,Gmts Item  and Gmts Size wise
	//Qty
	public function getQty_by_orderCountryGmtsitemAndGmtssize($poId,$countryId,$gmtsItem,$sizeId){
		$Qty=$this->_setQty($this->_By_orderCountryGmtsitemAndGmtssize);
		return $Qty[$poId][$countryId][$gmtsItem][$sizeId];
	}

	public function getQtyArray_by_orderCountryGmtsitemAndGmtssize(){
		$Qty=$this->_setQty($this->_By_orderCountryGmtsitemAndGmtssize);
		return $Qty;
	}


	//Amount
	public function getAmount_by_orderCountryGmtsitemAndGmtssize($poId,$countryId,$gmtsItem,$sizeId){
		$Amount=$this->_setAmount($this->_By_orderCountryGmtsitemAndGmtssize);
		return $Amount[$poId][$countryId][$gmtsItem][$sizeId];
	}
	public function getAmountArray_by_orderCountryGmtsitemAndGmtssize(){
		$Amount=$this->_setAmount($this->_By_orderCountryGmtsitemAndGmtssize);
		return $Amount;
	}

	// Order,Country,Gmts Color  and Gmts Size wise
	//Qty
	public function getQty_by_orderCountryGmtscolorAndGmtssize($poId,$countryId,$colorId,$sizeId){
		$Qty=$this->_setQty($this->_By_orderCountryGmtscolorAndGmtssize);
		return $Qty[$poId][$countryId][$colorId][$sizeId];
	}

	public function getQtyArray_by_orderCountryGmtscolorAndGmtssize(){
		$Qty=$this->_setQty($this->_By_orderCountryGmtscolorAndGmtssize);
		return $Qty;
	}


	//Amount
	public function getAmount_by_orderCountryGmtscolorAndGmtssize($poId,$countryId,$colorId,$sizeId){
		$Amount=$this->_setAmount($this->_By_orderCountryGmtscolorAndGmtssize);
		return $Amount[$poId][$countryId][$colorId][$sizeId];
	}
	public function getAmountArray_by_orderCountryGmtscolorAndGmtssize(){
		$Amount=$this->_setAmount($this->_By_orderCountryGmtscolorAndGmtssize);
		return $Amount;
	}

	// Order,Gmts item,Gmts Color  and Gmts Size wise
	//Qty
	public function getQty_by_orderGmtsitemGmtscolorAndGmtssize($poId,$gmtsItem,$colorId,$sizeId){
		$Qty=$this->_setQty($this->_By_orderGmtsitemGmtscolorAndGmtssize);
		return $Qty[$poId][$gmtsItem][$colorId][$sizeId];
	}

	public function getQtyArray_by_orderGmtsitemGmtscolorAndGmtssize(){
		$Qty=$this->_setQty($this->_By_orderGmtsitemGmtscolorAndGmtssize);
		return $Qty;
	}


	//Amount
	public function getAmount_by_orderGmtsitemGmtscolorAndGmtssize($poId,$gmtsItem,$colorId,$sizeId){
		$Amount=$this->_setAmount($this->_By_orderGmtsitemGmtscolorAndGmtssize);
		return $Amount[$poId][$gmtsItem][$colorId][$sizeId];
	}
	public function getAmountArray_by_orderGmtsitemGmtscolorAndGmtssize(){
		$Amount=$this->_setAmount($this->_By_orderGmtsitemGmtscolorAndGmtssize);
		return $Amount;
	}

	// Order,Country,Gmts Item, Gmts Color and Gmts size wise
	//Qty
	public function getQty_by_orderCountryGmtsitemGmtscolorAndGmtssize($poId,$countryId,$gmtsItem,$colorId,$sizeId){
		$Qty=$this->_setQty($this->_By_orderCountryGmtsitemGmtscolorAndGmtssize);
		return $Qty[$poId][$countryId][$gmtsItem][$colorId][$sizeId];
	}

	public function getQtyArray_by_orderCountryGmtsitemGmtscolorAndGmtssize(){
		$Qty=$this->_setQty($this->_By_orderCountryGmtsitemGmtscolorAndGmtssize);
		return $Qty;
	}


	//Amount
	public function getAmount_by_orderCountryGmtsitemGmtscolorAndGmtssize($poId,$countryId,$gmtsItem,$colorId,$sizeId){
		$Amount=$this->_setAmount($this->_By_orderCountryGmtsitemGmtscolorAndGmtssize);
		return $Amount[$poId][$countryId][$gmtsItem][$colorId][$sizeId];
	}
	public function getAmountArray_by_orderCountryGmtsitemGmtscolorAndGmtssize(){
		$Amount=$this->_setAmount($this->_By_orderCountryGmtsitemGmtscolorAndGmtssize);
		return $Amount;
	}

	// Item wise
	//Qty
	public function getQty_by_itemid($trimGroup){
		$Qty=$this->_setQty($this->_By_Itemid);
		return $Qty[$trimGroup];
	}

	public function getQtyArray_by_itemid(){
		$Qty=$this->_setQty($this->_By_Itemid);
		return $Qty;

	}
	//Amount
	public function getAmount_by_itemid($trimGroup){
		$Amount=$this->_setAmount($this->_By_Itemid);
		return $Amount[$trimGroup];
	}

	public function getAmountArray_by_itemid(){
		$Amount=$this->_setAmount($this->_By_Itemid);
		return $Amount;
	}

	// Item and description wise
	//Qty
	public function getQty_by_itemidAndDescription($trimGroup){
		$Qty=$this->_setQty($this->_By_ItemidAndDescription);
		return $Qty[$trimGroup];
	}

	public function getQtyArray_by_itemidAndDescription(){
		$Qty=$this->_setQty($this->_By_ItemidAndDescription);
		return $Qty;

	}
	//Amount
	public function getAmount_by_itemidAndDescription($trimGroup){
		$Amount=$this->_setAmount($this->_By_ItemidAndDescription);
		return $Amount[$trimGroup];
	}

	public function getAmountArray_by_itemidAndDescription(){
		$Amount=$this->_setAmount($this->_By_ItemidAndDescription);
		return $Amount;
	}

	//Trimcost id Id wise
	//Qty
	public function getQty_by_precostdtlsid($preCostDtlsId){
		$Qty=$this->_setQty($this->_By_Precostdtlsid);
		return $Qty[$preCostDtlsId];
	}

	public function getQtyArray_by_precostdtlsid(){
		$Qty=$this->_setQty($this->_By_Precostdtlsid);
		return $Qty;

	}
	public function getQtyArray_by_precostdtlsid_consAndTotcons(){
		$Qty=$this->_setQty($this->_By_Precostdtlsid_consAndTotcons);
		return $Qty;

	}
	public function getQtyArray_by_precostdtlsidAndGmtssize_consAndTotcons(){
		$Qty=$this->_setQty($this->_By_PrecostdtlsidAndGmtssize_consAndTotcons);
		return $Qty;

	}
	// Precostdtls id And Job No
	public function getQtyArray_by_jobAndPrecostdtlsid_consAndTotcons(){
		$Qty=$this->_setQty($this->_By_JobAndPrecostdtlsid_consAndTotcons);
		return $Qty;

	}
	//Trimcost id and country wise
	//Qty
	public function getQtyArray_by_precostdtlsidAndCountry(){
		$Qty=$this->_setQty($this->_By_PrecostdtlsidAndCountry);
		return $Qty;
	}
	//Amount
		public function getAmountArray_precostdtlsidAndCountry(){
		$Amount=$this->_setAmount($this->_By_PrecostdtlsidAndCountry);
		return $Amount;
	}
	//Amount Sourcing
		public function getAmountArray_precostdtlsidAndCountrySourcing(){
		$Amount=$this->_setAmount($this->_By_PrecostdtlsidAndCountrySourcing);
		return $Amount;
	}

	//Amount 
	public function getAmount_by_precostdtlsid($preCostDtlsId){
		$Amount=$this->_setAmount($this->_By_Precostdtlsid);
		return $Amount[$preCostDtlsId];
	}

	public function getAmountArray_precostdtlsid(){
		$Amount=$this->_setAmount($this->_By_Precostdtlsid);
		return $Amount;
	}
	//Amount Sourcing
	public function getAmount_by_precostdtlsidSourcing($preCostDtlsId){
		$Amount=$this->_setAmount($this->_By_PrecostdtlsidSourcing);
		return $Amount[$preCostDtlsId];
	}

	public function getAmountArray_precostdtlsidSourcing(){
		$Amount=$this->_setAmount($this->_By_PrecostdtlsidSourcing);
		return $Amount;
	}

	public function getAmountArray_by_precostdtlsidAndGmtssize_consAndTotcons(){
		$Amount=$this->_setAmount($this->_By_PrecostdtlsidAndGmtssize_consAndTotcons);
		return $Amount;

	}
	public function getAmountArray_by_jobAndPrecostdtlsid_consAndTotcons(){
		$Amount=$this->_setAmount($this->_By_JobAndPrecostdtlsid_consAndTotcons);
		return $Amount;

	}

	//Order,Country,Gmtsitem And Precostdtlsid wise
	//Qty

	public function getQtyArray_by_orderCountryGmtsitemAndPrecostdtlsid(){
		$Qty=$this->_setQty($this->_By_orderCountryGmtsitemAndPrecostdtlsid);
		return $Qty;

	}
	//Amount

	public function getAmountArray_by_orderCountryGmtsitemAndPrecostdtlsid(){
		$Amount=$this->_setAmount($this->_By_orderCountryGmtsitemAndPrecostdtlsid);
		return $Amount;
	}
	//Amount Sourcing

	public function getAmountArray_by_orderCountryGmtsitemAndPrecostdtlsidSourcing(){
		$Amount=$this->_setAmount($this->_By_orderCountryGmtsitemAndPrecostdtlsidSourcing);
		return $Amount;
	}

	//Order  Trimcost Id and GmtsColor wise
	//Qty
	public function getQtyArray_by_orderPrecostdtlsidAndGmtscolor(){
		$Qty=$this->_setQty($this->_By_OrderPrecostdtlsidAndGmtscolor);
		return $Qty;

	}
	//Amount
	public function getAmountArray_by_orderAndPrecostdtlsidAndGmtscolor(){
		$Amount=$this->_setAmount($this->_By_OrderPrecostdtlsidAndGmtscolor);
		return $Amount;
	}
	//Sourcing 
	public function getAmountArray_by_orderAndPrecostdtlsidAndGmtscolorSourcing(){
		$Amount=$this->_setAmount($this->_By_OrderPrecostdtlsidAndGmtscolorSourcing);
		return $Amount;
	}

	//Order,country,trimcost id and gmts color
	//Qty
	public function getQtyArray_by_orderCountryPrecostdtlsidAndGmtscolor(){
		$Qty=$this->_setQty($this->_By_OrderCountryPrecostdtlsidAndGmtscolor);
		return $Qty;

	}
	//Amount
	public function getAmountArray_by_orderCountryPrecostdtlsidAndGmtscolor(){
		$Amount=$this->_setAmount($this->_By_OrderCountryPrecostdtlsidAndGmtscolor);
		return $Amount;
	}

	//Order  Trimcost Id and Gmtssize wise
	//Qty
	public function getQtyArray_by_orderPrecostdtlsidAndGmtssize(){
		$Qty=$this->_setQty($this->_By_OrderPrecostdtlsidAndGmtssize);
		return $Qty;

	}
	//Amount
	public function getAmountArray_by_orderAndPrecostdtlsidAndGmtssize(){
		$Amount=$this->_setAmount($this->_By_OrderPrecostdtlsidAndGmtssize);
		return $Amount;
	}

	//Order, Country,Trimcost Id and Gmtssize wise
	//Qty
	public function getQtyArray_by_orderCountryPrecostdtlsidAndGmtssize(){
		$Qty=$this->_setQty($this->_By_OrderCountryPrecostdtlsidAndGmtssize);
		return $Qty;

	}
	//Amount
	public function getAmountArray_by_orderCountryPrecostdtlsidAndGmtssize(){
		$Amount=$this->_setAmount($this->_By_OrderCountryPrecostdtlsidAndGmtssize);
		return $Amount;
	}


	//Order  Trimcost Id and Gmtssize wise and Article
	//Qty
	public function getQtyArray_by_orderPrecostdtlsidGmtssizeAndArticle(){
		$Qty=$this->_setQty($this->_By_OrderPrecostdtlsidGmtssizeAndArticle);
		return $Qty;

	}
	//Order  Trimcost Id and Gmtssize wise Item size and Article
	//Qty
	public function getQtyArray_by_orderPrecostdtlsidGmtssizeItemsizeAndArticle(){
		$Qty=$this->_setQty($this->_By_OrderPrecostdtlsidGmtssizeItemsizeAndArticle);
		return $Qty;

	} 
	//Amount
	public function getAmountArray_by_orderAndPrecostdtlsidGmtssizeItemsizeAndArticle(){
		$Amount=$this->_setAmount($this->_By_OrderPrecostdtlsidGmtssizeItemsizeAndArticle);
		return $Amount;
	}
	//Order  Item Id  GmtsColor and Gmtssize wise
	//Qty
	public function getQtyArray_by_orderItemidGmtscolorAndrGmtssize(){
		$Qty=$this->_setQty($this->_By_OrderItemidGmtscolorAndrGmtssize);
		return $Qty;

	}
	//Amount
	public function getAmountArray_by_orderAndPrecostdtlsidGmtssizeAndArticle(){
		$Amount=$this->_setAmount($this->_By_OrderPrecostdtlsidGmtssizeAndArticle);
		return $Amount;
	}
	//Amount
	public function getAmountArray_by_orderItemidGmtscolorAndrGmtssize(){
		$Amount=$this->_setAmount($this->_By_OrderItemidGmtscolorAndrGmtssize);
		return $Amount;
	}

	//Order  Trimcost Id and GmtsColor Gmts size wise
	//Qty
	public function getQtyArray_by_OrderPrecostdtlsidGmtscolorAndGmtssize(){
		$Qty=$this->_setQty($this->_By_OrderPrecostdtlsidGmtscolorAndGmtssize);
		return $Qty;

	}
	//Amount
	public function getAmountArray_by_OrderPrecostdtlsidGmtscolorAndGmtssize(){
		$Amount=$this->_setAmount($this->_By_OrderPrecostdtlsidGmtscolorAndGmtssize);
		return $Amount;
	}
	//Amount-Sourcing
	public function getAmountArray_by_OrderPrecostdtlsidGmtscolorAndGmtssizeSourcing(){
		$Amount=$this->_setAmount($this->_By_OrderPrecostdtlsidGmtscolorAndGmtssizeSourcing);
		return $Amount;
	}
	//Order  Trimcost Id and GmtsColor Gmts size wise
	//Trimcost Id and GmtsColor Item size wise
	//Qty
	public function getQtyArray_by_PrecostdtlsidGmtscolorAndItemsize(){
		$Qty=$this->_setQty($this->_By_PrecostdtlsidGmtscolorAndItemSize);
		return $Qty;

	}
	//Qty
	public function getQtyArray_by_OrderCountryPrecostdtlsidGmtscolorAndGmtssize(){
		$Qty=$this->_setQty($this->_By_OrderCountryPrecostdtlsidGmtscolorAndGmtssize);
		return $Qty;

	}
	//Amount
	public function getAmountArray_by_OrderCountryPrecostdtlsidGmtscolorAndGmtssize(){
		$Amount=$this->_setAmount($this->_By_OrderCountryPrecostdtlsidGmtscolorAndGmtssize);
		return $Amount;
	}
	//Amount Sourcing
	public function getAmountArray_by_OrderCountryPrecostdtlsidGmtscolorAndGmtssizeSourcing(){
		$Amount=$this->_setAmount($this->_By_OrderCountryPrecostdtlsidGmtscolorAndGmtssizeSourcing);
		return $Amount;
	}


	//Order  Trimcost Id , GmtsColor, Gmts size and article wise 
	//Qty
	public function getQtyArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticle(){
		$Qty=$this->_setQty($this->_By_OrderPrecostdtlsidGmtscolorGmtssizeAndArticle);
		return $Qty;

	}
	//Amount
	public function getAmountArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticle(){
		$Amount=$this->_setAmount($this->_By_OrderPrecostdtlsidGmtscolorGmtssizeAndArticle);
		return $Amount;
	}
	
	//Order  Trimcost Id , GmtsColor, Gmts size and article wise ItemColor
	//Qty
	public function getQtyArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColor(){
		$Qty=$this->_setQty($this->_By_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColor);
		return $Qty;

	}
	//Amount
	public function getAmountArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColor(){
		$Amount=$this->_setAmount($this->_By_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColor);
		return $Amount;
	}
	
	//Order  Trimcost Id , GmtsColor, Gmts size and article wise ItemColor ItemSize
	//Qty
	public function getQtyArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColorItemSize(){
		$Qty=$this->_setQty($this->_By_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColorItemSize);
		return $Qty;

	}
	//Amount
	public function getAmountArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColorItemSize(){
		$Amount=$this->_setAmount($this->_By_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColorItemSize);
		return $Amount;
	}
	

	//Order,Country,Gmtsitem Gmtscolor And Precostdtlsid wise
	//Qty

	public function getQtyArray_by_orderCountryGmtsitemGmtscolorAndPrecostdtlsid(){
		$Qty=$this->_setQty($this->_By_orderCountryGmtsitemGmtscolorAndPrecostdtlsid);
		return $Qty;

	}
	//Amount

	public function getAmountArray_by_orderCountryGmtsitemGmtscolorAndPrecostdtlsid(){
		$Amount=$this->_setAmount($this->_By_orderCountryGmtsitemGmtscolorAndPrecostdtlsid);
		return $Amount;
	}

	//Order,Country,Gmtsitem Gmtssize And Precostdtlsid wise
	//Qty

	public function getQtyArray_by_orderCountryGmtsitemGmtssizeAndPrecostdtlsid(){
		$Qty=$this->_setQty($this->_By_orderCountryGmtsitemGmtssizeAndPrecostdtlsid);
		return $Qty;

	}
	//Amount

	public function getAmountArray_by_orderCountryGmtsitemGmtssizeAndPrecostdtlsid(){
		$Amount=$this->_setAmount($this->_By_orderCountryGmtsitemGmtssizeAndPrecostdtlsid);
		return $Amount;
	}

	//Order,Country,Gmtsitem Gmtscolor Gmtssize And Precostdtlsid wise
	//Qty

	public function getQtyArray_by_orderCountryGmtsitemGmtscolorGmtssizeAndPrecostdtlsid(){
		$Qty=$this->_setQty($this->_By_orderCountryGmtsitemGmtscolorGmtssizeAndPrecostdtlsid);
		return $Qty;

	}
	//Amount

	public function getAmountArray_by_orderCountryGmtsitemGmtscolorGmtssizeAndPrecostdtlsid(){
		$Amount=$this->_setAmount($this->_By_orderCountryGmtsitemGmtscolorGmtssizeAndPrecostdtlsid);
		return $Amount;
	}



	function __destruct() {
		parent::__destruct();
		unset($this->_dataArray);
	}
}
?>