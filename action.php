<?php
if (!defined('DOKU_INC')) die();

class action_plugin_clippings extends DokuWiki_Action_Plugin {
    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook('AJAX_CALL_UNKNOWN', 'BEFORE', $this, 'handle_ajax_call');
    }

    public function handle_ajax_call(Doku_Event &$event, $param) {
        if ($event->data !== 'clippings_save') return;
        $event->preventDefault();
        $event->stopPropagation();

        global $INPUT;
        global $ID;
        global $INFO;
        global $conf;

        // Get the selected text and URL from the AJAX request
        $text = $INPUT->post->str('text');
        $url = $INPUT->post->str('url');
        $title = $INPUT->post->str('title');

        if (empty($text) || empty($url)) {
            http_status(400);
            echo json_encode(['success' => false, 'message' => 'Text and URL are required.']);
            return;
        }

        // Create a unique page ID for the clipping
        $pageId = 'clippings:' . str_replace(':', '_', $title);

        // Prepare the page content
        $content = "===== Clipping =====\n\n$text\n\n----\n\nSource: $url";

        // Save the page
        saveWikiText($pageId, $content, 'Clipping saved from ' . $url);

        http_status(200);
        echo json_encode(['success' => true, 'pageId' => $pageId]);
    }
}
