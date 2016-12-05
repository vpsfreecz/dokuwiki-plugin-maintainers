<?php
/**
 * Page Maintainers
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Jakub Skokan <jakub.skokan@vpsfree.cz>
 */

if (!defined('DOKU_INC')) die();

class helper_plugin_maintainers extends DokuWiki_Plugin {
    public function setPageMaintainers($page, $maintainers) {
        $db = $this->getDb();

        if (!$db)
            return;

        $db->query('DELETE FROM page_maintainers WHERE page_id = ?', $page);

        foreach ($maintainers as $maintainer) {
            $db->query(
                'INSERT INTO page_maintainers (page_id, maintainer) VALUES (?, ?)',
                $page, $maintainer[0]
            );
        }
    }

    public function getMaintainersPages($nick) {
        $db = $this->getDb();

        if (!$db)
            return;

        $res = $db->query(
            'SELECT page_id FROM page_maintainers WHERE maintainer = ?
             ORDER BY page_id ASC',
            $nick
        );
        $ret = array();

        while($row = $db->res_fetch_assoc($res)) {
            $ret[] = $row['page_id'];
        }

        return $ret;
    }

    protected function getDb() {
        $sqlite = $this->loadHelper('sqlite');
        
        if (!$sqlite){
            msg('This plugin requires the sqlite plugin. Please install it', -1);
            return;
        }

        if (!$sqlite->init('maintainers', DOKU_PLUGIN.'maintainers/db/'))
            return;
        
        return $sqlite;
    }
}
