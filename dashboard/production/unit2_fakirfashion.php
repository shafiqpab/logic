<?
require_once('../../includes/common.php');
$url = ltrim($_SERVER['PHP_SELF'], '/');

echo load_html_head_contents("$url", "../../", '', '','','','','');
?>  

<script> 
    
    $(document).ready(function() {

        var cbo_company_name = '1';
        var txt_date = '<? echo date('d-M-Y'); ?>';
        //var txt_date = '01-Jan-2018';
        var cbo_floor = '2';
        var data="action=report_generate"+"&cbo_company_name='"+cbo_company_name+"'"+"&txt_date='"+txt_date+"'"+"&cbo_floor='"+cbo_floor+"'";

        var ajax_call = function(){
            $.ajax({
                type:'POST',
                url:'requires/unit2_controller.php',
                data:data,
                beforeSend:function(){
                    $('#loading').fadeIn('fast');
                },
                success:function(response){
                    $('#loading').fadeOut('fast');
                    $('#report').html(response);
                }
            });
        }
        var interval = 15*60000;
        setInterval(ajax_call,interval);
    });

    function fn_report_generated()
    {  
        var cbo_company_name = '1';
        var txt_date = '<? echo date('d-M-Y'); ?>';
        //var txt_date = '01-Jan-2018';
        var cbo_floor = '2';
        var data="action=report_generate"+"&cbo_company_name='"+cbo_company_name+"'"+"&txt_date='"+txt_date+"'"+"&cbo_floor='"+cbo_floor+"'";
        //freeze_window(3);
        http.open("POST","requires/unit2_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fn_report_generated_reponse;
    }        

    function fn_report_generated_reponse()
    {    
        if(http.readyState == 4) 
        {
            document.title = "<?php echo $url; ?>";  // show title
            $("#show").fadeIn().fadeOut(2000);  // Message show  
            var reponse=trim(http.responseText); 
            $('#report').html(reponse);
            $('#preloader').fadeOut();    // loading preloader 

        }    
    }  


</script>

<style>
    .show_msg {display: none; width:25%; border-left: 1px solid #8DAFDA; border-right: 1px solid #8DAFDA; border-bottom: 3px solid #99B9E2; border-top: 1px solid #8DAFDA; border-radius: .7em; margin: auto; background-image: -moz-linear-gradient(bottom, rgb(254,151,174) 0%, rgb(255,255,255) 10%, rgb(254,151,174) 96%); text-align: center;}
    .load_msg {display: none; color: white; width: 15%; height:3%;position: absolute; bottom: 0; left: 0; background-color: #000; z-index: 1;}
    img { height: auto;  max-width: 100%; }
    #preloader { background-color: #f7f7f7; width: 100%; height: 100%; position: fixed; top: 0; left: 0; right: 0; z-index: 5000; display: -webkit-box; display: -ms-flexbox; display: flex; -webkit-box-align: center; -ms-flex-align: center; -ms-grid-row-align: center; align-items: center; -webkit-box-pack: center; -ms-flex-pack: center; justify-content: center; }
</style>  
      
</head>
 
<body>
    <!-- Preloader -->
    <div id="preloader">
        <img style="width: 110px;" src="../../images/loading1.gif">
    </div>

    <div id="loading" class="load_msg">
        Please wait the page is loading......
    </div>

    <div id="show" class="show_msg">Report is Generated Successfully.....</div>
    
    <div id="report" align="center">  
         <? //echo load_freeze_divs ("../../",'');  ?>
    </div>

    <script>
        window.onload = function() {
            fn_report_generated();
        };        
    </script>

</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>
