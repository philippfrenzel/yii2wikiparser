<?php
 
 /**
 * This class is merely used to publish assets that are needed by all yiimetroui
 * widgets and thus have to be imported before any widget gets rendered.
 * @copyright Frenzel GmbH - www.frenzel.net
 * @link http://www.frenzel.net
 * @author Philipp Frenzel <philipp@frenzel.net>
 */

namespace yii2wikiparser;

class unnameduri extends uri{
    
    public function __construct($params = array()) {
        parent::__construct($params);
    }

    public function build($node, $matches, $options = array()) {
        return parent::build($node, array($matches[0], $matches[1], $matches[1]), $options);
    }

}
