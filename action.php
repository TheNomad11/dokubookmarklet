<?php
if(!defined('DOKU_INC')) die();

class action_plugin_clippings extends DokuWiki_Action_Plugin {

    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, 'handle_act');
        $controller->register_hook('HTML_EDITFORM_OUTPUT', 'BEFORE', $this, 'inject_text');
    }

    public function handle_act(Doku_Event $event, $param) {
        global $INPUT;

        if($event->data !== 'clipping') return;

        $event->preventDefault();
        $event->stopPropagation();

        $title = $INPUT->str('title','clipping');
        $url   = $INPUT->str('url','');
        $text  = $INPUT->str('text','');

        $id = 'clippings:'.preg_replace('/\W+/','_', $title);

        // store in session so we can inject into textarea
        $_SESSION['clipping_prefill'] = "Source: $url\n\n$text";

        // redirect to edit page
        send_redirect(wl($id, ['do'=>'edit']));
    }

    public function inject_text(Doku_Event $event, $param) {
        if(empty($_SESSION['clipping_prefill'])) return;

        $prefill = $_SESSION['clipping_prefill'];
        unset($_SESSION['clipping_prefill']); // only once

        $event->data = preg_replace_callback(
            '#<textarea([^>]*name="wikitext"[^>]*)>(.*?)</textarea>#is',
            function($m) use ($prefill){
                return '<textarea'.$m[1].'>'.htmlspecialchars($prefill,ENT_QUOTES,'UTF-8').'</textarea>';
            },
            $event->data,
            1
        );
    }
}
