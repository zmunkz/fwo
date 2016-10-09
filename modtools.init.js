// ==UserScript==
// @name         FWO Mod Tools
// @namespace    http://zmunk.com/
// @version      1.0
// @description  Pull in a script to help with moderating stories
// @match        http://www.fantasy-writers.org/story/*
// @author       ZmunkZ
// ==/UserScript==

(function() {
  'use strict';
  try {
    var fwomod=document.createElement("script");
    fwomod.type="text/javascript";
    fwomod.src="//zmunk.com/public/fwo/mod_tools.js";
    document.getElementsByTagName('head')[0].appendChild(fwomod);  
  }
  catch(err) { console.log('Sorry, I failed.'); }
})();

