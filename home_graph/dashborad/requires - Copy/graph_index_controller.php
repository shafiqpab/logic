<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//style search------------------------------//
if($action=="ex_factory_date_category_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Date Category","../../../", 1, 1, $unicode);
	?>
    <script>
	function js_set_value(str)
	{
		parent.emailwindow.hide();
	}
    </script>
    </head>
    <body>
        <div align="center" style="width:100%;" >
        <form name="searchyarnfrm_1"  id="searchyarnfrm_1" autocomplete="off">
			<table width="280">
                <tbody>
                    <tr>
                        <td>Date Category : </td>
                        <td align="center">				
                            <?
                                echo create_drop_down( "cbo_date_type", 200, array(1=>"Ship date",2=>"Ext. ship date"),"", 1, "--Select--", 2, "",0 );
                            ?>                    
                        </td>  
                    </tr>
                    <tr><td colspan="2" align="center">&nbsp;</td></tr>
                    <tr><td colspan="2" align="center"><button type="button" onClick="js_set_value();" class="formbutton" style="width:80px;">Close</button></td></tr>
                </tbody>
            </table>
            
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
	<?
	exit();
}



?>

