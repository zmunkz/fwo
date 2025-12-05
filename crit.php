<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FWO SuperCrit</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: {
                            bg: '#111827',
                            surface: '#1f2937',
                            text: '#f3f4f6',
                            border: '#374151'
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;1,400&family=Inter:wght@400;500;600&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            height: 100vh;
            overflow: hidden;
            transition: background-color 0.3s, color 0.3s;
        }

        /* --- Dark Mode Transitions --- */
        .dark body { background-color: #111827; color: #f3f4f6; }
        .dark .bg-white { background-color: #1f2937; color: #f3f4f6; }
        .dark .bg-gray-50 { background-color: #111827; }
        .dark .border-gray-200 { border-color: #374151; }
        .dark .text-gray-700 { color: #d1d5db; }
        .dark .text-gray-600 { color: #9ca3af; }
        .dark .text-gray-500 { color: #6b7280; }
        
        /* Story Font */
        .story-text {
            font-family: 'Merriweather', serif;
            line-height: 1.6; /* Default relative line-height */
            transition: font-size 0.2s ease, line-height 0.2s ease;
        }

        /* --- Highlight Logic --- */
        .critique-highlight {
            background-color: #fde047; /* Light Mode Yellow */
            cursor: pointer;
            transition: all 0.2s;
            border-radius: 2px;
            padding: 2px 0;
            box-decoration-break: clone;
            -webkit-box-decoration-break: clone;
            position: relative;
        }

        .dark .critique-highlight {
            background-color: #854d0e; /* Dark Mode Amber */
            color: #fff;
        }

        .critique-highlight:hover {
            background-color: #facc15; 
            color: #1e3a8a;
        }
        .dark .critique-highlight:hover {
            background-color: #a16207;
            color: #bfdbfe;
        }

        .critique-highlight.active-group {
            background-color: #fbbf24 !important;
            border-bottom: 3px solid #ea580c;
            box-shadow: 0 0 5px rgba(234, 88, 12, 0.5);
        }
        .dark .critique-highlight.active-group {
            background-color: #ca8a04 !important;
            border-bottom: 3px solid #f97316;
            color: white;
        }

        /* Number Badge in Text */
        .highlight-number {
            font-size: 0.6em;
            vertical-align: super;
            font-weight: bold;
            color: #4f46e5;
            margin-left: 2px;
            user-select: none;
        }
        .dark .highlight-number { color: #818cf8; }

        /* General Note Marker */
        .note-marker {
            display: inline-block;
            width: 0;
            height: 0;
            overflow: hidden;
            vertical-align: text-bottom;
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .dark ::-webkit-scrollbar-thumb { background: #4b5563; }
        .dark ::-webkit-scrollbar-thumb:hover { background: #6b7280; }

        /* --- Compact Editor UI --- */
        .rich-editor {
            min-height: 40px; 
            max-height: 300px;
            overflow-y: auto;
            outline: none;
            padding: 0.5rem;
            cursor: text; 
            user-select: text;
            -webkit-user-select: text;
        }
        .rich-editor:empty:before {
            content: attr(placeholder);
            color: #9ca3af;
            pointer-events: none;
            display: block;
        }

        /* Toolbar visibility logic */
        .comment-card:not(:focus-within) .editor-toolbar {
            display: none;
        }
        .comment-card:not(:focus-within) .rich-editor {
            border-top-left-radius: 0.25rem;
            border-top-right-radius: 0.25rem;
            min-height: 30px; 
            color: #4b5563;
        }
        .dark .comment-card:not(:focus-within) .rich-editor {
            color: #9ca3af;
        }
        
        .comment-card.focused-card {
            border-color: #6366f1;
            box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.1), 0 2px 4px -1px rgba(99, 102, 241, 0.06);
        }
        
        /* Updated: Comment List Bottom Spacing */
        #comments-list {
            padding-bottom: 50vh; /* Ensures the last card is never stuck at the very bottom */
        }

        /* Drag and Drop Styles */
        .draggable-source {
            opacity: 0.4;
            border: 2px dashed #a5b4fc;
        }
        .comment-card {
            cursor: grab;
        }
        .comment-card:active {
            cursor: grabbing;
        }
        /* Restore text cursor for editor */
        .comment-card .rich-editor {
            cursor: text !important;
        }

        /* Dashboard Preview Styling */
        #final-preview blockquote {
            background-color: #f3f4f6;
            border-left: 4px solid #d1d5db;
            padding: 1rem;
            margin: 1rem 0;
            color: #4b5563;
        }
        .dark #final-preview blockquote {
            background-color: #1f2937;
            border-color: #4b5563;
            color: #d1d5db;
        }
        
        /* Updated: FWO Spacing Match */
        #final-preview p {
            margin-bottom: 0.5em;
        }
    </style>
</head>
<body class="flex flex-col h-screen bg-gray-100 text-gray-800">

    <!-- Header -->
    <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 shadow-sm z-10 shrink-0 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <i class="fas fa-dragon text-indigo-600 text-xl dark:text-indigo-400"></i>
                <h1 class="text-xl font-bold text-gray-800 tracking-tight dark:text-white">FWO SuperCrit</h1>
            </div>
            
            <div id="view-controls" class="hidden md:flex items-center gap-2 ml-4 px-4 border-l border-gray-200 dark:border-gray-600">
                <button onclick="toggleTheme()" class="w-8 h-8 rounded-full flex items-center justify-center text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-400 transition" title="Toggle Dark Mode">
                    <i class="fas fa-moon dark:hidden"></i>
                    <i class="fas fa-sun hidden dark:inline text-yellow-400"></i>
                </button>
                <div class="flex items-center bg-gray-100 rounded-lg p-1 dark:bg-gray-700">
                    <button onclick="adjustZoom(-1)" class="w-6 h-6 flex items-center justify-center text-gray-500 hover:text-indigo-600 dark:text-gray-400"><i class="fas fa-minus text-xs"></i></button>
                    <i class="fas fa-text-height text-xs text-gray-400 mx-1"></i>
                    <button onclick="adjustZoom(1)" class="w-6 h-6 flex items-center justify-center text-gray-500 hover:text-indigo-600 dark:text-gray-400"><i class="fas fa-plus text-xs"></i></button>
                </div>
            </div>

            <div id="timer-container" class="hidden md:flex items-center gap-2 ml-4 text-sm font-mono text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-900 px-3 py-1 rounded border border-gray-200 dark:border-gray-700">
                <span id="timer-display">00:00</span>
                <button onclick="toggleTimer()" id="timer-btn" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">
                    <i class="fas fa-pause"></i>
                </button>
            </div>
        </div>
        
        <div class="flex gap-3" id="action-buttons"></div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex overflow-hidden relative dark:bg-gray-900">
        
        <!-- SETUP MODE -->
        <div id="setup-view" class="w-full h-full flex flex-col items-center justify-center p-6 bg-gray-50 dark:bg-gray-900 overflow-y-auto">
            <div class="w-full max-w-3xl flex flex-col bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden my-auto h-[80vh]">
                <div class="bg-indigo-600 p-4 border-b border-indigo-700">
                    <h2 class="text-white font-semibold flex items-center gap-2"><i class="fas fa-cog"></i> Setup Critique</h2>
                </div>
                <div class="p-6 flex flex-col flex-1 gap-6">
                    <div class="flex-1 flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Story Text</label>
                        <textarea id="raw-input" class="flex-1 w-full p-4 story-text text-base border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-indigo-500 focus:outline-none resize-none bg-gray-50 dark:bg-gray-900 dark:text-gray-100" placeholder="Paste the story content here..."></textarea>
                    </div>
                </div>
                <div class="p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex justify-end">
                    <button onclick="startCritique()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-md font-medium transition shadow-sm flex items-center gap-2">
                        Start Critiquing <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- CRITIQUE MODE -->
        <div id="critique-view" class="hidden w-full h-full flex">
            
            <!-- Left: Reader -->
            <div class="flex-1 h-full overflow-y-auto bg-white dark:bg-gray-900" id="story-container">
                <div class="mx-auto py-12 px-8 min-h-full">
                    <div id="render-area" 
                         class="story-text text-lg pb-32 focus:outline-none dark:text-gray-200" 
                         contenteditable="false"></div>
                </div>
            </div>

            <!-- Right: Sidebar -->
            <div class="w-96 bg-gray-50 dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700 h-full flex flex-col shrink-0 shadow-inner z-20">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm flex justify-between items-center">
                    <div class="flex flex-col">
                        <span id="comment-status" class="text-xs font-bold text-gray-700 dark:text-gray-300">0 Comments</span>
                        <span id="word-count-status" class="text-[10px] text-gray-400 font-medium">0 Words</span>
                    </div>
                    <button onclick="insertNote()" class="text-xs bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900 dark:hover:bg-indigo-800 text-indigo-700 dark:text-indigo-200 border border-indigo-200 dark:border-indigo-700 px-3 py-1.5 rounded transition shadow-sm flex items-center" title="Insert general note">
                        <i class="fas fa-map-pin mr-1.5"></i> Add Note
                    </button>
                </div>
                <!-- Comments list container for Drag n Drop -->
                <div id="comments-list" class="flex-1 overflow-y-auto p-4 space-y-4">
                    <div id="empty-state" class="text-center text-gray-400 mt-10 px-4">
                        <i class="fas fa-highlighter text-4xl mb-3 opacity-50"></i>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Highlight text or click "Add Note"<br>to leave feedback.</p>
                        <p class="text-xs text-gray-400 mt-4 leading-relaxed">Comments compile Top to Bottom. Drag cards to reorder.</p>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- Submission Dashboard -->
    <div id="export-modal" class="hidden fixed inset-0 bg-black bg-opacity-70 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-4xl flex flex-col max-h-[95vh] overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-900">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Compiled Crit</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"><i class="fas fa-times text-xl"></i></button>
            </div>
            <div class="p-3 bg-blue-50 dark:bg-blue-900/30 border-b border-blue-100 dark:border-blue-800 text-xs text-blue-800 dark:text-blue-200 flex justify-between items-center">
                <span>Make any last edits directly in the box below.</span>
                <!-- Added Metrics Span -->
                <span id="compile-metrics" class="font-semibold opacity-80"></span>
            </div>
            <div class="flex-1 overflow-y-auto p-8 bg-white dark:bg-gray-800">
                <div id="final-preview" contenteditable="true" class="max-w-3xl mx-auto font-serif text-gray-800 dark:text-gray-200 outline-none min-h-[50vh]"></div>
            </div>
            <div class="p-3 bg-blue-50 dark:bg-blue-900/30 border-b border-blue-100 dark:border-blue-800 text-xs text-blue-800 dark:text-blue-200 flex justify-between items-center">
                <span><strong>REMEMBER:</strong> In the FWO crit box, click the "switch to plain text editor" button before pasting.</span>
            </div>
            <div class="p-4 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3 bg-gray-50 dark:bg-gray-900">
                <button onclick="copyFinalHtml()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded shadow-sm font-medium transition flex items-center gap-2">
                    <i class="fas fa-paper-plane mr-1.5"></i> Copy HTML Code
                </button>
            </div>
        </div>
    </div>

    <div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 pointer-events-none"></div>

    <script>
        // --- State ---
        let comments = {};
        let isSetupMode = true;
        let lastCaretRange = null;
        let timerInterval = null;
        let timerSeconds = 0;
        let isTimerPaused = false;
        let currentZoom = 18;

        // DOM Elements
        const els = {
            setupView: document.getElementById('setup-view'),
            critiqueView: document.getElementById('critique-view'),
            rawInput: document.getElementById('raw-input'),
            renderArea: document.getElementById('render-area'),
            commentsList: document.getElementById('comments-list'),
            emptyState: document.getElementById('empty-state'),
            actionButtons: document.getElementById('action-buttons'),
            commentStatus: document.getElementById('comment-status'),
            wordCountStatus: document.getElementById('word-count-status'),
            timerDisplay: document.getElementById('timer-display'),
            timerBtn: document.getElementById('timer-btn'),
            finalPreview: document.getElementById('final-preview'),
            exportModal: document.getElementById('export-modal'),
            compileMetrics: document.getElementById('compile-metrics') // Added to element list
        };

        // --- Core Application Logic ---

        function startCritique() {
            const text = els.rawInput.value.trim();
            if (!text) return showToast("Please paste story text.", "error");

            const paragraphs = text.split(/\n\s*\n/).filter(p => p.trim() !== '');
            const htmlContent = paragraphs.map(p => `<p class="mb-4">${p.replace(/\n/g, '<br>')}</p>`).join('');
            els.renderArea.innerHTML = htmlContent;

            isSetupMode = false;
            els.setupView.classList.add('hidden');
            els.critiqueView.classList.remove('hidden');
            
            updateHeaderButtons();
            startTimer();
            
            // Initialize line height match
            els.renderArea.style.fontSize = currentZoom + 'px';
            els.renderArea.style.lineHeight = (currentZoom * 1.6) + 'px';

            document.addEventListener('mouseup', handleSelection);
            
            // Drag and Drop Initialization
            els.commentsList.addEventListener('dragover', handleDragOver);
            els.commentsList.addEventListener('drop', handleDrop);
        }

        function resetApp() {
            if(!confirm("Start over? This deletes all comments.")) return;
            stopTimer();
            isSetupMode = true;
            comments = {};
            timerSeconds = 0;
            updateTimerDisplay();

            els.critiqueView.classList.add('hidden');
            els.setupView.classList.remove('hidden');
            els.renderArea.innerHTML = '';
            els.rawInput.value = '';
            els.commentsList.innerHTML = '';
            els.commentsList.appendChild(els.emptyState);
            els.emptyState.style.display = 'block';
            
            updateStatus();
            updateHeaderButtons();
            document.removeEventListener('mouseup', handleSelection);
        }

        // --- Selection & Highlight Logic ---

        function handleSelection(event) {
            // Ignore if in sidebar
            if (event && event.target.closest('#comments-list')) return;

            const selection = window.getSelection();
            if (selection.isCollapsed || selection.rangeCount === 0) return;

            const range = selection.getRangeAt(0);
            if (!els.renderArea.contains(range.commonAncestorContainer)) return;

            // Prevent highlighting inside existing note markers
            if (range.commonAncestorContainer.parentNode?.classList.contains('note-marker')) return;

            // --- Overlap Logic ---
            const iterator = document.createNodeIterator(
                range.commonAncestorContainer,
                NodeFilter.SHOW_ELEMENT,
                { acceptNode: (node) => 
                    node.classList.contains('critique-highlight') && range.intersectsNode(node) 
                    ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_REJECT 
                }
            );

            let existingId = null;
            const nodesToRemove = [];
            let node;
            
            while (node = iterator.nextNode()) {
                const id = node.dataset.groupId;
                if (!existingId) existingId = id;
                if (id) nodesToRemove.push(id); 
            }

            if (nodesToRemove.length > 0) {
                const uniqueIds = [...new Set(nodesToRemove)];
                uniqueIds.forEach(id => { unwrapHighlight(id); });
            }

            let id = existingId || 'crit-' + Date.now();
            let isUpdate = !!existingId;

            const contentDiv = document.createElement('div');
            contentDiv.appendChild(range.cloneContents());
            const capturedHtml = contentDiv.innerHTML;
            const capturedText = contentDiv.textContent;

            try {
                const successful = applyHighlightToRange(range, id);
                if (successful) {
                    if (isUpdate) {
                        if(comments[id]) {
                            comments[id].quoteHtml = capturedHtml;
                            comments[id].text = capturedText;
                            showToast("Selection updated!", "success");
                        } else {
                            createCommentEntry(id, capturedHtml, capturedText);
                        }
                    } else {
                        createCommentEntry(id, capturedHtml, capturedText);
                    }
                    
                    selection.removeAllRanges();
                    
                    // Slightly delayed focus so we can scroll
                    setTimeout(() => focusComment(id), 50);
                    refreshNumbering(); 
                }
            } catch (e) { console.error(e); }
        }

        function createCommentEntry(id, html, text) {
            comments[id] = {
                quoteHtml: html,
                text: text,
                comment: '',
                type: 'highlight',
                timestamp: Date.now()
            };
            addCommentCard(id);
        }

        function applyHighlightToRange(range, id) {
            const extractNodes = (node) => {
                 if (node.nodeType === 3 && node.textContent.trim().length > 0) return [node];
                 let list = [];
                 if (node.childNodes) node.childNodes.forEach(c => list = list.concat(extractNodes(c)));
                 return list;
            };

            const iterator = document.createNodeIterator(
                range.commonAncestorContainer,
                NodeFilter.SHOW_TEXT,
                { acceptNode: (node) => range.intersectsNode(node) ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_REJECT 
                }
            );

            let node;
            const nodesToWrap = [];
            while (node = iterator.nextNode()) nodesToWrap.push(node);

            if(nodesToWrap.length === 0) return false;

            nodesToWrap.forEach((node, index) => {
                const span = document.createElement('span');
                span.className = 'critique-highlight';
                span.dataset.groupId = id;
                span.onclick = (e) => { 
                    e.stopPropagation(); 
                    e.preventDefault(); 
                    focusComment(id); 
                }; 
                
                let start = (node === range.startContainer) ? range.startOffset : 0;
                let end = (node === range.endContainer) ? range.endOffset : node.length;
                
                const text = node.textContent;
                const mid = text.substring(start, end);
                const before = text.substring(0, start);
                const after = text.substring(end);

                span.textContent = mid;
                
                if (index === nodesToWrap.length - 1) {
                     const badge = document.createElement('sup');
                     badge.className = 'highlight-number';
                     span.appendChild(badge);
                }

                const parent = node.parentNode;
                if(after) parent.insertBefore(document.createTextNode(after), node.nextSibling);
                parent.insertBefore(span, node.nextSibling);
                if(before) parent.insertBefore(document.createTextNode(before), span);
                parent.removeChild(node);
            });
            return true;
        }

        function unwrapHighlight(id) {
            const spans = document.querySelectorAll(`.critique-highlight[data-group-id="${id}"]`);
            spans.forEach(span => {
                const badge = span.querySelector('.highlight-number');
                if(badge) badge.remove();
                
                const parent = span.parentNode;
                while (span.firstChild) parent.insertBefore(span.firstChild, span);
                parent.removeChild(span);
            });
            els.renderArea.normalize(); 
        }

        function insertNote() {
            // No selection needed. Just add card to bottom.
            const id = 'note-' + Date.now();
            comments[id] = { text: null, comment: '', type: 'note', timestamp: Date.now() };
            addCommentCard(id);
            // Delay focus slightly to ensure DOM is ready and scroll happens
            setTimeout(() => focusComment(id), 50);
        }

        // --- Comment UI ---

        function addCommentCard(id) {
            els.emptyState.style.display = 'none';

            const card = document.createElement('div');
            card.id = `card-${id}`;
            card.className = "comment-card bg-white dark:bg-gray-800 p-3 rounded shadow-sm border border-gray-200 dark:border-gray-700 transition-all duration-200 ring-2 ring-transparent focus-within:ring-indigo-500 mb-4 card-item cursor-pointer";
            
            // FIX: Dynamic Drag Control on Mouse events
            card.setAttribute('draggable', 'true');
            
            // Only allow dragging if NOT interacting with editor/buttons
            card.addEventListener('mousedown', (e) => {
                if (e.target.closest('.rich-editor') || e.target.closest('button')) {
                    card.setAttribute('draggable', 'false');
                } else {
                    card.setAttribute('draggable', 'true');
                }
            });
            // Re-enable always on mouseup
            card.addEventListener('mouseup', () => card.setAttribute('draggable', 'true'));

            // Drag Start Logic
            card.addEventListener('dragstart', (e) => {
                if (card.getAttribute('draggable') === 'false') {
                    e.preventDefault();
                    return false;
                }
                card.classList.add('dragging', 'draggable-source');
            });
            card.addEventListener('dragend', () => card.classList.remove('dragging', 'draggable-source'));

            // FIXED: Only auto-scroll/focus if click is on card background, not editor
            card.addEventListener('click', (e) => {
                if(!e.target.closest('button') && !e.target.closest('.rich-editor')) {
                    const editor = document.getElementById(`editor-${id}`);
                    if(editor) editor.focus();
                    scrollToHighlight(id);
                }
            });

            const data = comments[id];
            let quoteHtml = '';
            if (data.type === 'highlight') {
                const quoteText = data.text.length > 60 ? data.text.substring(0, 60) + "..." : data.text;
                quoteHtml = `
                    <div class="cursor-pointer hover:text-indigo-600 dark:hover:text-indigo-400 transition" onclick="scrollToHighlight('${id}')">
                        <div class="text-xs text-gray-400 font-serif italic border-l-2 border-yellow-400 pl-2 line-clamp-2 select-none mb-1">
                            <span class="card-number text-xs font-bold text-indigo-600 dark:text-indigo-400 mr-1"></span> "${quoteText}"
                        </div>
                    </div>
                `;
            } else {
                quoteHtml = `<div class="text-xs text-indigo-500 font-bold uppercase tracking-wider mb-2"><i class="fas fa-map-pin"></i> COMMENT</div>`;
            }

            const toolbar = `
                <div class="editor-toolbar flex items-center justify-between mb-1 border-b border-gray-100 dark:border-gray-700 pb-1 bg-gray-50 dark:bg-gray-900 rounded-t p-1">
                    <div class="flex gap-1">
                        <button onmousedown="execCmd(event, 'bold')" class="w-6 h-6 text-xs text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded font-bold">B</button>
                        <button onmousedown="execCmd(event, 'italic')" class="w-6 h-6 text-xs text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded italic">I</button>
                        <button onmousedown="execCmd(event, 'underline')" class="w-6 h-6 text-xs text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded underline">U</button>
                        <button onmousedown="execCmd(event, 'strikeThrough')" class="w-6 h-6 text-xs text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded line-through">S</button>
                    </div>
                    ${data.type === 'highlight' ? `<button onmousedown="insertQuote(event, '${id}')" class="w-6 h-6 text-xs text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900 rounded"><i class="fas fa-quote-right"></i></button>` : ''}
                </div>
            `;

            card.innerHTML = `
                <div class="flex justify-between items-start mb-2">
                    <div class="flex-1">
                        ${quoteHtml}
                    </div>
                    <button onclick="deleteComment('${id}')" class="text-gray-300 hover:text-red-500 transition ml-2 z-10 relative">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                ${toolbar}
                <div 
                    id="editor-${id}"
                    contenteditable="true"
                    oninput="updateCommentText('${id}')" 
                    onblur="checkEmpty('${id}')"
                    class="rich-editor w-full text-sm text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 rounded-b border border-transparent focus:border-indigo-100 dark:focus:border-indigo-900" 
                    placeholder="Type feedback..."></div>
            `;

            // CHANGED: Append to bottom (Chronological)
            els.commentsList.appendChild(card);
            
            // Updated: Scroll new card into view (centered)
            setTimeout(() => {
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                const ed = document.getElementById(`editor-${id}`);
                if(ed) ed.focus();
            }, 50);
            
            updateStatus();
        }

        // --- Drag and Drop Handlers ---

        function handleDragOver(e) {
            e.preventDefault();
            const draggingCard = document.querySelector('.dragging');
            if(!draggingCard) return;

            const afterElement = getDragAfterElement(els.commentsList, e.clientY);
            if (afterElement == null) {
                els.commentsList.appendChild(draggingCard);
            } else {
                els.commentsList.insertBefore(draggingCard, afterElement);
            }
        }

        function handleDrop(e) {
            e.preventDefault();
            // Re-order done in DOM by dragover, just cleanup
        }

        function getDragAfterElement(container, y) {
            const draggableElements = [...container.querySelectorAll('.comment-card:not(.dragging)')]

            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;
                if (offset < 0 && offset > closest.offset) {
                    return { offset: offset, element: child };
                } else {
                    return closest;
                }
            }, { offset: Number.NEGATIVE_INFINITY }).element;
        }

        // --- Helpers ---

        function checkEmpty(id) {
            if (comments[id] && (!comments[id].comment || comments[id].comment.trim() === '')) {
                setTimeout(() => {
                    const card = document.getElementById(`card-${id}`);
                    if (!card || !card.matches(':focus-within')) {
                         deleteComment(id);
                    }
                }, 200);
            }
        }

        function refreshNumbering() {
            // Count highlights for [1] badges
            const markers = document.querySelectorAll('.critique-highlight');
            const seenIds = new Set();
            let count = 1;

            markers.forEach(el => {
                const id = el.dataset.groupId;
                if (!seenIds.has(id)) {
                    seenIds.add(id);
                    // Update Card Number
                    const card = document.getElementById(`card-${id}`);
                    if (card) {
                        const cardNum = card.querySelector('.card-number');
                        if (cardNum) cardNum.textContent = `[${count}]`;
                    }
                    // Update Text Badges
                    const badges = document.querySelectorAll(`.critique-highlight[data-group-id="${id}"] .highlight-number`);
                    badges.forEach(b => b.textContent = `[${count}]`);
                    count++;
                }
            });
        }

        function focusComment(id) {
            document.querySelectorAll('.critique-highlight').forEach(el => el.classList.remove('active-group'));
            document.querySelectorAll('.comment-card').forEach(el => el.classList.remove('focused-card'));
            
            const group = document.querySelectorAll(`[data-group-id="${id}"]`);
            group.forEach(el => el.classList.add('active-group'));
            
            const card = document.getElementById(`card-${id}`);
            if (card) {
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                card.classList.add('focused-card');
                const editor = document.getElementById(`editor-${id}`);
                // Restore Focus Logic Here
                if(editor) editor.focus();
                
                setTimeout(() => card.classList.remove('focused-card'), 1000);
            }
        }

        function scrollToHighlight(id) {
            const el = document.querySelector(`[data-group-id="${id}"]`);
            if (el) {
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                focusComment(id);
            }
        }

        function deleteComment(id) {
            unwrapHighlight(id); 
            const card = document.getElementById(`card-${id}`);
            if (card) card.remove();
            delete comments[id];
            
            if (Object.keys(comments).length === 0) els.emptyState.style.display = 'block';
            
            refreshNumbering();
            updateStatus();
        }

        function updateCommentText(id) {
            const ed = document.getElementById(`editor-${id}`);
            if (ed && comments[id]) {
                comments[id].comment = ed.innerHTML;
                updateStatus();
            }
        }

        function updateStatus() {
            const count = Object.keys(comments).length;
            els.commentStatus.textContent = `${count} Comment${count !== 1 ? 's' : ''}`;
            let w = 0;
            Object.values(comments).forEach(c => {
                 w += c.comment.replace(/<[^>]*>/g, '').trim().split(/\s+/).filter(x=>x).length;
            });
            els.wordCountStatus.textContent = `${w} Words`;
        }

        function execCmd(e, cmd) { e.preventDefault(); document.execCommand(cmd, false, null); }
        function insertQuote(e, id) {
            e.preventDefault();
            const data = comments[id];
            const ed = document.getElementById(`editor-${id}`);
            if(ed && data.text) {
                ed.focus();
                document.execCommand('insertText', false, " " + data.text + " ");
                updateCommentText(id);
            }
        }

        function compileFeedback() {
            let html = '';
            // Compile follows sidebar DOM order (Top to Bottom) - No Reversal
            const sidebarCards = Array.from(document.querySelectorAll('#comments-list .comment-card'));
            
            sidebarCards.forEach(card => {
                const id = card.id.replace('card-', '');
                const data = comments[id];
                if(data && data.comment.trim()) {
                    const safeComment = cleanHtml(data.comment);
                    if(data.type === 'highlight') {
                        let q = data.quoteHtml || data.text;
                        q = q.replace(/class="[^"]*"/g, "").replace(/data-[^=]*="[^"]*"/g, "");
                        q = q.replace(/<sup[^>]*>.*?<\/sup>/g, ""); 
                        q = q.replace(/<p[^>]*>(?:<br\s*\/?>|&nbsp;|\s)*<\/p>/gi, "");
                        html += `<blockquote>${q}</blockquote>\n<p>${safeComment}</p>\n\n`;
                    } else {
                        html += `<p>${safeComment}</p>\n\n`;
                    }
                }
            });

            if(!html) return showToast("No comments to compile.", "error");

            // --- Updated Logic: Grab existing Metrics ---
            // 1. Get Word count from the sidebar status
            const wordCount = parseInt(els.wordCountStatus.textContent) || 0;

            // 2. Get Time
            const timeSpent = els.timerDisplay.textContent;

            // 3. Update Header
            if (els.compileMetrics) {
                els.compileMetrics.textContent = `Crit length: ${wordCount} words, Time spent: ${timeSpent}`;
            }
            // ----------------------------------------

            els.finalPreview.innerHTML = html;
            els.exportModal.classList.remove('hidden');
        }

        function cleanHtml(html) {
            let h = html;
            h = h.replace(/<b>/gi, "<strong>").replace(/<\/b>/gi, "</strong>");
            h = h.replace(/<i>/gi, "<em>").replace(/<\/i>/gi, "</em>");
            return h;
        }

        function closeModal() { els.exportModal.classList.add('hidden'); }
        
        function copyFinalHtml() {
            const code = els.finalPreview.innerHTML;
            navigator.clipboard.writeText(code).then(() => {
                showToast("HTML Code Copied!", "success");
            });
        }

        function updateHeaderButtons() {
            if (isSetupMode) {
                els.actionButtons.innerHTML = ''; 
            } else {
                els.actionButtons.innerHTML = `
                    <button onclick="resetApp()" class="text-gray-500 hover:text-red-600 px-3 py-1 text-sm font-medium transition dark:text-gray-400 dark:hover:text-red-400">
                        <i class="fas fa-trash-alt mr-1"></i> Reset
                    </button>
                    <button onclick="compileFeedback()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-medium shadow-sm transition">
                        <i class="fas fa-paper-plane mr-1.5"></i> Compile
                    </button>
                `;
            }
        }

        function toggleTheme() { document.documentElement.classList.toggle('dark'); }

        function adjustZoom(dir) {
            currentZoom += dir;
            if(currentZoom < 12) currentZoom = 12;
            if(currentZoom > 32) currentZoom = 32;
            
            // Updated: Set both Font Size and Line Height together
            els.renderArea.style.fontSize = currentZoom + 'px';
            els.renderArea.style.lineHeight = (currentZoom * 1.6) + 'px';
        }

        function startTimer() {
            if(timerInterval) clearInterval(timerInterval);
            els.timerDisplay.parentElement.classList.remove('hidden');
            isTimerPaused = false;
            els.timerBtn.innerHTML = '<i class="fas fa-pause"></i>';
            timerInterval = setInterval(() => {
                if(!isTimerPaused) {
                    timerSeconds++;
                    updateTimerDisplay();
                }
            }, 1000);
        }
        function stopTimer() {
            clearInterval(timerInterval);
            els.timerDisplay.parentElement.classList.add('hidden');
        }
        function toggleTimer() {
            isTimerPaused = !isTimerPaused;
            els.timerBtn.innerHTML = isTimerPaused ? '<i class="fas fa-play"></i>' : '<i class="fas fa-pause"></i>';
        }
        function updateTimerDisplay() {
            const m = Math.floor(timerSeconds / 60).toString().padStart(2, '0');
            const s = (timerSeconds % 60).toString().padStart(2, '0');
            els.timerDisplay.textContent = `${m}:${s}`;
        }

        function showToast(msg, type='success') {
            const t = document.createElement('div');
            t.className = `${type==='success'?'bg-gray-800 dark:bg-gray-700':'bg-red-600'} text-white px-4 py-3 rounded shadow-lg text-sm font-medium flex items-center gap-2 toast-enter`;
            t.innerHTML = `<span>${msg}</span>`;
            document.getElementById('toast-container').appendChild(t);
            setTimeout(() => { t.style.opacity='0'; setTimeout(()=>t.remove(),300); }, 3000);
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === "Escape" && !els.exportModal.classList.contains('hidden')) closeModal();
        });

    </script>
</body>
</html>
