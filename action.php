<?php
/**
 * Page Maintainers
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Jakub Skokan <jakub.skokan@vpsfree.cz>
 */

if (!defined('DOKU_INC')) die();

class action_plugin_maintainers extends DokuWiki_Action_Plugin {
    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook('PARSER_CACHE_USE', 'BEFORE', $this, 'onCacheUse');
    }

    public function onCacheUse(Doku_Event $event) {
        $cache = $event->data;

        if ($cache->mode != 'xhtml' || !$cache->page)
            return;
        
        if (getNS($cache->page) !== $this->getConf('user_ns'))
            return;

        $helper = $this->loadHelper('maintainers');
        $pages = $helper->getMaintainersPages(noNS($cache->page));
        $meta = p_get_metadata($cache->page);

        // Purge cache if the list of maintainer's pages has changed
        if ($pages == $meta['maintained_pages'])
            return;

        $cache->depends['purge'] = true;

        $meta['maintained_pages'] = $pages;
        p_set_metadata($cache->page, $meta);
    }
}
