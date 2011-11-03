# MediaWiki extensions

## DismissableSiteNoticePlus
*DismissableSiteNoticePlus* is a fork of the original [DismissableSiteNotice](http://mediawiki.org/wiki/Extension:DismissableSiteNotice) by Brion Vibber. It features an improved output format that also allows a better styling of the site notice.

## ListTransclusions
The *ListTransclusions* extension adds a special page `Special:ListTransclusions` which lists all used images and templates of a given page. It also adds a link to the toolbox portlet to quickly access the list for the current shown page.

The extension was created to make sure that attribution information for licenses such as the GFDL is easily accessible even if an article uses multiple nested templates or images with the link parameter, that makes it impossible to reach the image description page without browsing through the page's source code.

[Extension:ListTransclusions on MediaWiki.org](http://mediawiki.org/wiki/Extension:ListTransclusions).

## AlternativeUserTalk
The *AlternativeUserTalk* extension allows users to use a different page as their user talk page. This extension basically changes the new messages notice so it appears for changes on the alternative user talk page, as specified in a custom system message, and adds functionality to clear it for that page.

Given how the extension currently is made, it cannot be used on wiki farms, where multiple wikis are connected and you are able to see new messages notices from other wikis. This is because of the construction of the notice itself, as it uses the *multi new messages* feature to be able to link to the alternative talk page.

[Extension:AlternativeUserTalk on MediaWiki.org](http://www.mediawiki.org/wiki/Extension:AlternativeUserTalk).