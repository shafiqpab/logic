<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Dash Board.
Functionality	:	
JS Functions	:
Created by		:	CTO 
Creation date 	: 	
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
require_once('includes/common.php');
 
//--------------------------------------------------------------------------------------------------------------------

 
?>
<script src="js/jquery.js"></script>

<style>

.container {
	position:relative;
	width:150px;
	height:55px;
	 
	border-radius:8px;
	background-color: rgba(155,85,232,0.75);
	 
}

.container .textbox {
	width:100%;
	height:100%;
	position:absolute;
	top:0;
	left:0;
	-webkit-transform: scale(0);
	transform: scale(0);
	border-radius:8px;
	background-color: rgba(100,0,100,0.75);
	-webkit-box-shadow: 0px 0px 15px 2px rgba(255,255,255,.75);
	box-shadow: 0px 0px 15px 2px rgba(255,255,255,.75);
	color:#FFF;
	cursor:pointer;
}

.container:hover .textbox {
	-webkit-transform: scale(1);
	transform: scale(1);
}

.text {
	padding-top: 00px;
}

.textbox {
	-webkit-transition: all 0.7s ease;
	transition: all 0.7s ease;
}

p
{
	width:100%;
	display:block;
	background-color:#009966;
	color:#FFF;
}

/*
.read_more
{
	margin-top:0px;
	display:block;
	background-color:#3300FF;
	color:#FFF;
	width:100px;
	height:20px;
}
.read_more a
{
	text-decoration:none;
	color:#FFF;
}

*/


.view {
   width:200px;
   height: 230px;
   margin: 10px;
   float: left;
   border: 10px solid #fff;
   overflow: hidden;
   position: relative;
   text-align: center;
   -webkit-box-shadow: 1px 1px 2px #e6e6e6;
   -moz-box-shadow: 1px 1px 2px #e6e6e6;
   box-shadow: 1px 1px 2px #e6e6e6;
   cursor: default;
   background: #fff url(../images/bgimg.jpg) no-repeat center center;
}
.view .mask,.view .content {
   width:200px;
   height: 230px;
   position: absolute;
   overflow: hidden;
   top: 0;
   left: 0;
}
.view img {
   display: block;
   position: relative;
}
.view h2 {
   text-transform: uppercase;
   color: #fff;
   text-align: center;
   position: relative;
   font-size: 17px;
   padding: 10px;
   background: rgba(0, 0, 0, 0.8);
   margin: 20px 0 0 0;
}
.view p {
   font-family: Georgia, serif;
   font-style: italic;
   font-size: 12px;
   position: relative;
   color: #fff;
   padding: 10px 20px 20px;
   text-align: center;
}
.view a.info {
   display: inline-block;
   text-decoration: none;
   padding: 7px 14px;
   background: #000;
   color: #fff;
   text-transform: uppercase;
   -webkit-box-shadow: 0 0 1px #000;
   -moz-box-shadow: 0 0 1px #000;
   box-shadow: 0 0 1px #000;
}

.view a.info: hover {
   -webkit-box-shadow: 0 0 5px #000;
   -moz-box-shadow: 0 0 5px #000;
   box-shadow: 0 0 5px #000;
}

.view-seventh img {
   -webkit-transition: all 0.5s ease-out;
   -moz-transition: all 0.5s ease-out;
   -o-transition: all 0.5s ease-out;
   -ms-transition: all 0.5s ease-out;
   transition: all 0.5s ease-out;
   -ms-filter: "progid: DXImageTransform.Microsoft.Alpha(Opacity=100)";
   filter: alpha(opacity=100);
   opacity: 1;
}
.view-seventh .mask {
   background-color: rgba(77,44,35,0.5);
   -webkit-transform: rotate(0deg) scale(1);
   -moz-transform: rotate(0deg) scale(1);
   -o-transform: rotate(0deg) scale(1);
   -ms-transform: rotate(0deg) scale(1);
   transform: rotate(0deg) scale(1);
   -ms-filter: "progid: DXImageTransform.Microsoft.Alpha(Opacity=0)";
   filter: alpha(opacity=0);
   opacity: 0;
   -webkit-transition: all 0.3s ease-out;
   -moz-transition: all 0.3s ease-out;
   -o-transition: all 0.3s ease-out;
   -ms-transition: all 0.3s ease-out;
   transition: all 0.3s ease-out;
}
.view-seventh h2 {
   -webkit-transform: translateY(-200px);
   -moz-transform: translateY(-200px);
   -o-transform: translateY(-200px);
   -ms-transform: translateY(-200px);
   transform: translateY(-200px);
   -webkit-transition: all 0.2s ease-in-out;
   -moz-transition: all 0.2s ease-in-out;
   -o-transition: all 0.2s ease-in-out;
   -ms-transition: all 0.2s ease-in-out;
   transition: all 0.2s ease-in-out;
}
.view-seventh p {
   -webkit-transform: translateY(-200px);
   -moz-transform: translateY(-200px);
   -o-transform: translateY(-200px);
   -ms-transform: translateY(-200px);
   transform: translateY(-200px);
   -webkit-transition: all 0.2s ease-in-out;
   -moz-transition: all 0.2s ease-in-out;
   -o-transition: all 0.2s ease-in-out;
   -ms-transition: all 0.2s ease-in-out;
   transition: all 0.2s ease-in-out;
}
.view-seventh a.info {
   -webkit-transform: translateY(-200px);
   -moz-transform: translateY(-200px);
   -o-transform: translateY(-200px);
   -ms-transform: translateY(-200px);
   transform: translateY(-200px);
   -webkit-transition: all 0.2s ease-in-out;
   -moz-transition: all 0.2s ease-in-out;
   -o-transition: all 0.2s ease-in-out;
   -ms-transition: all 0.2s ease-in-out;
   transition: all 0.2s ease-in-out;
}
.view-seventh:hover img {
   -webkit-transform: rotate(720deg) scale(0);
   -moz-transform: rotate(720deg) scale(0);
   -o-transform: rotate(720deg) scale(0);
   -ms-transform: rotate(720deg) scale(0);
   transform: rotate(720deg) scale(0);
   -ms-filter: "progid: DXImageTransform.Microsoft.Alpha(Opacity=0)";
   filter: alpha(opacity=0);
   opacity: 0;
}
.view-seventh:hover .mask {
   -ms-filter: "progid: DXImageTransform.Microsoft.Alpha(Opacity=100)";
   filter: alpha(opacity=100);
   opacity: 1;
   -webkit-transform: translateY(0px) rotate(0deg);
   -moz-transform: translateY(0px) rotate(0deg);
   -o-transform: translateY(0px) rotate(0deg);
   -ms-transform: translateY(0px) rotate(0deg);
   transform: translateY(0px) rotate(0deg);
   -webkit-transition-delay: 0.4s;
   -moz-transition-delay: 0.4s;
   -o-transition-delay: 0.4s;
   -ms-transition-delay: 0.4s;
   transition-delay: 0.4s;
}
.view-seventh:hover h2 {
   -webkit-transform: translateY(0px);
   -moz-transform: translateY(0px);
   -o-transform: translateY(0px);
   -ms-transform: translateY(0px);
   transform: translateY(0px);
   -webkit-transition-delay: 0.7s;
   -moz-transition-delay: 0.7s;
   -o-transition-delay: 0.7s;
   -ms-transition-delay: 0.7s;
   transition-delay: 0.7s;
}
.view-seventh:hover p {
   -webkit-transform: translateY(0px);
   -moz-transform: translateY(0px);
   -o-transform: translateY(0px);
   -ms-transform: translateY(0px);
   transform: translateY(0px);
   -webkit-transition-delay: 0.6s;
   -moz-transition-delay: 0.6s;
   -o-transition-delay: 0.6s;
   -ms-transition-delay: 0.6s;
   transition-delay: 0.6s;
}
.view-seventh:hover a.info {
   -webkit-transform: translateY(0px);
   -moz-transform: translateY(0px);
   -o-transform: translateY(0px);
   -ms-transform: translateY(0px);
   transform: translateY(0px);
   -webkit-transition-delay: 0.5s;
   -moz-transition-delay: 0.5s;
   -o-transition-delay: 0.5s;
   -ms-transition-delay: 0.5s;
   transition-delay: 0.5s;
}

</style>
<script>
<?

$home_page_arr= json_encode( $home_page_array );
echo "var home_pages = ". $home_page_arr . ";\n";

?>

function show_data ( lnk, lid  )
{
	//alert(lnk+"="+lid);
	var comp=$('#cbo_company_home').val();
	var locat=$('#cbo_location_home').val();
	
	if( lid == 1 ) //Static Graph design
	{
		//alert(lnk)	
		if( lnk == 'VG9kYXlfSG91cmx5X1Byb2R1Y3Rpb24=')//VG9kYXlfSG91cmx5X1Byb2R1Y3Rpb24= //Today_Hourly_Production' )
			window.open('today_production_graph.php?m='+lnk+'&cp='+comp+"__"+locat, "MY PAGE");
		else if( lnk == 'b3JkZXJfaW5faGFuZF9xbnR5')//b3JkZXJfaW5faGFuZF9xbnR5 //order_in_hand_qnty
			window.open('show_graph.php?m='+lnk+'&cp='+comp, "MY PAGE");
		else if( lnk == 'b3JkZXJfaW5faGFuZF92YWw')//b3JkZXJfaW5faGFuZF92YWw //order_in_hand_val
			window.open('show_graph.php?m='+lnk+'&cp='+comp, "MY PAGE");
		else if( lnk == 'c3RhY2tfcW50eQ==')//c3RhY2tfcW50eQ== //stack_qnty
			window.open('show_graph.php?m='+lnk+'&cp='+comp, "MY PAGE");
		else if( lnk == 'c3RhY2tfdmFsdWU=')//c3RhY2tfdmFsdWU= //stack_value
			window.open('show_graph.php?m='+lnk+'&cp='+comp, "MY PAGE");
		else if( lnk == 'Y29tcGFueV9rcGk=')//Y29tcGFueV9rcGk= //company_kpi
			window.open('dash_board.php?m='+lnk+'&cp='+comp, "MY PAGE");
		else
			window.open('show_graph.php?m='+lnk+'&cp='+comp, "MY PAGE");
			
		return;
	}
	else
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+lnk+'</body</html>');
		d.close(); 
	}
}

function fnc_save_sequence()
{
	$( ".sortable .container" ).each(function() {
		alert($(this).attr('dataval'))
	});
	
}

$(function() {
	$.each( home_pages, function( key, value ) {
	 // $( ".div"+key ).draggable({ containment: "#containment-wrapper"+key, scroll: false });
	});
	
	$( ".sortable" ).sortable();
});
</script>
<div style="width:100%" align="center">
<?
//echo $_SESSION['logic_erp']['user_id'];
/*
$con = connect();
foreach( $home_page_array as $d=>$t )
{
	$p=0;
	foreach($t as $k=>$g)
	{
		$p++;
		$v++;
		if($data_array_dtls!='') $data_array_dtls .=",";
		$data_array_dtls .="(".$v.",".$d.",".$k.",1,".$p.")";
	}
}
$field_array_dtls="id,module_id,item_id,user_id,sequence_no";
	$rID=sql_insert("HOME_PAGE_PRIVILEDGE",$field_array_dtls,$data_array_dtls,1);
	oci_commit($con); */
 
	$sql=sql_select("select id,module_id,item_id,user_id,sequence_no from HOME_PAGE_PRIVILEDGE where USER_ID='".$_SESSION['logic_erp']['user_id']."' order by module_id,sequence_no");
	foreach( $sql as $rows )
	{
		$priv_items[$rows[csf("module_id")]][$rows[csf("item_id")]]['seq']=$rows[csf("sequence_no")];
	}
?>
<table width="80%" cellpadding="5" cellspacing="5" border="1">
        <tr>
        <td class="ui-state-default" colspan="<? echo count( $home_page_array ); ?>" align="center">Company Name &nbsp;&nbsp;
			<? 
				echo create_drop_down( "cbo_company_home", 232, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company--", $selected,"load_drop_down( 'today_production_graph', this.value, 'load_drop_down_location', 'sp_location' );" );
				
				?>&nbsp;Location Name:&nbsp;<span id="sp_location" ><? 
				echo create_drop_down( "cbo_location_home", 232, "select id,location_name from lib_location where status_active=1 and is_deleted=0  order by location_name","id,location_name", 1, "-- Select Location--", $selected );
            
            ?> </span>&nbsp;&nbsp;<input type="button" name="savem" value="Save Sequence" class="formbutton" onclick="fnc_save_sequence()" /> 
        </td>
        </tr>
        <tr>
        		<? 
				foreach( $priv_items as $k=>$val )
				{
						?>
                        <td width="" valign="top" align="center" class="sortable" id="containment-wrapper<? echo $k; ?>" style="border:1px dotted #CCC">
                        <?
                            echo "<p>".$home_page_module[$k]."<p/>";
                            foreach( $val as $j=>$dat )
                            {
								?>
								<div style="border:0px solid #F00; font-size: 14px; vertical-align:middle; margin:10px 5px 10px 5px;" dataval="<? echo $k."-".$j; ?>" class="container div<? echo $k;?>">
								<!-- <img src="images/logic_logo.png" height="70"  width="100" alt=""> --><br />
								<? //echo $home_page_array[$k][$j]['lnk']."sssssssss";//echo $j.$home_page_array[$k][$j]['name']."=".$dat['seq'];
								echo $home_page_array[$k][$j]['name']; ?>
								<div class="textbox" onclick="show_data( '<? echo base64_encode($home_page_array[$k][$j]['lnk']); ?>',<? echo $k; ?>)">
								<? echo $home_page_array[$k][$j]['name']; ?>
								<? //echo $dat['lnk']; ?>
								</div>
								</div>
								<?
							}
                        ?>
                        </td>
            			<? 
				}
				?>
        </tr>
</table>
</div>
<script>
$(function() {
	 
	
//	$( ".sortable" ).sortable(revert: true);
});

</script>