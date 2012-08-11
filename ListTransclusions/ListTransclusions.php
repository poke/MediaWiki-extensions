<?php
/**
 * ListTransclusions extension
 *
 * @author Patrick Westerhoff [poke]
 */
if ( !defined( 'MEDIAWIKI' ) )
	exit( 1 );

$wgExtensionFunctions[]              = 'efListTransclusions';
$wgExtensionCredits['specialpage'][] = array(
	'name'           => 'ListTransclusions',
	'author'         => 'Patrick Westerhoff',
	'url'            => 'http://mediawiki.org/wiki/Extension:ListTransclusions',
	'description'    => 'Lists all transcluded templates and used images of a given page',
	'descriptionmsg' => 'listtransclusions-desc',
);

/* Extension setup */
$dir                                           = dirname( __FILE__ ) . '/';
$wgAutoloadClasses['ListTransclusions']        = $dir . 'ListTransclusions_body.php';
$wgExtensionMessagesFiles['ListTransclusions'] = $dir . 'ListTransclusions.i18n.php';
$wgExtensionAliasesFiles['ListTransclusions']  = $dir . 'ListTransclusions.alias.php';
$wgSpecialPages['ListTransclusions']           = 'ListTransclusions';
$wgSpecialPageGroups['ListTransclusions']      = 'pagetools';

/**
 * SkinTemplateToolboxEnd hook
 *
 * @param $tpl Object the calling template object
 * @param $dummy boolean
 * @return boolean always true
 */
function efListTransclusionsSkinTemplateToolboxEnd ( $tpl, $dummy )
{
	if ( $dummy )
		return true;
	
	if( $tpl->data['notspecialpage'] )
	{
		$spTitle = SpecialPage::getTitleFor( 'ListTransclusions', $tpl->getSkin()->thispage );
		
		echo "\n				";
		echo '<li id="t-listtransclusions"><a href="' . htmlspecialchars( $spTitle->getLocalUrl() ) . '"';
		echo Linker::tooltipAndAccesskeyAttribs( 't-listtransclusions' ) . '>';
		$tpl->msg( 'listtransclusions' );
		echo "</a></li>\n";
	}
	return true;
}

/**
 * BaseTemplateToolbox hook
 *
 * @param $tpl Object the skin template
 * @param $toolbox Object array of toolbox items
 * @return boolean always true
 */
function efListTransclusionsBaseTemplateToolbox ( $tpl, $toolbox )
{
	if ( $tpl->data['notspecialpage'] )
	{
		$toolbox['listtransclusions'] = array(
			'href' => SpecialPage::getTitleFor( 'ListTransclusions', $tpl->getSkin()->thispage )->getLocalUrl(),
			'id' => 't-listtransclusions'
		);
	}
	return true;
}


/**
 * Extension initialization
 */
function efListTransclusions ()
{
	global $wgHooks;
	wfLoadExtensionMessages( 'ListTransclusions' );
	
	// Hooks to add entry to the toolbox
	$wgHooks['SkinTemplateToolboxEnd'][] = 'efListTransclusionsSkinTemplateToolboxEnd';
	$wgHooks['BaseTemplateToolbox'][] = 'efListTransclusionsBaseTemplateToolbox';
}