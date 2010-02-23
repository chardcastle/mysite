/*
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2009, EllisLab, Inc.
 * @license		http://expressionengine.com/docs/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
/*
 * ExpressionEngine Filebrowser Plugin
 *
 * @package		ExpressionEngine
 * @subpackage	Control Panel
 * @category	Control Panel
 * @author		ExpressionEngine Dev Team
 * @link		http://expressionengine.com
 */
(function(g){var h,i,f,m,j,d=0,l=EE.filebrowser.theme_url+"images/publish_file_manager_loader.gif",a=EE.PATH_CP_GBL_IMG+"default.png",c;g.ee_filebrowser=function(){h=20;g.ee_filebrowser.endpoint_request("setup",function(p){i={};f={};c=g(p.manager).appendTo(document.body);for(var q in p.directories){if(!d){d=q}i[q]=""}e()})};g.ee_filebrowser.endpoint_request=function(p,q,r){if(!r&&g.isFunction(q)){r=q;q={}}q=g.extend(q,{action:p});g.getJSON(EE.BASE+"&"+EE.filebrowser.endpoint_url+"&"+g.param(q),r)};g.ee_filebrowser.add_trigger=function(p,q,r){if(!r&&g.isFunction(q)){r=q;q="userfile"}g(p).click(function(){g("#upload_file",c).attr("name",q);c.dialog("open");j=function(s){r.call(p,s,q)};return false})};g.ee_filebrowser.change_dim=function(r,q){if(g("#cloned #constrain:checked").length==0){return}if(q.attr("id")=="resize_width"){var p=r.height/r.width;g("#resize_height").val(Math.floor(p*q.val()))}else{var p=r.width/r.height;g("#resize_width").val(Math.floor(p*q.val()))}};g.ee_filebrowser.submit_image_edit=function(p,q){g.ajax({type:"POST",url:EE.BASE+"&"+EE.filebrowser.endpoint_url+"&action=edit_image",data:g("#image_edit_form").serialize(),success:function(r){p.name=r;p.dimensions='width="'+p.width+'" height="'+p.height+'" ';g.ee_filebrowser.clean_up(p,q)},error:function(r){if(g.ee_notice){g.ee_notice(r.responseText,{type:"error"})}else{r.responseText=r.responseText.replace(/<p>/,"");alert(r.responseText.replace(/<\/p>/,""))}}})};g.ee_filebrowser.clean_up=function(p,q){g("#page_0 .items").html(q);c.dialog("close");j(p)};var k={upload_start:function(){g("#progress",c).show()},upload_success:function(p){i[p.directory]="";g("#page_"+p.directory+" .items",c).empty();g("#progress",c).hide();var q=g("#page_0 .items").html();if(p.is_image){g("#page_0 .items").html('<button id="resize_image"><span>'+EE.lang.resize_image+"</span></button> "+EE.lang.or+' <button class="place_image"><span>'+EE.lang.return_to_publish+"</span></button>").fadeIn("fast");g(".place_image").click(function(){g.ee_filebrowser.clean_up(p,q)});g("#resize_image").click(function(){g("#page_0 .items").html(g(".image_edit_form_options").clone().css("display","block").attr("id","cloned"));g("#resize_width").val(p.width);g("#resize_height").val(p.height);g("#file").val(p.url_path);g("#resize_width, #resize_height").keyup(function(){g.ee_filebrowser.change_dim(p,g(this))});g(".place_image").click(function(){g.ee_filebrowser.clean_up(p,q)});g(".icons li").click(function(){var r=g(this).attr("class");switch(r){case"rotate_90r":rotate=90;break;case"rotate_90l":rotate=270;break;case"rotate_180":rotate=180;break;case"rotate_flip_vert":rotate="vrt";break;case"rotate_flip_hor":rotate="hor";break;default:rotate="none"}g("#image_edit_form input:text").val("");g("#image_edit_form").prepend('<input type="hidden" name="rotate" value="'+rotate+'"/>');g.ee_filebrowser.submit_image_edit(p,q)});g("#image_edit_form").submit(function(){if(g("#crop_width").val()==""&&g("#crop_height").val()==""&&g("#crop_x").val()==""&&g("#crop_y").val()==""&&g("#resize_width").val()==""&&g("#resize_height").val()==""){g.ee_filebrowser.clean_up(p,q)}else{p.width=g("#resize_width").val();p.height=g("#resize_height").val();g.ee_filebrowser.submit_image_edit(p,q)}return false})})}else{g.ee_filebrowser.clean_up(p,q)}},upload_error:function(p){g("#progress",c).hide();if(g.ee_notice){g.ee_notice(p.error,{type:"error"})}else{p.error=p.error.replace(/<p>/,"");alert(p.error.replace(/<\/p>/,""))}console.log(p)}};g.ee_filebrowser=g.extend(g.ee_filebrowser,k);function b(q){if(!q.id in i){return}i[q.id]=q.files;f[q.id]=q.url;var p="",r=0,t=[],s=g("#page_"+q.id,c).scrollable();g.each(q.files,function(u,v){if(u%h==0&&u!=0){t.push(p);p=""}if(v.mime!==false&&v.mime.indexOf("image")<0){p+='<div><div title="{filedir_'+q.id+"}|"+v.name+'"><img title="'+a+'" src="'+a+'" alt="default thumbnail" /></div>'+v.name+"</div>"}else{p+="<div><div title='"+v.dimensions+'\'><img class="image" title="{filedir_'+q.id+"}"+v.name+'" src="';if(!v.has_thumb){p+=a;if(u<h){g.ajax({type:"POST",url:EE.BASE+"&"+EE.filebrowser.endpoint_url+"&action=ajax_create_thumb",data:"XID="+EE.XID+"&dir="+q.id+"&image="+v.name})}}else{if(u<h){p+=q.url+"_thumbs/thumb_"+v.name}else{p+=l}}p+='" alt="thumbnail" /></div>'+v.name+"</div>"}r++});t.push(p);p=t.join('</div><div class="item">');s.getItemWrap().append('<div class="item">'+p+"</div>");s.reload();g(".item > div",c).unbind();g(".item > div",c).click(function(){var v,u=!(g(this).find("img").attr("src")==a);if(u===true){v={is_image:true,thumb:g(this).find("img").attr("src"),directory:d,dimensions:g(this).find("div").attr("title"),name:g(this).find("img").attr("title").split("}")[1]}}else{v={is_image:false,thumb:a,directory:d,name:g(this).find("div").attr("title").split("|")[1]}}j(v);c.dialog("close")});if(s.getPageAmount()==1){g("#nav_controls_"+q.id,c).hide()}else{g("#nav_controls_"+q.id,c).show()}}function o(p){if(i[p]==""){g.ee_filebrowser.endpoint_request("directory_contents",{directory:p},b)}}function n(p){var r=g("#page_"+p).scrollable();page_index=(r.getPageIndex()=="")?0:r.getPageIndex();if(g("#page_"+p+" .item:eq("+page_index+") img").length>0){var q={};g("#page_"+p+" .item:eq("+page_index+") img").each(function(u){var w=g(this);var v=/^\{filedir_(\d+)\}/;var t=v.exec(w.attr("title"));if(w.attr("src")==l){q[u]=f[t[1]]+"/_thumbs/thumb_"+w.attr("title").replace(v,"");g('<img src="'+q[u]+'" />').load(function(){w.attr("src",q[u])})}else{if(w.attr("class")=="image"&&w.attr("src")==a){w.attr("src",l);var s=w.attr("title").substring(w.attr("title").indexOf("}")+1);g.ajax({type:"POST",url:EE.BASE+"&"+EE.filebrowser.endpoint_url+"&action=ajax_create_thumb",data:"XID="+EE.XID+"&dir="+p+"&image="+s,success:function(x){w.attr("src",f[t[1]]+"/_thumbs/thumb_"+w.attr("title").replace(v,""))},error:function(){w.attr("src",a)}})}}})}}function e(){c.dialog({width:730,height:495,resizable:false,position:["center","center"],modal:true,draggable:true,title:EE.filebrowser.window_title,autoOpen:false,open:function(p,q){g("#file_manager_main").scrollable().getConf().keyboard="static";g("#file_manager_main").scrollable().reload();g(".vertscrollable").scrollable().getConf().keyboard=true;g(".vertscrollable").scrollable().reload()},close:function(p,q){g("#file_manager_main").scrollable().getConf().keyboard=false;g("#file_manager_main").scrollable().reload();g("#main_navi li:first").click()}});g("#file_manager_main").scrollable({vertical:true,size:1,clickable:false,speed:250,keyboard:false,onSeek:function(p){d=g("li:eq("+p+")","#main_navi").attr("id").replace(/main_navi_/,"");o(d);n(d);focused_tab.scrollable(p).focus();g("#page_"+d).scrollable().reload()}}).navigator("#main_navi");focused_tab=g(".vertscrollable").scrollable({size:1,clickable:false,nextPage:".newThumbs",prevPage:".prevThumbs",keyboard:false,onBeforeSeek:function(p){if(g("#file_manager_main:visible").length==0){this.getConf().keyboard=false;this.reload()}},onSeek:function(p){n(d)}}).navigator({navi:".navi"});o(d);n(d);focused_tab.eq(0).scrollable().focus();g("#upload_form",c).submit(g.ee_filebrowser.upload_start)}})(jQuery);