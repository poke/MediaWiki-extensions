# MediaWiki extensions

## AlternativeUserTalk
The *AlternativeUserTalk* extension allows users to use a different page as their user talk page. This extension basically changes the new messages notice so it appears for changes on the alternative user talk page, as specified in a custom system message, and adds functionality to clear it for that page.

Given how the extension currently is made, it cannot be used on wiki farms, where multiple wikis are connected and you are able to see new messages notices from other wikis. This is because of the construction of the notice itself, as it uses the *multi new messages* feature to be able to link to the alternative talk page.

[Extension:AlternativeUserTalk on MediaWiki.org](http://www.mediawiki.org/wiki/Extension:AlternativeUserTalk).
