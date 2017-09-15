<?php

namespace Hpkns\WordpressConfig;

use PhpParser\ParserFactory;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Parser;

class Reader
{
    /**
     * Initialize the config parser.
     */
    public function __construct(Parser $parser = null)
    {
        $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        die(get_class($this->parser));
    }

    /**
     * Return the value of every defines in a wp-config.php file.
     *
     * @param  string $config
     * @return array
     */
    public function parseConfig($config)
    {
        $dict = [];

        $defines = array_filter($this->parser->parse($config), function($node) {
            return $node instanceof FuncCall && $node->name == 'define';
        });

        foreach ($defines as $define) {
            $key = isset($define->args[0]) ? $define->args[0]->value->value : null;
            $value = isset($define->args[1]) ? $define->args[1]->value->value : null;

            if ($key !== null) {
                $dict[$key] = $value;
            }
        }

        return $dict;
    }

    /**
     * Static version of parseConfig()
     *
     * @param  string $config
     * @return array
     */
    static public function parse($config)
    {
        return (new static)->parseConfig($config);
    }
}
