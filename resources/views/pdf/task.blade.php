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

        .preview-page {
            width: 240mm;
            /* width: 210mm; */
            min-height: 297mm;
            margin: 5mm auto;
            padding: 15mm;
            /* padding: 15mm; */
            background: #fff;
            box-shadow: 0 0 6mm rgba(0, 0, 0, .15);
            box-sizing: border-box;
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
    <script type="text/javascript"
            src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.5/MathJax.js?config=TeX-MML-AM_CHTML"></script>
    <style>
        .header {
            text-align: center;
        }
        :root { --math-width: 550px; }

        .MathJax_Display {
            display: block;
            page-break-inside: auto !important;
            break-inside: auto !important;
        }


        form > table > tbody > tr > td {
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
        window.ShowPictureQ = function(s, hint) {
            s = cleanName(s);
            document.write('<img src="' + window.qfiles_location + s + '" align="absmiddle" alt="' + (hint || '') + '" border="0"> ');
        };

        window.ShowPictureQBL = function(s, hint, h, bl) {
            s = cleanName(s);
            var vspace = h / 2 - bl;
            if (vspace < 0) vspace = -vspace;
            document.write('<img src="' + window.qfiles_location + s + '" align="middle" border="0" alt="' + (hint || '') + '" vspace="' + vspace + '" style="position:relative; top:' + (h / 2 - bl) + 'px;">');
        };

        window.ShowPictureQ2WH = function(s, s2, hint, w, h) {
            if (s.indexOf('.flv') > 0 || s.indexOf('.mp4') > 0 || s.indexOf('.swf') > 0) {
                s = '../../show_media.php?m=' + encodeURIComponent(s) + '&w=' + w + '&h=' + h;
            }
            w = w + 40;
            h = h + 30;
            var url = window.qfiles_location + s;
            var popup = 'var wnd=open(\'' + url + '\',\'\',\'\',status=1,resizable=1,menubar=0,scrollbars=1,width=' + w + ',height=' + h + ',left=' + ((screen.width - w) / 2) + ',top=' + ((screen.height - h) / 2) + ');wnd.focus();';
            document.write('<a href="javascript:' + popup + '"><img border="0" src="' + window.qfiles_location + s2 + '" align="absmiddle" style="cursor:pointer" alt="' + (hint || '') + '"></a> ');
        };

        window.ShowPictureQ3WH = function(s, s2, s3, hint, w, h) {
            window.ShowPictureQ2WH(s, s2, hint, w, h);
        };

        window.ShowPictureQ2 = function(s, s2, hint) {
            window.ShowPictureQ2WH(s, s2, hint, 600, 400);
        };

        window.invertImage = function(img, s, s2) {
            if (img.src.indexOf(s) > 0) img.src = window.qfiles_location + s2;
            else img.src = window.qfiles_location + s;
        };

        window.ShowPictureQ3 = function(s, s2, hint) {
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

<body id="group_{{ $group->id ?? 'unknown' }}">
<div class="container preview-page">
    <div class="header">
        <b>Тренировочная работа в формате ОГЭ по МАТЕМАТИКЕ</b>
        <br>
        <br>

        9 КЛАСС
        <br>
        <br>
        Дата: ___ ___ 2023 г.
        <br>
        Вариант №: ___
        <br>
        Выполнена: ФИО_________________________________
        <br><br>
        <b>Инструкция по выполнению работы</b>
        <br>
        <div class="instruction">
            Работа состоит из двух частей, включающих в себя 25 заданий. Часть 1 содержит
            19 заданий, часть 2 содержит 6 заданий с развёрнутым ответом.
            На выполнение работы по математике отводится 3 часа 55 минут (235 минут).
            Ответы к заданиям 7 и 13 запишите в виде одной цифры, которая соответствует
            номеру правильного ответа.
            Для остальных заданий части 1 ответом является число или последовательность
            цифр. Если получилась обыкновенная дробь, ответ запишите в виде десятичной.
            Решения заданий части 2 и ответы к ним запишите на отдельном листе бумаги.
            Задания можно выполнять в любом порядке. Текст задания переписывать не надо,
            необходимо только указать его номер.
            Сначала выполняйте задания части 1. Начать советуем с тех заданий, которые
            вызывают у вас меньше затруднений, затем переходите к другим заданиям. Для
            экономии времени пропускайте задание, которое не удаётся выполнить сразу, и
            переходите к следующему. Если у вас останется время, вы сможете вернуться к
            пропущенным заданиям.
            При выполнении части 1 все необходимые вычисления, преобразования
            выполняйте в черновике. <b>Записи в черновике, а также в тексте контрольных
                измерительных материалов не учитываются при оценивании работы.</b>
            Если задание содержит рисунок, то на нём непосредственно в тексте работы
            можно выполнять необходимые вам построения. Рекомендуем внимательно читать
            условие и проводить проверку полученного ответа.
            При выполнении работы вы можете воспользоваться справочными материалами,
            выданными вместе с вариантом КИМ, и линейкой.
            Баллы, полученные вами за выполненные задания, суммируются. Постарайтесь
            выполнить как можно больше заданий и набрать наибольшее количество баллов.
            После завершения работы проверьте, чтобы ответ на каждое задание был записан
            под правильным номером.

        </div>
        <br><br>
        Желаем успеха!
    </div>
    <div class="page-break"></div>
    @foreach ($tasks as $t)
        <div class="task-content">

            <div class="task-title">{{ optional($t->group)->formatted_title ?? '№' }}</div>

            <div style="display: flex; flex-direction: column; gap: 5px; width:100%">
                <div class="task-content">
                    <div style="width: 100%;">
                        {!! ($questionHtmlMap[$t->id] ?? $t->question) !!}

                    </div>
                </div>
                <div class="flex flex-col gap-1 border border-1 border-solid"
style="display: flex; flex-direction: column; gap: 5px; border: 1px solid;

 width: 100%">
                    <p>
                        Ответ:
                    </p>
                    <div id="answer_kim" style="display: inline-flex; gap: 5px; width: 100%">

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
                                <div
                                    style="border: 0px solid #000; min-width: 150px; padding-bottom: 0px; border-bottom: 1px solid #000; line-height: 1.5;">
                                    {!! $ans !!}
                                </div>
                            @else
                                ___________________________
                            @endif
                        @else
                            ___________________________
                        @endif
                    </div>
                </div>
            </div>

        </div>
        <br><br>

    @endforeach


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

        (function() {
            function hideEmptyElements() {
                // Скрываем пустые параграфы MsoNormal
                document.querySelectorAll('p.MsoNormal').forEach(function(p) {
                    // Не скрывать элементы, содержащие изображения или другие медиа/MathML
                    if (p.querySelector('img, picture, svg, math, video, iframe, object, embed')) return;

                    var text = (p.textContent || '').replace(/\u00A0/g, '').trim();
                    if (text.length === 0) {
                        // Клонируем и удаляем пустые вложенные теги (например пустые span с &nbsp;)
                        var temp = p.cloneNode(true);
                        temp.querySelectorAll('*').forEach(function(el) {
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
                // Скрываем пустые strong элементы
                document.querySelectorAll('strong').forEach(function(strong) {
                    var text = (strong.textContent || '')
                        .replace(/\u00A0/g, '') // убрать &nbsp;
                        .replace(/\s/g, '')     // убрать все пробелы
                        .trim();
                    if (text.length === 0) {
                        strong.style.display = 'none';
                    }
                });
            }

            if (document.readyState !== 'loading') hideEmptyElements();
            else document.addEventListener('DOMContentLoaded', hideEmptyElements);
            if (MathJax && MathJax.Hub && MathJax.Hub.Queue) {
                MathJax.Hub.Queue(hideEmptyElements);
            }
        })();
    </script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Проверяем, что это группа 132
        const bodyId = document.body.id;
        const isGroup132 = bodyId === 'group_132' || bodyId === 'group_133' || bodyId === 'group_134' || bodyId === 'group_26' || bodyId === 'group_28' || bodyId === 'group_29';

        const answer = document.getElementById('answer_kim');
        const answer_p = answer.getElementsByTagName('p');
        const answer_is_already_visible = [];
        for (let i = 0; i < answer_p.length; i++) {
            const answer_div = answer_p[i];
            const isIncludeAnswer = answer_div.textContent.includes('Ответ:');
            answer_div.style.display = isIncludeAnswer ? 'none' : 'block';
            if(answer_is_already_visible.length > 0 && !answer_div.textContent.includes('ОТВЕТ:') ) {
                answer_div.style.display = 'none';
            }
            if(isIncludeAnswer) {
                answer_is_already_visible.push(answer_div);
            }
            // Заменяем "ОТВЕТ:" на "Ответ:" в тексте после проверки
            if(answer_div.innerHTML.includes('ОТВЕТ:')) {
                answer_div.innerHTML = answer_div.innerHTML.replace(/ОТВЕТ:/g, 'Ответ:');
            }
        }

        // Для группы 132 удаляем "ОТВЕТ:" из ответов, оставляя только число
        if (isGroup132) {
            const answerDivs = answer.querySelectorAll('div');
            answerDivs.forEach(function(div) {
                let text = div.textContent || div.innerText || '';
                // Удаляем "ОТВЕТ:" или "Ответ:" и оставляем только число
                text = text.replace(/ОТВЕТ:\s*/gi, '').replace(/Ответ:\s*/gi, '').trim();
                // Если остался только число, заменяем содержимое
                if (/^\d+$/.test(text.trim())) {
                    div.textContent = text.trim();
                } else {
                    // Если есть другой текст, просто удаляем "ОТВЕТ:" или "Ответ:"
                    div.innerHTML = div.innerHTML.replace(/ОТВЕТ:\s*/gi, '').replace(/Ответ:\s*/gi, '');
                }
            });
        }
    });
</script>


</div>
</body>
