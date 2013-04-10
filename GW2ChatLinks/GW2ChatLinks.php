<?php
/**
 * Guild Wars 2 Chat Links extension
 *
 * @author Patrick Westerhoff [poke]
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	exit( 1 );
}

$wgExtensionCredits['other'][] = array(
	'path'           => __FILE__,
	'name'           => 'GW2ChatLinks',
	'author'         => 'Patrick Westerhoff',
	'url'            => 'http://wiki.guildwars2.com/wiki/User:Poke/GW2ChatLinks',

	'description'    => 'Native support for Guild Wars 2 chat links',
	//'descriptionmsg' => 'gw2chatlinks-desc',
);

/* Extension setup */
$dir = dirname( __FILE__ ) . '/';
$wgExtensionMessagesFiles['GW2ChatLinks'] = $dir . '/GW2ChatLinks.i18n.php';

class GW2ChatLinks
{
	/**
	 * The SpecialSearchSetupEngine hook runs when the search engine is set up.
	 *
	 * @param $specialPage SpecialSearch the special page instance
	 * @param $profile string the search profile
	 * @param $search SearchEngine the configured search engine
	 */
	public static function onSpecialSearchSetupEngine ( $specialPage, $profile, $search ) {
		$out = $specialPage->getOutput();
		$term = $specialPage->getRequest()->getVal('search');

		$matches = null;
		if (preg_match_all('/\[&([A-Za-z0-9\/=]+)\]/', $term, $matches, PREG_SET_ORDER))
		{
			$out->addWikiText( 'Your search contained the following [[chat link]]s:' );

			$out->addHtml( '<ul style="margin-bottom: 1em;">' );
			foreach ($matches as $match) {
				$out->addHtml( '<li>' . $match[1] . '</li>' );
			}
			$out->addHtml( '</ul>' );

			$out->addWikiText( '== Standard search results ==' );
			//$out->redirect($t->getFullURL(array('action' => 'edit')));
		}

		return true;
	}

	public static function onParserFirstCallInit ( &$parser ) {
		$parser->setFunctionHook( 'chatlink', 'GW2ChatLinks::renderChatLink' );

		return true;
	}

	public static function parseType ( $type )
	{
		// normalize
		$type = strtolower( trim( $type ) );

		switch ( $type ) {
			case 'coin':
			case 'gold':
			case 'g':
			case 'silver':
			case 's':
			case 'copper':
			case 'c':
				return 0x01;

			case 'item':
			case 'i':
				return 0x02;

			case 'text':
				return 0x03;

			case 'map':
			case 'm':
			case 'location':
			case 'l':
			case 'pointofinterest':
			case 'poi':
			case 'waypoint':
			case 'wp':
				return 0x04;

			case 'skill':
			case 's':
				return 0x06;

			case 'trait':
			case 't':
				return 0x08;

			case 'recipe':
			case 'r':
				return 0x0A;

			default:
				return intval($type);
		}
	}

	public static function renderChatLink ( $parser, $type, $id = 0 ) {
		$type = GW2ChatLinks::parseType( $type );
		if ( $type <= 0 )
			return '';

		$c = new GW2ChatLinkConverter;

		if ($type == 1)
			$chatLink = $c->encodeCoin($id);
		else if ($type == 2)
			$chatLink = $c->encodeItem($id, $type);
		else
			$chatLink = $c->encodeId($id, $type);

		return '[&' . $chatLink . ']';
	}
}

class GW2ChatLinkConverter {
	public function encodeCoin ( $amount ) {
		return base64_encode( pack( 'cV', 0x01, $amount ) );
	}
	public function decodeCoin ( $code ) {
		$data = unpack( 'ctype/Vamount', base64_decode( $code ) );
		return $data['amount'];
	}

	public function encodeId ( $id, $header ) {
		return base64_encode( pack('cV', $header, $id ) );
	}
	public function decodeId ( $code ) {
		return unpack( 'ctype/Vid', base64_decode( $code ) );
	}

	public function encodeItem ( $id, $header, $amount = 1 ) {
		return base64_encode( pack('ccV', $header, $amount, $id ) );
	}
	public function decodeItem ( $code ) {
		return unpack( 'ctype/ccount/Vid', base64_decode( $code ) );
	}
}

/* Extension hooks */
$wgHooks['SpecialSearchSetupEngine'][] = 'GW2ChatLinks::onSpecialSearchSetupEngine';
$wgHooks['ParserFirstCallInit'][] = 'GW2ChatLinks::onParserFirstCallInit';