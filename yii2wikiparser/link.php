<?php
 
 /**
 * This class is merely used to publish assets that are needed by all yiimetroui
 * widgets and thus have to be imported before any widget gets rendered.
 * @copyright Frenzel GmbH - www.frenzel.net
 * @link http://www.frenzel.net
 * @author Philipp Frenzel <philipp@frenzel.net>
 */

namespace yii2wikiparser;

use node;

class link extends rule{
    
    public function __construct($params = array()) {
        parent::__construct($params);
    }

    public function format_link($link, $format) {
        if (function_exists($format)) {
            return call_user_func($format, $link);
        }
        return sprintf($format, rawurlencode($link));
    }

    public function build($node, $matches, $options = array()) {
        $link = preg_replace('/~(.)/', '$1', $matches[1][0]);

        if (isset($options['current_page']) && $options['current_page'] == $link) {
            $self_references = isset($options['self_references']) ? $options['self_references'] : 'allow';

            switch ($self_references) {
                case 'ignore':
                    $this->apply($node, $matches[2][0], $options);
                    return;

                case 'emphasize':
                    $child = new creole_node('strong');
                    break;
            }
        }

        if (!isset($child)) {
            $child = new node('a');
            $child->set_attribute(
                'href',
                isset($options['link_format']) ? $this->format_link($link, $options['link_format']) : $link
            );
        }

        $this->apply($child, $matches[2][0], $options);
        $node->append($child);
    }

}
