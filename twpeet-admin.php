<?php

require_once('twpeet-post.php');
require_once('twpeet.inc.php');

function twpeet_admin() {
	$message = null;
	$message_updated = __("TwitterSifu Options Updated.", 'TwitterSifu');
	$response=null;
	$save=null;

	if(isset($_POST['twpeet_opt_twitter_password']) && isset($_POST['twpeet_opt_twitter_username']))
	{
		$response=verify_credentials($_POST['twpeet_opt_twitter_username'],$_POST['twpeet_opt_twitter_password']);
		if($response != 200 && $response == 401)
		{
			$message = __("Incorrect Twitter Username & Password. Please verify your credentials.", 'TwitterSifu');
			print('
			<div id="message" class="updated fade">
				<p>'.__('Incorrect Twitter Username & Password. Please verify your credentials.', 'TwitterSifu').'</p>
			</div>');
			$save=false;
		}
		else
		$save=true;
	}

	if(isset($_POST['twpeet_opt_url_shortener']))
	{
		if($_POST['twpeet_opt_url_shortener']=="bit.ly")
		{
			if($save)
			{
				if(!isset($_POST['twpeet_opt_bitly_user']))
				{
					print('
			<div id="message" class="updated fade">
				<p>'.__('Please enter bit.ly username.', 'TwitterSifu').'</p>
			</div>');
					$save=false;
				}
				elseif(!isset($_POST['twpeet_opt_bitly_key']))
				{
					print('
			<div id="message" class="updated fade">
				<p>'.__('Please enter bit.ly API Key.', 'TwitterSifu').'</p>
			</div>');
					$save=false;
				}
				else
				{
					$save=true;
				}
			}
		}
	}

	if (isset($_POST['submit']) && $save ) {
		$message = $message_updated;
		if (isset($_POST['twpeet_opt_twitter_username'])) {
			update_option('twpeet_opt_twitter_username',$_POST['twpeet_opt_twitter_username']);
		}
		if (isset($_POST['twpeet_opt_twitter_password'])) {
			update_option('twpeet_opt_twitter_password',$_POST['twpeet_opt_twitter_password']);
		}
		if (isset($_POST['twpeet_opt_interval'])) {
			update_option('twpeet_opt_interval',$_POST['twpeet_opt_interval']);
		}
		if (isset($_POST['twpeet_opt_interval_slop'])) {
			update_option('twpeet_opt_interval_slop',$_POST['twpeet_opt_interval_slop']);
		}
		if (isset($_POST['twpeet_opt_age_limit'])) {
			update_option('twpeet_opt_age_limit',$_POST['twpeet_opt_age_limit']);
		}
		if (isset($_POST['twpeet_opt_max_age_limit'])) {
			update_option('twpeet_opt_max_age_limit',$_POST['twpeet_opt_max_age_limit']);
		}
		if (isset($_POST['twpeet_opt_tweet_prefix'])) {
			update_option('twpeet_opt_tweet_prefix',$_POST['twpeet_opt_tweet_prefix']);
		}
		if (isset($_POST['twpeet_opt_add_data'])) {
			update_option('twpeet_opt_add_data',$_POST['twpeet_opt_add_data']);
		}
		if (isset($_POST['twpeet_opt_publishnew'])) {
			update_option('twpeet_opt_publishnew',$_POST['twpeet_opt_publishnew']);
		}		
		if (isset($_POST['post_category'])) {
			update_option('twpeet_opt_omit_cats',implode(',',$_POST['post_category']));
		}
		else {
			update_option('twpeet_opt_omit_cats','');
		}
		
        if (isset($_POST['twpeet_opt_hashtags'])) {
			update_option('twpeet_opt_hashtags',$_POST['twpeet_opt_hashtags']);
		}
		else {
			update_option('twpeet_opt_hashtags','');
		}
		
		if(isset($_POST['twpeet_opt_url_shortener']))
		{
			update_option('twpeet_opt_url_shortener',$_POST['twpeet_opt_url_shortener']);
			if($_POST['twpeet_opt_url_shortener']=="bit.ly")
			{
				if(isset($_POST['twpeet_opt_bitly_user']))
				{
					update_option('twpeet_opt_bitly_user',$_POST['twpeet_opt_bitly_user']);
				}
				if(isset($_POST['twpeet_opt_bitly_key']))
				{
					update_option('twpeet_opt_bitly_key',$_POST['twpeet_opt_bitly_key']);
				}
			}
		}
		print('
			<div id="message" class="updated fade">
				<p>'.__('TwitterSifu Options Updated.', 'TwitterSifu').'</p>
			</div>');
	}
	elseif (isset($_POST['tweet']))
	{
		twpeet_opt_tweet_old_post();
		print('
			<div id="message" class="updated fade">
				<p>'.__('Tweet posted successfully.', 'TwitterSifu').'</p>
			</div>');
	}
	$omitCats = get_option('twpeet_opt_omit_cats');
	if (!isset($omitCats)) {
		$omitCats = twpeet_opt_OMIT_CATS;
	}
	$publishnew = get_option('twpeet_opt_publishnew');
	if (!isset($publishnew)) {
		$publishnew = 'yes';
	}
	
	$ageLimit = get_option('twpeet_opt_age_limit');
	if (!isset($ageLimit)) {
		$ageLimit = twpeet_opt_AGE_LIMIT;
	}

	$maxAgeLimit = get_option('twpeet_opt_max_age_limit');
	if (!isset($maxAgeLimit)) {
		$maxAgeLimit = twpeet_opt_MAX_AGE_LIMIT;
	}
	
	$interval = get_option('twpeet_opt_interval');
	if (!(isset($interval) && is_numeric($interval))) {
		$interval = twpeet_opt_INTERVAL;
	}
	$slop = get_option('twpeet_opt_interval_slop');
	if (!(isset($slop) && is_numeric($slop))) {
		$slop = twpeet_opt_INTERVAL_SLOP;
	}
	$tweet_prefix = get_option('twpeet_opt_tweet_prefix');
	if(!isset($tweet_prefix)){
		$tweet_prefix = twpeet_opt_TWEET_PREFIX;
	}
	$url_shortener=get_option('twpeet_opt_url_shortener');
	if(!isset($url_shortener)){
		$url_shortener=twpeet_opt_URL_SHORTENER;
	}
	
	$twitter_hashtags=get_option('twpeet_opt_hashtags');
	if(!isset($twitter_hashtags)){
		$twitter_hashtags=twpeet_opt_HASHTAGS;
	}
	
	$bitly_api=get_option('twpeet_opt_bitly_key');
	if(!isset($bitly_api)){
		$bitly_api="";
	}
	$bitly_username=get_option('twpeet_opt_bitly_user');
	if(!isset($bitly_username)){
		$bitly_username="";
	}
	$add_data = get_option('twpeet_opt_add_data');
	$twitter_username = get_option('twpeet_opt_twitter_username');
	$twitter_password = get_option('twpeet_opt_twitter_password');

	print('
			<div class="wrap">
				<h2>'.__('TwitterSifu - Wordpress Premuim Twitter tool by WebSifu', 'TwitterSifu').'</h2>
				<div id="top-copyright">
					WebSifu is a Malaysia Website Design Company with a world class quality. We provide Superior Quality Website Design Services, SEO Optimization and Web Marketing Solutions for your business needs. Be it dazzling websites and portals or customized software solutions, our team is trained and ready to fulfill your every need. Contact us now for more info. <a href="http://www.websifu.biz">Visit our site now</a>
				</div>	
				<a target="_blank" href="http://twitter.com/i1websifu">
					<img src="'.get_bloginfo('wpurl').'/wp-content/plugins/twittersifu/css/twittersifu.png" border="0" />
				</a>				
				
				<br />
				<br />
				<form id="twpeet_opt" name="twpeet_TwitterSifu" action="'.get_bloginfo('wpurl').'/wp-admin/options-general.php?page=TwitterSifu" method="post">
					<input type="hidden" name="twpeet_opt_action" value="twpeet_opt_update_settings" />
					<fieldset class="options">
						<div class="option">
							<h3>General options</h3>								
						</div>
						
						<div class="option">
							<label for="twpeet_opt_twitter_username">'.__('Twitter Username', 'TwitterSifu').':</label>
							<input type="text" size="25" name="twpeet_opt_twitter_username" id="twpeet_opt_twitter_username" value="'.$twitter_username.'" autocomplete="off" />
						</div>
						<div class="option">
							<label for="twpeet_opt_twitter_password">'.__('Twitter Password', 'TwitterSifu').':</label>
							<input type="password" size="25" name="twpeet_opt_twitter_password" id="twpeet_opt_twitter_password" value="'.$twitter_password.'" autocomplete="off" />
						</div>
						<div class="option">
							<label for="twpeet_opt_tweet_prefix">'.__('Tweet Prefix', 'TwitterSifu').':</label>
							<input type="text" size="25" name="twpeet_opt_tweet_prefix" id="twpeet_opt_tweet_prefix" value="'.$tweet_prefix.'" autocomplete="off" />
							<b>If set, it will show as: "{tweet prefix}: {post title}... {url}</b>
						</div>
						<div class="option">
							<label for="twpeet_opt_add_data">'.__('Add post data to tweet', 'TwitterSifu').':</label>
							<select id="twpeet_opt_add_data" name="twpeet_opt_add_data" style="width:100px;">
								<option value="false" '.twpeet_opt_optionselected("false",$add_data).'>'.__(' No ', 'TwitterSifu').'</option>
								<option value="true" '.twpeet_opt_optionselected("true",$add_data).'>'.__(' Yes ', 'TwitterSifu').'</option>
							</select>
							<b>If set, it will show as: "{tweet prefix}: {post title}- {content}... {url}</b>
						</div>
						<div class="option">
							<label for="twpeet_opt_url_shortener">'.__('URL Shortener Service', 'TwitterSifu').':</label>
							<select name="twpeet_opt_url_shortener" id="twpeet_opt_url_shortener" onchange="javascript:showURLAPI()" style="width:100px;">
									<option value="tinyurl" '.twpeet_opt_optionselected('tinyurl',$url_shortener).'>'.__('tinyurl', 'TwitterSifu').'</option>
									<option value="is.gd" '.twpeet_opt_optionselected('is.gd',$url_shortener).'>'.__('is.gd', 'TwitterSifu').'</option>
									<option value="su.pr" '.twpeet_opt_optionselected('su.pr',$url_shortener).'>'.__('su.pr', 'TwitterSifu').'</option>
									<option value="bit.ly" '.twpeet_opt_optionselected('bit.ly',$url_shortener).'>'.__('bit.ly', 'TwitterSifu').'</option>
									<option value="tr.im" '.twpeet_opt_optionselected('tr.im',$url_shortener).'>'.__('tr.im', 'TwitterSifu').'</option>
									<option value="3.ly" '.twpeet_opt_optionselected('3.ly',$url_shortener).'>'.__('3.ly', 'TwitterSifu').'</option>
									<option value="u.nu" '.twpeet_opt_optionselected('u.nu',$url_shortener).'>'.__('u.nu', 'TwitterSifu').'</option>
									

							</select>
						</div>
						<div id="showDetail" style="display:none">
							<div class="option">
								<label for="twpeet_opt_bitly_user">'.__('bit.ly Username', 'TwitterSifu').':</label>
								<input type="text" size="25" name="twpeet_opt_bitly_user" id="twpeet_opt_bitly_user" value="'.$bitly_username.'" autocomplete="off" />
							</div>
							
							<div class="option">
								<label for="twpeet_opt_bitly_key">'.__('bit.ly API Key', 'TwitterSifu').':</label>
								<input type="text" size="25" name="twpeet_opt_bitly_key" id="twpeet_opt_bitly_key" value="'.$bitly_api.'" autocomplete="off" />
							</div>
						</div>
						
						<div class="option">
							<label for="twpeet_opt_hashtags">'.__('Default #hashtags for your tweets', 'TwitterSifu').':</label>
							<input type="text" size="25" name="twpeet_opt_hashtags" id="twpeet_opt_hashtags" value="'.$twitter_hashtags.'" autocomplete="off" />
							<b>Include #, like #thoughts</b>
						</div>
						
						
						<div class="option category">
				    	<div style="float:left">
						    	<label class="catlabel">'.__('Categories to Omit from tweets: ', 'TwitterSifu').'</label> </div>
						    	<div style="float:left">
						    		<ul id="categorychecklist" class="list:category categorychecklist form-no-clear">
								');
	wp_category_checklist(0, 0, explode(',',$omitCats));
	print('				    		</ul>
								</div>
								</div>
								
						<div class="option">
							<label for="twpeet_opt_interval">'.__('Tweet when publich new post: ', 'TwitterSifu').'</label>
							<select name="twpeet_opt_publishnew" id="twpeet_opt_publishnew" style="width:100px;">
							
									<option value="yes" '.twpeet_opt_optionselected('yes',$publishnew).'>'.__('Yes', 'TwitterSifu').'</option>
									<option value="no" '.twpeet_opt_optionselected('no',$publishnew).'>'.__('No', 'TwitterSifu').'</option>
							</select>
							<b>If it is enabled, new tweets will populate when you publish new blog post</b>
						</div>		
								
						<div class="option">
							<h3>Old content Options (Will effect only old post tweets)</h3>								
						</div>
						
						<div class="option">
							<label for="twpeet_opt_interval">'.__('Minimum interval between tweets: ', 'TwitterSifu').'</label>
							<select name="twpeet_opt_interval" id="twpeet_opt_interval">
									<option value="'.twpeet_opt_1_MIN.'" '.twpeet_opt_optionselected(twpeet_opt_1_MIN,$interval).'>'.__('1 Min', 'TwitterSifu').'</option>
									<option value="'.twpeet_opt_15_MIN.'" '.twpeet_opt_optionselected(twpeet_opt_15_MIN,$interval).'>'.__('15 Min', 'TwitterSifu').'</option>
									<option value="'.twpeet_opt_30_MIN.'" '.twpeet_opt_optionselected(twpeet_opt_30_MIN,$interval).'>'.__('30 Min', 'TwitterSifu').'</option>
									<option value="'.twpeet_opt_1_HOUR.'" '.twpeet_opt_optionselected(twpeet_opt_1_HOUR,$interval).'>'.__('1 Hour', 'TwitterSifu').'</option>
									<option value="'.twpeet_opt_4_HOURS.'" '.twpeet_opt_optionselected(twpeet_opt_4_HOURS,$interval).'>'.__('4 Hours', 'TwitterSifu').'</option>
									<option value="'.twpeet_opt_6_HOURS.'" '.twpeet_opt_optionselected(twpeet_opt_6_HOURS,$interval).'>'.__('6 Hours', 'TwitterSifu').'</option>
									<option value="'.twpeet_opt_12_HOURS.'" '.twpeet_opt_optionselected(twpeet_opt_12_HOURS,$interval).'>'.__('12 Hours', 'TwitterSifu').'</option>
									<option value="'.twpeet_opt_24_HOURS.'" '.twpeet_opt_optionselected(twpeet_opt_24_HOURS,$interval).'>'.__('24 Hours (1)', 'TwitterSifu').'</option>
									<option value="'.twpeet_opt_48_HOURS.'" '.twpeet_opt_optionselected(twpeet_opt_48_HOURS,$interval).'>'.__('48 Hours (2 days)', 'TwitterSifu').'</option>
									<option value="'.twpeet_opt_72_HOURS.'" '.twpeet_opt_optionselected(twpeet_opt_72_HOURS,$interval).'>'.__('72 Hours (3 days)', 'TwitterSifu').'</option>
									<option value="'.twpeet_opt_168_HOURS.'" '.twpeet_opt_optionselected(twpeet_opt_168_HOURS,$interval).'>'.__('168 Hours (7 days)', 'TwitterSifu').'</option>
							</select>
						</div>
						<div class="option">
							<label for="twpeet_opt_interval_slop">'.__('Random Interval (added to minimum interval): ', 'TwitterSifu').'</label>
							<select name="twpeet_opt_interval_slop" id="twpeet_opt_interval_slop">
									<option value="'.twpeet_opt_15_MIN.'" '.twpeet_opt_optionselected(twpeet_opt_15_MIN,$slop).'>'.__('Upto 15 Min', 'TwitterSifu').'</option>
									<option value="'.twpeet_opt_1_HOUR.'" '.twpeet_opt_optionselected(twpeet_opt_1_HOUR,$slop).'>'.__('Upto 1 Hour', 'TwitterSifu').'</option>
									<option value="'.twpeet_opt_4_HOURS.'" '.twpeet_opt_optionselected(twpeet_opt_4_HOURS,$slop).'>'.__('Upto 4 Hours', 'TwitterSifu').'</option>
									<option value="'.twpeet_opt_6_HOURS.'" '.twpeet_opt_optionselected(twpeet_opt_6_HOURS,$slop).'>'.__('Upto 6 Hours', 'TwitterSifu').'</option>
									<option value="'.twpeet_opt_12_HOURS.'" '.twpeet_opt_optionselected(twpeet_opt_12_HOURS,$slop).'>'.__('Upto 12 Hours', 'TwitterSifu').'</option>
									<option value="'.twpeet_opt_24_HOURS.'" '.twpeet_opt_optionselected(twpeet_opt_24_HOURS,$slop).'>'.__('Upto 24 Hours (1)', 'TwitterSifu').'</option>
							</select>
						</div>
						<div class="option">
							<label for="twpeet_opt_age_limit">'.__('Minimum age of post to be eligible for tweet: ', 'TwitterSifu').'</label>
							<select name="twpeet_opt_age_limit" id="twpeet_opt_age_limit">
									<option value="1" '.twpeet_opt_optionselected('1',$ageLimit).'>'.__('1 Day', 'TwitterSifu').'</option>
									<option value="3" '.twpeet_opt_optionselected('3',$ageLimit).'>'.__('3 Days', 'TwitterSifu').'</option>
									<option value="7" '.twpeet_opt_optionselected('7',$ageLimit).'>'.__('7 Days', 'TwitterSifu').'</option>
									<option value="15" '.twpeet_opt_optionselected('15',$ageLimit).'>'.__('15 Days', 'TwitterSifu').'</option>
									<option value="30" '.twpeet_opt_optionselected('30',$ageLimit).'>'.__('30 Days', 'TwitterSifu').'</option>
									<option value="60" '.twpeet_opt_optionselected('60',$ageLimit).'>'.__('60 Days', 'TwitterSifu').'</option>
									<option value="90" '.twpeet_opt_optionselected('90',$ageLimit).'>'.__('90 Days', 'TwitterSifu').'</option>
									<option value="120" '.twpeet_opt_optionselected('120',$ageLimit).'>'.__('120 Days', 'TwitterSifu').'</option>
									<option value="240" '.twpeet_opt_optionselected('240',$ageLimit).'>'.__('240 Days', 'TwitterSifu').'</option>
									<option value="365" '.twpeet_opt_optionselected('365',$ageLimit).'>'.__('365 Days', 'TwitterSifu').'</option>
							</select>
						</div>
						
						<div class="option">
							<label for="twpeet_opt_max_age_limit">'.__('Maximum age of post to be eligible for tweet: ', 'TwitterSifu').'</label>
							<select name="twpeet_opt_max_age_limit" id="twpeet_opt_max_age_limit">
									<option value="None" '.twpeet_opt_optionselected('None',$maxAgeLimit).'>'.__('None', 'TwitterSifu').'</option>
									<option value="15" '.twpeet_opt_optionselected('15',$maxAgeLimit).'>'.__('15 Days', 'TwitterSifu').'</option>
									<option value="30" '.twpeet_opt_optionselected('30',$maxAgeLimit).'>'.__('30 Days', 'TwitterSifu').'</option>
									<option value="60" '.twpeet_opt_optionselected('60',$maxAgeLimit).'>'.__('60 Days', 'TwitterSifu').'</option>
									<option value="90" '.twpeet_opt_optionselected('90',$maxAgeLimit).'>'.__('90 Days', 'TwitterSifu').'</option>
									<option value="120" '.twpeet_opt_optionselected('120',$maxAgeLimit).'>'.__('120 Days', 'TwitterSifu').'</option>
									<option value="240" '.twpeet_opt_optionselected('240',$maxAgeLimit).'>'.__('240 Days', 'TwitterSifu').'</option>
									<option value="365" '.twpeet_opt_optionselected('365',$maxAgeLimit).'>'.__('365 Days', 'TwitterSifu').'</option>
							</select>
							<b>If set, it will not fetch posts which are older than specified day.</b>
						</div>
						
				    	
					</fieldset>
					<p class="submit">
						<input type="submit" class="button-primary" name="submit" onclick="javascript:return validate()" value="'.__('Update TwitterSifu Options', 'TwitterSifu').'" />
						<input type="submit" class="button-primary" name="tweet" value="'.__('Tweet Now', 'TwitterSifu').'" />
					</p>
						
				</form>
				
				<div id="bottom-copyright">
					<a href="http://www.websifu.biz">Copyright &copy 2010 - Malaysia Website Design, SEO Optimization and Web Marketing Agency</a>
				</div>
				<br />
				<script language="javascript" type="text/javascript">

function showURLAPI()
{
	var urlShortener=document.getElementById("twpeet_opt_url_shortener").value;
	if(urlShortener=="bit.ly")
	{
		document.getElementById("showDetail").style.display="block";
	}
	else
	{
		document.getElementById("showDetail").style.display="none";
	}
}

function validate()
{

	if(document.getElementById("showDetail").style.display=="block" && document.getElementById("twpeet_opt_url_shortener").value=="bit.ly")
	{
		if(trim(document.getElementById("twpeet_opt_bitly_user").value)=="")
		{
			alert("Please enter bit.ly username.");
			document.getElementById("twpeet_opt_bitly_user").focus();
			return false;
		}

		if(trim(document.getElementById("twpeet_opt_bitly_key").value)=="")
		{
			alert("Please enter bit.ly API key.");
			document.getElementById("twpeet_opt_bitly_key").focus();
			return false;
		}
	}
	if(eval(document.getElementById("twpeet_opt_age_limit").value) > eval(document.getElementById("twpeet_opt_max_age_limit").value))
	{
		alert("Post max age limit cannot be less than Post min age iimit");
		document.getElementById("twpeet_opt_age_limit").focus();
		return false;
	}
}

function trim(stringToTrim) {
	return stringToTrim.replace(/^\s+|\s+$/g,"");
}

showURLAPI();
</script>' );

}

function twpeet_opt_optionselected($opValue, $value) {
	if($opValue==$value) {
		return 'selected="selected"';
	}
	return '';
}

function twpeet_opt_head_admin()
{
	$home = get_settings('siteurl');
	$base = '/'.end(explode('/', str_replace(array('\\','/twpeet-admin.php'),array('/',''),__FILE__)));
	$stylesheet = $home.'/wp-content/plugins' . $base . '/css/twpeet.css';
	echo('<link rel="stylesheet" href="' . $stylesheet . '" type="text/css" media="screen" />');
}


//Verify a user's credentials
function verify_credentials($auth_user, $auth_pass) {

	$url = "http://twitter.com/account/verify_credentials.xml";
	$response = send_request($url, 'GET', '', $auth_user, $auth_pass);
	if(is_numeric($response))
	{
		return $response;
	}
	else {
		$xml = new SimpleXmlElement($response);
		return $xml;}

}
?>