/**
* CONTROLS URL and HISTORY related to AJAX Loading
*
*/

( function( $ ){

	// Default to the current location.
	var strLocation = window.location.href;
	var strHash = window.location.hash;
	var strPrevLocation = "";
	var strPrevHash = "";
	var history_arry = new Array( document.location.toString() );
	var history_count = 0;
	var direction = 'start';
	var last_known_location = document.location.toString();

	// This is how often we will be checkint for
	var intIntervalTime = 200;

	// This method removes the pound from the hash.
	var fnCleanHash = function( strHash ){
		return(
			strHash.substring( 1, strHash.length )
		);
	}

	// bind url -> change
	var registerChange = function( objData ){

		// url changed so do this.
		var frame = document.location.toString();

		/**
		* back button check
		*
		* track history incase user
		* activates the back button.
		* Needed to retroactively load AJAX content
		* once page is loaded from cache.
		**/
		if( frame == last_known_location ) {
			direction = 'back';
			history_arry.pop();
			var prev = --history_count - 1;
			last_known_location = history_arry[prev];

			// reload if is first page in history and the page is static
			if(history_count == 0 && !frame.match('#!') ) {
				window.location.reload(true);
			}
		} else {
			direction = 'forward';

			// add to array
			last_known_location = history_arry[ history_count ];
			history_arry[ ++history_count ] = frame;

		}

		// if we are going backward load aux url
		if( frame.match('~') && direction == 'back') {
			// load auxilery url
			var aux_url = frame.split("~")[1];
			// ajax request data from aux url
			$.get( urlPrefix + '://' + useSite + '/' + aux_url, function(data) {
				/**
				* Pull content from requested data
				* and paste it in to live content.
				*/
				// title
				//var myBodyClass = data.match(/<body\sid="*"\sclass=("[\w\d]*")>*<\/body>/);
				//console.log(data);
				//var myBodyClass = data.match(/(.*<\s*body[^>]*>)|(<\s*\s*body\s*\>.+)/);
				var htmlObject = document.createElement('html');
				htmlObject.innerHTML = data;
				var klass = htmlObject.querySelector("body").className;
				document.querySelector('body').className = klass;
				//var matches = html.match(/<div\s+id="LiveArea">[\S\s]*?<\/div>/gi);
				//var matches = data.match(/body\s+class=[\'\"]{0,1}(\w+)/);
				//
				//var matches = matches[0].replace(/(<\/?[^>]+>)/gi, ''); // Strip HTML tags?

				console.log(klass);

				document.title = jQuery("<head></head>").html(data).find("title").text();
				// class
				var body_class = jQuery("<div></div>").html(data).find("html").attr('class');
				//alert(body_class);
				//$('html,body').attr("class", body_class );
				// content
				var aux_html = jQuery("<div></div>").html(data).find("#limiter").html();
				$("#limiter").html(aux_html);
			});

		} else if( frame.match('#!') ) {

			// if there is a secondary url, disregard it
			if( frame.match('~') ) {
				// if we aren't going back we are ignoring the tilde urls
			} else {
				// request ajax content
				$().ajaxLoad(objData.currentHash);
			}
		}

		// Add the URL change.
		var jLog = $( "#log" );
		jLog.append( "<li>Hash changed from <strong>" + objData.previousHash + "</strong> -&gt; <strong>" + objData.currentHash + "</strong></li>" );


	}

	// changes in the window location.
	var fnCheckLocation = function(){

		// Check to see if the location has changed.
		if( strLocation != window.location.href ){

			// Store the new and previous locations.
			strPrevLocation = strLocation;
			strPrevHash = strHash;
			strLocation = window.location.href;
			strHash = window.location.hash;

			// The location has changed.
			registerChange({ currentHref: strLocation, currentHash: fnCleanHash( strHash ), previousHref: strPrevLocation, previousHash: fnCleanHash( strPrevHash ) });

		}
	}

	// Set an interval to check the location changes.
	setInterval( fnCheckLocation, intIntervalTime );
}) ( jQuery );





$(document).ready(function() {

	/**
	* if there is ajax data to be
	* loaded on first load
	*/

	var frame = document.location.toString();
	if( frame.match('#!') ) {

		// we are ajax loading
		url = frame.split("#!")[1];

		// don't process ajax requests if not logged in
		//if( loggedIn != 1 )
		//	window.location = urlPrefix + '://' + useSite + url;

		// if on load there is a tilde, load the tilde content
		if( url.match('~') ) {
			var prime_url = frame.split('~')[1];
			//
			window.location = urlPrefix + '://' + useSite + prime_url;

		} else {
			$().ajaxLoad( '!' + url );
		}
	}
});


$.fn.ajaxLoad = function() {
	var url = arguments[0];
	//
	//return false;
	var urlclean = url.split("!")[1];

	var bread = null;
	var html_foot = null;
	var html_content = null;
	var content = null;
	var html_head = null;
	
	$.get( urlPrefix + '://' + useSite + '/' + url, function(d) {
		
		$(d).find('doc_parts').each( function() {
		
			document.title =  $(this).find("doc_title").text();
			bread = $(this).find("doc_breadcrumb").text() ;
			head_html = $(this).find("doc_header").text();
			html_content = $(this).find("doc_content").text();
			html_foot = $(this).find("doc_footer").text();

			$('html,body').attr("class", $(this).find("doc_class").text() );
			$('head').append( Url.decode( head_html ) );
			$('#ajax').html( Url.decode( html_content + html_foot ) );

			//$(this).find("doc_content").text();
		});


		if(html_content == null)
			window.location = urlPrefix + '://' + useSite + '/';

		$().prepContent('#content');

		if( $(document).scrollTop() > 100 )
			$('body').animate({ scrollTop: 0 }, 800);

		// custom - google
		// track the page view
		if(typeof _gaq !== 'undefined')
			_gaq.push(['_trackPageview', '/' + urlclean]);

	});
	
	

	return;
}
