<?php

function twpeet_tweet_old_post()
{
	//check last tweet time against set interval and span
	if (twpeet_opt_update_time()) {
		update_option('twpeet_opt_last_update', time());
		twpeet_opt_tweet_old_post();
	}
}

//get random post and tweet
function twpeet_opt_tweet_old_post()
{
	global $wpdb;
	$omitCats = get_option('twpeet_opt_omit_cats');
	$ageLimit = get_option('twpeet_opt_age_limit');
	$maxAgeLimit = get_option('twpeet_opt_max_age_limit');
	if (!isset($omitCats)) {
		$omitCats = twpeet_opt_OMIT_CATS;
	}
	if (!isset($ageLimit)) {
		$ageLimit = twpeet_opt_AGE_LIMIT;
	}
	if (!isset($maxAgeLimit)) {
		$maxAgeLimit = twpeet_opt_MAX_AGE_LIMIT;
	}
	$sql = "SELECT ID
            FROM $wpdb->posts
            WHERE post_type = 'post'
                  AND post_status = 'publish'
                  AND post_date < curdate( ) - INTERVAL ".$ageLimit. " day";

	if($maxAgeLimit != "None")
	{
		$sql = $sql." AND post_date > curdate( ) - INTERVAL ".$maxAgeLimit." day";
	}

	if ($omitCats!='') {
		$sql = $sql." AND NOT(ID IN (SELECT tr.object_id
                                    FROM $wpdb->terms  t 
                                          inner join $wpdb->term_taxonomy tax on t.term_id=tax.term_id and tax.taxonomy='category' 
                                          inner join $wpdb->term_relationships tr on tr.term_taxonomy_id=tax.term_taxonomy_id 
                                    WHERE t.term_id IN (".$omitCats.")))";
	}
	$sql = $sql."
            ORDER BY RAND() 
            LIMIT 1 ";
	$oldest_post = $wpdb->get_var($sql);

	if (isset($oldest_post)) {
		twpeet_opt_tweet_post($oldest_post);
	}
}

function twitterpost_tweet($un, $pw, $tweet) { 
		$api_url = 'http://twitter.com/statuses/update.xml';
		$body = array( 'status' => $tweet );
		$headers = array( 'Authorization' => 'Basic '.base64_encode("$un:$pw") );
		$request = new WP_Http;
		$result = $request->request( $api_url , array( 'method' => 'POST', 'body' => $body, 'headers' => $headers ) );
	}

function do_twpeet_opt_tweet_post($post_ID){
	if (get_option("twpeet_opt_publishnew")=='yes')	
		twpeet_opt_tweet_post($post_ID);	
	return $post_ID;	
}

//tweet for the passed random post
function twpeet_opt_tweet_post($oldest_post)
{
	$post = get_post($oldest_post);
	$content=null;
	$permalink = get_permalink($oldest_post);
	$add_data = get_option("twpeet_opt_add_data");
	$twitter_hashtags = get_option('twpeet_opt_hashtags');
	$url_shortener=get_option('twpeet_opt_url_shortener');
	if($url_shortener=="bit.ly")
	{
		$bitly_key=get_option('twpeet_opt_bitly_key');
		$bitly_user=get_option('twpeet_opt_bitly_user');
		$shorturl=shorten_url($permalink,$url_shortener,$bitly_key,$bitly_user);
	}
	else
	{
		$shorturl = shorten_url($permalink,$url_shortener);
	}
	
	$prefix=get_option('twpeet_opt_tweet_prefix');

	if($add_data == "true")
	{
		$content = stripslashes($post->post_content);
		$content = strip_tags($content);
		$content = preg_replace('/\s\s+/', ' ', $content);
		$content = " - ".$content;
	}
	else {
		$content="";
	}

	if(!is_numeric($shorturl))
	{
		if($prefix)
		{
			$message = $prefix.": ".$post->post_title;
		}
		else {
			$message = $post->post_title;
		}

		$message = set_tweet_length($message.$content,$shorturl,$twitter_hashtags);
		$username = get_option('twpeet_opt_twitter_username');
		$password = get_option('twpeet_opt_twitter_password');	
		$status = urlencode(stripslashes(urldecode($message)));		
		if ($message) {			
			twitterpost_tweet($username, $password, $message);			
		}
	}
}

//send request to passed url and return the response
function send_request($url, $method='GET', $data='', $auth_user='', $auth_pass='') {
	$ch = curl_init($url);
	if (strtoupper($method)=="POST") {
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	}
	if (ini_get('open_basedir') == '' && ini_get('safe_mode') == 'Off'){
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	}
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	if ($auth_user != '' && $auth_pass != '') {
		curl_setopt($ch, CURLOPT_USERPWD, "{$auth_user}:{$auth_pass}");
	}
	$response = curl_exec($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if ($httpcode != 200) {
		return $httpcode;
	}

	return $response;

}


//Shorten long URLs with is.gd or bit.ly.
function shorten_url($the_url, $shortener='is.gd', $api_key='', $user='') {

	if (($shortener=="bit.ly") && isset($api_key) && isset($user)) {
		$url = "http://api.bit.ly/shorten?version=2.0.1&longUrl={$the_url}&login={$user}&apiKey={$api_key}&format=xml";
		$response = send_request($url, 'GET');
		$the_results = new SimpleXmlElement($response);
		if ($the_results->errorCode == '0') {
			$response = $the_results->results->nodeKeyVal->shortUrl;
		} else {
			$response = "";
		}
	}elseif ($shortener=="su.pr") {
		$url = "http://su.pr/api/simpleshorten?url={$the_url}";
		$response = send_request($url, 'GET');
	} elseif ($shortener=="tr.im") {
		$url = "http://api.tr.im/api/trim_simple?url={$the_url}";
		$response = send_request($url, 'GET');
	} elseif ($shortener=="3.ly") {
		$url = "http://3.ly/?api=em5893833&u={$the_url}";
		$response = send_request($url, 'GET');
	} elseif ($shortener=="tinyurl") {
		$url = "http://tinyurl.com/api-create.php?url={$the_url}";
		$response = send_request($url, 'GET');
	}elseif ($shortener=="u.nu") {
		$url = "http://u.nu/unu-api-simple?url={$the_url}";
		$response = send_request($url, 'GET');
	} else {
		$url = "http://is.gd/api.php?longurl={$the_url}";
		$response = send_request($url, 'GET');
	}
	return $response;

}



//Shrink a tweet and accompanying URL down to 140 chars.
function set_tweet_length($message, $url, $twitter_hashtags="") {

	$message_length = strlen($message);
	$url_length = strlen($url);
	$hashtags_length = strlen($twitter_hashtags);
	if ($message_length + $url_length + $hashtags_length > 140) {
		$shorten_message_to = 140 - $url_length - $hashtags_length;
		$shorten_message_to = $shorten_message_to - 4;
		//$message = $message." ";
		$message = substr($message, 0, $shorten_message_to);
		$message = substr($message, 0, strrpos($message,' '));
		$message = $message."...";
	}
	return $message." ".$url." ".$twitter_hashtags;

}

//check time and update the last tweet time
function twpeet_opt_update_time () {
	$last = get_option('twpeet_opt_last_update');
	$interval = get_option('twpeet_opt_interval');
	if (!(isset($interval) && is_numeric($interval))) {
		$interval = twpeet_opt_INTERVAL;
	}
	$slop = get_option('twpeet_opt_interval_slop');
	if (!(isset($slop) && is_numeric($slop))) {
		$slop = twpeet_opt_INTERVAL_SLOP;
	}
	if (false === $last) {
		$ret = 1;
	} else if (is_numeric($last)) {
		$ret = ( (time() - $last) > ($interval+rand(0,$slop)));
	}
	return $ret;
}

?>