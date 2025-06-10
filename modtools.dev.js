// INTENDED FOR fantasy-writers.org AS OF 2016-10-08 rev 2025.21
// jQuery is old, we load a known version
window.jQuery = window.$ = undefined;

var jqscript = document.createElement('script');
jqscript.type = "text/javascript";
jqscript.src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js";
document.getElementsByTagName('head')[0].appendChild(jqscript);

var content_sel = "#content-modcleaned";

const curseWords = ["shit\\w*", "fuck\\w*", "motherfuck\\w*", "cunt", "slut", "dick\\w*", "nigger", "piss", "cock\\w*", "spic", "prick", "bastard", "bitch\\w*", "ass(?:hole|clown|face|es)?", "twat", "vagina"];
const adultWords = ["(?:gang)?rape(?:d|s)?", "gor(?:e|y)", "naked", "nude", "cum", "jizz", "torture", "stripped", "penis", "breasts?", "tits?", "orgasm", "ejaculate(?:d|s)?", "orgy"];

function extractNormalizedText(el) {
    const clone = el.cloneNode(true);
    const text = clone.textContent || clone.innerText || "";
    return text.replace(/\s+/g, " ").trim();
}

function highlightMatches(text, wordList, cssClass, tooltip) {
    const re = new RegExp("(?<!\\w)(" + wordList.join("|") + ")(?!\\w)", "gi");
    return text.replace(re, (match) => `<span class="bad ${cssClass}" title="${tooltip}">${match}</span>`);
}

function countMatches(text, wordList) {
    const re = new RegExp("(?<!\\w)(" + wordList.join("|") + ")(?!\\w)", "gi");
    return (text.match(re) || []).length;
}

function cleanContent() {
    const rawContent = $(".node.node-type-book .content");
    const clone = rawContent.clone();

    const keepers = clone.find(".fivestar-static-form-item, .book-navigation, #notification-ui-options-form-0").detach();

    clone.find('*').not('p, div').each(function () {
        $(this).replaceWith($(this).text());
    });
    //clone.find('div').each(function () {
    //    const $p = $('<p></p>').html($(this).text());
    //    $(this).replaceWith($p);
    //});

    let html = clone.html();

    $(".node.node-type-book .content").html(`<div id='content-modcleaned'>${html}</div>`).append(keepers);
}

function highlightContent() {
    html = $("#content-modcleaned").html();
    html = highlightMatches(html, curseWords, "curse_word", "Word is potentially vulgar");
    html = highlightMatches(html, adultWords, "adult_theme", "Word might suggest adult themes");
    $("#content-modcleaned").html(html);
}

function do_moderate() {
    var summary = $("#modtools");
    summary.html("<h3 style='margin:0'>Mod Tools Analysis:</h3>");

    cleanContent();
    
    const contentEl = $("#content-modcleaned")[0];
    const normalizedText = extractNormalizedText(contentEl);

    const words = normalizedText.split(/\s+/).filter(w => w.trim() !== "").length;
    const word_count = new Intl.NumberFormat('en-US').format(words);
    summary.append(`<p class='${words > 7000 ? "bad" : ""}'>${word_count} words.</p>`);

    const img_count = $("#content-modcleaned img").length;
    $("#content-modcleaned img").addClass("bad");
    summary.append(`<p class='${img_count > 0 ? "bad" : ""}'>${img_count} images.</p>`);

    const url_count = $("#content-modcleaned a").length;
    $("#content-modcleaned a").addClass("bad url");
    summary.append(`<p class='${url_count > 0 ? "bad url" : ""}'>${url_count} links.</p>`);

    const curseMatches = countMatches(normalizedText, curseWords);
    summary.append(`<p class='${curseMatches > 0 ? "bad curse_word" : ""}'>${curseMatches} bad words.</p>`);

    const adultMatches = countMatches(normalizedText, adultWords);
    summary.append(`<p class='${adultMatches > 0 ? "bad adult_theme" : ""}'>${adultMatches} potential adult themes.</p>`);

    const llmButtonHtml = words > 8000 ?
        `<button id="llmAuditBtn" type="button" disabled title="Site limit is 8000 words">(Too long for LLM)</button>` :
        `<button id="llmAuditBtn" type="button" onclick="do_llm_audit()">LLM Audit</button>`;

    summary.append(`
<div id="modtools-buttons" style="float:none;">
  <button style="display:none;" type="button" onclick="do_moderate()">Refresh</button>
  <button type="button" onclick="do_highlight()">Toggle Highlight</button>
  <button onclick="goToBad();" type="button">&gt;</button>
  ${llmButtonHtml}
</div>
<div id="llmResult" style="margin-top:1em;"></div>`);

    highlightContent();
}

function goToBad() {
    if ($("#content-modcleaned .bad").length > 0) {
        $("#content-modcleaned").addClass("highlight_problems");
        $("html,body").scrollTop($("#content-modcleaned .bad").first().offset().top);
    }
}

function do_highlight() {
    $("#content-modcleaned").toggleClass("highlight_problems");
}

var mtcss = `.highlight_problems.content > *{color:#aaa;} 
.highlight_problems .bad, #modtools .bad {color:red;background-color:#fcc;font-weight:bold;}
.highlight_problems .bad.adult_theme,#modtools .bad.adult_theme {color:darkred;background-color:#daa;}
.highlight_problems .bad.url, #modtools .bad.url {color:darkgreen;background-color:#afa;}
.highlight_problems img.bad {border:1px solid red; background:#fcc; padding:10px;}
#modtools {border:1px solid #aaf; background-color:#eaeaff; margin: 0 0 1em; padding: 1em 0 1em 1em;} 
#modtools p {float:left; margin-right:5px;}
.highlight_problems .bad {border:1px solid red;padding:2px 4px;}
#llmAuditBtn{margin-left: 1em;}`;

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

    const contentText = extractNormalizedText($("#content-modcleaned")[0]).slice(0, 8000);

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
