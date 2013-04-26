<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'DismissableSiteNoticePlus',
	'author' => 'Brion Vibber, Patrick Westerhoff',
	'description' => 'Allows users to close the sitenotice. Based on the [http://www.mediawiki.org/wiki/Extension:DismissableSiteNotice original version] by Brion Vibber.',
	'url' => 'http://wiki.guildwars.com/wiki/User:Poke',
);

$wgExtensionMessagesFiles['DismissableSiteNotice'] = __FILE__ . '/DismissableSiteNotice.i18n.php';

function wfDismissableSiteNoticeBefore( &$notice ) {
	global $wgMajorSiteNoticeID;

	$noticeId = intval( $wgMajorSiteNoticeID ) . '.' . intval( wfMessage( 'sitenotice_id' )->inContentLanguage()->text() );

	if ( isset( $_COOKIE['dismissSiteNotice'] ) && $_COOKIE['dismissSiteNotice'] == $noticeId ) {
		return false;
	}

	return true;
}

function wfDismissableSiteNotice( &$notice ) {
	global $wgMajorSiteNoticeID, $wgUser;

	if ( !$notice ) {
		return true;
	}

	$noticeId = intval( $wgMajorSiteNoticeID ) . '.' . intval( wfMessage( 'sitenotice_id' )->inContentLanguage()->text() );
	$closeText = wfMessage( 'sitenotice_close' )->text();
	$encNotice = Xml::escapeJsString($notice);
	$encClose = Xml::escapeJsString( wfMessage( 'sitenotice_close' )->text() );

	$notice = <<<EOT
		<script type="text/javascript">/*<![CDATA[*/
		function dismissNotice() {
			var noticeDiv  = document.getElementById( 'mw-dismissable-notice' );
			var expireDate = new Date();
			expireDate.setTime( expireDate.getTime() + 30 * 86400 * 1000 ); // 30 days
			document.cookie = 'dismissSiteNotice=$noticeId; expires=' + expireDate.toGMTString() + '; path=/';
			noticeDiv.parentNode.removeChild( noticeDiv );
		}
		/*]]>*/</script>
		<table id="mw-dismissable-notice" style="width: 100%; margin-top: 0.5em;" cellpadding="0" cellspacing="0"><tr>
			<td>$notice</td>
			<td style="padding-left: 0.5em;">[<a href="javascript:dismissNotice();">$closeText</a>]</td>
		</tr></table>
EOT;

	return true;
}

$wgHooks['SiteNoticeBefore'][] = 'wfDismissableSiteNoticeBefore';
$wgHooks['SiteNoticeAfter'][] = 'wfDismissableSiteNotice';
$wgMajorSiteNoticeID = 1;