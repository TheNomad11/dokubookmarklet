<?php
if(!defined('DOKU_INC')) die();

class action_plugin_clippings extends DokuWiki_Action_Plugin {

    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, 'handle_clip');
    }

    public function handle_clip(Doku_Event $event, $param) {
        global $INPUT;

        if ($event->data !== 'clip') return;

        $event->preventDefault();
        $event->stopPropagation();

        // fetch parameters via GET (safe for latest DokuWiki)
        $rawTitle = $INPUT->str('title', 'clipping_' . date('Ymd_His'));
        $text     = $INPUT->str('text', '');
        $url      = $INPUT->str('url', '');

        // sanitize page ID
        $idSafe = preg_replace('/[^\p{L}\p{N}_\-]/u', '_', $rawTitle);
        $idSafe = trim($idSafe);
        if ($idSafe === '') $idSafe = 'clipping_' . date('Ymd_His');

        $pageId = 'clippings:' . $idSafe;

        // ensure unique page
        $i = 1;
        $uniquePageId = $pageId;
        while (page_exists($uniquePageId)) {
            $uniquePageId = $pageId . '_' . $i;
            $i++;
        }
        $pageId = $uniquePageId;

        // prepare content
        $now = date('Y-m-d H:i:s');
        $content = "====== $rawTitle ======\n\n";
        $content .= "Source: $url\n\n";
        $content .= "$text\n\n";
        $content .= "Clipped: $now\n";

        // save automatically
        if (auth_quickaclcheck($pageId) >= AUTH_EDIT) {
            saveWikiText($pageId, $content, 'Clipped from web');
        }

        // redirect to a normal view page
        header('Location: ' . wl($pageId, 'show'));
        exit;
    }
}
