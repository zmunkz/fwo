// INTENDED FOR fantasy-writers.org AS OF 2016-10-08 rev 2025
// they use an old version
window.jQuery = window.$ = undefined;

var jqscript = document.createElement('script');
jqscript.type = "text/javascript";
jqscript.src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js";
document.getElementsByTagName('head')[0].appendChild(jqscript);

var content_sel = ".node.node-type-book .content";

function do_moderate() {
    var summary = $("#modtools");
    summary.html("<h3 style='margin:0'>Mod Tools Analysis:</h3>");
    var content = $(content_sel)[0];

    // check length
    var spoiled_words = 
	( $(".fivestar-static-form-item").text().length>1 ?
	  $(".fivestar-static-form-item").text().split(' ').length : 0 ) +
	( $(".book-navigation").text().length>1 ?
	  $(".book-navigation").text().split(' ').length : 0 ) +
	( $(content_sel+" form").text().length>1 ?
	  $(content_sel+" form").text().split(' ').length : 0 );
    //var words = $(content).text().split(' ').length - spoiled_words;
    var words = $(content).text().split(/(\s+)/).filter(function(el){ return el != " " && el != null && el != "" && el.trim() == el; } ).length - spoiled_words;
    var word_count = new Intl.NumberFormat('en-US').format(words);
    $(summary).append( "<p class='"+(word_count > 7000 ? "bad" : "")+"'>" + word_count + " words.</p>" )
 
    // check for images
    var img_count = $(content_sel+" img").length;
    $(content_sel+" img").addClass("bad");
    $(summary).append( "<p class='"+(img_count > 0 ? "bad" : "")+"'>" + img_count + " images.</p>" )

    // check for links
    var spoiled_url = $(".fivestar-static-form-item a").length + 
	$(".book-navigation a").length + 
	$(content_sel+" form a").length
    var url_count = $(content_sel+" a").length - spoiled_url;
    $(content_sel+" a").addClass("bad url");
    $(".fivestar-static-form-item a, .book-navigation a, "+content_sel+" form a").removeClass("bad")
    $(summary).append( "<p class='"+(url_count > 0 ? "bad url" : "")+"'>" + url_count + " links.</p>" )

    // check for possible vulgarity
    var curse_list=["shit[\\w]*","fuck[\\w]*","cunt","slut","dick[\\w]*","nigger","spic","prick","bastard","bitch[\\w]*","ass(?:hole|clown|face|es)?","twat","vagina"];
    $.each(curse_list, function(i,v){
        $(content).html(function(_, html) {
            var re = new RegExp("\\b("+v+")\\b","gi");
            return html.replace(re, "<span class='bad curse_word' title='Word is potentially vulgar'>$1</span>");
        }); 
    });
    var curses = $(".curse_word").length;
    $(summary).append( "<p class='"+(curses > 0 ? "bad curse_word" : "")+"'>" + curses + " bad words.</p>" )

    // check for potential adult themes
    var adult_content=["(?:gang)?rape[d|s]?","gor[e|y]","naked","nude","stripped","penis","breast[s]?","tit[s]?","orgasm","ejaculate[d|s]?","orgy"];
    $.each(adult_content, function(i,v){
        $(content).html(function(_, html) {
            var re = new RegExp("\\b("+v+")\\b","gi");
            return html.replace(re, "<span class='bad adult_theme' title='Word might suggest adult themes'>$1</span>");
        }); 
    });
    var adult = $(".adult_theme").length;
    $(summary).append( "<p class='"+(adult > 0 ? "bad adult_theme" : "")+"'>" + adult + " potential adult themes.</p>" )
    let llmButtonHtml = "";
    if (words > 8000) {
        llmButtonHtml = `<button id="llmAuditBtn" type="button" disabled>(Too long for LLM)</button>`;
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
}
function goToBad() {
    if ( $(content_sel+" .bad").length > 0 ) {
        $(content_sel).addClass("highlight_problems");
        $("html,body").scrollTop( $(content_sel+" .bad").first().offset().top );
    }
}
function do_highlight() {
    if ( $(content_sel).hasClass("highlight_problems")) $(content_sel).removeClass("highlight_problems");
    else $(content_sel).addClass("highlight_problems");
}

var mtcss = '.highlight_problems.content > *{color:#aaa;} .highlight_problems a.bad,.highlight_problems .bad, #modtools .bad{color:red;background-color:#fcc;font-weight:bold;}.highlight_problems .bad.adult_theme,#modtools .bad.adult_theme{color:darkred;background-collor:#daa;}.highlight_problems.content .bad.adult_theme{border-color:darkred;}.highlight_problems img.bad{border:1px solid red; background:#fcc; padding:10px;}.highlight_problems.content .bad.url{border-color:#0b0;}.highlight_problems .bad.url,#modtools .bad.url{color:darkgreen;background-color:#afa;}#modtools{border:1px solid #aaf; background-color:#eaeaff; margin: 0 0 1em; padding: 1em 0 1em 1em;} #modtools p {float:left; margin-right:5px;}.highlight_problems.content .bad {border:1px solid red;padding:5px;}#llmAuditBtn{margin-left: 1em;}',
    mthead = document.getElementsByTagName('head')[0],
    mtstyle = document.createElement('style');
mtstyle.type = 'text/css';
if (mtstyle.styleSheet){
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
    if ( i == 0 ) {
        console.log("Opps, I need jQuery and it never loaded.");
    }

    if (typeof jQuery == 'undefined') setTimeout( "wait_for_init(" + --i + ")", 100);
    else do_init();
}
async function do_llm_audit() {
    $("#llmAuditBtn").remove();
    $("#llmResult").html("<div><em>Waking up the bots...</em></div>");

    const contentText = $(content_sel).text().trim().replace(/\s+/g, " ").slice(0, 8000);

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
        $("#llmResult").html("<div style='color:red;'>Sorry, the bots failed :-(<!--" + gptMessage.replace(/\n/g, "<br>") + "--></div>");
    }
}

setTimeout( "wait_for_init(20)", 100);

