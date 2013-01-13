<?php
/*
Plugin Name: MemberFindMe
Plugin URI: http://memberfind.me
Description: MemberFindMe plugin
Version: 0.2
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
  add_action('admin_menu','sf_admin_menu');
  add_action('admin_init','sf_admin_init');
}

add_action('wp_head','sf_head');
add_action('wp_enqueue_scripts','sf_scripts');
add_shortcode('memberfindme','sf_shortcode');
remove_action('wp_head','rel_canonical');
add_filter('wp_title','sf_title',10,3);
add_action('widgets_init','sf_widgets_init');

function sf_admin_menu() {
  add_options_page('MemberFindMe Settings','MemberFindMe','manage_options','sf_admin_menu','sf_admin_form');
}

function sf_admin_init() {
  register_setting('sf_admin_group','sf_set','sf_admin_validate');
}

function sf_admin_form() {
  if (!current_user_can('manage_options'))  {
    wp_die(__('You do not have sufficient permissions to access this page.'));
  }
  echo '<div class="wrap"><h2>MemberFindMe Settings</h2>'
    .'<form action="options.php" method="post">';
  settings_fields("sf_admin_group");
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

function sf_admin_validate($in) {
  $in['org']=intval($in['org']);
  $in['org']=($in['org']?strval($in['org']):'');
  $in['pay']=trim($in['pay']);
  $in['fbk']=trim($in['fbk']);
  $in['fnd']=trim($in['fnd']);
  $in['rsp']=trim($in['rsp']);
  return $in;
}

function sf_scripts() {
  if (isset($_GET['_escaped_fragment_'])) {
    wp_register_style('sf-css','http://cdn.sourcefound.com/wl/SF.css');
    wp_enqueue_style('sf-css');
  }
}

function sf_title($ttl,$sep,$loc) {
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

function sf_head() {
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

function sf_shortcode($opt) {
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
	    .'<script src="//www.sourcefound.com/js/?mfm&ses"></script>'
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

class sf_widget_event extends WP_Widget {
	public function __construct() {
		parent::__construct('sf_widget_event','MemberFindMe Events',array('description'=>'Upcoming events from your MemberFindMe calendar'));
	}
	public function widget($args,$instance ) {
		extract($args);
		$title=apply_filters('widget_title',$instance['title']);
		echo $before_widget;
		if (!empty($title))
			echo $before_title.$title.$after_title;
		$set=get_option('sf_set');
		$cto=array('http'=>array('method'=>"GET"));
		$ctx=stream_context_create($cto); 
		$rsp=@file_get_contents("http://www.sourcefound.com/api?fi=evt&org=".$set['org']."&wee=1&grp=".$instance['grp']."&cnt=".$instance['cnt']."&sdp=".time(),false,$context); 
		$dat=json_decode($rsp,true);
		echo '<ul>';
		foreach ($dat as $x) {
			if (isset($x['ezp'])&&$x['ezp']&&explode(',',$x['ezp'])[0]==explode(',',$x['szp'])[0]) $x['ezp']='- '.trim(explode(',',$x['ezp'])[1]);
			echo '<li><a href="https://'.$x['url'].'">'.esc_html($x['ttl']).'</a><div class="event-start">'.$x['szp'].(isset($x['ezp'])&&$x['ezp']?('</div><div class="event-end">'.$x['ezp'].'</div>'):'</div>').'</small></li>';
		}
		echo '</ul>';
		echo $after_widget;
	}
	public function update($new_instance,$old_instance ) {
		$instance=$old_instance;
		$instance['title']=strip_tags($new_instance['title']);
		$instance['grp']=$new_instance['grp']?strval(intval($new_instance['grp'])):'0';
		$instance['cnt']=$new_instance['cnt']?strval(intval($new_instance['cnt'])):'0';
		return $instance;
	}
	public function form($instance) {
		$instance=wp_parse_args($instance,array('title'=>'','grp'=>'0','cnt'=>'3'));
		$title=strip_tags($instance['title']);
		$grp=intval($instance['grp']);
		$cnt=intval($instance['cnt']);
		echo '<p><label for="'.$this->get_field_id('title').'">Title:</label> <input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.esc_attr($title).'" /></p>';
		echo '<p><label for="'.$this->get_field_id('grp').'">Calendar group:</label> <input id="'.$this->get_field_id('grp').'" name="'.$this->get_field_name('grp').'" type="text" value="'.$grp.'" size="3"/></p>';
		echo '<p><label for="'.$this->get_field_id('cnt').'">Number of events to show:</label> <input id="'.$this->get_field_id('cnt').'" name="'.$this->get_field_name('cnt').'" type="text" value="'.$cnt.'" size="3"/></p>';
	}
}

class sf_widget_folder extends WP_Widget {
	public function __construct() {
		parent::__construct('sf_widget_folder','MemberFindMe Folder',array('description'=>'Display contacts from your public MemberFindMe folder'));
	}
	public function widget($args,$instance ) {
		extract($args);
		$title=apply_filters('widget_title',$instance['title']);
		if (empty($title))
			echo str_replace('widget_sf_widget_folder','widget_sf_widget_folder widget_no_title',$before_widget);
		else
			echo $before_widget;
		if (!empty($title))
			echo $before_title.$title.$after_title;
		$set=get_option('sf_set');
		$cto=array('http'=>array('method'=>"GET"));
		$ctx=stream_context_create($cto); 
		$rsp=@file_get_contents("http://www.sourcefound.com/api?fi=dek&org=".$set['org']."&wem=1&lbl=".urlencode($instance['lbl']),false,$context); 
		$dat=json_decode($rsp,true);
		if ($instance['act']=='1') {
			$fn=str_replace('-','_',$this->id);
			echo '<ul class="sf_widget_folder_logos" style="list-style:none;margin:0;padding:10px;">';
		} else
			echo '<ul class="sf_widget_folder_list">';
		foreach ($dat as $x) {
			if ($instance['act']=='1')
				echo '<li style="display:none;background-color:white;text-align:center;height:130px;padding:0;margin:0;table-layout:fixed;width:100%;"><div style="display:table-cell;vertical-align:middle;width:100%;"><a href="//'.esc_attr($x['url']).'" style="display:block;width:100%;font-size:1.5em;">'
					.($x['lgo']?('<img src="//d7efyznwb7ft3.cloudfront.net/'.$x['_id'].'_lgl.jpg" alt="'.esc_attr($x['nam']).'" onerror="this.parentNode.innerHTML=this.alt;" style="display:block;margin:0 auto;max-width:100%;max-height:80px;">'):esc_html($x['nam']))
					.'</a><small class="cnm" style="display:block;padding:10px;">'.esc_html($x['cnm']).'</small></div></li>';
			else
				echo '<li><a href="//'.esc_attr($x['url']).'">'.esc_html($x['nam']).'</a><small class="cnm" style="display:block;">'.esc_html($x['cnm']).'</small></li>';
		}
		echo '</ul>';
		if ($instance['act']=='1') {
			$delay=intval($instance['delay'])*1000;
			echo '<script>'
				.$fn.'_animate=function(){var r=document.getElementById("'.$this->id.'").lastChild.previousSibling,x=r.querySelector(\'li[style*="table;"]\');if (x) {x.style.display="none";x=(x.nextSibling?x.nextSibling:r.firstChild);} else x=r.childNodes[Math.round(Math.random()*r.childNodes.length)];x.style.display="table";setTimeout('.$fn.'_animate,'.($delay?$delay:10000).');};'
				.$fn.'_animate();'
				.'</script>';
		}
		echo $after_widget;
	}
	public function update($new_instance,$old_instance ) {
		$instance=$old_instance;
		$instance['title']=strip_tags($new_instance['title']);
		$instance['lbl']=trim($new_instance['lbl']);
		$instance['act']=strval(intval($new_instance['act']));
		$instance['delay']=strval(intval($new_instance['delay']));
		return $instance;
	}
	public function form($instance) {
		$instance=wp_parse_args($instance,array('title'=>'','lbl'=>'','act'=>'0','delay'=>'10'));
		$title=strip_tags($instance['title']);
		$lbl=$instance['lbl'];
		$act=$instance['act'];
		$delay=$instance['delay'];
		echo '<p><label for="'.$this->get_field_id('title').'">Title:</label> <input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.attribute_escape($title).'" /></p>';
		echo '<p><label for="'.$this->get_field_id('lbl').'">Folder name:</label> <input class="widefat" id="'.$this->get_field_id('lbl').'" name="'.$this->get_field_name('lbl').'" type="text" value="'.attribute_escape($lbl).'" /></p>';
		echo '<p><label for="'.$this->get_field_id('act').'">Display:</label> <select id="'.$this->get_field_id('act').'" name="'.$this->get_field_name('act').'" onchange="this.parentNode.nextSibling.style.display=(this.value==\'1\'?\'\':\'none\');">'
				.'<option value="0"'.($act=='0'?' selected="selected"':'').'>List</option>'
				.'<option value="1"'.($act=='1'?' selected="selected"':'').'>Slideshow</option>'
			.'</select></p>';
		echo '<p'.($act=='1'?'':' style="display:none;"').'><label for="'.$this->get_field_id('delay').'">Seconds between slides:</label> <input id="'.$this->get_field_id('delay').'" name="'.$this->get_field_name('delay').'" type="text" value="'.$delay.'" size="3"/></p>';
	}
}

function sf_widgets_init() {
	register_widget('sf_widget_event');
	register_widget('sf_widget_folder');
}

?>