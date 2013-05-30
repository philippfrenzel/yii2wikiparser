<?php
 
 /**
 * This class is merely used to publish assets that are needed by all yiimetroui
 * widgets and thus have to be imported before any widget gets rendered.
 * @copyright Frenzel GmbH - www.frenzel.net
 * @link http://www.frenzel.net
 * @author Philipp Frenzel <philipp@frenzel.net>
 */

namespace yii2wikiparser;

class rule extends creole{
    
    public $regex = false;
    public $capture = false;
    public $replace_regex = false;
    public $replace_string = false;
    public $tag = false;
    public $attrs = array();
    public $children = array();
    public $fallback = false;

    public function __construct($params = array()) {
        foreach ($params as $k => $v) {
            eval('$this->' . $k . ' = $v;');
        }
    }

    public function build($node, $matches, $options = array()) {
        if ($this->capture !== false) {
            $data = $matches[$this->capture][0];
        }

        if ($this->tag !== false) {
            $target = new node($this->tag);
            $node->append($target);
        }
        else {
            $target = $node;
        }

        if (isset($data)) {
            if ($this->replace_regex) {
                $data = preg_replace($this->replace_regex, $this->replace_string, $data);
            }
            $this->apply($target, $data, $options);
        }

        foreach ($this->attrs as $attr => $value) {
            $target->set_attribute($attr, $value);
        }
    }

    public function match($data) {
        return preg_match($this->regex, $data, $matches, PREG_OFFSET_CAPTURE)
            ? $matches : false;
    }

    public function apply($node, $data, $options = array()) {
        $tail = $data;

        if (!is_object($this->fallback)) {
            $this->fallback = $this->fallback
                ? new rule($this->fallback)
                : new default_fallback();
        }

        while (true) {
            $best = false;
            $rule = false;

            for ($i = 0; $i < count($this->children); $i++) {
                if (!isset($matches[$i])) {
                    if (!is_object($this->children[$i])) {
                        $this->children[$i] = new rule($this->children[$i]);
                    }
                    $matches[$i] = $this->children[$i]->match($tail);
                }

                if ($matches[$i] && (!$best || $matches[$i][0][1] < $best[0][1])) {
                    $best = $matches[$i];
                    $rule = $this->children[$i];
                    if ($best[0][1] == 0) {
                        break;
                    }
                }
            }

            $pos = $best ? $best[0][1] : strlen($tail);
            if ($pos > 0) {
                $this->fallback->apply($node, substr($tail, 0, $pos), $options);
            }

            if (!$best) {
                break;
            }

            if (!is_object($rule)) {
                $rule = new rule($rule);
            }
            $rule->build($node, $best, $options);

            $chopped = $best[0][1] + strlen($best[0][0]);
            $tail = substr($tail, $chopped);

            for ($i = 0; $i < count($this->children); $i++) {
                if (isset($matches[$i])) {
                    if ($matches[$i][0][1] >= $chopped) {
                        $matches[$i][0][1] -= $chopped;
                    }
                    else {
                        unset($matches[$i]);
                    }
                }
            }
        }
    }
}
