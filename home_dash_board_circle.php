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
<script src="two.js"></script>
<style>

.ball_wrap {
    position: relative;
    margin: 150px;
    width: 90px;
}

.green_ball {
    background: #00C762;
    height: 80px;
    width: 80px;
    border-radius: 50%;
    border: 3px solid #ccc;
    position: absolute;
}

.blue_ball {
    background: #2F9BC1;
    height: 90px;
    width: 90px;
    border-radius: 50%;
    border: 3px solid #ccc;
}

.ball_wrap div:nth-of-type(2) {
    top: 20px;
    left: -130px;
}

.ball_wrap div:nth-of-type(2):after {
    content: "";
    display: block;
    border-bottom: 1px solid #000;
    position: absolute;
    width: 50px;
    right: -50px;
    top: 50%;
}

.ball_wrap div:nth-of-type(3) {
    top: 20px;
    right: -130px;
}

.ball_wrap div:nth-of-type(3):after {
    content: "";
    display: block;
    border-bottom: 1px solid #000;
    position: absolute;
    width: 50px;
    left: -52px;
    top: 50%;
}

.ball_wrap div:nth-of-type(4) {
    right: 0px;
    bottom: -120px;
}

.ball_wrap div:nth-of-type(4):after {
    content: "";
    display: block;
    border-left: 1px solid #000;
    position: absolute;
    height: 50px;
    left: 50%;
    top: -52px;
}
.ball_wrap div:nth-of-type(5) {
    right: 0px;
    bottom: -120px;
}

.ball_wrap div:nth-of-type(5):after {
    content: "";
    display: block;
    border-left: 1px solid #000;
    position: absolute;
    height: 50px;
    left: 80px;
    top: -50%;
}
</style>

<style type="text/css">
    #parentdiv
    {
        position: relative;
        width: 100px;
        height: 100px;
        background-color: #ac5;
        border-radius: 150px;
        margin: 210px auto;
		text-align:center;
		line-height:110px;
		/*background-image:url(md.jpg);
		background-size: 210px 210px;*/
		font-size:15px;
		color:#FFFBF0;
		border: .5em dotted #235EA4;
		
    }

    .div2
    {
        position: absolute;
        width: 80px;
        height: 80px;
        background-color: #ac5;
        border-radius: 80px;
		text-align:center;
		line-height:80px;
		
    }
	span {
		text-align:center;
		vertical-align:middle;
		height:50px;
		
	}
	img
	{
		 border-radius: 100px;
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
				echo create_drop_down( "cbo_company_home", 232, "select id,company_name from lib_company where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company--", $selected,"load_drop_down( 'today_production_graph', this.value, 'load_drop_down_location', 'sp_location' );" );
				
				?>&nbsp;Location Name:&nbsp;<span id="sp_location" ><? 
				echo create_drop_down( "cbo_location_home", 232, "select id,location_name from lib_location where status_active=1 and is_deleted=0  order by location_name","id,location_name", 1, "-- Select Location--", $selected );
            
            ?> </span>&nbsp;&nbsp;<input type="button" name="savem" value="Save Sequence" class="formbutton" onclick="fnc_save_sequence()" /> 
        </td>
        </tr>
        <tr>
        	<td width="" valign="top" align="center" class="sortable" id="containment-wrapper<? echo $k; ?>" style="border:1px dotted #CCC">
            
                 
                
           
			
           
			
<div class="ball_wrap_test" id="parentdiv">PLATFORM</div>
            
                
             <script type="text/javascript">
			 var ccounts=Array();
			<?
            foreach( $priv_items as $k=>$val )
            {
				echo "var ccount='".count($val)."';";
				//echo "var ctext='".count($val)."';";
				$t=0;
				foreach( $val as $j=>$dat )
				{
					$t++;
					 
					 echo "ccounts[$t]='".$home_page_array[$k][$j]['lnk']."';";
					 
				}
				
			}
            ?>
            
				var div = 180 / ccount;
				var radius = 230;
				var parentdiv = document.getElementById('parentdiv');
				var offsetToParentCenter = parseInt(parentdiv.offsetWidth / 2);  //assumes parent is square
				var offsetToChildCenter = 50;
				var totalOffset = offsetToParentCenter - offsetToChildCenter;
				for (var i = 1; i <= ccount; ++i)
				{
					var childdiv = document.createElement('div');
					childdiv.className = 'div2';
					childdiv.style.position = 'absolute';
					childdiv.innerHTML  = '<span>'+ccounts[i]+'</span>';
					var y = Math.sin((div * i) * (Math.PI / 210)) * radius;
					var x = Math.cos((div * i) * (Math.PI / 210)) * radius;
					childdiv.style.right = (y + totalOffset).toString() + "px";
					childdiv.style.top = (x + totalOffset).toString() + "px";
					parentdiv.appendChild(childdiv);
				}
			</script>    
        </td>
        </tr>
</table>
</div>
<script>
$(function() {
	 
	
//	$( ".sortable" ).sortable(revert: true);
});

</script>