// start from po qnty balancing and production data check and SMV item wise po from po search
// re-check copy, cut and delete
// tag production data datewise
//https://www.youtube.com/watch?v=OnUjyHeGG_E   common sense
var cdate="";
var k=0;
var complexity_type=1;
var group_po_info='';
function context_menu_operation(  tid, operation  )
{
	if( operation=='new' )
	{
		var td=tid.split("-");
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/planning_board_controller.php?action=order_popup&company_id='+$('#cbo_company_mst').val()+'&location_id='+$('#cbo_location_name').val()+'&pline='+td[1]+'&pdate='+td[2]+'&cdate='+cdate+'&complexity_type='+complexity_type+'&floor_id='+$('#cbo_floor_name').val(), '', 'width=1250px,height=450px, center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_job");
			
			if (theemail.value!="")
			{
				//alert(theemail.value)
				var tmp=theemail.value;
				var tmpd=tmp.split("______");
				//	 265**13-02-2016**5000**0**0**1**2**999
				//OG-15-00431_5647_143000_12-01-2016_3698_12-01-2016_SS-33_5_2_116600_16-11-2015_21-12-2015_3_G-Star Raw ______265**13-02-2016**5000**0**0**1**2**999______2**800**100**1200**5647**0
				group_po_info=tmpd[0];
				var npos=tmpd[0].split("|*|");
					var gpo=2;
				if( npos.length>1 )
				{
					tmpd[0]="Multiple_0_0_02-11-2016_Multiple PO_02-11-2016_838492_5_2_700_31-07-2016_30-09-2016_";
					var gpo=1;
					//jdata[8]
				}
				//else
				var podt=tmpd[0].split("_");
				var comp=tmpd[2].split("**");
				var pln=tmpd[1].split("****");
				for(var m=0; m<pln.length; m++)
				{
					// alert(pln[m])
					var npln=pln[m].split("**");
					//_265**14-03-2016**3000**0**0**1**2**0**
					var plength=Number( get_production_days( tmpd[2], (npln[2]-(npln[7]*1)), npln[1] ,npln[6], npln[0] ));
					var td=npln[1].split("-");
					var actenddate=js_date_add( td[2]+"-"+td[1]+"-"+td[0], plength );
					var tdd=actenddate.split("-");
					
					var benddate=$('#txt_from_to_date').val().split("__");
					var btd=benddate[1].split("-");
					if( ((tdd[2]+""+tdd[1]+""+tdd[0])*1) > ((btd[0]+""+btd[1]+""+btd[2])*1) )
					{
						alert('Your plan length has exceeded the board length, please check plan quantity again.');
						return;
					}
					//alert( plength+"="+npln[1]+"="+actenddate );
					//return;
					//18=04-03-2016=22-03-2016
					k=(Array.max(pln_grp_indx))+1;
					pln_grp_indx.push(k);
					
					var shp=podt[3].split("-");
					
					var nshp=js_date_add( shp[2]+"-"+shp[1]+"-"+shp[0], -2 );
					var shp=nshp.split("-");
					//alert(nshp);
				    
				 	if( npos.length>1 ) // Multiple PO
						var podtls="0**"+npln[0].replace('_', '')+"**"+podt[1]+"**0**"+npln[1]+"**0**"+npln[1]+"**0**"+plength+"**"+npln[2]+"**"+comp[0]+"**"+comp[1]+"**"+comp[2]+"**"+comp[3]+"**0**"+npln[1]+"**"+npln[1]+"**Multiple PO**"+podt[7]+"**"+podt[8]+"**"+podt[9]+"**"+podt[10]+"**"+npln[6]+"**"+podt[10]+"**"+shp[2]+""+shp[1]+""+shp[0]+"**"+podt[13]+"**"+podt[7]+"**"+podt[6]+"**"+npln[7];
					else
						var podtls="0**"+npln[0].replace('_', '')+"**"+podt[1]+"**0**"+npln[1]+"**0**"+npln[1]+"**0**"+plength+"**"+npln[2]+"**"+comp[0]+"**"+comp[1]+"**"+comp[2]+"**"+comp[3]+"**0**"+npln[1]+"**"+npln[1]+"**"+podt[4]+"**"+podt[7]+"**"+podt[8]+"**"+podt[9]+"**"+podt[10]+"**"+npln[6]+"**"+podt[10]+"**"+shp[2]+""+shp[1]+""+shp[0]+"**"+podt[13]+"**"+podt[7]+"**"+podt[6]+"**"+npln[7];
					
					
					//alert(podtls);
					
					//npln[7]=overlapped_qnty, position=28
					// 7 13
					// 0**142**5579**0**18-10-2015**0**18-10-2015**0**5**5000**1**1000**100**1200**0**18-10-2015**18-10-2015**F001**5**1**-9199**04-10-2015**1**04-10-2015**20151031
					undo_event_start_date=npln[1];
					undo_event_start_line=npln[0];
					undo_event_start_duartion=plength.toFixed(1);
					undo_event_start_qnty=npln[2];
					undo_event_start_updid='';
					undo_event_start_off=npln[6];
					undo_event_start_podtls=podtls;
					undo_event_start_complx=tmpd[2];
					undo_event_start_group=k;
					
					draw_chart( npln[0].replace('_', ''), npln[1], plength.toFixed(1),podtls, npln[2], npln[6],0,gpo,group_po_info );
					//draw_chart( npln[0], npln[1], plength.toFixed(1),podtls,npln[2],npln[6] );
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
		//return; // Allow to view board without selecting floor
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
	
	
	var dddat=$('#group_po_det').val();
	var group_po_det = JSON.parse(dddat);
	
	
				
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
			var grouo_po=2;
			if(group_po_det[tmpd[0]]!=undefined)
			{
				group_po_info=group_po_det[tmpd[0]];
				var npos=group_po_info.split("|*|");
				if( npos.length>1 )
					grouo_po=1;
			}
			else
				 group_po_info=old_plan_data_arr[l];
			
			draw_chart( tmpd[1], tmpd[4], tmpd[8], old_plan_data_arr[l], tmpd[9], tmpd[22],'', grouo_po, group_po_info);	
		}
	}
	
	//alert (check_overlapped_plan("tdbody-268-05032016"))
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
	var group_po_info='';
	var is_next=new Array();
	$( "#plan_container .verticalStripes1_plan" ).each(function() {
		
		if( is_next[$(this).attr('plan_group')]==undefined )
		{
			 if( today_plan_qnty=="" ) today_plan_qnty=$(this).attr('today_plan_qnty'); else today_plan_qnty=today_plan_qnty+"**"+$(this).attr('today_plan_qnty');
			if( total_plan==0)
			{
				
				//alert($(this).attr('podtls'));return;
				
				poid=$(this).attr('po_id');
				cmpid=$(this).attr('compid');
				cmpstart=$(this).attr('compstart');
				cmpinc=$(this).attr('compinc');
				cmptarg=$(this).attr('comptarg');
				lineid=$(this).attr('line_id');
				var startdt=$(this).attr('start_date').split(".");
				var tmppodtls=$(this).attr('podtls').split("**");
				tmpshipdate=tmppodtls[24];
				extra_param=tmppodtls[25]+"__"+tmppodtls[26]+"__"+tmppodtls[27]+"__"+tmppodtls[28];
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
				group_po_info=$(this).attr('group_po_info');
			}
			else
			{
				
				var tmppodtls=$(this).attr('podtls').split("**");
				tmpshipdate=tmpshipdate+"**"+tmppodtls[24];
				
				extra_param=extra_param+"**"+tmppodtls[25]+"__"+tmppodtls[26]+"__"+tmppodtls[27]+"__"+tmppodtls[28];
				
				
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
				group_po_info=group_po_info+"||**||"+$(this).attr('group_po_info');
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
	var data="action=save_update_delete&operation="+operation+"&num_row="+total_plan+"&poid="+poid+"&cmpid="+cmpid+"&cmpstart="+cmpstart+"&cmpinc="+cmpinc+"&cmptarg="+cmptarg+"&lineid="+lineid+"&startdate="+startdate+"&duratin="+duratin+"&planqty="+planqty+"&enddate="+enddate+"&planid="+planid+"&updid="+updid+"&isnew="+isnew+"&isedited="+isedited+"&today_plan_qnty="+today_plan_qnty+'&all_deleted_ids='+$('#deleted_id').val()+'&cbo_company_mst='+$('#cbo_company_mst').val()+'&cbo_location_name='+$('#cbo_location_name').val()+'&item_name_id='+item_name_id+'&off_day_plan='+off_day_plan+'&order_complexity='+order_complexity+'&tmpshipdate='+tmpshipdate+'&extra_param='+extra_param+'&group_po_info='+group_po_info;
	 
	//freeze_window(0); // +get_submitted_data_string('cbo_location_name*cbo_company_mst',"../")
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
		//$('#operation_closed').val(http.responseText);
		$('#deleted_id').val('');
		//alert(response[0]);
		show_msg(response[0]);
		//OG-16-01902_21694_18180_03-10-2016_1203_03-10-2016_special-30_3_3_18180_02-09-2016_15-09-2016__1_BIZZBEE
		// release_freezing();
	}
}

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
function draw_chart( line, stdate, wid, podtls, planqnty, off_day_plan , eupdid, is_group, group_po_info )
{
	 //alert(podtls);
	
	// alert( pln_grp_indx )
 	//if( pln_grp_indx=='-Infinity' ) pln_grp_indx.push('0');
	 //alert( group_po_info );
	
	if(!is_group) var is_group=2;
	if( !eupdid ) var eupdid=0;
	cdate=$('#txt_cdate').val();
	var podtl=podtls.split("**");
	var vd=stdate;//podtl[4].replace('-', ''); 
	var stdate=vd.replace('-', '');
	var stdid='tdbody-'+ line+"-"+stdate;
	
	var chkstda=podtl[4].split("-");
	var chkstdate=chkstda[2]+""+chkstda[1]+""+chkstda[0];
	 
	var chkeddate=cdate.substr(4,4)+""+cdate.substr(2,2)+""+cdate.substr(0,2); 
	var is_prod=0;
	var tdate=stdate;
	var vd=cdate.replace('-', ''); 
	var ptdate=vd.replace('-', '');
	var wid=Number(Math.ceil(wid))+".0";
	 
	if( wid==0 ) alert( 'Production days not available. Please check.' );
	
	if( (chkstdate*1)>=(chkeddate*1) )
	{
		if( (podtl[0]*1)>0) { k=podtl[0]; pln_grp_indx.push(podtl[0]); } 
		else { 
			
			k=(Array.max(pln_grp_indx))+1;
			pln_grp_indx.push(k);
		}
		//if( trim(podtl[0])==5412) alert(prodcution_data_sum[podtl[0]]);
		//if( podtl[2]==5412) alert(prodcution_data[podtl[1]])
		var ymd1=podtl[4].split("-"); var ymd=ymd1[2]+"-"+ymd1[1]+"-"+ymd1[0];
		var dpos=wid.indexOf(".");
	 	if( is_group==1) podtl[17]="Multiple PO";
		
		var wpart=''; 
		if(dpos>0)
			wpart=wid.split("."); 
		else
		{
			 wpart[0]=wid; wpart[1]=0;
		}
		 if( ( podtl[0]*1)>0 )
		{
			 
			day_qnty=podtl[18].split(",");
		}
		
		var days_date=wpart[0];
		days_date=days_date.split("__");
		var wp=days_date[0];
		var dd=js_date_add( ymd, wp );
		var vd=dd.replace('-', ''); var vdt=vd.replace('-', '');
		//alert(podtl[28])
		//alert(podtl[28]);
		var actenddate=js_date_add( ymd, wp-1 );
		for( var i=0; i<Math.ceil(wp); i++ )
		{
			if( i==0 && (podtl[28]*1)>0 )
			{
				var d=js_date_add( ymd, -1 );
				var tdd=d.split("-");
				var tdds=tdd[2]+""+tdd[1]+""+tdd[0];
				var vd=d.replace('-', ''); var vdt=vd.replace('-', '');
			 	if( $('#tdbody-'+ line+"-"+vdt).attr('today_plan_qnty')!=undefined)
				{
					var plnqnt=( $('#tdbody-'+ line+"-"+vdt).attr('today_plan_qnty')*1)+(podtl[28]*1);
					var prdqnty=( $('#tdbody-'+ line+"-"+vdt).attr('today_production')*1);
				}
				else
				{
					var plnqnt=( podtl[28]*1);
					var prdqnty=0;
				}
				if( plnqnt>prdqnty )  //This cond added on 26-07-15,
					$('#tdbody-'+ line+"-"+vdt).html( "<font color='#000000' >"+plnqnt+"</font><br> <font color='#000000' >"+prdqnty+"</font>");
				else
					$('#tdbody-'+ line+"-"+vdt).html( "<font color='#000000' >"+plnqnt+"</font> <br><font color='#000000' >"+prdqnty+"</font>");
				// $('#tdbody-'+ line+"-"+vdt).attr('today_plan_qnty',plnqnt);
				$('#tdbody-'+ line+"-"+vdt).css('background','repeating-linear-gradient( 45deg, #606dbc, #606dbc 2px, #465298 2px, #465298 4px)');
				 
					
				
				// $('#tdbody-'+ line+"-"+vdt).css('border-radius', '70% 0 0 70%');
			}
			
			
			var d=js_date_add( ymd, i );
			var tmpdate=d;
			var tdd=d.split("-");
			var tdds=tdd[2]+""+tdd[1]+""+tdd[0];
			var vd=d.replace('-', ''); var vdt=vd.replace('-', '');
		 	//alert(tmpdate)
			if( podtl[14]==1)
				var ccol='_crossed';
			else
				var ccol='_plan';
			// alert(prodcution_data_sum[k]);
			if( prodcution_data_sum[k]!=undefined )
			{
				if( ( prodcution_data_sum[k]*1)>0 )
					var ccol='_produced';
					is_prod=1;
				//var ccol='_produced';//$('#tdbody-'+ line+"-"+vdt).addClass('verticalStripes1'+ccol) //oclass+""+ccol
			}
			
			//alert(prodcution_data);
			
			var prd=0;
			if( prodcution_data[podtl[1]]!=undefined)
			{
				if( prodcution_data[podtl[1]][podtl[2]]!=undefined)
				{
					if(prodcution_data[podtl[1]][podtl[2]][tmpdate]!=undefined)
					{
						prd=prodcution_data[podtl[1]][podtl[2]][tmpdate];
					//	 alert(podtl[1]+"="+prodcution_data[podtl[1]][podtl[2]][tmpdate])
						// var ccol='_produced';
					}
				}
			}
			if( is_prod==1 && prd==0 ) var ccol='_non_produced';
			
			var plan_sts=0; //0=Regular Plan, 1=Crossed Date PLan, 2=Production Started, Back Date pla
			//if( tdds < today_sys_date) var ccol='_crossed';// var plan_sts=2;
			//else
			
				$('#tdbody-'+ line+"-"+vdt).addClass('verticalStripes1_plan')
				$('#tdbody-'+ line+"-"+vdt).addClass('verticalStripes1'+ccol) //oclass+""+ccol
			//#tdbody-_30-12092015
			//var overlap=0;
			//if(( $('#tdbody-'+ line+"-"+vdt).attr('today_plan_qnty')*1)>0)
			//	var overlap=1;
			
			if( $( '#tdbody-'+ line+"-"+vdt ).hasClass( "verticalStripes_off" ).toString()=='true' ) //$( "p:first" ).hasClass( "selected" ).toString()
				$('#tdbody-'+ line+"-"+vdt).css('border-bottom-color', '#FFFFA8');
			else
				$('#tdbody-'+ line+"-"+vdt).css('border-bottom-color', '#F5F5F5');
			
			if(i==0)
			{
				$('#tdbody-'+ line+"-"+vdt).css('border-radius', '70% 0 0 70%');
				var strtid='tdbody-'+ line+"-"+vdt;
				
				/* $('#tdbody_blank-'+ line+"-"+vdt).css('border-left','1px solid #FF00FF');
				$('#tdbody_blank-'+ line+"-"+vdt).attr('colspan',wp);//('border-left','1px solid #FF00FF');
				$('#tdbody_blank-'+ line+"-"+vdt).css('border-right','1px solid #FF00FF');
				
				$('#tdbody_blank-'+ line+"-"+vdt).attr('align','center');
				
				$('#tdbody_blank-'+ line+"-"+vdt).css('background-color','#FF00FF');
				$('#tdbody_blank-'+ line+"-"+vdt).css('color','#FFFFFF');
				//$('#tdbody_blank-'+ line+"-"+vdt).css('font-size','8');
				$('#tdbody_blank-'+ line+"-"+vdt).html('TEST');//('color','#FFFFFF');
				*/
			}
			/*else 
				$('#tdbody_blank-'+ line+"-"+vdt).remove();*/
			
			if( i==wp-1 )
			{
				if((wpart[1]*1)>0)
				{
					if(wpart[1].length==1) wpart[1]=wpart[1]+"0";
					var ext= (wpart[1]/100)*30;
					var extp=(ext/30)*100;
					var extp_none=100-extp;
					var ext_no=30-ext;
					
				}
				else
				{
					var ext=30;
					var ext_no=0;
					var extp= 100;
					var extp_none=0;
				}
			}
				
			$('#tdbody-'+ line+"-"+vdt).attr('plan_group', k);
			var shpdt=podtl[24].substr(6, 2)+"-"+podtl[24].substr(4, 2)+"-"+podtl[24].substr(0,4);
			if(podtl[17]!='Multiple PO')
				$('#tdbody-'+ line+"-"+vdt).attr('title','Start Date:: '+podtl[15]+', End Date:: '+actenddate+', Quantity:: '+podtl[9]+', Item:: '+item_name[podtl[19]]+', Order:: '+podtl[17]+', Ship Date:: '+shpdt+', SMV:: '+podtl[26]+', Style:: '+podtl[27]); // Buyer:: '+podtl[25]+',
			else
				$('#tdbody-'+ line+"-"+vdt).attr('title','Start Date:: '+podtl[15]+', End Date:: '+actenddate+', Quantity:: '+podtl[9]+', Order:: '+podtl[17]+''); // Buyer:: '+podtl[25]+',
				
			$('#tdbody-'+ line+"-"+vdt).attr('compstart',podtl[11]);
			$('#tdbody-'+ line+"-"+vdt).attr('start_td_id',strtid);
			
				if( day_qnty[i]>prd )  //This cond added on 26-07-15,
					$('#tdbody-'+ line+"-"+vdt).html( "<font color='#000000' >"+day_qnty[i]+"</font><br> <font color='#000000' >"+prd+"</font>");
				else
					$('#tdbody-'+ line+"-"+vdt).html( "<font color='#000000' >"+day_qnty[i]+"</font> <br><font color='#000000' >"+prd+"</font>");
					//('compinc',podtl[12]);
			//background-color:#FF0000;
			
		 	//alert(group_po_info)
			$('#tdbody-'+ line+"-"+vdt).attr('group_po_info',group_po_info);
			$('#tdbody-'+ line+"-"+vdt).attr('item_name_id',podtl[19]);
			$('#tdbody-'+ line+"-"+vdt).attr('compinc',podtl[12]);
			$('#tdbody-'+ line+"-"+vdt).attr('comptarg',podtl[13]);
			$('#tdbody-'+ line+"-"+vdt).attr('podtls',podtls);
			$('#tdbody-'+ line+"-"+vdt).attr('compid',podtl[10]);
			$('#tdbody-'+ line+"-"+vdt).attr('po_id',podtl[2]);
			$('#tdbody-'+ line+"-"+vdt).attr('plan_id',k);
			$('#tdbody-'+ line+"-"+vdt).attr('line_id',podtl[1]);
			$('#tdbody-'+ line+"-"+vdt).attr('start_date',podtl[4]);
			$('#tdbody-'+ line+"-"+vdt).attr('start_hr',podtl[5]);
			$('#tdbody-'+ line+"-"+vdt).attr('end_hr',podtl[7]);
			$('#tdbody-'+ line+"-"+vdt).attr('plan_qnty',podtl[9]);
			$('#tdbody-'+ line+"-"+vdt).attr('duration',wp+"."+wpart[1]);
			$('#tdbody-'+ line+"-"+vdt).attr('upd_id',podtl[0]);
			if(eupdid>0)
				$('#tdbody-'+ line+"-"+vdt).attr('isedited','1');
			else
				$('#tdbody-'+ line+"-"+vdt).attr('isedited','0');
			
			$('#tdbody-'+ line+"-"+vdt).attr('plan_end_date',actenddate);
			$('#tdbody-'+ line+"-"+vdt).attr('is_partial',podtl[14]);
			
			$('#tdbody-'+ line+"-"+vdt).attr('today_production',prd);
			$('#tdbody-'+ line+"-"+vdt).attr('today_plan_qnty',day_qnty[i]);
			$('#tdbody-'+ line+"-"+vdt).attr('off_day_plan',off_day_plan);
			
			if( podtl[22]==undefined ) podtl[22]=0;
				$('#tdbody-'+ line+"-"+vdt).attr('order_complexity', podtl[22]);
			
			if( ( podtl[0]*1)>0 )
			{ 
				$('#tdbody-'+ line+"-"+vdt).attr('isnew','0'); 
			}
			else
			{
				$('#tdbody-'+ line+"-"+vdt).attr('isnew','1');
				$('#tdbody-'+ line+"-"+vdt).css('border-bottom-color','#F2B7E2' );  //F2B7E2
				$('#last_event_id').val( 'tdbody-'+ line+"-"+vdt )
			}
			if( (podtl[10]*1)>0) var ncom=(podtl[10]*1); else var ncom=(podtl[23]*1);
			$('#tdbody-'+ line+"-"+vdt).css('border-top-color', complexity_color_code[ncom]); //#6247F8
			
			var ndt=vdt.substr(4, 4)+""+vdt.substr(2, 2)+""+vdt.substr(0,2);
			
			
			//( podtl[24]*1)
			
			if( ( podtl[24]*1)<=(ndt*1)  )
				$('#tdbody-'+ line+"-"+vdt).css('background-color','#FF0000' );
			//if(podtl[24]!='99999999')alert( podtl[24]+"=="+ndt )
			
			$('#tdbody-'+ line+"-"+vdt).attr('name',podtl[17]);
			$('#tdbody-'+ line+"-"+vdt).attr('onclick','event_select(this.id)');
			//alert( i +"=="+ Math.ceil(wp) )
			//if( i==(wp-1) ) alert('asd')
			if(podtl[17]=="Multiple PO")
				$('#tdbody-'+ line+"-"+vdt).css('background','repeating-linear-gradient( 45deg, #606dbc, #606dbc 2px, #465298 2px, #465298 4px)');
				
				
			
			

			if( i==(wp-1) )
			{
				var ds=js_date_add( ymd, (i*1)+1 );
				var tdds=ds.split("-");
				var tdds=tdds[0]+""+tdds[1]+""+tdds[2];
				var npod=$('#tdbody-'+ line+"-"+tdds).attr('podtls');
				//alert(ymd+"="+tdds+"="+npod)
				if( npod != undefined)
				{
					var npods=npod.split("**"); 
					
					if(  ( npods[28]*1) > 0 )
					//if( overlap==1)  203.82.196.18/
					{
						//alert('12312')
						var plnqnt=( $('#tdbody-'+ line+"-"+vdt).attr('today_plan_qnty')*1)+(npods[28]*1);
						var prdqnty=( $('#tdbody-'+ line+"-"+vdt).attr('today_production')*1);
						
						if( plnqnt>prdqnty )  //This cond added on 26-07-15,
							$('#tdbody-'+ line+"-"+vdt).html( "<font color='#000000' >"+plnqnt+"</font><br> <font color='#000000' >"+prdqnty+"</font>");
						else
							$('#tdbody-'+ line+"-"+vdt).html( "<font color='#000000' >"+plnqnt+"</font> <br><font color='#000000' >"+prdqnty+"</font>");
						// $('#tdbody-'+ line+"-"+vdt).attr('today_plan_qnty',plnqnt);
						 $('#tdbody-'+ line+"-"+vdt).css('background','repeating-linear-gradient( 45deg, #606dbc, #606dbc 2px, #465298 2px, #465298 4px)');	
					}
				}
				
			}
			 
			
		}
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
		
		//if( $(this).hasClass( "verticalStripes_off" ).toString()=='true' )
		//	$(this).css('border-bottom-color', '#FFFFA8');
		//else
			$(this).css('border-bottom-color','#F2B7E2' );
			
	});
	/*
	if($('#'+ selid).attr( 'isnew' )!=0)
	{
		alert('You can not edit New Plan'); 
		return;
	}
	
	if( ($('#'+selid).attr('pgroup')*1)==0 )
	{ 
		alert('Please Click on a plan cut or split.'); 
		return;
	}
	
	if( $( '#'+ selid ).hasClass( "verticalStripes1_crossed" ).toString()=='true' )
	{
		alert('Sorry! This is a partial Plan. You cannot edit this plan.');
		return;
	}
	if( $( '#'+ selid).hasClass( "verticalStripes1_produced" ).toString()=='true' )
	{
		alert('Sorry! Production has been started. You cannot edit this plan.');
		return;
	}
	
	*/
	
	// Commented due to wrong msg on selection
	///leventType='move';
	///last_operation='move';
	//event_check_copy( selid );
	
	return;
	/*
	
	if($('#'+ $(el).attr('id')).attr( 'isnew' )!=0)
	{
		alert('You can Move/edit New Plan. PLease Undo.'); 
		return;
	}
	
	leventType='move';
	if( (pgroup*1)==0 )
	{ 
		alert('Please Click on a plan to Move.'); 
		return;
	}
	last_operation='move';
	event_check_copy( $(el).attr('id') );
	
	*/
	
	if( $('#last_event_id').val()!="")
	{
		var remov_selid=$('#last_event_id').val();
		var start_date=String($('#'+remov_selid).attr('start_date'));
		var end_date= String($('#'+remov_selid).attr('plan_end_date')); 
		var duration=String($('#'+remov_selid).attr('duration'));
		duration=duration.split(".");
		var line=remov_selid.split("-");
		var did=start_date.split("-");
		var ymd=did[2]+"-"+did[1]+"-"+did[0]; 
		$('#last_event_id').val('');
		
		for( var i=0; i<duration[0]; i++ )
		{
			var cldte=js_date_add( ymd, i )
			var vd=cldte.replace('-', ''); var clkdte=vd.replace('-', '');
			
			if( $('#tdbody-'+ line[1]+"-"+clkdte).hasClass( "verticalStripes_off" ).toString()=='true' )
				$('#tdbody-'+ line[1]+"-"+clkdte).css('border-bottom-color', '#FFFFA8');
			else
				$('#tdbody-'+ line[1]+"-"+clkdte).css('border-bottom-color','#F5F5F5' );
		}
	}
	if( $( '#'+ selid ).hasClass( "verticalStripes1_crossed" ).toString()=='true' ) //$( "p:first" ).hasClass( "selected" ).toString()
	{
		alert('Sorry! This is a partial Plan. You cannot edit this plan.');
		return;
	}
	
	var start_date=String($('#'+selid).attr('start_date'));
	var end_date= String($('#'+selid).attr('plan_end_date'));
	//alert()
	var duration= String($('#'+selid).attr('duration'));
	duration=duration.split(".");
	var line=selid.split("-");
	var did=start_date.split("-");
	var ymd=did[2]+"-"+did[1]+"-"+did[0];
	
	$('#last_event_id').val(selid);
	
	for( var i=0; i<duration[0]; i++ )
	{
		var cldte=js_date_add( ymd, i )
		var vd=cldte.replace('-', ''); var clkdte=vd.replace('-', '');
		$('#tdbody-'+ line[1]+"-"+clkdte).css('border-bottom-color','#F2B7E2' ); 
	}
}

function clear_td( cleartid, desttd, is_delete, inc )
{
	if( is_delete==0) //copy and clear
	{
		 $('#'+desttd).html( $('#'+cleartid).html());
		 $('#'+desttd).attr('name', $('#'+cleartid).attr('name'));
		 
		$('#'+desttd).attr('compstart',$('#'+cleartid).attr('compstart'));
		$('#'+desttd).attr('compinc',$('#'+cleartid).attr('compinc'));
		$('#'+desttd).attr('comptarg',$('#'+cleartid).attr('comptarg'));
		$('#'+desttd).attr('podtls',$('#'+cleartid).attr('podtls'));
		$('#'+desttd).attr('compid',$('#'+cleartid).attr('compid'));
		$('#'+desttd).attr('po_id',$('#'+cleartid).attr('po_id'));
		$('#'+desttd).attr('group_po_info',$('#'+cleartid).attr('group_po_info'));
		$('#'+desttd).attr('start_hr','0');
		$('#'+desttd).attr('end_hr','0');
		 
		$('#'+desttd).attr('is_partial','0');
		$('#'+desttd).attr('today_production',$('#'+cleartid).attr('today_production'));
		$('#'+desttd).attr('today_plan_qnty',$('#'+cleartid).attr('today_plan_qnty'));
		$('#'+desttd).attr('off_day_plan',$('#'+cleartid).attr('off_day_plan'));
		$('#'+desttd).attr('plan_qnty',$('#'+cleartid).attr('plan_qnty'));
		$('#'+desttd).attr('item_name_id',$('#'+cleartid).attr('item_name_id'));
		$('#'+desttd).css('border-radius', '0 0 0 0');
		
		if( $("#"+ cleartid).hasClass( "verticalStripes_off" ).toString()=='true' ) //$( "p:first" ).hasClass( "selected" ).toString()
		{
			$('#'+ cleartid).removeAttr('class');
			$('#'+ cleartid).attr('class','verticalStripes_off');
			
			if( $("#"+ desttd).hasClass( "verticalStripes_off" ).toString()=='true' )
				$('#'+ desttd).css('border-bottom-color', '#FFFFA8');
			else
				$('#'+ desttd).css('border-bottom-color', '#F5F5F5');
			$('#'+ desttd).attr('class','verticalStripes1_plan');
		}
		else
		{
			$('#'+ cleartid).removeAttr('class');
			$('#'+ cleartid).attr('class','verticalStripes1');
			
			if( $("#"+ desttd).hasClass( "verticalStripes_off" ).toString()=='true' )
				$('#'+ desttd).css('border-bottom-color', '#FFFFA8');
			else
				$('#'+ desttd).css('border-bottom-color', '#F5F5F5');
				
			$('#'+ desttd).attr('class','verticalStripes1_plan');
		}
		
		if( inc==0 )
		{
			$('#'+ desttd).css('border-radius', '70% 0 0 70%');
			leventnstart=desttd;
		}
	}
	else // Only clear
	{
		if( $("#"+ cleartid).hasClass( "verticalStripes_off" ).toString()=='true' ) //$( "p:first" ).hasClass( "selected" ).toString()
		{
			$('#'+ cleartid).removeAttr('class');
			$('#'+ cleartid).attr('class','verticalStripes_off');
		}
		else
		{
			$('#'+ cleartid).removeAttr('class');
			$('#'+ cleartid).attr('class','verticalStripes1');
		}
	} 
	
	//alert(cleartid)
	var lines=cleartid.split("-");
	var seldate=lines[2].substr(0, 2)+"-"+lines[2].substr(2, 2);//+""+lines[2].substr(0,2) 
	var htm='<span style="font-size:7px; color:#CCC;">'
			+  seldate +'<br>'
			+ line_arr[lines[1]]+'</span>';
 	
	$('#'+cleartid).html( htm );
	$('#'+cleartid).removeAttr('title');
	$('#'+cleartid).removeAttr('plan_group');
	$('#'+cleartid).removeAttr('start_td_id');
	$('#'+cleartid).removeAttr('comptarg');
	$('#'+cleartid).removeAttr('compid');
	$('#'+cleartid).removeAttr('plan_id');
	$('#'+cleartid).removeAttr('start_date');
	$('#'+cleartid).removeAttr('end_hr');
	$('#'+cleartid).removeAttr('duration');
	$('#'+cleartid).removeAttr('isedited');
	$('#'+cleartid).removeAttr('is_partial');
	$('#'+cleartid).removeAttr('isnew');
	$('#'+cleartid).removeAttr('style');
	$('#'+cleartid).removeAttr('item_name_id');
	
	$('#'+cleartid).removeAttr('group_po_info');
	$('#'+ cleartid).removeAttr('onclick');
	$('#'+ cleartid).removeAttr('compstart');
	$('#'+ cleartid).removeAttr('compinc');
	$('#'+ cleartid).removeAttr('podtls');
	$('#'+ cleartid).removeAttr('po_id');
	$('#'+ cleartid).removeAttr('line_id');
	$('#'+ cleartid).removeAttr('start_hr');
	$('#'+ cleartid).removeAttr('plan_qnty');
	$('#'+ cleartid).removeAttr('upd_id');
	$('#'+ cleartid).removeAttr('plan_end_date');
	$('#'+ cleartid).removeAttr('off_day_plan');
	$('#'+ cleartid).attr('valign','middle');
	$('#'+ cleartid).attr('name','middle');
	$('#'+ cleartid).attr('title','middle');
	$('#'+ cleartid).attr('onclick','paste_plan(this.id)');
	$('#'+ cleartid).attr('plan_group','0');
	$('#'+ cleartid).attr('onmouseover','showmenu(this.id)');
	$('#'+ desttd).attr('onclick','event_select(this.id)');
	
	$('#'+cleartid).removeAttr('order_complexity');
	$('#'+cleartid).removeAttr('today_plan_qnty');
	$('#'+cleartid).removeAttr('today_production');
	
	
	var clea=cleartid.split("-");
  	var ymdq=clea[2].substr(0,2)+"-"+clea[2].substr(2,2)+"-"+clea[2].substr(4,4);
	$('#'+ cleartid).attr('title',"Line: "+clea[1]+",Date:"+ ymdq);
}

function set_old_plan_attr( dur, lstid )
{
	var clkdt=lstid.split("-");
	var pln_grp=$('#'+ lstid).attr('plan_group');
	var podtlss=$('#'+ lstid).attr('podtls'); 
	//alert(podtlss)
	var pod=podtlss.split("**");
	
	var shpdt=pod[24].substr(6, 2)+"-"+pod[24].substr(4, 2)+"-"+pod[24].substr(0,4);
		
	var pln_qnty=$('#'+ lstid).attr('plan_qnty')-copied_plan_qnty;		
//18	
	//1**265**21676**1**28-02-2016****07-03-2016**0**17**7400**2**800**100**1200**0**28-02-2016**07-03-2016**18930**800,900,1000,1100,1200,1200,0,0,1200,1200,0,1200,1200,1200,1200,1200,400**2**3**9**2**2**20160930**1**3**RAY-009	
	
	var du=dur-ncopy_plan_length;
	var ne=pod[18].split(",");
	var ned=ne.slice(0, du);
	
	var tmpd=$('#'+ lstid).attr('group_po_info').split("_");
	tmpd[9]=pln_qnty;
	group_po_info= tmpd.join("_") ;
		
	for( var i=1; i<=dur; i++ ) // Set prev plan new options
	{
		var ymd=clkdt[2].substr(4,4)+"-"+clkdt[2].substr(2,2)+"-"+clkdt[2].substr(0,2);
		var cldte=js_date_add( ymd, -i )
		var vd=cldte.replace('-', ''); var clkdte=vd.replace('-', '');
		if(i==1)
		{  
			var endt=cldte; 
			
			var stdate=js_date_add( ymd, -du )
			
			var podtl=pod[0]+"**"+pod[1]+"**"+pod[2]+"**"+pod[3]+"**"+pod[4]+"**"+pod[5]+"**"+endt+"**"+pod[7]+"**"+pod[8]+"**"+pln_qnty+"**"+pod[10]+"**"+pod[11]+"**"+pod[12]+"**"+pod[13]+"**"+pod[14]+"**"+pod[15]+"**"+endt +"**"+pod[17]+"**"+ned+"**"+pod[19]+"**"+pod[20]+"**"+pod[21]+"**"+pod[22]+"**"+pod[23]+"**"+pod[24]+"**"+pod[25]+"**"+pod[26]+"**"+pod[27]+"**"+pod[28];
		}
		
		 
		if(pln_grp==$('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('plan_group'))
		{
			$('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('podtls',podtl);
			$('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('end_hr','');
			$('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('plan_qnty',pln_qnty);
			$('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('duration',du);
			$('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('isedited','1');
			$('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('plan_end_date',endt);
			$('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('group_po_info',group_po_info);
			 
			$('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('title','Start Date:: '+pod[15]+', End Date:: '+endt+', Quantity:: '+pln_qnty+', Item:: '+item_name[pod[19]]+', Order:: '+pod[17]+', Ship Date:: '+shpdt+', Buyer:: '+pod[25]+', SMV:: '+pod[26]+', Style:: '+pod[27]);
		
		}
		else  break ;
	}
}


var undo_event_start_date='';
var undo_event_start_line='';
var undo_event_start_duartion='';
var undo_event_start_qnty='';
var undo_event_start_updid='';
var undo_event_start_off='';
var undo_event_start_podtls='';
var undo_event_start_complx='';
var undo_event_start_group='';

var undo_event_new_start_id='';
var undo_event_new_dur='';
 
var act_copy_plan_length=0;
var ncopy_plan_length=0;


function paste_plan( pasteid )
{
	//alert('paste')
	
	if( $('#last_event_id').val()!="")
	{
		$('td[name="'+$('#last_event_id').val()+'"]').each(function() {
			if( $(this).hasClass( "verticalStripes_off" ).toString()=='true' )
				$(this).css('border-bottom-color', '#FFFFA8');
			else
				$(this).css('border-bottom-color','#F5F5F5' );
		});
	}
	$('#last_event_id').val()
	
	
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
	copy_plan_length='';
	var is_overlapped=false;
	var available_days=0;
	var did=pasteid.split("-");
	
	undo_event_start_date=clkdt[2].substr(0,2)+"-"+clkdt[2].substr(2,2)+"-"+clkdt[2].substr(4,4);
	undo_event_start_line=clkdt[1];
	undo_event_start_duartion=duration;
	undo_event_start_qnty=$('#'+vid).attr('plan_qnty');
	undo_event_start_updid=$('#'+vid).attr('upd_id');
	undo_event_start_off=$('#'+vid).attr('off_day_plan');
	undo_event_start_podtls=$('#'+vid).attr('podtls');
	undo_event_start_complx= $('#'+vid).attr('compid')+"**"+$('#'+vid).attr('compstart')+"**"+$('#'+vid).attr('compinc')+"**"+$('#'+vid).attr('comptarg')+"**"+undo_event_start_qnty+"**"+undo_event_start_qnty;
	undo_event_start_group=$('#'+vid).attr('plan_group');
	
	for( var i=0; i<ncopy_plan_length; i++ )
	{
		var ymd=did[2].substr(4,4)+"-"+did[2].substr(2,2)+"-"+did[2].substr(0,2);
		var cldte=js_date_add( ymd, i )
		var vd=cldte.replace('-', ''); var clkdte=vd.replace('-', '');
		if( Number( $('#tdbody-'+ did[1]+"-"+clkdte).attr('plan_group'))!=0)
		{
			if(Number( $('#tdbody-'+ did[1]+"-"+clkdte).attr('plan_group'))!=pln_grp)
				is_overlapped=true ;
		}
		else available_days++;
	}
	
	var test=vid;
	var need_break=false;
	if( is_overlapped==true)
	{
		need_break=true;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/planning_board_controller.php?action=overlapped_decision&ncopy_plan_length='+ncopy_plan_length+'&available_days='+available_days, '', 'width=350px,height=150px, center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_job");
			
			if (theemail.value!="")
			{
				if(theemail.value==1) // Push Forward
				{
					var variance=ncopy_plan_length-available_days;
					var clkdt=pasteid.split("-");
					var ymd=clkdt[2].substr(4,4)+"-"+clkdt[2].substr(2,2)+"-"+clkdt[2].substr(0,2);
					var pnextdate=js_date_add( ymd, (available_days*1) );
					var vd=pnextdate.replace('-', ''); var pnextdatef=vd.replace('-', '');
					var pnextid='tdbody-'+ clkdt[1]+"-"+pnextdatef;
					var ndur=Number($('#'+ pnextid).attr('duration'));
					var plan=$('#'+ pnextid).attr('plan_id');
					ndur=Math.floor(ndur);
					var pdt=pnextdate.split("-");
					var vfrom=pnextid;
					var npdt= js_date_add( ymd, (variance*1)+(available_days*1) ); 
					var vd=npdt.replace('-', ''); var npdt=vd.replace('-', '');
					var npdid='tdbody-'+ clkdt[1]+"-"+npdt;
					
					var plast_date=js_date_add( pdt[2]+"-"+pdt[1]+"-"+pdt[0], (ndur*1) );
					var plast_date_existing=js_date_add( pdt[2]+"-"+pdt[1]+"-"+pdt[0], (ndur*1)-1 );
					
					var vdf=plast_date.replace('-', ''); var plast_datef=vdf.replace('-', '');
					var pnextid='tdbody-'+ clkdt[1]+"-"+plast_datef;
					var avil=0;
					for( var i=0; i<variance; i++ ) // Checking available days in forward
					{
						var ymd=plast_datef.substr(4,4)+"-"+plast_datef.substr(2,2)+"-"+plast_datef.substr(0,2);
						var cldte=js_date_add( ymd, i )
						var vd=cldte.replace('-', ''); var clkdte=vd.replace('-', '');
						if( Number( $('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('plan_group'))==0)
						{
							var last_destination_id='#tdbody-'+ clkdt[1]+"-"+clkdte;
							avil++;
							if( avil==variance ) break;	
						}
						else break;
					}
					if(avil!=variance)
					{
						alert('No available space to push forward this plan');
						return;
					}
					
					var pdt=plast_date_existing.split("-");
					
					var clkdt=last_destination_id.split("-");
					var ymd=clkdt[2].substr(4,4)+"-"+clkdt[2].substr(2,2)+"-"+clkdt[2].substr(0,2);
					var tmp=leventType
					leventType='move';
			 		paste_replan_plan( npdid, vfrom, 1 ); // Move plan
			 		 
					var pln_grp=$('#'+vid).attr('plan_group');
					var duration= ($('#'+vid).attr('duration'));
					duration=duration.split(".");
					var clkdt=vid.split("-");
					var did=pasteid.split("-");
					set_old_plan_attr( duration[0], vid );
					leventType=tmp;
					paste_replan_plan( pasteid, vid, 1 ); // Create new plan
					
					return;
				}
				else if( theemail.value==2) // Resize  ok OK
				{
					var variance=ncopy_plan_length-available_days;
					
					var clkdt=test.split("-");
					var ymd=clkdt[2].substr(4,4)+"-"+clkdt[2].substr(2,2)+"-"+clkdt[2].substr(0,2);
					
					var cldte=js_date_add( ymd, variance );
					var vd=cldte.replace('-', ''); var clkdte=vd.replace('-', '');
					
					var test2='tdbody-'+ clkdt[1]+"-"+clkdte;
					var clkdt=test2.split("-");
					copy_plan_length=available_days;
					act_copy_plan_length=copy_plan_length;
					var pln_grp=$('#'+ test2).attr('plan_group');
					var durations= ($('#'+vid).attr('duration'));
					durations=durations.split(".");
					set_old_plan_attr( durations[0], test2 );
					
					var nplan=0;
					for(var l=1; l<=copy_plan_length; l++)
					{
						nplan+=(copied_qnty_arr[ncopy_plan_length-l]*1);
					}
					//var tmp=leventType
					//alert(copy_plan_length+"="+nplan+"="+ncopy_plan_length+"="+copied_qnty_arr)
					leventType='cut';
					copied_plan_qnty=nplan;
			 		paste_replan_plan( pasteid, test2,1 );
					duration[0]=durations[0];
					vid=test2;
					need_break==false
					leventType='cut';
					
					return;
				}
				else if( theemail.value==3 ) // Another Line or date
				{	
					clicked_id=vid;
					need_break=true;
					if(leventType=="cut")
						event_check( clicked_id )
					else if(leventType=="move")
						event_check_copy( clicked_id )
					
					alert( 'Please click on a desired blank location.' );
				
				}
				else if(theemail.value==4) // Discard
				{
					need_break=true;
					//if(leventstart=='') return;
						
					var pln_grp=$('#'+leventstart).attr('plan_group');
					var duration= String($('#'+leventstart).attr('duration'));
					duration=duration.split(".");
					var clkdt=leventstart.split("-");
					for( var i=0; i<=copy_plan_length; i++ )
					{
						var ymd=clkdt[2].substr(4,4)+"-"+clkdt[2].substr(2,2)+"-"+clkdt[2].substr(0,2);
						var cldte=js_date_add( ymd, i )
						var vd=cldte.replace('-', ''); var clkdte=vd.replace('-', '');
						
						if(pln_grp==$('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('plan_group'))
						{
							//$('#tdbody-'+ line[1]+"-"+clkdte).css('border-color','#F2B7E2' );
							 $('#tdbody-'+ clkdt[1]+"-"+clkdte).css('border-bottom-color','#F5F5F5' );
							//$('#tdbody-'+ clkdt[1]+"-"+clkdte).css('background', '#0AE7E1');
						}
						else  break ;
					}
					
				} 
			}
		}
	}
	
	
	
	
	else//if( need_break==false) 
	{
		
		act_copy_plan_length=ncopy_plan_length;
		if( leventType=='cut')
		{
			set_old_plan_attr( duration[0], vid );
		}
		else // delete old plan/Clear
		{
			
		}
		//alert('adas');
	 	paste_replan_plan( pasteid, vid, 0 );
	 	return;
	}
}

function paste_replan_plan( pasteid, clicked_id, prev_settings )
{
	// alert(prev_settings);
	
	
	if( !prev_settings ) var prev_settings=0;
	
	if( leventType=='cut' || leventType=='forwards' )
	{  //alert( leventType )
		var duration= copy_plan_length;
		var plan_qnty= copied_plan_qnty;
		k=(Array.max(pln_grp_indx))+1;
		pln_grp_indx.push(k);
		var updid=0;
		var clear_length=act_copy_plan_length;
		
		if( $('#'+clicked_id).attr('compinc')*1>0)
			var learn_yes_no=2;
		else
			var learn_yes_no=2;
	}
	//alert(copied_plan_qnty+"-"+copy_plan_length)
	else
	{
		var duration= $('#'+clicked_id).attr('duration');
		var plan_qnty= $('#'+clicked_id).attr('plan_qnty');
		var updid=$('#'+clicked_id).attr('upd_id');
		var clear_length=duration;
		if( $('#'+clicked_id).attr('compinc')*1>0 )
			var learn_yes_no=1;
		else
			var learn_yes_no=2;
	}
	
	var off_day_include= $('#'+clicked_id).attr('off_day_plan');
	var compid= $('#'+clicked_id).attr('compid');
	var off_day_plan= $('#'+clicked_id).attr('off_day_plan');
	var podtls= $('#'+clicked_id).attr('podtls');
	var tmppo=podtls.split("**");
	 
	var did=clicked_id.split("-");
	var ymd=did[2].substr(4,4)+"-"+did[2].substr(2,2)+"-"+did[2].substr(0,2);
	var off_days_list=0;
	var durationold=duration;
	
	var new_id=pasteid;
	
	var did=new_id.split("-");
	var dmy=did[2].substr(0,2)+"-"+did[2].substr(2,2)+"-"+did[2].substr(4,4);
	var ymd=did[2].substr(4,4)+"-"+did[2].substr(2,2)+"-"+did[2].substr(0,2);
	// alert(prev_settings)
	
	group_po_info=$('#'+clicked_id).attr('group_po_info');
	//group_po_info=group_po_det[tmpd[0]];
	var grouo_po=2;
	var npos=group_po_info.split("|*|");
	if( npos.length>1 )
		grouo_po=1;
					
	if( prev_settings==1 )   // chanegd to keep pld settign here ===
	{
		var str=off_day_plan+"__"+learn_yes_no;
		str=str.split("__");
		
		if( str[1]==2) // Complex No
		{
			if( $('#'+clicked_id).attr('compid')!=0 )
				var compx= $('#'+clicked_id).attr('compid')+"**"+$('#'+clicked_id).attr('comptarg')+"**0**"+$('#'+clicked_id).attr('comptarg')+"**"+plan_qnty+"**"+plan_qnty;
			else
			{
				var comn=$('#'+clicked_id).attr('compstart').split(",");
				var compx= $('#'+clicked_id).attr('compid')+"**"+comn[comn.length-1] +"**0**"+$('#'+clicked_id).attr('comptarg')+"**"+plan_qnty+"**"+plan_qnty;
			}
			
			if(str[0]==1) // Offday
				var duration=Number(get_production_days( compx, plan_qnty, dmy ,str[0], did[1]));
			else
				var duration=Number(get_production_days( compx, plan_qnty, dmy ,str[0], did[1]));
			
			var podtls=updid+"**"+did[1]+"**"+$('#'+clicked_id).attr('po_id')+"**1**"+dmy+"**"+$('#'+clicked_id).attr('start_hr')+"**"+$('#'+clicked_id).attr('end_date')+"**"+$('#'+clicked_id).attr('end_hr')+"**"+duration+"**"+plan_qnty+"**"+$('#'+clicked_id).attr('compid')+"**"+$('#'+clicked_id).attr('comptarg')+"**0**"+$('#'+clicked_id).attr('comptarg')+"**0**"+dmy+"**"+dmy+"**"+$('#'+clicked_id).attr('name')+"**"+day_qnty.join()+"**"+$('#'+clicked_id).attr('item_name_id')+"**"+tmppo[20]+"**"+tmppo[21]+"**"+tmppo[22]+"**"+tmppo[23]+"**"+tmppo[24]+"**"+tmppo[25]+"**"+tmppo[26]+"**"+tmppo[27]+"**"+tmppo[27]; 
		}
		else
		{
			var compx= $('#'+clicked_id).attr('compid')+"**"+$('#'+clicked_id).attr('compstart')+"**"+$('#'+clicked_id).attr('compinc')+"**"+$('#'+clicked_id).attr('comptarg')+"**"+plan_qnty+"**"+plan_qnty;
			
			if(str[0]==1) // Offday
				var duration=Number(get_production_days( compx, plan_qnty, dmy ,str[0], did[1]));
			else
				var duration=Number(get_production_days( compx, plan_qnty, dmy ,str[0], did[1]));
			
			var podtls=updid+"**"+did[1]+"**"+$('#'+clicked_id).attr('po_id')+"**1**"+dmy+"**"+$('#'+clicked_id).attr('start_hr')+"**"+$('#'+clicked_id).attr('end_date')+"**"+$('#'+clicked_id).attr('end_hr')+"**"+duration+"**"+plan_qnty+"**"+$('#'+clicked_id).attr('compid')+"**"+$('#'+clicked_id).attr('compstart')+"**"+$('#'+clicked_id).attr('compinc')+"**"+$('#'+clicked_id).attr('comptarg')+"**0**"+dmy+"**"+dmy+"**"+$('#'+clicked_id).attr('name')+"**"+day_qnty.join()+"**"+$('#'+clicked_id).attr('item_name_id')+"**"+tmppo[20]+"**"+tmppo[21]+"**"+tmppo[22]+"**"+tmppo[23]+"**"+tmppo[24]+"**"+tmppo[25]+"**"+tmppo[26]+"**"+tmppo[27]+"**"+tmppo[27];
		}
		//alert(compx+"="+  plan_qnty+"="+  dmy+"="+ str[0]);
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
	// alert(did[1]+"=="+dmy+"=="+duration+"=="+podtls+"=="+plan_qnty)
		undo_event_new_start_id=new_id;
		undo_event_new_dur=duration;
 		
		var tmpd=group_po_info.split("_");
		tmpd[9]=plan_qnty;
		group_po_info= tmpd.join("_") ;
 		
		draw_chart( did[1], dmy, duration, podtls, plan_qnty, str[0], updid, grouo_po, group_po_info );
		clear_overlapped_plan();
		clicked_id='';
	}
	else
	{
		//alert(grouo_po+"="+pasteid+"="+clicked_id+"="+group_po_info);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/planning_board_controller.php?action=move_plan_decision&plan_qnty='+plan_qnty+'&compid='+compid+'&off_days_list='+off_days_list+'&off_day_include='+off_day_include+'&learn_yes_no='+learn_yes_no, '', 'width=500px,height=155px, center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_job");
			
			if ( theemail.value!="")
			{
				var str=theemail.value;
				 //alert(str);
				str=str.split("__");
				if( leventType!='cut' ) updid=$('#'+clicked_id).attr('upd_id') ;
			//	alert(str[1])
				plan_qnty=plan_qnty-(str[2]*1);
				if( str[1]==2 ) // complex No
				{
					if( $('#'+clicked_id).attr('compid')!=0 )
						var compx= $('#'+clicked_id).attr('compid')+"**"+$('#'+clicked_id).attr('compstart')+"**"+$('#'+clicked_id).attr('compinc')+"**"+$('#'+clicked_id).attr('comptarg')+"**"+plan_qnty+"**"+plan_qnty;
					else
					{
						var comn=$('#'+clicked_id).attr('compstart').split(",");
						var compx= $('#'+clicked_id).attr('compid')+"**"+comn[comn.length-1] +"**0**"+$('#'+clicked_id).attr('comptarg')+"**"+plan_qnty+"**"+plan_qnty;
					}
					//alert(compx);
					if( str[0]==1 ) // Offday
						var duration=Number(get_production_days( compx, plan_qnty, dmy ,str[0],did[1]));
					else
						var duration=Number(get_production_days( compx, plan_qnty, dmy ,str[0],did[1]));
						 
					var podtls=updid+"**"+did[1]+"**"+$('#'+clicked_id).attr('po_id')+"**1**"+dmy+"**"+$('#'+clicked_id).attr('start_hr')+"**"+$('#'+clicked_id).attr('end_date')+"**"+$('#'+clicked_id).attr('end_hr')+"**"+duration+"**"+plan_qnty+"**"+$('#'+clicked_id).attr('compid')+"**"+$('#'+clicked_id).attr('comptarg')+"**0**"+$('#'+clicked_id).attr('comptarg')+"**0**"+dmy+"**"+dmy+"**"+$('#'+clicked_id).attr('name')+"**"+day_qnty.join()+"**"+$('#'+clicked_id).attr('item_name_id')+"**"+tmppo[20]+"**"+tmppo[21]+"**"+tmppo[22]+"**"+tmppo[23]+"**"+tmppo[24]+"**"+tmppo[25]+"**"+tmppo[26]+"**"+tmppo[27]+"**"+str[2]; 
				}
				else
				{
					var compx= $('#'+clicked_id).attr('compid')+"**"+$('#'+clicked_id).attr('compstart')+"**"+$('#'+clicked_id).attr('compinc')+"**"+$('#'+clicked_id).attr('comptarg')+"**"+plan_qnty+"**"+plan_qnty;
				
					
					if( str[0]==1 ) // Offday
						var duration=Number(get_production_days( compx, plan_qnty, dmy ,str[0],did[1]));
					else
						var duration=Number(get_production_days( compx, plan_qnty, dmy ,str[0],did[1]));
					
					var podtls=updid+"**"+did[1]+"**"+$('#'+clicked_id).attr('po_id')+"**1**"+dmy+"**"+$('#'+clicked_id).attr('start_hr')+"**"+$('#'+clicked_id).attr('end_date')+"**"+$('#'+clicked_id).attr('end_hr')+"**"+duration+"**"+plan_qnty+"**"+$('#'+clicked_id).attr('compid')+"**"+$('#'+clicked_id).attr('compstart')+"**"+$('#'+clicked_id).attr('compinc')+"**"+$('#'+clicked_id).attr('comptarg')+"**0**"+dmy+"**"+dmy+"**"+$('#'+clicked_id).attr('name')+"**"+day_qnty.join()+"**"+$('#'+clicked_id).attr('item_name_id')+"**"+tmppo[20]+"**"+tmppo[21]+"**"+tmppo[22]+"**"+tmppo[23]+"**"+tmppo[24]+"**"+tmppo[25]+"**"+tmppo[26]+"**"+tmppo[27]+"**"+str[2]; 
				}
				
				
				var vid=clicked_id;
				var didw=vid.split("-");
				var ymd=didw[2].substr(4,4)+"-"+didw[2].substr(2,2)+"-"+didw[2].substr(0,2);
				var off_days_list=0;
				// alert("####"+podtls);
				//alert(podtls) 
				for( var i=0; i<clear_length; i++ )
				{
					var cldte=js_date_add( ymd, i )
					var vd=cldte.replace('-', ''); var clkdte=vd.replace('-', '');
					//clear_td( cleartid, desttd, is_delete, inc )
					clear_td( "tdbody-"+ didw[1] +"-"+ clkdte, '', 1, i );
				}
				//alert(duration);
				undo_event_new_start_id=new_id;
				undo_event_new_dur=duration;
				//alert(duration);
						//	line, stdate, wid, podtls, planqnty, off_day_plan , eupdid )
				var tmpd=group_po_info.split("_");
				tmpd[9]=plan_qnty;
				group_po_info= tmpd.join("_") ;
				
				draw_chart( did[1], dmy, duration, podtls, plan_qnty, str[0], updid ,grouo_po,group_po_info);
				clear_overlapped_plan();
				clicked_id='';
			}
		}
	}
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
//	alert("test")
	if(leventType=="cut")
	{
		$('#tdbody-'+ cldt[1]+"-"+ stdate).attr('upd_id','0');
		$('#tdbody-'+ cldt[1]+"-"+ stdate).attr('isedited','0');
		$('#tdbody-'+ cldt[1]+"-"+ stdate).attr('isnew','1');
		//$('#'+desttd).attr('isedited','1');
		$('#tdbody-'+ cldt[1]+"-"+stdate ).attr('plan_group',k);
	}
	else if(leventType=="move")
	{
		$('#tdbody-'+ cldt[1]+"-"+ stdate).attr('upd_id',$('#'+ oid).attr('upd_id'));
		//$('#tdbody-'+ cldt[1]+"-"+ stdate).attr('upd_id','0');
		$('#tdbody-'+ cldt[1]+"-"+ stdate).attr('isedited','1');
		$('#tdbody-'+ cldt[1]+"-"+ stdate).attr('isnew','0');
		$('#tdbody-'+ cldt[1]+"-"+stdate ).attr('plan_group',$('#'+ oid).attr('plan_group'));
	}
		
	clear_td( oid, 'tdbody-'+ cldt[1]+"-"+stdate, 0, inc );
	
	$('#tdbody-'+ cldt[1]+"-"+stdate ).attr('title','Plan Start Date:: '+vstart+' ,Plan End Date:: '+vend);
	
	$('#tdbody-'+ cldt[1]+"-"+stdate ).attr('start_td_id',stdid);
	$('#tdbody-'+ cldt[1]+"-"+stdate ).attr('start_date',vstart);
	$('#tdbody-'+ cldt[1]+"-"+stdate ).attr('plan_end_date',vend);
	$('#tdbody-'+ cldt[1]+"-"+stdate ).attr('duration',act_copy_plan_length);
	$('#tdbody-'+ cldt[1]+"-"+ stdate).attr('plan_id','');
	$('#tdbody-'+ cldt[1]+"-"+ stdate).attr('line_id',cldt[1]);
		

}

var clicked_id="";
var copy_plan_length='';
var copied_plan_qnty='';
var copied_qnty_arr=new Array;

var revise_adjacent_prev_plan=0;
var revise_adjacent_next_plan=0;
var revise_adjacent_prev_qnty=0;

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
		$('#'+ revise_adjacent_prev_plan).css('background','');
		$('#'+ revise_adjacent_prev_plan ).addClass('verticalStripes1'+ccol) //oclass+""+ccol
		$('#'+ revise_adjacent_prev_plan ).css('background-color',$('#'+ prev_date_b4 ).css('background'));
		
		if( plnqnt>prdqnty )  //This cond added on 26-07-15,
			$('#'+ revise_adjacent_prev_plan).html( "<font color='#000000' >"+plnqnt+"</font><br> <font color='#000000' >"+prdqnty+"</font>");
		else
			$('#'+ revise_adjacent_prev_plan).html( "<font color='#000000' >"+plnqnt+"</font> <br><font color='#000000' >"+prdqnty+"</font>");	
		 
	}
	if( revise_adjacent_next_plan!=0 )
	{
		//alert(revise_adjacent_next_plan)
		leventType='forwards';
		last_operation='forwards';
		
		forwd_days=-1;
		event_check_copy_forward( revise_adjacent_next_plan, -1, 1 );
		//alert(revise_adjacent_next_plan) 	
		
	}
	
	revise_adjacent_next_plan=0;
	revise_adjacent_prev_plan=0;
}
var prev_date_b4='';
function check_overlapped_plan_old( chkid )
{
	var podtls= $('#'+chkid).attr('podtls');
	var tmppo=podtls.split("**");
	
	var clkdt=chkid.split("-");
	
	var endd=$('#'+chkid).attr('plan_end_date').split("-");
	var ymd=endd[2]+"-"+endd[1]+"-"+endd[0];
	var next_date=js_date_add( ymd, 1 )
	var vd=next_date.replace('-', ''); var next_date=vd.replace('-', '');
	var podtlsnxt= $('#tdbody-'+clkdt[1]+"-"+next_date).attr('podtls');
	if(podtlsnxt!=undefined)
	{
		var podtlsnxt=podtlsnxt.split("**");
		if( ( podtlsnxt[28]*1)>0 )  revise_adjacent_next_plan=$('#tdbody-'+clkdt[1]+"-"+next_date).attr('id');;
	}
	
	var startd=$('#'+chkid).attr('start_date').split("-");
	var ymd=startd[2]+"-"+startd[1]+"-"+startd[0];
	
	var prev_date=js_date_add( ymd, -1 )
	prev_date_b4=js_date_add( ymd, -2 )
	var vd=prev_date_b4.replace('-', ''); var prev_date_b4=vd.replace('-', '');
	prev_date_b4='tdbody-'+clkdt[1]+"-"+prev_date_b4;
	alert(ymd+'='+prev_date)
	var vd=prev_date.replace('-', ''); var prev_date=vd.replace('-', '');
	var podtlsprv= $('#tdbody-'+clkdt[1]+"-"+prev_date).attr('podtls');
	if(podtlsprv!=undefined)
	{
		var podtlsprv=podtlsprv.split("**");
		if( (tmppo[28]*1)>0 )  revise_adjacent_prev_plan=$('#tdbody-'+clkdt[1]+"-"+prev_date).attr('id');
		revise_adjacent_prev_qnty=podtlsprv[28];
	}
	alert(revise_adjacent_prev_plan)
	//plan_end_date  start_date
	
}
function check_overlapped_plan( chkid )
{
	revise_adjacent_prev_plan=0;
	revise_adjacent_next_plan=0;

	var podtls= $('#'+chkid).attr('podtls');
	var tmppo=podtls.split("**");
	var clkdt=chkid.split("-");
	
	var endd=$('#'+chkid).attr('plan_end_date').split("-");
	var ymd=endd[2]+"-"+endd[1]+"-"+endd[0];
	var next_date=js_date_add( ymd, 1 )
	var vd=next_date.replace('-', ''); var next_date=vd.replace('-', '');
	var podtlsnxt= $('#tdbody-'+clkdt[1]+"-"+next_date).attr('podtls');
	if(podtlsnxt!=undefined)
	{
		var podtlsnxt=podtlsnxt.split("**");
		if( ( podtlsnxt[28]*1)>0 )  revise_adjacent_next_plan=$('#tdbody-'+clkdt[1]+"-"+next_date).attr('id');;
	}
	
	if( (tmppo[28]*1)>0 )
	{
		var startd=$('#'+chkid).attr('start_date').split("-");
		var ymd=startd[2]+"-"+startd[1]+"-"+startd[0];
		var prev_date=js_date_add( ymd, -1 )
		var vd=prev_date.replace('-', ''); var prev_date=vd.replace('-', '');
		if( leventType!='cut' ) revise_adjacent_prev_plan='tdbody-'+clkdt[1]+"-"+prev_date;
	}
	
	//alert(revise_adjacent_prev_plan+"=@@@="+revise_adjacent_next_plan)
	
	/*
	prev_date_b4=js_date_add( ymd, -2 )
	var vd=prev_date_b4.replace('-', ''); var prev_date_b4=vd.replace('-', '');
	prev_date_b4='tdbody-'+clkdt[1]+"-"+prev_date_b4;
	alert(ymd+'='+prev_date)
	var vd=prev_date.replace('-', ''); var prev_date=vd.replace('-', '');
	var podtlsprv= $('#tdbody-'+clkdt[1]+"-"+prev_date).attr('podtls');
	if(podtlsprv!=undefined)
	{
		var podtlsprv=podtlsprv.split("**");
		if( (tmppo[28]*1)>0 )  revise_adjacent_prev_plan=$('#tdbody-'+clkdt[1]+"-"+prev_date).attr('id');
		revise_adjacent_prev_qnty=podtlsprv[28];
	}
	alert(revise_adjacent_prev_plan)*/
	//plan_end_date  start_date
}
function event_check( copyid ) // on cut
{
	clicked_id=copyid;
	leventstart=copyid;
	var pln_grp=$('#'+copyid).attr('plan_group');
	var duration= ($('#'+copyid).attr('duration'));
	
	check_overlapped_plan( copyid );
	
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
		
		if( pln_grp==$('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('plan_group') )
		{
			$('#tdbody-'+ clkdt[1]+"-"+clkdte).css('border-color','#F2B7E2' );
			copied_plan_qnty=(copied_plan_qnty*1)+($('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('today_plan_qnty')*1);
			copy_plan_length++;
			copied_qnty_arr[i]=($('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('today_plan_qnty')*1);
		}
		else  break ;
	}
}

function event_check_copy( copyid ) 
{
	clicked_id=$('#'+copyid).attr('start_td_id');
	leventstart=clicked_id;
	var pln_grp=$('#'+clicked_id).attr('plan_group');
	var duration= ($('#'+clicked_id).attr('duration'));
	check_overlapped_plan( clicked_id );
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
		else  break ;
	}
	
}

function event_check_copy_forward( copyid, fdays, keep_set ) // Done
{
	if(!keep_set) var keep_set=0; 
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
	var podtlsevt=($('#'+clicked_id).attr('podtls')).split("**");
	copied_plan_qnty=( podtlsevt[28]*1);
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
	
	act_copy_plan_length=copy_plan_length;
	stymd=js_date_add( ymd, fdays );
	var vd=stymd.replace('-', ''); var clkdte=vd.replace('-', '');
	var pastid='tdbody-'+ clkdt[1]+"-"+clkdte;
 //alert(pastid+"=="+leventstart)
	paste_replan_plan( pastid, leventstart, keep_set );
	
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
	if( leventType=="delete" )
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
	else if( leventType=="cut" )
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
			//clear_td( cleartid, desttd, is_delete, inc )
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
	else if( leventType=="move" )
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
	else if( leventType=="new" )
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
			ft= ( ( (dRight >= cLeft) && (dLeft <= cRight) ) && ( ((dTop + 15) >= cTop) && (dTop <= (cTop + 15)) ) );
		 	if(ft==true) return false;
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
		var leftt=$(window).scrollLeft()-120;
		
		var vid=this.id; //tdbody-173-16032015
		var vidd=vid.split("-")
		
	//$('#scroll_contents').html('Line No: '+vidd[1]+'; Date: '+ vidd[2].substr(0,2)+"-"+vidd[2].substr(2,2)+"-"+vidd[2].substr(4,4) );//scroll_contents
		if( $('#'+this.id).attr('plan_group')==undefined || $('#'+this.id).attr('plan_group')==0)
		{
			$('#scroll_contents').html($('#'+this.id).attr('title'));
			$('#scroll_contents').css({
				 'top' : (((e.clientY*1)+($(window).scrollTop()*1))+5) ,
				 'left' : ((e.clientX*1)+(leftt*1)),
				 'display':'block', 
			});
		}
		else
		{
			$('#scroll_contents').css({
				 'display':'none', 
			});
		}
		clearTimeout( movementTimer );
		movementTimer = setTimeout(function()
		{
			$('#selection_line_hor').css({
				 'display':'none', 
			});
			$('#selection_line_vert').css({
				 'display':'none', 
			});
			$('#scroll_contents').css({
				 'display':'none', 
			});
		}, 1000);
}) 
   
