<!DOCTYPE html>
<html lang="ru" xmlns:m="http://www.w3.org/1998/Math/MathML">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Задание </title>
    <base href="{{ url('/') }}/">

    <style>
        body {
            font-family: 'Times New Roman', serif;
            margin: 20px;
            line-height: 1.6;
            color: #000;
        }
        /* Принудительно Times New Roman для всего содержимого */
        body, * { font-family: 'Times New Roman', serif !important; }

        .container {
            width: 100%;
            max-width: 800px;
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
            width: 210mm;
            min-height: 297mm;
            margin: 5mm auto;
            padding: 15mm;
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
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.5/MathJax.js?config=TeX-MML-AM_CHTML"></script>
    <style>
        .header {
            text-align: center;
        }

        form>table>tbody>tr>td {
            padding: 0 !important;
            margin: 0 !important;
        }

        .instruction {
            text-wrap: wrap;
            text-align: justify;
        }

        /* Компактная вёрстка задания: текст слева, номер справа в квадрате */
        .task-block { margin-bottom: 12px; }
        .task-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }
        .task-body {
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .task-number {
            font-weight: 700;
            width: 31px;
            height: 31px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #000;
            flex-shrink: 0;
        }

        /* .task-title больше не используется */

        .MsoNormalTable,
        .MsoTableGrid,
        table {
            margin: 0;
            padding: 0;
        }

        .submit-outblock {
            display: none;
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
</head>

<body>
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
            <div class="task-block">
                <div class="task-row">
                    <div class="task-body">
                        <div class="task-html">
                            {!! ($questionHtmlMap[$t->id] ?? $t->question) !!}
                        </div>
                        <div style="display: inline-flex; gap: 6px; align-items: baseline;">
                            <span>Ответ:</span>
                            @if ($withAnswers)
                                @php $ans = trim(strip_tags($t->response ?? '')); @endphp
                                @if($ans !== '')
                                    <span style="min-width: 150px; padding-bottom: 0px; border-bottom: 1px solid #000; line-height: 1.5; display: inline-block;">{{ $ans }}</span>
                                @else
                                    <span>___________________________</span>
                                @endif
                            @else
                                <span>___________________________</span>
                            @endif
                        </div>
                    </div>
                    <div class="task-number">{{ optional($t->group)->formatted_title ?? '№' }}</div>
                </div>
            </div>
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
        </script>
    </div>
</body>