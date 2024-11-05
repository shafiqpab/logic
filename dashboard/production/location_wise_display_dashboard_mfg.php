<?
/*
Created by      :   Rakib 
Creation date   :   03-06-2021 
Updated by      :   
Update date     :   

http://localhost/platform-v3.5/dashboard/production/floor_wise_display_dashboard.php?url=company name/line Name/
http://172.16.30.9:8021/dashboard/production/all_floor_wise_display_dashboard_mfg.php?url=LIBERTY KNITWEAR LTD. [KALIAKOIR UNIT]/Unit-02 Kaliakoir-Gazipur/
*/
require_once('../../includes/common.php');
$url_title = ltrim($_SERVER['PHP_SELF'], '/');
$url = rtrim($_GET['url'], '/');
//echo $url;die;
$url = explode('/', $url);
$url_com_name = $url[0];
$url_location_name = $url[1];
$url_floor_name = $url[2];

// convert company name to id
if ($url_com_name !="") {
    $cbo_company_id = return_field_value('id', 'lib_company',"company_name='$url_com_name'");
}

// convert location name to id
if ($cbo_company_id != "") {
    $cbo_location_id=return_field_value("id", "lib_location", "location_name='$url_location_name' and company_id=$cbo_company_id and is_deleted=0", "id");
}

if ($cbo_company_id =="" || $cbo_location_id =="" ) {
    echo '<div style="width: 98%; height: 45px; color: red; font-weight: bold; font-size: 30px; text-align: center; background-color: #444; padding: 10px; border-radius: 15px;">Please Correct Your URL...</div>';die;
}

// convert floor name to id
$floor_Arr=array();
if ($cbo_company_id != "" && $cbo_location_id != "") {
    $sql_floor=sql_select("select id as ID, floor_name as FLOOR_NAME from lib_prod_floor where company_id=$cbo_company_id and location_id=$cbo_location_id and is_deleted=0 and production_process=5 and id in(56,25,57)");
    
    $floor_Arr=array();
    $floor_id_Arr=array();
    foreach ($sql_floor as $row){
        $floor_Arr[]=$row['FLOOR_NAME'];
        $floor_id_Arr[$row['FLOOR_NAME']]=$row['ID'];
    }
}
//print_r($floor_Arr);die;

if ($cbo_company_id =="" || $cbo_location_id =="" || (count($floor_Arr) < 1)) {
    echo '<div style="width: 98%; height: 45px; color: red; font-weight: bold; font-size: 30px; text-align: center; background-color: #444; padding: 10px; border-radius: 15px;">Floor Not Found of This Location...</div>';die;
}

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

        //dmbChart(150,75,60,20,values,colors,labels,0);
		//dmbChart(155,76,67,12,values,colors,labels,0);
		dmbChart(140,76,65,12,values,colors,labels,0);

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
            ctx.fillStyle='White';
            //ctx.font=(innerRadius)+'px Arial';
			ctx.font = "bold 55px Arial";
            ctx.fillText(values[selectedValue],cx,cy+innerRadius*.9);
            //ctx.font=(innerRadius/4)+'px Arial';
			ctx.font = "22px Cambria";
            ctx.fillText(labels[selectedValue],cx,cy-innerRadius*.20);
        }

    }

    const dataArray = <? echo json_encode($floor_Arr); ?>;
    const floorIdArr = <? echo json_encode($floor_id_Arr); ?>;
    const floordataArray = Object.values(dataArray);
    const floorLength = floordataArray.length;
   
    let currentIndex = 0;

    $(document).ready(function() {
        var cbo_company_id = '<? echo $cbo_company_id; ?>';
        var txt_date = '<? echo date('d-M-Y'); ?>';
        //var txt_date = '09-Jun-2021';
        var cbo_location_id = '<? echo $cbo_location_id; ?>';
        
        let numSuccessfullAjaxCallCount = 0;       
       
        var ajax_call = function(){
            
            var cbo_floor_id = floorIdArr[dataArray[currentIndex]];
            //alert(cbo_floor_id+"**"+currentIndex);
            var data="action=report_generate"+"&max_height='"+max_height+"'"+"&cbo_company_id='"+cbo_company_id+"'"+"&cbo_location_id='"+cbo_location_id+"'"+"&txt_date='"+txt_date+"'"+"&cbo_floor_id='"+cbo_floor_id+"'";
            //alert(data);
            $.ajax({
                type:'POST',
                url:'requires/location_wise_display_dashboard_controller_mfg.php',
                data:data,
                /*beforeSend:function(){
                    $('#loading').fadeIn('fast');
                },*/
                success:function(response){
                    //$('#loading').fadeOut('fast');
                    numSuccessfullAjaxCallCount++;
                    $('#report').html(response);
                    $('#preloader').fadeOut();
                    var efficiency=$("#efficiency").val();
                    fn_create_chart(efficiency);
                    //alert(numSuccessfullAjaxCallCount);
                    if (numSuccessfullAjaxCallCount%2 == 1)
                    {
                        $("#firsttime").css('display','block');                        
                    }
                    else
                    {
                        $("#secondtime").css('display','block');
                        currentIndex++;
                        numSuccessfullAjaxCallCount=0;
                    }
                    //alert(currentIndex+'**'+floorLength);
                    if (currentIndex == floorLength) {
                        currentIndex = 0;                       
                    }
                }
            });
        }
        var interval = 20000;  //20s
        setInterval(ajax_call,interval);
    });  
    

    

    function fn_report_generated()
    {  
        var cbo_company_id = '<? echo $cbo_company_id; ?>';
        var txt_date = '<? echo date('d-M-Y'); ?>';
        //var txt_date = '09-Jun-2021';
        var cbo_location_id = '<? echo $cbo_location_id; ?>';
        var cbo_floor_id = floorIdArr[dataArray[currentIndex]];

        var data="action=report_generate"+"&max_height='"+max_height+"'"+"&cbo_company_id='"+cbo_company_id+"'"+"&cbo_location_id='"+cbo_location_id+"'"+"&txt_date='"+txt_date+"'"+"&cbo_floor_id='"+cbo_floor_id+"'";
        //alert(data);
        http.open("POST","requires/location_wise_display_dashboard_controller_mfg.php",true);
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
            //$("#secondtime").css('display','block');
            
            var efficiency=$("#efficiency").val();
            fn_create_chart(efficiency);         
        }    
    } 


</script>

<style>
    img { height:90%;  max-width: 100%; }
    #preloader { background-color: #f7f7f7; width: 100%; height: 100%; position: fixed; top: 0; left: 0; right: 0; z-index: 5000; display: -webkit-box; display: -ms-flexbox; display: flex; -webkit-box-align: center; -ms-flex-align: center; -ms-grid-row-align: center; align-items: center; -webkit-box-pack: center; -ms-flex-pack: center; justify-content: center; }
</style>
      
</head>
 
<body>
    <!-- Preloader -->
    <div id="preloader">
        <img style="width: 110px; height:auto" src="../../images/loading1.gif">
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
