<style>

    ul{
        padding: 0;
        list-style: none;
        background-image: -moz-linear-gradient(bottom, rgb(136,170,214) 7%, rgb(194,220,255) 10%, rgb(136,170,214) 96%);
        float: right;
    }
    ul li{
        display: inline-block;
        position: relative;
        line-height: 21px;
        text-align: left;
        border-bottom: 1px solid gray;
    }
    ul li a{
        display: block;
        padding: 8px 25px;
        color: #333;
        text-decoration: none;
    }
    ul li a:hover{
        color: #759389;
       
    }
    ul li ul.dropdown{
        min-width: 125px; /* Set width of the dropdown */
        background: #f2f2f2;
        display: none;
        position: absolute;
        z-index: 999;
        left: 0;
    }
    ul li:hover ul.dropdown{
        display: block;	/* Display the dropdown */
    }
    ul li ul.dropdown li{
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
 
 
 </style>

<ul>
        <li>
        <a href="#"><img src="images/pending.png"> &#9662;</a>
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
            <a href="#"><img src="images/message.png"> &#9662;</a>
            <ul class="dropdown">
                <h4 style="text-align:center;color:#fff;background-color:#759389">Messages</h4>
                <li><a href="#">Sumon sir</a></li>
                <li><a href="#">Monzu vai</a></li>
                <li><a href="#">Fuyad vai</a></li>
                <li><a href="#">Monir vai</a></li>
            </ul>
        </li>
        <li>
            <a href="#"><img src="images/notification.png"> &#9662;</a>
            <ul class="dropdown">
                <h4 style="text-align:center;color:#fff;background-color:#759389">Notifications</h4>
                <li><a href="#">Location of Monzu vai</a></li>
                <li><a href="#">Project Complete</a></li>
                <li><a href="#">Firedns Request</a></li>
            </ul>
        </li>
        <li>
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
        </li>
    </ul>