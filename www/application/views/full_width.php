<div class="paginationFullWidth">	
	<span>
		<?php if($prevUrl){?>
		    <a href="<?php echo $prevUrl?>">&laquo;&nbsp;backwards</a>
		<?php } ?>
		&nbsp;
		<?php if($nextUrl){ ?>
			<a href="<?php echo $nextUrl?>">forwards&nbsp;&raquo;</a>
		<?php } ?>	
	</span>
	<a href="#" class="back">Back</a>
</div>
<br style="clear:both"/>
<h1><?php echo $date; ?></h1>
<?php foreach($post as $key=>$value){
    echo $value;
} ?>
<p>&nbsp;</p>
<div id="user-comments">
    <?php if($comments){
        echo "<h4>User comments</h4>".$comments;
        }else{?>
      <p>Nobody has commented, why don't you?</p>
    <?php  } ?>
</div>
<p>&nbsp;</p>
<h4 class="comment-title"></h4>
<div id="profileControls">
    <div id="twitter-connect-placeholder"></div>
    <div id="twitter-connect-logout"></div>
</div>
<p>&nbsp;</p>
<div id="comment" style="display:none;">
    <form method="post" action="/comment/create">
        <h4>Submit a comment to my site</h4>
        <input type="hidden" name="author" id="author" value=""/>
        <input type="hidden" name="token" value="<?php echo $token; ?>"/>
        <input type="hidden" name="time_line_ref" value="<?php echo $id; ?>"/>
        <textarea rows="6" cols="30" name="body" id="body"></textarea>
        <br/>
        <button type="submit">Publish</button>
    </form>
</div>
<script type="text/javascript">
	$(function(){
		if(history.length){
			$('.back').click(function(e){
				e.preventDefault();
				history.go(-1);
			});
		}
	});
</script>

<?php if(kohana::config('config.anywhere_key')){ ?>	
<script type="text/javascript">	
    twttr.anywhere(function (T) {
        // Only offer comment feature if js is available.
        $(".comment-title").text("Comment with your Twitter account:");
        // connect with twitter
        var currentUser,
            screenName,
            profileImage,
            profileImageTag;

        if (T.isConnected()) {
          currentUser = T.currentUser;
          screenName = currentUser.data('screen_name');
          profileImage = currentUser.data('profile_image_url');
          profileImageTag = "<img src='" + profileImage + "'/>";
          $.post("/comment/obfuscateUserName",{"username":screenName},function(json){
            $('#author').val(json.username);
        },"json");
          $('#twitter-connect-placeholder').append("Logged in as " + profileImageTag + " " + screenName);
          $("#twitter-connect-placeholder").append('<button id="signout" type="button">Sign out of Twitter</button>');
          $("#signout").bind("click", function () {              
              twttr.anywhere.signOut();             
            });
           $("#comment").show(0);
        } else {
          T("#twitter-connect-placeholder").connectButton({authComplete: function(user) {
            // triggered when auth completed successfully            
            $("#comment").show(300);
            // Protect username
            $.post("/comment/obfuscateUserName",{"username":user.attributes.name},function(json){
                $('#author').val(json.username);
            },"json");
          },
          signOut: function() {
            // triggered when user logs out
                $("#comment").hide(300,function(){
                    $(this).value("You have been signed out of Twitter.");
                });
            }});
          }
    });
</script>
<?php } ?>
