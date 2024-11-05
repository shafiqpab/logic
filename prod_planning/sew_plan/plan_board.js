$('#tdbody-'+ line+"-"+vdt).attr('plan_group', podtl[0]);
$('#tdbody-'+ line+"-"+vdt).attr('title','Plan Start Date:: '+podtl[15]+' ,Plan End Date:: '+actenddate);
$('#tdbody-'+ line+"-"+vdt).attr('compstart',podtl[11]);
$('#tdbody-'+ line+"-"+vdt).attr('start_td_id',strtid);
$('#tdbody-'+ line+"-"+vdt).html( day_qnty[i]+"<br><font color='#0000FF'>"+'1250'+"</font>");// ('compinc',podtl[12]);
$('#tdbody-'+ line+"-"+vdt).attr('compinc',podtl[12]);
$('#tdbody-'+ line+"-"+vdt).attr('comptarg',podtl[13]);
$('#tdbody-'+ line+"-"+vdt).attr('podtls',podtls);
$('#tdbody-'+ line+"-"+vdt).attr('compid',podtl[10]);
$('#tdbody-'+ line+"-"+vdt).attr('po_id',podtl[2]);
$('#tdbody-'+ line+"-"+vdt).attr('plan_id',podtl[0]);
$('#tdbody-'+ line+"-"+vdt).attr('line_id',podtl[1]);
$('#tdbody-'+ line+"-"+vdt).attr('start_date',podtl[4]);
$('#tdbody-'+ line+"-"+vdt).attr('start_hr',podtl[5]);
$('#tdbody-'+ line+"-"+vdt).attr('end_hr',podtl[7]);
$('#tdbody-'+ line+"-"+vdt).attr('plan_qnty',podtl[9]);
$('#tdbody-'+ line+"-"+vdt).attr('duration',wp+"."+wpart[1]);
$('#tdbody-'+ line+"-"+vdt).attr('upd_id',podtl[0]);
$('#tdbody-'+ line+"-"+vdt).attr('isedited','0');
$('#tdbody-'+ line+"-"+vdt).attr('plan_end_date',actenddate);
$('#tdbody-'+ line+"-"+vdt).attr('is_partial',podtl[14]);
$('#tdbody-'+ line+"-"+vdt).attr('today_production',day_qnty[i]);
$('#tdbody-'+ line+"-"+vdt).attr('today_plan_qnty',day_qnty[i]);
$('#tdbody-'+ line+"-"+vdt).attr('off_day_plan',off_day_plan);
if( ( podtl[0]*1)>0 ) $('#tdbody-'+ line+"-"+vdt).attr('isnew','0'); else $('#tdbody-'+ line+"-"+vdt).attr('isnew','1');
$('#tdbody-'+ line+"-"+vdt).attr('name',podtl[17]);
$('#tdbody-'+ line+"-"+vdt).attr('onclick','event_select(this.id)');
$('#tdbody-'+ line+"-"+vdt).addClass('verticalStripes1_plan')
$('#tdbody-'+ line+"-"+vdt).css('border-radius', '70% 0 0 70%');


var cdate="";

function context_menu_operation( e, tid, operation, isdiv )
{
	if( isdiv==0 )
	{
		var td=tid.split("-");
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/planning_board_controller.php?action=order_popup&company_id='+$('#cbo_company_mst').val()+'&location_id='+$('#cbo_location_name').val()+'&pline='+td[1]+'&pdate='+td[2]+'&cdate='+cdate, '', 'width=1050px,height=350px, center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_job");
			
			if (theemail.value!="")
			{
				var tmp=theemail.value;
				var tmpd=tmp.split("______");
				var podt=tmpd[0].split("_");
				var comp=tmpd[2].split("**");
				
				var pln=tmpd[1].split("****");
				//alert('ss'+pln[0]);
				for(var m=0; m<pln.length; m++)
				{
					
					var npln=pln[m].split("**");
					//0**7**1407**0**13-03-2015**0**13-03-2015**0**undefined**15000**2**800**100**1200**0**13-03-2015**13-03-2015**77539-120-13-212-001
					var plength=get_production_days( tmpd[2], npln[2] ); // else plength=plength+"_"+get_production_days( tmpd[2], tline[2] ); 
					//alert('ss'+plength);
					var podtls="0**"+npln[0]+"**"+podt[1]+"**0**"+npln[1]+"**0**"+npln[1]+"**0**"+plength+"**"+npln[2]+"**"+comp[0]+"**"+comp[1]+"**"+comp[2]+"**"+comp[3]+"**0**"+npln[1]+"**"+npln[1]+"**"+podt[4];
					//alert(podtls);
					draw_chart( npln[0], npln[1], plength, podtls, npln[2] );	
				}
			}
		}
	}
	else if( isdiv==1 )
	{
		if(operation==1)// Cut Div
		{
			//var tid =this.id;
			 
			var dt=$('#'+tid).position();
			
			leftsize=0;
			var lpos=((e.clientX*1)+($(window).scrollLeft()*1));
			var leftsize=lpos -$('#'+tid).attr('left_pos');
			 
			var oldwid=$('#'+tid).attr('width_px');
			var oldenddate=$('#'+tid).attr('enddate');
			
			sval=0;
			var rightsize=$('#'+tid).attr('width_px')-leftsize;
			var name=$('#'+tid).attr('start_td_id').split("x");
			$('#'+tid).width( leftsize );
			 
			$('#'+tid).attr('width_px',leftsize);
			$('#'+tid).css( {
				'position': 'absolute',
				'top': $(this).attr('top_pos')+'px', //$(this).parent().offset().top
				'width': leftsize+'px',
				'z-index':500,
				'display':'block', 
				
			});
			$('#'+tid).attr('isedited','1');
			var new_old_plan_qnty=($('#'+tid).attr('plan_qnty')/oldwid)*leftsize;
			var new_plan_qnty=$('#'+tid).attr('plan_qnty')-new_old_plan_qnty;
			 
			$('#'+tid).attr('plan_qnty',new_old_plan_qnty);
			
			// 2nd part of plan after split
			var tv=get_starting_td(lpos*1, ((e.clientY*1)+($(window).scrollTop()*1)) );
			var varnid=$('#txt_overlaaped_id').val();
			//alert(lpos+"=="+$(this).attr('top_pos')+"=="+e.clientX+"=="+e.clientY)
			var vartdate=varnid.split("-");
			
			var new_dur= datediff( $('#'+tid).attr('start_date'), vartdate[2].substr(4, 4)+"-"+vartdate[2].substr(2, 2)+"-"+vartdate[2].substr(0, 2));
			
			$('#'+tid).attr('duration',new_dur);
			$('#'+tid).attr('enddate',vartdate[2].substr(4, 4)+"-"+vartdate[2].substr(2, 2)+"-"+vartdate[2].substr(0, 2));
			
			var $button =$( "#plan_bar" ).clone();
			eval($('#bodycont').append($button));
			$($button).css( {
				'position': 'absolute',
				'left': lpos+'px',
				'top': $('#'+tid).attr('top_pos')+'px', //$(this).parent().offset().top
				'width': rightsize+'px',
				'z-index':500,
				'display':'block', 
			});
			k++;
			//$($button).attr('title',rightsize+"__"+lpos+"__"+''+name[0]+"x"+k+"__"+titlear[3]+"__"+tid+"__"+titlear[5]);
				$($button).attr('completed_px',0);
					$($button).attr('completed_qnty', 0);
					$($button).attr('width_px',rightsize);
					$($button).attr('left_pos',lpos);
					$($button).attr('top_pos',$('#'+tid).attr('top_pos'));
					$($button).attr('start_td_id',varnid);
					$($button).attr('plan_div_id',$('#'+tid).attr('plan_div_id')+k);
					$($button).attr('podtls',$('#'+tid).attr('podtls'));
					$($button).attr('id','mdvtdbody-'+$('#'+tid).attr('line_id')+"-"+vartdate[2].substr(0, 2)+""+vartdate[2].substr(2, 2)+""+vartdate[2].substr(4, 4)+""+k++);
					//mdvtdbody-8-301220146
				$($button).attr('compid',$('#'+tid).attr('compid'));
					$($button).attr('compstart',$('#'+tid).attr('compstart'));
					$($button).attr('compinc',$('#'+tid).attr('compinc'));
					$($button).attr('comptarg',$('#'+tid).attr('comptarg'));
					$($button).attr('po_id',$('#'+tid).attr('po_id'));
					$($button).attr('plan_id',$('#'+tid).attr('plan_id'));
					$($button).attr('line_id',$('#'+tid).attr('line_id'));
					
					$($button).attr('start_date',vartdate[2].substr(0, 2)+"-"+vartdate[2].substr(2, 2)+"-"+vartdate[2].substr(4, 4));
					$($button).attr('plan_qnty',new_plan_qnty);
					var dd=$($button).attr('start_date').split("-");
					var new_dur= datediff( dd[2]+"-"+dd[1]+"-"+dd[0], oldenddate );
			 
					$($button).attr('duration',new_dur);
					$($button).attr('enddate',oldenddate);
					
					$($button).attr('upd_id',0);
					$($button).attr('isedited','0');
					$($button).attr('isnew',1);
					$($button).attr('main_id',tid);
					
			fval=0;xpos=0;
			
			 
		}
		if(operation==2)// Reverse Join Cut Div
		{
			var main_id=$('#'+tid).attr('main_id');
			if(!main_id) return;
			
			 
			 
			var newwidth=$('#'+main_id).attr('width_px')*1+$('#'+tid).attr('width_px')*1;
			$('#'+main_id).width( newwidth );
			
			$('#'+main_id).attr('width_px',newwidth);
			var new_plan_qnty=$('#'+main_id).attr('plan_qnty')*1+$('#'+tid).attr('plan_qnty')*1;
			
			$('#'+main_id).attr('plan_qnty',new_plan_qnty);
		 
			var new_dur= $('#'+main_id).attr('duration')*1+$('#'+tid).attr('duration')*1;
	 		
			$('#'+main_id).attr('duration',new_dur);
			$('#'+main_id).attr('enddate',$('#'+tid).attr('enddate'));
			
			//$('#'+cdivid[4]).attr('title',newwidth+"__"+odivid[1]+"__"+''+odivid[2]+"__"+odivid[3]+"__"+tid);
			$('#'+tid).remove();
		}
		if(operation==5)// Remove Div
		{
			$('#'+tid).remove();
		}
		if(operation==6)// Div
		{
			$( "#footer" ).toggle( "slow", function(   ) {
				//if( $(this) )
				show_information(tid);
			});
			 
			 var wind=($(window).width()-1000)/2;
			 $('#footer').css({
				 'left' :  wind,
				'margin-left' : -wind,
			});
		}
	}
	   
}

var off_day_array=''; 
var prodcution_data=new Array();	 
function populate_line()
{
	var vd=$('#txt_start_date').val().replace('-', ''); var cdate=vd.replace('-', '') ;
	$('#txt_cdate').val(cdate);
	if($('#cbo_company_mst').val()==0)
	{
		alert('Please Select Company');
		return;
	}
	if($('#cbo_location_name').val()==0)
	{
		alert('Please Select Location');
		return;
	}
	var data=$('#cbo_company_mst').val()+"__"+$('#cbo_location_name').val()+"__"+$('#txt_start_date').val();
	show_list_view(data,'show_plan_details','plan_container','requires/planning_board_controller','');
	
	prodcution_data.length=0;
	off_day_array=$('#off_day_data').val();
	
	$('#off_day_data').val('');
	var old_plan_data=$('#old_plan_data').val();
	 
	$('#old_plan_data').val('');
	var old_plan_data_arr=old_plan_data.split("**__**");
	var dd=$('#txt_production_data').val();
	var tmpprd=dd.split("**");
	$('#txt_production_data').val('');
	for(var n=0; n<tmpprd.length; n++)
	{
		var tmp=tmpprd[n].split("_");
		prodcution_data[tmp[0]]=tmp[1];
	}
	for(var l=0; l<old_plan_data_arr.length; l++)
	{
		if(old_plan_data_arr[l]!="")
		{
			var tmp=old_plan_data_arr[l];
			var tmpd=tmp.split("**");
			draw_chart( tmpd[1], tmpd[4], tmpd[8], old_plan_data_arr[l], tmpd[9] );	
		}
	}
}

function fnc_save_planning( )
{
	var total_plan=0;
	var poid="";
	var cmpid="";
	var cmpstart="";
	var cmpinc="";
	var cmptarg="";
	var planqty="";
	var startdate="";
	var starttime="";
	var enddate="";
	var endtime="";
	var lineid="";
	var duratin="";
	var rowid="";
	var updid="";
	var planid="";
	var isedited="";
	var isnew="";
	$( "#plan_container .testdiv" ).each(function() {
		if(total_plan==0)
		{
			poid=$(this).attr('po_id');
			cmpid=$(this).attr('compid');
			cmpstart=$(this).attr('compstart');
			cmpinc=$(this).attr('compinc');
			cmptarg=$(this).attr('comptarg');
			lineid=$(this).attr('line_id');
			var startdt=$(this).attr('start_date').split(".");
			
			startdate=startdt[0]; //porest[2].substr(4, 4)+"-"+porest[2].substr(2, 2)+"-"+porest[2].substr(0, 2);
			starttime=startdt[1]; //porest[2].substr(4, 4)+"-"+porest[2].substr(2, 2)+"-"+porest[2].substr(0, 2);
			duratin=$(this).attr('duration');
			planqty=$(this).attr('plan_qnty');
			enddate=$(this).attr('enddate');
			updid=$(this).attr('upd_id');
			planid=$(this).attr('plan_id');
			isnew=$(this).attr('isnew');
			isedited=$(this).attr('isedited');
		}
		else
		{
			poid=poid+"**"+$(this).attr('po_id');
			cmpid=cmpid+"**"+$(this).attr('compid');
			cmpstart=cmpstart+"**"+$(this).attr('compstart');
			cmpinc=cmpinc+"**"+$(this).attr('compinc');
			cmptarg=cmptarg+"**"+$(this).attr('comptarg');
			lineid=lineid+"**"+$(this).attr('line_id');
			
			var startdt=$(this).attr('start_date').split(".");
			
			startdate=startdate+"**"+startdt[0]; //porest[2].substr(4, 4)+"-"+porest[2].substr(2, 2)+"-"+porest[2].substr(0, 2);
			starttime=starttime+"**"+startdt[1];
			
			//startdate=startdate+"**"+$(this).attr('start_date');//porest[2].substr(4, 4)+"-"+porest[2].substr(2, 2)+"-"+porest[2].substr(0, 2);
			duratin=duratin+"**"+$(this).attr('duration');
			planqty=planqty+"**"+$(this).attr('plan_qnty');
			enddate=enddate+"**"+$(this).attr('enddate');
			updid=updid+"**"+$(this).attr('upd_id');
			planid=planid+"**"+$(this).attr('plan_id');
			isnew=isnew+"**"+$(this).attr('isnew');
			isedited=isedited+"**"+$(this).attr('isedited');
		}
		
		total_plan++;
		//FAL-14-01123_2426_120000_22-SEP-14_0121613-1686_22-SEP-14_0121613-1686______2**800**100**1200***2***17122014***42.5***50000***2015-01-28
	});
	var operation=0;
	var data="action=save_update_delete&operation="+operation+"&num_row="+total_plan+"&poid="+poid+"&cmpid="+cmpid+"&cmpstart="+cmpstart+"&cmpinc="+cmpinc+"&cmptarg="+cmptarg+"&lineid="+lineid+"&startdate="+startdate+"&duratin="+duratin+"&planqty="+planqty+"&enddate="+enddate+"&planid="+planid+"&updid="+updid+"&isnew="+isnew+"&isedited="+isedited;
	 
	//freeze_window(operation);  +get_submitted_data_string('cbo_location_name*cbo_company_mst',"../")
	http.open("POST","requires/planning_board_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_save_planning_response;
	//alert( startdate );
	//alert(total_plan);
}

function fnc_save_planning_response()
{
	if(http.readyState == 4) 
	{
		var response=trim(http.responseText).split('**');
		alert(http.responseText);
		//show_msg(response[0]);
		
		 
		
		//release_freezing();
	}
}
 

//id="common"
/*

setInterval(function(){
		$('.progress').css({
			  
			 'background':'green', 
		});
	 $('.progress').animate({ width: '80%' }, 1500);
	 $('.progress').animate({ width: '0%' }, 1500);
},500)
*/
/*
$('#txt_search_text').live("keypress", function(e) {
	 
        if (e.keyCode == 40) {
            $('#plan_container').animate({
				scrollLeft: $( "div[po_id='646']" ).offset().left
			}, 1000);
            return false; // prevent the button click from happening
        } //$("#target-element") =""
});
*/
var fval=0;
var sval=0;
var fcolind=0;
var scolind=0;
var frowind=0;
var srowind=0;
var xpos=0;
var stdid="";
var k=0;
var movementTimer = null;

function draw_chart( line, stdate, wid, podtls, planqnty )
{
	cdate=$('#txt_cdate').val();
	var podtl=podtls.split("**");
	
	var vd=podtl[4].replace('-', ''); var stdate=vd.replace('-', '');
	var stdid='tdbody-'+ line+"-"+stdate;
	
	var tdate=stdate;
	var vd=cdate.replace('-', ''); 
	var ptdate=vd.replace('-', '');
	
	if((tdate*1)>=(ptdate*1))
	{
		var offs=get_offset( stdid );
		var spos=offs.split(",");
		var xpos=spos[0];//(150*1)+(sd[k]*20);
		var ypos=spos[1];//((20*1)+(ln[k]*20);
		var ymd1=podtl[4].split("-"); var ymd=ymd1[2]+"-"+ymd1[1]+"-"+ymd1[0];
		var wpart= wid.split("."); 
		var days_date=append_off_days( ymd , Math.ceil(wid) );
		
		days_date=days_date.split("__");
		var wp=days_date[0];
		if( wpart[1]*1>0 ) wp=wp-1;
		
		var dd=js_date_add( ymd, wp );
		var vd=dd.replace('-', ''); var vdt=vd.replace('-', '');
		var offsright=get_offset( 'tdbody-'+ line+"-"+vdt);
		var offsri=offsright.split(",");
		
		var ext=0;
		var ext_start=0;
		if((wpart[1]*1)>0)
		{
			if(wpart[1].length==1) wpart[1]=wpart[1]+"0";
			ext= (wpart[1]/100)*30;
		}
		if(podtl[5]*1>0)
		{
			if(podtl[5].length==1) podtl[5]=podtl[5]+"0";
			ext_start= (podtl[5]/100)*30;
			xpos=(xpos*1)+(ext_start*1);
		}
		
		var width=((offsri[0]-xpos)+(ext*1));
		
		if( ( podtl[14]*1 )==1  )
		{
			var left_color="9C8AE3";
			var $button =$( "#plan_bar_partial" ).clone();
		}
		else
		{
			var left_color="0AE7E1";
			var $button =$( "#plan_bar" ).clone();
		}
		
		eval($('#bodycont').append($button));
		var completed=0;
		var prod_left=0;
		completed=(prodcution_data[podtl[10]]/podtl[9])*100;
		prod_left=100-completed;
		if(!completed) completed=0;
		$($button).css( {
			'position': 'absolute',
			'left': xpos+'px',
			'top': ypos+'px',
			'width': width+'px',
			'z-index':500,
			'display':'block',
			'background':' -moz-linear-gradient(left, #70BC3D '+completed+'%, #'+left_color+' ' +prod_left+'%)',
		});
		k++;
		
		var comppx=(completed/100)*width;
		
		$($button).attr('title','Plan Start Date:: '+podtl[15]+' ,Plan End Date:: '+podtl[16]);
		
		$($button).attr('completed_px',Math.floor(comppx));
		$($button).attr('completed_qnty', prodcution_data[podtl[10]]);
		$($button).attr('width_px',width);
		$($button).attr('left_pos',xpos);
		$($button).attr('top_pos',ypos);
		$($button).attr('start_td_id','tdbody-'+ line+"-"+stdate);
		$($button).attr('plan_div_id','mdv'+line+"-"+stdate+k);
		$($button).attr('podtls',podtls);
		
		$($button).attr('id','mdv'+line+"-"+stdate+k);
		
		if( (completed*1)>0 && (podtl[14]*1)!=1) $($button).attr('class','testdiv_freeze');
		
		$($button).attr('compid',podtl[10]);
		$($button).attr('compstart',podtl[11]);
		$($button).attr('compinc',podtl[12]);
		$($button).attr('comptarg',podtl[13]);
		$($button).attr('po_id',podtl[2]);
		$($button).attr('plan_id',podtl[3]);
		$($button).attr('line_id',podtl[1]);
		$($button).attr('start_date',podtl[4]);
		$($button).attr('start_hr',podtl[5]);
		$($button).attr('end_hr',podtl[7]);
		$($button).attr('plan_qnty',podtl[9]);
		$($button).attr('duration',wp+"."+wpart[1]);
		$($button).attr('upd_id',podtl[0]);
		$($button).attr('enddate',dd);
		$($button).attr('isedited','0');
		if( ( podtl[0]*1)>0 ) $($button).attr('isnew','0'); else $($button).attr('isnew','1');
 
		$($button).attr('name',podtl[17]);
	}
}

function show_information( tid )
{
	$('#common').html( $( "#"+tid ).attr('title'));
	
	var data=$("#"+tid).attr('completed_qnty')+"__"+ $("#"+tid).attr('po_id')+"__"+ $("#"+tid).attr('plan_id')+"__"+ $("#"+tid).attr('line_id')+"__"+ $("#"+tid).attr('start_date')+"__"+ $("#"+tid).attr('plan_qnty')+"__"+ $("#"+tid).attr('duration')+"__"+ $("#"+tid).attr('enddate');
	
	
	 var datass= $.ajax({
		  url: "requires/planning_board_controller.php?data="+data+"&action=show_planning_details_bottom",
		  async: false
		}).responseText
		$('#footer').html( datass); 
}

function get_starting_td( pleft, ptop )
{
	var ft=false;
	
	//alert(pleft+"=="+ptop)
	if( pleft<170 ){ return false;   }
	if( ptop<80 ) { return false;   }
	$('.verticalStripes1').each(function(index, element) {
        var offs=get_offset( $(this).attr('id') );
		var off=offs.split(",");
		var pwdth=(off[0]*1)+30;
		var phgt=(off[1]*1)+25;
		
		if( (pleft*1)>=(off[0]*1) && (pleft*1)<= (pwdth*1) )
		{
			//alert(phgt+'asdasd--'+ptop+'=='+off[1])
			if( (ptop*1)>(off[1]*1) && (ptop*1)<(phgt*1) )
			{
				$('#txt_overlaaped_id').val($(this).attr('id'));
				ft=true;
				//alert('asdasd--'+$(this).attr('id'))
			}
				 //alert('asdasd--'+$(this).attr('id'))
		}
		 
		if( ft==true )
		{
			
			return false;
		}
		//return ft;
		//return ft;
    });
	//return ft;
}

function get_starting_td_offday( pleft, ptop )
{
	var ft=false;
	
	//alert(pleft+"=="+ptop)
	if( pleft<170 ){ return false;   }
	if( ptop<80 ) { return false;   }
	$('.verticalStripes_off').each(function(index, element) {
        var offs=get_offset( $(this).attr('id') );
		var off=offs.split(",");
		var pwdth=(off[0]*1)+30;
		var phgt=(off[1]*1)+25;
		
		if( (pleft*1)>=(off[0]*1) && (pleft*1)<= (pwdth*1) )
		{
			//alert(phgt+'asdasd--'+ptop+'=='+off[1])
			if( (ptop*1)>(off[1]*1) && (ptop*1)<(phgt*1) )
			{
				 
				var odate=$(this).attr('id').split("-");
				var od=(odate[2].substr(0, 2)*1)+1
				//alert($.trim(od).length+'=='+od)
				if( $.trim(od).length<2 ) od ="0"+od;
				$('#txt_overlaaped_id').val( odate[0]+"-"+odate[1]+"-"+od+""+odate[2].substr(2, 6) );
				ft=true;
				 //alert($('#txt_overlaaped_id').val())
			}
				 //alert('asdasd--'+$(this).attr('id'))
		}
		 
		if( ft==true )
		{
			
			return false;
		}
		else
		{
			var lp=((pleft*1)+2);
			get_starting_td_offday(lp , ptop );
		}
		//return ft;
		//return ft;
    });
	//return ft;
}

function check_overlapping( id )
{
 	var dElem=$('#'+id);
	var doffset=dElem.offset();
	var dLeft=doffset.left;
	var dRight=dElem.width()+dLeft;
	var dTop=doffset.top;
	var dbottom=doffset.top+dElem.height();
	
	var ft=true;
	$( "#plan_container .testdiv" ).each(function() {
		var cElem=$('#'+$(this).attr('id'));
		var coffset=cElem.offset();
		var cLeft=coffset.left;
		var cRight=cElem.width()+cLeft;
		var cTop=coffset.top;
		var cbottom=coffset.top+cElem.height();
		
		if( id!=$(this).attr('id'))
		{
			/*if( (pleft*1)>=(off[0]*1) && (pleft*1)<= (pwdth*1) )
			{
				//alert(phgt+'asdasd--'+ptop+'=='+off[1])
				if( (ptop*1)>(off[1]*1) && (ptop*1)<(phgt*1) )
				{
					$('#txt_overlaaped_id').val($(this).attr('id'));
					ft=true;
					//alert('asdasd--'+$(this).attr('id'))
				}
					 //alert('asdasd--'+$(this).attr('id'))
			}*/
			
			ft= ( ( (dRight >= cLeft) && (dLeft <= cRight) ) && ( ((dTop + 15) >= cTop) && (dTop <= (cTop + 15)) ) );
		 	if(ft==true) return false;
			//alert(ft)
		}
	});
	return ft; 
	return false;
}

function datediff(start, end )
{
	var diff = new Date(new Date(end) - new Date(start));
	return diff/1000/60/60/24;
}

function get_offset( vid )
{
	var offset = $('#'+vid).offset();
	return   offset.left + ", " + offset.top;
}
 
function get_production_days( complx, qnty )
{
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
	}
}

var off_day_arrayk =new Array();
function append_off_days( start_date, duration )
{
	off_day_arrayk=off_day_array.split(",");
	for(var m=0;m<duration; m++)
	{
		var d=js_date_add( start_date, m );
		var dd=d.split("-");
		
		var dates= dd[2]+"-"+dd[1]+"-"+dd[0];
		
		for( var km=0; km<off_day_arrayk.length; km++)
		{ 
			if( trim(off_day_arrayk[km])==dates) //jQuery.inArray( dates, off_day_arrayk)!=-1)
			{
				 duration++;
			}
		}
	}
	return duration+"__"+dates;
}

function js_date_add( from_date, no_of_days )
{
	from_date = from_date.split(/\D+/);
	from_date = new Date(from_date[0],from_date[1]-1,(parseInt(from_date[2])+(no_of_days*1)));
	var ndateArr = from_date.toString().split(' ');
	var Months = 'Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec';
	//var mon=(parseInt(Months.indexOf(ndateArr[1])/4)+1);
	var adding_date = ndateArr[2]+'-'+strPad((parseInt(Months.indexOf(ndateArr[1])/4)+1),2,"0")+'-'+ndateArr[3];
	return (adding_date);
}

function daysInMonth(month,year) {
    return new Date(year, month, 0).getDate();
}

function strPad(input, length, string) {
    string = string || '0'; input = input + '';
    return input.length >= length ? input : new Array(length - input.length + 1).join(string) + input;
}

// Scale on mouse move
$('.verticalStripes1').live('mousemove', function(e){  
		if(($(window).scrollTop()*1)<50)
			var toop=$(window).scrollTop()+50;
		else var toop=$(window).scrollTop();
		var leftt=$(window).scrollLeft();
		
		$('#selection_line_vert').css({
			 'left' :  (e.clientX)+1+leftt,
			 'top' :  toop,
			 'display':'block', 
		});
		
		$('#selection_line_hor').css({
			 'top' : ((e.clientY*1)+($(window).scrollTop()*1))+2 ,
			 'left' : leftt,
			 'display':'block', 
		});
		
		clearTimeout(movementTimer);
		movementTimer = setTimeout(function()
		{
			$('#selection_line_hor').css({
				 
				 'display':'none', 
			});
			$('#selection_line_vert').css({
				 'display':'none', 
			});
		}, 1000);
})

// Context menu on Board TD
$('.verticalStripes1').live('mousedown', function(e){ 
	var tid=$(this).attr('id');
	$('#bodycont tr').contextPopup({
          title: 'Planning Tools',
          items: [
            {label:'Order Selection',                 	action:function(e) { context_menu_operation( e, tid, 1, 0 ); } },
			 null, // divider
            {label:'Cut Here',                  		action:function() { alert('Please click on an existing plan.') } },
            {label:'Undo Cut',                  		action:function() { alert('Please click on an existing plan.') } },
            null, // divider
            {label:'Change Date',            			action:function() { alert('Please click on an existing plan.') } },
            {label:'Change Quantity',                   action:function() { alert('Please click on an existing plan.') } }
          ]
        });
		$("#footer").hide(1000);
})

// Context menu on Board Bar chart or Plan Div and show footer
$('.testdiv').live('mousedown', function(e){ 
	var tid=$(this).attr('id');
	if(e.button==2 || e.button==1)
	{
		$('.testdiv').contextPopup({
			  title: 'Planning Tools',
			  items: [
				{label:'Order Selection',                  	action:function() { alert('Please click on blank space.')  } },
				 null, // divider
				{label:'Cut Here',                 			action:function() { context_menu_operation( e, tid, 1, 1 ); } },
				{label:'Undo Cut',               			action:function() { context_menu_operation( e, tid, 2, 1 );  } },
				{label:'Delete Plan',                		action:function() { context_menu_operation( e, tid, 5, 1 );  } },
				null, // divider
				//{label:'Change Date',                		action:function() { context_menu_operation( e, tid, 3, 1 );  } },
				//{label:'Change Quantity',                   action:function() { context_menu_operation( e, tid, 4, 1 );  } },
				{label:'Show Particulars',                   action:function() { context_menu_operation( e, tid, 6, 1 );  } }
				// {label:'Change Quantity',        icon:'',                   action:function() { alert('clicked 5') } }
			  ]
			});
	}
	 
	 
});

//Show footer for  testdiv_freeze
$('.testdiv_freeze').live('mousedown', function(e){ 
	var tid=$(this).attr('id');
	if(e.button==2 || e.button==1)
	{
		 $('.testdiv').contextPopup({
			  title: 'Planning Tools',
			  items: [
				{label:'Order Selection',                  	action:function() { alert('Please click on blank space.')  } },
				 null, // divider
				{label:'Cut Here',                 			action:function() {  alert('Please click on blank space.') } },
				{label:'Undo Cut',               			action:function() {  alert('Please click on blank space.');  } },
				{label:'Delete Plan',                		action:function() {  alert('Please click on blank space.');  } },
				null, // divider
				//{label:'Change Date',                		action:function() {  alert('Please click on blank space.');  } },
				//{label:'Change Quantity',                   action:function() {  alert('Please click on blank space.');  } },
				{label:'Show Particulars',                   action:function() { context_menu_operation( e, tid, 6, 1 );  } }
				// {label:'Change Quantity',        icon:'',                   action:function() { alert('clicked 5') } }
			  ]
			});
	}
	 
});

//Show footer for plan_crossed_bar
$('.plan_crossed_bar').live('mousedown', function(e){ 
	var tid=$(this).attr('id');
	if(e.button==2 || e.button==1)
	{
		$('.testdiv').contextPopup({
			  title: 'Planning Tools',
			  items: [
				{label:'Order Selection',                  	action:function() { alert('Please click on blank space.')  } },
				 null, // divider
				{label:'Cut Here',                 			action:function() {  alert('Please click on blank space.') } },
				{label:'Undo Cut',               			action:function() {  alert('Please click on blank space.');  } },
				{label:'Delete Plan',                		action:function() {  alert('Please click on blank space.');  } },
				null, // divider
				{label:'Change Date',                		action:function() {  alert('Please click on blank space.');  } },
				{label:'Change Quantity',                   action:function() {  alert('Please click on blank space.');  } },
				{label:'Show Particulars',                   action:function() { context_menu_operation( e, tid, 6, 1 );  } }
				// {label:'Change Quantity',        icon:'',                   action:function() { alert('clicked 5') } }
			  ]
			});
	}
	 
});

 //div split on double click
$('.testdiv').live("dblclick" , function(e) {
	var tid =this.id;
	var dt=$(this).position();

	leftsize=0;
	var lpos=((e.clientX*1)+($(window).scrollLeft()*1));
	var leftsize=lpos -$(this).attr('left_pos');
	 
	var oldwid=$(this).attr('width_px');
	var oldenddate=$(this).attr('enddate');
	
	sval=0;
	var rightsize=$(this).attr('width_px')-leftsize;
	var name=$(this).attr('start_td_id').split("x");
	$(this).width( leftsize );
	
	$(this).attr('width_px',leftsize);
	$(this).css( {
		'position': 'absolute',
		'top': $(this).attr('top_pos')+'px', //$(this).parent().offset().top
		'width': leftsize+'px',
		'z-index':500,
		'display':'block',
	});
	$(this).attr('isedited','1');
	var new_old_plan_qnty=Math.floor(($(this).attr('plan_qnty')/oldwid)*leftsize);
	var new_plan_qnty=$(this).attr('plan_qnty')-new_old_plan_qnty;
	
 	$(this).attr('plan_qnty',new_old_plan_qnty);
	
	//2nd part of plan after split 16032015
	var tv=get_starting_td( lpos*1, ((e.clientY*1)+($(window).scrollTop()*1)) );
	var varnid=$('#txt_overlaaped_id').val();
	
	var vartdate=varnid.split("-");
	var td=$(this).attr('start_date').split(".");
	//alert($(this).attr('start_date')+"=="+td[0]);
	var ttd=td[0].split("-");
	var ndur=((leftsize/30).toFixed(2)).split(".");
	var nhr=Math.floor(((ndur[1]*1)*10)/100);
	var new_dur=ndur[0]+"."+nhr;  //-(ndur[0]*2)
	//var new_dur= datediff(ttd[2]+"-"+ttd[1]+"-"+ttd[0], vartdate[2].substr(4, 4)+"-"+vartdate[2].substr(2, 2)+"-"+vartdate[2].substr(0, 2));
	//$(this).attr('start_hr',podtl[5]);
	$(this).attr('end_hr',nhr);
		
	$(this).attr('duration',new_dur);
	if(nhr<1)  ndur[0]= ndur[0]-1;
	var enddate=js_date_add( ttd[2]+"-"+ttd[1]+"-"+ttd[0], ndur[0] );
alert(leftsize+'=='+ndur[0]+'=='+ndur[1]+'=='+nhr+'=='+enddate);
	$(this).attr('enddate',enddate);//vartdate[2].substr(4, 4)+"-"+vartdate[2].substr(2, 2)+"-"+vartdate[2].substr(0, 2));
	
	//alert('asd'+varnid);return;
	var $button =$( "#plan_bar" ).clone();
	eval($('#bodycont').append($button));
	$($button).css( {
		'position': 'absolute',
		'left': lpos+'px',
		'top': $(this).attr('top_pos')+'px', //$(this).parent().offset().top
		'width': rightsize+'px',
		'z-index':500,
		'display':'block', 
	});
	k++;
	
	
	if(nhr<1)
	{
		var endate=enddate.split("-");
		var nstdate=js_date_add( endate[2]+"-"+endate[1]+"-"+endate[0], 1 );
	}
	else  var nstdate=enddate;
	 
	
	 
			$($button).attr('completed_px',0);
			$($button).attr('completed_qnty', 0);
			$($button).attr('width_px',rightsize);
			$($button).attr('left_pos',lpos);
			$($button).attr('top_pos',$(this).attr('top_pos'));
			$($button).attr('start_td_id',varnid);
			$($button).attr('plan_div_id',$(this).attr('plan_div_id')+k);
			$($button).attr('podtls',$(this).attr('podtls'));
			$($button).attr('id','mdvtdbody-'+$(this).attr('line_id')+"-"+vartdate[2].substr(0, 2)+""+vartdate[2].substr(2, 2)+""+vartdate[2].substr(4, 4)+""+k++);
			//mdvtdbody-8-301220146
		$($button).attr('compid',$(this).attr('compid'));
			$($button).attr('compstart',$(this).attr('compstart'));
			$($button).attr('compinc',$(this).attr('compinc'));
			$($button).attr('comptarg',$(this).attr('comptarg'));
			$($button).attr('po_id',$(this).attr('po_id'));
			$($button).attr('plan_id',$(this).attr('plan_id'));
			$($button).attr('line_id',$(this).attr('line_id'));
			
			$($button).attr('start_date',nstdate);
			$($button).attr('plan_qnty',new_plan_qnty);
			var dd=$($button).attr('start_date').split("-");
			var new_dur= datediff( dd[2]+"-"+dd[1]+"-"+dd[0], oldenddate );
	 
			$($button).attr('duration',new_dur);
			$($button).attr('enddate',oldenddate);
			
			$($button).attr('upd_id',0);
			$($button).attr('isedited','0');
			$($button).attr('isnew',1);
		 	
	fval=0;xpos=0;
							
});

// Freezed div split on double click
$('.testdiv_freeze').live("dblclick" , function(e) {
	var tid =this.id;
	var dt=$(this).position();
	$('#txt_overlaaped_id').val('');
	leftsize=0;
	var lpos=((e.clientX*1)+($(window).scrollLeft()*1));
	var leftsize=lpos -$(this).attr('left_pos');
	if((leftsize*1)<$(this).attr('completed_px')*1)
	{
		alert('You can not change plan of produced quantity');
		return false;
	}
	var oldwid=$(this).attr('width_px');
	var oldenddate=$(this).attr('enddate');
	
	sval=0;
	var rightsize=$(this).attr('width_px')-leftsize;
	var name=$(this).attr('start_td_id').split("x");
	$(this).width( leftsize );
	if( ( $(this).attr('completed_px')*1)<1  )
	{
		var left_color="9C8AE3";
	}
	else
	{
		var left_color="0AE7E1";
	}
	$(this).attr('width_px',leftsize);
	$(this).css( {
		'position': 'absolute',
		'top': $(this).attr('top_pos')+'px', //$(this).parent().offset().top
		'width': leftsize+'px',
		'z-index':500,
		'display':'block', 
		'background':' -moz-linear-gradient(left, #70BC3D '+$(this).attr('completed_px')+'px, #'+left_color+' ' +(leftsize-$(this).attr('completed_px'))+'px)',
	});
	$(this).attr('isedited','1');
	var new_old_plan_qnty=($(this).attr('plan_qnty')/oldwid)*leftsize;
	var new_plan_qnty=$(this).attr('plan_qnty')-new_old_plan_qnty;
	 
 	$(this).attr('plan_qnty',new_old_plan_qnty);
	
	
	// 2nd part of plan after split
	var tv=get_starting_td(lpos*1, ((e.clientY*1)+($(window).scrollTop()*1)) );
	var varnid=$('#txt_overlaaped_id').val();
	//alert(lpos+"=="+$(this).attr('top_pos')+"=="+e.clientX+"=="+e.clientY)
	var vartdate=varnid.split("-");
	
	var new_dur= datediff( $(this).attr('start_date'), vartdate[2].substr(4, 4)+"-"+vartdate[2].substr(2, 2)+"-"+vartdate[2].substr(0, 2));
	
	$(this).attr('duration',new_dur);
	$(this).attr('enddate',vartdate[2].substr(4, 4)+"-"+vartdate[2].substr(2, 2)+"-"+vartdate[2].substr(0, 2));
	
	var $button =$( "#plan_bar" ).clone();
	eval($('#bodycont').append($button));
	$($button).css( {
		'position': 'absolute',
		'left': lpos+'px',
		'top': $(this).attr('top_pos')+'px', //$(this).parent().offset().top
		'width': rightsize+'px',
		'z-index':500,
		'display':'block', 
	});
	k++;
	//$($button).attr('title',rightsize+"__"+lpos+"__"+''+name[0]+"x"+k+"__"+titlear[3]+"__"+tid+"__"+titlear[5]);
		$($button).attr('completed_px',0);
			$($button).attr('completed_qnty', 0);
			$($button).attr('width_px',rightsize);
			$($button).attr('left_pos',lpos);
			$($button).attr('top_pos',$(this).attr('top_pos'));
			$($button).attr('start_td_id',varnid);
			$($button).attr('plan_div_id',$(this).attr('plan_div_id')+k);
			$($button).attr('podtls',$(this).attr('podtls'));
			
		$($button).attr('compid',$(this).attr('compid'));
			$($button).attr('compstart',$(this).attr('compstart'));
			$($button).attr('compinc',$(this).attr('compinc'));
			$($button).attr('comptarg',$(this).attr('comptarg'));
			$($button).attr('po_id',$(this).attr('po_id'));
			$($button).attr('plan_id',$(this).attr('plan_id'));
			$($button).attr('line_id',$(this).attr('line_id'));
			$($button).attr('id','mdvtdbody-'+$(this).attr('line_id')+"-"+vartdate[2].substr(0, 2)+""+vartdate[2].substr(2, 2)+""+vartdate[2].substr(4, 4)+""+k++);
			
			$($button).attr('start_date',vartdate[2].substr(0, 2)+"-"+vartdate[2].substr(2, 2)+"-"+vartdate[2].substr(4, 4));
			$($button).attr('plan_qnty',new_plan_qnty);
			var dd=$($button).attr('start_date').split("-");
			var new_dur= datediff( dd[2]+"-"+dd[1]+"-"+dd[0], oldenddate );
	 
			$($button).attr('duration',new_dur);
			$($button).attr('enddate',oldenddate);
			
			$($button).attr('upd_id',0);
			$($button).attr('isedited','0');
			$($button).attr('isnew',1);
		 	
	fval=0;xpos=0;
							
});

//Resizable
$('.testdiv').live("mouseover" , function(e) {
	//var wid=$(this).attr('title');
	//var wid=wid.split("__");
	
	$('.testdiv').resizable({
			handles: 'e',
			minWidth: $(this).attr('width_px'),
			maxHeight:15,
			minHeight:15,
			helper: false,
			stop: function(event, ui) {
				var width = $(this).outerWidth();
				 $(this).css( {
								'position': 'absolute',
								'left': $(this).attr('left_pos')+'px',
								'top': $(this).attr('top_pos')+'px', //$(this).parent().offset().top
								'width': width+'px',
								'z-index':500,
								'display':'block', 
							});
						
							var dur=$(this).attr('duration');
							var stdate=$(this).attr('start_date').split(".");
							var std=stdate[0].split("-");
							var newdur=(dur/$(this).attr('width_px'))*width;
							newdur=newdur.toFixed(2);
							newdur=newdur.split(".");
							
							$(this).attr('isedited','1'); 
							var newdate=js_date_add( std[2]+"-"+stdate[1]+"-"+stdate[0], newdur[0] )+"."+Math.ceil(newdur[1]);
							$(this).attr('enddate',newdate);
							$(this).attr('duration',newdur[0]+"."+Math.ceil(newdur[1]));
							
							$(this).attr('width_px',width);
							 
			
			}
	 });
});
  
// Dragable
$('.testdiv').live("mouseenter" , function(e) {
	var bwid=$('#bodycont').width();
	var bhet=$('#bodycont').height();
	
	$( ".testdiv" ).draggable(
						 {
							 //containment: "verticalStripes1",
							 containment: [ 185, 95, bwid, bhet ],
							// Find original position of dragged box.
							start: function(event, ui) {
								// Show start dragged position of box.
								///var Startpos = $(this).position();
								$(this).removeAttr('class');
								$(this).attr('class','now_moving');
								//$('#display_pos').html(Startpos);
								
							},
							// Find position where box is dropped.
							stop: function(event, ui) {
								$(this).removeAttr('class');
								$(this).attr('class','testdiv');
								var Stoppos = $(this).position();
								 $('#txt_overlaaped_id').val('');
								if( check_overlapping( $(this).attr('id') )==false)
								{
									//	var el=document.elementFromPoint(Stoppos.left, Stoppos.top) ;
									//var el = document.elementFromPoint(Stoppos.left, Stoppos.top);
	 								
									var tv=get_starting_td( Stoppos.left, Stoppos.top );
									if($('#txt_overlaaped_id').val()=="")
									{
										var tv=get_starting_td( (Stoppos.left*1)+3, (Stoppos.top*1)+3 );
									}
									if($('#txt_overlaaped_id').val()=="")
									{
										var tv=get_starting_td_offday( (Stoppos.left*1)+3, (Stoppos.top*1)+3 );
									}
									
									var varnid=$('#txt_overlaaped_id').val();
									//alert(varnid)
			
									if(varnid!=''){ var offset = $('#'+varnid).offset(); vtop=offset.top; var vleft=offset.left; } else vtop=Stoppos.top;
									var dif=vleft-Stoppos.left;
									Stoppos.left=Stoppos.left+dif*1;
									$(this).css( {
										'position': 'absolute',
										'left': Stoppos.left+'px',
										'top': vtop+'px', //$(this).parent().offset().top
										'width': $(this).attr('width_px')+'px',
										'z-index':500,
										'display':'block', 
									});
									var frac=Stoppos.left-offset.left;
									frac=(10/30)*frac;
									var vid=varnid.split("-");
									//$(this).attr('line_id',podtl_dtls[6]);
									$(this).attr('isedited','1');
									$(this).attr('line_id',vid[1]);
									$(this).attr('start_date',vid[2].substr(0, 2)+"-"+vid[2].substr(2, 2)+"-"+vid[2].substr(4, 4)+"."+Math.ceil(frac));
									
									$(this).attr('width_px',$(this).attr('width_px'));
									$(this).attr('left_pos',Stoppos.left);
									$(this).attr('top_pos',vtop);
									$(this).attr('start_td_id',varnid);
									$(this).attr('plan_div_id',$(this).attr('plan_div_id'));
									$(this).attr('podtls',$(this).attr('podtls'));
								}
								else
								{
									$(this).css( {
										'position': 'absolute',
										'left': $(this).attr('left_pos')+'px',
										'top': $(this).attr('top_pos')+'px', //$(this).parent().offset().top
										'width': $(this).attr('width_px')+'px',
										'z-index':500,
										'display':'block', 
									});
									//$(this).attr('title', prev);
								}
							}
						});
						 
 });


