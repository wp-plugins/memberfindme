<?php
/*
Plugin Name: MemberFindMe Membership, Event & Directory System
Plugin URI: http://memberfind.me
Description: MemberFindMe plugin
Version: 3.2
Author: SourceFound
Author URI: http://memberfind.me
License: GPL2
*/

/*  Copyright 2013  SOURCEFOUND INC.  (email : info@sourcefound.com)

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

global $sf_dat;
$sf_dat=false;

function sf_admin_menu() {
	add_menu_page('MemberFindMe Admin','MemberFindMe','add_users','sf_admin_page','sf_admin_page','','2.1');
	add_submenu_page('sf_admin_page','Members','Members','add_users','sf_admin_members','sf_admin_page');
	add_submenu_page('sf_admin_page','Labels','Labels &amp; Membership','add_users','sf_admin_labels','sf_admin_page');
	add_submenu_page('sf_admin_page','Folders','Folders','add_users','sf_admin_folders','sf_admin_page');
	add_submenu_page('sf_admin_page','Event List','Event List','add_users','sf_admin_event-list','sf_admin_page');
	add_submenu_page('sf_admin_page','Event Calendar','Event Calendar','add_users','sf_admin_calendar','sf_admin_page');
	add_submenu_page('sf_admin_page','Forms Carts Donations','Forms Carts Donations','add_users','sf_admin_forms','sf_admin_page');
	add_submenu_page('sf_admin_page','Customization','Customization','add_users','sf_admin_custom','sf_admin_page');
	add_submenu_page('sf_admin_page','Help','Help','add_users','sf_admin_help','sf_admin_page');
	add_submenu_page('sf_admin_page','Organization Settings','Organization Settings','add_users','sf_admin_account','sf_admin_page');
	add_submenu_page('sf_admin_page','Plugin Settings','Plugin Settings','add_users','sf_admin_options','sf_admin_options');
}

function sf_admin_init() {
	register_setting('sf_admin_group','sf_set','sf_admin_validate');
}

if (is_admin()) {
	add_action('admin_menu','sf_admin_menu');
	add_action('admin_init','sf_admin_init');
}

function sf_admin_options() {
	if (!current_user_can('manage_options'))  {
		wp_die(__('You do not have sufficient permissions to access this page.'));
	}
	echo '<div class="wrap"><h2>MemberFindMe Plugin Settings</h2>'
		.'<form action="options.php" method="post">';
	settings_fields("sf_admin_group");
	$set=get_option('sf_set');
	echo '<table class="form-table">'
		.'<tr valign="top"><th scope="row">MemberFindMe Organization ID</th><td><input type="text" name="sf_set[org]" value="'.(isset($set['org'])?$set['org']:'').'" /></td></tr>'
		.'<tr valign="top"><th scope="row">Facebook API key (optional)</th><td><input type="text" name="sf_set[fbk]" value="'.(isset($set['fbk'])?$set['fbk']:'').'" /></td></tr>'
		.'<tr valign="top"><th scope="row">Google Maps API key (optional)</th><td><input type="text" name="sf_set[map]" value="'.(isset($set['map'])?$set['map']:'').'" /></td></tr>'
		.'<tr valign="top"><th scope="row">Display contact name on cards in directory</th><td><input type="checkbox" name="sf_set[ctc]"'.(empty($set['ctc'])?'':' checked="1"').' /></td></tr>'
		.'<tr valign="top"><th scope="row">Customize text for directory search button</th><td><input type="text" name="sf_set[fnd]" value="'.(empty($set['fnd'])?'Search':$set['fnd']).'" /></td></tr>'
		.'<tr valign="top"><th scope="row">Customize text for directory search options button</th><td><input type="text" name="sf_set[adv]" placeholder="disabled" value="'.(isset($set['adv'])?$set['adv']:'Options').'" /></td></tr>'
		.'<tr valign="top"><th scope="row">Customize text for directory group email button</th><td><input type="text" name="sf_set[rsp]" placeholder="disabled" value="'.(isset($set['rsp'])?$set['rsp']:'').'" /></td></tr>'
		.'<tr valign="top"><th scope="row">Disable social share buttons</th><td><input type="checkbox" name="sf_set[scl]"'.(empty($set['scl'])?'':' checked="1"').' /></td></tr>'
		.'<tr valign="top"><th scope="row">Load js/css inline</th><td><input type="checkbox" name="sf_set[htm]"'.(empty($set['htm'])?'':' checked="1"').' /></td></tr>'
		.'<tr valign="top"><th scope="row">URL redirect upon signing out</th><td><input type="text" name="sf_set[out]" value="'.(empty($set['out'])?'':$set['out']).'" /></td></tr>'
		.'<tr valign="top"><th scope="row">Page top offset (pixels)</th><td><input type="text" name="sf_set[top]" value="'.(empty($set['top'])?'':$set['top']).'" /></td></tr>'
		.'</table>'
		//.(empty($set['wpl'])?'':('<input type="hidden" name="sf_set[wpl]" value="'.$set['wpl'].'" />'))
		.'<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes"></p>'
		.'</form></div>';
}

function sf_admin_validate($in) {
	$in['org']=intval($in['org']);
	$in['org']=($in['org']?strval($in['org']):'');
	if (!empty($in['fbk'])) $in['fbk']=trim($in['fbk']);
	if (!empty($in['map'])) $in['map']=empty($in['map'])?'':trim($in['map']);
	if (!empty($in['fnd'])) $in['fnd']=trim($in['fnd']);
	if (isset($in['adv'])) $in['adv']=trim($in['adv']);
	if (!empty($in['rsp'])) $in['rsp']=trim($in['rsp']);
	if (!empty($in['scl'])) $in['scl']='1'; else unset($in['scl']);
	if (!empty($in['htm'])) $in['htm']='1'; else unset($in['htm']);
	if (!empty($in['ctc'])) $in['ctc']='1'; else unset($in['ctc']);
	return $in; // preserve other fields for $in including wpl
}

function sf_admin_page() {
	global $plugin_page;
	$set=get_option('sf_set');
	switch (substr($plugin_page,9)) {
		case 'members':		$ini='folder/Members'; $hme='folder/Members'; break;
		case 'labels':		$ini='labels'; $hme='labels'; break;
		case 'folders':		$ini='folders'; $hme='folders'; break;
		case 'event-list':	$ini='!event-list'; $hme='!event-list'; break;
		case 'calendar':	$ini='!calendar'; $hme='!calendar'; break;
		case 'forms':		$ini='forms'; $hme='forms'; break;
		case 'custom':		$ini='custom'; $hme='custom'; break;
		case 'help':		$ini='!help'; $hme='!help'; break;
		case 'account': 	$ini='account/manage'; $hme='account'; break;
		default:			$ini='dashboard'; $hme='dashboard'; break;
	}
	echo '<div id="SFctr" class="SF" data-org="10000" data-hme="'.$hme.'" data-ini="'.$ini.'"'.(empty($set)||empty($set['map'])?'':(' data-map="'.$set['map'].'"')).' data-typ="org" data-wpo="options.php" style="position:relative;padding:30px 20px 20px;"></div>'
		.'<script>function sf_admin(){'
			.'var t=document.getElementById("toplevel_page_sf_admin_page");'
			.'if (!t) return;'
			.'var a=t.querySelectorAll(".wp-submenu a"),i,x,n;'
			.'for(i=0;n=a[i];i++){'
				.'x=n.href.split("sf_admin_")[1];'
				.'n.parentNode.className="";'
				.'if (x=="options") continue;'
				.'else if (x=="page"){n.innerHTML="Dashboard";n.parentNode.id="SFhdrdbd";n.href="#dashboard";}'
				.'else if (x=="members"){n.parentNode.id="SFhdrdem";n.href="#folder/Members";}'
				.'else if (x=="labels"){n.parentNode.id="SFhdrlbl";n.href="#labels";}'
				.'else if (x=="folders"){n.parentNode.id="SFhdrdek";n.href="#folders";}'
				.'else if (x=="event-list"){n.parentNode.id="SFhdrevl";n.href="#!event-list";}'
				.'else if (x=="calendar"){n.parentNode.id="SFhdrevc";n.href="#!calendar";}'
				.'else if (x=="forms"){n.parentNode.id="SFhdrfrm";n.href="#forms";}'
				.'else if (x=="help"){n.parentNode.id="SFhdrhlp";n.href="#help";}'
				.'else if (x=="custom"){n.parentNode.id="SFhdrtpl";n.href="#custom";}'
				.'else if (x=="account"){n.parentNode.id="SFhdracc";n.href="#account";}'
			.'}'
		.'}sf_admin();</script>'
		.'<script type="text/javascript" src="//mfm-sourcefoundinc.netdna-ssl.com/all.js"></script>'
		.'<script>SF.init();</script>';
	if ($set===false||empty($set['org'])) {
		echo '<form id="SFwpo" style="display:none" action="options.php" method="post">';
		settings_fields("sf_admin_group");
		echo '</form>';
	}
}

function sf_scripts() {
	wp_register_script('sf-mfm','//mfm-sourcefoundinc.netdna-ssl.com/mfm.js',array(),null);
}
add_action('wp_enqueue_scripts','sf_scripts');

function sf_false() {
	return false;
}

function sf_title() {
	global $post,$sf_dat;
	$arg=func_get_args();
	$set=get_option('sf_set');
	for ($i=0,$mat=array();($x=strpos($post->post_content,'[memberfindme open=',$i))!==false;$i=$x+1) {
		$y=strpos($post->post_content,']',$x);
		if ((!$x||substr($post->post_content,$x-1,1)!='[')&&$y!==false) break; // not escaped shortcode and shortcode is closed
	}
	if ($x!==false&&empty($set['htm'])) {
		wp_register_style('sf-css','//mfm-sourcefoundinc.netdna-ssl.com/all.css');
		wp_enqueue_style('sf-css');
	}
	if ($x!==false&&(isset($_GET['_escaped_fragment_'])||preg_match("/googlebot|slurp|msnbot|facebook/i",$_SERVER['HTTP_USER_AGENT'])>0)) {
		if (!isset($set['org'])||!$set['org'])
			return empty($arg)?'':$arg[0];
		$mat=array();
		$opt=array();
		if (preg_match_all('/\s([a-z]*)(="[^"]*")?/',substr($post->post_content,$x+1,$y-$x-1),$mat,PREG_PATTERN_ORDER)!==false) {
			foreach ($mat[1] as $key=>$val) $opt[$val]=empty($mat[2][$key])?'':substr($mat[2][$key],2,-1);
		}
		if (isset($_GET['_escaped_fragment_'])) {
			$pne=$_GET['_escaped_fragment_'];
			remove_action('wp_head','jetpack_og_tags');
			if (defined('WPSEO_VERSION')) { // Yoast SEO
				global $wpseo_front;
				remove_action('wp_head',array($wpseo_front,'head'),1);
			} else if (defined('AIOSEOP_VERSION')) { // All in One SEO
				global $aiosp;
				remove_action('wp_head',array($aiosp,'wp_head'));
			}
			remove_action('wp_head','rel_canonical');
			remove_action('wp_head','index_rel_link');
			remove_action('wp_head','start_post_rel_link');
			remove_action('wp_head','adjacent_posts_rel_link_wp_head');
			add_action('wp_head','sf_head',0);
		} else if (!empty($opt['open'])) {
			$pne=$opt['open'];
		} else {
			return empty($arg)?'':$arg[0];
		}
		do {
			if (empty($try)) $try=0; else usleep(100000);
			$rsp=wp_remote_get("http://www.sourcefound.com/api?hdr&dtl&org=".$set['org']
				."&url=".urlencode(get_permalink())
				."&pne=".urlencode($pne)
				.(empty($set['ctc'])?'':('&ctc=1'))
				.(empty($opt['lbl'])&&empty($opt['labels'])?'':('&lbl='.(empty($opt['lbl'])?$opt['labels']:$opt['lbl'])))
				.(empty($opt['folder'])?'':"&dek=".urlencode($opt['folder']))
				.(isset($opt['evg'])?('&evg='.$opt['evg']):''));
		} while (is_wp_error($rsp)&&($try++)<3);
		if (is_wp_error($rsp)||empty($rsp['body'])) 
			return empty($arg)?'':$arg[0];
		$sf_dat=json_decode($rsp['body'],true);
		$sf_dat['set']=$set;
		if (!isset($sf_dat['ttl'])||!$sf_dat['ttl']) 
			return empty($arg)?'':$arg[0];
		if (!empty($arg)&&!empty($arg[2]))
			return ($arg[2]=='left'?($arg[0].' '.(empty($arg[1])?'':($arg[1].' '))):'').$sf_dat['ttl'].($arg[2]=='right'?((empty($arg[1])?'':(' '.$arg[1])).' '.$arg[0]):'');
		else
			return $sf_dat['ttl'];
	} else 
		return empty($arg)?'':$arg[0];
}
add_filter('wp_title','sf_title',20,3);

function sf_head() {
	global $sf_dat;
	if ($sf_dat) {
		$out=array();
		if (isset($sf_dat['sum'])) 
			$out[]='<meta name="description" content="'.str_replace('"','&quot;',$sf_dat['sum']).'" />';
		if (isset($sf_dat['ttl'])) {
			$out[]='<meta property="og:site_name" content="'.str_replace('"','&quot;',get_bloginfo('name')).'" />';
			$out[]='<meta property="og:title" content="'.str_replace('"','&quot;',$sf_dat['ttl']).'" />';
		}
		if (isset($sf_dat['img'])) 
			$out[]='<meta property="og:image" content="'.$sf_dat['img'].'" />';
		if (isset($sf_dat['sum'])) 
			$out[]='<meta property="og:description" content="'.str_replace('"','&quot;',$sf_dat['sum']).'" />';
		if (isset($_GET['_escaped_fragment_'])&&isset($sf_dat['rel'])) {
			$out[]='<meta property="og:url" content="'.$sf_dat['rel'].'" />';
			$out[]='<link rel="canonical" href="'.$sf_dat['rel'].'" />';
		}
		if (isset($sf_dat['nxt'])) 
			$out[]='<link rel="next" href="'.$sf_dat['nxt'].'" />';
		if (isset($sf_dat['prv'])) 
			$out[]='<link rel="prev" href="'.$sf_dat['prv'].'" />';
		echo implode("\r\n",$out).(count($out)?"\r\n":'');
	}
}

function sf_shortcode($content) {
	global $sf_dat;
	$set=get_option('sf_set');
	$mfm=false;
	for ($i=0;($x=strpos($content,'[memberfindme ',$i))!==false;$i=$x+1) {
		$y=strpos($content,']',$x);
		if (($x>0&&substr($content,$x-1,1)=='[')||$y===false) continue; // escaped shortcode or shortcode not closed
		$mat=array();
		if (!preg_match_all('/\s([a-z]*)(=("|&[^;]*;)+.*?("|&[^;]*;))?/',substr($content,$x+1,$y-$x-1),$mat,PREG_PATTERN_ORDER)||empty($mat)||empty($mat[1])) 
			continue;
		$opt=array();
		foreach ($mat[1] as $key=>$val) $opt[$val]=empty($mat[2][$key])?'':trim(preg_replace('/^=("|&[^;]*;)*|("|&[^;]*;)$/','',$mat[2][$key]));
		// create output
		if ($set===false||empty($set['org'])) {
			$out=(isset($opt['open'])&&!$mfm?'<div>MemberFindMe organization key not setup. Please update settings.</div>':'');
		} else if (isset($opt['open'])) {
			if ($mfm) {
				$out='';
			} else if ($sf_dat) {
				$out='<div id="SFctr" class="SF" style="'.(isset($opt['style'])?$opt['style']:'position:relative;height:auto;').'">'
					.'<div id="SFpne" style="position:relative;">'.$sf_dat['dtl'].'</div><div style="clear:both;"></div></div>';
			} else {
				$out=(empty($set['htm'])?'':'<div style="display:none"><script>if (typeof(SF)=="object"&&SF.close) SF.close();</script></div>')
					.'<div id="SFctr" class="SF" data-ini="'.$opt['open'].'"'
					.(strpos($opt['open'],'account')===0?'':(' data-hme="'.$opt['open'].'"'))
					.(empty($set['org'])?'':(' data-org="'.$set['org'].'"'))
					.(empty($set['pay'])?'':(' data-pay="'.$set['pay'].'"'))
					.(empty($set['map'])?'':(' data-map="'.$set['map'].'"'))
					.(empty($set['fbk'])?'':(' data-fbk="'.$set['fbk'].'"'))
					.(empty($set['fnd'])?'':(' data-fnd="'.$set['fnd'].'"'))
					.(isset($set['adv'])?(' data-adv="'.$set['adv'].'"'):'')
					.(empty($set['rsp'])?'':(' data-rsp="'.$set['rsp'].'"'))
					.(empty($set['ctc'])?'':(' data-ctc="1"'))
					.(empty($set['scl'])&&empty($opt['noshare'])?'':(' data-scl="0"'))
					.(empty($set['out'])?'':(' data-out="'.$set['out'].'"'))
					.(empty($set['top'])?'':(' data-top="'.$set['top'].'"'))
					.(defined('SF_WPL')?(' data-wpl="'.esc_url(preg_replace('/^http[s]?:\\/\\/[^\\/]*/','',SF_WPL>=3?admin_url('admin-ajax.php'):site_url('wp-login.php','login_post'))).'"'):(empty($set['wpl'])?'':(' data-wpl="'.esc_url($set['wpl']).'"')))
					.(empty($opt['lbl'])&&empty($opt['labels'])?'':(' data-lbl="'.esc_attr(empty($opt['lbl'])?$opt['labels']:$opt['lbl']).'"'))
					.(empty($opt['folder'])?'':(' data-dek="'.esc_attr($opt['folder']).'"'))
					.(isset($opt['evg'])?(' data-evg="'.esc_attr($opt['evg']).'"'):'')
					.(isset($opt['viewport'])&&$opt['viewport']=='fixed'?(' data-ofy="1"'):'')
					.(isset($opt['redirect'])?(' data-zzz="'.$opt['redirect'].'"'):'')
					.(isset($opt['checkout'])?(' data-zgo="'.$opt['checkout'].'"'):'')
					.(isset($opt['ini'])&&$opt['ini']=='0'?'':' data-sfi="1"')
					.' style="'.(isset($opt['style'])?$opt['style']:'position:relative;height:auto;').'">'
					.'<div id="SFpne" style="position:relative;">'.(isset($opt['ini'])&&$opt['ini']=='0'?'':'<div class="SFpne">Loading...</div>').'</div>'
					.'<div style="clear:both;"></div>'
					.(empty($set['htm'])?'':'<script type="text/javascript" src="//mfm-sourcefoundinc.netdna-ssl.com/mfm.js" defer="defer"></script>')
					.'</div>';
				if (empty($set['htm']))
					wp_enqueue_script('sf-mfm');
			}
			$mfm=true;
		} else if (isset($opt['button'])) { 
			$out=(isset($opt['type'])?('<'.$opt['type']):'<button')
				.(isset($opt['type'])&&$opt['type']=='img'&&isset($opt['src'])?(' src="'.$opt['src'].'"'):'')
				.(isset($opt['class'])?(' class="'.$opt['class'].'"'):'')
				.(isset($opt['style'])?(' style="'.$opt['style'].'"'):' style="cursor:pointer;"')
				.($opt['button']=='account'?(' onmouseout="if(typeof(SF)!=\'undefined\')SF.usr.account(event,this);" onmouseover="if(typeof(SF)!=\'undefined\')SF.usr.account(event,this);" onclick="if(typeof(SF)!=\'undefined\')SF.usr.account(event,this);">'.(isset($opt['text'])?$opt['text']:'My Account')):'')
				.($opt['button']=='join'?(' onclick="if(typeof(SF)!=\'undefined\')SF.open(\'account/join\');">'.(isset($opt['text'])?$opt['text']:'Join')):'')
				.(isset($opt['type'])?($opt['type']=='img'?'':('</'.$opt['type'].'>')):'</button>');
		} else if (isset($opt['join'])) {
			$out=(isset($opt['type'])?('<'.$opt['type']):'<a')
				.(isset($opt['type'])&&$opt['type']=='img'&&isset($opt['src'])?(' src="'.$opt['src'].'"'):'')
				.(isset($opt['class'])?(' class="'.$opt['class'].'"'):'')
				.(isset($opt['style'])?(' style="'.$opt['style'].'"'):' style="cursor:pointer;"')
				.(isset($opt['type'])&&$opt['type']!='a'?(' onclick="window.location.hash=\'account/join/'.$opt['join'].'\';if(typeof(SF)!=\'undefined\')setTimeout(\'SF.init()\',50);">'):(' onclick="if(typeof(SF)!=\'undefined\')setTimeout(\'SF.init()\',50)" href="#account/join/'.$opt['join'].'">'))
				.(isset($opt['text'])?$opt['text']:'Join')
				.(isset($opt['type'])?($opt['type']=='img'?'':('</'.$opt['type'].'>')):'</a>');
		} else if (isset($opt['listlabel'])||isset($opt['listfolder'])) {
			do {
				if (empty($try)) $try=0; else usleep(100000);
				$rsp=wp_remote_get("http://www.sourcefound.com/api?fi=dek&org=".$set['org']."&typ=".(isset($opt['listlabel'])?3:1)."&wem=1&lbl=".urlencode(isset($opt['listlabel'])?$opt['listlabel']:$opt['listfolder']));
			} while (is_wp_error($rsp)&&($try++)<3);
			if (is_wp_error($rsp)||empty($rsp['body'])) {
				$out='MFM connection error';
			} else {
				$dat=json_decode($rsp['body'],true);
				$out=array();
				if (!empty($dat)) foreach ($dat as $usr)
					$out[]='<li><a href="'.esc_attr($usr['url']).'">'.esc_html($usr['nam']).'</a></li>';
				$out='<ul class="sf_list">'.implode('',$out).'</ul>';
			}
		} else if (isset($opt['listevents'])) {
			do {
				if (empty($try)) $try=0; else usleep(100000);
				$rsp=wp_remote_get("http://www.sourcefound.com/api?fi=evt&org=".$set['org']."&wee=1&grp=".$opt['listevents']."&cnt=".(isset($opt['count'])?$opt['count']:'5')."&sdp=".time());
			} while (is_wp_error($rsp)&&($try++)<3);
			if (is_wp_error($rsp)||empty($rsp['body'])) {
				$out='MFM connection error';
			} else {
				$dat=json_decode($rsp['body'],true);
				$out=array();
				if (!empty($dat)) foreach ($dat as $evt) {
					$te=explode(',',$evt['ezp']);
					$ts=explode(',',$evt['szp']);
					if (!empty($evt['ezp'])&&$te[0]==$ts[0]) $evt['ezp']=trim($te[1]);
					$out[]='<li><a href="'.$evt['url'].'">'.esc_html($evt['ttl']).'</a><div class="event-when"><span class="event-start">'.$evt['szp'].'</span>'.(isset($evt['ezp'])&&$evt['ezp']?('<span class="event-sep"> - </span><span class="event-end">'.$evt['ezp'].'</span>'):'').'</div></li>';
				}
				$out='<ul class="sf_list">'.implode('',$out).'</ul>';
			}
		} else
			$out='';
		$content=substr_replace($content,$out,$x,$y-$x+1);
	}
	if ($mfm)
		define('DONOTCACHEPAGE',true);
	return $content;
}
add_filter('the_content','sf_shortcode',99);

class sf_widget_event extends WP_Widget {
	public function __construct() {
		parent::__construct('sf_widget_event','MemberFindMe Events',array('description'=>'Upcoming events from your MemberFindMe calendar'));
	}
	public function widget($args,$instance ) {
		extract($args);
		$set=get_option('sf_set');
		do {
			if (empty($try)) $try=0; else usleep(100000);
			$rsp=wp_remote_get("http://www.sourcefound.com/api?fi=evt&org=".$set['org']."&wee=1&grp=".$instance['grp']."&cnt=".$instance['cnt']."&sdp=".time());
		} while (is_wp_error($rsp)&&($try++)<3);
		if (is_wp_error($rsp)||empty($rsp['body'])) return;
		$dat=json_decode($rsp['body'],true);
		$title=apply_filters('widget_title',$instance['title']);
		echo $before_widget;
		if (!empty($title))
			echo $before_title.$title.$after_title;
		echo '<ul class="sf_widget_event_list">';
		if (!empty($dat)) foreach ($dat as $x) {
			$te=explode(',',$x['ezp']);
			$ts=explode(',',$x['szp']);
			if (isset($x['ezp'])&&$x['ezp']&&$te[0]==$ts[0]) $x['ezp']=trim($te[1]);
			echo '<li class="event-item"><a class="event-link" href="'.$x['url'].'">'.$x['ttl'].'</a><div class="event-when"><span class="event-start">'.$x['szp'].'</span>'.(isset($x['ezp'])&&$x['ezp']?('<span class="event-sep"> - </span><span class="event-end">'.$x['ezp'].'</span>'):'').'</div></li>';
		}
		echo '</ul>';
		echo $after_widget;
	}
	public function update($new_instance,$old_instance ) {
		$instance=$old_instance;
		$instance['title']=strip_tags($new_instance['title']);
		$instance['grp']=isset($new_instance['grp'])?$new_instance['grp']:'';
		$instance['cnt']=$new_instance['cnt']?strval(intval($new_instance['cnt'])):'0';
		return $instance;
	}
	public function form($instance) {
		$instance=wp_parse_args($instance,array('title'=>'','grp'=>'','cnt'=>'3'));
		$title=strip_tags($instance['title']);
		$grp=$instance['grp'];
		$cnt=intval($instance['cnt']);
		echo '<p><label for="'.$this->get_field_id('title').'">Title:</label> <input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.esc_attr($title).'" /></p>';
		echo '<p><label for="'.$this->get_field_id('grp').'">Calendar group:</label> <input id="'.$this->get_field_id('grp').'" name="'.$this->get_field_name('grp').'" type="text" value="'.$grp.'" size="3"/></p>';
		echo '<p><label for="'.$this->get_field_id('cnt').'">Number of events to show:</label> <input id="'.$this->get_field_id('cnt').'" name="'.$this->get_field_name('cnt').'" type="text" value="'.$cnt.'" size="3"/></p>';
	}
}

class sf_widget_folder extends WP_Widget {
	public function __construct() {
		parent::__construct('sf_widget_folder','MemberFindMe Widget',array('description'=>'Display contacts from your public MemberFindMe folder or label'));
	}
	public function widget($args,$instance ) {
		extract($args);
		$set=get_option('sf_set');
		do {
			if (empty($try)) $try=0; else usleep(100000);
			$rsp=wp_remote_get("http://www.sourcefound.com/api?fi=dek&org=".$set['org']."&typ=".(empty($instance['typ'])?'1':$instance['typ'])."&wem=1&lbl=".urlencode($instance['lbl']));
		} while (is_wp_error($rsp)&&($try++)<3);
		if (is_wp_error($rsp)||empty($rsp['body'])) return;
		$dat=json_decode($rsp['body'],true);
		$title=apply_filters('widget_title',$instance['title']);
		if (empty($title))
			echo str_replace('widget_sf_widget_folder','widget_sf_widget_folder widget_no_title',$before_widget);
		else
			echo $before_widget;
		if (!empty($title))
			echo $before_title.$title.$after_title;
		if ($instance['act']=='1') {
			$fn=str_replace('-','_',$this->id);
			echo '<ul id="'.$this->id.'-list" class="sf_widget_folder_logos" style="list-style:none;margin:0;padding:5px;">';
		} else
			echo '<ul id="'.$this->id.'-list" class="sf_widget_folder_list">';
		if (!empty($dat)) foreach ($dat as $x) {
			if ($instance['act']=='1')
				echo '<li style="display:none;background-color:white;text-align:center;height:148px;padding:0;margin:0;table-layout:fixed;width:100%;"><a href="'.esc_attr($x['url']).'" style="display:table-cell;vertical-align:middle;padding:10px;text-decoration:none;"><div style="display:block;width:100%;font-size:1.5em;">'
					.($x['lgo']?('<img src="//usr-sourcefoundinc.netdna-ssl.com/'.$x['_id'].'_lgl.jpg?'.$x['lgo'].'" alt="'.esc_attr($x['nam']).'" onerror="this.parentNode.innerHTML=this.alt;" style="display:block;margin:0 auto;max-width:100%;max-height:75px;">'):esc_html($x['nam']))
					.'</div><small class="cnm" style="display:block;padding:10px;">'.esc_html($x['cnm']).'</small></a></li>';
			else
				echo '<li><a href="'.esc_attr($x['url']).'">'.$x['nam'].'</a><small class="cnm" style="display:block;">'.esc_html($x['cnm']).'</small></li>';
		}
		echo '</ul>';
		if ($instance['act']=='1'&&!empty($x)) {
			$delay=intval($instance['delay'])*1000;
			echo '<script>'
				.$fn.'_animate=function(){var r=document.getElementById("'.$this->id.'-list"),x=r.querySelector(\'li[style*="table;"]\');if (x) {x.style.display="none";x=(x.nextSibling?x.nextSibling:r.firstChild);} else x=r.childNodes[Math.round(Math.random()*(r.childNodes.length-1))];if (x) x.style.display="table";setTimeout('.$fn.'_animate,'.($delay?$delay:10000).');};'
				.$fn.'_animate();'
				.'</script>';
		}
		echo $after_widget;
	}
	public function update($new_instance,$old_instance ) {
		$instance=$old_instance;
		$instance['title']=strip_tags($new_instance['title']);
		$instance['lbl']=trim($new_instance['lbl']);
		$instance['typ']=strval(intval($new_instance['typ']));
		$instance['act']=strval(intval($new_instance['act']));
		$instance['delay']=strval(intval($new_instance['delay']));
		return $instance;
	}
	public function form($instance) {
		$instance=wp_parse_args($instance,array('title'=>'','typ'=>'1','lbl'=>'','act'=>'0','delay'=>'10'));
		$title=strip_tags($instance['title']);
		echo '<p><label for="'.$this->get_field_id('title').'">Title:</label> <input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.esc_attr($title).'" /></p>'
			.'<p><label for="'.$this->get_field_id('lbl').'">Folder/label name:</label> <input class="widefat" id="'.$this->get_field_id('lbl').'" name="'.$this->get_field_name('lbl').'" type="text" value="'.esc_attr($instance['lbl']).'" /></p>'
			.'<p><label for="'.$this->get_field_id('typ').'">Type:</label> <select id="'.$this->get_field_id('typ').'" name="'.$this->get_field_name('typ').'">'
				.'<option value="1"'.($instance['typ']=='1'?' selected="selected"':'').'>Public folder</option>'
				.'<option value="3"'.($instance['typ']=='3'?' selected="selected"':'').'>Publicly searchable label</option>'
			.'</select></p>'
			.'<p><label for="'.$this->get_field_id('act').'">Display:</label> <select id="'.$this->get_field_id('act').'" name="'.$this->get_field_name('act').'" onchange="this.parentNode.nextSibling.style.display=(this.value==\'1\'?\'\':\'none\');">'
				.'<option value="0"'.($instance['act']=='0'?' selected="selected"':'').'>List</option>'
				.'<option value="1"'.($instance['act']=='1'?' selected="selected"':'').'>Slideshow</option>'
			.'</select></p>'
			.'<p'.($instance['act']=='1'?'':' style="display:none;"').'><label for="'.$this->get_field_id('delay').'">Seconds between slides:</label> <input id="'.$this->get_field_id('delay').'" name="'.$this->get_field_name('delay').'" type="text" value="'.$instance['delay'].'" size="3"/></p>';
	}
}

function sf_widgets_init() {
	register_widget('sf_widget_event');
	register_widget('sf_widget_folder');
}
add_action('widgets_init','sf_widgets_init');

?>