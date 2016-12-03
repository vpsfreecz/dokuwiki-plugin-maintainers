<?php

class syntax_plugin_maintainers_maintainer extends DokuWiki_Syntax_Plugin {
    public function getType() {
        return 'formatting';
    }

    public function getSort() {
        return 32;
    }

    public function getAllowedTypes() {
        return array('disabled');
    }

    public function connectTo($mode) {
        $this->Lexer->addEntryPattern(
            '<maintainer name=".+?">(?=.*?</maintainer>)',
            $mode,
            'plugin_maintainers_maintainer'
        );

        $this->Lexer->addPattern('irc .*?\n', 'plugin_maintainers_maintainer');
        $this->Lexer->addPattern('mail .*?\n', 'plugin_maintainers_maintainer');
    }

    public function postConnect() {
        $this->Lexer->addExitPattern('</maintainer>', 'plugin_maintainers_maintainer');
    }
 
    public function handle($match, $state, $pos, Doku_Handler $handler) {
        switch ($state) {
        case DOKU_LEXER_ENTER:
            preg_match('/name="([^"]+)"/', $match, $matches);

            return array($state, $matches[1]);

        case DOKU_LEXER_MATCHED:
            return array($state, $match);

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
            $renderer->doc .= '<div class="maintainer">';
            $renderer->doc .= '<h1>'.$match.'</h1>';
            $renderer->doc .= '<table>';
            break;

        case DOKU_LEXER_MATCHED:
            $trimmed = trim($match);
            $i = strpos($trimmed, ' ');
            $k = substr($trimmed, 0, $i);
            $v = substr($trimmed, $i);

            switch ($k) {
            default:
                $renderer->doc .= '<tr class="'.$k.'"><th>'.$k.':</th><td>'.$v.'</td></tr>';
            }

            break;

        case DOKU_LEXER_EXIT:
            $renderer->doc .= '</table></div>';
            break;
        }

        return true;
    }
}
