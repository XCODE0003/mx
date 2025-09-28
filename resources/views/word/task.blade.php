<!DOCTYPE html>
<html lang="ru" lang="ru" xmlns:m="http://www.w3.org/1998/Math/MathML">

<head>
    <meta charset="UTF-8">
    <title>Вариант (1231Word)</title>

    <style>
        body { font-family: 'Times New Roman', serif; color:#000; margin: 10mm; }
        .container { width: 100%; max-width: 800px; margin: 0 auto; }
        .task-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .task-table td { padding: 0; }
        .num-cell { width: 28px; padding-right: 2px; }
        .num-box { font-weight: 700; width: 28px; height: 28px; text-align: center; border: 1px solid #000; line-height: 28px; display: inline-block; }
        .content { font-size: 16px; line-height: 1.4; }
        .answer-row { margin-top: 6px; }
        .answer-line { min-width: 150px; border-bottom: 1px solid #000; line-height: 1.4; display: inline-block; }
        img { max-width: 100%; height: auto; }
        table { border-collapse: collapse; }
        td, th { vertical-align: top; }
        p { margin: 0 0 6px 0; }
        .MsoNormal { margin: 0; padding: 0; }
    </style>
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

</head>

<body>
    <div class="container">
        @foreach ($tasks as $t)
            <table class="task-table">
                <tr>
                    <td class="num-cell"><span class="num-box">{{ optional($t->group)->formatted_title ?? '№' }}</span></td>
                    <td>
                        <div class="content">{!! ($questionHtmlMap[$t->id] ?? $t->question) !!}</div>
                        <div class="answer-row">
                            <span>Ответ:</span>
                            @if (!empty($withAnswers) && $withAnswers)
                                @php $ans = trim(strip_tags($t->response ?? '')); @endphp
                                @if($ans !== '')
                                    <span class="answer-line">{{ $ans }}</span>
                                @else
                                    <span class="answer-line">&nbsp;</span>
                                @endif
                            @else
                                <span class="answer-line">&nbsp;</span>
                            @endif
                        </div>
                    </td>
                </tr>
            </table>
        @endforeach
    </div>
</body>

</html>