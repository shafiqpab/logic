<?
class report{
	protected $_By_Job='By_Job';
	protected $_By_Order='By_Order';
	protected $_By_OrderAndCountry='By_OrderAndCountry';
	protected $_By_OrderAndGmtsitem='By_OrderAndGmtsitem';
	protected $_By_OrderAndGmtscolor='By_OrderAndGmtscolor';
	protected $_By_OrderAndGmtssize='By_OrderAndGmtssize';
	protected $_By_orderCountryAndGmtsitem='By_orderCountryAndGmtsitem';
	protected $_By_OrderCountryAndGmtscolor='By_OrderCountryAndGmtscolor';
	protected $_By_OrderCountryAndGmtssize='By_OrderCountryAndGmtssize';
	protected $_By_OrderGmtsitemAndGmtscolor='By_OrderGmtsitemAndGmtscolor';
	protected $_By_OrderGmtsitemAndGmtssize='By_OrderGmtsitemAndGmtssize';
	protected $_By_OrderGmtscolorAndGmtssize='By_OrderGmtscolorAndGmtssize';
	protected $_By_orderCountryGmtsitemAndGmtscolor='By_orderCountryGmtsitemAndGmtscolor';
	protected $_By_orderCountryGmtsitemAndGmtssize='By_orderCountryGmtsitemAndGmtssize';
	protected $_By_orderCountryGmtscolorAndGmtssize='By_orderCountryGmtscolorAndGmtssize';
	protected $_By_orderGmtsitemGmtscolorAndGmtssize='By_orderGmtsitemGmtscolorAndGmtssize';
	protected $_By_orderCountryGmtsitemGmtscolorAndGmtssize='By_orderCountryGmtsitemGmtscolorAndGmtssize';
	protected $_gmtsitemRatioArray=array();
	protected $_costingPerQtyArr=array();
	protected $condition='';
	protected $cond='';
	protected $jobtablecond='';
	
	// class constructor
	function __construct(condition $condition){
		$this->condition=$condition;
		$this->cond=$this->condition->getCond();
		$this->jobtablecond=$this->condition->getJobTableCond();
		$this->_gmtsitemRatioArray=$this->condition->getGmtsitemRatioArr();
		$this->_costingPerQtyArr=$this->condition->getCostingPerArr();
		$this->_trimTypeArray=$this->condition->getTrimsTypeArr();
		}// end class constructor
		
	public function get(){
			return $this->jobtablecond;
			
		}
	function __destruct() {
		unset($this->_gmtsitemRatioArray);
		unset($this->_costingPerArr);
		unset($this->_trimTypeArray);
	}
}
?>