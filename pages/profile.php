<?php
	include_once('../includes/session.php');
	include_once('../includes/date.php');
	include_once('../database/db_user.php');
	include_once('../database/db_story.php');
	include_once('../database/db_vote.php');
	include_once('../database/db_channel.php');
	include_once('../templates/tpl_common.php');
	include_once('../templates/tpl_stories.php');
	include_once('../templates/tpl_comments.php');

	if (!isset($_GET['username']))
		die(header('Location: ../index.php'));

	$username = urldecode($_GET['username']);

	getUserInfo($user_id, $username, $realname, $email, $birthday, $join_date, $bio);

	if($user_id == '')
		header('Location: ../pages/404.php');

	$stories = array_reverse(getStories($user_id));
	$comments = array_reverse(getComments($user_id));
	$now = time();

	$score = 0;

	for($i = 0; $i < count($stories); $i++){
		$stories[$i]['username'] = $username;
		$stories[$i]['score'] = getScore($stories[$i]['opinion_id']);
		$stories[$i]['comments'] = getNumberComments($stories[$i]['opinion_id']);
		$stories[$i]['channel_name']= getChannelName($stories[$i]['channel_id']);
		$score += $stories[$i]['score'];

		if(isset($_SESSION['user_id']))
			$stories[$i]['vote'] = getVote($stories[$i]['opinion_id'], $_SESSION['user_id']);
	}

	for($i = 0; $i < count($comments); $i++){
		$comments[$i]['username'] = $username;
		$comments[$i]['score'] = getScore($comments[$i]['opinion_id']);
		$comments[$i]['replies'] = getNumberComments($comments[$i]['opinion_id']);
		$score += $comments[$i]['score'];

		if(isset($_SESSION['user_id']))
			$comments[$i]['vote'] = getVote($comments[$i]['opinion_id'], $_SESSION['user_id']);
	}

	$formatted_birthday = formatDate($birthday);
	$formatted_join_date = formatDate($join_date);
	
	getPicture($user_id,$path,$alt);

	$page = 'profile.php?username='.$username;

	draw_header(true);
?>

	<h2><?=htmlentities($username)?> Profile</h2>
	<h3><img src=<?=$path?> alt=<?=$alt?> width="100" height="100"></h3>
	<h3>Username: <?=htmlentities($username)?></h3>
	<h3>Score: <?=$score?></h3>
	<h3>Name: <?=htmlentities($realname)?></h3>
	<h3>Bio: <?=htmlentities($bio)?></h3>
	<h3>Email: <?=htmlentities($email)?></h3>
	<h3>Birthday: <?=htmlentities($formatted_birthday)?></h3>
	<h3>Join Date: <?=$formatted_join_date?></h3>

<?php
	if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id){?>

<section id="edit">
	<header>
		<h2>Edit</h2>
	</header>
	<form method="post" action="../actions/action_edit_profile.php" enctype="multipart/form-data">
		<input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
		<label>Profile Picture:
			<input type="file" name="img" value="" accept="image/png, image/jpeg">
		</label>
		<label>Username:
			<input type="text" name="username" value="<?=htmlentities($username)?>" required>
		</label>
		<label>Name:
			<input type="text" name="realname" value="<?=htmlentities($realname)?>">
		</label>
		<label>Email:
			<input type="email" name="email" value="<?=htmlentities($email)?>" required>
		</label>
		<label>Birthday:
			<input type="date" name="birthday" value="<?=htmlentities($birthday)?>">
		</label>
		<label>Bio:
			<textarea name="bio"><?=htmlentities($bio)?></textarea>
		</label>

		<input type="submit" value="Edit Profile">
	</form>
</section>
<?php } 
	draw_stories($stories, false);
	draw_comments_header($comments,false);
?>

<?php draw_footer(); ?>
