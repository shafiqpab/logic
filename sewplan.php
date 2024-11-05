<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Order Info","", 1, 1, $unicode,1,'');
//load_html_head_contents($title, $path, $filter, $popup, $unicode, $multi_select, $am_chart)

$line_array=return_library_array("select id,line_name from lib_sewing_line where id<30 order by id ", "id","line_name",1);

?>	

<style>

.topsss_header
{
	font-size:10px;
	width:30px;
	background-color:#CCC;
	color:#FF0000;
	text-align:center;
}
.left_header
{
	font-size:10px;
	width:120px;
	background-color:#CCC;
	color:#FF0000;
	text-align:center;
}


.verticalStripes1 {	
	cursor:pointer;
	-webkit-touch-callout: none;
	-webkit-user-select: none;
	-khtml-user-select: none;
	-moz-user-select: -moz-none;
	-ms-user-select: none;
	user-select: none;
	background-color:#EFEFE9;
	border-width:1px;
	border-color:#CCCCCC;
	border-style: solid solid solid solid;
	height:22px;
}

 
.datagrid
{
	background: repeating-linear-gradient(to right, #f6ba52,#f6ba52 10px, #ffd180 10px, #ffd180 20px);
	background-color: #FFFFFF;
	background-size: 10px;
	background-image: -webkit-linear-gradient(0, transparent 90%, rgba(000, 000, 000, .25) 50%);
	background-image: -moz-linear-gradient(0px 50%, transparent 90%, rgba(000, 000, 000, 0.25) 50%);
	background-image: -ms-linear-gradient(0, transparent 90%, rgba(000, 000, 000, .25) 50%);
	background-image: -o-linear-gradient(0, transparent 90%, rgba(000, 000, 000, .25) 50%);		
}
.testdiv
{
	border-radius:10px;
	margin-top:2px;
}
#footer {
    position: fixed;
    bottom: 0;
    
	width:70%; height:50px; background-color:#33CC33; display:none;
}
</style>
 <script type="text/javascript" src="swdata/contextMenu.js"></script>
 
 <link type="text/css" href="swdata/contextMenu.css">
 
<script type="text/javascript"> 
/*$(".testdiv").mouseenter( function() {
   
});

$(".testdiv").mouseleave( function() {
   
});*/
 
function context_menu_operation( e, tid, operation, isdiv )
{
	if( isdiv==0 )
	{
		//alert(tid);return;
		var td=tid.split("y");//tdbody8-10102014
		var d_line=td[1].split("-");
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'sewplan_controller.php?action=order_popup&pline='+d_line[0]+'&pdate='+d_line[1], '', 'width=1050px,height=350px, center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_job");
			if (theemail.value!="")
			{
				//FAL-14-00122_771_500_03-DEC-12_136646_03-DEC-12_1024483.09.40______7**09-10-2014**200****1**14-10-2014**300______2**800**100**1200
				var tmp=theemail.value;
				var tmpd=tmp.split("______");
				var vline=tmpd[1].split("****");
				var linid="";
				var pdates="";
				var plength="";
				for(var m=0; m<vline.length; m++)
				{
					var tline=vline[m].split("**");
					if(linid=="") linid=tline[0]; else linid=linid+"_"+tline[0];
					
					var vd=tline[1].replace('-', ''); var vdt=vd.replace('-', '')
					if(pdates=="") pdates=vdt; else pdates=pdates+"_"+vdt; 
					
					if( plength=="" ) plength=get_production_days( tmpd[2], tline[2] ); else plength=plength+"_"+get_production_days( tmpd[2], tline[2] ); 
					
				}
				
				//alert( pdates+"==="+linid );
				$("#start").text("date: \nLeft: "+ pdates + "\ndate: " + linid);
				draw_chart( linid, pdates, plength, '1' );	
				return;
			}
			//alert(theemail.value);
			//draw_chart( '5_8_10', '02102014_08102014_02102014', '5000', '1' );	
		}
	}
	else if( isdiv==1 )
	{
		if(operation==1)// Cut Div
		{
			//var tid =this.id;
			
			var valss=$('#'+tid).attr('title');
			
			leftsize=0;
			var valssa=valss.split("__");
			var leftsize=e.clientX-valssa[1];
			sval=0;
			var rightsize=valssa[0]-leftsize;
			var name=valssa[2].split("x");
			$('#'+tid).width( leftsize );
			
			$('#'+tid).attr('title',leftsize+"__"+valssa[1]+"__"+name[0]+"__"+valssa[3]);
		//	 alert(leftsize+"__"+valssa[1]+"__"+name[0]+"__"+valssa[3]);  return;
			var $button =$( "#namee" ).clone();
			eval($('#bodycont').append($button));
			$($button).css( {
				'position': 'absolute',
				'left': e.clientX+'px',
				'top': valssa[3]+'px', //$(this).parent().offset().top
				'width': rightsize+'px',
				'z-index':500,
			});
			k++;
			//$($button).attr('onClick','check( this.id )');
			//$($button).attr('title',rightsize+"__"+e.clientX);
			$($button).attr('title',rightsize+"__"+e.clientX+"__"+''+name[0]+"x"+k+"__"+valssa[3]+"__"+tid);
			$($button).attr('id','mdv'+name[0]+"x"+k); 
			//////alert('Clicked!'+valssa[2] +"="+k+"="+leftsize +"="+rightsize );
			fval=0;xpos=0;
			$('#'+tid).width( leftsize );
		}
		if(operation==2)// Reverse Join Cut Div
		{
			var cdivid=$('#'+tid).attr('title');
			var cdivid=cdivid.split("__");
			if(!cdivid[4]) return;
			var odivid=$('#'+cdivid[4]).attr('title');
			var odivid=odivid.split("__");
			 
			var newwidth=cdivid[0]*1+odivid[0]*1;
			$('#'+cdivid[4]).width( newwidth );
			$('#'+cdivid[4]).attr('title',newwidth+"__"+odivid[1]+"__"+''+odivid[2]+"__"+odivid[3]+"__"+tid);
			$('#mdv'+cdivid[2]).remove();
		}
		if(operation==5)// Remove Div
		{
			$('#'+tid).remove();
		}
	}
	//alert( e.clientX+"++"+id );   
}
/* 
$(function() {
        $('#bodycont tr').contextPopup({
          title: 'Planning Tools',
          items: [
            {label:'Order Selection',                 action:function(e) { alert($(this).attr('id'));; test(e) } },
			 null, // divider
            {label:'Cut Here',                  action:function() { alert('Please click on an existing plan.') } },
            {label:'Undo Cut',                  action:function() { alert('Please click on an existing plan.') } },
            null, // divider
            {label:'Change Date',            action:function() { alert('Please click on an existing plan.') } },
            {label:'Change Quantity',                       action:function() { alert('Please click on an existing plan.') } }
          ]
        });
		
		 
		
      }); */
 

/*
$(document).ready(function() {
  var x1;
  var x2;
  var y1;
  var y2;
  $("tr").mousedown(function(e) {
							 var indexmar=$(this).index();
  $("#current").attr({ 
                     id: ''
                     })
  box = $('<div class="dra-dro-res" style="border-top:15px #FF00FF solid;position:fixed;width:1px;">').hide();
  $("table tr:eq("+indexmar+") td").append(box); 
   x1 = e.pageX;
   y1 = e.pageY;
  
  box.attr({id: 'current'}).css({
                                top: e.pageY , //offsets
                                left: e.pageX //offsets
                               }).fadeIn();
            });
  
 $(".datagrid").mousemove(function(e) {
   $("#current").css({
                     width:Math.abs(e.pageX - x1), //offsets
                     height:Math.abs(e.pageY - y1)//offsets
                   }).fadeIn();
                });
 $(".datagrid").mouseup(function() {
							 // alert($("#current").width());
                             $("#current").attr({ id: ''
                         })
							 $( ".dra-dro-res" ).resizable();
							 $( ".dra-dro-res" ).draggable();
							
                 });
 });

   
 $(".verticalStripes1 tr td").click(function(e){
    var x = e.pageX - e.target.offsetLeft;
    alert(x);    
});
 background: linear-gradient(to left, #ff0000 10%, #0000ff 10%)
*/


  $(function() {
    $( ".testdiv" ).draggable();
  });
  
</script>
</head>

<body>
<div id="namee" class="testdiv"  style="background-color:#FF0000; height:15px;">&nbsp;</div>
<div id="start" class="testdiv" style="height:15px">&nbsp;</div>
<div id="stop" class="testdiv" style="height:15px">&nbsp;</div>
<table border="1" width="6000" cellpadding="0" cellspacing="0" rules="all" id="bodycont" >
	
	<tr>
    	<td style="width:150px;">Line</td>
         <? 
		 		$todate=date("Y-m-d",time());
				for($j=1; $j<105; $j++)
				{
					$tdate=date("d-M",strtotime(add_date($todate,$j-1))); 
					?>
					<td class="topsss_header"><? echo $tdate; ?></td>
					<?
				}
			?>
    </tr>
	<? 
		//for($i=0; $i<25; $i++)
		foreach( $line_array as $i=>$line)
		{
			?>
            <tr>
            <? 
				for($j=0; $j<105; $j++)
				{
					
					if( $j==0 ) { $width=""; $idd=""; $text="Line ".$line; $tdcls="left_header";  } else { $tdate=date("dmY",strtotime(add_date($todate,$j-1))); $idd=$i."-".$tdate; $width="20"; $text="&nbsp;"; $tdcls="verticalStripes1"; }
					if($j==89) $width="";
					?>
					<td style="width:<? echo $width."px"; ?>" class="<? echo $tdcls; ?>" id="tdbody<? echo $idd; ?>" name="tdbody<? echo $i.$j; ?>"><? echo $text; ?></td>
					<?
				}
			?>
            
            </tr>
            <?
		}
	?>
</table>
 <div id="footer" >adfasdadada</div>
</body>

<script>

var fval=0;
var sval=0;
var fcolind=0;
var scolind=0;
var frowind=0;
var srowind=0;
var xpos=0;
var stdid="";
var k=0;
$(".verticalStripes1").dblclick(function(e) {
	var fval=0;
	var sval=0;
	var fcolind=0;
	var scolind=0;
	var frowind=0;
	var srowind=0;
	var xpos=0;
	var stdid="";
	var k=0;
})
	
	
$('#bodycont tr td').mousedown(function(e){

   // if(e.button==2) alert( $(this).attr('id') );
	var tid=$(this).attr('id');
	$('#bodycont tr').contextPopup({
          title: 'Planning Tools',
          items: [
            {label:'Order Selection',                 action:function(e) { context_menu_operation( e, tid, 1, 0 ); } },
			 null, // divider
            {label:'Cut Here',                  action:function() { alert('Please click on an existing plan.') } },
            {label:'Undo Cut',                  action:function() { alert('Please click on an existing plan.') } },
            null, // divider
            {label:'Change Date',            action:function() { alert('Please click on an existing plan.') } },
            {label:'Change Quantity',                       action:function() { alert('Please click on an existing plan.') } }
          ]
        });
		//$("#footer").hide(1000);
 });
 
$('body div').live('mousedown', function(e){

    //alert( );
	var tid=$(this).attr('id');
	$('.testdiv').contextPopup({
          title: 'Planning Tools',
          items: [
            {label:'Order Selection',                  action:function() { alert('Please click on blank space.')  } },
			 null, // divider
            {label:'Cut Here',                 action:function() { context_menu_operation( e, tid, 1, 1 ); } },
            {label:'Undo Cut',                action:function() { context_menu_operation( e, tid, 2, 1 );  } },
			{label:'Delete Plan',                action:function() { context_menu_operation( e, tid, 5, 1 );  } },
            null, // divider
            {label:'Change Date',                action:function() { context_menu_operation( e, tid, 3, 1 );  } },
            {label:'Change Quantity',                        action:function() { context_menu_operation( e, tid, 4, 1 );  } }
			// {label:'Change Quantity',        icon:'',                   action:function() { alert('clicked 5') } }
          ]
        });
		
		 //$(".fixed").hide();  
		/* $("#footer").toggle(1000);
		 $('#footer').css({
			 
			'left' :  $(this).width()/2,
			'margin-left' : -$(this).width()/2,
			
		});
		 */

});
 
//alert( get_production_days( '2**800**100**1200', 26500 ));

function get_production_days( complx, qnty )
{
	//2**800**100**1200
	var compl=complx.split("**");
	var app_day=((qnty/compl[1])+2).toFixed(0);
	var toprod=compl[1]*1;
	var k=0;
	var dtarg=compl[1];
	var prt=0;
	for( var j=0; j<app_day; j++ )
	{
		if((qnty*1)<1 ) return k;
		if( (qnty*1)>(dtarg*1) )
		{	
			if( (dtarg*1)<(compl[3]*1) )
			{
				if(j!=0) dtarg=(dtarg*1)+( compl[2]*1 );
				qnty=qnty-dtarg;
				k++;
			}
			else
			{
				qnty=qnty-dtarg;
				k++;
			}
		}
		else
		{
			 prt=( (qnty*10)/compl[3] ).toFixed(0);
			 return k+"."+prt;
		}
		//alert(k+"--"+qnty); 
	}
}

function js_date_add( adate, days )
{
	var d=adate.split("-");
	var day=(d[2]*1)+(days*1);
	var dinM=daysInMonth(d[1],d[0]);
	if( (day*1)>(dinM*1) )
	{
		day=day-dinM;
		d[1]=d[1]+1;
		if( (d[1]*1)>12)
		{
			d[1]=d[1]-12;
			d[0]=d[1]+1;
		}
	}
	return strPad (day, 2, 0)+'-'+strPad (d[1], 2, 0)+'-'+d[0];
}

function daysInMonth(month,year) {
    return new Date(year, month, 0).getDate();
}

function strPad(input, length, string) {
    string = string || '0'; input = input + '';
    return input.length >= length ? input : new Array(length - input.length + 1).join(string) + input;
}

function draw_chart( line, stdate, wid, poid )
{
	//draw_chart( '1_3_4', '2_5_2', '5000', '1' );
	var ln=line.split("_");
	var sd=stdate.split("_");
	var widths=wid.split("_");
	for(var m=0; m<ln.length; m++)
	{
		var stdid='tdbody'+ ln[m]+"-"+sd[m];
		var offs=get_offset( 'tdbody'+ ln[m]+"-"+sd[m]);
		var spos=offs.split(",");
		var xpos=spos[0];//(150*1)+(sd[k]*20);
		var ypos=spos[1];//((20*1)+(ln[k]*20);
		//alert(width[m]);
		var wpart= widths[m].split(".");
		
		var dd=js_date_add( sd[m].substr(4, 4)+"-"+sd[m].substr(2, 2)+"-"+sd[m].substr(0, 2), wpart[0] );
		var vd=dd.replace('-', ''); var vdt=vd.replace('-', '')
		
		var offsright=get_offset( 'tdbody'+ ln[m]+"-"+vdt);
		var offsri=offsright.split(",");
		var ext=0;
		if((wpart[1]*1)>0)
		{
			if(wpart[1].length==1) wpart[1]=wpart[1]+"0";
			ext= (wpart[1]/100)*20;
		}
		//alert(widths[m]+"=="+ext+"="+offsri[0]+"="+xpos);
		var width=((offsri[0]-xpos)+(ext*1));
		// alert(width);
		//visibility:visible; visibility:hidden
		var $button =$( "#namee" ).clone();
		//eval($('#tdbody'+ln[m]+"-"+sd[m]).append($button));
		eval($('#bodycont').append($button));
		$($button).css( {
			'position': 'absolute',
			'left': xpos+'px',
			'top': ypos+'px',
			'width': width+'px',
			'z-index':500,
			
		});
		k++;
		//$($button).attr('onClick','check( this.id )');
		$($button).attr('title',width+"__"+xpos+"__"+'tdbody'+ ln[m]+"-"+sd[m]+"__"+ypos);
		$($button).attr('id','mdv'+stdid+k);
		//$($button).attr('click');
	}
}

function get_offset( vid )
{
	//alert('asd '+vid);
	var offset = $('#'+vid).offset();
	//event.stopPropagation();
	return   offset.left + ", " + offset.top;
	
}

draw_chart( '2_4_6', '07102014_10102014_07102014', '5.65_8.3_2.85', '1' );	

	/*
$(".verticalStripes1").click(function(e) {
	if(fval==0)
	{
		fval=e.clientX + ", "+ e.clientY;
		xpos=e.clientX ;
		ypos=$(this).parent().offset().top;
		sval=0;
		fcolind= parseInt( $(this).index() ) + 1;
		frowind= parseInt( $(this).parent().index() )+1; 
		stdid=this.id;
		//alert(e.clientY+"=="+ypos)
	}
	else
	{
		sval=e.clientX + ", "+ e.clientY;
		scolind= parseInt( $(this).index() ) + 1;
		srowind= parseInt( $(this).parent().index() )+1; 
		var width=e.clientX*1-xpos*1;
		if(width<1){ fval=0; xpos=0; return; }
		if(frowind!=srowind){ fval=0; xpos=0; return; }
		
		 
		  k++;
		var $button =$( "#namee" ).clone();
		eval($('#'+stdid).append($button));
		$($button).css( {
			'position': 'absolute',
			'left': xpos+'px',
			'top': ypos+'px',
			'width': width+'px',
			'z-index':500,
		});
		//$($button).attr('onClick','check( this.id )');
		$($button).attr('title',width+"__"+xpos);
		$($button).attr('id','mdv'+stdid+k);
		//$($button).attr('click');
		//alert(fval + "*"+ sval + "##"+fcolind + "*"+ scolind  + "***"+  frowind + "*"+  srowind + "*"+  this.id +"*"+width);
		fval=0;
		xpos=0;
		stdid="";
		
		
		//var box = $('<div class="dra-dro-res" style="border-top:15px #FF00FF solid;position:fixed;width:1px;">').hide();
	}    
})*/

 $('body div').live("dblclick" , function(e) {
	 	var tid =this.id;
		
		var valss=$('#'+tid).attr('title');
		leftsize=0;
		var valssa=valss.split("__");
		var leftsize=e.clientX-valssa[1];
		sval=0;
		var rightsize=valssa[0]-leftsize;
		var name=valssa[2].split("x");
        $(this).width( leftsize );
		//alert(valss+"=="+leftsize+"=="+tid);  
	 	$(this).attr('title',leftsize+"__"+valssa[1]+"__"+name[0]+"__"+valssa[3]);
		
		var $button =$( "#namee" ).clone();
		eval($('#bodycont').append($button));
		$($button).css( {
			'position': 'absolute',
			'left': e.clientX+'px',
			'top': valssa[3]+'px', //$(this).parent().offset().top
			'width': rightsize+'px',
			'z-index':500,
		});
		k++;
		//$($button).attr('onClick','check( this.id )');
		//$($button).attr('title',rightsize+"__"+e.clientX);
		$($button).attr('title',rightsize+"__"+e.clientX+"__"+''+name[0]+"x"+k+"__"+valssa[3]+"__"+tid);
		$($button).attr('id','mdv'+name[0]+"x"+k); 
		//////alert('Clicked!'+valssa[2] +"="+k+"="+leftsize +"="+rightsize );
		fval=0;xpos=0;
		$(this).width( leftsize );
    });
 
 $('body div').live("mouseenter" , function(e) {
	$( ".testdiv" ).draggable(
						 
						 {

							// Find original position of dragged image.
							start: function(event, ui) {
						
								// Show start dragged position of image.
								var Startpos = $(this).position();
								//alert(Startpos.left);
								//$("#start").text("START: \nLeft: "+ Startpos.left + "\nTop: " + Startpos.top);
							},
						
							// Find position where image is dropped.
							stop: function(event, ui) {
						
								// Show dropped position.
								var Stoppos = $(this).position();
								
								var prev=$(this).attr('title');
								var p=prev.split("__");
								$("#stop").text("STOP: \nLeft: "+ Stoppos.left + "\nTop: " + Stoppos.top+ "==="+prev);
								var el=document.elementFromPoint(Stoppos.left, Stoppos.top) ;
								varnid=$(el).attr('id');
								//alert(varnid);
								if(varnid){ var offset = $('#'+varnid).offset(); vtop=offset.top; } else vtop=Stoppos.top;
								$(this).css( {
									'position': 'absolute',
									'left': Stoppos.left+'px',
									'top': vtop+'px', //$(this).parent().offset().top
									'width': p[0]+'px',
									'z-index':500,
								});
								$(this).attr('title',p[0]+"__"+Stoppos.left+"__"+p[2]+"__"+vtop+"__"+p[4]);
								
								 
								 
							}
						}
						 
						 );
 });
 
$('#bodycont tr td').live("click" , function(e) {
	 
  //alert( e.clientX)
});

/*

$('#foo').appear(function() {
  $(this).text('Hello world');
});
http://stackoverflow.com/questions/487073/check-if-element-is-visible-after-scrolling   scrolig for inv ID 
http://stackoverflow.com/questions/16419791/how-to-detect-overlapping-html-elements

function check( tid, point )
{
	alert( $('#'+tid).parent().offset().left);
	 
	var width = parseInt($('#'+tid).css('width'));
	alert( $('#'+tid).offset().left )
}

http://www.jqueryrain.com/?WoyTRY7r
 */
</script>

</html>

 