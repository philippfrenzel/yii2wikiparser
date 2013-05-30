<?php
 
 /**
 * This class is merely used to publish assets that are needed by all yiimetroui
 * widgets and thus have to be imported before any widget gets rendered.
 * @copyright Frenzel GmbH - www.frenzel.net
 * @link http://www.frenzel.net
 * @author Philipp Frenzel <philipp@frenzel.net>
 */

namespace yii2wikiparser;

class extension extends rule{
    
    public function __construct __construct($params = array()) {
        parent::__construct($params);
    }

    public function build($node, $matches, $options = array()) {
        if (isset($options['extension']) && is_callable($options['extension'])) {
            call_user_func($options['extension'], $node, $matches[1][0]);
        }
        else {
            $node->append($matches[0][0]);
        }
    }
    
}
