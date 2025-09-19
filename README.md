(made by Chatgpt)

# DokuWiki Clippings Plugin

Capture text from any website and save it automatically into your DokuWiki as a new page in the `clippings:` namespace.

## Features

- Save selected text from any website with a simple **bookmarklet**.  
- Automatically adds:  
  - Page title as header  
  - Source URL  
  - Timestamp  
- Fully compatible with the **latest DokuWiki**.  
- No buttons, alerts, or manual edits in DokuWiki required.  
- Redirects automatically to the saved page in **view mode**.  

## Bookmarklet

1. Create a bookmark in your browser with this code:

```javascript
javascript:(function(){
  var s = window.getSelection().toString();
  if(!s) return;
  var u = location.href;
  var t = document.title;
  var wiki = 'https://yourwiki.example.com/doku.php';
  var url = wiki + '?do=clip'
    + '&title=' + encodeURIComponent(t)
    + '&url=' + encodeURIComponent(u)
    + '&text=' + encodeURIComponent(s);
  window.location.href = url;
})();
```

2. Select text on any webpage and click the bookmarklet.
3. A new page is created in `clippings:` and you are redirected to **view mode**.

---

## Notes

* Each clipping creates a **new page** with a sanitized title.
* Optionally, you can modify the plugin to **append all clippings to a single page**.
* No JavaScript buttons are used in DokuWiki itself — everything is handled automatically.

---
