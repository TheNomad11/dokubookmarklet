<?php
if(!defined('DOKU_INC')) die();

class action_plugin_clippings extends DokuWiki_Action_Plugin {

    public function register(Doku_Event_Handler $controller) {
        // Hook before any action is executed
        $controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, 'handle_clipping');
    }

    public function handle_clipping(Doku_Event $event, $param) {
        global $INPUT;

        if ($event->data !== 'clip') return;

        $event->preventDefault();
        $event->stopPropagation();

        // fetch parameters
        $rawTitle = $INPUT->str('title', 'clipping_' . date('Ymd_His'));
        $text     = $INPUT->str('text', '');
        $url      = $INPUT->str('url', '');

        // sanitize title to make valid page ID
        $title = preg_replace('/[^\p{L}\p{N}_\-]/u', '_', $rawTitle);
        $title = trim($title);
        if ($title === '') $title = 'clipping_' . date('Ymd_His');

        $pageId = 'clippings:' . $title;

        // ensure unique page
        $i = 1;
        while (page_exists($pageId)) {
            $pageId = 'clippings:' . $title . '_' . $i;
            $i++;
        }

        // prepare content
        $now = date('Y-m-d H:i:s');
        $content = "Source: $url\n\n$text\n\nClipped: $now\n";

        // save the page
        if (auth_quickaclcheck($pageId) >= AUTH_EDIT) {
            $summary = 'Clipped from web';
            saveWikiText($pageId, $content, $summary);
        }

        // redirect to the newly created page
        send_redirect(wl($pageId));
        exit;
    }
}
