<?

/*
Created by      :   Rakib 
Creation date   :   03-06-2021 
Updated by      :   
Update date     :   

production type = 5 Not entry_form //output

http://localhost/platform-v3.5/dashboard/production/company_wise_display_dashboard.php?url=company name/
http://103.147.56.98/nzerp/dashboard/production/company_wise_display_dashboard.php?url=N.A.Z. Bangladesh Ltd./
*/
require_once('../../includes/common.php');
$url_title = ltrim($_SERVER['PHP_SELF'], '/');
$url = rtrim($_GET['url'], '/');
//echo $url;die;
//$url = explode('/', $url);
$url_com_name = $url;
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
    function fn_create_chart(efficiency)
    {
        var efficiency=efficiency*1;
        var others=100-efficiency;

        var canvas=document.getElementById("canvas");
        var ctx=canvas.getContext("2d");

        var colors=['red','green','black'];
        var values=[efficiency,0,others];
        var labels=['Actual','Robot','Mandatory'];

        dmbChart(150,75,60,20,values,colors,labels,0);

        function dmbChart(cx,cy,radius,arcwidth,values,colors,labels,selectedValue){
            var tot=0;
            var accum=0;
            var PI=Math.PI;
            var PI2=PI*2;
            var offset=-PI/2;
            ctx.lineWidth=arcwidth;
            for(var i=0;i<values.length;i++){tot+=values[i];}
            for(var i=0;i<values.length;i++){
                ctx.beginPath();
                ctx.arc(cx,cy,radius,
                    offset+PI2*(accum/tot),
                    offset+PI2*((accum+values[i])/tot)
                );
                ctx.strokeStyle=colors[i];
                ctx.stroke();
                accum+=values[i];
            }
            var innerRadius=radius-arcwidth-3;
            ctx.beginPath();
            ctx.arc(cx,cy,innerRadius,0,PI2);
            ctx.fillStyle=colors[selectedValue];
            ctx.fill();
            ctx.textAlign='center';
            ctx.textBaseline='bottom';
            ctx.fillStyle='white';
            ctx.font=(innerRadius)+'px verdana';
            ctx.fillText(values[selectedValue],cx,cy+innerRadius*.9);
            ctx.font=(innerRadius/4)+'px verdana';
            ctx.fillText(labels[selectedValue],cx,cy-innerRadius*.25);
        }

    }

    
    $(document).ready(function() {
        var cbo_company_name = '<? echo $url_com_name; ?>';
        var txt_date = '<? echo date('d-M-Y'); ?>';
        //var txt_date = '<? //echo date( "d-M-Y", strtotime( date('d-M-Y') . "-1 day"));?>';
        //var txt_date = '09-Jun-2021';
        var data="action=report_generate"+"&max_height='"+max_height+"'"+"&cbo_company_name='"+cbo_company_name+"'"+"&txt_date='"+txt_date+"'";
        //alert(data);
        var numSuccessfullAjaxCallCount = 0;
        var ajax_call = function(){
            $.ajax({
                type:'POST',
                url:'requires/company_wise_display_dashboard_controller.php',
                data:data,
                /*beforeSend:function(){
                    $('#loading').fadeIn('fast');
                },*/
                success:function(response){
                    //$('#loading').fadeOut('fast');
                    numSuccessfullAjaxCallCount++;
                    $('#report').html(response);
                    var efficiency=$("#efficiency").val();
                    fn_create_chart(efficiency);
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
        var data="action=report_generate"+"&max_height='"+max_height+"'"+"&cbo_company_name='"+cbo_company_name+"'"+"&txt_date='"+txt_date+"'";
        //alert(data);
        http.open("POST","requires/company_wise_display_dashboard_controller.php",true);
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
            
            var efficiency=$("#efficiency").val();
            fn_create_chart(efficiency);         
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
