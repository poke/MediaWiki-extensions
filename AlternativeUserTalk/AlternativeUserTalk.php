<?php
/**
 * AlternativeUserTalk extension
 *
 * @author Patrick Westerhoff [poke]
 */
if ( !defined( 'MEDIAWIKI' ) )
	exit( 1 );

$wgExtensionFunctions[]        = 'efAlternativeUserTalk';
$wgExtensionCredits['other'][] = array(
	'name'           => 'AlternativeUserTalk',
	'author'         => 'Patrick Westerhoff',
	'url'            => 'http://mediawiki.org/wiki/Extension:AlternativeUserTalk',
	'description'    => 'Allows to set alternative user talk page with a working "new messages" notice',
	'descriptionmsg' => 'alternativeusertalk-desc',
);

/* Extension setup */
$dir                                             = dirname( __FILE__ ) . '/';
$wgExtensionMessagesFiles['AlternativeUserTalk'] = $dir . 'AlternativeUserTalk.i18n.php';

/**
 * ArticleEditUpdates hook
 *
 * @param &$article Object current article
 * @param &$editInfo Object information about the edit
 * @param $changed boolean true if the page was changed
 * @return boolean
 */
function efAlternativeUserTalkArticleEditUpdates ( &$article, $editInfo, $changed )
{
	// we don't notify on null or unimportant edits
	if ( !$changed )
		return true;

	$config = wfMessage( 'alternativeusertalk-conf' )->inContentLanguage();
	if ( $config->isBlank() )
		return true;

	$source    = explode( "\n", $config->text() );
	$pageTitle = $article->mTitle->getPrefixedText();
	$userName  = '';

	// look for pageTitle in the settings
	foreach ( $source as $entry )
	{
		$entry = explode( '=', $entry );
		if ( trim( $entry[1] ) == $pageTitle )
		{
			$userName = trim( $entry[0] );
			break;
		}
	}

	// no match, then we are done
	if ( $userName === '' )
		return true;

	// this is the alternative user talk page for $userName, so add a notice now.
	$other = User::newFromName( $userName, false );
	if ( !$other )
		wfDebug( __METHOD__ . ": invalid username\n" );
	elseif ( $other->isLoggedIn() )
		$other->setNewtalk( true );
	else
		wfDebug( __METHOD__ . ": don't need to notify a nonexistent user\n" );

	return true;
}


/**
 * UserRetrieveNewTalks hook
 *
 * @param &$user Object current user
 * @param &$talks array talk page entries
 * @return boolean
 */
function efAlternativeUserTalkUserRetrieveNewTalks ( &$user, &$talks )
{
	global $wgTitle;

	// no new messages
	if ( !$user->getNewtalk() )
		return true;

	$config = wfMessage( 'alternativeusertalk-conf' )->inContentLanguage();
	if ( $config->isBlank() )
		return true;

	// try to find alternative user talk page entry
	$userName = $user->getName();
	$source   = explode( "\n", $config->text() );

	foreach ( $source as $entry )
	{
		$entry = explode( '=', $entry );
		if ( trim( $entry[0] ) == $userName )
		{
			// check if we are on that page, in which case we need to clear the notification manually
			if ( $wgTitle->getPrefixedText() == trim( $entry[1] ) )
			{
				$user->setNewtalk( false );
				return true;
			}

			// alternative talk page
			$atp = Title::newFromText( trim( $entry[1] ) );

			// kind of abusing the multi-new-message feature for multiple wikis here
			// to be able to change the target link... (wiki is the "link text" here)
			$talks[] = array( 'wiki' => wfMessage( 'alternativeusertalk-link' )->escaped(),
				'link' => $atp->getLinkUrl( array( 'redirect' => 'no' ) ));
			$talks[] = array( 'wiki' => wfMessage( 'alternativeusertalk-diff' )->escaped(),
				'link' => $atp->getLinkUrl( array( 'diff' => 'cur' ) ) );

			return false;
		}
	}
	return true;
}


/**
 * Extension initialization
 */
function efAlternativeUserTalk ()
{
	global $wgHooks;
	wfLoadExtensionMessages( 'AlternativeUserTalk' );

	$wgHooks['ArticleEditUpdates'][]   = 'efAlternativeUserTalkArticleEditUpdates';
	$wgHooks['UserRetrieveNewTalks'][] = 'efAlternativeUserTalkUserRetrieveNewTalks';
}
