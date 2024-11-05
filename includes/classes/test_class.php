<?

//echo ini_get('memory_limit'). "<br/>";
//echo memory_get_usage() . "<br/>"; // 36640
//ini_set('memory_limit','3072M')."<br/>";
$st= microtime(true);

require_once('../common.php');
require_once('connection.php');
require_once('class.conditions.php');
require_once('class.reports.php');
require_once('class.jobs.php');
require_once('class.pos.php');

$com='=1';
$buy='=1';
$job_no_prefix_num='=687';
$style_ref_no="like '%ss%'";
$job="= 'FAL-15-00687'";

$condition= new condition();
$dd=$condition->company_name($com);
//$jobObj= new job($condition);
//$jobs=$jobObj->getJobs();
//print_r($jobs);
$jobObj= new po($condition);
echo $jobObj->getQuery();
$jobs=$jobObj->getpos();
echo print_r($jobs);
 

echo "<br/>";
$et= microtime(true);
echo $st;
echo "<br/>";
echo $et-$st;
echo "<br/>";
echo memory_get_usage() . "<br/>"; 
echo (memory_get_usage()/1024)/1024;
?>
