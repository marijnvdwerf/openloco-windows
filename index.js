const puppeteer = require('puppeteer');
const fs = require('fs');

(async () => {
    const browser = await puppeteer.launch();
    const page = await browser.newPage();
    await page.goto('http://localhost:8085');

    // Get the "viewport" of the page, as reported by the page.
    const dimensions = await page.evaluate(() => {
        var json = {};
        let langs = ['en-US', 'de-DE', 'en-GB', 'es-ES', 'fr-FR', 'it-IT', 'ko-KR', 'zh-TW'];
        let windows = document.querySelectorAll('.window');

        langs.forEach((lang) => {
            document.body.lang = lang;
            json[lang] = {};

            windows.forEach((el, i) => {
                json[lang][el.dataset['file']] = dump(el);
            });
        });
        return json;
    });

    if (!fs.existsSync('out-2')) {
        fs.mkdirSync('out-2');
    }

    Object.entries(dimensions).forEach(([lang, items]) => {
        if (!fs.existsSync('out-2/' + lang)) {
            fs.mkdirSync('out-2/' + lang);
        }
        Object.entries(items).forEach(([window, widgets]) => {
            fs.writeFile('out-2/' + lang + '/' + window + '.json', JSON.stringify(widgets), {}, function (a, b) {
                // console.error(a, b);
            });
        });
    });

    await browser.close();
})();