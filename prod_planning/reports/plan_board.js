// Overlapped plan move   1280


//https://www.youtube.com/watch?v=OnUjyHeGG_E   common sense
var cdate="";
var k=0;
var complexity_type=1;
function context_menu_operation(  tid, operation  )
{
	if( operation=='new' )
	{
		var td=tid.split("-");
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/machine_wise_knitting_plan_controller.php?action=booking_item_details_popup&company_id='+$('#cbo_company_name').val()+'&location_id='+$('#cbo_location_name').val()+'&pmachine='+td[1]+'&pdate='+td[2]+'&floor_id='+$('#cbo_floor_id').val(), 'Planning Info Entry', 'width=1300px,height=480px, center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_data");
			
			if(theemail.value!="")
			{
				// alert(theemail.value)
				var tmp=theemail.value;
				 
				var tmpd=tmp.split("______");
				var podt=tmpd[0].split("_");
				var pln=tmpd[1].split("****");
			    //alert(pln.length)
				for(var m=0; m<pln.length; m++)
				{
					//var plength=Number( get_production_days( tmpd[2], (npln[2]-(npln[7]*1)), npln[1] ,npln[6], npln[0] ));
					
					var nshp=js_date_add( shp[2]+"-"+shp[1]+"-"+shp[0], 0 );
					var shp=nshp.split("-");
				    
					var podtls="0**"+npln[0].replace('_', '')+"**"+podt[1]+"**0**"+npln[1]+"**0**"+npln[1]+"**0**"+plength+"**"+npln[2]+"**"+comp[0]+"**"+comp[1]+"**"+comp[2]+"**"+comp[3]+"**0**"+npln[1]+"**"+npln[1]+"**"+podt[4]+"**"+podt[7]+"**"+podt[8]+"**"+podt[9]+"**"+podt[10]+"**"+npln[6]+"**"+podt[10]+"**"+shp[2]+""+shp[1]+""+shp[0]+"**"+podt[13]+"**"+podt[7]+"**"+podt[6]+"**"+npln[7]+"**"+npln[8];
					
					
					//tdbody-10-25052016
					draw_chart( npln[0].replace('_', ''), npln[1], plength.toFixed(1),podtls, npln[2],0 );
				}
			}
		}
	}
}

var off_day_array=''; 
var prodcution_data=new Array();	
var prodcution_data_sum=new Array(); 
function populate_line()
{	
	var fval=0;
	var sval=0;
	var fcolind=0;
	var scolind=0;
	var frowind=0;
	var srowind=0;
	var xpos=0;
	var stdid="";
	var leventType="";
	var leventstart="";
	var leventend="";
	
	var leventnstart="";
	var leventnend="";
	var leventduration="";
	var leventstartnew="";
	//var leventType="";
	//var include_offday_production=0;
	var pln_grp_indx=new Array;
	var movementTimer = null;
	pln_grp_indx.push('0');


	$('#operation_closed').val('');
	var vd=$('#txt_start_date').val().replace('-', ''); var cdate=vd.replace('-', '') ;
	$('#txt_cdate').val(cdate);
	var is_locked=0;
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
	if($('#cbo_floor_name').val()==0)
	{
		is_locked=1;
		alert('Board will be viewed as read-only mode. You can not edit.');
		//return;
		
	}
		
	$( '#cbo_company_mst' ).attr('disabled',true);
	$( '#cbo_location_name' ).attr('disabled',true);
//	$( '#cbo_floor_name' ).attr('disabled',true);
	
	var data=$('#cbo_company_mst').val()+"__"+$('#cbo_location_name').val()+"__"+$('#txt_start_date').val()+"__"+$('#current_menu').val()+"__"+$('#cbo_floor_name').val()+"__"+is_locked;
	
	show_list_view(data,'show_plan_details','plan_container','requires/planning_board_controller','');
	
	prodcution_data.length=0;
	off_day_array=$('#off_day_data').val();
	
	$('#off_day_data').val('');
	/*if($('#old_plan_data').val()=='')
		var old_plan_data=$('#old_plan_data').val();
	 else
	 	var old_plan_data="0**__**0";
		*/
	var old_plan_data=$('#old_plan_data').val();
	$('#old_plan_data').val('');
	
	var old_plan_data_arr=old_plan_data.split("**__**");
	
	var dd=$('#txt_production_data').val();
	prodcution_data = JSON.parse(dd);
	
	var dddat=$('#txt_production_summary').val();
	prodcution_data_sum = JSON.parse(dddat);
	
	
	/*alert(prodcution_data[138][4922]['19-03-2015']);
	return;
	var tmpprd=dd.split("**");
	$('#txt_production_data').val('');
	for(var n=0; n<tmpprd.length; n++)
	{
		var tmp=tmpprd[n].split("_");
		prodcution_data[tmp[0]]=tmp[1];
	}*/
	
	for(var l=0; l<old_plan_data_arr.length; l++)
	{
		if( old_plan_data_arr[l]!="")
		{
			var tmp=old_plan_data_arr[l];
			var tmpd=tmp.split("**");
			
			draw_chart( tmpd[1], tmpd[4], tmpd[8], old_plan_data_arr[l], tmpd[9], tmpd[22] );	
		}
	}
	
}

function fnc_release_board()
{
	//alert('Please wait. Releasing Board....');
	return_global_ajax_value( $('#current_menu').val(), 'release_board_function', '', 'requires/planning_board_controller');
	alert('Board Released.....');
}

function fnc_save_planning( )
{
	//$('#operation_closed').val('');
	
	if( $('#operation_closed').val()!='')
	{
		alert('This board is Saved. Please Reload page again.');
		return;
	}
	
	if( $('#txt_lock_operation').val()!=0)
	{
		alert('This board Locked by another user. Please wait until released.');
		return;
	}
	
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
	var today_plan_qnty="";
	var item_name_id="";
	var off_day_plan="";
	var order_complexity="";
	var tmpshipdate="";
	var extra_param='';
	var is_next=new Array();
	$( "#plan_container .verticalStripes1_plan" ).each(function() {
		
		if( is_next[$(this).attr('plan_group')]==undefined )
		{
			 if( today_plan_qnty=="" ) today_plan_qnty=$(this).attr('today_plan_qnty'); else today_plan_qnty=today_plan_qnty+"**"+$(this).attr('today_plan_qnty');
			if( total_plan==0)
			{
				
				poid=$(this).attr('po_id');
				cmpid=$(this).attr('compid');
				cmpstart=$(this).attr('compstart');
				cmpinc=$(this).attr('compinc');
				cmptarg=$(this).attr('comptarg');
				lineid=$(this).attr('line_id');
				var startdt=$(this).attr('start_date').split(".");
				var tmppodtls=$(this).attr('podtls').split("**");
				tmpshipdate=tmppodtls[24];
				extra_param=tmppodtls[25]+"__"+tmppodtls[26]+"__"+tmppodtls[27]+"__"+tmppodtls[28]+"__"+tmppodtls[29];
			//	0**165**6036**1**05-01-2016****undefined**0**6**2300**0**500**0**500**0**05-01-2016**05-01-2016**000012**400,400,400,400,400,300**5
			//365**169**6036**365**19-12-2015****02-01-2016**0**15**5000**0**10,50,80****500**0**19-12-2015**02-01-2016**000012**50,250,400,400,400,400,0,400,400,400,400,400,400,400,300**5**1**1**2**2**20160227
				startdate=startdt[0]; //porest[2].substr(4, 4)+"-"+porest[2].substr(2, 2)+"-"+porest[2].substr(0, 2);
				starttime=startdt[1]; //porest[2].substr(4, 4)+"-"+porest[2].substr(2, 2)+"-"+porest[2].substr(0, 2);
				duratin=$(this).attr('duration');
				planqty=$(this).attr('plan_qnty');
				enddate=$(this).attr('plan_end_date');
				updid=$(this).attr('upd_id');
				planid=$(this).attr('plan_id');
				isnew=$(this).attr('isnew');
				isedited=$(this).attr('isedited');
				item_name_id=$(this).attr('item_name_id');
				off_day_plan=$(this).attr('off_day_plan');
				order_complexity=$(this).attr('order_complexity');
			}
			else
			{
				
				var tmppodtls=$(this).attr('podtls').split("**");
				tmpshipdate=tmpshipdate+"**"+tmppodtls[24];
				
				extra_param=extra_param+"**"+tmppodtls[25]+"__"+tmppodtls[26]+"__"+tmppodtls[27]+"__"+tmppodtls[28]+"__"+tmppodtls[29];
				
				
				poid=poid+"**"+$(this).attr('po_id');
				cmpid=cmpid+"**"+$(this).attr('compid');
				cmpstart=cmpstart+"**"+$(this).attr('compstart');
				cmpinc=cmpinc+"**"+$(this).attr('compinc');
				cmptarg=cmptarg+"**"+$(this).attr('comptarg');
				lineid=lineid+"**"+$(this).attr('line_id');
				
				var startdt=$(this).attr('start_date').split(".");
				
				startdate=startdate+"**"+startdt[0]; //porest[2].substr(4, 4)+"-"+porest[2].substr(2, 2)+"-"+porest[2].substr(0, 2);
				starttime=starttime+"**"+startdt[1];
				
				duratin=duratin+"**"+$(this).attr('duration');
				planqty=planqty+"**"+$(this).attr('plan_qnty');
				enddate=enddate+"**"+$(this).attr('plan_end_date');
				updid=updid+"**"+$(this).attr('upd_id');
				planid=planid+"**"+$(this).attr('plan_id');
				isnew=isnew+"**"+$(this).attr('isnew');
				isedited=isedited+"**"+$(this).attr('isedited');
				item_name_id=item_name_id+"**"+$(this).attr('item_name_id');
				off_day_plan=off_day_plan+"**"+$(this).attr('off_day_plan');
				order_complexity=order_complexity+"**"+$(this).attr('order_complexity');
			}
			total_plan++;
			is_next[$(this).attr('plan_group')]=$(this).attr('plan_group');
		}
		else
		{
			today_plan_qnty=today_plan_qnty+","+$(this).attr('today_plan_qnty');
		}
	});
	 
	var operation=0;
	var data="action=save_update_delete&operation="+operation+"&num_row="+total_plan+"&poid="+poid+"&cmpid="+cmpid+"&cmpstart="+cmpstart+"&cmpinc="+cmpinc+"&cmptarg="+cmptarg+"&lineid="+lineid+"&startdate="+startdate+"&duratin="+duratin+"&planqty="+planqty+"&enddate="+enddate+"&planid="+planid+"&updid="+updid+"&isnew="+isnew+"&isedited="+isedited+"&today_plan_qnty="+today_plan_qnty+'&all_deleted_ids='+$('#deleted_id').val()+'&cbo_company_mst='+$('#cbo_company_mst').val()+'&cbo_location_name='+$('#cbo_location_name').val()+'&item_name_id='+item_name_id+'&off_day_plan='+off_day_plan+'&order_complexity='+order_complexity+'&tmpshipdate='+tmpshipdate+'&extra_param='+extra_param;
	 
	//freeze_window(operation);  +get_submitted_data_string('cbo_location_name*cbo_company_mst',"../")
	http.open("POST","requires/planning_board_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_save_planning_response;
}

function fnc_save_planning_response()
{
	if(http.readyState == 4) 
	{
		var response=trim(http.responseText).split('**');
	 	$('#operation_closed').val(http.responseText);
		$('#deleted_id').val('');
		alert(http.responseText);
		//show_msg( response[0] );
		
		//release_freezing();
	}
}

var fval=0;
var sval=0;
var fcolind=0;
var scolind=0;
var frowind=0;
var srowind=0;
var xpos=0;
var stdid="";
var leventType="";
var leventstart="";
var leventend="";

var leventnstart="";
var leventnend="";
var leventduration="";
var leventstartnew="";
//var leventType="";
//var include_offday_production=0;
var pln_grp_indx=new Array;
var movementTimer = null;

Array.max = function( array ){
				return Math.max.apply( Math, array );
			};
pln_grp_indx.push('0');

function draw_chart( line, stdate, wid, podtls, planqnty, eupdid )
{
 	//if( pln_grp_indx=='-Infinity' ) pln_grp_indx.push('0');
	
	var tmpdate=stdate;
	if( !eupdid ) var eupdid=0;
	cdate=$('#txt_date_from').val();
	var podtl=podtls.split("**");
	var vd=stdate;//podtl[4].replace('-', ''); 
	var regex = new RegExp('-', 'g');
	var stdate=vd.replace(regex, '');
	var stdid='tdbody-'+ line+"-"+stdate;
	
	var chkeddate=cdate.substr(4,4)+""+cdate.substr(2,2)+""+cdate.substr(0,2); 
	var is_prod=0;
	var tdate=stdate;
	var vd=cdate.replace('-', ''); 
	var ptdate=vd.replace('-', '');
	var wid=Number(Math.ceil(wid))+".0";
	
	var planned_qnty =0;
	var remain_qnty =0;
	if( wid==0 ) alert( 'Production days not available. Please check.' );
	
	if( ( podtl[0]*1)>0) { k=podtl[0]; pln_grp_indx.push(podtl[0]); } 
	else { 
		
		k=(Array.max(pln_grp_indx))+1;
		pln_grp_indx.push(k);
	}
	//if( trim(podtl[0])==5412) alert(prodcution_data_sum[podtl[0]]);
	//if( podtl[2]==5412) alert(prodcution_data[podtl[1]])
	var ymd1=tmpdate.split("-"); var ymd=ymd1[2]+"-"+ymd1[1]+"-"+ymd1[0];
	var dpos=wid.indexOf(".");
	
	var capacity=$('#'+stdid).attr('capacity');
	var days_req=(planqnty*1/capacity*1).toFixed(2);
	//alert(days_req);
	wid=days_req;
	var ceil_duration=Math.ceil(wid)-1;
	var end_date=js_date_add(ymd,ceil_duration);
	
	var planDtls=podtl[0]+"**"+podtl[1]+"**"+podtl[2];
	var totalPlan=0;
	
	for( var i=0; i<Math.ceil(wid); i++ )
	{
		var d=js_date_add( ymd, i );
		var tdd=d.split("-");
		var tdds=tdd[2]+""+tdd[1]+""+tdd[0];
		var vd=d.replace('-', ''); var vdt=vd.replace('-', '');
		
		$('#tdbody-'+ line+"-"+vdt).attr('class','verticalStripes1 verticalStripes1_plan');
		if(i==0)
		{
			$('#tdbody-'+ line+"-"+vdt).css('border-radius', '50% 0 0 50%');
		}

		$('#tdbody-'+ line+"-"+vdt).attr('plan_qnty',planqnty);
		$('#tdbody-'+ line+"-"+vdt).attr('plan_group',1);
		$('#tdbody-'+ line+"-"+vdt).attr('planDtls',planDtls);
		$('#tdbody-'+ line+"-"+vdt).attr('duration',wid);
		$('#tdbody-'+ line+"-"+vdt).attr('dtls_id',podtl[2]);
		$('#tdbody-'+ line+"-"+vdt).attr('start_date',tmpdate);
		$('#tdbody-'+ line+"-"+vdt).attr('end_date',end_date);
		$('#tdbody-'+ line+"-"+vdt).attr('start_td_id',tmpdate);

		if(eupdid>0)
		{
			$('#tdbody-'+ line+"-"+vdt).attr('isedited',1);
			$('#tdbody-'+ line+"-"+vdt).attr('isnew',0);
			$('#tdbody-'+ line+"-"+vdt).attr('upd_id',eupdid);
			$('#tdbody-'+ line+"-"+vdt).attr('plan_id',eupdid);
		}
		else
		{
			$('#tdbody-'+ line+"-"+vdt).attr('isedited',0);
			$('#tdbody-'+ line+"-"+vdt).attr('isnew',1);
			$('#tdbody-'+ line+"-"+vdt).attr('upd_id','');
			$('#tdbody-'+ line+"-"+vdt).attr('plan_id');
		}
		
		var is_partial=0;
		if( i==ceil_duration )
		{
			var today_plan_qnty=planqnty*1-totalPlan*1;
			if(capacity*1>today_plan_qnty*1)
			{
				var is_partial=1;
			}
		}
		else
		{
			var today_plan_qnty=capacity;
		}
		
		totalPlan = totalPlan*1+today_plan_qnty*1;
		
		$('#tdbody-'+ line+"-"+vdt).attr('is_partial',is_partial);
		$('#tdbody-'+ line+"-"+vdt).attr('today_plan_qnty',today_plan_qnty);
	}
}

function event_select( selid ) // This function makes select and de-select any plan on click event
{
	if( $('#last_event_id').val()!="")
	{
		$('td[name="'+$('#last_event_id').val()+'"]').each(function() {
			if( $(this).hasClass( "verticalStripes_off" ).toString()=='true' )
				$(this).css('border-bottom-color', '#FFFFA8');
			else
				$(this).css('border-bottom-color','#F5F5F5' );
		});
	}
	$('#last_event_id').val($('#'+selid).attr('name'))
	
	var tname=$('#'+selid).attr('name');
	$('td[name="'+tname+'"]').attr('bgcolor','FF3366');
	
	$('td[name="'+tname+'"]').each(function() {
		$(this).css('border-bottom-color','#F2B7E2' );
	});
	
	return;
}

function clear_td( cleartid, desttd, is_delete, inc )
{
	$('#'+cleartid).removeAttr('class').attr('class','verticalStripes1');
	$('#'+cleartid).removeAttr('plan_group').attr('plan_group','0');
	$('#'+cleartid).removeAttr('plan_id');
	$('#'+cleartid).removeAttr('start_date');
	$('#'+cleartid).removeAttr('duration');
	$('#'+cleartid).removeAttr('isedited');
	$('#'+cleartid).removeAttr('is_partial');
	$('#'+cleartid).removeAttr('is_overlapped');
	$('#'+cleartid).removeAttr('isnew');
	$('#'+cleartid).removeAttr('style');
	$('#'+cleartid).removeAttr('plandtls');
	$('#'+cleartid).removeAttr('plan_qnty');
	$('#'+cleartid).removeAttr('upd_id');
	$('#'+cleartid).removeAttr('end_date');
	$('#'+cleartid).removeAttr('today_plan_qnty');
	$('#'+cleartid).removeAttr('dtls_id');
}

function set_old_plan_attr( dur, lstid )
{
	var clkdt=lstid.split("-");
	var pln_grp=$('#'+ lstid).attr('plan_group');
	var podtlss=$('#'+ lstid).attr('plandtls'); 
	//alert(podtlss)
	var pod=podtlss.split("**");
	
	var pln_qnty=$('#'+ lstid).attr('plan_qnty')-copied_plan_qnty;		
			
	for( var i=1; i<=dur; i++ ) // Set prev plan new options
	{
		var ymd=clkdt[2].substr(4,4)+"-"+clkdt[2].substr(2,2)+"-"+clkdt[2].substr(0,2);
		var cldte=js_date_add( ymd, -i )
		var vd=cldte.replace('-', ''); var clkdte=vd.replace('-', '');
		if(i==1)
		{  
			var endt=cldte; 
			var du=dur-ncopy_plan_length;
			var stdate=js_date_add( ymd, -du )
			var podtl=pod[0]+"**"+pod[1]+"**"+pod[2]+"**"+pod[3];
		}
		
		if( pln_grp==$('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('plan_group') )
		{
			$('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('planDtls',podtl);
			$('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('plan_qnty',pln_qnty);
			$('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('duration',du);
			$('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('isedited','1');
			$('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('end_date',endt);
		}
		else break ;
	}
}

var act_copy_plan_length=0;
var ncopy_plan_length=0;

function paste_plan( pasteid )
{
	if( clicked_id=="" ) return;
	
	leventduration='';
	var vid=clicked_id;
	clicked_id="";
	var pln_grp=$('#'+vid).attr('plan_group');
	var duration= String($('#'+vid).attr('duration'));
	
	duration=duration.split(".");
	if(pln_grp==0) return;
	k=(Array.max(pln_grp_indx))+1;
	pln_grp_indx.push(k);
	ncopy_plan_length=0;
	var clkdt=vid.split("-");
	ncopy_plan_length=copy_plan_length;
	//copy_plan_length='';
	var is_overlapped=false;
	var available_days=0;
	var did=pasteid.split("-");
	
	act_copy_plan_length=copy_plan_length;
	if( leventType=='cut' )
	{
		set_old_plan_attr( duration[0], vid );
	}

	paste_replan_plan( pasteid, vid, 0 );
	return;
}

function paste_replan_plan( pasteid, clicked_id, prev_settings )
{
	if( !prev_settings ) var prev_settings=0;
	//  alert( copied_plan_qnty+"="+copy_plan_length+"="+act_copy_plan_length+"="+act_copy_plan_length)
	if( leventType=='cut' )
	{ //alert( leventType )
		var duration= copy_plan_length;
		var plan_qnty= copied_plan_qnty;
		k=(Array.max(pln_grp_indx))+1;
		pln_grp_indx.push(k);
		var updid=0;
		var clear_length=copy_plan_length;
	}
	else
	{
		var duration= $('#'+clicked_id).attr('duration');
		var plan_qnty= $('#'+clicked_id).attr('plan_qnty');
		var updid=$('#'+clicked_id).attr('upd_id');
		var clear_length=duration;
	}
	 	
	var podtls= $('#'+clicked_id).attr('plandtls');
	var tmppo=podtls.split("**");
	 
	var did=clicked_id.split("-");
	var ymd=did[2].substr(4,4)+"-"+did[2].substr(2,2)+"-"+did[2].substr(0,2);
	var off_days_list=0;
	var durationold=duration;
	
	var new_id=pasteid;
	
	var did=new_id.split("-");
	var dmy=did[2].substr(0,2)+"-"+did[2].substr(2,2)+"-"+did[2].substr(4,4);
	var ymd=did[2].substr(4,4)+"-"+did[2].substr(2,2)+"-"+did[2].substr(0,2);
	 
	var vid=clicked_id;
	var didw=vid.split("-");
	var ymd=didw[2].substr(4,4)+"-"+didw[2].substr(2,2)+"-"+didw[2].substr(0,2);
	var off_days_list=0;
	//  alert( clear_length +"="+ act_copy_plan_length+"="+duration );
	for( var i=0; i<clear_length; i++ )
	{
		var cldte=js_date_add( ymd, i )
		var vd=cldte.replace('-', ''); var clkdte=vd.replace('-', '');
		//clear_td( cleartid, desttd, is_delete, inc )
		clear_td( "tdbody-"+ didw[1] +"-"+ clkdte, '', 1, i );
	}

	draw_chart( did[1], dmy, duration, podtls, plan_qnty,updid );
	clicked_id='';
}

var vstart='';
var vend='';
var stdid='';
function paste_data( nid, oid, inc, plan )
{
	var cldt=nid.split("-");
  	var ymd=cldt[2].substr(4,4)+"-"+cldt[2].substr(2,2)+"-"+cldt[2].substr(0,2);
	
	var cldte=js_date_add( ymd, inc )
	var vd=cldte.replace('-', ''); var stdate=vd.replace('-', '');
	if( inc==0 )
	{
		vstart=cldte;
		vend=js_date_add( ymd, act_copy_plan_length-1 );
		stdid='tdbody-'+ cldt[1]+"-"+stdate;
		leventstartnew=stdid;
	}
	leventnend='tdbody-'+ cldt[1]+"-"+stdate;
	leventend=oid;
	
	if(leventType=="cut")
	{
		$('#tdbody-'+ cldt[1]+"-"+ stdate).attr('upd_id','0');
		$('#tdbody-'+ cldt[1]+"-"+ stdate).attr('isedited','0');
		$('#tdbody-'+ cldt[1]+"-"+ stdate).attr('isnew','1');
		$('#tdbody-'+ cldt[1]+"-"+ stdate).attr('plan_group',k);
	}
	else if(leventType=="move")
	{
		$('#tdbody-'+ cldt[1]+"-"+ stdate).attr('upd_id',$('#'+ oid).attr('upd_id'));
		$('#tdbody-'+ cldt[1]+"-"+ stdate).attr('isedited','1');
		$('#tdbody-'+ cldt[1]+"-"+ stdate).attr('isnew','0');
		$('#tdbody-'+ cldt[1]+"-"+stdate).attr('plan_group',$('#'+ oid).attr('plan_group'));
	}
		
	clear_td( oid, 'tdbody-'+ cldt[1]+"-"+stdate, 0, inc );
	
	$('#tdbody-'+ cldt[1]+"-"+stdate ).attr('title','Plan Start Date:: '+vstart+' ,Plan End Date:: '+vend);
	
	$('#tdbody-'+ cldt[1]+"-"+stdate ).attr('start_td_id',stdid);
	$('#tdbody-'+ cldt[1]+"-"+stdate ).attr('start_date',vstart);
	$('#tdbody-'+ cldt[1]+"-"+stdate ).attr('plan_end_date',vend);
	$('#tdbody-'+ cldt[1]+"-"+stdate ).attr('duration',act_copy_plan_length);
	$('#tdbody-'+ cldt[1]+"-"+stdate ).attr('plan_id','');
	$('#tdbody-'+ cldt[1]+"-"+stdate ).attr('line_id',cldt[1]);
}

var clicked_id="";
var copy_plan_length='';
var copied_plan_qnty='';
var copied_qnty_arr=new Array;

var revise_adjacent_prev_plan=0;
var revise_adjacent_next_plan=0;
var revise_adjacent_prev_qnty=0;
var revise_adjacent_next_pl=0;
function clear_overlapped_plan()
{
	if( revise_adjacent_prev_plan!=0 )
	{
		var podtls= $('#'+revise_adjacent_prev_plan).attr('podtls');
		var tmppo=podtls.split("**");
		var prdqnty=( $('#'+ revise_adjacent_prev_plan).attr('today_production')*1);
		var plnqnt=( $('#'+revise_adjacent_prev_plan).attr('today_plan_qnty')*1);
		
		if( tmppo[14]==1)
			var ccol='_crossed';
		else
			var ccol='_plan';
		if( prdqnty!=undefined )
		{
			if( ( prdqnty*1)>0 )
				var ccol='_produced';
				is_prod=1;
		}
		var plan_sts=0;
		$('#'+ prev_date_b4).css('background','');
		$('#'+ revise_adjacent_prev_plan).css('background','');
		
		$('#'+ revise_adjacent_prev_plan ).addClass('verticalStripes1'+ccol) //oclass+""+ccol
		$('#'+ revise_adjacent_prev_plan ).css('background-color',$('#'+ prev_date_b4 ).css('background'));
			if( plnqnt>prdqnty )
				$('#'+ revise_adjacent_prev_plan).html( "<font color='#000000' >"+plnqnt+"</font><br> <font color='#FF0000' >"+prdqnty+"</font>");
			else
				$('#'+ revise_adjacent_prev_plan).html( "<font color='#000000' >"+plnqnt+"</font> <br><font color='#0000FF' >"+prdqnty+"</font>");
	}

	if( revise_adjacent_next_pl!=0 )
	{
		revise_adjacent_next_plan=revise_adjacent_next_pl;
		/*var clkdt=revise_adjacent_next_plan.split("-");
		
		var podtls= $('#'+revise_adjacent_next_plan).attr('podtls');
		var pod=podtls.split("**");
		
		var ymd=clkdt[2].substr(4,4)+"-"+clkdt[2].substr(2,2)+"-"+clkdt[2].substr(0,2);
		var start_date=js_date_add( ymd, -1 )
		var start_dt=start_date.split("-"); 
		var cc=Number(get_production_days( pod[10]+"**"+pod[11]+"**"+pod[12]+"**"+pod[13]+"**"+pod[14], $('#'+revise_adjacent_next_plan).attr('plan_qnty') , start_dt[0]+"-"+start_dt[1]+"-"+start_dt[2], $('#'+revise_adjacent_next_plan).attr('off_day_plan'), clkdt[1] ));
		var endt=js_date_add( start_dt[2]+"-"+start_dt[1]+"-"+start_dt[1], 5-1 );
		
		var podtl=pod[0]+"**"+pod[1]+"**"+pod[2]+"**"+pod[3]+"**"+pod[4]+"**"+pod[5]+"**"+endt+"**"+pod[7]+"**"+pod[8]+"**"+pod[4]+"**"+pod[10]+"**"+pod[11]+"**"+pod[12]+"**"+pod[13]+"**"+pod[14]+"**"+pod[15]+"**"+endt +"**"+pod[17]+"**"+pod[18]+"**"+pod[19]+"**"+pod[20]+"**"+pod[21]+"**"+pod[22]+"**"+pod[23]+"**"+pod[24]+"**"+pod[25]+"**"+pod[26]+"**"+pod[27]+"**"+pod[28];
		alert(revise_adjacent_next_pl);
		draw_chart( clkdt[1], $('#'+revise_adjacent_next_plan).attr('plan_qnty'), cc, podtl, start_date,  $('#'+revise_adjacent_next_plan).attr('off_day_plan'), $('#'+revise_adjacent_next_plan).attr('upd_id') );
		 */
		
		leventType='forwards';
		var days =-1;// prompt("Please enter Days", "Forward Days");
		 
		last_operation='forwards';
		forwd_days=days;
		event_check_copy_forward( revise_adjacent_next_plan, days,1 ); 
					
	}
	//alert(revise_adjacent_next_pl);
	revise_adjacent_next_plan=0;
	revise_adjacent_next_pl=0;
	revise_adjacent_prev_plan=0;
}

var prev_date_b4='';
function check_overlapped_plan( chkid )
{
	var podtlsprv= $('#'+chkid).attr('planDtls');
	var tmppool=podtlsprv.split("**");
	
	var clkdtolp=chkid.split("-");
	
	var endd=$('#'+chkid).attr('plan_end_date').split("-");
	var ymd=endd[2]+"-"+endd[1]+"-"+endd[0];
	var next_date=js_date_add( ymd, 1 )
	var vd=next_date.replace('-', ''); var next_date=vd.replace('-', '');
	var podtlsnxt= $('#tdbody-'+clkdtolp[1]+"-"+next_date).attr('podtls');
	if(podtlsnxt!=undefined)
	{
		var podtlsnxt=podtlsnxt.split("**");
		if( (podtlsnxt[28]*1)>0 )  {  revise_adjacent_next_plan=$('#tdbody-'+clkdtolp[1]+"-"+next_date).attr('id'); revise_adjacent_next_pl=$('#tdbody-'+clkdtolp[1]+"-"+next_date).attr('id'); }
	}
	 
	//alert( revise_adjacent_next_pl +"=="+revise_adjacent_next_plan)
	
	var startd=$('#'+chkid).attr('start_date').split("-");
	var ymd=startd[2]+"-"+startd[1]+"-"+startd[0];
	
	var prev_date=js_date_add( ymd, -1 )
	prev_date_b4=js_date_add( ymd, -2 )
	// alert(prev_date+'='+prev_date_b4)
	var vd=prev_date_b4.replace('-', ''); var prev_date_b4=vd.replace('-', '');
	prev_date_b4='tdbody-'+clkdtolp[1]+"-"+prev_date_b4;
	
	var vd=prev_date.replace('-', ''); var prev_date=vd.replace('-', '');
	var podtlsprv= $('#tdbody-'+clkdtolp[1]+"-"+prev_date).attr('podtls');
	if( podtlsprv!=undefined )
	{
		var podtlsprv=podtlsprv.split("**");
		if( (tmppool[28]*1)>0 )  revise_adjacent_prev_plan=$('#tdbody-'+clkdtolp[1]+"-"+prev_date).attr('id');
		revise_adjacent_prev_qnty=podtlsprv[28];
	}
	if( leventType=='cut' )  { revise_adjacent_prev_plan=0; revise_adjacent_prev_qnty=0;}
	// alert( revise_adjacent_prev_plan+"=="+revise_adjacent_next_plan )
	//plan_end_date  start_date
}

function event_check( copyid ) // on cut
{
	clicked_id=copyid;
	leventstart=copyid;
	var pln_grp=$('#'+copyid).attr('plan_group');
	var duration= $('#'+copyid).attr('duration');
	var is_overlapped=$('#'+copyid).attr('is_overlapped');
	if(is_overlapped==1)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/machine_wise_knitting_plan_controller.php?action=overlapped_popup&overlapped_data='+$('#'+copyid).attr('overlapped_data'), 'Planning Info Entry', 'width=370px,height=300px, center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var plan_ids=this.contentDoc.getElementById("hidden_plan_ids").value;
			if(plan_ids!="")
			{
				var machinePlanId=plan_ids.split(',');
				//check_overlapped_plan( copyid );
				duration=duration.split(".");
				var clkdt=copyid.split("-");
				copy_plan_length=0;
				copied_plan_qnty=0;
				copied_qnty_arr.length=0;
				for( var i=0; i<=duration[0]; i++ )
				{
					var ymd=clkdt[2].substr(4,4)+"-"+clkdt[2].substr(2,2)+"-"+clkdt[2].substr(0,2);
					var cldte=js_date_add( ymd, i )
					var vd=cldte.replace('-', ''); var clkdte=vd.replace('-', '');
					
					if(pln_grp==$('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('plan_group'))
					{
						if($('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('is_overlapped')==1)
						{
							var overlapped_data=$('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('overlapped_data').split(",");
							var today_plan_qnty='';
							for(var j=0; j<overlapped_data.length; j++ )
							{
								var planData=overlapped_data[j].split("_");
								if( jQuery.inArray( planData[0], machinePlanId )>-1) 
								{
									today_plan_qnty=today_plan_qnty*1+planData[2]*1;
								}
							}
						}
						else
						{
							var today_plan_qnty=$('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('today_plan_qnty');
						}
						$('#tdbody-'+ clkdt[1]+"-"+clkdte).css('border-color','#F2B7E2' );
						
						copied_plan_qnty=(copied_plan_qnty*1)+today_plan_qnty*1;
						copy_plan_length++;
						copied_qnty_arr[i]=today_plan_qnty*1;
					}
					else break ;
				}
			}
			else
			{
				alert("Please Select At Least One Plan Id");
				return;
			}
		}
	}
	else
	{
		//check_overlapped_plan( copyid );
		var machinePlanId=$('#'+copyid).attr('plan_id');
		duration=duration.split(".");
		var clkdt=copyid.split("-");
		copy_plan_length=0;
		copied_plan_qnty=0;
		copied_qnty_arr.length=0;
		for( var i=0; i<=duration[0]; i++ )
		{
			var ymd=clkdt[2].substr(4,4)+"-"+clkdt[2].substr(2,2)+"-"+clkdt[2].substr(0,2);
			var cldte=js_date_add( ymd, i )
			var vd=cldte.replace('-', ''); var clkdte=vd.replace('-', '');
			
			if(pln_grp==$('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('plan_group'))
			{
				if($('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('is_overlapped')==1)
				{
					var overlapped_data=$('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('overlapped_data').split(",");
					var today_plan_qnty='';
					for(var j=0; j<overlapped_data.length; j++ )
					{
						var planData=overlapped_data[j].split("_");
						if( planData[0]=machinePlanId) 
						{
							today_plan_qnty=today_plan_qnty*1+planData[2]*1;
							break;
						}
					}
				}
				else
				{
					var today_plan_qnty=$('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('today_plan_qnty');
				}
				
				$('#tdbody-'+ clkdt[1]+"-"+clkdte).css('border-color','#F2B7E2' );
				
				copied_plan_qnty=(copied_plan_qnty*1)+today_plan_qnty*1;
				copy_plan_length++;
				copied_qnty_arr[i]=today_plan_qnty*1;
			}
			else break ;
		}
	}
}


function event_check_copy( copyid ) 
{
	clicked_id=$('#'+copyid).attr('start_td_id');
	leventstart=clicked_id;
	var pln_grp=$('#'+clicked_id).attr('plan_group');
	var duration= ($('#'+clicked_id).attr('duration'));
	//check_overlapped_plan( clicked_id );
	//alert( revise_adjacent_prev_plan+"=="+ revise_adjacent_next_plan)
	 
	duration=duration.split(".");
	var clkdt=clicked_id.split("-");
	copy_plan_length=0;
	copied_plan_qnty=0;
	copied_qnty_arr.length=0;
	 
	for( var i=0; i<=duration[0]; i++ )
	{
		var ymd=clkdt[2].substr(4,4)+"-"+clkdt[2].substr(2,2)+"-"+clkdt[2].substr(0,2);
		var cldte=js_date_add( ymd, i )
		var vd=cldte.replace('-', ''); var clkdte=vd.replace('-', '');
		
		if( pln_grp==$('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('plan_group') )
		{
			$('#tdbody-'+ clkdt[1]+"-"+clkdte).css('border-color','#F2B7E2' );
			copied_plan_qnty=(copied_plan_qnty*1)+($('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('today_plan_qnty')*1);
			copy_plan_length++;
			copied_qnty_arr[i]=($('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('today_plan_qnty')*1);
		}
		else break ;
	}
}

function event_check_copy_forward( copyid, fdays, keep_org ) // Done
{
	if( !keep_org ) var keep_org=0;
	clicked_id=$('#'+copyid).attr('start_td_id');
	leventstart=clicked_id;
	var pln_grp=$('#'+clicked_id).attr('plan_group');
	var duration= ($('#'+clicked_id).attr('duration'));
	duration=duration.split(".");
	var clkdt=clicked_id.split("-");
	copy_plan_length=0;
	copied_plan_qnty=0;
	var stymd=clkdt[2].substr(4,4)+"-"+clkdt[2].substr(2,2)+"-"+clkdt[2].substr(0,2);
	copied_qnty_arr.length=0;
	for( var i=0; i<=duration[0]; i++ )
	{
		var ymd=clkdt[2].substr(4,4)+"-"+clkdt[2].substr(2,2)+"-"+clkdt[2].substr(0,2);
		var cldte=js_date_add( ymd, i )
		var vd=cldte.replace('-', ''); var clkdte=vd.replace('-', '');
		
		if( pln_grp==$('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('plan_group'))
		{
			$('#tdbody-'+ clkdt[1]+"-"+clkdte).css('border-bottom-color','#F2B7E2' );
			copied_plan_qnty=(copied_plan_qnty*1)+($('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('today_plan_qnty')*1);
			copied_qnty_arr[i]=($('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('today_plan_qnty')*1);
			copy_plan_length++;
		}
		else  break ;
	}
	
	//check_overlapped_plan( clicked_id );
	
	act_copy_plan_length=copy_plan_length;
	stymd=js_date_add( ymd, fdays );
	var vd=stymd.replace('-', ''); var clkdte=vd.replace('-', '');
	var pastid='tdbody-'+ clkdt[1]+"-"+clkdte;
	paste_replan_plan( pastid, leventstart, keep_org );
}

function delete_full_plan( delid)
{
	clicked_id=$('#'+delid).attr('start_td_id');
	leventstart=$('#'+delid).attr('start_td_id');
	var pln_grp=$('#'+clicked_id).attr('plan_group');
	var duration= ($('#'+clicked_id).attr('duration'));
	
	var upd_id=$('#'+clicked_id).attr('upd_id');
	duration=duration.split(".");
	var clkdt=clicked_id.split("-");
	copy_plan_length=0;
	
	
	undo_event_start_date=clkdt[2].substr(0,2)+"-"+clkdt[2].substr(2,2)+"-"+clkdt[2].substr(4,4);
	undo_event_start_line=clkdt[1];
	undo_event_start_duartion=duration;
	undo_event_start_qnty=$('#'+clicked_id).attr('plan_qnty');
	undo_event_start_updid=$('#'+clicked_id).attr('upd_id');
	undo_event_start_off=$('#'+clicked_id).attr('off_day_plan');
	undo_event_start_podtls=$('#'+clicked_id).attr('podtls');
	undo_event_start_complx= $('#'+clicked_id).attr('compid')+"**"+$('#'+clicked_id).attr('compstart')+"**"+$('#'+clicked_id).attr('compinc')+"**"+$('#'+clicked_id).attr('comptarg')+"**"+$('#'+clicked_id).attr('plan_qnty')+"**"+$('#'+clicked_id).attr('plan_qnty');
	undo_event_start_group=$('#'+clicked_id).attr('plan_group');
	
	clicked_id="";
	
	if( $('#deleted_id').val()=="")
		$('#deleted_id').val( upd_id );
	else
		$('#deleted_id').val($('#deleted_id').val()+","+ upd_id);
	leventType="delete";
	leventduration=duration[0];
	for( var i=0; i<=duration[0]; i++ )
	{
		var ymd=clkdt[2].substr(4,4)+"-"+clkdt[2].substr(2,2)+"-"+clkdt[2].substr(0,2);
		var cldte=js_date_add( ymd, i )
		var vd=cldte.replace('-', ''); var clkdte=vd.replace('-', '');
		
		if(pln_grp==$('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('plan_group'))
		{
			clear_td( 'tdbody-'+ clkdt[1]+"-"+clkdte,  'tdbody-'+ clkdt[1]+"-"+clkdte, 1, 0 );
			leventend='tdbody-'+ clkdt[1]+"-"+clkdte;
		}
		else  break ;
	}
}

function undo_event()
{
	if( leventType=="delete" ) //OK
	{
		$('#deleted_id').val('');
		
		undo_event_start_duartion=Number(get_production_days( undo_event_start_complx, undo_event_start_qnty, undo_event_start_date ,undo_event_start_off));
		
		draw_chart( undo_event_start_line, undo_event_start_date, undo_event_start_duartion, undo_event_start_podtls, undo_event_start_qnty, undo_event_start_off, undo_event_start_updid );
		
		undo_event_new_start_id='';
		undo_event_new_dur='';
		undo_event_start_line='';
		undo_event_new_start_id='';
		undo_event_start_duartion='';
		undo_event_start_podtls='';
		undo_event_start_qnty='';
		undo_event_start_off='';
		undo_event_start_updid='';
		undo_event_start_complx='';
		undo_event_start_date='';
		return;
	}
	else if( leventType=="cut" )  // Not OK
	{
		//alert(undo_event_start_date+'='+undo_event_start_duartion)
		var tmpd=undo_event_start_date.split("-");
		var strdate='';
		for( var i=0; i<undo_event_start_duartion; i++ )
		{
			var ymd2=tmpd[2]+"-"+tmpd[1]+"-"+tmpd[0];
			var cldte2=js_date_add( ymd2, -i )
			var vd2=cldte2.replace('-', ''); var clkdte2=vd2.replace('-', '');
			
			if( $('#tdbody-'+ undo_event_start_line+"-"+clkdte2 ).attr('plan_group')==undo_event_start_group)
			{
				strdate=clkdte2.substr(0,2)+"-"+clkdte2.substr(2,2)+"-"+clkdte2.substr(4,4);
				clear_td( 'tdbody-'+ undo_event_start_line+"-"+clkdte2, '', 1, i );
			}
			else break;
		}
		var undo_event_start_podtls_arr=undo_event_start_podtls.split("**");
		
		//alert(undo_event_start_complx+'=='+undo_event_start_qnty+'=='+strdate+'=='+undo_event_start_off)
	//	0**30,40,50,60****800**4000**4000==4000== ==2
		day_qnty.length=0;
		undo_event_start_duartion=Number(get_production_days( undo_event_start_complx, undo_event_start_qnty, undo_event_start_podtls_arr[4] ,undo_event_start_off));
		/*
		33**269**6265**33**24-02-2016****07-03-2016**0**13**15000**1**1000**100**1200**0**24-02-2016**07-03-2016**SS01**1000,1100,1200,1200,1200,1200,1200,1200,1200,1200,1200,1200,200**1**3**9**2**2**20160428**BIZZBEE**5**54875**700
		33**269**6265**33**24-02-2016****07-03-2016**0**13**15000**1**1000**100**1200**0**24-02-2016**07-03-2016**SS01**1000,1100,1200,1200,1200,1200,1200,1200,1200,1200,1200,1200,200**1**3**9**2**2**20160428**BIZZBEE**5**54875**700
		
		0**268**6265**1**12-03-2016****undefined**0**3**2600**1**1000**100**1200**0**12-03-2016**12-03-2016**SS01**1000,1100,500**1**3**9**2**2**20160428**BIZZBEE**5**54875****undefined
		33**269**6265**33**24-02-2016****04-03-2016**0**13**12400**1**1000**100**1200**0**24-02-2016**04-03-2016**SS01**1000,1100,1200,1200,1200,1200,1200,1200,1200,1200,1200,1200,200**1**3**9**2**2**20160428**BIZZBEE**5**54875**700**undefined
		
		
		 alert(undo_event_start_duartion+"="+day_qnty.length)*/
		 //if( day_qnty[undo_event_start_duartion]=='undefined' || day_qnty[undo_event_start_duartion]==undefined ) 
		// if( day_qnty.length == undo_event_start_duartion ) 
		 //	undo_event_start_duartion=undo_event_start_duartion-1;
		// alert(day_qnty.length)
		//0,320,0,400,0,480,480,480,480,0,480,480,400
		//0,320,0,400,0,480,480,480,480,0,480,480,400//alert(undo_event_start_line+'=='+undo_event_start_duartion+'=='+undo_event_new_start_id+'=='+undo_event_new_dur+'=='+strdate+'=='+undo_event_start_qnty+'=='+undo_event_start_updid+'=='+undo_event_start_podtls)
		
		///269==10==tdbody-267-20032016==2== ==4000==263==
		///263**269**6306**263**09-03-2016****20-03-2016**0**12**4000**0**30,40,50,60****800**0**09-03-2016**20-03-2016**KKL-4**240,320,0,400,480,480,480,480,480,0,480,160**4**3**9**2**2**20160506**BIZZBEE**3**riaj**0
		 
		var didw=undo_event_new_start_id.split("-");
		var ymd=didw[2].substr(4,4)+"-"+didw[2].substr(2,2)+"-"+didw[2].substr(0,2);
		var off_days_list=0;
		for( var i=0; i<undo_event_new_dur; i++ )
		{
			var cldte=js_date_add( ymd, i )
			var vd=cldte.replace('-', ''); var clkdte=vd.replace('-', '');
			//clear_td( cleartid, desttd, is_delete, inc )
			 clear_td( "tdbody-"+ didw[1] +"-"+ clkdte, '', 1, i );
		} 
		alert(undo_event_start_podtls);
		draw_chart( undo_event_start_line, strdate, undo_event_start_duartion, undo_event_start_podtls, undo_event_start_qnty, undo_event_start_off, undo_event_start_updid );
		
		undo_event_new_start_id='';
		undo_event_new_dur='';
		undo_event_start_line='';
		undo_event_new_start_id='';
		undo_event_start_duartion='';
		undo_event_start_podtls='';
		undo_event_start_qnty='';
		undo_event_start_off='';
		undo_event_start_updid='';
		undo_event_start_complx='';
		undo_event_start_date='';
		return;
	}
	else if( leventType=="move" ) //OK
	{
		var tmpd=undo_event_start_date.split("-");
		var strdate='';
		for( var i=0; i<undo_event_start_duartion; i++ )
		{
			var ymd2=tmpd[2]+"-"+tmpd[1]+"-"+tmpd[0];
			var cldte2=js_date_add( ymd2, -i )
			var vd2=cldte2.replace('-', ''); var clkdte2=vd2.replace('-', '');
			
			if( $('#tdbody-'+ undo_event_start_line+"-"+clkdte2 ).attr('plan_group')==undo_event_start_group)
			{
				strdate=clkdte2.substr(0,2)+"-"+clkdte2.substr(2,2)+"-"+clkdte2.substr(4,4);
				clear_td( 'tdbody-'+ undo_event_start_line+"-"+clkdte2, '', 1, i );
			}
			else break;
		}
		
		undo_event_start_duartion=Number(get_production_days( undo_event_start_complx, undo_event_start_qnty, strdate ,undo_event_start_off));
		
		draw_chart( undo_event_start_line, strdate, undo_event_start_duartion, undo_event_start_podtls, undo_event_start_qnty, undo_event_start_off, undo_event_start_updid );
		
		var didw=undo_event_new_start_id.split("-");
		var ymd=didw[2].substr(4,4)+"-"+didw[2].substr(2,2)+"-"+didw[2].substr(0,2);
		var off_days_list=0;
		for( var i=0; i<undo_event_new_dur; i++ )
		{
			var cldte=js_date_add( ymd, i )
			var vd=cldte.replace('-', ''); var clkdte=vd.replace('-', '');
			clear_td( "tdbody-"+ didw[1] +"-"+ clkdte, '', 1, i );
		}
		undo_event_new_start_id='';
		undo_event_new_dur='';
		undo_event_start_line='';
		undo_event_new_start_id='';
		undo_event_start_duartion='';
		undo_event_start_podtls='';
		undo_event_start_qnty='';
		undo_event_start_off='';
		undo_event_start_updid='';
		undo_event_start_complx='';
		undo_event_start_date='';
		return; 
	}
	else if( leventType=="new" ) //OK
	{
		var tmpd=undo_event_start_date.split("-");
		var strdate='';
		
		for( var i=0; i<undo_event_start_duartion; i++ )
		{
			var ymd2=tmpd[2]+"-"+tmpd[1]+"-"+tmpd[0];
			var cldte2=js_date_add( ymd2, i )
			var vd2=cldte2.replace('-', ''); var clkdte2=vd2.replace('-', '');
			if(i==0) undo_event_start_group=$('#tdbody-'+ undo_event_start_line+"-"+clkdte2 ).attr('plan_group');
			
			if( $('#tdbody-'+ undo_event_start_line+"-"+clkdte2 ).attr('plan_group')==undo_event_start_group)
			{
				strdate=clkdte2.substr(0,2)+"-"+clkdte2.substr(2,2)+"-"+clkdte2.substr(4,4);
				clear_td( 'tdbody-'+ undo_event_start_line+"-"+clkdte2, '', 1, i );
			}
			else break;
		}
		
		undo_event_new_start_id='';
		undo_event_new_dur='';
		undo_event_start_line='';
		undo_event_new_start_id='';
		undo_event_start_duartion='';
		undo_event_start_podtls='';
		undo_event_start_qnty='';
		undo_event_start_off='';
		undo_event_start_updid='';
		undo_event_start_complx='';
		undo_event_start_date='';
		return;
	}
	
	
}

function show_information( tid )
{
	$('#common').html( $( "#"+tid ).attr('title'));
	
	var data=$("#"+tid).attr('completed_qnty')+"__"+ $("#"+tid).attr('po_id')+"__"+ $("#"+tid).attr('plan_id')+"__"+ $("#"+tid).attr('line_id')+"__"+ $("#"+tid).attr('start_date')+"__"+ $("#"+tid).attr('plan_qnty')+"__"+ $("#"+tid).attr('duration')+"__"+ $("#"+tid).attr('plan_end_date');
	
	
	 var datass= $.ajax({
		  url: "requires/planning_board_controller.php?data="+data+"&action=show_planning_details_bottom",
		  async: false
		}).responseText
		$('#footer').html( datass); 
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

var day_qnty=Array();
var off_day_arrayk =new Array();
var lastdate='';
function get_production_days( complx, qnty, start_date, include_offday_production, mline )
{
	off_day_arrayk=off_day_array.split(", ");
	var compl=complx.split("**");
	
	if( compl[0]*1 > 0 )
	{
		var app_day=((qnty/compl[1])+25).toFixed(0);
		var toprod=compl[1]*1;
		var k=0;
		var dtarg=compl[1];
		var prt=0;
		day_qnty.length=0;
		var jm=0;
		var ddd=start_date.split("-");
	//	alert(app_day);
		for( var j=0; j<app_day; j++ )
		{
			var d=js_date_add( ddd[2]+"-"+ddd[1]+"-"+ddd[0], j );
			var dd=d.split("-");
			var dates= dd[0]+"-"+dd[1]+"-"+dd[2];
			// alert(dd[0]+"-"+dd[1]+"-"+dd[2]);
			//alert( $("#tdbody-"+mline+"-"+dd[0]+""+dd[1]+""+dd[2] ).attr('plan_group') );
			// 26-08-2015
			//alert(app_day+"="+qnty+"="+compl[1]+"="+ddd+"="+ddd+"="+$("#tdbody-"+mline+"-"+dd[0]+""+dd[1]+""+dd[2] ).attr('plan_group'))
			//40=15000=1000=22,01,2016=22,01,2016=1
			
			if( ($("#tdbody-"+mline+"-"+dd[0]+""+dd[1]+""+dd[2] ).attr('plan_group')*1)>0 )
			{
				if ( leventType!='forwards')  return 0;
			}
			if((qnty*1)<1 ) return k;
			
			
			if( (qnty*1)>(dtarg*1) )
			{	
				if( (dtarg*1)<(compl[3]*1) )
				{
					if( jQuery.inArray( dates, off_day_arrayk)>-1 && include_offday_production==1)
					{
						if(j!=0) dtarg=(dtarg*1)+( compl[2]*1 );
						if( (dtarg*1)>( compl[3]*1) )
						{
							dtarg=compl[3];
						}
						
						qnty=qnty-dtarg;
						k++;
						day_qnty[j]=dtarg;
						jm=0;
					}
					else if( jQuery.inArray( dates, off_day_arrayk)>-1 && include_offday_production==2)
					{
						jm=1;
						k++;
						day_qnty[j]=0;
					}
					else
					{
						if(j!=0) dtarg=(dtarg*1)+( compl[2]*1 );
						if( (dtarg*1)>( compl[3]*1) )
						{
							dtarg=compl[3];
						}
						if(jm==1 && j==0) dtarg=(dtarg*1)-( compl[2]*1 );
						qnty=qnty-dtarg;
						k++;
						day_qnty[j]=dtarg;
						jm=0;
					}
					
					
				}
				else
				{
					if( jQuery.inArray( dates, off_day_arrayk)>-1 && include_offday_production==1)
					{
						qnty=qnty-dtarg;
						k++;
						day_qnty[j]=dtarg;
					}
					else if( jQuery.inArray( dates, off_day_arrayk)>-1 && include_offday_production==2)
					{
						k++;
						day_qnty[j]=0;
					}
					else
					{
						qnty=qnty-dtarg;
						k++;
						day_qnty[j]=dtarg;
					}
				}
			}
			else
			{
				if( jQuery.inArray( dates, off_day_arrayk)>-1 && include_offday_production==1)
				{
					prt=( (qnty*10)/compl[3] ).toFixed(0);
					 day_qnty[j]=qnty;
					 if((prt*1) >0 ) k++;
					 return k; //+"."+prt;
				}
				else if( jQuery.inArray( dates, off_day_arrayk)>-1 && include_offday_production==2)
				{
					k++;
					day_qnty[j]=0;
				}
				else
				{
					prt=( (qnty*10)/compl[3] ).toFixed(0);
					 day_qnty[j]=qnty;
					 if((prt*1) >0 ) k++;
					 return k;
				}
			}
		}
	}
	else
	{
		var vcom=compl[1].split(",");
		var vtarg=compl[3];
		var new_targ=new Array();
		for( var i=0; i<vcom.length; i++)
		{
			new_targ[i]= ((vcom[i]*vtarg)/100).toFixed(0);
			compl[3]=((vcom[i]*vtarg)/100).toFixed(0);
		}
		var app_day=((qnty/new_targ[0])+25).toFixed(0);
		var ddd=start_date.split("-");
		
		var toprod=compl[1]*1;
		var k=0;
		var dtarg=compl[1];
		var prt=0;
		day_qnty.length=0;
		var jm=0;
		var ddd=start_date.split("-");
		for( var j=0; j<app_day; j++ )
		{
			var d=js_date_add( ddd[2]+"-"+ddd[1]+"-"+ddd[0], j );
			var dd=d.split("-");
			var dates= dd[0]+"-"+dd[1]+"-"+dd[2];
			 
			if((qnty*1)<1 ) return k;
			
			if( j<i )
			{
				if( jQuery.inArray( dates, off_day_arrayk)>-1 && include_offday_production==2)
				{	new_targ.splice(j-1, 0, 0); i++; }
				dtarg=new_targ[j];
			}
			if( (qnty*1)>(dtarg*1) )
			{	
				if( (dtarg*1)<(compl[3]*1) )
				{
					if( jQuery.inArray( dates, off_day_arrayk)>-1 && include_offday_production==1)
					{
						qnty=qnty-dtarg;
						k++;
						day_qnty[j]=dtarg;
						jm=0;
					}
					else if( jQuery.inArray( dates, off_day_arrayk)>-1 && include_offday_production==2)
					{
						jm=1;
						k++;
						day_qnty[j]=0;
					}
					else
					{
						if(jm==1 && j==0) dtarg=new_targ[j-1];//(dtarg*1)-( compl[2]*1 );
						qnty=qnty-dtarg;
						k++;
						day_qnty[j]=dtarg;
						jm=0;
					}
					
					
				}
				else
				{
					if( jQuery.inArray( dates, off_day_arrayk)>-1 && include_offday_production==1)
					{
						qnty=qnty-dtarg;
						k++;
						day_qnty[j]=dtarg;
					}
					else if( jQuery.inArray( dates, off_day_arrayk)>-1 && include_offday_production==2)
					{
						k++;
						day_qnty[j]=0;
					}
					else
					{
						qnty=qnty-dtarg;
						k++;
						day_qnty[j]=dtarg;
					}
				}
			}
			else
			{
				if( jQuery.inArray( dates, off_day_arrayk)>-1 && include_offday_production==1)
				{
					prt=( (qnty*10)/compl[3] ).toFixed(0);
					 day_qnty[j]=qnty;
					 if((prt*1) >0 ) k++;
					 return k; //+"."+prt;
				}
				else if( jQuery.inArray( dates, off_day_arrayk)>-1 && include_offday_production==2)
				{
					k++;
					day_qnty[j]=0;
				}
				else
				{
					prt=( (qnty*10)/compl[3] ).toFixed(0);
					day_qnty[j]=qnty;
					if((prt*1) >0 ) k++;
					return k;
				}
			}
		}
	}
}


function append_off_days( start_date, duration, include_offday_production)
{
	 //alert(day_qnty.length)
	return duration+"__"+dates;
	
	for(var m=0;m<duration; m++)
	{
		var d=js_date_add( start_date, m );
		var dd=d.split("-");
		
		var dates= dd[0]+"-"+dd[1]+"-"+dd[2];
		
		for( var km=0; km<off_day_arrayk.length; km++ )
		{ 
			if( trim(off_day_arrayk[km])==dates) //jQuery.inArray( dates, off_day_arrayk)!=-1)
			{
				if( include_offday_production!=1 ) // No working in offday
				{
					day_qnty[day_qnty.length]=day_qnty[m];
					//alert(day_qnty[day_qnty.length-1]+"=="+day_qnty[m]);
					day_qnty[m]=0;
					duration++;
				}
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
