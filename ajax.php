<?php
require_once('inc/config.php');
require_once('inc/connect.php');

session_start();
$id = 0;
$score = 0;

if(isset($_SESSION['game_id']) && $_SESSION['game_id'] && !isset($_GET['newGame']))
{
	$id = (int)$_SESSION['game_id'];
	$query = sprintf("select * from `%sgames` where id=%d", 
				mysql_real_escape_string($mysql_prefix),
				$id);
	$result = mysql_query($query);
	if($result != null && mysql_num_rows($result) > 0)
	{
		$id = (int)$_SESSION['game_id'];
		$data = mysql_fetch_array($result);
		$score = $data['score'];
	}
	else{
		$_GET['newGame'] = true;
		$id = -1;
		$score = 0;
	}
}
if(isset($_GET['newGame']))
{
	$query = sprintf("insert into `%sgames` set score=0, starttime=%u",
				mysql_real_escape_string($mysql_prefix),
				time());
	
	mysql_query($query);
	$_SESSION['game_id'] = mysql_insert_id();
	$id = (int)$_SESSION['game_id'];

	//cleanup
	$query = sprintf("delete from `%squestions` where game in (select id from `%sgames` where starttime < %u)",
				mysql_real_escape_string($mysql_prefix),
				mysql_real_escape_string($mysql_prefix),
				(time() - (int)$timeout));
	mysql_query($query);
	
	$query = sprintf("delete from `%sgames` where starttime < %u",
				mysql_real_escape_string($mysql_prefix),
				(time()-(int)$timeout));
	mysql_query($query);
}


if(isset($_GET['nextQuestion']) && isset($id) && is_int($id) && $id >= 0)
{
	$query = sprintf("select * from `%squestions` where game=%d",
				mysql_real_escape_string($mysql_prefix),
				$id);
	if(mysql_num_rows(mysql_query($query)) < $question_limit)
	{
		$query = sprintf("select * from `%spoints` where id not in(select point from `%squestions` where game=%d) order by RAND() limit 1",
					mysql_real_escape_string($mysql_prefix),
					mysql_real_escape_string($mysql_prefix),
					$id);
		
		$result = mysql_query($query);
		if(!mysql_num_rows($result))
			die();
		$data = mysql_fetch_array($result);
		print $data['name'];
		
		$query = sprintf("insert into `%squestions` set point=%d, game=%d, start_time=%u, total_time=-1",
					mysql_real_escape_string($mysql_prefix),
					(int)$data['id'],
					(int)$id,
					time());
		mysql_query($query);
	}
}

if(isset($_GET['getAnswer']) && $id)
{
	if(is_numeric($_GET['x']) && is_numeric($_GET['y']))
	{
		$x = $_GET['x'];
		$y = $_GET['y'];
	}
	else
		die();

	$query_q = sprintf("select * from `%squestions` where game=%d and total_time=-1",
				mysql_real_escape_string($mysql_prefix),
				(int)$id);
	$result_q = mysql_query($query_q);
	if($result_q == null || mysql_num_rows($result_q) <= 0)
		die();
	$data_q = mysql_fetch_array($result_q);

	$query = sprintf("select * from `%spoints` where id=%d",
				mysql_real_escape_string($mysql_prefix),
				(int)$data_q['point']);
	$result = mysql_query($query);
	if($result == null || mysql_num_rows($result) <= 0)
		die();
	$data = mysql_fetch_array($result);

	$distance = round(8.82*sqrt(($data['x']-$x)*($data['x']-$x) + ($data['y']-$y)*($data['y']-$y)), 0);
	$time = time()-$data_q['start_time'];
	$addedscore = calculateScore($distance, $time);

	$query_x = sprintf("select * from `%squestions` where game=%d",
				mysql_real_escape_string($mysql_prefix),
				(int)$id);
	if(mysql_num_rows(mysql_query($query_x)) >= $question_limit){
		print $data['x']."\n".$data['y']."\n".($score+$addedscore)."\n".calculateMessage($distance, $time)."<br />You missed by ".$distance."km<br />Your total score is ".($score+$addedscore)."<br /><span id=\"save_highscore\">Your name: <input type=\"text\" name=\"name\" id=\"highscore_name\" /><input type=\"button\" name=\"submit\" onclick=\"ajaxSaveScore()\" value=\"Save highscore\" /></span><br /><a href=\"#game\" onClick=\"ajaxNextQuestion(true)\">TRY AGAIN</a>";
	}
	else{
		print $data['x']."\n".$data['y']."\n".($score+$addedscore)."\n".calculateMessage($distance, $time)."<br />You missed by ".$distance."km<br /><a href=\"#game\" onclick=\"ajaxNextQuestion(false);\">NEXT&raquo;</a>";
	}
	
	$query = sprintf("update `%sgames` set score=%d where id=%d",
				mysql_real_escape_string($mysql_prefix),
				($score+$addedscore),
				$id);
	mysql_query($query);
	
	$query = sprintf("update `%squestions` set total_time=%u where id=%d",
				mysql_real_escape_string($mysql_prefix),
				$time,
				(int)$data_q['id']);
	mysql_query($query);
}

function calculateScore($distance, $time){
	/*$score = 2000;
	if($distance <= 1000)
		$score *= (1- $distance*0.5/1000);
	else if($distance > 1000 && $distance < 4000)
		$score *= (2/3 - $distance*0.5/3000);
	else
		$score = 0;


	if($time < 25)
		$score *= 1 - 0.032*$time;
	else
		$score = 0;

	return round($score,0);*/

	$score = 3000;
	if($distance <= 200)
		$score *= (1- $distance*0.4/200);
	else if($distance > 200 && $distance <= 1000)
		$score *= (0.2 + 0.5 - $distance*0.4/800);
	else if($distance > 1000 && $distance <= 4000)
		$score *= (4/15 - $distance*0.2/3000);
	else
		$score = 0;


	if($time < 25)
		$score *= 1 - $time*0.5/25;
	else
		$score = 0;

	return round($score,0);
}

function calculateMessage($distance, $time){
	$message = "Good job!";
	
	if($distance < 200)
		$message = "Excellent!";
	if($distance < 60)
		$message = "Now that is serious accuracy!";
	if($distance > 1500)
		$message = "Not quite!";
	if($time > 15)
		$message = "Nice. But try faster for a higher score.";
	if($time < 3)
		$message = "Whoa! That was fast!";
	if($time > 30)
		$message = "You have to be faster than that!";
	if($distance > 3000)
		$message = "Well that was a bit off.";
	
	return $message;
}

if(isset($_GET['highscores']))
{
	$query = sprintf("select * from `%shighscores` order by score desc",
				mysql_real_escape_string($mysql_prefix));
	$result=mysql_query($query);
	$i = 1;
	$lisatud = array();
	print "<table>";
	while($result != null && $data = mysql_fetch_array($result))
	{
		if(!in_array(strtolower($data['name']), $lisatud))
		{
			?>
			<tr><td class="num"><?php print $i; ?></td><td class="name"><?php print $data['name']; ?></td><td class="score"><?php print $data['score']; ?></td></tr>
			<?php
			$i++;
			$lisatud[] = strtolower($data['name']);
		}
		if($i > 25)
			break;
	}
	print "</table>";
}

if(isset($_GET['saveScore']) && isset($_GET['name']) && strlen($_GET['name']) > 0 && $id)
{
	$query = sprintf("select * from `%sgames` where id=%d",
				mysql_real_escape_string($mysql_prefix),
				(int)$_SESSION['game_id']);
	$result = mysql_query($query);
	if(mysql_num_rows($result) > 0)
	{
		$data = mysql_fetch_array($result);

		if (get_magic_quotes_gpc())
			$_GET['name'] = stripslashes($_GET['name']);
		$_GET['name'] = mysql_real_escape_string($_GET['name']); 
		
		$query = sprintf("insert into `%shighscores` set name='%s', score=%d",
					mysql_real_escape_string($mysql_prefix),
					mysql_real_escape_string($_GET['name']),
					(int)$data['score']);
		mysql_query($query);
		
		$query = sprintf("delete from `%sgames` where id=%d",
					mysql_real_escape_string($mysql_prefix),
					$id);
		mysql_query($query);
		
		$query = sprintf("delete from `%squestions` where game=%d",
					mysql_real_escape_string($mysql_prefix),
					$id);
		mysql_query($query);
	}
}
?>