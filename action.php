<?php
if(!defined('DOKU_INC')) die();

class action_plugin_clippings extends DokuWiki_Action_Plugin {

    public function register(Doku_Event_Handler $controller) {
        // intercept our custom action
        $controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, 'handle_clip');
    }

    public function handle_clip(Doku_Event $event, $param) {
        global $INPUT;

        if ($event->data !== 'clip') return;

        // prevent DokuWiki from doing anything else
        $event->preventDefault();
        $event->stopPropagation();

        // fetch parameters
        $rawTitle = $INPUT->str('title', 'clipping_' . date('Ymd_His'));
        $text     = $INPUT->str('text', '');
        $url      = $INPUT->str('url', '');

        // sanitize page ID
        $idSafe = preg_replace('/[^\p{L}\p{N}_\-]/u', '_', $rawTitle);
        $idSafe = trim($idSafe);
        if ($idSafe === '') $idSafe = 'clipping_' . date('Ymd_His');

        $pageId = 'clippings:' . $idSafe;

        // make unique if page exists
        $i = 1;
        $uniquePageId = $pageId;
        while (page_exists($uniquePageId)) {
            $uniquePageId = $pageId . '_' . $i;
            $i++;
        }
        $pageId = $uniquePageId;

        // prepare content
        $now = date('Y-m-d H:i:s');
        $content = "====== $rawTitle ======\n\n";   // page title as header
        $content .= "Source: $url\n\n";
        $content .= "$text\n\n";
        $content .= "Clipped: $now\n";

        // save automatically
        if (auth_quickaclcheck($pageId) >= AUTH_EDIT) {
            saveWikiText($pageId, $content, 'Clipped from web');
        }

        // redirect to the new page view and stop execution
        header('Location: ' . wl($pageId));
        exit;
    }
}
