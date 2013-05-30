<?php
 
 /**
 * This class is merely used to publish assets that are needed by all yiimetroui
 * widgets and thus have to be imported before any widget gets rendered.
 * @copyright Frenzel GmbH - www.frenzel.net
 * @link http://www.frenzel.net
 * @author Philipp Frenzel <philipp@frenzel.net>
 */

namespace yii2wikiparser;

class node extends yii2wikiparser{
    
    public $tag = NULL;
    public $attrs = NULL;
    public $content = array();

    public function __construct($tag = false) {
        $this->tag = $tag;
    }

    public function append($node) {
        $this->content[] = $node;
    }

    public function set_attribute($attr, $value) {
        $this->attrs[$attr] = $value;
    }

    public function as_string() {
        $result = '';
        foreach ($this->content as $item) {
            $result .= is_object($item) ? $item->as_string() : self::mild_htmlspecialchars($item);
        }

        if (!empty($this->tag)) {
            $tag = $this->tag;

            $attrs = '';
            if (!empty($this->attrs)) {
                foreach ($this->attrs as $attr => $value) {
                    $attrs .= ' ' . $attr . '="' . self::mild_htmlspecialchars($value) . '"';
                }
            }

            $result = empty($result) ? "<$tag$attrs/>" : "<$tag$attrs>$result</$tag>";
        }

        return $result;
    }
}
