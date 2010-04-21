<p class="pagination">    
    <a href="/day/view/<?php echo $id+1;?>" class="forwards">forwards&nbsp;&raquo;</a>
    <a href="/day/view/<?php echo $id-1;?>">&laquo;&nbsp;backwards</a>
</p>
<h1><?php echo $date; ?></h1>
<?php foreach($post as $key=>$value){
    echo $value;
} ?>
<div id="profileControls">
    <div id="twitter-connect-placeholder"></div>
    <div id="twitter-connect-logout"></div>
</div>

<div id="comment" style="display:none;">
    <form method="post" action="/comment/create">
        <h4>Submit a comment to my site</h4>
        <input type="hidden" name="author" id="author" value=""/>
        <input type="hidden" name="token" value="<?php echo $token; ?>"/>
        <textarea rows="6" cols="30" name="comment" id="comment"></textarea>
        <button type="submit">Publish</button>
    </form>
</div>
<script type="text/javascript">
    twttr.anywhere(function (T) {
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
          $('#author').val(screenName);
          $('#twitter-connect-placeholder').append("Logged in as " + profileImageTag + " " + screenName);
          $("#twitter-connect-placeholder").append('<button id="signout" type="button">Sign out of Twitter</button>');
          $("#signout").bind("click", function () {
              twttr.anywhere.signOut();
            });
           $("#comment").show(0);
        } else {
          T("#twitter-connect-placeholder").connectButton();
        };
    });
</script>