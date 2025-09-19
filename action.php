<?php
// lib/plugins/clippings/action.php
if(!defined('DOKU_INC')) die();

class action_plugin_clippings extends DokuWiki_Action_Plugin {

    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, 'handle_act');
    }

    public function handle_act(Doku_Event $event, $param) {
        if ($event->data != 'clipping') return;

        global $INPUT;

        $title = cleanID('clippings:' . $INPUT->str('title', 'clipping'));
        $url   = $INPUT->str('url', '');
        $text  = $INPUT->str('text', '');

        // Build clipping text
        $content  = "Source: $url\n\n$text";

        // Switch into edit mode with prefilled content
        $event->data = 'edit';
        $_REQUEST['id'] = $title;
        $_POST['wikitext'] = $content;
        $_POST['prefix']   = '';
        $_POST['suffix']   = '';
        $_POST['summary']  = 'Clipped from web';
        $_REQUEST['rev']   = '';
    }
}
