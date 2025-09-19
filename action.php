<?php
if(!defined('DOKU_INC')) die();

class action_plugin_clippings extends DokuWiki_Action_Plugin {

    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, 'handle_clip');
    }

    public function handle_clip(Doku_Event $event, $param) {
        global $INPUT;

        if ($event->data !== 'clip') return;

        // Stop normal processing
        $event->preventDefault();
        $event->stopPropagation();

        // Fetch parameters (works in older DokuWiki versions)
        $rawTitle = $INPUT->str('title', 'clipping_' . date('Ymd_His'));
        $text     = $INPUT->str('text', '');
        $url      = $INPUT->str('url', '');

        // Sanitize page ID
        $idSafe = preg_replace('/[^\p{L}\p{N}_\-]/u', '_', $rawTitle);
        $idSafe = trim($idSafe);
        if ($idSafe === '') $idSafe = 'clipping_' . date('Ymd_His');

        $pageId = 'clippings:' . $idSafe;

        // Ensure unique page
        $i = 1;
        $uniquePageId = $pageId;
        while (page_exists($uniquePageId)) {
            $uniquePageId = $pageId . '_' . $i;
            $i++;
        }
        $pageId = $uniquePageId;

        // Prepare content
        $now = date('Y-m-d H:i:s');
        $content = "====== $rawTitle ======\n\n";
        $content .= "Source: $url\n\n";
        $content .= "$text\n\n";
        $content .= "Clipped: $now\n";

        // Save automatically
        if (auth_quickaclcheck($pageId) >= AUTH_EDIT) {
            saveWikiText($pageId, $content, 'Clipped from web');
        }

        // Redirect to page view (do=show)
        header('Location: ' . wl($pageId, 'show'));
        exit;
    }
}
