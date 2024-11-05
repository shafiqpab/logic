<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
$short_booking_causedes_arr=array();
//$short_booking_cause_arr = array(1=>"Merchandising",2=>"Technical",3=>"Yarn",4=>"Knitting",5=>"Dyeing",6=>"Dyeing Finishing",7=>"Textile Quality",8=>"Color Lab",9=>"Sample And RND Textile",10=>"Finish Fabric Store",11=>"AOP",12=>"Dyed Yarn",13=>"Placement Print",14=>"Embroidery",15=>"Garments Wash",16=>"Garments Unit");

$short_booking_causedes_arr[1]= array(1=>"Wrong calculation of consumption",2=>"Wrong Entry / typo in KD program",3=>"Wrong calculation of wastage %");

$short_booking_causedes_arr[2]= array(1=>"Fabric DIA Change after Booking & Bulk Production.",2=>"GSM Up & Down between Booking requirement & Bulk production or Buyer Change demand.",3=>"Styling change after Booking Marker.",4=>"M-List [garments measurement increase or decrease] change by Buyer after Booking Marker.",5=>"PO / Order Ratio Change after Booking Marker.",6=>"Fabric problem [Knitting hole, Bias, Running Shade, Uneven selvage, etc]",7=>"Fabric abnormal stability result for that Bulk Patten increase.",8=>" Fabric DIA wise not cutting during Bulk [DIA wise separate plan & cutting need].");
$short_booking_causedes_arr[3]= array(1=>"Delay & quality problem");
$short_booking_causedes_arr[4]= array(1=>"Knitting Hole",2=>"Dia plus /minus",3=>"GSM HIGH",4=>"Over all knitting any quality problem");
$short_booking_causedes_arr[5]= array(1=>"Shade not ok, running shade, uneven",2=>"Rope /crease mark. Recipe adjustment specially for only one batch",3=>"Over all dyeing any quality problem");
$short_booking_causedes_arr[6]= array(1=>"Dia plus/minus",2=>"Varities spot, shining mark from peach finish",3=>"Over all finishing any quality problem");
$short_booking_causedes_arr[7]= array(1=>"Wrong decission and decission pending");
$short_booking_causedes_arr[8]= array(1=>"Lab dip delay sub also matching delay",2=>"Also wrong standard delivery to floor");
$short_booking_causedes_arr[9]= array(1=>"Risk assestion not properly.",2=>"Process route is Not production friendly",3=>"Sample & RND at 10% least fab supplementary required");
$short_booking_causedes_arr[10]= array(1=>"Fab missing",2=>"Handling improper. Also dirty sopt");
$short_booking_causedes_arr[11]= array(1=>"Due to  side curling,  uneven dia & roll to roll joint.",2=>"Due to hole [GSM hole, Shade hole etc.] & dia & GSM variations.",3=>"Sometimes  color bleeding.",4=>"Due to Side to middle shading [Big dia for M/C-01].");
$short_booking_causedes_arr[12]= array(1=>"Fault/repeat missing from knitting.",2=>"Dyed yarn quality hard/weak , this is yarn dyeing fault.",3=>"Fly problem from knitting floor.",4=>"One repeat body or long size body if GSM cutting more than supplementary may come.",5=>"If GSM high responsible Kniiting.",6=>"If Dia Lees for all fabric or Dia more all fabric knitting responsible [If suplimentary come for Dia up-down not responsible for knitting].",7=>" Knitting loop/ Needle-sinker mark/knitting hole [if knitting hole found in knitting inspection stage] for knitting.");
$short_booking_causedes_arr[13]= array(1=>"Multi set of print shade and approval keeping and distribution.",2=>"Keeping a copy for each line to check quality for QI.",3=>"Display a copy to confirm design and style for each line and machine.",4=>"Display multi set print pcs at machine as a process lay out display.",5=>"PDisplay a copy of print beside curing machine, heat press machine and fusing machine.",6=>"Keeping before wash and after wash copy from every day production.",7=>"Keeping copy of approval print reference for after printing check.",8=>" Criticality of print.  Line increase due to higher demand on same style cause deviation on print from different lines");
$short_booking_causedes_arr[14]= array(1=>"Needle hole.",2=>"Machine problem[rotary]",3=>"Electricity probem.",4=>"Fabric spot.",5=>"Oil spot.");
$short_booking_causedes_arr[15]= array(1=>"Shade problem.",2=>"Color spot.",3=>"Overall quality problem",4=>"Wrong recipe dyeing",5=>"Machine / Power problem",6=>"Dyes spot / Gmts hole",7=>"Iron spot from dryer");
$short_booking_causedes_arr[16]= array(1=>"Cutting Mistake.",2=>"Fabric lost from warehouse or floor.",3=>"Excessive sewing rejection.",4=>"Excessive cut panel rejection.",5=>"Excessive complete garments rejection lack of panel check.",6=>"Missing complete garments qty.",7=>"Measurement discrepancy complete gmts out of tolerances.",8=>"Excessive cut panel received short from Print/EMB unit.",9=>"Consumption not meet for Dia up down.",10=>"Quality issues",11=>"Finish fabric GSM high.",12=>"Differance booking consumption VS Actual consumption.",13=>"Defective fabric received from Dyeing request.",14=>"Excessive Print part rejection found on complete garments.",15=>"Complete gmts rejection for knitting pin hole.",16=>"Excessive garments rejection from washing unit.",17=>"Fabric delivery short as our require qty.",18=>"Pattern increase for measurement ensure.");

$user_id=$_SESSION['logic_erp']['user_id'];
$id=return_next_id("id","booking_cause",1);
$field_arr="id, cause_id, cause, inserted_by, insert_date, status_active, is_deleted, entry_form"; 
$data_array=""; $add_commaa=0; $i=1;
foreach($short_booking_causedes_arr as $cid=>$did)
{
	//print_r($short_booking_causedes_arr[$cid]);
	foreach($did as $r=>$cause)
	{
		if ($add_commaa!=0) $data_array .=",";
		$data_array .="(".$id.",".$cid.",'".$cause."','".$user_id."','".$pc_date_time."',1,0,0)";
		$id++;
		$i++;
	}
}
//echo "10**INSERT INTO booking_cause (".$field_arr.") VALUES ".$data_array; die;
//$rID=sql_insert("booking_cause",$field_arr,$data_array,1);

if($rID==1)
{
	oci_commit($con);
	echo "Success".$i;
}
else
{
	oci_rollback($con);
	echo "F".$i;
}