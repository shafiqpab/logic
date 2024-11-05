

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
         
    </head>
  <?
  $date=date("Y",time());
$month_prev=add_month(date("Y-m-d",time()),-3);
// echo date("Y-m-d",time());
$month_next=add_month(date("Y-m-d",time()),8);
// echo $month_next;

$start_yr=date("Y",strtotime($month_prev));
$end_yr=date("Y",strtotime($month_next));
  
  function add_month($orgDate,$mon){
  $cd = strtotime($orgDate);
  $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,date('d',$cd),date('Y',$cd)));
  return $retDAY;
}

  ?>
  
        <!-- saved from url=(0013)about:internet -->
        <!-- amcharts script-->
        <!-- swf object (version 2.2) is used to detect if flash is installed and include swf in the page -->
     
        <script type="text/javascript" src="reports/amcharts/flash/swfobject.js"></script>

		<!-- following scripts required for JavaScript version. The order is important! -->
		<script type="text/javascript" src="reports/amcharts/javascript/amcharts.js"></script>
		<script type="text/javascript" src="reports/amcharts/javascript/amfallback.js"></script>
		<script type="text/javascript" src="reports/amcharts/javascript/raphael.js"></script>
        <!-- chart is placed in this div. if you have more than one chart on a page, give unique id for each div -->
        <table width="1050" cellpadding="0" cellspacing="0">
    	<tr>
        	<td height="50" valign="middle" align="center" colspan="2">
            	<font size="3"><strong>Yearly Order Status for Year: <? echo "$start_yr"."-"."$end_yr"; ?></strong></font>
            </td>
            <td colspan="2"></td>
        </tr>
        <tr>
        	<td bgcolor="#00CCFF" height="8" valign="middle" align="center" colspan="2">
            <td bgcolor="#00CCFF" width="8"></td>
            <td></td>
            </td>
        </tr>
        <tr>
        	<td width="8" bgcolor="#00CCFF">
        	<td align="center" width="794">
        		<div id="chartdiv" style="width:794px; height:400px; background-color:#FFFFFF"></div>
        
        <script type="text/javascript">
        
            var params = 
            {
                bgcolor:"#FFFFFF"
            };
            
            var flashVars = 
            {
                path: "reports/amcharts/flash/", 
                settings_file: "settings.xml",
                data_file: "data.php"
            };
            
            window.onload = function()
            {            
                // change 8 to 80 to test javascript version            
                if (swfobject.hasFlashPlayerVersion("8"))
                {
                    swfobject.embedSWF("reports/amcharts/flash/amcolumn.swf", "chartdiv", "800", "400", "8.0.0", "../../../../amcharts/flash/expressInstall.swf", flashVars, params);
                }
                else
                {
                    // Note, as this example loads external data, JavaScript version might only work on server
                    var amFallback = new AmCharts.AmFallback();
                    amFallback.pathToImages = "../../../../amcharts/javascript/images/";
                    amFallback.settingsFile = flashVars.settings_file;
                    amFallback.dataFile = flashVars.data_file;				
                    amFallback.type = "column";
                    amFallback.write("chartdiv");
                }
            }
        
        </script>
        </td>
            <td width="8" bgcolor="#00CCFF"></td>
            <td valign="middle" align="center">
            <img src="images/logic_logo.png" height="175" width="150" />
            <br /><br />
            <font size="3"><strong>Logic Software Ltd.</strong></font><br />
            House#345, Road#25,<br />
            New DOHS, Mohakhali,<br />
            Dhaka.
            </td>
        </tr>
        <tr>
        	<td height="8" colspan="2" bgcolor="#00CCFF"></td>
            <td width="8" bgcolor="#00CCFF"></td>
            <td></td>
        </tr>
        <tr>
        	<td height="70" valign="middle" colspan="2" align="center"><font size="5"><strong>Platform</strong></font> <font size="2">...a system that speaks.</font></td>
            <td width="8"></td>
            <td></td>
        </tr>
	</table>
    </body>
</html>
