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
$condition= new condition();
$dd=$condition->company_name($com);
$com='=1';
$buy='=1';
$job_no_prefix_num='=687';
$style_ref_no="like '%ss%'";
$job="= 'FAL-15-00687'";

$sql=sql_select('select job.*, po.*,po.id AS "po_id" from wo_po_details_master job, wo_po_break_down po where job.job_no=po.job_no_mst and job.company_name =1 and job.is_deleted=0 and job.status_active=1');

print_r($sql); 

echo "<br/>";
$et= microtime(true);
echo $st;
echo "<br/>";
echo $et-$st;
echo "<br/>";
echo memory_get_usage() . "<br/>"; 
echo (memory_get_usage()/1024)/1024;
?>
