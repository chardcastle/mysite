(function($){
	// blank plugin definition .. should I need one
	$.fn.runHardcastleMove = function(options) {
	  var defaults = {
		url: '',
		req: [], 
		isForward: '' 
	  };
	  // Extend our default options with those provided.
	  var opts = $.extend(defaults, options);
	  // Our plugin implementation code goes here.
	  this.each(function(){
			$(this)
			.find("div[id^=box] .inner")
			.fadeOut(0)
			.end() // hide data targets and force call back for request
			.fadeTo(0,1,function(){
				$.getJSON(opts.url+"/true",function(json){
					$.each(json,function(key,value){
						var ele = $("div[id*=box"+value["index"]+"]");
						// its a pain, but consider the homepage
						if(value["index"] !== 1){
							ele.removeClass("home");
						}else{
							ele.addClass("home");
						}		
						ele // update the div with new content
						.find(".body")
						.html(value["body"])
						.end()
						.find(".pubDate")
						.html(value["title"])
						.end()
						.find(".close")
						.attr("href","/day/view/"+value["id"]);							
					});
				});
			})				
			.find("#container") // Snap the container back in position 		
			.css("left",0)		// trick usr into thinking its not moved?
			.end()
			.find("div[id^=box] .inner") // Slowly fade content in ... mmm nice
			.fadeIn(600);
	  });
	  return this;
	};
})(jQuery);
/*
 * One long init function
*/
(function($){

   $("body")
	.fadeTo(0,1,function(){
		/* use fadeTo as forced link in this chain
		 * save required data
		*/ 
		// store current page digit
		$.data(document.body,"currentPage",1);
		// make data for each element
		$("#container")
		.children("div.outer")
		.each(function(i,item){
			//console.log("ere alright");
			$.data(item,"pos",{top:$(item).css("top"),left:$(item).css("left")});			
		});
		// TEST via console
		$("#container")
		.children("div.outer")
		.each(function(i,item){
			//console.log($.data(item,"pos").left);
		});	
	})
	.find(".pagination a")
	.each(function(i,item){
		$(item).click(function(e){
				e.preventDefault();
				// decide direction based on last choice
				// * forward is the addition of viewport width to contents left position
				// * backwards is minus move value of the viewport
				var click = {
					url: $(this).attr("href"),
					req: [], 
					isForward: '' 
				}				
				click.req = click.url.split("/").reverse();				
				click.isForward = ($.data(document.body,"currentPage") < click.req[0])?'-':'';
				// save request for decision on next click
				$.data(document.body,"currentPage",click.req[0]);	
				// respond			
				$("#container")
				.animate({left:(click.isForward)+$("#container").width()},600,function(){
					$("body").runHardcastleMove(click);					
				})
				// Update navigation selection
				$(".paginationjs")
				.find("a")
				.removeClass("selected")
				.end()
				.find("a[href="+click.url+"]")
				.addClass("selected");
			return false;
		});
	})
	.end()
	.find(".pagination")
	.attr("class","paginationjs")
	.click(function(){
		/*
		 no link for that .. was either first page
		 or "previous" which will always be first page
		*/
	})	
	// make new navigation stucture
})(jQuery);
