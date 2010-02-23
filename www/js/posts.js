(function($){
	// plugin definition
	$.fn.scrambel = function(options) {
	  var defaults = {
	    speed: 1000,
	    limit: 300,
	    animate:false
	  };
	  // Extend our default options with those provided.
	  var opts = $.extend(defaults, options);
	  // Our plugin implementation code goes here.
	  this.each(function(){
		var top = Math.floor(Math.random()*opts.limit);
		var left = Math.floor(Math.random()*opts.limit);
			//console.log(top+" "+left+" "+lim);
		if(opts.animate){
			if($(this).is(":animated")){
				$(this).stop();
			}
			$(this)
			.animate({top:top+"px",left:left+"px"},opts.speed);	
		}else{			
			$(this)
			.css("top",top+"px")
			.css("left",left+"px");	  	
		}
	  });
	  // return
	  return this;
	};
})(jQuery);

$(function(){
	var lim = $("#container").width()-$("#container div:first").width();
	var originLocations = [];
	var numberOfPages = 0;
	$("body")
	.find("#container")
		.find("div")
		.each(function(){
			var pos = {
				top:$(this).offset().top,
				left:$(this).offset().left
			};
			originLocations[$(this).attr("class")] = pos;			
		})
		.end()
	.end()
	.find("#clearUp")
	.click(function(e){
		e.preventDefault();
		$("#container")
		.find("div")		
		.each(function(){
			if($(this).is(":animated")){
				$(this).stop();
			}		
			var className = $(this).attr("class");
			var top = originLocations[className].top;
			var left = originLocations[className].left;
			
			$(this).animate({top:top+"px",left:left+"px"},1000);
		});		
	})
	.end()
	.find("a#scrambel")
	.click(function(e){
		e.preventDefault();
		$("#container")
			.find("div")
			.each(function(){
				$(this).scrambel({limit:lim,animate:true});
			})
			.end()
		.end()	
	})
	.end()
	.find(".close")
	.click(function(e){
		e.preventDefault();		
		$(this).parent().hide(300);
	})
	.end()
	.find("#washAndGo")
	.click(function(e){
		e.preventDefault();
		console.log("here");
			$("#container")
				.find("div")
				.each(function(){
					if($(this).is(":animated")){
						$(this).stop();
					}
					var className = $(this).attr("class");
					var top = originLocations[className].top;
					var left = originLocations[className].left;
				
					$(this)
					.scrambel({limit:lim,animate:true})
					.animate({top:top+"px",left:left+"px"},1000);
				})
				.end()
			.end();
			return false;	
	})
	.end()
	.find("#navigation a")
	.each(function(i,item){		
		numberOfPages++;
		var ele = $("<li><a href=''></a></li>")
		.find("a")
		.text(numberOfPages)
		.click(function(e){
			e.preventDefault();
			var req = (($(this).text()-1)*9);
			console.log("/index.php/Posts/posts_json/P"+req);
			$.getJSON("/index.php/Posts/posts_json/P"+req,function(data){				
				$.each(data,function(key,value){					
					$("."+value["index"]).html(value["body"]);
				});
				console.log(data);				
			});			
			return false;		
		});
		$("#jsnav").append(ele);
	});
	// make new navigation stucture
	
});
