<?php

use Symfony\Component\Finder\Finder;

require 'vendor/autoload.php';

$finder = new Finder();
$finder->files()->in(__DIR__ . '/html')->name('*.html');

?>
<!DOCTYPE html>
<html>
<body>

<style>
    * {
        box-sizing: border-box;
    }

    .widget {
        border: 1px solid red;
        position: absolute;
    }

    .window {
        position: relative;
    }
</style>

<?php

function process(DOMNode $node)
{
    if ($node instanceof DOMElement) {
        if ($node->tagName == 'dropdown') {
            $document = $node->ownerDocument;
            $parent = $node->parentNode;

            $xml = $document->saveXML($node);
            $new = $document->createElement('div');
            $new->setAttribute('class', 'widget dropdown');
            $colour = $node->getAttribute('data-colour');
            $widx = $node->getAttribute('data-widx');
            $tooltip = $node->getAttribute('data-tooltip');
            $content = $node->getAttribute('data-content');
            $css = $node->getAttribute('style');
            $new->setAttribute('class', 'widget dropdown');
            $new->setAttribute('style', $css);
            $new->setAttribute('data-colour', $colour);
            $new->setAttribute('data-widx', $widx);
            $new->setAttribute('data-type', 18);
            $new->setAttribute('data-tooltip', $tooltip);
            $new->setAttribute('data-content', $content);

            $btn = $document->createElement('div');
            $btn->setAttribute('class', 'dropdown-button');
            $btn->setAttribute('data-colour', $colour);
            $btn->setAttribute('data-type', 11);
            $btn->setAttribute('data-widx', $widx + 1);
            $btn->setAttribute('data-tooltip', $tooltip);
            $btn->setAttribute('data-content', 96);

            $new->appendChild($btn);
            $parent->replaceChild($new, $node);
            $parent->insertBefore($document->createComment($xml), $new);
            return;
        }
    }

    if (!$node->hasChildNodes())
        return;

    for ($i = 0; $i < $node->childNodes->length; $i++) {
        process($node->childNodes->item($i));
    }
}

foreach ($finder as $file) {
    $xml = new DOMDocument();
    $xml->loadHTML(file_get_contents($file));

    process($xml);

    echo $xml->saveHTML($xml->getElementsByTagName('body')->item(0));
}

?>

<script>

    function getOffset(el, relativeTo) {
        let x = 0;
        let y = 0;

        while (true) {
            x += el.offsetLeft;
            x += el.offsetParent.clientLeft;
            y += el.offsetTop;
            y += el.offsetParent.clientTop;
            el = el.offsetParent;
            if (el === relativeTo)
                break;
        }

        return {x, y};
    }

    function dump(root) {
        let widgets = root.querySelectorAll('*[data-widx]');

        var json = [];
        json[63] = null;
        json.fill(null, 0, 64);
        widgets.forEach((w) => {
            var el = {};

            el.left = getOffset(w, root).x;
            el.top = getOffset(w, root).y;
            el.right = el.left + w.offsetWidth - 1;
            el.bottom = el.top + w.offsetHeight - 1;
            el.type = parseInt(w.dataset['type']);
            el.content = parseInt(w.dataset['content']);
            el.tooltip = parseInt(w.dataset['tooltip']);
            el.colour = parseInt(w.dataset['colour']);

            json.splice(parseInt(w.dataset['widx'], 10), 1, el);
        });
        json = json.filter(item => item !== null);

        return json;
    }

    let windows = document.querySelectorAll('.window');
    windows.forEach((window) => {
        dump(window);
    });
</script>
</body>
</html>
