<?php
 
 /**
 * This class is merely used to publish assets that are needed by all yiimetroui
 * widgets and thus have to be imported before any widget gets rendered.
 * @copyright Frenzel GmbH - www.frenzel.net
 * @link http://www.frenzel.net
 * @author Philipp Frenzel <philipp@frenzel.net>
 */

namespace yii2wikiparser;

class interwikilink extends rule{
    
    public function __construct($params = array()) {
        parent::__construct($params);
    }

    public function build($node, $matches, $options = array()) {
        if (isset($options['interwiki'])) {
            preg_match('/(.*?):(.*)/', $matches[1][0], $m);
        }

        if (!isset($m[1]) || !isset($options['interwiki'][$m[1]])) {
            return parent::build($node, $matches, $options);
        }

        $format = $options['interwiki'][$m[1]];

        $link = new node('a');
        $link->set_attribute('href', $this->format_link(preg_replace('/~(.)/', '$1', $m[2]), $format));

        $this->apply($link, $matches[2][0], $options);
        $node->append($link);
    }

}
