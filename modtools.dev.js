// INTENDED FOR fantasy-writers.org AS OF 2016-10-08 rev 2025.16
// rewritten 2025-06-09 with DOM clone and content flattening
window.jQuery = window.$ = undefined;

var jqscript = document.createElement('script');
jqscript.type = "text/javascript";
jqscript.src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js";
document.getElementsByTagName('head')[0].appendChild(jqscript);

var content_sel = ".node.node-type-book .content";

function getCleanContentClone() {
    const original = document.querySelector(content_sel);
    const clone = original.cloneNode(true);

    // Remove unwanted elements from clone only
    ["fivestar-static-form-item", "book-navigation", "notification-ui-options-form-0"].forEach(cls => {
        $(clone).find(`.${cls}, #${cls}`).remove();
    });

    // Flatten all elements except p and div
    $(clone).find("*").each(function () {
        if (!["DIV", "P"].includes(this.tagName)) {
            $(this).replaceWith($(this).text());
        }
    });

    return clone;
}

function scanAndHighlightText(clone, patternList, className, tooltip) {
    let fullText = clone.textContent || clone.innerText || "";
    fullText = fullText.replace(/\s+/g, " ").trim();

    let matchCount = 0;
    const reList = patternList.map(v => new RegExp("(^|[^\\w])(" + v + ")", "gi"));

    reList.forEach(re => {
        const matches = [...fullText.matchAll(re)];
        matchCount += matches.length;
        $(clone).html(function (_, html) {
            return html.replace(re, `$1<span class="bad ${className}" title="${tooltip}">$2</span>`);
        });
    });

    return { matchCount, cleanedText: fullText, updatedHTML: clone.innerHTML };
}

function do_moderate() {
    var summary = $("#modtools");
    summary.html("<h3 style='margin:0'>Mod Tools Analysis:</h3>");

    const contentEl = document.querySelector(content_sel);
    const clone = getCleanContentClone();

    const curse_list = ["shit[\\w]*", "fuck[\\w]*", "motherfuck[\\w]*", "cunt", "slut", "dick[\\w]*", "nigger", "piss", "cock[\\w]*", "spic", "prick", "bastard", "bitch[\\w]*", "ass(?:hole|clown|face|es)?", "twat", "vagina"];
    const adult_content = ["(?:gang)?rape[d|s]?", "gor[e|y]", "naked", "nude", "cum", "jizz", "torture", "stripped", "penis", "breast[s]?", "tit[s]?", "orgasm", "ejaculate[d|s]?", "orgy"];

    const curseScan = scanAndHighlightText(clone, curse_list, "curse_word", "Word is potentially vulgar");
    const adultScan = scanAndHighlightText(clone, adult_content, "adult_theme", "Word might suggest adult themes");

    const cleanedText = curseScan.cleanedText;
    const visibleContent = clone.innerHTML;
    $(content_sel).html(visibleContent);

    // Word count
    const spoiled_words =
        ($(".fivestar-static-form-item").text().split(/\s+/).length || 0) +
        ($(".book-navigation").text().split(/\s+/).length || 0) +
        ($(content_sel + " form").text().split(/\s+/).length || 0);

    const words = cleanedText.split(/\s+/).filter(w => w.trim()).length - spoiled_words;
    const word_count = new Intl.NumberFormat('en-US').format(words);
    $(summary).append(`<p class='${words > 7000 ? "bad" : ""}'>${word_count} words.</p>`);

    // Image count
    const img_count = $(content_sel + " img").length;
    $(content_sel + " img").addClass("bad");
    $(summary).append(`<p class='${img_count > 0 ? "bad" : ""}'>${img_count} images.</p>`);

    // Link count
    const spoiled_url = $(".fivestar-static-form-item a").length +
        $(".book-navigation a").length +
        $(content_sel + " form a").length;
    const url_count = $(content_sel + " a").length - spoiled_url;
    $(content_sel + " a").addClass("bad url");
    $(".fivestar-static-form-item a, .book-navigation a, " + content_sel + " form a").removeClass("bad");
    $(summary).append(`<p class='${url_count > 0 ? "bad url" : ""}'>${url_count} links.</p>`);

    // Curse words
    $(summary).append(`<p class='${curseScan.matchCount > 0 ? "bad curse_word" : ""}'>${curseScan.matchCount} bad words.</p>`);

    // Adult content
    $(summary).append(`<p class='${adultScan.matchCount > 0 ? "bad adult_theme" : ""}'>${adultScan.matchCount} potential adult themes.</p>`);

    // LLM Button
    let llmButtonHtml = "";
    if (words > 8000) {
        llmButtonHtml = `<button id="llmAuditBtn" type="button" disabled title="Site limit is 8000 words">(Too long for LLM)</button>`;
    } else {
        llmButtonHtml = `<button id="llmAuditBtn" type="button" onclick="do_llm_audit()">LLM Audit</button>`;
    }

    $(summary).append(`
  <div id="modtools-buttons" style="float:none;">
    <button style="display:none;" data-comment="This is just for me for testing" type="button" onclick="do_moderate()">Refresh</button>
    <button type="button" onclick="do_highlight()">Toggle Highlight</button>
    <button onclick="goToBad();" type="button">&gt;</button>
    ${llmButtonHtml}
  </div>
  <div id="llmResult" style="margin-top:1em;"></div>
`);

    // Save for LLM use
    window._fwo_cleanedText = cleanedText;
}

function goToBad() {
    if ($(content_sel + " .bad").length > 0) {
        $(content_sel).addClass("highlight_problems");
        $("html,body").scrollTop($(content_sel + " .bad").first().offset().top);
    }
}

function do_highlight() {
    if ($(content_sel).hasClass("highlight_problems")) {
        $(content_sel).removeClass("highlight_problems");
    } else {
        $(content_sel).addClass("highlight_problems");
    }
}

var mtcss = `
.highlight_problems.content > * { color: #aaa; }
.highlight_problems .bad, #modtools .bad {
  color: red;
  background-color: #fcc;
  font-weight: bold;
  border-radius: 2px;
  padding: 1px 3px;
}
.highlight_problems .bad.adult_theme, #modtools .bad.adult_theme {
  color: darkred;
  background-color: #faa;
}
.highlight_problems img.bad {
  border: 1px solid red;
  background: #fcc;
  padding: 10px;
}
.highlight_problems .bad.url, #modtools .bad.url {
  color: darkgreen;
  background-color: #afa;
}
#modtools {
  border: 1px solid #aaf;
  background-color: #eaeaff;
  margin: 0 0 1em;
  padding: 1em 0 1em 1em;
}
#modtools p {
  float: left;
  margin-right: 5px;
}
#llmAuditBtn {
  margin-left: 1em;
}
`;

var mthead = document.getElementsByTagName('head')[0];
var mtstyle = document.createElement('style');
mtstyle.type = 'text/css';
if (mtstyle.styleSheet) {
    mtstyle.styleSheet.cssText = mtcss;
} else {
    mtstyle.appendChild(document.createTextNode(mtcss));
}
mthead.appendChild(mtstyle);

function do_init() {
    $(".submitted").first().parent().prepend("<div id='modtools'></div>");
    do_moderate();
}

function wait_for_init(i) {
    if (i == 0) {
        console.log("Oops, I need jQuery and it never loaded.");
    }

    if (typeof jQuery == 'undefined') {
        setTimeout("wait_for_init(" + --i + ")", 100);
    } else {
        do_init();
    }
}

async function do_llm_audit() {
    $("#llmAuditBtn").remove();
    $("#llmResult").html("<div><em>Waking up the bots...</em></div>");

    const contentText = (window._fwo_cleanedText || "").slice(0, 8000);

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

setTimeout("wait_for_init(20)", 100);
