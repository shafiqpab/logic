<?
/*-------------------------------------------- Comments
Purpose			: 	Help Desk contain All report and entry page business documentary
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam Reza [Cell: +880 151 1100004]
Creation date 	: 	24-10-2019
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:

*/


header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="sweater_sample_progress_monitoring_report")
{
 echo load_html_head_contents("Help Desk","../../../",0,0,$unicode,0,''); 
?>
    <table>
        <tr> 
            <td>
                <h3>Show Button</h3>
                <ul>
                    <li>Washing Completion Date=if emblishment is No than linking plan date +1</li>
                    <li>Washing Completion Date=if emblishment is yes than embelishment+1</li>
                    <li>Sample Delivery Date come form Sweater Sample Acknowledge Confirm Del. End Date Coloum</li>
                    <li><span style="background-color:#5ED05A">&nbsp;&nbsp;&nbsp;&nbsp;</span> [Green color] On Time Done</li>
                    <li><span style="background-color:#F00">&nbsp;&nbsp;&nbsp;&nbsp;</span> [Red color] Late Done</li>
                    <li><span style="background-color:#FF0">&nbsp;&nbsp;&nbsp;&nbsp;</span> [Yellow color] Not Done</li>
                </ul>
            </td>                   			
        </tr>
    </table>

<?
exit();
}



?>