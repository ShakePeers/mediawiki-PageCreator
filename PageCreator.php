
<?php

/**
 * Step 1: choose a magic word ID
 *
 * Storing the chosen ID in a constant is not required, but still good
 * programming practice - it makes searching for all occurrences of the magic
 * word ID a bit easier.
 * Note that the name of the constant and the value it is assigned don't have
 * to have anything to do with each other.
 */
define( 'PPP_PAGECREATOR', 'PAGECREATOR' );
define( 'PPP_CREATIONTIMESTAMP', 'CREATIONTIMESTAMP' );


 
/**
 * Step 2: define some words to use in wiki markup
 */
$wgHooks['LanguageGetMagic'][] = 'wfPppWikiWords';
function wfPppWikiWords( &$magicWords, $langCode ) {
        // tell MediaWiki that all {{NiftyVar}}, {{NIFTYVAR}}, {{CoolVar}},
        // {{COOLVAR}} and all case variants found in wiki text should be mapped to
        // magic ID 'mycustomvar1' (0 means case-insensitive)
        $magicWords[PPP_PAGECREATOR] = array( 0, PPP_PAGECREATOR);
        $magicWords[PPP_CREATIONTIMESTAMP] = array( 0, PPP_CREATIONTIMESTAMP);
 
        // must do this or you will silence every LanguageGetMagic hook after this!
        return true;
}
 
/**
 * Step 3: assign a value to our variable
 */
$wgHooks['ParserGetVariableValueSwitch'][] = 'wfPppAssignAValue';
function wfPppAssignAValue( &$parser, &$cache, &$magicWordId, &$ret ) {
        if ( PPP_PAGECREATOR == $magicWordId ) {
                        global $wgUser;
                        $revuser = $wgUser->getName();

                $ret = $revuser;
               
                global $wgArticle;

                if (isset($wgArticle))
                {
                  $myArticle=$wgArticle;
                }
                else
                {
                  $myTitle=$parser->getTitle();
                  $myArticle=new Article($myTitle);
                }

                $dbr = wfGetDB( DB_SLAVE );
                $revTable = $dbr->tableName( 'revision' );

                $pageId = $myArticle->getId();
                $q0 = "select rev_user_text from ".$revTable." where rev_page=".$pageId." order by rev_timestamp asc limit 1";
                if(($res0 = mysql_query($q0)) && ($row0 = mysql_fetch_object($res0)))
                {
                  $ret=$row0->rev_user_text;
// $ret= $magicWordId ;
                }
                else
                {
// try to print a little bit of debug info there
                  $myTitle=$parser->getTitle();
                  $articleId=$myTitle->getArticleID();
//                $ret="pageId:".$pageId."-arcticleId:".$articleId."-getText:".$myTitle->getText()."-getFullText:".$myTitle->getFullText();
                }
        }




        if ( PPP_CREATIONTIMESTAMP == $magicWordId ) {
                        global $wgUser;
                        $revuser = $wgUser->getName();

                $ret = $revuser;
               
                global $wgArticle;

                if (isset($wgArticle))
                {
                  $myArticle=$wgArticle;
                }
                else
                {
                  $myTitle=$parser->getTitle();
                  $myArticle=new Article($myTitle);
                }

                $dbr = wfGetDB( DB_SLAVE );
                $revTable = $dbr->tableName( 'revision' );

                $pageId = $myArticle->getId();
                $q0 = "select rev_timestamp from ".$revTable." where rev_page=".$pageId." order by rev_timestamp asc limit 1";
                if(($res0 = mysql_query($q0)) && ($row0 = mysql_fetch_object($res0)))
                {
                  $ret=$row0->rev_timestamp;
//$ret='coucou';
                }
                else
                {
// try to print a little bit of debug info there
                  $myTitle=$parser->getTitle();
                  $articleId=$myTitle->getArticleID();
//                $ret="pageId:".$pageId."-arcticleId:".$articleId."-getText:".$myTitle->getText()."-getFullText:".$myTitle->getFullText();
                }
        }




        // We must return true for two separate reasons:
        // 1. To permit further callbacks to run for this hook.
        //    They might override our value but that's life.
        //    Returning false would prevent these future callbacks from running.
        // 2. At the same time, "true" indicates we found a value.
        //    Returning false would the set variable value to null.
        //
        // In other words, true means "we found a value AND other
        // callbacks will run," and false means "we didn't find a value
        // AND abort future callbacks." It's a shame these two meanings
        // are mixed in the same return value.  So as a rule, return
        // true whether we found a value or not.
        return true;
}
 
/**
 * Step 4: register the custom variable(s) so that it shows up in
 * Special:Version under the listing of custom variables.
 */
$wgExtensionCredits['variable'][] = array(
        'name' => 'PageCreator',
        'author' => 'Pierro78',
        'version' => '0.3',
        'description' => 'Provides variables for retrieving the creator of a page an the time stamp of page creation',
        'url' => 'https://www.mediawiki.org/wiki/Extension:PageCreator',
);
 
/**
 * Step 5: register wiki markup words associated with
 *         PPP_PAGECREATOR as a variable and not some
 *         other type of magic word
 */
$wgHooks['MagicWordwgVariableIDs'][] = 'wfPppDeclareVarIds';
function wfPppDeclareVarIds( &$customVariableIds ) {
        // $customVariableIds is where MediaWiki wants to store its list of custom
        // variable IDs. We oblige by adding ours:
        $customVariableIds[] = PPP_PAGECREATOR;
        $customVariableIds[] = PPP_CREATIONTIMESTAMP;
 
        // must do this or you will silence every MagicWordwgVariableIds hook
        // registered after this!
        return true;
}
