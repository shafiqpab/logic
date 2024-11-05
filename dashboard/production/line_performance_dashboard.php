<?php
/* -------------------------------------------- Comments -----------------------
  Purpose           :   This Form Will Create For Line Wise Performance .
  Functionality :
  JS Functions  :
  Created by        :   Rakib Hasan Mondal
  Creation date     :   25-09-2023
  Updated by        :
  Update date       :
  QC Performed BY   :
  QC Date           :
  Comments          :   
 */

session_start();
// if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
$_SESSION['page_permission']=$permission;
$url = rtrim($_GET['url'], '/');
$url = explode('/', $url);
$url_com_name = $url[0];
$url_location_name = $url[1];
$url_unit_name = $url[2];

//------------------------------------------------------------------------------------
echo load_html_head_contents("Display Report", "../../", 1, 1,$unicode,1,1,1);
?>  
 <style>
    body{
        background: #000000;
    }
    *{
        color: #fff;
    }
 </style>
</head>
 
<body >
    <div id="report_container2">
       
    </div> 
</body>
<script>
    function genarate_report ()
    { 
        var data="action=report_generate"+'&comp_name='+'<?=$url_com_name ?>'+'&location_name='+'<?=$url_location_name?>'+'&unit_name='+'<?=$url_unit_name?>';
        http.open("POST","requires/line_performance_dashboard_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fn_report_generated_reponse;   
    }
    function fn_report_generated_reponse()
    {
        if(http.readyState == 4) 
        {
            var reponse=trim(http.responseText).split("####");  
            $('#report_container2').html(reponse[0]); 
            
        }
        
    } 
    // Run the function once when the page is loaded
    genarate_report();

    // Run the function every 20 seconds using setInterval
    setInterval(genarate_report, 20000);
</script>
<!-- <script src="../../includes/functions_bottom.js" type="text/javascript"></script> -->
</html>
