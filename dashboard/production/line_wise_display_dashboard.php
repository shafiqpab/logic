<?
/*
Created by      :   Rakib 
Creation date   :   03-06-2021 
Updated by      :   
Update date     :   

Note: Must be Line name unique.

production type = 4 Not entry_form //input
production type = 5 Not entry_form //output
company: JK Group.

http://localhost/platform-v3.5/dashboard/production/line_wise_display_dashboard.php?url=Fashion Apparel Ltd./28/
http://192.168.100.4/fakirfashion_erp/dashboard/production/line_wise_display_dashboard.php?url=Fakir%20Fashion%20Ltd.//28
*/
require_once('../../includes/common.php');
$url_title = ltrim($_SERVER['PHP_SELF'], '/');
$url = rtrim($_GET['url'], '/');
//echo $url;die;
$url = explode('/', $url);
$url_com_name = $url[0];
$url_floor_name = $url[1];
$url_line_name = $url[2];
echo load_html_head_contents("$url_title", '../../', '', '','','','','');
?>  

<script> 
    const intervalID = setInterval(myCallback, 3000, 1);
    var flag=0;
    function myCallback(a)
    {
        flag+=a;
        if(flag==2){
            //alert(flag);
            flag=0;
        }
        else{
            //alert(flag);
        }
    }
    $(document).ready(function() {
        var cbo_company_name = '<? echo $url_com_name; ?>';
        var txt_date = '<? echo date('d-M-Y'); ?>';
        //var txt_date = '09-Jun-2021';
        //var txt_date = '19-Feb-2021';C-12,C-13
        var cbo_floor = '<? echo $url_floor_name; ?>';
        var cbo_line = '<? echo $url_line_name; ?>';
        var data="action=report_generate"+"&max_height='"+max_height+"'"+"&cbo_company_name='"+cbo_company_name+"'"+"&txt_date='"+txt_date+"'"+"&cbo_floor='"+cbo_floor+"'"+"&cbo_line='"+cbo_line+"'";

        var numSuccessfullAjaxCallCount = 0;
        var ajax_call = function(){
            $.ajax({
                type:'POST',
                url:'requires/line_wise_display_dashboard_controller.php',
                data:data,
                /*beforeSend:function(){
                    $('#loading').fadeIn('fast');
                },*/
                success:function(response){
                    //$('#loading').fadeOut('fast');
                    numSuccessfullAjaxCallCount++;
                    $('#rptContainer').html(response);

                    if (numSuccessfullAjaxCallCount%2 == 1)
                    {
                        $("#secondtime").css('display','block');
                    }
                    else
                    {
                        $("#firsttime").css('display','block');                       
                       
                    }
                }
            });
        }
        var interval = 10000;  //10s
        setInterval(ajax_call,interval);
    });

    function fn_report_generated()
    {  
        var cbo_company_name = '<? echo $url_com_name; ?>';
        var txt_date = '<? echo date('d-M-Y'); ?>';
        //var txt_date = '09-Jun-2021';
        //var txt_date = '19-Feb-2021';
        //alert(max_height);
        var cbo_floor = '<? echo $url_floor_name; ?>';
        var cbo_line = '<? echo $url_line_name; ?>';
        var data="action=report_generate"+"&max_height='"+max_height+"'"+"&cbo_company_name='"+cbo_company_name+"'"+"&txt_date='"+txt_date+"'"+"&cbo_floor='"+cbo_floor+"'"+"&cbo_line='"+cbo_line+"'";
        //alert(data);
        http.open("POST","requires/line_wise_display_dashboard_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fn_report_generated_reponse;
    }        

    function fn_report_generated_reponse()
    {    
        if(http.readyState == 4) 
        {
            document.title = "<?php echo $url_title; ?>";  // show title
            //$("#show").fadeIn().fadeOut(2000);  // Message show  
            var reponse=trim(http.responseText); 
            $('#rptContainer').html(reponse);
            $('#preloader').fadeOut();    // loading preloader
            $("#firsttime").css('display','block');        
        }    
    }  


</script>

<style>
    img { height: auto;  max-width: 100%; }
    #preloader { background-color: #f7f7f7; width: 100%; height: 100%; position: fixed; top: 0; left: 0; right: 0; z-index: 5000; display: -webkit-box; display: -ms-flexbox; display: flex; -webkit-box-align: center; -ms-flex-align: center; -ms-grid-row-align: center; align-items: center; -webkit-box-pack: center; -ms-flex-pack: center; justify-content: center; }
</style>  
      
</head>
 
<body>
    <!-- Preloader -->
    <div id="preloader">
        <img style="width: 110px;" src="../../images/loading1.gif">
    </div>

    <!-- <div id="loading" class="load_msg">
    </div> -->

   
    <div id="rptContainer" align="center">  
         <? //echo load_freeze_divs ("../../",'');  ?>
    </div>

    <script>
        window.onload = function() {
            fn_report_generated();
        };        
    </script>

</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">max_height=screen.height;</script>

</html>
