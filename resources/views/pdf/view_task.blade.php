<!DOCTYPE html>
<html lang="ru" xmlns:m="http://www.w3.org/1998/Math/MathML">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Задание </title>
    <base href="{{ url('/') }}/">
    <link rel="stylesheet" href="/assets/task.scss">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <style>
        body {
            /* font-family: 'Times New Roman', serif; */
            font-family: 'Inter', serif !important;
            margin: 20px;
            line-height: 1.6;
            color: #000;
        }



        .task-title {
            font-size: 18px;
            margin-bottom: 12px;
            flex-shrink: 0;
            font-weight: 700;
        }

        .task-content {
            line-height: 1.5;
            font-size: 16px;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        /* table {
            border-collapse: collapse;
            width: 100%;
            margin: 16px 0;
            background-color: #fff !important;
        } */

        /* td,
        th {
            padding: 8px;
            vertical-align: top;
            ;
        } */

        .MsoNormal {
            margin: 0;
            padding: 0;
        }

        .mml {
            font-family: 'Times New Roman', serif;
        }

        .tbody {
            border-color: transparent !important;
        }

        /* Пределы страницы A4 и предпросмотр разбиения */
        @page {
            size: A4;
            margin: 15mm;
        }


        @media print {
            .preview-page {
                box-shadow: none;
                margin: 0;
                padding: 0;
            }
        }

        /* Явный разрыв страницы */
        .page-break {
            display: block;
            height: 0;
            border-top: 2px dashed #9aa;
            margin: 18px 0;
        }

        @media print {
            .page-break {
                page-break-after: always;
                border: 0;
                margin: 0;
            }
        }
    </style>

    <!-- MathJax v2.7.5 (как на ФИПИ) -->
    <script type="text/x-mathjax-config">
        MathJax.Hub.Config({
            messageStyle: "none",
            "HTML-CSS": {
                matchFontHeight: true,
                availableFonts: ["STIX","TeX"],
                preferredFont: "STIX",
                webFont: "STIX-Web",
                imageFont: null
            },
            MMLorHTML: {
                prefer: { MSIE: "MML", Firefox: "HTML", Safari: "HTML", Chrome: "HTML" }
            },
            tex2jax: {
                inlineMath: [["\\(","\\)"],["$","$"]],
                displayMath: [["\\[","\\]"],["$$","$$"]],
                processEscapes: true,
                processEnvironments: true,
                preview: "TeX"
            },
            mml2jax: { processClass: "mml" },
            TeX: { extensions: ["AMSmath.js","AMSsymbols.js","noErrors.js","noUndefined.js"] }
        });
        MathJax.Hub.Queue(["Typeset", MathJax.Hub, function(){
            document.body.setAttribute('data-mathjax-ready','1');
        }]);
    </script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.5/MathJax.js?config=TeX-AMS-MML_HTMLorMML"></script>
    <style>
        .header {
            text-align: center;
        }
        form > table > tbody > tr > td{
            padding: 0 !important;
            margin: 0 !important;
        }
        .instruction {
            text-wrap: wrap;
            text-align: justify;
        }

        .task-content {
            display: flex;
            gap: 10px;
        }

        .task-title {
            font-weight: 700;
            height: 31px;
            width: 31px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #000;
            height: fit-content;
        }
        .MsoNormalTable, .MsoTableGrid, table {
            margin: 0;
            padding: 0;
        }
        .submit-outblock{
            display: none;
        }
        *, ::after, ::before{
            box-sizing: border-box !important;
        }
        .Distractor{
            margin: 0 !important;
        }
    </style>
</head>

<body>
    <div class="container preview-page">

        <div class="task-content">


            <div style="display: flex; flex-direction: column; gap: 5px;">
                <div class="task-content">
                    <div>
                    {!! ($questionHtmlMap[$task->id] ?? $task->question) !!}
                    </div>

                </div>
                Ответ: ___________________________
            </div>

        </div>
        <br><br>




<script>
    // (function () {
    //     function hideEmptyMsoParagraphs() {
    //         document.querySelectorAll('p.MsoNormal').forEach(function (p) {
    //             var text = (p.textContent || '')
    //                 .replace(/\u00A0/g, '') // убрать &nbsp;
    //                 .trim();
    //             if (text.length === 0) {
    //                 p.style.display = 'none';
    //             }
    //         });
    //     }
    //     if (document.readyState !== 'loading') hideEmptyMsoParagraphs();
    //     else document.addEventListener('DOMContentLoaded', hideEmptyMsoParagraphs);
    //     if (window.MathJax && MathJax.Hub && MathJax.Hub.Queue) {
    //         MathJax.Hub.Queue(hideEmptyMsoParagraphs);
    //     }
    // })();
</script>
<script>
(function(){
    var taskId = {{ (int) ($task->id ?? 0) }};
    function getContentHeight(){
        var body = document.body;
        var html = document.documentElement;
        var h = Math.max(
            body.scrollHeight, body.offsetHeight, body.clientHeight,
            html.scrollHeight, html.offsetHeight, html.clientHeight
        );
        return h;
    }
    function postHeight(){
        try {
            var height = getContentHeight();
            parent && parent.postMessage({ type: 'TASK_IFRAME_HEIGHT', taskId: taskId, height: height }, '*');
        } catch(e) {}
    }
    function schedule(){
        postHeight();
        setTimeout(postHeight, 100);
        setTimeout(postHeight, 300);
        setTimeout(postHeight, 800);
    }
    if (document.readyState !== 'loading') schedule();
    else document.addEventListener('DOMContentLoaded', schedule);
    window.addEventListener('load', schedule);
    if ('ResizeObserver' in window) {
        try { new ResizeObserver(postHeight).observe(document.body); } catch(e) {}
    }
    if (window.MathJax && MathJax.Hub && MathJax.Hub.Queue) {
        MathJax.Hub.Queue(function(){ setTimeout(postHeight, 0); setTimeout(postHeight, 300); });
    }
})();
</script>
</div>
</body>