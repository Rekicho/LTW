<?php
	include_once('../includes/database.php');
	
	function insertStory($user_id, $title, $story, $channel_id, &$story_id) {
		$db = Database::instance()->db();
        $stmt = $db->prepare('INSERT INTO opinion VALUES(NULL, NULL, ?, ?, ?, datetime("now"), ?)');
		$stmt->execute(array($title, $story, $channel_id, $user_id));
		$stmt = $db->prepare('SELECT opinion_id FROM opinion WHERE parent_id IS NULL AND opinion_title = ? AND opinion_text = ? AND channel_id = ? AND user_id = ?');
		$stmt->execute(array($title, $story, $channel_id, $user_id));
		$stories = $stmt->fetchAll();
		$story_id = $stories[count($stories)-1]['opinion_id'];
	}

	function getAllStories() {
		$db = Database::instance()->db();
		$stmt = $db->prepare('SELECT * FROM opinion WHERE parent_id IS NULL');
		$stmt->execute();
		return $stmt->fetchAll();
	}

	function getAllChannelStories($channel) {
		$db = Database::instance()->db();
		$stmt = $db->prepare('SELECT * FROM opinion INNER JOIN channel ON opinion.channel_id = channel.channel_id WHERE parent_id IS NULL AND channel_name = ?');
		$stmt->execute(array($channel));
		return $stmt->fetchAll();
	}

	function getAllSubscribedStories($user_id){
		$db = Database::instance()->db();
		$stmt = $db->prepare('SELECT * FROM subscription JOIN opinion ON subscription.channel_id = opinion.channel_id WHERE parent_id IS NULL AND subscription.user_id = ?');
		$stmt->execute(array($user_id));
		return $stmt->fetchAll();
	}

	function getStory($story_id) {
		$db = Database::instance()->db();
		$stmt = $db->prepare('SELECT * FROM opinion WHERE opinion_id = ? AND parent_id IS NULL');
		$stmt->execute(array($story_id));
		return $stmt->fetch();
	}

	function getStories($user_id){
		$db = Database::instance()->db();
		$stmt = $db->prepare('SELECT * FROM opinion WHERE user_id = ? AND parent_id IS NULL');
		$stmt->execute(array($user_id));
		return $stmt->fetchAll();
	}

	function getComments($user_id){
		$db = Database::instance()->db();
		$stmt = $db->prepare('SELECT * FROM opinion WHERE user_id = ? AND parent_id NOT NULL');
		$stmt->execute(array($user_id));
		return $stmt->fetchAll();
	}
?>