<?
/*
Created by      :   Rakib 
Creation date   :   15-01-20 
Updated by      :   
Update date     : 
http://localhost/platform_v3.5/printing/reports/printing_hourly_production_display_report.php?url=company name/
http://localhost/platform_v3.5/printing/reports/printing_hourly_production_display_report.php?url=Fashion Apparel Ltd./
http://116.193.218.254/trims_erp//printing/reports/printing_hourly_production_display_report.php?url=Tahmid%20n%20Twalha%20Accessories%20and%20Printing%20Ltd./
*/
require_once('../../includes/common.php');
$url_title = ltrim($_SERVER['PHP_SELF'], '/');
$url = rtrim($_GET['url'], '/');
$url = explode('/', $url);
$url_com_name = $url[0];
echo load_html_head_contents("$url_title", '../../', '', '','','','','');
?>  

<script> 
    
    $(document).ready(function() {
        var cbo_company_name = '<? echo $url_com_name; ?>';
        var txt_date = '<? echo date('d-m-Y'); ?>';
        //var txt_date = '04-11-2021';
        var data="action=report_generate"+"&max_height='"+max_height+"'"+"&cbo_company_name='"+cbo_company_name+"'"+"&txt_date='"+txt_date+"'";

        var numSuccessfullAjaxCallCount = 0;
        var ajax_call = function(){
            $.ajax({
                type:'POST',
                url:'requires/printing_hourly_production_display_report_controller.php',
                data:data,
                /*beforeSend:function(){
                    $('#loading').fadeIn('fast');
                },*/
                success:function(response){
                    //$('#loading').fadeOut('fast');
                    //numSuccessfullAjaxCallCount++;
                    $('#report').html(response);

                    /*if (numSuccessfullAjaxCallCount%2 == 1)
                        $("#secondtime").css('display','block');
                    else
                        $("#firsttime").css('display','block');*/

                }
            });
        }
        var interval = 10000;  //10s
        setInterval(ajax_call,interval);
    });

    function fn_report_generated()
    {  
        var cbo_company_name = '<? echo $url_com_name; ?>';
        var txt_date = '<? echo date('d-m-Y'); ?>';
        //var txt_date = '04-11-2021';
        var data="action=report_generate"+"&max_height='"+max_height+"'"+"&cbo_company_name='"+cbo_company_name+"'"+"&txt_date='"+txt_date+"'";
        //alert(data);
        http.open("POST","requires/printing_hourly_production_display_report_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fn_report_generated_reponse;
    }        

    function fn_report_generated_reponse()
    {    
        if(http.readyState == 4) 
        {
            var reponse=trim(http.responseText); 
            $('#report').html(reponse);
        }    
    }  


</script>
</head>
 
<body>
    <div id="report" align="center">  
    </div>

    <script>
        window.onload = function() {
            fn_report_generated();
        };        
    </script>

</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">max_height=window.innerHeight</script>

</html>
