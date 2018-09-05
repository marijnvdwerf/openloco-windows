const cheerio = require('cheerio');
const fs = require('fs');
const Mustache = require('mustache');
const http = require("http");

const port = 8080;

function processWindow(path) {
    const $ = cheerio.load(fs.readFileSync(path, 'utf-8'));

    $('dropdown').each((i, elem) => {
        console.log($.html(elem));
        elem = $(elem);

        let css = elem.attr('style');
        let colour = elem.data('colour');
        let widx = elem.data('widx');
        let tooltip = elem.data('tooltip');
        let content = elem.data('content');

        let newNode = $('<div>');
        newNode.attr('class', 'widget dropdown');
        newNode.attr('style', css);
        newNode.attr('data-colour', colour);
        newNode.attr('data-widx', widx);
        newNode.attr('data-type', 18);
        newNode.attr('data-tooltip', tooltip);
        newNode.attr('data-content', content);

        let btnNode = $('<div>');
        btnNode.attr('class', 'dropdown-button');
        btnNode.attr('data-colour', colour);
        btnNode.attr('data-widx', widx + 1);
        btnNode.attr('data-type', 11);
        btnNode.attr('data-tooltip', tooltip);
        btnNode.attr('data-content', 96);
        newNode.append(btnNode);

        elem.replaceWith(newNode);

        console.log($.html(newNode));
    });

    return $.html();
}

console.log('====');

http.createServer(function (request, response) {
    let template = fs.readFileSync(process.cwd() + '/index.html', 'utf-8');
    console.dir(process.cwd() + '/index.html');

    winHtml = '';
    winHtml += processWindow(__dirname + '/html/options-0.html');
    let output = Mustache.render(template, {windows: winHtml});

    response.writeHead(200, {"Content-Type": "text/html"});
    response.write(output);
    response.end();
}).listen(port);

console.log("Static file server running at\n  => http://localhost:" + port + "/\nCTRL + C to shutdown");
