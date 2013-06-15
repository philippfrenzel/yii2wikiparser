<?php
 
 /**
 * This class is merely used to publish assets that are needed by all yiimetroui
 * widgets and thus have to be imported before any widget gets rendered.
 * @copyright Frenzel GmbH - www.frenzel.net
 * @link http://www.frenzel.net
 * @author Philipp Frenzel <philipp@frenzel.net>
 */

namespace yii2wikiparser;

class image extends rule{
    
    public function __construct($params = array()) {
        parent::__construct($params);
    }

    public function build($node, $matches, $options = array()) {
        $img = new node('img');
        $img->set_attribute('src', $matches[1][0]);
        $alt = isset($matches[2]) ? $matches[2][0] : '';
        $img->set_attribute('alt', preg_replace('/~(.)/', '$1', $alt));

        $node->append($img);
    }

}
