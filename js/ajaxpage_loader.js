/***********************************************
* Ajax Page Fetcher- by JavaScript Kit (www.javascriptkit.com)
***********************************************/

var callurl = {
	loadingmessage: '<img src="images/loading.gif" alt="Please wait for your results">',
	//document.getElementById('myspan').innerHTML = '<img src="images/loading.gif" alt="Please wait for your results">';
	exfilesadded: "",
	
	connect:function( containerid, pageurl, bustcache, jsfiles, cssfiles ) {
		var page_request = false;
		var bustcacheparameter = "";
		if( window.XMLHttpRequest) page_request = new XMLHttpRequest();	// if Mozilla, IE7, Safari etc
		else if( window.ActiveXObject ) {								// if IE6 or below
			try {
				page_request = new ActiveXObject( "Msxml2.XMLHTTP" );
			}
			catch( e ) {
				try {
					page_request = new ActiveXObject( "Microsoft.XMLHTTP" );
				}
				catch( e ) {}
			}
		}
		else return false;
		if( pageurl == "" ) return false;
		// alert(pageurl);
		 npagel=pageurl.split("mid="); 
		 npagel=npagel[1].split("&"); 
		// page_container.php?m=tna/tna_task_template.php?permission=1_1_1_1&mid=104&fnat=TNA Template Entry__0
		change_menu_color( npagel[0] )
		var ajaxfriendlyurl = pageurl.replace( /^http:\/\/[^\/]+\//i, "http://" + window.location.hostname + "/" ); 
		page_request.onreadystatechange = function() { callurl.loadpage( page_request, containerid, pageurl, jsfiles, cssfiles ); }
		if( bustcache ) bustcacheparameter = ( ajaxfriendlyurl.indexOf("?") != -1 ) ? "&" + new Date().getTime() : "?" + new Date().getTime();	//if bust caching of external page
		
		document.getElementById(containerid).innerHTML = callurl.loadingmessage //Display "fetching page message"
		page_request.open( 'GET', ajaxfriendlyurl + bustcacheparameter, true );
		page_request.send( null );
	},
	
	loadpage:function( page_request, containerid, pageurl, jsfiles, cssfiles ) {
		if( page_request.readyState == 4 && ( page_request.status == 200 || window.location.href.indexOf("http") == -1 ) ) 		
		{
			document.getElementById(containerid).innerHTML = page_request.responseText;
			for( var i = 0; i < jsfiles.length; i++ ) this.loadjscssfile( jsfiles[i], "js" );
			for( var i = 0; i < cssfiles.length; i++ ) this.loadjscssfile( cssfiles[i], "css" );
			this.pageloadaction( pageurl );	//invoke custom "onpageload" event
		}
	},
	
	createjscssfile:function( filename, filetype ) {
		if( filetype == "js" ) {	//if filename is a external JavaScript file
			var fileref = document.createElement('script');
			fileref.setAttribute( "type", "text/javascript" );
			fileref.setAttribute( "src", filename );
		}
		else if( filetype == "css" ) {	//if filename is an external CSS file
			var fileref = document.createElement("link");
			fileref.setAttribute( "rel", "stylesheet" );
			fileref.setAttribute( "type", "text/css" );
			fileref.setAttribute( "href", filename );
		}
		return fileref;
	},
	
	loadjscssfile:function( filename, filetype ) {	//load or replace (if already exists) external .js and .css files
		if( this.exfilesadded.indexOf( "[" + filename + "]" ) == -1 ) {	//if desired file to load hasnt already been loaded
			var newelement = this.createjscssfile( filename, filetype );
			document.getElementsByTagName("head")[0].appendChild( newelement );
			this.exfilesadded += "[" + filename + "]";	//remember this file as being added
		}
		else {	//if file has been loaded already (replace/ refresh it)
			var targetelement = ( filetype == "js" ) ? "script" : ( filetype == "css" ) ? "link" : "none";	//determine element type to create nodelist using
			var targetattr = ( filetype == "js" ) ? "src" : ( filetype == "css" ) ? "href" : "none";	//determine corresponding attribute to test for
			var allsuspects = document.getElementsByTagName( targetelement );
			for( var i = allsuspects.length; i >= 0; i-- ) {	//search backwards within nodelist for matching elements to remove
				if( allsuspects[i] && allsuspects[i].getAttribute( targetattr ) != null && allsuspects[i].getAttribute( targetattr ).indexOf( filename ) != -1 ) {
					var newelement = this.createjscssfile( filename, filetype );
					allsuspects[i].parentNode.replaceChild( newelement, allsuspects[i] );
				}
			}
		}
	},
	
	pageloadaction:function( pageurl ) {
		this.onpageload( pageurl );	//call customize onpageload() function when an ajax page is fetched/ loaded
	},
	
	onpageload:function( pageurl ) {
		//do nothing by default
		/*var permission_str = pageurl.substr( pageurl.length - 7 );
		var permission = permission_str.split( '_' );
		
		if( document.getElementById('content_iframe') ) {
			if( permission[0] != 1 ) {	//Save not allowed
				alert( 'You don\'t have Save permission.' );
				$('#content_iframe').contents().find('input[value="Save"]').attr('disabled','disabled').css('background-color', 'red');
			}
		}
		else {
			if( permission[0] != 1 ) {	//Save not allowed
				alert( 'You don\'t have Save permission.' );
				$('input[value="Save"]').each(function() {
					$(this).attr('disabled','disabled').css('background-color', 'red');
				});
			}
		}*/
	},
	checkValidAction:function(urlStr){
		var urlArr=urlStr.split('?');
		 //alert(urlArr[1]);
		
		 var xhttp = new XMLHttpRequest();
		  xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) { 
			 if( this.responseText == 0 ){
				 alert("Session time out or you have no permission in this page. Please login again.");
				 window.location.href ='logout.php';
				}
			}
		  };
		  xhttp.open("GET", "tools/valid_user_action.php?data="+urlArr[1], true);
		  xhttp.send();		
		
	},
	load:function( containerid, pageurl, bustcache, jsfiles, cssfiles ) {
		var jsfiles = ( typeof jsfiles == "undefined" || jsfiles == "" ) ? [] : jsfiles;
		var cssfiles = ( typeof cssfiles == "undefined" || cssfiles == "" ) ? [] : cssfiles;
		this.checkValidAction(pageurl);
		this.connect( containerid, pageurl, bustcache, jsfiles, cssfiles );
	}
}	//End object

//Sample usage:
//1) ajaxpagefetcher.load("mydiv", "content.htm", true)
//2) ajaxpagefetcher.load("mydiv2", "content.htm", true, ["external.js"])
//3) ajaxpagefetcher.load("mydiv2", "content.htm", true, ["external.js"], ["external.css"])
//4) ajaxpagefetcher.load("mydiv2", "content.htm", true, ["external.js", "external2.js"])
//5) ajaxpagefetcher.load("mydiv2", "content.htm", true, "", ["external.css", "external2.css"])


function change_menu_color( mid )
{
	 
		$('#jQ-menu ul li').find('a').each(function() 
		{
			 $(this).css('color', 'black');
			
		}); 
		$('#lid'+mid).css('color', '#DF5EBF');
		 
}