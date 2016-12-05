<?php
/**
 * Page Maintainers
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Jakub Skokan <jakub.skokan@vpsfree.cz>
 */

class syntax_plugin_maintainers_maintainers extends DokuWiki_Syntax_Plugin {
    public function getType() {
        return 'formatting';
    }

    public function getSort() {
        return 32;
    }

    public function connectTo($mode) {
        $this->Lexer->addEntryPattern(
            '<maintainers.*?>(?=.*?</maintainers>)',
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
            return array($state, trim(substr($match, 12, -1)));

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
        global $ID;

        if ($mode === 'metadata') {
            list($state, $match) = $data;

            if ($state != DOKU_LEXER_UNMATCHED)
                return;

            $m = $this->loadHelper('maintainers');
            $m->setPageMaintainers($ID, $match);

            return;
        }

        if ($mode != 'xhtml')
            return false;

        list($state, $match) = $data;

        switch ($state) {
        case DOKU_LEXER_ENTER:
            $classes = array('maintainers');

            if ($match === 'hidden')
                $classes[] = 'hidden';

            $renderer->doc .= '<ul class="'.implode(' ', $classes).'">';
            break;

        case DOKU_LEXER_UNMATCHED:
            $s = '';

            foreach ($match as $maintainer) {
                list($name, $info) = $maintainer;

                $id = $this->getConf('user_ns').':'.$name;
                $exists = page_exists($id);
                $class = $exists ? '' : 'class="wikilink2"';

                $s .= '<li>';
                $s .= '<a href="'.wl($id).'"'.$class.' ';
                $s .= 'data-page-exists="'.($exists ? 1 : 0).'" ';
                $s .= 'data-page-id="'.$id.'">';
                $s .= $name.'</a>';
                $s .= ' '.$info;
                $s .= '</li>';
            }

            $renderer->doc .= $s;

            break;

        case DOKU_LEXER_EXIT:
            $renderer->doc .= '</ul>';
            break;
        }

        return true;
    }
}
