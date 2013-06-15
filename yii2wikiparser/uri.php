<?php
 
 /**
 * This class is merely used to publish assets that are needed by all yiimetroui
 * widgets and thus have to be imported before any widget gets rendered.
 * @copyright Frenzel GmbH - www.frenzel.net
 * @link http://www.frenzel.net
 * @author Philipp Frenzel <philipp@frenzel.net>
 */

namespace yii2wikiparser;

class uri extends rule{
    
    public function __construct($params = array()) {
        parent::__construct($params);
    }

    public function build($node, $matches, $options = array()) {
        $link = new node('a');
        $link->set_attribute('href', rawurldecode($matches[1][0]));

        $this->apply($link, $matches[2][0], $options);
        $node->append($link);
    }

}
