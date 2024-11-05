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

http://localhost/platform-v3.5/production/reports/line_wise_display_dashboard.php?url=company name/line Name/
http://118.179.205.12/jk_erp/production/reports/line_wise_display_dashboard.php?url=JK Knit Composite Ltd./33/
http://192.168.100.4/fakirfashion_erp/production/reports/line_wise_display_dashboard.php?url=Fakir Fashion Ltd./Unit-10 Sewing/80/
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
                    $('#report').html(response);

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
        var interval = 20000;  //20s
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
            $('#report').html(reponse);
            $('#preloader').fadeOut();    // loading preloader
            $("#firsttime").css('display','block');
            //var interval = setInterval(function(){ alert("Hello"); }, 3000);
            /*var words = [
                'Lärare',
                'Rektor',
                'Studievägledare',
                'Lärare'
            ];
            setInterval(function() {
                $('#order_buyer').fadeOut(function() {
                  $(this).html(words[i = (i + 1) % words.length]).fadeIn();
                });
                // 2 seconds
              }, 2000);*/

            
            /*var timesRun = 0;
            var interval = setInterval(function(){
                timesRun += 1;
                alert("Hello");
                if(timesRun === 3){
                    clearInterval(interval);
                }
                //do whatever here..
            }, 2000);*/ 
            //clearInterval(interval);
            /*$('#order_buyer').hide();
            var datas=$('#order_buyer').val();
            var data=datas.split('**');
            var order=data[0];
            var buyer=data[1];*/

           /* $.setInterval(function() {
                $('#orderbuyer').fadeOut().fadeIn();
            }, 2000);*/

           /* function greet(){
              alert('Howdy!');
            }
            setInterval(greet, 2000);
           */
            /*$(function () {
                $('#order_buyer').val(order);
                $("#order_buyer").slideDown(1000); //have tried "fast" also
                $('#order_buyer').val(buyer);
                $("#order_buyer").slideDown(1000);*/ //have tried "fast" also
                  //$("#order").delay(500);
                   //$("#order").slideDown(1000);

                //$("#orderbuyer").slideDown(2000); 
                //$("#orderbuyer").slideUp(2000).delay(500).slideDown(2000);
                  //.delay(5000)
                  //.slideDown(500);
                  /*$('#orderbuyer').slideDown(2000, function() {
                      var self = this;
                      alert(self);
                      setTimeout(function() {
                         $(self).slideDown(1000);
                      }, 1000);
                   });*/
            //});  
            //alert('system');            
        }    
    }  


</script>

<style>
    /*.show_msg {display: none; width:25%; border-left: 1px solid #8DAFDA; border-right: 1px solid #8DAFDA; border-bottom: 3px solid #99B9E2; border-top: 1px solid #8DAFDA; border-radius: .7em; margin: auto; background-image: -moz-linear-gradient(bottom, rgb(254,151,174) 0%, rgb(255,255,255) 10%, rgb(254,151,174) 96%); text-align: center;}*/
    /*.load_msg {display: none; color: white; width: 15%; height:3%;position: absolute; bottom: 0; left: 0; background-color: #000; z-index: 1;}*/
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
<script type="text/javascript">max_height=screen.height;</script>

</html>
