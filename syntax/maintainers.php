<?php

class syntax_plugin_maintainers_maintainers extends DokuWiki_Syntax_Plugin {
    public function getType() {
        return 'formatting';
    }

    public function getSort() {
        return 32;
    }

    public function connectTo($mode) {
        $this->Lexer->addEntryPattern(
            '<maintainers>(?=.*?</maintainers>)',
            $mode,
            'plugin_maintainers_maintainers'
        );
    }

    public function postConnect() {
        $this->Lexer->addExitPattern('</maintainers>', 'plugin_maintainers_maintainers');
    }
 
    public function handle($match, $state, $pos, Doku_Handler $handler) {
        switch ($state) {
        case DOKU_LEXER_ENTER:
            return array($state, null);

        case DOKU_LEXER_UNMATCHED:
            $lines = preg_split('/\r\n|\n|\r/', $match);
            $ret = array();

            foreach ($lines as $line) {
                $trimmed = trim($line);

                if (!$trimmed)
                    continue;

                $m = array();

                if ($i = strpos($trimmed, ' ')) {
                    $m[] = substr($trimmed, 0, $i);
                    $m[] = substr($trimmed, $i);

                } else {
                    $m[] = $trimmed;
                    $m[] = '';
                }

                $ret[] = $m;
            }

            return array($state, $ret);

        case DOKU_LEXER_EXIT:
            return array($state, null);
        }

        return null;
    }
 
    public function render($mode, Doku_Renderer $renderer, $data) {
        if ($mode != 'xhtml' || $mode === 'metadata')
            return false;

        list($state, $match) = $data;

        switch ($state) {
        case DOKU_LEXER_ENTER:
            $renderer->doc .= '<ul class="maintainers">';
            break;

        case DOKU_LEXER_UNMATCHED:
            foreach ($match as $maintainer) {
                list($name, $info) = $maintainer;

                $id = $this->getConf('user_ns').':'.$name;
                $class = page_exists($id) ? '' : 'class="wikilink2"';

                $renderer->doc .= '<li>';
                $renderer->doc .= '<a href="'.wl($id).'"'.$class.'>'.$name.'</a>';
                $renderer->doc .= ' '.$info;
                $renderer->doc .= '</li>';
            }

            break;

        case DOKU_LEXER_EXIT:
            $renderer->doc .= '</ul>';
            break;
        }

        return true;
    }
}
