<?
require('ext_resource/mpdf60/mpdf.php');

$len = 10;

//$i = 1;
//foreach($matches as $value) 
    for($i=1;$i<$len;$i++)
    {
    if ($i < $len) {
        $html = "<div style='page-break-after:always'>Page break</div>";
    } else {
        $html = "<div style='page-break-after:avoid'>Page break</div>";
    }
    $i++;
}
//$mpdf->WriteHTML($html);
?>
<div style='page-break-after:avoid'>Page break</div>
<?
//$html = "test";

$html = ob_get_contents();
$mpdf = new mPDF('', 'A4-L', '', '', 10, 10, 10, 20, 3, 3);	
$mpdf->WriteHTML($html,2);
$output  = $mpdf->Output(time().'.pdf','S');//output as string
echo($output); //final result to JS as blob data

die;
$sew_fin_reject_type_for_arr = array(1=>"Fabric", 2=>"Sewing", 3=>"Measurement", 4=>"Spot", 5=>"Shade", 6=>"Hole", 7=>"Cutting", 8=>"Wash", 9=>"Print", 10=>"Twisting", 11=>"Conta",12 => "Color Spot",13 => "Crease Mark",14 => "Dirty Spot",15 => "Distinguish",16 => "Dusted",17 => "Dyeline",18 => "Emb Rejection ",19 => "Embroidery",20 => "Fabric (Z) Hole",21 => "HTS Problem Cutting",22 => "HTS Problem Finishing",23 => "Iron Spot",24 => "Knot",25 => "M/C Knife  Cut",26 => "Measurement (+-)",27 => "Needle Cut",28 => "Oil Spot",29 => "Part Mistake",30 => "Part Shade",31 => "Patta",32 => "Pleat",33 => "Print Reject",34 => "Runing Shade",35 => "Scissor Cut",36 => "Slub",37 => "Softner Mark",38 => "Tag Gun Rej",39 => "Twist",40 => "Uneven Dyeing",41 => "Yarn Missing",42 => "Yarn Contamination",43=>"DIRTY MARK",44=>"FAB FAULT",45=>"SEW IN COMPLETE",46=>"SHADING",47=>"REP DAMAGE",48=>"OTHERS-1",49=>"OTHERS-2",50=>"Sewing Loss",100=>"Others");
echo "<table border='1' style='border-collapse:collapse'>";
foreach($sew_fin_reject_type_for_arr as $key=>$row)
{
    
    echo "<tr>";
    echo "<td>".$key."</td>";
    echo "<td>".$row."</td>";
    echo "<tr>";
}
echo "</table>";
die;
ini_set('precision', 14);
ini_set('serialize_precision', 17);
echo 354.25400000 * 213 . "<br />";
echo strlen((string)354.25400000 * 213);
die;
echo dirname(__FILE__);die;
$url = "http://api.alarabiya.net/sections/2/";
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HEADER, false);
$data = curl_exec($curl);
curl_close($curl);
echo $data;
die;
$employees = array("JAHIDUR", "KAUSAR", "AZIZ", "REZA", "JOY");
$positions = array("Chair 1", "Chair 2", "Chair 3");
shuffle($employees);

foreach($employees as $row)
{
    $selected_position = (!empty($positions)) ? array_rand($positions) : $positions;
    echo $row . " = " . ((!empty($positions)) ? $positions[$selected_position] : "") . "<br />";
    
    if(!empty($positions))
        unset($positions[$selected_position]);
}

//$rand_keys = array_rand($input, 2);
//echo $input[$rand_keys[0]] . "\n";

//phpinfo();
die;
include ("ext_resource/excel/excel/vendor/autoload.php");
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

$htmlString = '<table>
                  <tr>
                      <td>Hello World</td>
                  </tr>
                  <tr>
                      <td>Hello<br />World</td>
                  </tr>
                  <tr>
                      <td>Hello<br>World</td>
                  </tr>
              </table>';

$reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
$spreadsheet = $reader->loadFromString($htmlString);

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
$writer->save('write.xls'); die;
include('includes/common.php');
$con = oci_pconnect('PLATFORM_ACCOUNTS', 'PLATFORM_ACCOUNTS', '//192.168.11.242:1521/logicdb');

//:in_journal_id_pk,:in_journal_source,:in_company_id,:in_user_id,:out_journal_id,:out_error

function sql_insert_plsql($con, $procedure_name="PKG_JOURNALENGINE.PRC_JOURNALENTRY", $in_param_arr, $out_param_arr)
{
    $field_names = implode(",", $fields_arr);
    $sql = "BEGIN $procedure_name($field_names); END;";
    
    $journal_entry = oci_parse($con, $sql);

    $journal_id     = 74;
    $journal_source = 1;
    $company_id     = null;
    $user_id        = 11111;
    $out_journal_id = null;
    $out_error      = null;

    oci_bind_by_name($journal_entry, ':in_journal_id_pk', $journal_id);
    oci_bind_by_name($journal_entry, ':in_journal_source', $journal_id);
    oci_bind_by_name($journal_entry, ':in_company_id', $company_id);
    oci_bind_by_name($journal_entry, ':in_user_id', $user_id);

    oci_bind_by_name($journal_entry, ':out_journal_id', $out_journal_id, 100 );
    oci_bind_by_name($journal_entry, ':out_error', $out_error, 500 );

    $r = oci_execute($journal_entry); 
    oci_free_statement($journal_entry);

    if($r)
    {
        echo $out_journal_id;
    }
    else
    {
        echo "no";
    }
}
die;




//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.office365.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username = 'trims@team.com.bd';
	$mail->Password = 'tsb6%4&8&$3FcGT';                              //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->SetFrom('trims@team.com.bd', 'FromEmail');
	$mail->addAddress('jahid0209@gmail.com', 'ToEmail');
    $mail->addReplyTo('trims@team.com.bd', 'Information');

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Here is the subject';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
die;
?>

<script src="includes/functions.js" type="text/javascript"></script>
<script>
var a = number_format_common_new( 10000.23457777777776, 3 );
alert(a);
</script>
<?
ini_set('display_errors',1);
//echo $con = oci_connect('LOGIC3RDVERSION', 'LOGIC3RDVERSION', '//192.168.11.252:1521/ORCL');
echo oci_connect('LOGIC3RDVERSION', 'LOGIC3RDVERSION', '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.11.252)(PORT = 1521)) (CONNECT_DATA = (SERVICE_NAME = ORCL) (SID = ORCL)))');
//echo oci_connect('LOGIC3RDVERSION', 'LOGIC3RDVERSION', '//192.168.11.252:1521/ORCL');
//oci_connect('LOGIC3RDVERSION', 'LOGIC3RDVERSION', 'ORCL');
die;

include('includes/common.php');

 //echo return_next_id_by_sequence("PURCHASE_REQ_SEQ");
 $con = connect();
 //echo $updateSqqSql = execute_query("update platform_sequence_pk set next_id=(next_id+1) where id=1");
//oci_commit($con);
 //echo sql_select("select NEXT_ID from PLATFORM_SEQUENCE_PK where id=1");
 //die;
 //

$seq_sql="select f_NextSeq('pro_roll_details',1,0,2017,13) next_id from dual";
$seqArray=sql_select( $seq_sql,'', $con );

			// Prepare System ID
$comp_prefix = return_field_value("company_short_name","lib_company", "id=$company_id");
$recv_number_prefix = $comp_prefix . "-" . $mrr_prefix . "-" . substr(date("Y", time()),2,2) . "-";
$recv_number = $recv_number_prefix . "*" . str_pad($seqArray[0]['NEXT_ID'], 5, '0', STR_PAD_LEFT) . "*". $recv_number_prefix . "" . str_pad($seqArray[0]['NEXT_ID'], 5, '0', STR_PAD_LEFT);

echo $recv_number; die;

 $seq_sql = "select NextVal('$table_name',1,2,2017,13)";
			$seqArray = sql_select( $seq_sql,'' );
			print_r($seqArray); die;
			foreach ($seqArray as $result)
				return $result[0];
			die;
$queryText="select f_NextSeq('INV_RECEIVE_MASTERS',1,2,2017,15) next_id from dual";
$nameArray=sql_select( $queryText,'', $new_conn );
 echo $nameArray[0]['NEXT_ID']; die;

$queryText="select NextSeq('INV_RECEIVE_MASTER',' and company_id=1') next_seq from dual";
$nameArray=sql_select( $queryText,'', $con );
foreach ($nameArray as $result)
	echo $result[csf('NEXT_SEQ')];


die;

$trans_id="26257,27072,1884,20702,25659,1882,12184,22453,23132,760,1866,22464,26255,12059,22342,1179,12166,4184,3638,22522,3641,1860,1026";
$trans_id_arr=array_unique(explode(",",$trans_id));

 //313,11903,939,944,23230,23232,1077,21653,21683,25656,21429,21478,21225,21228,21473,21585,21425,21549,1207,22775,22345,27065,22471,19506,26252,26253,26254,3167,3176,22385,577,20927,23133,23140,3711,26256,12260,12168,12261,23234,23235

$all_prod_id="313,12184,11903,20702,939,944,23230,23232,1026,1077,21653,21683,25656,25659,21429,21478,21225,21228,21473,21585,21425,21549,1860,1882,1866,1884,1179,1207,22522,22775,22342,22345,27065,27072,22464,22471,12059,19506,26252,26253,26254,3167,3176,22385,22453,577,760,4184,20927,3638,23133,3641,23140,3711,23132,26255,26256,26257,12166,12260,12168,12261,23234,23235";
$all_prod_id_arr=array_unique(explode(",",$all_prod_id));


$result=array_diff($all_prod_id_arr,$trans_id_arr);
$result=implode(",",$result);
echo($result);



/*insert into product_details_master (id, company_id, supplier_id, store_id, item_category_id, entry_form, detarmination_id,	sub_group_code, sub_group_name, item_group_id, item_description, product_name_details, lot, item_code, unit_of_measure, 	re_order_label, minimum_label, maximum_label, item_account, packing_type, avg_rate_per_unit, last_purchased_qnty, current_stock, last_issued_qnty, stock_value, item_return_qty, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, item_color, gmts_size, gsm, brand, brand_supplier, dia_width, item_size, weight, allocated_qnty, available_qnty, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, origin, model, capacity, otherinfo, brand_name)
select ROW_NUMBER() OVER (PARTITION BY company_id ORDER BY company_id desc) as num_row, 4 as company_id, supplier_id, store_id, item_category_id, entry_form, detarmination_id,    sub_group_code, sub_group_name, item_group_id, item_description, product_name_details, lot, item_code,
unit_of_measure,  re_order_label, minimum_label, maximum_label, item_account, packing_type, avg_rate_per_unit, last_purchased_qnty, current_stock, last_issued_qnty, stock_value, item_return_qty, yarn_count_id,
yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, item_color, gmts_size, gsm, brand, brand_supplier, dia_width, item_size, weight, allocated_qnty, available_qnty,
 inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, origin, model, capacity, otherinfo, brand_name
 from product_details_master where company_id=1 and item_category_id in(8,9,10,11,15,16,17,18,19,20,21,22,32,33,34,35,36,37,38,39,40,41,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,89,90,91,92,93,94)
 and status_active=1 and is_deleted=0
 order by ROW_NUMBER() OVER (PARTITION BY company_id ORDER BY company_id desc)*/


 ?>