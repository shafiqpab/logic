<?

//echo ini_get('memory_limit'). "<br/>";
//echo memory_get_usage() . "<br/>"; // 36640
//ini_set('memory_limit','3072M')."<br/>";
//$st= microtime(true);
require_once('../common.php');
require_once('class.conditions.php');
require_once('class.reports.php');
require_once('class.fabrics.php');
//require_once('class.yarns.php');
require_once('class.conversions.php');
//require_once('class.trims.php');
//require_once('class.emblishments.php');
//require_once('class.washes.php');
//require_once('class.commisions.php');
//require_once('class.commercials.php');
//require_once('class.others.php');
function array_flatten($array) { 
  if (!is_array($array)) { 
    return false; 
  } 
  $result = array(); 
  foreach ($array as $key => $value) { 
    if (is_array($value)) { 
      $result = array_merge($result, array_flatten($value)); 
    } else { 
      $result[$key] = $value; 
    } 
  } 
  return $result; 
}
$com='=1';
$buy='=1';
$job_no_prefix_num='=687';
$style_ref_no="like '%ss%'";
$job="= 'FAL-15-00687'";
//$st='2015-09-01';
//$et='2015-10-30';
//$spd="between '".$st."' and '".$et."'";
//->where(report:: $shipment_date,$spd)
$condition= new condition();
$dd=$condition->company_name($com)
		->job_no($job)
		->init();
		//echo $dd->getCond();
		//die;
$fabric= new fabric($condition);
$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
 //echo $fabric->get();
 //die;
print_r($fabric_costing_arr);
echo "<br/> vvv";
$dd=array_flatten($fabric_costing_arr);
print_r($dd);

//$yarn= new yarn($condition);
//echo $yarn->getQuery();
//$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
//print_r($yarn_costing_arr);


//$conversion= new conversion($condition);
//print_r  ($conversion->getRateArray());
 //$conversion->_setAmount();
//$conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
//print_r($conversion_costing_arr_process);

//$trims= new trims($condition);
//echo $trims->getQuery();
//$trims_costing_arr=$trims->getAmountArray_by_order();
//print_r($trims_costing_arr);

//$emblishment= new emblishment($condition);
//echo $emblishment->getQuery();
//$emblishment_costing_arr_name=$emblishment->getAmountArray_by_order();
//print_r($emblishment_costing_arr_name);

//$wash= new wash($condition);
//echo $wash->getQuery();
//$wash_costing_arr_name=$wash->getAmountArray_by_order();
//print_r($wash_costing_arr_name);


//$commission= new commision($condition);
//echo $commission->getQuery();
//$commission_costing_arr=$commission->getAmountArray_by_order();
//print_r($commission_costing_arr);


//$commercial= new commercial($condition);
//echo $commercial->getQuery();
//$commercial_costing_arr=$commercial->getAmountArray_by_order();
//print_r($commercial_costing_arr);

//$other= new other($condition);
//echo $other->getQuery();
//$other_costing_arr=$other->getAmountArray_by_order();
//print_r($other_costing_arr);

echo "<br/>";
$et= microtime(true);
echo $st;
echo "<br/>";
echo $et-$st;
echo "<br/>";
echo memory_get_usage() . "<br/>"; 
echo (memory_get_usage()/1024)/1024;
?>
