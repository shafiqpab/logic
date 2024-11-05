var left_array="";
var top_array="";
var height_array="";
var right_array="";
var val_text="";
var cap_text="";
/*	
	ctx.font="20px Georgia";
	ctx.fillStyle="#CC0099";
	ctx.fillText("LOGIC BAR CHART",100,20);
	
	charttype:::1=sigle column bar, 
				2=multi column bar, 
				3=single column Stack bar chart, 
				4=multi column Stack bar chart,
				5=PIE chart,
				6=Line curve chart.
	
	data format:1="5000*3000*325*4000*850*10000" (Seperated by *)
				2=multi column bar, 
				3=signle column Stack bar chart, 
				5=multi column Stack bar chart,
				6=PIE chart,
				7=Line curve chart.
				
	uses option:  LogiChart( container, dataval, datacap, width, height, charttype, bcolor)
	
	1.	LogiChart( "divbarchart", "5000*3000*325*4000*850*1000", "Jan*Feb*420*400*830*800", 500, 300, 1 , "009900");
	2.	LogiChart( "divbarchart", "5000__3000*325__4000*850__1000*5000__3000*325__4000*850__1000", "Jan*Feb*mar*apr*may*june__projected*confirmed", 500, 300, 1 , "009900");
*/

 function draw_pie_chart(ctx, column, gheight, gwidth, bardepth, bcolor, valperpxl )
 {
	var data=column.split("*");
	var total=0;
	var colors=Array();
	for (var i=0; i<data.length; i++)
	{
		total=total+(data[i]*1);
		var color = getColor();
    	colors[colors.length] = color; // save for later
	}
	var canvas_size = [gwidth, gheight];
	var radius = Math.min(canvas_size[0], canvas_size[1]) / 2;
	var center = [canvas_size[0]/2, canvas_size[1]/2];
	
	var sofar = 0; // keep track of progress
	// loop the data[]
	for (var piece in data) {
	 
		var thisvalue = data[piece] / total;
	 	
		ctx.beginPath();
		ctx.moveTo(center[0], center[1]); // center of the pie
		ctx.arc(  // draw next arc
			center[0],
			center[1],
			radius,
			Math.PI * (- 0.5 + 2 * sofar), // -0.5 sets set the start to be top
			Math.PI * (- 0.5 + 2 * (sofar + thisvalue)),
			false
		);
	// alert(radius+"=="+center[1])
		ctx.lineTo(center[0], center[1]); // line back to the center
		ctx.closePath();
		ctx.fillStyle = colors[piece];    // color
		ctx.fill();
	 
		sofar += thisvalue; // increment progress tracker
	}
 }
 
function LogiChart( container, dataval, datacap, width, height, charttype, bcolor )
{
	// call function to create a canvas
	create_canvas( container,width, height );
	val_text=dataval;
	cap_text=datacap;
	
	var column=dataval.split("*"); // Get column length
	var mxval=Math.max.apply(Math, column);
	var minval=Math.min.apply(Math, column);
	
	var gheight=Math.round((80*height)/100);
	var bheightp=Math.round((100*gheight)/mxval);
	var gwidth=Math.round((100*width)/100);
	
	var bardepth=Math.round(gwidth/column.length)-30;
	
	lineht=Math.round(gheight/8);
	var valperpxl=(gheight/mxval);
	
	var c=document.getElementById("myCanvas");
	var ctx=c.getContext("2d");
	draw_row_grids(ctx,lineht,gwidth  );
	//alert(dataval)
	if(charttype==1) draw_single_barchart(ctx, column, gheight, gwidth, bardepth, bcolor, valperpxl ); 
	else if(charttype==5) draw_pie_chart(ctx, dataval, gheight, gwidth, bardepth, bcolor, valperpxl );
	// Set Value Level at left Side
	var cs=document.getElementById("myCanvavalues");
	var ctxs=cs.getContext("2d");
	set_value_caption(ctxs,gheight,mxval );
	
	// Set Caption Level at Bottom
	var cc=document.getElementById("myCanvascaption");
	var ctxc=cc.getContext("2d");
	set_name_caption_bar_simple(ctxc, datacap, bardepth, bheightp, gheight, column, ctxs )
	
	var canvas = document.getElementById("myCanvas");
	canvas.addEventListener("mousemove", doMouseOver, true); 
	canvas.addEventListener("mouseout", doMouseOut, true);
}

function draw_row_grids(ctx, bgap, bwidth, bnum  ) // draw_row_grids
{
	if(!bnum) var bnum=8;
	for(var i=1; i<=bnum; i++)
	{
		lineht3=bgap*i;
		ctx.fillStyle="#CCCCCC";
		ctx.fillRect(0,lineht3,bwidth, 1);
	}
}

function set_value_caption(ctxs,gheight,mxval )
{
	var partval=((gheight)/8);
	var valperpxl=(mxval/gheight);
	var k=0;
	for(var i=9; i>=1; i--)
	{
		k++;
		var v=Math.round(valperpxl*partval*(i-1));
		lineht3=(lineht*(k-1))+8;
		ctxs.font="10px Georgia";
		ctxs.fillStyle="#CC0099";
		var w=65-ctxs.measureText(v).width;
		ctxs.fillText(v,w,lineht3);
		
	}
}

function set_name_caption_bar_simple(ctxc, datacap, bardepth, bheightp, gheight, column, ctxs )
{
	var columnn=datacap.split("*");
	var llp=10;
	for(var i=0; i<columnn.length; i++)
	{
		 
		var lp=10; var tp=0; var stroke=bardepth; var bh=0;
		
		var bheight=Math.round((bheightp*column[i])/100);
		
		tp=gheight-bheight;
		//lp=(i*((bardepth*1)+15))+(lp*1);
		lp=(i*((bardepth*1)+5))+(lp*1);
		
		ctxc.fillStyle="#CCCCCC";
		ctxc.fillRect(lp-3,0,1,20);
		ctxc.fillStyle="#FF0000";
		//ctx.fillRect(lp,tp,stroke,bheight);
		var w=ctxs.measureText(columnn[i]).width;
		var post=lp-llp; var postt=post-w; var mid=Math.round(postt/2);
		ctxc.font="10px Georgia";
		ctxc.fillStyle="#CC0099";
		if(i!=0)var position=(mid*1)+lp; else { var mid=Math.round((((bardepth*1)+10)-w)/2); var position=(mid*1); }
		ctxc.fillText(columnn[i],position,10);
		
		llp=lp;
			
	}
}

function draw_single_barchart(ctx, column, gheight, gwidth, bardepth, bcolor, valperpxl  )
{
	for(var i=0; i<column.length; i++)
	{
		var lp=10; var tp=0; var stroke=bardepth; var bh=0;
		
		var bheight=valperpxl*column[i];// Math.round((gheight*bheig)/100);
		
		tp=gheight-bheight;
		lp=(i*((bardepth*1)+5))+(lp*1);
		//ctx.fillStyle="#CCCCCC";
		//ctx.fillRect(lp-3,0,1,gheight);
		ctx.fillStyle="#"+bcolor;
		ctx.fillRect(lp,tp,stroke,bheight);
		 
		if(left_array=="") left_array=lp; else left_array=left_array+","+lp;
		if(top_array=="") top_array=tp; else top_array=top_array+","+tp;
		if(height_array=="") height_array=bheight; else height_array=height_array+","+bheight;
		if(right_array=="") right_array=(lp*1)+stroke; else right_array=right_array+","+((lp*1)+stroke);
	}
}

function getMousePos(canvas, evt) 
{
    var rect = canvas.getBoundingClientRect();
    return {
      x: evt.clientX - rect.left,
      y: evt.clientY - rect.top
    };
}

function doMouseOut(event)
{ 
	 $('#charttooltips').hide(20);
}

function getIndex(x,y)
{
	var left_array_data=left_array.split(",");
	var top_array_data=top_array.split(",");
	var right_array_data=right_array.split(",");
	
	for (var i=0; i<left_array_data.length; i++)
	{
		if( x>(left_array_data[i]*1) && x<(right_array_data[i]*1) && y>(top_array_data[i]*1) )
		{
			return i;
			break;
		}
	}	
}

function doMouseOver(event)
{
	var val_text_arr=val_text.split("*");
	var cap_text_arr=cap_text.split("*");
	var xx=event.pageX;//  pageX;
	var yx=event.pageY;
	// alert(xx)
	var mousePos = getMousePos(document.getElementById('myCanvas'), event);
	var y=mousePos.y;
	var x=mousePos.x;
	var ind= getIndex(x,y);
	 
	// $('#test').html('x-'+x +"y="+y);
	if( cap_text_arr[ind] )
	{
		$('#charttooltips').css({'z-index':5});
		$('#myCanvas').css({'z-index':-5});
		$('#charttooltips').css({'width':120,'background-color':000000 });
		$('#charttooltips').html(cap_text_arr[ind]+" : "+val_text_arr[ind]);
		 $('#charttooltips').css({'top':yx, 'left':xx+10}).show(200);
	}
	else { $('#charttooltips').hide(200);}
}

function create_canvas( container,width, height )
{
	var gheight=Math.round((80*height)/100);
	var gwidth=Math.round((80*width)/100);
	
	var html='<div style="width:'+width+'px; height:'+((height*1)+50)+'px;" id="lchart_area">';
	html=html+' <canvas id="myCanvavalues" width="'+65+'" height="'+((gheight*1)+20)+'" style="float:left;border:0px solid #FF00FF; margin-top:15px;margin-left:5px;"></canvas><canvas id="myCanvas" width="'+gwidth+'" height="'+gheight+'" style="float:left;border:1px solid #FF00FF; margin-top:15px;margin-left:5px;"></canvas><canvas id="myCanvascaption" width="'+gwidth+'" height="'+75+'" style="float:left; border:0px solid #FF00FF; margin-top:-10px; margin-left:77px;">';
	html=html+'</div><div id="charttooltips"></div>';
	$('#'+container).html(html);
}

function getColor() 
{
	var rgb = [];
	for (var i = 0; i < 3; i++) {
		rgb[i] = Math.round(80 * Math.random() + 80) ; // [155-255] = lighter colors
	}
	return 'rgb(' + rgb.join(',') + ')';
}
	