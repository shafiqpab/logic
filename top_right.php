
<? 
$ptype=1;
if( $ptype==1)
{  $photoPath=($_SESSION['logic_erp']['photo'])?$_SESSION['logic_erp']['photo']:'img/userprofile.png';
	?>
<table width="100%" height="100%" border="0">
    <tr>
        <td>
          <div class="n4-dropdown-click "  id="sidebarButton">
            <span class="notification">
            <img class="n4-black " src="<? echo $photoPath;?>" width="35" height="35" style="border-radius:18px; border:1px solid #CCC;" />
            <span class="badge">0</span>
            </span>
             
            <div id="notification_list" class="n4-dropdown-content n4-bar-block n4-animate-zoom">
             <!-- <a href="javascript:search_menu_popup()" class="n4-bar-item n4-hover-shadow">Search page location</a>
			  <a href="javascript:daily_task_popup()" class="n4-bar-item n4-hover-shadow">Daily Task</a> -->
            </div>
          </div>
          
         </td>
        <td valign="middle" align="center">
           <b>Login By:</b><br /><small><? echo $_SESSION['logic_erp']['user_name']; ?></small>
        </td>
        <td width="95" valign="middle" align="center" style="border-left:1px; border-left-style:solid;">
            <a href="logout.php" style="text-decoration:none">
            <img src="images/logic/Logout.png" width="90" height="30" /></a>
        </td>
        <td width="220" align="right" valign="middle" style="border-left:1px; border-left-style:solid; padding-left:5px;">
        <img src="images/logic/Platfrom.png" id="logininfo" width="220" height="30" />
        </td>
    </tr>
</table>


<style>
.n4-dropdown-click:hover .n4-dropdown-content {
  display: block; cursor:pointer;border:1px solid #9EBDE5; border-radius:5px;
  background-image: -webkit-linear-gradient(bottom, rgb(136,170,214) 0%, rgb(194,220,255) 0%, rgb(136,170,214) 100%);
}
 .n4-dropdown-content{cursor:auto;color:#000;background-color:#fff;display:none;position:absolute;min-width:160px;margin:0;padding:0;z-index:1}
.n4-bar-item{display:block;text-align:left;font-size:14px; border-bottom:1px solid #DDD; text-decoration:none; padding:5px; color:#000;}

.n4-hover-shadow:hover{ color:#00F;}
.n4-hover-shadow i{ color:#D00;}

.n4-animate-zoom {animation:animatezoom 0.6s}
@keyframes animatezoom{from{transform:scale(0)} to{transform:scale(1)}}



.notification {
  position: relative;
  display: inline-block;
}

.notification .badge {
  position: absolute;
  top: -1px;
  right: -1px;
  padding: 1px 5px;
  border-radius: 50%;
  background-color: red;
  color: white;
  display:none;
}
#notification_list{max-height:80vh; overflow-y:scroll;}

</style>

<script>
function search_menu_popup(){
	page_link='search_menu.php?action=search';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Detail View', 'width=800px, height=400px, center=1, resize=0, scrolling=0','home');
	emailwindow.onclose=function()
	{
		var menu_info=this.contentDoc.getElementById("txt_menu_info").value; //alert(menu_info);
		var dataArr=menu_info.split('**');
		var dataStr="'"+dataArr.join("','")+"'";
		//localStorage['visited'] = menu_info;
		
		window.location.href = window.location.origin+"/platform_v3.5/index.php?module_id="+dataArr[5];
		//callurl.load(dataArr[0],dataArr[1],dataArr[2],'','');
	}



}
	/*$(document).ready(function() {
		var dataArr = localStorage['visited'];
		if (dataArr) { alert(dataArr);
			var dataStr="'"+dataArr.join("','")+"'";
			callurl.load(dataArr[0],dataArr[1],dataArr[2],'','');
		}
	});
*/

function daily_task_popup(){
	page_link='daily_task.php?action=daily_task_entry';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Detail View', 'width=800px, height=400px, center=1, resize=0, scrolling=0','home');
	emailwindow.onclose=function()
	{
		var menu_info=this.contentDoc.getElementById("txt_menu_info").value; //alert(menu_info);
		var dataArr=menu_info.split('**');
		var dataStr="'"+dataArr.join("','")+"'";
		//localStorage['visited'] = menu_info;
		
		window.location.href = window.location.origin+"/platform_v3.5/index.php?module_id="+dataArr[5];
		//callurl.load(dataArr[0],dataArr[1],dataArr[2],'','');
	}



}


/*
$.ajax({
  method: "POST",
  url: "user_notification/notification.php",
  data: { action: "app_notification", location: "Boston" }
})
  .done(function( msg ) {
	
	var totalNotification=0;

	$('.notification .badge').show();
	
	var msgDataArr=msg.split('__');
	for(var i=0;i<msgDataArr.length;i++){
		var msgArr=msgDataArr[i].split('*');
		
		if(msgArr[0]==428 && msgArr[1]>0){
			$("#notification_list").append('<a href="javascript:callurl.load('+ "'main_body', 'page_container.php?m=approval/pre_costing_approval.php?permission=1_1_1_1&amp;mid=428&amp;fnat=Pre-Costing Approval__0', false, '', '' "+')" class="n4-bar-item n4-hover-shadow">Pre-Costing Approval (<i>'+msgArr[1]+'</i>)</a>');
		}
		else if(msgArr[0]==427 && msgArr[1]>0){		
			$("#notification_list").append('<a href="javascript:callurl.load('+ "'main_body', 'page_container.php?m=approval/price_quatation_approval.php?permission=1_1_1_1&amp;mid=427&amp;fnat=Price Quotation Approval__0', false, '', '' "+')" class="n4-bar-item n4-hover-shadow">Price Quotation Approval (<i>'+msgArr[1]+'</i>)</a>');
		}
		else if(msgArr[0]==410 && msgArr[1]>0){		
			$("#notification_list").append('<a href="javascript:callurl.load('+ "'main_body', 'page_container.php?m=approval/fabric_booking_approval.php?permission=1_1_1_1&amp;mid=410&amp;fnat=Fabric Booking Approval New__2', false, '', '' "+')" class="n4-bar-item n4-hover-shadow">Fabric Booking Approval New (<i>'+msgArr[1]+'</i>)</a>');
		}
		else if(msgArr[0]==820 && msgArr[1]>0){		
			$("#notification_list").append('<a href="javascript:callurl.load('+ "'main_body', 'page_container.php?m=approval/pi_approval.php?permission=1_1_1_1&mid=820&fnat=PI Approval__0', false, '', '' "+')" class="n4-bar-item n4-hover-shadow">PI Approval (<i>'+msgArr[1]+'</i>)</a>');
		}
		else if(msgArr[0]==670 && msgArr[1]>0){		
			$("#notification_list").append('<a href="javascript:callurl.load('+ "'main_body', 'page_container.php?m=approval/gate_pass_activation_entry.php?permission=1_1_1_1&mid=670&fnat=Gate Pass Activation Approval__0', false, '', '' "+')" class="n4-bar-item n4-hover-shadow">Gate Pass Activation Approval (<i>'+msgArr[1]+'</i>)</a>');
		}
		else if(msgArr[0]==674 && msgArr[1]>0){		
			$("#notification_list").append('<a href="javascript:callurl.load('+ "'main_body', 'page_container.php?m=approval/yarn_requisition_approval.php?permission=1_1_1_1&mid=674&fnat=Yarn Requisition Approval__0', false, '', '' "+')" class="n4-bar-item n4-hover-shadow">Yarn Requisition Approval(<i>'+msgArr[1]+'</i>)</a>');
		}
		else if(msgArr[0]==479 && msgArr[1]>0){		
			$("#notification_list").append('<a href="javascript:callurl.load('+ "'main_body', 'page_container.php?m=approval/yarn_delivery_approval.php?permission=1_1_1_1&mid=479&fnat=Yarn Delivery Approval__0', false, '', '' "+')" class="n4-bar-item n4-hover-shadow">Yarn Delivery Approval(<i>'+msgArr[1]+'</i>)</a>');
		}
		else if(msgArr[0]==902 && msgArr[1]>0){		
			$("#notification_list").append('<a href="javascript:callurl.load('+ "'main_body', 'page_container.php?m=approval/fabric_sales_order_approval.php?permission=1_1_1_1&mid=902&fnat=Fabric Sales Order Approval__0', false, '', '' "+')" class="n4-bar-item n4-hover-shadow">Fabric Sales Order Approval (<i>'+msgArr[1]+'</i>)</a>');
		}
		else if(msgArr[0]==627 && msgArr[1]>0){		
			$("#notification_list").append('<a href="javascript:callurl.load('+ "'main_body', 'page_container.php?m=approval/stationary_work_order_approval.php?permission=1_1_1_1&mid=627&fnat=Stationary Work Order Approval__0', false, '', '' "+')" class="n4-bar-item n4-hover-shadow">Stationary Work Order Approval (<i>'+msgArr[1]+'</i>)</a>');
		}
		else if(msgArr[0]==628 && msgArr[1]>0){		
			$("#notification_list").append('<a href="javascript:callurl.load('+ "'main_body', 'page_container.php?m=approval/other_purchase_work_order_approval.php?permission=1_1_1_1&mid=628&fnat=Other Purchase WO Approval__0', false, '', '' "+')" class="n4-bar-item n4-hover-shadow">Other Purchase WO Approval (<i>'+msgArr[1]+'</i>)</a>');
		}
		else if(msgArr[0]==616 && msgArr[1]>0){		
			$("#notification_list").append('<a href="javascript:callurl.load('+ "'main_body', 'page_container.php?m=approval/dyeing_batch_approval.php?permission=1_1_1_1&mid=616&fnat=Dyeing Batch Approval__0', false, '', '' "+')" class="n4-bar-item n4-hover-shadow">Dyeing Batch Approval(<i>'+msgArr[1]+'</i>)</a>');
		}
		else if(msgArr[0]==813 && msgArr[1]>0){		
			$("#notification_list").append('<a href="javascript:callurl.load('+ "'main_body', 'page_container.php?m=approval/purchase_requisition_approval.php?permission=1_1_1_1&mid=813&fnat=Purchase Requisition Approval__0', false, '', '' "+')" class="n4-bar-item n4-hover-shadow">Purchase Requisition Approval(<i>'+msgArr[1]+'</i>)</a>');
		}
		else if(msgArr[0]==626 && msgArr[1]>0){		
			$("#notification_list").append('<a href="javascript:callurl.load('+ "'main_body', 'page_container.php?m=approval/dyes_chemical_wo_approval.php?permission=1_1_1_1&mid=626&fnat=Dyes N Chemical WO Approval__0', false, '', '' "+')" class="n4-bar-item n4-hover-shadow">Dyes N Chemical WO Approval(<i>'+msgArr[1]+'</i>)</a>');
		}
		else if(msgArr[0]==414 && msgArr[1]>0){		
			$("#notification_list").append('<a href="javascript:callurl.load('+ "'main_body', 'page_container.php?m=approval/sample_feb_booking_wo_approval.php?permission=1_1_1_1&mid=414&fnat=Sample Fabric Booking Aproval-With order__0', false, '', '' "+')" class="n4-bar-item n4-hover-shadow">Sample Fabric Booking Aproval-With order(<i>'+msgArr[1]+'</i>)</a>');
		}
		else if(msgArr[0]==411 && msgArr[1]>0){		
			$("#notification_list").append('<a href="javascript:callurl.load('+ "'main_body', 'page_container.php?m=approval/non_order_sample_booking_approval.php?permission=1_1_1_1&mid=411&fnat=Sample Booking [Without Order] Approval New__0', false, '', '' "+')" class="n4-bar-item n4-hover-shadow">Sample Booking [Without Order] Approval New(<i>'+msgArr[1]+'</i>)</a>');
		}
		
		
		else if(msgArr[0]==336 && msgArr[1]>0){		
			$("#notification_list").append('<a href="javascript:callurl.load('+ "'main_body', 'page_container.php?m=approval/trims_booking_approval.php?permission=1_1_1_1&mid=336&fnat=Trims Booking Approval__0', false, '', '' "+')" class="n4-bar-item n4-hover-shadow">Trims Booking Approval[With Order](<i>'+msgArr[1]+'</i>)</a>');
		}
		else if(msgArr[0]==413 && msgArr[1]>0){		
			$("#notification_list").append('<a href="javascript:callurl.load('+ "'main_body', 'page_container.php?m=approval/short_feb_booking_approval.php?permission=1_1_1_1&mid=413&fnat=Short Fabric Booking Approval New__0', false, '', '' "+')" class="n4-bar-item n4-hover-shadow">Short Fabric Booking Approval New(<i>'+msgArr[1]+'</i>)</a>');
		}
		
		
		else if(msgArr[0]==869 && msgArr[1]>0){		
			$("#notification_list").append('<a href="javascript:callurl.load('+ "'main_body', 'page_container.php?m=approval/pre_costing_approval_new.php?permission=1_1_1_1&mid=869&fnat=New Pre Costing Approval__0', false, '', '' "+')" class="n4-bar-item n4-hover-shadow">New Pre Costing Approval(<i>'+msgArr[1]+'</i>)</a>');
		}
		
		
		
		
		
		
		
		totalNotification+=msgArr[1]*1;
		
	
	}//for loof
	
	$('.notification .badge').text(totalNotification);
	
  });


*/


</script>

<!-- start Socket io.......................................................................... -->
<!--
<script src="http://192.168.10.230:4000/socket.io/socket.io.js"></script>
 
<script>
    const socket = io("http://192.168.10.230:4000", { transports: ['websocket']});


    socket.on("broadcast", function(results) {
        $("#notification_list").append('<a href="javascript:daily_task_popup()" class="n4-bar-item n4-hover-shadow">'+results+'</a>');
    }); 


    fetch('http://192.168.10.230:4000/user')
        .then(response => {
                if (response.ok) {
                    console.log('success')
                    console.log(response);
                } else {
                    console.log('failure')
                }
                return response.json();
            })
            .then(function(data) {

                  let data_arr=[];
                    data.forEach(function(rows) {
                       // data_arr.push(rows.NAME);
                        $("#notification_list").append('<a href="#" class="n4-bar-item n4-hover-shadow">'+rows.NAME+'</a>');
                    });
                   // let dataStr=data_arr.join();
                    //socket.emit("broadcast", dataStr);

            });

           

</script>-->
<!-- ..........................................................................Socket io end; -->


<?
} 
else  if( $ptype==2)
{
?>
<style>

	body
	{ margin:0pc; }
    .top_right_ul {
        padding: 0;
        list-style: none;
        background-image: -moz-linear-gradient(bottom, rgb(255,255,255)  7%, rgb(136,170,214) 10%, rgb(255,240,255) 96%);
        float: right;
		height:40px; 
    }
    .top_right_ul  li{
        display: inline-block;
        position: relative;
        line-height: 21px;
        text-align: left;
        border-bottom: 0px solid gray;
    }
    .top_right_ul li a{
        display: block;
        padding: 3px 15px;
        color: #333;
        text-decoration: none;
    }
	.top_right_ul li a:hover{
        display: block;
        padding: 3px 15px;
        color: #333;
        text-decoration: none;
		 height:40px; 
		background-image: -moz-linear-gradient(bottom, rgb(255,255,255)  7%, rgb(136,170,214) 10%, rgb(255,255,255) 96%);
    }
	
    .top_right_ul li a:hover{
        color: #759389;
       
    }
    .top_right_ul li ul.dropdown{
        min-width: 125px; /* Set width of the dropdown */
        background: #f2f2f2;
        display: none;
        position: absolute;
        z-index: 999;
        left: 0;
    }
    .top_right_ul li:hover ul.dropdown{
        display: block;	/* Display the dropdown */
    }
    .top_right_ul li ul.dropdown li{
        display: block;
    }
    .round{
            border-radius: 100%;
            border:2px solid #688A7E;
            float: left;
            overflow: hidden;
            margin-top: -20px;
            margin-bottom: 5px;
               
    }
      .roundcount{
            border-radius: 100%;
          
            float: left;
            overflow: hidden;
            margin-top: -5px;
            margin-bottom: 20px;
            margin-right: -10px;
            color: #fff;
            background-color: #00A0DF;
            z-index: 1;
            position:relative;
            padding: 10px;
            font-size: 10px;
            
               
    }
 
 </style>
<ul class="top_right_ul">
        <li>
        <a href="#"><img src="images/pending.png"><div class="roundcount">12</div> &#9662;</a>
            <ul class="dropdown">
                <h4 style="text-align:center;color:#fff;background-color:#759389">Pending letter</h4>
                <li><a href="#"><h5 style="background-color:red;color:#fff;width:100%;">100%</h5></a></li>
               <li><a href="#"><h5 style="background-color:blue;color:#fff;width:50%;">50%</h5></a></li>
                <li><a href="#"><h5 style="background-color:green;color:#fff;width:30%;">30%</h5></a></li>
                 <li><a href="#"><h5 style="background-color:gray;color:#fff;width:90%;">90%</h5></a></li>
                  <li><a href="#"><h5 style="background-color:black;color:#fff;width:80%;">80%</h5></a></li>
            </ul>
        </li>
        <li>
            <a href="#"><img src="images/message.png" ><div class="roundcount">12</div> &#9662;</a>
            <ul class="dropdown">
                <h4 style="text-align:center;color:#fff;background-color:#759389">Messages</h4>
                <li><a href="#">Sumon sir</a></li>
                <li><a href="#">Monzu vai</a></li>
                <li><a href="#">Fuad vai</a></li>
                <li><a href="#">Monir vai</a></li>
            </ul>
        </li>
        <li>
            <a href="#"><img src="images/privacy.png" width="35" height="35"> &#9662;</a>
            <ul class="dropdown">
                <h4 style="text-align:center;color:#fff;background-color:#759389">Profile Privacy</h4>
                <li><a href="#">Change Password</a></li>
                <li><a href="#">Profile Edit</a></li>
                <li><a href="#">Security Settings</a></li>
                <li></li>
                <li><a href="logout.php">LOGOUT</a></li>
            </ul>
        </li>
        <!--<li>
            <a href="#"><div class="round"><img style="width:40px;height:auto;" style="float:left;" src="images/mehedi.jpg"></div> <b style="color:#FFF;margin-left:5px;"> kaiyum</b> &#9662;</a>
            
            <ul class="dropdown">

                <h4 style="text-align:center;color:#fff;background-color:#759389">User history</h4>
                <li><a href="#">My Profile</a></li>
                <li><a href="#">My Inbox</a></li>
                <li><a href="#">Timeline</a></li>
                <li><a href="#">Chat</a></li>
                <li><a href="#">Log Out</a></li>
                <li><a href="#">Documentation</a></li>

            </ul>
        </li>-->
    </ul>
<? } ?>