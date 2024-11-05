<?php
/* -------------------------------------------- Comments -----------------------
  Purpose           :   This Form Will Create Display Report Report.
  Functionality :
  JS Functions  :
  Created by        :   Abdul Barik Tipu
  Creation date     :   04-11-2023
  Updated by        :
  Update date       :
  QC Performed BY   :
  QC Date           :
  Comments          :   URL: dashboard\production\location_wise_finish_stock_display_board_v2.php
 */

session_start();
// if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//------------------------------------------------------------------------------------
echo load_html_head_contents("Display Report", "../../", 1, 1,$unicode,1,1,1);
?>  
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> -->
<script type="text/javascript">
    <? 
        $companyArr = return_library_array("select id,company_name from lib_company","id","company_name"); 
        $companyArr = json_encode($companyArr);
        echo "var companyArr= " . $companyArr . ";\n";
        
        $floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name"); 
        $floorArr = json_encode($floorArr);
        echo "var floorArr= " . $floorArr . ";\n";     

        $locationArr = return_library_array("SELECT id,location_name from lib_location where status_active =1 and is_deleted=0","id","location_name"); 
        $locationArr = json_encode($locationArr);
        echo "var locationArr= " . $locationArr . ";\n";

        $storeArr = return_library_array("select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and b.category_type=2 and a.status_active=1 and a.is_deleted=0","id","store_name"); 
        $storeArr = json_encode($storeArr);
        echo "var storeArr= " . $storeArr . ";\n";

        $floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where status_active =1 and is_deleted=0","floor_room_rack_id","floor_room_rack_name");

        $floorRoomRackArr = json_encode($floor_room_rack_arr);
        echo "var floorRoomRackArr= " . $floorRoomRackArr . ";\n";
    ?>
    var comJsonData = JSON.stringify(companyArr);
    var comObj = JSON.parse(comJsonData);

    var floorJsonData = JSON.stringify(floorArr);
    var floorObj = JSON.parse(floorJsonData);

    var locationJsonData = JSON.stringify(locationArr);
    var locationObj = JSON.parse(locationJsonData);

    var storeJsonData = JSON.stringify(storeArr);
    var storeObj = JSON.parse(storeJsonData);

    var idleTime = 0;
    $(document).ready(function () {
        // Increment the idle time counter every sec
        var idleInterval = setInterval(timerIncrement, 1000); // 1 sec

        // Zero the idle timer on mouse movement.
        $(this).mousemove(function (e) {
            idleTime = 0;
        });
        $(this).keypress(function (e) {
            idleTime = 0;
        });
    });

    function timerIncrement() {
        idleTime = idleTime + 1;
        if (idleTime > 10) { // 10 sec
            // $(".menu_icon").click(); right: 0px; background-position: 0px center;
            // document.getElementById("searchPanel").style.right  = '-200px'; 
            // document.getElementById("menu_icon").style.right  = '0px'; 
            // document.getElementById("menu_icon").style.backgroundPosition   = '0px center'; 
            // clearInterval(idleInterval);
            if($('.menu_icon').hasClass('open'))
            {
                $('.menu_icon').removeClass('open');
                $('.menu_icon').text('Open');
                $('.menu_icon').animate({
                    "right":"0px",
                    "background-position":"0px"
                });
                $('#searchPanel').animate({"right":"-200px"});
                document.getElementById("menu_icon").style.right  = '0px'; 
                document.getElementById("menu_icon").style.backgroundPosition   = '0px center';
                $('body').animate({
                    "right":"0px",
                    "z-index":"999"
                });
            }
        }
    }
    // alert(screen.height);
</script>

<script>
  /*if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
  var permission = '<? echo $permission; ?>'; */
  const refresh_time = $("#refresh_time").val()*1;
  function fn_report_generated()
  {   
      /*if (form_validation('cbo_company_id*txt_today_date','Comapny Name*Sewing Date')==false)
      {
          return;
      }
      else
      {*/
          var cbo_company_name = $("#cbo_company_id").val(); 
          var cbo_location = $("#cbo_location").val(); 
          var cbo_store_name = $("#cbo_store_name").val(); 
          var cbo_floor_id = $("#cbo_floor_id").val(); 
          var cbo_room_id = $("#cbo_room_id").val();
          var cbo_rack_id = $("#cbo_rack_id").val();
          var cbo_shelf_id = $("#cbo_shelf_id").val();
          var cbo_buyer_name = $("#cbo_buyer_name").val(); 
          var txt_today_date = $("#txt_today_date").val(); 
          // =====================================
          $("#company_name").text("Company : "+comObj[cbo_company_name]);
          if(locationObj[cbo_location]!=undefined)
          {
              $("#location_name").text(", Location : "+locationObj[cbo_location]);
          }
          else
          {
              $("#location_name").text(", Location : All Location");
          }
          if(storeObj[cbo_store_name]!=undefined)
          {
              $("#store_name").text(", Store : "+storeObj[cbo_store_name]);
          }
          else
          {
              $("#store_name").text(", Store : All Store");
          }
          if(floorRoomRackArr[cbo_floor_id]!=undefined)
          {
              $("#floor_name").text(", Floor : "+floorRoomRackArr[cbo_floor_id]);
          }
          else
          {
              $("#floor_name").text(", Floor : All Floor");
          }
          if(floorRoomRackArr[cbo_room_id]!=undefined)
          {
              $("#room_name").text(", Room : "+floorRoomRackArr[cbo_room_id]);
          }
          else
          {
              $("#room_name").text(", Room : All Room");
          }
          $("#current_date").text(", Date : "+txt_today_date);

        // =====================================
        var page_width = screen.width;
        var page_height = screen.height;
        var data="action=report_generate&cbo_company_name="+cbo_company_name+"&txt_date="+txt_today_date+"&cbo_location="+cbo_location+"&cbo_store_name="+cbo_store_name+"&cbo_floor_id="+cbo_floor_id+"&cbo_room_id="+cbo_room_id+"&cbo_rack_id="+cbo_rack_id+"&cbo_shelf_id="+cbo_shelf_id+'&cbo_buyer_name='+cbo_buyer_name+"&page_width="+page_width+"&page_height="+page_height;
        // freeze_window(3);
        http.open("POST","requires/location_wise_finish_stock_display_board_controller_v2.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fn_report_generated_reponse;
      // }
  }   

  fn_report_generated();     

  function fn_report_generated_reponse()
  {
    if(http.readyState == 4) 
    {
      show_msg('3');
      var reponse=trim(http.responseText).split("####");
      var refresh_time = $("#refresh_time").val()*1000;
      $('#rptContainer').html(reponse[0]);
      //alert(reponse[1]);
      if(reponse[2]!=undefined)
      {
        // alert(reponse[2]);
        document.getElementById('txt_today_date').value = reponse[2];
      }
      document.getElementById('export_btn').innerHTML='<a href="##" onclick="fnExportToExcel()" target=_blank; style="text-decoration:none" id="dlink"><button type="button">Export to Excel</button></a>';
      // release_freezing();
      setTimeout(fn_report_generated,refresh_time);
      setTimeout(refreshTimer,refresh_time);

      // autoScrollAndCallFunction();
    }    
  }

  function fnExportToExcel()
	{
    // $(".fltrow").hide();
    let tableData = document.getElementById("rptContainer").innerHTML;
    // alert(tableData);
    let data_type = 'data:application/vnd.ms-excel;base64,',
    template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table border="2px">{table}</table></body></html>',
		base64 = function (s) {
			return window.btoa(unescape(encodeURIComponent(s)))
		},
		format = function (s, c) {
			return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; })
		}
		
		let ctx = {
			worksheet: 'Worksheet',
			table: tableData
		}
		
    let dt = new Date();
    document.getElementById("dlink").href = data_type + base64(format(template, ctx));
    document.getElementById("dlink").traget = "_blank";
    document.getElementById("dlink").download = dt.getTime()+'_display_board.xls';
    document.getElementById("dlink").click();
    // $(".fltrow").show();
    // alert('ok');
	}

  function new_window()
  {
      var w = window.open("Surprise", "#");
      var d = w.document.open();
      d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report').innerHTML+'</body</html>');
      d.close();
  }
  
</script>  
<style>
    body{
        background: #000000;
    }
    #searchPanel {
      position: fixed;
      top: 0;
      right: 0;
      width: 200px;
      height: 100%;
      background: #082032;
      padding:0px;
      overflow: hidden;
      z-index: 99999;
    }

    #searchPanelInner{
      border-radius: 10px;
      height: 100%;
      background: #082032;
      margin: 5px;
      padding: 5px;
      color: #ffffff;
      overflow-y: scroll;
    }


    #searchPanel ul {
      margin: 0;
      padding: 0;
    }

    #searchPanel ul li {
      margin: 0 10px;
      list-style: none;
      display: block;
      line-height: 30px;
      border-bottom: 1px solid #d39b33;
    }

    #searchPanel ul li.active {
      background: #fbce52;
    }

    #searchPanel ul li a {
      padding: 6px 0;
      color: #FFF;
      font-size: 14px;
      font-weight: bold;
      text-transform: uppercase;
      display: block;
      text-decoration: none;
      -webkit-transition-property: all;
      -webkit-transition-duration: 0.1s;
      -webkit-transition-timing-function: ease-out;
      -moz-transition-property: all;
      -moz-transition-duration: 0.1s;
      -moz-transition-timing-function: ease-out;
      -ms-transition-property: all;
      -ms-transition-duration: 0.1s;
      -ms-transition-timing-function: ease-out;
      -o-transition-property: all;
      -o-transition-duration: 0.1s;
      -o-transition-timing-function: ease-out;
      transition-property: all;
      transition-duration: 0.1s;
      transition-timing-function: ease-out;
    }

    #searchPanel ul li:hover {
      border-bottom: 1px solid #000;
    }

    #searchPanel ul li a:hover {
      color: #000;
    }

    .menu_icon {
      position: fixed;
      right: 0px;
      top: 0px;
      width: 30px;
      height: 18px;
      background: #082032;
      color: #ffffff;
      cursor: pointer;
      font-weight: bold;
      padding: 3px;
      z-index: 9999;
    }
    #currentTimer,#refreshTimer{
        font-weight: bold;
        font-size: 16px;
        border-radius: 4px;
        border:1px solid #ffffff;
        padding: 2px 0;
    }
    button,input[type=text],input[type=number], select,.combo_boxes {
      width: 100%;
      padding: 0px 5px !important;
      margin: 5px 0 !important;
      display: inline-block;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
      height: 20px;
      font-weight: bold;
      font-size: 16px;
      color: #000000;
      text-align: center;
      cursor: pointer;
    }
    button{
        cursor: pointer;
    }
    .indicator p{
        line-height: 20px;
    }
    
    /*=============================*/
    .inputGroup {
         background-color: #fff;
         display: block;
         margin: 3px 0;
         position: relative;
    }
     .inputGroup label {
         padding: 2px 5px;
         width: 100%;
         display: block;
         text-align: left;
         color: #3c454c;
         cursor: pointer;
         position: relative;
         z-index: 2;
         transition: color 200ms ease-in;
         overflow: hidden;
    }
     .inputGroup label:before {
         width: 5px;
         height: 5px;
         border-radius: 50%;
         content: '';
         background-color: #5562eb;
         position: absolute;
         left: 50%;
         top: 50%;
         transform: translate(-50%, -50%) scale3d(1, 1, 1);
         transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
         opacity: 0;
         z-index: -1;
    }
     .inputGroup label:after {
         width: 18px;
         height: 18px;
         content: '';
         border: 2px solid #d1d7dc;
         background-color: #fff;
         background-image: url("data:image/svg+xml,%3Csvg width='32' height='32' viewBox='0 0 32 32' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M5.414 11L4 12.414l5.414 5.414L20.828 6.414 19.414 5l-10 10z' fill='%23fff' fill-rule='nonzero'/%3E%3C/svg%3E ");
         background-repeat: no-repeat;
         background-position: 0px 0px;
         border-radius: 50%;
         z-index: 2;
         position: absolute;
         right: 20px;
         top: 50%;
         transform: translateY(-50%);
         cursor: pointer;
         transition: all 200ms ease-in;
    }
     .inputGroup input:checked ~ label {
         color: #fff;
    }
     .inputGroup input:checked ~ label:before {
         transform: translate(-50%, -50%) scale3d(56, 56, 1);
         opacity: 1;
    }
     .inputGroup input:checked ~ label:after {
         background-color: #54e0c7;
         border-color: #54e0c7;
    }
     .inputGroup input {
         width: 22px;
         height: 22px;
         order: 1;
         z-index: 2;
         position: absolute;
         right: 0px;
         top: 50%;
         transform: translateY(-50%);
         cursor: pointer;
         visibility: hidden;
    }
 
    #new_style div{z-index: 1;}
    #searchPanel{z-index: 2;}
</style>    
</head>
 
<body onload="startTime();">
    <form id="displayReport">
        <div style="width:100%;" align="center">        
            <? //echo load_freeze_divs ("../../",'');  ?>                   
            <div class="layoutContainer" id="layoutContainer">               
                <!-- <div id="ticker">this is a simple scrolling text!</div> -->
               <div class="menu_icon" id="menu_icon"> Open </div>
                <div id="searchPanel">
                    <div id="searchPanelInner">
                        <p>Clock</p>
                        <div id="currentTimer" data-start="<?php echo time()-7200;?>"></div>
                        <p>Last Refresh On:</p>
                        <div id="refreshTimer"></div>
                        <p>Working Company:</p>
                        <? 
                            echo create_drop_down( "cbo_company_id", "", "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down('requires/location_wise_finish_stock_display_board_controller_v2', this.value, 'load_drop_down_location_lc', 'location_td' );load_drop_down( 'requires/location_wise_finish_stock_display_board_controller_v2',this.value, 'load_drop_down_buyer', 'buyer_td' );",0 );
                            ?>
                        <p>Location:</p>
                        <div id="location_td">
                            <? 
                            echo create_drop_down( "cbo_location", "", $blank_array,"",1, "-- Select Location --", "", "" );
                            ?>
                        </div>
                        <p>Store:</p>
                        <div id="store_td">
                            <? 
                            echo create_drop_down( "cbo_store_name", "", $blank_array,"",1, "-- Select Store --", "", "" );
                            ?>
                        </div>
                        <p>Floor:</p>
                        <div id="floor_td">
                            <? 
                            echo create_drop_down( "cbo_floor_id", "", $blank_array,"",1, "-- Select Floor --", "", "" );
                            ?>
                        </div>
                        <p>Room:</p>
                        <div id="room_td">
                            <? 
                            echo create_drop_down( "cbo_room_id", "", $blank_array,"",1, "-- Select Room --", "", "" );
                            ?>
                        </div>
                        <p>Rack:</p>
                        <div id="rack_td">
                            <? 
                            echo create_drop_down( "cbo_rack_id", "", $blank_array,"",1, "-- Select Rack --", "", "" );
                            ?>
                        </div>
                        <p>Shelf:</p>
                        <div id="shelf_td">
                            <?
                                echo create_drop_down( "cbo_shelf_id", "", $blank_array,"", 1, "--Select Shelf--", "", "" );
                            ?>
                        </div>
                        <p>Buyer:</p>
                        <div id="buyer_td">
                            <? 
                            echo create_drop_down( "cbo_buyer_name", "", $blank_array,"",1, "-- Select Buyer --", "", "" );
                            ?>
                        </div>    
                        <p>Date:</p>
                        <input type="text" class="datepicker" autocomplete="off" value="<?=date('d-m-Y');?>" name="txt_today_date" id="txt_today_date">
                        
                        <div type="button" id="export_btn"></div>
                        <span>Refresh Time(s) : <input type="number" name="refresh_time" id="refresh_time" value="5" style="width: 70px;"></span>
                        <button type="button" id="rpt_button" onclick="fn_report_generated()">Show By Selected</button>
                    </div>
                </div>
                <div style="padding: 0; margin:0;">
                    <div style="padding: 0; margin:0;"> 
                        <div style="font-size:24px;color:#FFFFFF;background:#000000;width:100%;z-index:-1;">
                            <div>
                                <div style="font-size:25px;color:#FFFFFF;text-align: center;">Knit Finish Fabric Stock</div>
                                <div style="text-align: center;">
                                    <span id="company_name" style="color: #ffffff;font-size:25px;">Company : ,</span>
                                    <span id="location_name" style="color: #ffffff;font-size:25px;">, Location : All Location</span>
                                    <span id="store_name" style="color: #ffffff;font-size:25px;">, Store : All Store</span>
                                    <span id="floor_name" style="color: #ffffff;font-size:25px;">, Floor : All Floor</span>
                                    <span id="room_name" style="color: #ffffff;font-size:25px;">, Room : All Room,</span>
                                    <span id="current_date" style="color: #ffffff;font-size:25px;"></span> 
                                </div>
                            </div>
                        </div>
                        <div id="rptContainer"></div>
                    </div>
                </div>
            </div>

        </div>
        
    </form>
</body>
<script>
    $(document).ready(function(){
                
        $('#searchPanel').css("right","-200px");
            $('.menu_icon').on('click',function(){
                if($('.menu_icon').hasClass('open'))
                {
                    $(this).removeClass('open');
                    $(this).text('Open');
                    $(this).animate({
                        "right":"0px",
                        "background-position":"0px"
                    });
                    $('#searchPanel').animate({"right":"-200px"});
                    // $('body').css("position","absolute");
                    $('body').animate({
                        "right":"0px",
                        "z-index":"999"
                    });
                }
                else
                {
                    $(this).addClass('open');
                    $(this).text('Close');
                    $(this).animate({
                        "right":"200px",
                        "background-position":"-40px"
                    });
                    $('#searchPanel').animate({"right":"0px"});
                    // $('body').css("position","absolute");
                    $('body').animate({
                        "right":"200px",
                        "z-index":"999"
                    });
                
                }
            });
            // ==============================
            // showClock();
            refreshTimer();
            
        });

        //get new date from timestamp in data-start attr
        // alert($("#currentTimer").attr("data-start"));
        var freshTime = new Date(parseInt($("#currentTimer").attr("data-start"))*1000);
        //loop to tick clock every second
        var displayClock = function showClock() {
            //set text of clock to show current time
            $("#currentTimer").text(freshTime.toLocaleTimeString());
            //add a second to freshtime var
            freshTime.setSeconds(freshTime.getSeconds() + 1);
            //wait for 1 second and go again
            setTimeout(showClock, 1000); 
            // ======================= set current date when day end ========================= 
            if(document.getElementById("currentTimer").innerHTML=='12:00:00 AM')
            {
                document.getElementById('txt_today_date').value = '<?=date('d-m-Y');?>';
            }
        };
        // displayClock();

        var ctoday = Date.parse('<?=date('D, d M Y H:i:s',time());?>');
        function loadServerTime() 
        {
            const xhttp = new XMLHttpRequest();
            xhttp.onload = function() {
                ctoday = Date.parse(this.responseText)*1;
            }
            xhttp.open("GET", "get_server_date.php");
            xhttp.send();
        }

        setInterval(function() {ctoday += 1000;},1000);        
        setInterval(function() {loadServerTime();},10000*60);

        /*function autoScrollWithinElement() {
          const container = document.getElementById('scroll_body'); // Replace with your specific container ID
          const scrollSpeed = 5; // Adjust this value as needed
          const scrollSpeedUp = 100;
          let scrollPosition = 0;
          let scrollingDown = true;

          function scroll() {
            if (scrollingDown) {
              scrollPosition += scrollSpeed;
              if (scrollPosition >= container.scrollHeight - container.clientHeight) {
                scrollingDown = false;
                callCustomFunction(); // Call your custom function when you reach the end
              }
            } else {
              scrollPosition -= scrollSpeedUp;
              if (scrollPosition <= 0) {
                scrollingDown = true;
              }
            }

            container.scrollTop = scrollPosition;
            setTimeout(scroll, 10); // Adjust the scroll speed and interval as needed
          }

          if (container.scrollHeight > container.clientHeight) {
            scroll(); // Start scrolling
          }
        }
        autoScrollWithinElement();*/

        /*function yourCustomFunction() {
            // Implement your custom function here
            // This function will be called when you reach the end of the page
            console.log("Reached the end of the page, now calling your custom function.");
            // You can replace the console.log with your specific function logic.
          }*/

        function startTime() {
            var today = new Date(ctoday);
            var montharray = new Array("Jan","Feb","Mar","Abr","May","Jun","Jul","Agu","Sep","Oct","Nov","Des");
            var h = today.getHours();
            var ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12;
            h = h ? h : 12;
            var m = today.getMinutes();
            var s = today.getSeconds();
            h = checkTimes(h);
            m = checkTimes(m);
            s = checkTimes(s);
            // document.getElementById('currentTimer').innerHTML = checkTimes(today.getDate())+" "+montharray[today.getMonth()]+" "+today.getFullYear() + " (" + ampm +" " + h + ":" + m + ":" + s +")"; 
            
            document.getElementById('currentTimer').innerHTML = h + ":" + m + ":" + s + " " + ampm;
            setTimeout(startTime, 1000);
        }
        // startTime();
        function checkTimes(i) {
            if (i < 10) {i = "0" + i};
            return i;
        }

        function refreshTimer() 
        {
            var date = new Date;
            var refresh_time = $("#refresh_time").val()*1000;
            // var date = new Date(Date.now() - refresh_time);
            var hours = date.getHours();
            var minutes = date.getMinutes();
            var seconds = date.getSeconds();
            // seconds = seconds - refresh_time;
            var ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? '0'+minutes : minutes;
            var strTime = hours + ':' + minutes + ':' + seconds + ' ' + ampm;
            // return strTime;
            // $("#refreshTimer").text(strTime);
            // $("#refreshTimer").text(freshTime.toLocaleTimeString());
            $("#refreshTimer").text(document.getElementById("currentTimer").innerHTML);
            // alert('ok');
        }
        refreshTimer();
        // $(".menu_icon").click();

</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
