<?
class emblishment extends report{
	private $_By_JobAndEmblishmentid='By_JobAndEmblishmentid';
	private $_By_JobAndEmbname='By_JobAndEmbname';
	private $_By_JobAndEmbtype='By_JobAndEmbtype';
	private $_By_JobEmbnameAndEmbtype='By_JobEmbnameAndEmbtype';
	private $_By_JobEmbnameAndEmbtypeColor='By_JobEmbnameAndEmbtypeColor';

	private $_By_OrderAndEmblishmentid='By_OrderAndEmblishmentid';
	private $_By_OrderEmblishmentidAndGmtsitem='By_OrderEmblishmentidAndGmtsitem';
	private $_By_OrderEmblishmentidAndGmtsitemSourcing='By_OrderEmblishmentidAndGmtsitemSourcing';
	private $_By_OrderAndEmbname='By_OrderAndEmbname';
	private $_By_OrderAndEmbtype='By_OrderAndEmbtype';
	private $_By_OrderEmbnameAndEmbtype='By_OrderEmbnameAndEmbtype';

	private $_By_OrderCountryAndEmblishmentid='By_OrderCountryAndEmblishmentid';
	private $_By_OrderCountryAndEmbname='By_OrderCountryAndEmbname';
	private $_By_OrderCountryAndEmbtype='By_OrderCountryAndEmbtype';
	private $_By_OrderCountryEmbnameAndEmbtype='By_OrderCountryEmbnameAndEmbtype';
	private $_By_Emblishmentid='By_Emblishmentid';
	private $_By_OrderGmtsitemGmtscolorAndEmblishmentid='By_OrderGmtsitemGmtscolorAndEmblishmentid';
	private $_By_OrderGmtsitemGmtscolorAndEmbName='By_OrderGmtsitemGmtscolorAndEmbName';
	private $_By_OrderGmtsitemGmtssizeAndEmblishmentid='By_OrderGmtsitemGmtssizeAndEmblishmentid';

	private $_By_OrderCountryGmtsitemGmtscolorAndEmblishmentid='By_OrderCountryGmtsitemGmtscolorAndEmblishmentid';

	private $_By_OrderEmblishmentidAndGmtscolor='By_OrderEmblishmentidAndGmtscolor';
	private $_By_OrderEmblishmentidAndGmtssize='By_OrderEmblishmentidAndGmtssize';

	private $_By_OrderEmblishmentidGmtscolorAndGmtssize='By_OrderEmblishmentidGmtscolorAndGmtssize';
	private $_By_OrderEmblishmentidGmtssizeAndArticle='By_OrderEmblishmentidGmtssizeAndArticle';
	private $_By_OrderEmblishmentidGmtscolorGmtssizeAndArticle='By_OrderEmblishmentidGmtscolorGmtssizeAndArticle';

	private $_By_OrderEmblishmentidGmtscolorAndGmtsitem='By_OrderEmblishmentidGmtscolorAndGmtsitem';
	private $_By_OrderEmblishmentidGmtssizeAndGmtsitem='By_OrderEmblishmentidGmtssizeAndGmtsitem';

	private $_By_OrderEmblishmentidGmtscolorGmtssizeAndGmtsitem='By_OrderEmblishmentidGmtscolorGmtssizeAndGmtsitem';
	private $_By_OrderEmblishmentidGmtssizeArticleAndGmtsitem='By_OrderEmblishmentidGmtssizeArticleAndGmtsitem';
	private $_By_OrderEmblishmentidGmtscolorGmtssizeArticleAndGmtsitem='By_OrderEmblishmentidGmtscolorGmtssizeArticleAndGmtsitem';


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
		//$this->_query='select a.job_no AS "job_no",a.total_set_qnty AS "total_set_qnty",b.id AS "id",c.item_number_id AS "item_number_id",c.country_id AS "country_id",c.color_number_id AS "color_number_id",c.size_number_id AS "size_number_id",c.order_quantity AS "order_quantity",c.plan_cut_qnty AS "plan_cut_qnty",d.id AS "emblishment_id",d.emb_name AS "emb_name",d.emb_type AS "emb_type",d.cons_dzn_gmts AS "cons_dzn_gmts",d.rate AS "rate",d.amount AS "amount"   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_embe_cost_dtls d  where 1=1 '.$this->cond.' and d.emb_name in(1,2,4,5) and cons_dzn_gmts>0 and  a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no  and b.id=c.po_break_down_id   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1';//order by b.id,d.id
		if($this->_is_approval_histry==2 && $this->_is_sweater==0 ){
				$this->_query='select a.job_no AS "job_no",a.total_set_qnty AS "total_set_qnty",b.po_id AS "id",c.item_number_id AS "item_number_id",c.country_id AS "country_id",c.color_number_id AS "color_number_id" ,c.size_number_id AS "size_number_id",c.article_number AS "article_number",c.order_quantity AS "order_quantity" ,c.plan_cut_qnty AS "plan_cut_qnty",d.pre_cost_embe_cost_dtls_id AS "emblishment_id",d.emb_name AS "emb_name",d.emb_type AS "emb_type",d.cons_dzn_gmts AS "cons_dzn_gmts_mst",d.rate AS "rate_mst",d. amount AS "amount_mst", d.budget_on AS "budget_on", e.requirment AS "cons_dzn_gmts",e.rate AS "rate",e. amount AS "amount",e.country_id AS "country_id_emb", e.sourcing_rate AS "sourcing_rate"   from wo_po_dtls_mst_his a, wo_po_break_down_his b,wo_po_color_size_his c,wo_pre_cost_embe_cost_dtls_his d , wo_pre_emb_avg_con_dtls_h e where 1=1 '.$this->cond.' and d.emb_name in(1,2,4,5,99) and cons_dzn_gmts>0 and  a.job_id=b.job_id and a.approved_no=b.approved_no and a.approval_page=b.approval_page and a.job_id=c.job_id and a.approved_no=c.approved_no and a.approval_page=c.approval_page and a.job_id=d.job_id and a.approved_no=d.approved_no and a.approval_page=d.approval_page and a.job_id=e.job_id and a.approved_no=e.approved_no and a.approval_page=e.approval_page and b.po_id=c.po_break_down_id and b.po_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and d.pre_cost_embe_cost_dtls_id=e.pre_cost_emb_cost_dtls_id    and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1) and c.is_deleted=0 and c.status_active in(1)  and d.is_deleted=0 and d.status_active=1';//order by b.id,d.id
		}
		else{
			$this->_query='select a.job_no AS "job_no",a.total_set_qnty AS "total_set_qnty",b.id AS "id",c.item_number_id AS "item_number_id",c.country_id AS "country_id",c.color_number_id AS "color_number_id" ,c.size_number_id AS "size_number_id",c.article_number AS "article_number",c.order_quantity AS "order_quantity" ,c.plan_cut_qnty AS "plan_cut_qnty",d.id AS "emblishment_id",d.emb_name AS "emb_name",d.emb_type AS "emb_type",d.cons_dzn_gmts AS "cons_dzn_gmts_mst",d.rate AS "rate_mst",d. amount AS "amount_mst", d.budget_on AS "budget_on", e.requirment AS "cons_dzn_gmts",e.rate AS "rate",e. amount AS "amount",e.country_id AS "country_id_emb", e.sourcing_rate AS "sourcing_rate"   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_embe_cost_dtls d , wo_pre_cos_emb_co_avg_con_dtls e where 1=1 '.$this->cond.' and d.emb_name in(1,2,4,5,99) and cons_dzn_gmts>0 and  a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id  and b.id=c.po_break_down_id and b.id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and d.id=e.pre_cost_emb_cost_dtls_id    and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1) and c.is_deleted=0 and c.status_active in(1)  and d.is_deleted=0 and d.status_active=1';//order by b.id,d.id
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
	  //return $reqyarnqnty =def_number_format(($plan_cut_qnty/($costingPerQty*$total_set_qnty))*$cons_qnty,5,"");
		if($total_set_qnty !=0 && $costingPerQty !=0){
			return $reqyarnqnty =($plan_cut_qnty/$total_set_qnty)*($cons_qnty/$costingPerQty);
		}
		else{
			return $reqyarnqnty =0;
		}

	}

	private function _calculateAmount($reqyarnqnty,$rate){
	 return $amount=$reqyarnqnty*$rate;
	}

	private function _setQty($level){
		$jobNo='';
		$poId='';
		$itemNumberId='';
		$countryId='';
		$colorId='';
		$sizeId='';
		$articleNumber='';
		$planPutQnty='';
		$orderQuantity='';
		$total_set_qnty='';
		$emblishmentId='';
		$embnameId='';
		$embtypeId='';
		$req_qnty='';
		$avg_req_qnty='';
		$charge_unit='';$sourcing_rate='';
		$budget_on=0;
		$country_id_emb='';
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
			$emblishmentId=$row[csf('emblishment_id')];
			$embnameId=$row[csf('emb_name')];
			$embtypeId=$row[csf('emb_type')];
			$req_qnty=$row[csf('cons_dzn_gmts')];
			$charge_unit=$row[csf('rate')];*/

			$jobNo=$row['job_no'];
			$poId=$row['id'];
			$itemNumberId=$row['item_number_id'];
			$countryId=$row['country_id'];
			$colorId=$row['color_number_id'];
			$sizeId=$row['size_number_id'];
			$articleNumber=$row['article_number'];
			$planPutQnty=$row['plan_cut_qnty'];
			$orderQuantity=$row['order_quantity'];
			$total_set_qnty=$row['total_set_qnty'];
			$emblishmentId=$row['emblishment_id'];
			$embnameId=$row['emb_name'];
			$embtypeId=$row['emb_type'];
			$req_qnty=$row['cons_dzn_gmts'];
			$charge_unit=$row['rate'];	
			$sourcing_rate=$row['sourcing_rate'];
			$budget_on=$row['budget_on'];
			$country_id_emb=$row['country_id_emb'];

			//$costingPerQty=$this->_costingPer($costingPerArray[$jobNo]);
			//$set_item_ratio=$gmtsitemRatioArray[$jobNo][$itemNumberId];
			//$costingPerQty=$this->_costingPer($this->_costingPerArr[$jobNo]);
			$costingPerQty=$this->_costingPerQtyArr[$jobNo];
			$set_item_ratio=$this->_gmtsitemRatioArray[$jobNo][$itemNumberId];
			$reqqnty=0;
			if($budget_on==1){
				if($country_id_emb==0 || $country_id_emb==''){
					$reqqnty=$this->_calculateQty($orderQuantity,$costingPerQty,$set_item_ratio,$req_qnty);
				}
				else{
					$country_id_array=explode(",",$country_id_emb);
					if (in_array($countryId, $country_id_array)) {
						$reqqnty=$this->_calculateQty($orderQuantity,$costingPerQty,$set_item_ratio,$req_qnty);
					}
				}
			}else if($budget_on==2){
				if($country_id_emb==0 || $country_id_emb==''){
					$reqqnty=$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$req_qnty);
				}
				else{
					$country_id_array=explode(",",$country_id_emb);
					if (in_array($countryId, $country_id_array)) {
						$reqqnty=$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$req_qnty);
					}
				}
			}else{
				if($country_id_emb==0 || $country_id_emb==''){
					$reqqnty=$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$req_qnty);
				}
				else{
					$country_id_array=explode(",",$country_id_emb);
					if (in_array($countryId, $country_id_array)) {
						$reqqnty=$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$req_qnty);
					}
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
			elseif($level==$this->_By_JobAndEmblishmentid){
				if(isset($Qty[$jobNo][$emblishmentId])){
					$Qty[$jobNo][$emblishmentId]+=$reqqnty;
				}
				else{
					$Qty[$jobNo][$emblishmentId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_JobAndEmbname){
				if(isset($Qty[$jobNo][$embnameId])){
					$Qty[$jobNo][$embnameId]+=$reqqnty;
				}
				else{
					$Qty[$jobNo][$embnameId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_JobAndEmbtype){
				if(isset($Qty[$jobNo][$embtypeId])){
					$Qty[$jobNo][$embtypeId]+=$reqqnty;
				}
				else{
					$Qty[$jobNo][$embtypeId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_JobEmbnameAndEmbtype){ 
				if(isset($Qty[$jobNo][$embnameId][$embtypeId])){
					$Qty[$jobNo][$embnameId][$embtypeId]+=$reqqnty;
				}
				else{
					$Qty[$jobNo][$embnameId][$embtypeId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_JobEmbnameAndEmbtypeColor){ 
				if(isset($Qty[$jobNo][$embnameId][$embtypeId][$colorId])){
					$Qty[$jobNo][$embnameId][$embtypeId][$colorId]+=$reqqnty;
				}
				else{
					$Qty[$jobNo][$embnameId][$embtypeId][$colorId]=$reqqnty;
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
			elseif($level==$this->_By_OrderAndEmblishmentid){
				if(isset($Qty[$poId][$emblishmentId])){
					$Qty[$poId][$emblishmentId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$emblishmentId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderEmblishmentidAndGmtsitem){
				if(isset($Qty[$poId][$emblishmentId][$itemNumberId])){
					$Qty[$poId][$emblishmentId][$itemNumberId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$emblishmentId][$itemNumberId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderAndEmbname){
				if(isset($Qty[$poId][$embnameId])){
					$Qty[$poId][$embnameId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$embnameId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderAndEmbtype){
				if(isset($Qty[$poId][$embtypeId])){
					$Qty[$poId][$embtypeId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$embtypeId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderEmbnameAndEmbtype){
				if(isset($Qty[$poId][$embnameId][$embtypeId])){
					$Qty[$poId][$embnameId][$embtypeId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$embnameId][$embtypeId]=$reqqnty;
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
			elseif($level==$this->_By_OrderCountryAndEmblishmentid){
				if(isset($Qty[$poId][$countryId][$emblishmentId])){
					$Qty[$poId][$countryId][$emblishmentId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$emblishmentId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderCountryAndEmbname){
				if(isset($Qty[$poId][$countryId][$embnameId])){
					$Qty[$poId][$countryId][$embnameId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$embnameId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderCountryAndEmbtype){
				if(isset($Qty[$poId][$countryId][$embtypeId])){
					$Qty[$poId][$countryId][$embtypeId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$embtypeId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderCountryEmbnameAndEmbtype){
				if(isset($Qty[$poId][$countryId][$embnameId][$embtypeId])){
					$Qty[$poId][$countryId][$embnameId][$embtypeId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$embnameId][$embtypeId]=$reqqnty;
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
			elseif($level==$this->_By_Emblishmentid){
				if(isset($Qty[$emblishmentId])){
					$Qty[$emblishmentId]+=$reqqnty;
				}
				else{
					$Qty[$emblishmentId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderGmtsitemGmtscolorAndEmblishmentid){
				if(isset($Qty[$poId][$itemNumberId][$colorId][$emblishmentId])){
					$Qty[$poId][$itemNumberId][$colorId][$emblishmentId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$itemNumberId][$colorId][$emblishmentId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderGmtsitemGmtscolorAndEmbName){
				if(isset($Qty[$poId][$itemNumberId][$colorId][$embnameId])){
					$Qty[$poId][$itemNumberId][$colorId][$embnameId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$itemNumberId][$colorId][$embnameId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderGmtsitemGmtssizeAndEmblishmentid){
				if(isset($Qty[$poId][$itemNumberId][$sizeId][$emblishmentId])){
					$Qty[$poId][$itemNumberId][$sizeId][$emblishmentId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$itemNumberId][$sizeId][$emblishmentId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderCountryGmtsitemGmtscolorAndEmblishmentid){
				if(isset($Qty[$poId][$countryId][$itemNumberId][$colorId][$emblishmentId])){
					$Qty[$poId][$countryId][$itemNumberId][$colorId][$emblishmentId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$countryId][$itemNumberId][$colorId][$emblishmentId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderEmblishmentidAndGmtscolor){
				if(isset($Qty[$poId][$emblishmentId][$colorId])){
					$Qty[$poId][$emblishmentId][$colorId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$emblishmentId][$colorId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderEmblishmentidGmtscolorAndGmtsitem){
				if(isset($Qty[$poId][$emblishmentId][$colorId][$itemNumberId])){
					$Qty[$poId][$emblishmentId][$colorId][$itemNumberId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$emblishmentId][$colorId][$itemNumberId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderEmblishmentidAndGmtssize){
				if(isset($Qty[$poId][$emblishmentId][$sizeId])){
					$Qty[$poId][$emblishmentId][$sizeId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$emblishmentId][$sizeId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderEmblishmentidGmtssizeAndGmtsitem){
				if(isset($Qty[$poId][$emblishmentId][$sizeId][$itemNumberId])){
					$Qty[$poId][$emblishmentId][$sizeId][$itemNumberId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$emblishmentId][$sizeId][$itemNumberId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderEmblishmentidGmtscolorAndGmtssize){
				if(isset($Qty[$poId][$emblishmentId][$colorId][$sizeId])){
					$Qty[$poId][$emblishmentId][$colorId][$sizeId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$emblishmentId][$colorId][$sizeId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderEmblishmentidGmtssizeAndArticle){
				if(isset($Qty[$poId][$emblishmentId][$sizeId][$articleNumber])){
					$Qty[$poId][$emblishmentId][$sizeId][$articleNumber]+=$reqqnty;
				}
				else{
					$Qty[$poId][$emblishmentId][$sizeId][$articleNumber]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderEmblishmentidGmtscolorGmtssizeAndArticle){
				if(isset($Qty[$poId][$emblishmentId][$colorId][$sizeId][$articleNumber])){
					$Qty[$poId][$emblishmentId][$colorId][$sizeId][$articleNumber]+=$reqqnty;
				}
				else{
					$Qty[$poId][$emblishmentId][$colorId][$sizeId][$articleNumber]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderEmblishmentidGmtscolorGmtssizeAndGmtsitem){
				if(isset($Qty[$poId][$emblishmentId][$colorId][$sizeId][$itemNumberId])){
					$Qty[$poId][$emblishmentId][$colorId][$sizeId][$itemNumberId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$emblishmentId][$colorId][$sizeId][$itemNumberId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderEmblishmentidGmtssizeArticleAndGmtsitem){
				if(isset($Qty[$poId][$emblishmentId][$sizeId][$articleNumber][$itemNumberId])){
					$Qty[$poId][$emblishmentId][$sizeId][$articleNumber][$itemNumberId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$emblishmentId][$sizeId][$articleNumber][$itemNumberId]=$reqqnty;
				}
			}
			elseif($level==$this->_By_OrderEmblishmentidGmtscolorGmtssizeArticleAndGmtsitem){
				if(isset($Qty[$poId][$emblishmentId][$colorId][$sizeId][$articleNumber][$itemNumberId])){
					$Qty[$poId][$emblishmentId][$colorId][$sizeId][$articleNumber][$itemNumberId]+=$reqqnty;
				}
				else{
					$Qty[$poId][$emblishmentId][$colorId][$sizeId][$articleNumber][$itemNumberId]=$reqqnty;
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
		$sizeId='';
		$articleNumber='';
		$planPutQnty='';
		$orderQuantity='';
		$total_set_qnty='';

		$emblishmentId='';
		$embnameId='';
		$embtypeId='';
		$req_qnty='';
		$avg_req_qnty='';
		$charge_unit='';
		$budget_on=0;$sourcing_rate='';
		$country_id_emb='';
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
			$emblishmentId=$row[csf('emblishment_id')];
			$embnameId=$row[csf('emb_name')];
			$embtypeId=$row[csf('emb_type')];
			$req_qnty=$row[csf('cons_dzn_gmts')];
			$charge_unit=$row[csf('rate')];*/

			$jobNo=$row['job_no'];
			$poId=$row['id'];
			$itemNumberId=$row['item_number_id'];
			$countryId=$row['country_id'];
			$colorId=$row['color_number_id'];
			$sizeId=$row['size_number_id'];
			$articleNumber=$row['article_number'];
			$planPutQnty=$row['plan_cut_qnty'];
			$orderQuantity=$row['order_quantity'];
			$total_set_qnty=$row['total_set_qnty'];
			$emblishmentId=$row['emblishment_id'];
			$embnameId=$row['emb_name'];
			$embtypeId=$row['emb_type'];
			$req_qnty=$row['cons_dzn_gmts'];
			$charge_unit=$row['rate'];
			$sourcing_rate=$row['sourcing_rate'];
			$budget_on=$row['budget_on'];
			$country_id_emb=$row['country_id_emb'];

			//$costingPerQty=$this->_costingPer($costingPerArray[$jobNo]);
			//$set_item_ratio=$gmtsitemRatioArray[$jobNo][$itemNumberId];
			//$costingPerQty=$this->_costingPer($this->_costingPerArr[$jobNo]);
			$costingPerQty=$this->_costingPerQtyArr[$jobNo];
			$set_item_ratio=$this->_gmtsitemRatioArray[$jobNo][$itemNumberId];

			//$reqqnty=$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$req_qnty);
			$reqqnty=0;
			if($budget_on==1){
				if($country_id_emb==0 || $country_id_emb==''){
					$reqqnty=$this->_calculateQty($orderQuantity,$costingPerQty,$set_item_ratio,$req_qnty);
				}
				else{
					$country_id_array=explode(",",$country_id_emb);
					if (in_array($countryId, $country_id_array)) {
						$reqqnty=$this->_calculateQty($orderQuantity,$costingPerQty,$set_item_ratio,$req_qnty);
					}
				}
			}else if($budget_on==2){
				if($country_id_emb==0 || $country_id_emb==''){
					$reqqnty=$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$req_qnty);
				}
				else{
					$country_id_array=explode(",",$country_id_emb);
					if (in_array($countryId, $country_id_array)) {
						$reqqnty=$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$req_qnty);
					}
				}
			}else{
				if($country_id_emb==0 || $country_id_emb==''){
					$reqqnty=$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$req_qnty);
				}
				else{
					$country_id_array=explode(",",$country_id_emb);
					if (in_array($countryId, $country_id_array)) {
						$reqqnty=$this->_calculateQty($planPutQnty,$costingPerQty,$set_item_ratio,$req_qnty);
					}
				}
			}

			$amount=$this->_calculateAmount($reqqnty,$charge_unit);
			$sourcing_amount=$this->_calculateAmount($reqqnty,$sourcing_rate);

			if($level==$this->_By_Job){
				if(isset($Amount[$jobNo])){
					$Amount[$jobNo]+=$amount;
				}
				else{
					$Amount[$jobNo]=$amount;
				}
			}
			elseif($level==$this->_By_JobAndEmblishmentid){
				if(isset($Amount[$jobNo][$emblishmentId])){
					$Amount[$jobNo][$emblishmentId]+=$amount;
				}
				else{
					$Amount[$jobNo][$emblishmentId]=$amount;
				}
			}
			elseif($level==$this->_By_JobAndEmbname){
				if(isset($Amount[$jobNo][$embnameId])){
					$Amount[$jobNo][$embnameId]+=$amount;
				}
				else{
					$Amount[$jobNo][$embnameId]=$amount;
				}
			}
			elseif($level==$this->_By_JobAndEmbtype){
				if(isset($Amount[$jobNo][$embtypeId])){
					$Amount[$jobNo][$embtypeId]+=$amount;
				}
				else{
					$Amount[$jobNo][$embtypeId]=$amount;
				}
			}
			elseif($level==$this->_By_JobEmbnameAndEmbtype){
				if(isset($Amount[$jobNo][$embnameId][$embtypeId])){
					$Amount[$jobNo][$embnameId][$embtypeId]+=$amount;
				}
				else{
					$Amount[$jobNo][$embnameId][$embtypeId]=$amount;
				}
			}
			elseif($level==$this->_By_JobEmbnameAndEmbtypeColor){
				if(isset($Amount[$jobNo][$embnameId][$embtypeId][$colorId])){
					$Amount[$jobNo][$embnameId][$embtypeId][$colorId]+=$amount;
				}
				else{
					$Amount[$jobNo][$embnameId][$embtypeId][$colorId]=$amount;
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
			elseif($level==$this->_By_OrderAndEmblishmentid){
				if(isset($Amount[$poId][$emblishmentId])){
					$Amount[$poId][$emblishmentId]+=$amount;
				}
				else{
					$Amount[$poId][$emblishmentId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderEmblishmentidAndGmtsitem){
				if(isset($Amount[$poId][$emblishmentId][$itemNumberId])){
					$Amount[$poId][$emblishmentId][$itemNumberId]+=$amount;
				}
				else{
					$Amount[$poId][$emblishmentId][$itemNumberId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderEmblishmentidAndGmtsitemSourcing){
				if(isset($Amount[$poId][$emblishmentId][$itemNumberId])){
					$Amount[$poId][$emblishmentId][$itemNumberId]+=$sourcing_amount;
				}
				else{
					$Amount[$poId][$emblishmentId][$itemNumberId]=$sourcing_amount;
				}
			}
			elseif($level==$this->_By_OrderAndEmbname){
				if(isset($Amount[$poId][$embnameId])){
					$Amount[$poId][$embnameId]+=$amount;
				}
				else{
					$Amount[$poId][$embnameId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderAndEmbtype){
				if(isset($Amount[$poId][$embtypeId])){
					$Amount[$poId][$embtypeId]+=$amount;
				}
				else{
					$Amount[$poId][$embtypeId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderEmbnameAndEmbtype){
				if(isset($Amount[$poId][$embnameId][$embtypeId])){
					$Amount[$poId][$embnameId][$embtypeId]+=$amount;
				}
				else{
					$Amount[$poId][$embnameId][$embtypeId]=$amount;
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
			elseif($level==$this->_By_OrderCountryAndEmblishmentid){
				if(isset($Amount[$poId][$countryId][$emblishmentId])){
					$Amount[$poId][$countryId][$emblishmentId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$emblishmentId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderCountryAndEmbname){
				if(isset($Amount[$poId][$countryId][$embnameId])){
					$Amount[$poId][$countryId][$embnameId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$embnameId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderCountryAndEmbtype){
				if(isset($Amount[$poId][$countryId][$embtypeId])){
					$Amount[$poId][$countryId][$embtypeId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$embtypeId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderCountryEmbnameAndEmbtype){
				if(isset($Amount[$poId][$countryId][$embnameId][$embtypeId])){
					$Amount[$poId][$countryId][$embnameId][$embtypeId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$embnameId][$embtypeId]=$amount;
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
			elseif($level==$this->_By_Emblishmentid){
				if(isset($Amount[$emblishmentId])){
					$Amount[$emblishmentId]+=$amount;
				}
				else{
					$Amount[$emblishmentId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderGmtsitemGmtscolorAndEmblishmentid){
				if(isset($Amount[$poId][$itemNumberId][$colorId][$emblishmentId])){
					$Amount[$poId][$itemNumberId][$colorId][$emblishmentId]+=$amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$colorId][$emblishmentId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderGmtsitemGmtscolorAndEmbName){
				if(isset($Amount[$poId][$itemNumberId][$colorId][$embnameId])){
					$Amount[$poId][$itemNumberId][$colorId][$embnameId]+=$amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$colorId][$embnameId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderGmtsitemGmtssizeAndEmblishmentid){
				if(isset($Amount[$poId][$itemNumberId][$sizeId][$emblishmentId])){
					$Amount[$poId][$itemNumberId][$sizeId][$emblishmentId]+=$amount;
				}
				else{
					$Amount[$poId][$itemNumberId][$sizeId][$emblishmentId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderCountryGmtsitemGmtscolorAndEmblishmentid){
				if(isset($Amount[$poId][$countryId][$itemNumberId][$colorId][$emblishmentId])){
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$emblishmentId]+=$amount;
				}
				else{
					$Amount[$poId][$countryId][$itemNumberId][$colorId][$emblishmentId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderEmblishmentidAndGmtscolor){
				if(isset($Amount[$poId][$emblishmentId][$colorId])){
					$Amount[$poId][$emblishmentId][$colorId]+=$amount;
				}
				else{
					$Amount[$poId][$emblishmentId][$colorId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderEmblishmentidGmtscolorAndGmtsitem){
				if(isset($Amount[$poId][$emblishmentId][$colorId][$itemNumberId])){
					$Amount[$poId][$emblishmentId][$colorId][$itemNumberId]+=$amount;
				}
				else{
					$Amount[$poId][$emblishmentId][$colorId][$itemNumberId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderEmblishmentidAndGmtssize){
				if(isset($Amount[$poId][$emblishmentId][$sizeId])){
					$Amount[$poId][$emblishmentId][$sizeId]+=$amount;
				}
				else{
					$Amount[$poId][$emblishmentId][$sizeId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderEmblishmentidGmtssizeAndGmtsitem){
				if(isset($Amount[$poId][$emblishmentId][$sizeId][$itemNumberId])){
					$Amount[$poId][$emblishmentId][$sizeId][$itemNumberId]+=$amount;
				}
				else{
					$Amount[$poId][$emblishmentId][$sizeId][$itemNumberId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderEmblishmentidGmtscolorAndGmtssize){
				if(isset($Amount[$poId][$emblishmentId][$colorId][$sizeId])){
					$Amount[$poId][$emblishmentId][$colorId][$sizeId]+=$amount;
				}
				else{
					$Amount[$poId][$emblishmentId][$colorId][$sizeId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderEmblishmentidGmtssizeAndArticle){
				if(isset($Amount[$poId][$emblishmentId][$sizeId][$articleNumber])){
					$Amount[$poId][$emblishmentId][$sizeId][$articleNumber]+=$amount;
				}
				else{
					$Amount[$poId][$emblishmentId][$sizeId][$articleNumber]=$amount;
				}
			}
			elseif($level==$this->_By_OrderEmblishmentidGmtscolorGmtssizeAndArticle){
				if(isset($Amount[$poId][$emblishmentId][$colorId][$sizeId][$articleNumber])){
					$Amount[$poId][$emblishmentId][$colorId][$sizeId][$articleNumber]+=$amount;
				}
				else{
					$Amount[$poId][$emblishmentId][$colorId][$sizeId][$articleNumber]=$amount;
				}
			}

			elseif($level==$this->_By_OrderEmblishmentidGmtscolorGmtssizeAndGmtsitem){
				if(isset($Amount[$poId][$emblishmentId][$colorId][$sizeId][$itemNumberId])){
					$Amount[$poId][$emblishmentId][$colorId][$sizeId][$itemNumberId]+=$amount;
				}
				else{
					$Amount[$poId][$emblishmentId][$colorId][$sizeId][$itemNumberId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderEmblishmentidGmtssizeArticleAndGmtsitem){
				if(isset($Amount[$poId][$emblishmentId][$sizeId][$articleNumber][$itemNumberId])){
					$Amount[$poId][$emblishmentId][$sizeId][$articleNumber][$itemNumberId]+=$amount;
				}
				else{
					$Amount[$poId][$emblishmentId][$sizeId][$articleNumber][$itemNumberId]=$amount;
				}
			}
			elseif($level==$this->_By_OrderEmblishmentidGmtscolorGmtssizeArticleAndGmtsitem){
				if(isset($Amount[$poId][$emblishmentId][$colorId][$sizeId][$articleNumber][$itemNumberId])){
					$Amount[$poId][$emblishmentId][$colorId][$sizeId][$articleNumber][$itemNumberId]+=$amount;
				}
				else{
					$Amount[$poId][$emblishmentId][$colorId][$sizeId][$articleNumber][$itemNumberId]=$amount;
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
	public function getQty_by_jobAndEmblishmentid($jobNo,$emblishmentId){
		$Qty=$this->_setQty($this->_By_JobAndEmblishmentid);
		return $Qty[$jobNo][$emblishmentId];
	}

	public function getQtyArray_by_jobAndEmblishmentid(){
		$Qty=$this->_setQty($this->_By_JobAndEmblishmentid);
		return $Qty;

	}
	//Amount
	public function getAmount_by_jobAndEmblishmentid($jobNo,$emblishmentId){
		$Amount=$this->_setAmount($this->_By_JobAndEmblishmentid);
		return $Amount[$jobNo][$emblishmentId];
	}

	public function getAmountArray_by_jobAndEmblishmentid(){
		$Amount=$this->_setAmount($this->_By_JobAndEmblishmentid);
		return $Amount;
	}


	//Job and Fabric wise
	//Qty
	public function getQty_by_jobAndEmbname($jobNo,$embname){
		$Qty=$this->_setQty($this->_By_JobAndEmbname);
		return $Qty[$jobNo][$embname];
	}

	public function getQtyArray_by_jobAndEmbname(){
		$Qty=$this->_setQty($this->_By_JobAndEmbname);
		return $Qty;

	}
	//Amount
	public function getAmount_by_jobAndEmbname($jobNo,$embname){
		$Amount=$this->_setAmount($this->_By_JobAndEmbname);
		return $Amount[$jobNo][$embname];
	}

	public function getAmountArray_by_jobAndEmbname(){
		$Amount=$this->_setAmount($this->_By_JobAndEmbname);
		return $Amount;
	}

	//Job and Process wise
	//Qty
	public function getQty_by_jobAndEmbtype($jobNo,$embtype){
		$Qty=$this->_setQty($this->_By_JobAndEmbtype);
		return $Qty[$jobNo][$embtype];
	}

	public function getQtyArray_by_jobAndEmbtype(){
		$Qty=$this->_setQty($this->_By_JobAndEmbtype);
		return $Qty;

	}
	//Amount
	public function getAmount_by_jobAndEmbtype($jobNo,$embtype){
		$Amount=$this->_setAmount($this->_By_JobAndEmbtype);
		return $Amount[$jobNo][$embtype];
	}

	public function getAmountArray_by_jobAndEmbtype(){
		$Amount=$this->_setAmount($this->_By_JobAndEmbtype);
		return $Amount;
	}

	//Job, Fabric and Process wise
	//Qty
	public function getQty_by_jobEmbnameAndEmbtype($jobNo,$embname,$embtype){
		$Qty=$this->_setQty($this->_By_JobEmbnameAndEmbtype);
		return $Qty[$jobNo][$embname][$embtype];
	}

	public function getQtyArray_by_jobEmbnameAndEmbtype(){
		$Qty=$this->_setQty($this->_By_JobEmbnameAndEmbtype);
		return $Qty;

	}
	
	//Job, Fabric and Process wise Color
	//Qty
	public function getQty_by_jobEmbnameAndEmbtypeColor($jobNo,$embname,$embtype,$colorId){
		$Qty=$this->_setQty($this->_By_JobEmbnameAndEmbtypeColor);
		return $Qty[$jobNo][$embname][$embtype][$colorId];
	}

	public function getQtyArray_by_jobEmbnameAndEmbtypeColor(){
		$Qty=$this->_setQty($this->_By_JobEmbnameAndEmbtypeColor);
		return $Qty;

	}
	//Amount
	public function getAmount_by_jobEmbnameAndEmbtype($jobNo,$embname,$embtype){
		$Amount=$this->_setAmount($this->_By_JobEmbnameAndEmbtype);
		return $Amount[$jobNo][$embname][$embtype];
	}

	public function getAmountArray_by_jobEmbnameAndEmbtype(){
		$Amount=$this->_setAmount($this->_By_JobEmbnameAndEmbtype);
		return $Amount;
	}

//Amount Color
	public function getAmount_by_jobEmbnameAndEmbtypeColor($jobNo,$embname,$embtype,$colorId){
		$Amount=$this->_setAmount($this->_By_JobEmbnameAndEmbtypeColor);
		return $Amount[$jobNo][$embname][$embtype][$colorId];
	}

	public function getAmountArray_by_jobEmbnameAndEmbtypeColor(){
		$Amount=$this->_setAmount($this->_By_JobEmbnameAndEmbtypeColor);
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
	public function getAmount($poId){
		$Amount=$this->_setAmount($this->_By_Order);
		return $Amount[$poId];
	}

	public function getAmountArray_by_order(){
		$Amount=$this->_setAmount($this->_By_Order);
		return $Amount;
	}


	//Order and Conversion Id wise
	//Qty
	public function getQty_by_orderAndEmblishmentid($poId,$emblishmentId){
		$Qty=$this->_setQty($this->_By_OrderAndEmblishmentid);
		return $Qty[$poId][$emblishmentId];
	}

	public function getQtyArray_by_orderAndEmblishmentid(){
		$Qty=$this->_setQty($this->_By_OrderAndEmblishmentid);
		return $Qty;

	}

	public function getQtyArray_by_orderEmblishmentidAndGmtsitem(){
		$Qty=$this->_setQty($this->_By_OrderEmblishmentidAndGmtsitem);
		return $Qty;

	}

	//Amount
	public function getAmount_by_orderAndEmblishmentid($poId,$emblishmentId){
		$Amount=$this->_setAmount($this->_By_OrderAndEmblishmentid);
		return $Amount[$poId][$emblishmentId];
	}

	public function getAmountArray_by_orderAndEmblishmentid(){
		$Amount=$this->_setAmount($this->_By_OrderAndEmblishmentid);
		return $Amount;
	}
	public function getAmountArray_by_orderEmblishmentidAndGmtsitem(){
		$Amount=$this->_setAmount($this->_By_OrderEmblishmentidAndGmtsitem);
		return $Amount;
	}
	public function getAmountArray_by_orderEmblishmentidAndGmtsitemSourcing(){
		$Amount=$this->_setAmount($this->_By_OrderEmblishmentidAndGmtsitemSourcing);
		return $Amount;
	}


	//Order and Fabric wise
	//Qty
	public function getQty_by_orderAndEmbname($poId,$embname){
		$Qty=$this->_setQty($this->_By_OrderAndEmbname);
		return $Qty[$poId][$embname];
	}

	public function getQtyArray_by_orderAndEmbname(){
		$Qty=$this->_setQty($this->_By_OrderAndEmbname);
		return $Qty;

	}
	//Amount
	public function getAmount_by_orderAndEmbname($poId,$embname){
		$Amount=$this->_setAmount($this->_By_OrderAndEmbname);
		return $Amount[$poId][$embname];
	}

	public function getAmountArray_by_orderAndEmbname(){
		$Amount=$this->_setAmount($this->_By_OrderAndEmbname);
		return $Amount;
	}

	//Order and Process wise
	//Qty
	public function getQty_by_orderAndEmbtype($poId,$embtype){
		$Qty=$this->_setQty($this->_By_OrderAndEmbtype);
		return $Qty[$poId][$embtype];
	}

	public function getQtyArray_by_orderAndEmbtype(){
		$Qty=$this->_setQty($this->_By_OrderAndEmbtype);
		return $Qty;

	}
	//Amount
	public function getAmount_by_orderAndEmbtype($poId,$embtype){
		$Amount=$this->_setAmount($this->_By_OrderAndEmbtype);
		return $Amount[$poId][$embtype];
	}

	public function getAmountArray_by_orderAndEmbtype(){
		$Amount=$this->_setAmount($this->_By_OrderAndEmbtype);
		return $Amount;
	}

	//Order Fabric and Process wise
	//Qty
	public function getQty_by_orderEmbnameAndEmbtype($poId,$embname,$embtype){
		$Qty=$this->_setQty($this->_By_OrderEmbnameAndEmbtype);
		return $Qty[$poId][$embname][$embtype];
	}

	public function getQtyArray_by_orderEmbnameAndEmbtype(){
		$Qty=$this->_setQty($this->_By_OrderEmbnameAndEmbtype);
		return $Qty;

	}
	//Amount
	public function getAmount_by_orderEmbnameAndEmbtype($poId,$embname,$embtype){
		$Amount=$this->_setAmount($this->_By_OrderEmbnameAndEmbtype);
		return $Amount[$poId][$embname][$embtype];
	}

	public function getAmountArray_by_orderEmbnameAndEmbtype(){
		$Amount=$this->_setAmount($this->_By_OrderEmbnameAndEmbtype);
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
	public function getQty_by_orderCountryAndEmblishmentid($poId,$countryId,$emblishmentId){
		$Qty=$this->_setQty($this->_By_OrderCountryAndEmblishmentid);
		return $Qty[$poId][$countryId][$emblishmentId];
	}

	public function getQtyArray_by_orderCountryAndEmblishmentid(){
		$Qty=$this->_setQty($this->_By_OrderCountryAndEmblishmentid);
		return $Qty;

	}
	//Amount
	public function getAmount_by_orderCountryAndEmblishmentid($poId,$countryId,$emblishmentId){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndEmblishmentid);
		return $Amount[$poId][$countryId][$emblishmentId];
	}

	public function getAmountArray_by_orderCountryAndEmblishmentid(){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndEmblishmentid);
		return $Amount;
	}


	//Order,Country and Fabric wise
	//Qty
	public function getQty_by_orderCountryAndEmbname($poId,$countryId,$embname){
		$Qty=$this->_setQty($this->_By_OrderCountryAndEmbname);
		return $Qty[$poId][$countryId][$embname];
	}

	public function getQtyArray_by_orderCountryAndEmbname(){
		$Qty=$this->_setQty($this->_By_OrderCountryAndEmbname);
		return $Qty;

	}
	//Amount
	public function getAmount_by_orderCountryAndEmbname($poId,$countryId,$embname){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndEmbname);
		return $Amount[$poId][$countryId][$embname];
	}

	public function getAmountArray_by_orderCountryAndEmbname(){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndEmbname);
		return $Amount;
	}

	//Order,Country and Process wise
	//Qty
	public function getQty_by_orderCountryAndEmbtype($poId,$countryId,$embtype){
		$Qty=$this->_setQty($this->_By_OrderCountryAndEmbtype);
		return $Qty[$poId][$countryId][$embtype];
	}

	public function getQtyArray_by_orderCountryAndEmbtype(){
		$Qty=$this->_setQty($this->_By_OrderCountryAndEmbtype);
		return $Qty;

	}
	//Amount
	public function getAmount_by_orderCountryAndEmbtype($poId,$countryId,$embtype){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndEmbtype);
		return $Amount[$poId][$countryId][$embtype];
	}

	public function getAmountArray_by_orderCountryAndEmbtype(){
		$Amount=$this->_setAmount($this->_By_OrderCountryAndEmbtype);
		return $Amount;
	}

	//Order,Country Fabric and Process wise
	//Qty
	public function getQty_by_orderCountryEmbnameAndEmbtype($poId,$countryId,$embname,$embtype){
		$Qty=$this->_setQty($this->_By_OrderCountryEmbnameAndEmbtype);
		return $Qty[$poId][$countryId][$embname][$embtype];
	}

	public function getQtyArray_by_orderCountryEmbnameAndEmbtype(){
		$Qty=$this->_setQty($this->_By_OrderCountryEmbnameAndEmbtype);
		return $Qty;

	}
	//Amount
	public function getAmount_by_orderCountryEmbnameAndEmbtype($poId,$countryId,$embname,$embtype){
		$Amount=$this->_setAmount($this->_By_OrderCountryEmbnameAndEmbtype);
		return $Amount[$poId][$countryId][$embname][$embtype];
	}

	public function getAmountArray_by_orderCountryEmbnameAndEmbtype(){
		$Amount=$this->_setAmount($this->_By_OrderCountryEmbnameAndEmbtype);
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

	// Conversion Id wise
	//Qty
	public function getQty_by_emblishmentid($emblishmentId){
		$Qty=$this->_setQty($this->_By_Emblishmentid);
		return $Qty[$emblishmentId];
	}

	public function getQtyArray_by_emblishmentid(){
		$Qty=$this->_setQty($this->_By_Emblishmentid);
		return $Qty;

	}
	//Amount
	public function getAmount_by_emblishmentid($emblishmentId){
		$Amount=$this->_setAmount($this->_By_Emblishmentid);
		return $Amount[$emblishmentId];
	}

	public function getAmountArray_by_emblishmentid(){
		$Amount=$this->_setAmount($this->_By_Emblishmentid);
		return $Amount;
	}
	// Order,Gmts Item, Gmts Color and Emblishmentid wise
	//Qty
	public function getQtyArray_by_OrderGmtsitemGmtscolorAndEmblishmentid(){
		$Qty=$this->_setQty($this->_By_OrderGmtsitemGmtscolorAndEmblishmentid);
		return $Qty;
	}
	// Order,Gmts Item, Gmts Color and EmblishmentName wise
	public function getQtyArray_by_OrderGmtsitemGmtscolorAndEmbName(){
		$Qty=$this->_setQty($this->_By_OrderGmtsitemGmtscolorAndEmbName);
		return $Qty;
	}

	public function getQtyArray_by_OrderGmtsitemGmtssizeAndEmblishmentid(){
		$Qty=$this->_setQty($this->_By_OrderGmtsitemGmtssizeAndEmblishmentid);
		return $Qty;
	}

	public function getQtyArray_by_OrderCountryGmtsitemGmtscolorAndEmblishmentid(){
		$Qty=$this->_setQty($this->_By_OrderCountryGmtsitemGmtscolorAndEmblishmentid);
		return $Qty;
	}
	//Amount

	public function getAmountArray_by_OrderGmtsitemGmtscolorAndEmblishmentid(){
		$Amount=$this->_setAmount($this->_By_OrderGmtsitemGmtscolorAndEmblishmentid);
		return $Amount;
	}
	public function getAmountArray_by_OrderGmtsitemGmtscolorAndEmbName(){
		$Amount=$this->_setAmount($this->_By_OrderGmtsitemGmtscolorAndEmbName);
		return $Amount;
	}
	public function getAmountArray_by_OrderGmtsitemGmtssizeAndEmblishmentid(){
		$Amount=$this->_setAmount($this->_By_OrderGmtsitemGmtssizeAndEmblishmentid);
		return $Amount;
	}
	public function getAmountArray_by_OrderCountryGmtsitemGmtscolorAndEmblishmentid(){
		$Amount=$this->_setAmount($this->_By_OrderCountryGmtsitemGmtscolorAndEmblishmentid);
		return $Amount;
	}

	public function getQtyArray_by_OrderEmblishmentidAndGmtscolor(){
		$Qty=$this->_setQty($this->_By_OrderEmblishmentidAndGmtscolor);
		return $Qty;
	}
	public function getQtyArray_by_OrderEmblishmentidGmtscolorAndGmtsitem(){
		$Qty=$this->_setQty($this->_By_OrderEmblishmentidGmtscolorAndGmtsitem);
		return $Qty;
	}

	public function getAmountArray_by_OrderEmblishmentidAndGmtscolor(){
		$Amount=$this->_setAmount($this->_By_OrderEmblishmentidAndGmtscolor);
		return $Amount;
	}
	public function getAmountArray_by_OrderEmblishmentidGmtscolorAndGmtsitem(){
		$Amount=$this->_setAmount($this->_By_OrderEmblishmentidGmtscolorAndGmtsitem);
		return $Amount;
	}

	public function getQtyArray_by_OrderEmblishmentidAndGmtssize(){
		$Qty=$this->_setQty($this->_By_OrderEmblishmentidAndGmtssize);
		return $Qty;
	}
	public function getQtyArray_by_OrderEmblishmentidGmtssizeAndGmtsitem(){
		$Qty=$this->_setQty($this->_By_OrderEmblishmentidGmtssizeAndGmtsitem);
		return $Qty;
	}

	public function getAmountArray_by_OrderEmblishmentidAndGmtssize(){
		$Amount=$this->_setAmount($this->_By_OrderEmblishmentidAndGmtssize);
		return $Amount;
	}
	public function getAmountArray_by_OrderEmblishmentidGmtssizeAndGmtsitem(){
		$Amount=$this->_setAmount($this->_By_OrderEmblishmentidGmtssizeAndGmtsitem);
		return $Amount;
	}
	public function getQtyArray_by_OrderEmblishmentidAndGmtscolorAndGmtssize(){
		$Qty=$this->_setQty($this->_By_OrderEmblishmentidGmtscolorAndGmtssize);
		return $Qty;
	}


	public function getAmountArray_by_OrderEmblishmentidGmtscolorAndGmtssize(){
		$Amount=$this->_setAmount($this->_By_OrderEmblishmentidGmtscolorAndGmtssize);
		return $Amount;
	}


	public function getQtyArray_by_OrderEmblishmentidGmtssizeAndArticle(){
		$Qty=$this->_setQty($this->_By_OrderEmblishmentidGmtssizeAndArticle);
		return $Qty;
	}


	public function getAmountArray_by_OrderEmblishmentidGmtssizeAndArticle(){
		$Amount=$this->_setAmount($this->_By_OrderEmblishmentidGmtssizeAndArticle);
		return $Amount;
	}



	public function getQtyArray_by_OrderEmblishmentidAndGmtscolorGmtssizeAndArticle(){
		$Qty=$this->_setQty($this->_By_OrderEmblishmentidGmtscolorGmtssizeAndArticle);
		return $Qty;
	}

	public function getAmountArray_by_OrderEmblishmentidGmtscolorGmtssizeAndArticle(){
		$Amount=$this->_setAmount($this->_By_OrderEmblishmentidGmtscolorGmtssizeAndArticle);
		return $Amount;
	}

	public function getQtyArray_by_OrderEmblishmentidAndGmtscolorGmtssizeAndGmtsitem(){
		$Qty=$this->_setQty($this->_By_OrderEmblishmentidGmtscolorGmtssizeAndGmtsitem);
		return $Qty;
	}
	public function getAmountArray_by_OrderEmblishmentidGmtscolorGmtssizeAndGmtsitem(){
		$Amount=$this->_setAmount($this->_By_OrderEmblishmentidGmtscolorGmtssizeAndGmtsitem);
		return $Amount;
	}

	public function getQtyArray_by_OrderEmblishmentidGmtssizeArticleAndGmtsitem(){
		$Qty=$this->_setQty($this->_By_OrderEmblishmentidGmtssizeArticleAndGmtsitem);
		return $Qty;
	}
	public function getAmountArray_by_OrderEmblishmentidGmtssizeArticleAndGmtsitem(){
		$Amount=$this->_setAmount($this->_By_OrderEmblishmentidGmtssizeArticleAndGmtsitem);
		return $Amount;
	}

	public function getQtyArray_by_OrderEmblishmentidAndGmtscolorGmtssizeArticleAndGmtsitem(){
		$Qty=$this->_setQty($this->_By_OrderEmblishmentidGmtscolorGmtssizeArticleAndGmtsitem);
		return $Qty;
	}
	public function getAmountArray_by_OrderEmblishmentidGmtscolorGmtssizeArticleAndGmtsitem(){
		$Amount=$this->_setAmount($this->_By_OrderEmblishmentidGmtscolorGmtssizeArticleAndGmtsitem);
		return $Amount;
	}


	function __destruct() {
		parent::__destruct();
		unset($this->_dataArray);
	}
}
?>