<?php
/*
Plugin Name: MemberFindMe
Plugin URI: http://memberfind.me
Description: MemberFindMe plugin
Version: 0.1
Author: SourceFound
Author URI: http://www.sourcefound.com
License: GPL2
*/

/*  Copyright 2012  SOURCEFOUND INC.  (email : info@sourcefound.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (is_admin()) {
  add_action('admin_menu', 'sf_set_mnu');
  add_action('admin_init', 'sf_set_ini');
}

add_action('wp_head', 'sf_hdr');
add_action('wp_enqueue_scripts', 'sf_ini');
add_shortcode('memberfindme', 'sf_put');
remove_action('wp_head', 'rel_canonical');
add_filter('wp_title', 'sf_ttl', 10, 3);

function sf_set_mnu() {
  add_options_page('MemberFindMe Settings', 'MemberFindMe', 'manage_options', 'sf_set_mnu', 'sf_set_out');
}

function sf_set_ini() {
  register_setting('sf_set_grp', 'sf_set', 'sf_set_val');
}

function sf_set_out() {
  if (!current_user_can('manage_options'))  {
    wp_die(__('You do not have sufficient permissions to access this page.'));
  }
  echo '<div class="wrap"><h2>MemberFindMe Settings</h2>'
    .'<form action="options.php" method="post">';
  settings_fields("sf_set_grp");
  $set=get_option('sf_set');
  echo '<table class="form-table">'
    .'<tr valign="top"><th scope="row">MemberFindMe Organization Key</th><td><input type="text" name="sf_set[org]" value="'.(isset($set['org'])&&$set['org']?$set['org']:'').'" /></td></tr>'
    .'<tr valign="top"><th scope="row">Stripe Public Key</th><td><input type="text" name="sf_set[pay]" value="'.(isset($set['pay'])&&$set['pay']?$set['pay']:'').'" style="width:300px;" /></td></tr>'
    .'<tr valign="top"><th scope="row">Facebook API Key</th><td><input type="text" name="sf_set[fbk]" value="'.(isset($set['fbk'])&&$set['fbk']?$set['fbk']:'').'" /></td></tr>'
	.'<tr valign="top"><th scope="row">Customize Search Button Text</th><td><input type="text" name="sf_set[fnd]" value="'.(isset($set['fnd'])&&$set['fnd']?$set['fnd']:'Search').'" /></td></tr>'
	.'<tr valign="top"><th scope="row">Customize Group Email Button Text</th><td><input type="text" name="sf_set[rsp]" value="'.(isset($set['rsp'])&&$set['rsp']?$set['rsp']:'Request Quotes').'" /></td></tr>'
    .'</table>'
	.'<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes"></p>'
    .'</form></div>';
}

function sf_set_val($in) {
  $in['org']=intval($in['org']);
  $in['org']=($in['org']?strval($in['org']):'');
  $in['pay']=trim($in['pay']);
  $in['fbk']=trim($in['fbk']);
  $in['fnd']=trim($in['fnd']);
  $in['rsp']=trim($in['rsp']);
  return $in;
}

function sf_ini() {
  if (isset($_GET['_escaped_fragment_'])) {
    wp_register_style('sf-css', 'http://cdn.sourcefound.com/wl/SF.css');
    wp_enqueue_style('sf-css');
  }
}

function sf_ttl($ttl,$sep,$loc) {
  global $post;
  if (isset($_GET['_escaped_fragment_'])&&strpos($post->post_content,'[memberfindme')!==false&&preg_match('/^(biz|event)\//',$_GET['_escaped_fragment_'])==1) {
    $set=get_option('sf_set');
	$cto=array('http'=>array('method'=>"GET"));
	$ctx=stream_context_create($cto); 
    $out=@file_get_contents("http://www.sourcefound.com/api?hdr=1&org=".$set['org']."&url=".urlencode(get_permalink())."&pne=".urlencode($_GET['_escaped_fragment_']),false,$context); 
	$out=json_decode($out,true);
	return ($loc=='left'?" $sep ":' ').$out['ttl'].($loc=='right'?" $sep ":' ');
  }
}

function sf_hdr() {
  global $post;
  if (isset($_GET['_escaped_fragment_'])&&strpos($post->post_content,'[memberfindme')!==false&&preg_match('/^(biz|event)\//',$_GET['_escaped_fragment_'])==1) {
    $set=get_option('sf_set');
    $cto=array('http'=>array('method'=>"GET"));
	$ctx=stream_context_create($cto); 
    $rsp=@file_get_contents("http://www.sourcefound.com/api?hdr=1&org=".$set['org']."&url=".urlencode(get_permalink())."&pne=".urlencode($_GET['_escaped_fragment_']),false,$context); 
	$dat=json_decode($rsp,true);
	$out='';
	if (isset($dat['sum'])) $out.="<meta name='description' content='".$dat['sum']."'>\r\n";
	if (isset($dat['ttl'])) $out.="<meta property='og:title' content='".$dat['ttl']."' />\r\n";
    if (isset($dat['sum'])) $out.="<meta property='og:description' content='".$dat['sum']."' />\r\n";
    if (isset($dat['rel'])) $out.="<meta property='og:url' content='".$dat['rel']."' />\r\n";
    if (isset($dat['img'])) $out.="<meta property='og:image' content='".$dat['img']."' />\r\n";
    $out.="<meta property='og:site_name' content='".get_bloginfo('name')."' />\r\n";
    if (isset($dat['rel'])) $out.="<link rel='canonical' href='".$dat['rel']."'/>\r\n";
	echo $out;
  }
}

function sf_put($opt) {
  $set=get_option('sf_set');
  if (!isset($set['org'])||!$set['org']) {
    $out='<div>MemberFindMe organization key not setup. Please update settings.</div>';
  } else if (isset($opt['open'])) {
    if (isset($_GET['_escaped_fragment_'])||(preg_match("/googlebot|slurp|msnbot|facebook/i",$_SERVER['HTTP_USER_AGENT'])>0)) {
	  $cto=array('http'=>array('method'=>"GET"));
	  $ctx=stream_context_create($cto); 
      $rsp=@file_get_contents("http://www.sourcefound.com/api?org=".$set['org']."&url=".urlencode(get_permalink())."&pne=".urlencode($_GET['_escaped_fragment_']?$_GET['_escaped_fragment_']:substr($opt['open'],1)),false,$context); 
	  $out='<div id="SFctr" class="SF" style="'.(isset($opt['style'])?$opt['style']:'position:relative;height:auto;').'">'
	    .$rsp.'</div>';
	} else {
      $out='<div id="SFctr" class="SF" data-ini="'.$opt['open'].'"'
        .(isset($set['org'])&&$set['org']?(' data-org="'.$set['org'].'"'):'')
	    .(isset($set['pay'])&&$set['pay']?(' data-pay="'.$set['pay'].'"'):'')
	    .(isset($set['fbk'])&&$set['fbk']?(' data-fbk="'.$set['fbk'].'"'):'')
        .(isset($set['fnd'])&&$set['fnd']?(' data-fnd="'.$set['fnd'].'"'):'')
		.(isset($set['rsp'])&&$set['rsp']?(' data-rsp="'.$set['rsp'].'"'):'')
		.(isset($opt['viewport'])&&$opt['viewport']=='fixed'?(' data-ofy="1"'):'')
	    .' style="'.(isset($opt['style'])?$opt['style']:'position:relative;height:auto;').'">'
	    .'<script src="http://www.sourcefound.com/wl/SF.js"></script>'
	    .'<script>SF.init();</script>'
      .'</div>';
	}
  } else if (isset($opt['button'])) { 
    $out=(isset($opt['type'])?('<'.$opt['type']):'<button')
	  .(isset($opt['type'])&&$opt['type']=='img'&&isset($opt['src'])?(' src="'.$opt['src'].'"'):'')
	  .(isset($opt['class'])?(' class="'.$opt['class'].'"'):'')
	  .(isset($opt['style'])?(' style="'.$opt['style'].'"'):' style="cursor:pointer;"');
	if ($opt['button']=='account') 
	  $out.=' onmouseout="SF.usr.account(event,this);" onmouseover="SF.usr.account(event,this);" onclick="SF.usr.account(event,this);">'
	    .(isset($opt['text'])?$opt['text']:'My Account');
	else if ($opt['button']=='join')
	  $out.=' onclick="SF.open(\'account/join\');">'
	    .(isset($opt['text'])?$opt['text']:'Join');
	$out=(isset($opt['type'])?($opt['type']=='img'?'':('</'.$opt['type'].'>')):'</button>');
  }
  return isset($out)?$out:'';
}
?>