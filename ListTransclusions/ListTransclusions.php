<?php
/**
 * ListTransclusions extension
 *
 * @author Patrick Westerhoff [poke]
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	exit( 1 );
}

$wgExtensionCredits['specialpage'][] = array(
	'path'           => __FILE__,
	'name'           => 'ListTransclusions',
	'author'         => 'Patrick Westerhoff',
	'url'            => 'http://mediawiki.org/wiki/Extension:ListTransclusions',
	'description'    => 'Lists all transcluded templates and used images of a given page',
	'descriptionmsg' => 'listtransclusions-desc',
);

/* Extension setup */
$dir = dirname( __FILE__ ) . '/';
$wgAutoloadClasses['SpecialListTransclusions'] = $dir . 'SpecialListTransclusions.php';
$wgExtensionMessagesFiles['ListTransclusions'] = $dir . 'ListTransclusions.i18n.php';
$wgExtensionMessagesFiles['ListTransclusionsAlias'] = $dir . 'ListTransclusions.alias.php';

$wgSpecialPages['ListTransclusions'] = 'SpecialListTransclusions';
$wgSpecialPageGroups['ListTransclusions'] = 'pagetools';

/**
 * BaseTemplateToolbox hook
 *
 * @param $tpl Object the skin template
 * @param $toolbox Object array of toolbox items
 * @return boolean always true
 */
function efListTransclusionsBaseTemplateToolbox ( $tpl, $toolbox ) {
	if ( $tpl->data['notspecialpage'] ) {
		$toolbox['listtransclusions'] = array(
			'href' => SpecialPage::getTitleFor( 'ListTransclusions', $tpl->getSkin()->thispage )->getLocalUrl(),
			'id' => 't-listtransclusions'
		);
	}
	return true;
}

/* Extension hooks */
$wgHooks['BaseTemplateToolbox'][] = 'efListTransclusionsBaseTemplateToolbox';