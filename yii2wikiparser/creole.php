<?php
 
 /**
 * This class is merely used to publish assets that are needed by all yiimetroui
 * widgets and thus have to be imported before any widget gets rendered.
 * @copyright Frenzel GmbH - www.frenzel.net
 * @link http://www.frenzel.net
 * @author Philipp Frenzel <philipp@frenzel.net>
 */

namespace yii2wikiparser;

use rule;
use extension;
use image;
use interwiki_link;
use unnamed_interwiki_link;
use default_fallback;
use named_uri;
use unnamed_uri;
use named_link;
use unnamed_link;
use node;


class creole{
    
    /**
	 * @var array the grammar for the parser.
	 */
    public $grammar=array();

    /**
	 * @var array the HTML attributes for the widget container tag.
	 */
    public $options=array();

    public function __construct($options = array()) {

        $this->options = $options;

        $rx['ext'] = '<<<([^>]*(?:>>?(?!>)[^>]*)*)>>>';
        $rx['link'] = '[^\]|~\n]*(?:(?:\](?!\])|~.)[^\]|~\n]*)*';
        $rx['link_text'] = '[^\]~\n]*(?:(?:\](?!\])|~.)[^\]~\n]*)*';
        $rx['uri_prefix'] = '\b(?:(?:https?|ftp):\\/\\/|mailto:)';
        $rx['uri'] = $rx['uri_prefix'] . $rx['link'];
        $rx['raw_uri'] = $rx['uri_prefix'] . '\S*[^\s!"\',.:;?]';
        $rx['interwiki_prefix'] = '[\w.]+:';
        $rx['interwiki_link'] = $rx['interwiki_prefix'] . $rx['link'];
        $rx['image'] = '\{\{((?!\{)[^|}\n]*(?:}(?!})[^|}\n]*)*)' .
            '(?:\|([^}~\n]*((}(?!})|~.)[^}~\n]*)*))?}}';

        $g = array(
            'hr' => array(
                'tag' => 'hr',
                'regex' => '/(^|\n)\s*----\s*(\n|$)/'
            ),

            'br' => array(
                'tag' => 'br',
                'regex' => '/\\\\\\\\/'
            ),

            'pre' => array(
                'tag' => 'pre',
                'regex' => '/(^|\n)\{\{\{[ \t]*\n((.*\n)*?)}}}[ \t]*(\n|$)/',
                'capture' => 2,
                'replace_regex' => '/^ ([ \t]*}}})/m',
                'replace_string' => '$1'
            ),
            'tt' => array(
                'tag' => 'tt',
                'regex' => '/\{\{\{(.*?}}}+)/',
                'capture' => 1,
                'replace_regex' => '/}}}$/',
                'replace_string' => ''
            ),

            'ul' => array(
                'tag' => 'ul',
                'regex' => '/(^|\n)([ \t]*\*[^*#].*(\n|$)([ \t]*[^\s*#].*(\n|$))*([ \t]*[*#]{2}.*(\n|$))*)+/',
                'capture' => 0
            ),
            'ol' => array(
                'tag' => 'ol',
                'capture' => 0,
                'regex' => '/(^|\n)([ \t]*#[^*#].*(\n|$)([ \t]*[^\s*#].*(\n|$))*([ \t]*[*#]{2}.*(\n|$))*)+/'
            ),
            'li' => array(
                'tag' => 'li',
                'capture' => 0,
                'regex' => '/[ \t]*([*#]).+(\n[ \t]*[^*#\s].*)*(\n[ \t]*\1[*#].+)*/',
                'replace_regex' => '/(^|\n)[ \t]*[*#]/',
                'replace_string' => '$1'
            ),

            'table' => array(
                'tag' => 'table',
                'regex' => '/(^|\n)(\|.*?[ \t]*(\n|$))+/',
                'capture' => 0
            ),
            'tr' => array(
                'tag' => 'tr',
                'regex' => '/(^|\n)(\|.*?)\|?[ \t]*(\n|$)/',
                'capture' => 2
            ),
            'th' => array(
                'tag' => 'th',
                'regex' => '/\|+=([^|]*)/',
                'capture' => 1
            ),
            'td' => array(
                'tag' => 'td',
                'regex' => '/\|+([^|~\[{]*((~(.|(?=\n)|$)|' .
                       '\[\[' . $rx['link'] . '(\|' . $rx['link_text'] . ')?\]\]' .
                       '|' . $rx['image'] . '|[\[{])[^|~]*)*)/',
                'capture' => 1
            ),

            'single_line' => array(
                'regex' => '/.+/',
                'capture' => 0
            ),
            'text' => array(
                'regex' => '/(^|\n)([ \t]*[^\s].*(\n|$))+/',
                'capture' => 0
            ),
            'p' => array(
                'tag' => 'p',
                'regex' => '/(^|\n)([ \t]*\S.*(\n|$))+/',
                'capture' => 0
            ),

            'strong' => array(
                'tag' => 'strong',
                'regex' => '/\*\*([^*~]*((\*(?!\*)|~(.|(?=\n)|$))[^*~]*)*)(\*\*|\n|$)/',
                'capture' => 1
            ),
            'em' => array(
                'tag' => 'em',
                'regex' => '/\/\/(((?!' . $rx['uri_prefix'] . ')[^\/~])*' .
                       '((' . $rx['raw_uri'] . '|\/(?!\/)|~(.|(?=\n)|$))' .
                       '((?!' . $rx['uri_prefix'] . ')[^\/~])*)*)(\/\/|\n|$)/',
                'capture' => 1
            ),

            'img' => new image(array(
                'regex' => '/' . $rx['image'] . '/',
            )),

            'escaped_sequence' => array(
                'regex' => '/~(' . $rx['raw_uri'] . '|.)/',
                'capture' => 1,
                'tag' => 'span',
                'attrs' => array( 'class' => 'escaped' )
            ),
            'escaped_symbol' => array(
                'regex' => '/~(.)/',
                'capture' => 1,
                'tag' => 'span',
                'attrs' => array( 'class' => 'escaped' )
            ),

            'named_uri' => new named_uri(array(
                'regex' => '/\[\[(' . $rx['uri'] . ')\|(' . $rx['link_text'] . ')\]\]/'
            )),
            'unnamed_uri' => new unnamed_uri(array(
                'regex' => '/\[\[(' . $rx['uri'] . ')\]\]/'
            )),
            'named_link' => new named_link(array(
                'regex' => '/\[\[(' . $rx['link'] . ')\|(' . $rx['link_text'] . ')\]\]/'
            )),
            'unnamed_link' => new unnamed_link(array(
                'regex' => '/\[\[(' . $rx['link'] . ')\]\]/'
            )),
            'named_interwiki_link' => new named_interwiki_link(array(
                'regex' => '/\[\[(' . $rx['interwiki_link'] . ')\|(' . $rx['link_text'] . ')\]\]/'
            )),
            'unnamed_interwiki_link' => new unnamed_interwiki_link(array(
                'regex' => '/\[\[(' . $rx['interwiki_link'] . ')\]\]/'
            )),

            'raw_uri' => new unnamed_uri(array(
                'regex' => '/(' . $rx['raw_uri'] . ')/',
            )),

            'extension' => new extension(array(
                'regex' => '/' . $rx['ext'] . '/',
            ))
        );

        for ($i = 1; $i <= 6; $i++) {
            $g['h' . $i] = array(
                'tag' => 'h' . $i,
                'regex' => '/(^|\n)[ \t]*={' . $i . '}[ \t]' .
                       '([^~]*?(~(.|(?=\n)|$))*)[ \t]*=*\s*(\n|$)/',
                'capture' => 2
            );
        }

        $g['named_uri']->children = $g['unnamed_uri']->children = $g['raw_uri']->children =
                $g['named_link']->children = $g['unnamed_link']->children =
                $g['named_interwiki_link']->children = $g['unnamed_interwiki_link']->children =
            array(&$g['escaped_symbol'], &$g['img'], &$g['br']);

        $g['ul']['children'] = $g['ol']['children'] = array(&$g['li']);
        $g['li']['children'] = array(&$g['ul'], &$g['ol']);
        $g['li']['fallback'] = array('children' => array(&$g['text']));

        $g['table']['children'] = array(&$g['tr']);
        $g['tr']['children'] = array(&$g['th'], &$g['td']);
        $g['th']['children'] = $g['td']['children'] = array(&$g['single_line']);

        $g['h1']['children'] = $g['h2']['children'] = $g['h3']['children'] =
                $g['h4']['children'] = $g['h5']['children'] = $g['h6']['children'] =
                $g['single_line']['children'] = $g['text']['children'] = $g['p']['children'] =
                $g['strong']['children'] = $g['em']['children'] =
            array(
                &$g['escaped_sequence'], &$g['strong'], &$g['em'], &$g['br'], &$g['raw_uri'],
                &$g['named_uri'], &$g['named_interwiki_link'], &$g['named_link'],
                &$g['unnamed_uri'], &$g['unnamed_interwiki_link'], &$g['unnamed_link'],
                &$g['tt'], &$g['img']
            );

        $g['root'] = new rule(array(
            'children' => array(
                &$g['h1'], &$g['h2'], &$g['h3'], &$g['h4'], &$g['h5'], &$g['h6'],
                &$g['hr'], &$g['ul'], &$g['ol'], &$g['pre'], &$g['table'], &$g['extension']
            ),
            'fallback' => array('children' => array(&$g['p']))
        ));

        $this->grammar = $g;
    }

    /**
	* starts parsing a text
	* This method will ensure that the CSS class is unique and the "class" option is properly formatted.
	* @param string $data the data to be parsed.
	* @param array $options the options
	*/

    public function parse($data, $options = array()) {
        $node = new node();
        $data = preg_replace('/\r\n?/', "\n", $data);
        $options = array_merge($this->options, $options);
        $this->grammar['root']->apply($node, $data, $options);
        echo $node->as_string();exit;
    }


    /**
    * cleans the html chars
    * @param string $string to be cleaned
    */
    function mild_htmlspecialchars($string) {
        $subst = array(
            '"' => '&quot;',
            '&' => '&amp;',
            '<' => '&lt;',
            '>' => '&gt;',
        );
        return preg_replace('/(&(?:\w+|#x[0-9A-Fa-f]+|#\d+);|["&<>])/e',
            'isset(\$subst["$1"]) ? \$subst["$1"] : "$1"', $string);
    }
    
}
