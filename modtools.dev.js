// INTENDED FOR fantasy-writers.org AS OF 2016-10-08 rev 2025.17
// jQuery is old, we load a known version
window.jQuery = window.$ = undefined;

var jqscript = document.createElement('script');
jqscript.type = "text/javascript";
jqscript.src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js";
document.getElementsByTagName('head')[0].appendChild(jqscript);

var content_sel = ".node.node-type-book .content";

function extractNormalizedText(el) {
    const clone = el.cloneNode(true);
    const text = clone.textContent || clone.innerText || "";
    return text.replace(/\s+/g, " ").trim();
}

function highlightMatches(text, wordList, cssClass, tooltip) {
    const re = new RegExp("\\b(" + wordList.join("|") + ")\\b", "gi");
    return text.replace(re, (match) => `<span class="bad ${cssClass}" title="${tooltip}">${match}</span>`);
}

function cleanAndHighlightContent() {
    const rawContent = $(content_sel);
    const clone = rawContent.clone();

    // Remove unwanted footer sections
    clone.find(".fivestar-static-form-item, .book-navigation, #notification-ui-options-form-0").remove();

    // Remove all tags except basic structure
    clone.find("*":not(p):not(div)).each(function() {
        $(this).replaceWith($(this).text());
    }));

    let html = clone.html();

    // Highlight curse words
    const curseWords = ["shit[\\w]*", "fuck[\\w]*", "motherfuck[\\w]*", "cunt", "slut", "dick[\\w]*", "nigger", "piss", "cock[\\w]*", "spic", "prick", "bastard", "bitch[\\w]*", "ass(?:hole|clown|face|es)?", "twat", "vagina"];
    html = highlightMatches(html, curseWords, "curse_word", "Word is potentially vulgar");

    // Highlight adult themes
    const adultWords = ["(?:gang)?rape(?:d|s)?", "gor(?:e|y)", "naked", "nude", "cum", "jizz", "torture", "stripped", "penis", "breasts?", "tits?", "orgasm", "ejaculate(?:d|s)?", "orgy"];
    html = highlightMatches(html, adultWords, "adult_theme", "Word might suggest adult themes");

    return html;
}

function do_moderate() {
    var summary = $("#modtools");
    summary.html("<h3 style='margin:0'>Mod Tools Analysis:</h3>");

    const contentEl = $(content_sel)[0];
    const normalizedText = extractNormalizedText(contentEl);

    const spoiled_words =
        ($(".fivestar-static-form-item").text().split(' ').length || 0) +
        ($(".book-navigation").text().split(' ').length || 0) +
        ($(content_sel + " form").text().split(' ').length || 0);
    const words = normalizedText.split(/\s+/).filter(w => w.trim() !== "").length - spoiled_words;
    const word_count = new Intl.NumberFormat('en-US').format(words);
    summary.append(`<p class='${words > 7000 ? "bad" : ""}'>${word_count} words.</p>`);

    const img_count = $(content_sel + " img").length;
    $(content_sel + " img").addClass("bad");
    summary.append(`<p class='${img_count > 0 ? "bad" : ""}'>${img_count} images.</p>`);

    const spoiled_url = $(".fivestar-static-form-item a, .book-navigation a, " + content_sel + " form a").length;
    const url_count = $(content_sel + " a").length - spoiled_url;
    $(content_sel + " a").addClass("bad url");
    summary.append(`<p class='${url_count > 0 ? "bad url" : ""}'>${url_count} links.</p>`);

    const curseMatches = (normalizedText.match(/\b(?:shit[\w]*|fuck[\w]*|motherfuck[\w]*|cunt|slut|dick[\w]*|nigger|piss|cock[\w]*|spic|prick|bastard|bitch[\w]*|ass(?:hole|clown|face|es)?|twat|vagina)\b/gi) || []).length;
    summary.append(`<p class='${curseMatches > 0 ? "bad curse_word" : ""}'>${curseMatches} bad words.</p>`);

    const adultMatches = (normalizedText.match(/\b(?:gang)?rape(?:d|s)?|gor(?:e|y)|naked|nude|cum|jizz|torture|stripped|penis|breasts?|tits?|orgasm|ejaculate(?:d|s)?|orgy\b/gi) || []).length;
    summary.append(`<p class='${adultMatches > 0 ? "bad adult_theme" : ""}'>${adultMatches} potential adult themes.</p>`);

    const llmButtonHtml = words > 8000 ?
        `<button id="llmAuditBtn" type="button" disabled title="Site limit is 8000 words">(Too long for LLM)</button>` :
        `<button id="llmAuditBtn" type="button" onclick="do_llm_audit()">LLM Audit</button>`;

    summary.append(`
<div id="modtools-buttons" style="float:none;">
  <button style="display:none;" data-comment="This is just for me for testing" type="button" onclick="do_moderate()">Refresh</button>
  <button type="button" onclick="do_highlight()">Toggle Highlight</button>
  <button onclick="goToBad();" type="button">&gt;</button>
  ${llmButtonHtml}
</div>
<div id="llmResult" style="margin-top:1em;"></div>`);

    // Replace live content
    const visibleContent = cleanAndHighlightContent();
    const footerBlocks = $(content_sel).find(".fivestar-static-form-item, .book-navigation, #notification-ui-options-form-0").detach();
    $(content_sel).html(visibleContent);
    $(content_sel).append('<div class="modtools-footer"></div>');
    $(".modtools-footer").append(footerBlocks);
}

function goToBad() {
    if ($(content_sel + " .bad").length > 0) {
        $(content_sel).addClass("highlight_problems");
        $("html,body").scrollTop($(content_sel + " .bad").first().offset().top);
    }
}

function do_highlight() {
    $(content_sel).toggleClass("highlight_problems");
}

var mtcss = `.highlight_problems.content > *{color:#aaa;} 
.highlight_problems .bad, #modtools .bad {color:red;background-color:#fcc;font-weight:bold;}
.highlight_problems .bad.adult_theme,#modtools .bad.adult_theme {color:darkred;background-color:#daa;}
.highlight_problems .bad.url, #modtools .bad.url {color:darkgreen;background-color:#afa;}
.highlight_problems img.bad {border:1px solid red; background:#fcc; padding:10px;}
#modtools {border:1px solid #aaf; background-color:#eaeaff; margin: 0 0 1em; padding: 1em 0 1em 1em;} 
#modtools p {float:left; margin-right:5px;}
.highlight_problems .bad {border:1px solid red;padding:2px 4px;}
#llmAuditBtn{margin-left: 1em;}
.modtools-footer {margin-top: 2em; padding-top: 1em; border-top: 1px dashed #ccc;}`;

var mthead = document.getElementsByTagName('head')[0],
    mtstyle = document.createElement('style');
mtstyle.type = 'text/css';
if (mtstyle.styleSheet) mtstyle.styleSheet.cssText = mtcss;
else mtstyle.appendChild(document.createTextNode(mtcss));
mthead.appendChild(mtstyle);

function do_init() {
    $(".submitted").first().parent().prepend("<div id='modtools'></div>");
    do_moderate();
}

function wait_for_init(i) {
    if (i == 0) {
        console.log("Oops, jQuery never loaded.");
        return;
    }
    if (typeof jQuery == 'undefined') setTimeout(() => wait_for_init(i - 1), 100);
    else do_init();
}

async function do_llm_audit() {
    $("#llmAuditBtn").remove();
    $("#llmResult").html("<div><em>Waking up the bots...</em></div>");

    const contentText = extractNormalizedText($(content_sel)[0]).slice(0, 8000);

    try {
        const res = await fetch("https://zmunk.com/fwo_modcheck.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ content: contentText })
        });

        if (!res.ok) throw new Error("LLM check failed");

        const data = await res.json();
        const gptMessage = data.choices?.[0]?.message?.content ?? "No response from GPT.";

        $("#llmResult").html("<div><h4>LLM Audit:</h4><p style='float:none;'>" + gptMessage.replace(/\n/g, "<br>") + "</p></div>");
    } catch (e) {
        $("#llmResult").html("<div style='color:red;'>Sorry, the bots failed :-(</div>");
    }
}

setTimeout(() => wait_for_init(20), 100);
