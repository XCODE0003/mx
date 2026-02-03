<!DOCTYPE html>
<html lang="ru" xmlns:m="http://www.w3.org/1998/Math/MathML">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Задание </title>
    <base href="{{ url('/') }}/">
    <link rel="stylesheet" href="/assets/task.css?v={{ time() }}">
    <style>
        body {
            font-family: 'Times New Roman', serif;
            margin: 20px;
            line-height: 1.6;
            color: #000;
        }

        .container {
            width: 100%;
            /* max-width: 800px; */
            margin: 0 auto;
        }

        .task-title {
            font-size: 18px;
            margin-bottom: 12px;
            flex-shrink: 0;
            font-weight: 700;
        }

        .task-content {
            line-height: 1.5;
            font-size: 18px !important;
        }


        .header p {
            font-size: 18px !important;
        }

        .task-content p>span[style*="font-size: 16px"] {
            font-size: 18px !important;
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

        .preview-page {
            width: 240mm;
            min-height: auto;
            margin: 5mm auto;
            padding: 15mm;
            padding-bottom: 20mm;
            background: #fff;
            box-shadow: 0 0 6mm rgba(0, 0, 0, .15);
            box-sizing: border-box;
            position: relative;
        }

        @media print {
            .preview-page {
                box-shadow: none;
                margin: 0;
                padding: 15mm;
                padding-bottom: 20mm;
                min-height: auto;
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
                imageFont: null,
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
            displayAlign: "center",
            displayIndent: "0em",
            TeX: { extensions: ["AMSmath.js","AMSsymbols.js","noErrors.js","noUndefined.js"] }
        });
        MathJax.Hub.Queue(["Typeset", MathJax.Hub, function(){
            document.body.setAttribute('data-mathjax-ready','1');
        }]);
    </script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.5/MathJax.js?config=TeX-MML-AM_CHTML"></script>
    <style>
        .header {
            text-align: center;
        }

        :root {
            --math-width: 550px;
        }

        .task-content p>span[style*="font-size: 14pt"]:not(:has(.math)) {
            font-size: 18px !important;
        }

        .MathJax_Display {
            display: block;
            page-break-inside: auto !important;
            break-inside: auto !important;
        }


        form>table>tbody>tr>td {
            padding: 0 !important;
            margin: 0 !important;
        }

        .instruction {
            /* вместо text-wrap: wrap; */
            text-align: justify;
            text-justify: inter-word;

            line-height: 1.4;
            -webkit-hyphens: auto;
            hyphens: auto;

            overflow-wrap: break-word;
            word-break: normal;
        }

        .instruction p {
            margin: 0 0;
            text-indent: 2em;
            /* "красная строка" */
        }

        .instruction p:last-child {
            margin-bottom: 0;
        }

        /* Обертка задания для предотвращения разрыва */
        .task-block {
            display: block;
            width: 100%;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            -webkit-column-break-inside: avoid !important;
            margin-bottom: 20px;
            /* Для Chrome/Puppeteer */
            overflow: hidden;
        }

        @media print {
            .task-block {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                -webkit-column-break-inside: avoid !important;
                page-break-before: auto;
                page-break-after: auto;
            }
        }

        .task-content {
            display: flex;
            gap: 10px;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        /* Предотвращаем разрыв внутри вариантов ответов */
        .varinats-block,
        .distractors-table,
        #answer_block {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
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

        .MsoNormalTable,
        .MsoTableGrid,
        table {
            margin: 0;
            padding: 0;
        }

        /* .submit-outblock {
            display: none;
        } */
        .MsoTableGrid {
            border: 0;
        }

        *,
        ::after,
        ::before {
            box-sizing: border-box !important;
        }

        .Distractor {
            margin: 0 !important;
        }

        .page-footer {
            font-size: 12px;
            text-align: center;
            margin-top: 16px;
            padding-top: 8px;
            border-top: 1px solid #000;
            line-height: 1.4;
            position: absolute;
            left: 15mm;
            right: 15mm;
            bottom: 5mm;
        }

        @media print {
            .page-footer {
                position: fixed;
                bottom: 12mm;
                left: 15mm;
                right: 15mm;
            }
        }
    </style>

    <!-- Image display functions (must be loaded before body content) -->
    <script>
        // глобальные настройки
        window.qguid = '';
        window.qfiles_location = '../../'; // при необходимости поправьте путь к папке с файлами

        // Вспомог: убираем вхождения ". " внутри имени
        function cleanName(s) {
            var p;
            while ((p = s.indexOf('. ')) > 0) {
                s = s.substring(0, p + 1) + s.substring(p + 2);
            }
            return s;
        }

        // Показывает картинку внутри контента
        window.ShowPictureQ = function (s, hint) {
            s = cleanName(s);
            document.write('<img src="' + window.qfiles_location + s + '" align="absmiddle" alt="' + (hint || '') + '" border="0"> ');
        };

        window.ShowPictureQBL = function (s, hint, h, bl) {
            s = cleanName(s);
            var vspace = h / 2 - bl;
            if (vspace < 0) vspace = -vspace;
            document.write('<img src="' + window.qfiles_location + s + '" align="middle" border="0" alt="' + (hint || '') + '" vspace="' + vspace + '" style="position:relative; top:' + (h / 2 - bl) + 'px;">');
        };

        window.ShowPictureQ2WH = function (s, s2, hint, w, h) {
            if (s.indexOf('.flv') > 0 || s.indexOf('.mp4') > 0 || s.indexOf('.swf') > 0) {
                s = '../../show_media.php?m=' + encodeURIComponent(s) + '&w=' + w + '&h=' + h;
            }
            w = w + 40;
            h = h + 30;
            var url = window.qfiles_location + s;
            var popup = 'var wnd=open(\'' + url + '\',\'\',\'\',status=1,resizable=1,menubar=0,scrollbars=1,width=' + w + ',height=' + h + ',left=' + ((screen.width - w) / 2) + ',top=' + ((screen.height - h) / 2) + ');wnd.focus();';
            document.write('<a href="javascript:' + popup + '"><img border="0" src="' + window.qfiles_location + s2 + '" align="absmiddle" style="cursor:pointer" alt="' + (hint || '') + '"></a> ');
        };

        window.ShowPictureQ3WH = function (s, s2, s3, hint, w, h) {
            window.ShowPictureQ2WH(s, s2, hint, w, h);
        };

        window.ShowPictureQ2 = function (s, s2, hint) {
            window.ShowPictureQ2WH(s, s2, hint, 600, 400);
        };

        window.invertImage = function (img, s, s2) {
            if (img.src.indexOf(s) > 0) img.src = window.qfiles_location + s2;
            else img.src = window.qfiles_location + s;
        };

        window.ShowPictureQ3 = function (s, s2, hint) {
            s = cleanName(s);
            var src2 = window.qfiles_location + s2;
            if (s.indexOf('.jpg') > 0 || s.indexOf('.png') > 0 || s.indexOf('.gif') > 0) {
                var src1 = window.qfiles_location + s;
                document.write('<img src="' + src2 + '" onclick="invertImage(this, \'' + src1 + '\', \'' + src2 + '\')" align="absmiddle" style="cursor:pointer" alt="' + (hint || '') + '" border="0"> ');
            } else {
                document.write('<img src="' + src2 + '" align="absmiddle" style="cursor:pointer" alt="' + (hint || '') + '" border="0"> ');
            }
        };
    </script>
</head>

<body>

    <div class="container preview-page">
        @if(!$withAnswers)
            {!! $subject->text_header ?? '' !!}
            <div class="page-break"></div>
        @else
            <div class=" font-bold text-center w-full">
                Ответы и решения
            </div>
        @endif
        @foreach ($tasks as $t)
                        <div style="display: flex; flex-direction: column; gap: 10px;">

                            <div id="group_{{ $t->mark }}" class="task-block">
                                @if (!$withAnswers)
                                    {!! optional($t->group)->text_title ?? '' !!}
                                    @if(!$withAnswers)

<div style="font-size: 18px;">
    {!! optional($t->group)->formatted_title == 1 ? optional($t->group)->question : '' !!}
</div>
@endif
                                   <div style="margin-bottom: 24px; padding-left: 40px; font-size: 18px !important;">
                                   @if(!empty($t->border) && !empty($t->blank_text) && !is_null($t->type_answer))
                                        <div class="task-title-border" style="border: 1px solid #000; padding: 5px; margin-bottom: 10px;">
                                            {!! $t->blank_text ?? '' !!}
                                        </div>
                                    @else
                                        {!! $t->blank_text ?? '' !!}
                                    @endif
                                   </div>
                                @endif
                                <div class="task-content">

                                @if($t->response !== null)
                                    <div class="task-title">{{ optional($t->group)->formatted_title ?? '№' }}</div>
                                @endif
                                    <div style="display: flex; flex-direction: column; gap: 5px; width:100%">
                                        @if(!$withAnswers)
                                            <div class="task-content">
                                                <div style="width: 100%;">

                                                    {!! ($questionHtmlMap[$t->id] ?? $t->question) !!}

                                                </div>

                                            </div>
                                        @endif
                                        <div id="answer_block" class="flex gap-1 border border-1 border-solid" style="display: flex; gap: 5px; width: 100%">
                                            @if(!$withAnswers && $t->type_answer != 'hide_line')
                                                <p>
                                                    Ответ:
                                                </p>
                                            @endif
                                            <div id="answer_kim" style="min-height: 10px;display: inline-flex; gap: 5px; width: 100%">
                                                @if ($withAnswers)
                                                    @php
                    $ans = $t->response ?? '';
                    // Для группы 132 удаляем "ОТВЕТ:" из ответа
                    if (($group->id ?? null) == 132 && $ans !== '') {
                        $ans = preg_replace('/ОТВЕТ:\s*/i', '', $ans);
                        $ans = preg_replace('/Ответ:\s*/i', '', $ans);
                        $ans = trim($ans);
                    }
                                                     @endphp
                                                    @if($ans !== '')
                                                        <div style="min-width: 150px;  line-height: 1.5;">
                                                            {!! $ans !!}
                                                        </div>
                                                    @else

                                                    @endif
                                                @else
                                                    @if(isset($t->mark) && ($t->mark == 68 || $t->mark == 54))
                                                        <div style="display: flex; gap: 5px; align-items: center;">
                                                            <div style="width: 32px; height: 32px; border: 1px solid #000;">

                                                            </div>
                                                        </div>
                                                    @elseif (isset($t->mark) && $t->type_answer == 'hide_line')
                                                    @else
                                                           @if ($t->response !== null)
                                                            <div style="border: 0px solid #000; max-height: 25px; min-width: 150px; padding-bottom: 0px; border-bottom: 1px solid #000; line-height: 0;">

                                                            </div>
                                                        @endif
                                                    @endif

                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>


        @endforeach
    </div>

    <script>
        (function () {
            function hideEmptyElements() {
                document.querySelectorAll('p.MsoNormal').forEach(function (p) {
                    if (p.querySelector('img, picture, svg, math, video, iframe, object, embed')) return;
                    var text = (p.textContent || '').replace(/\u00A0/g, '').trim();
                    if (text.length === 0) {
                        var temp = p.cloneNode(true);
                        temp.querySelectorAll('*').forEach(function (el) {
                            if ((el.textContent || '').replace(/\u00A0/g, '').trim().length === 0 && el.querySelectorAll('*').length === 0) {
                                el.parentNode.removeChild(el);
                            }
                        });
                        var remaining = (temp.textContent || '').replace(/\u00A0/g, '').trim();
                        if (remaining.length === 0) {
                            p.style.display = 'none';
                        }
                    }
                });
                document.querySelectorAll('strong').forEach(function (strong) {
                    var text = (strong.textContent || '').replace(/\u00A0/g, '').replace(/\s/g, '').trim();
                    if (text.length === 0) {
                        strong.style.display = 'none';
                    }
                });
            }
            if (document.readyState !== 'loading') hideEmptyElements();
            else document.addEventListener('DOMContentLoaded', hideEmptyElements);
            if (typeof MathJax !== 'undefined' && MathJax.Hub && MathJax.Hub.Queue) {
                MathJax.Hub.Queue(hideEmptyElements);
            }
        })();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const bodyId = document.body.id;
            const isGroup132 = bodyId === 'group_132' || bodyId === 'group_133' || bodyId === 'group_134' || bodyId === 'group_26' || bodyId === 'group_28' || bodyId === 'group_29';
            const answers = document.querySelectorAll('[id="answer_kim"]');

            answers.forEach(function (answer) {
                const answer_p = answer.getElementsByTagName('p');

                function isAnswerContinuation(text) {
                    const trimmed = text.trim();
                    return /^[бвгдежзийклмнопрстуфхцчшщъыьэюя]\s*\)/i.test(trimmed) ||
                        /^\s*[бвгдежзийклмнопрстуфхцчшщъыьэюя]\s*\)/i.test(trimmed);
                }

                let lastAnswerIndex = -1;
                let hasOtvets = false;

                for (let i = 0; i < answer_p.length; i++) {
                    const textContent = answer_p[i].textContent || '';
                    const innerHTML = answer_p[i].innerHTML || '';
                    const hasOtvetsInText = textContent.includes('ОТВЕТ:') || innerHTML.includes('ОТВЕТ:');
                    const hasAnswerInText = textContent.includes('Ответ:') || innerHTML.includes('Ответ:');

                    if (hasOtvetsInText) {
                        lastAnswerIndex = i;
                        hasOtvets = true;
                    } else if (hasAnswerInText && !hasOtvets) {
                        lastAnswerIndex = i;
                    }
                }

                if (lastAnswerIndex >= 0) {
                    let hidingContinuation = false;
                    for (let i = 0; i < answer_p.length; i++) {
                        const answer_div = answer_p[i];
                        const textContent = answer_div.textContent || '';
                        const innerHTML = answer_div.innerHTML || '';
                        const hasAnswer = textContent.includes('Ответ:') || textContent.includes('ОТВЕТ:') ||
                            innerHTML.includes('Ответ:') || innerHTML.includes('ОТВЕТ:');

                        if (hidingContinuation) {
                            if (isAnswerContinuation(textContent)) {
                                answer_div.style.display = 'none';
                                continue;
                            } else if (hasAnswer && i === lastAnswerIndex) {
                                hidingContinuation = false;
                            } else if (hasAnswer) {
                                answer_div.style.display = 'none';
                                hidingContinuation = true;
                                continue;
                            } else {
                                hidingContinuation = false;
                            }
                        }

                        if (hasAnswer) {
                            if (i === lastAnswerIndex) {
                                answer_div.style.display = 'block';
                                if (innerHTML.includes('ОТВЕТ:')) {
                                    answer_div.innerHTML = innerHTML.replace(/ОТВЕТ:/g, 'Ответ:');
                                }
                                hidingContinuation = false;
                            } else {
                                answer_div.style.display = 'none';
                                hidingContinuation = true;
                            }
                        } else {
                            answer_div.style.display = 'block';
                        }
                    }
                }

                if (isGroup132) {
                    const answerDivs = answer.querySelectorAll('div');
                    answerDivs.forEach(function (div) {
                        let text = div.textContent || div.innerText || '';
                        text = text.replace(/ОТВЕТ:\s*/gi, '').replace(/Ответ:\s*/gi, '').trim();
                        if (/^\d+$/.test(text.trim())) {
                            div.textContent = text.trim();
                        }
                    });
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const taskContents = document.querySelectorAll('.task-content');
            taskContents.forEach(function (content) {
                content.innerHTML = content.innerHTML.replace(/&nbsp;/g, ' ');
            });
            const hints = document.getElementsByClassName('hint');
            Array.from(hints).forEach(function (h) {
                h.remove();
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Находим все блоки задач
            const taskBlocks = document.querySelectorAll('.task-block');

            taskBlocks.forEach(function (taskBlock) {
                const taskContent = taskBlock.querySelector('.task-content');
                if (!taskContent) return;

                // Проверяем, есть ли в variants-block таблица
                const variantsBlock = taskContent.querySelector('.varinats-block');
                if (variantsBlock) {
                    const hasTable = variantsBlock.querySelector('table.answer-table');
                    if (hasTable) {
                        // Скрываем answer_block
                        const answerBlock = taskContent.querySelector('#answer_block');
                        if (answerBlock) {
                            answerBlock.style.display = 'none';
                        }

                        // Добавляем "Ответ:" в variants-block
                        const answerLabel = document.createElement('p');
                        answerLabel.textContent = 'Ответ: ';
                        variantsBlock.insertBefore(answerLabel, variantsBlock.firstChild);
                    }
                }
            });
        });
    </script>
</body>
</html>
