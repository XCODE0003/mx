<!DOCTYPE html>
<html lang="ru" xmlns:m="http://www.w3.org/1998/Math/MathML">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Задание </title>
    <base href="{{ url('/') }}/">
    <link rel="stylesheet" href="/assets/task.css">

    <style>
        body {
            font-family: 'Times New Roman', serif;
            margin: 20px;
            line-height: 1.6;
            color: #000;
        }

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
        /* .submit-outblock{
            display: none;
        } */
        *, ::after, ::before{
            box-sizing: border-box !important;
        }
        .Distractor{
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
            document.write('<img src="' + window.qfiles_location + s + '" align="middle" border="0" alt="' + (hint || '') + '" vspace="' + vspace + '" style="position:relative; top:' + (h/2 - bl) + 'px;">');
        };

        window.ShowPictureQ2WH = function(s, s2, hint, w, h) {
            if (s.indexOf('.flv') > 0 || s.indexOf('.mp4') > 0 || s.indexOf('.swf') > 0) {
                s = '../../show_media.php?m=' + encodeURIComponent(s) + '&w=' + w + '&h=' + h;
            }
            w = w + 40;
            h = h + 30;
            var url = window.qfiles_location + s;
            var popup = "var wnd=open('" + url + "','','',status=1,resizable=1,menubar=0,scrollbars=1,width=" + w + ",height=" + h + ",left=" + ((screen.width - w)/2) + ",top=" + ((screen.height - h)/2) + ");wnd.focus();";
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

<body>
    <div class="container preview-page">

        <div class="task-content">


            <div style="display: flex; flex-direction: column; gap: 5px;">
                <div class="task-content">
                    <div>
                    @php
                        $inline = function (?string $html) {
                            if (!$html) return '';
                            return preg_replace_callback('/<img[^>]*src=["\']([^"\']+)["\'][^>]*>/i', function ($m) {
                                $src = $m[1] ?? '';
                                if ($src === '' || str_starts_with($src, 'data:')) {
                                    return $m[0];
                                }
                                if (preg_match('#^https?://#i', $src)) {
                                    return $m[0];
                                }
                                $path = ltrim($src, '/');
                                if (str_starts_with($path, 'public/')) {
                                    $path = substr($path, 7);
                                }
                                $full = public_path($path);
                                if (is_file($full)) {
                                    $ext = strtolower(pathinfo($full, PATHINFO_EXTENSION));
                                    $map = [ 'jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','gif'=>'image/gif','webp'=>'image/webp','svg'=>'image/svg+xml' ];
                                    $mime = $map[$ext] ?? (@mime_content_type($full) ?: 'image/jpeg');
                                    $data = @file_get_contents($full);
                                    if ($data !== false) {
                                        return str_replace($src, 'data:'.$mime.';base64,'.base64_encode($data), $m[0]);
                                    }
                                }
                                return str_replace($src, url('/'.ltrim($path, '/')), $m[0]);
                            }, $html);
                        };
                    @endphp
                    {!! $inline($task->question) !!}
                    </div>

                </div>
                Ответ: ___________________________
            </div>

        </div>
        <br><br>




        <script>
     (function () {
        function hideEmptyElements() {
            // Скрываем пустые параграфы MsoNormal
            document.querySelectorAll('p.MsoNormal').forEach(function (p) {
                var text = (p.textContent || '')
                    .replace(/\u00A0/g, '') // убрать &nbsp;
                    .trim();
                if (text.length === 0) {
                    p.style.display = 'none';
                }
            });

            // Скрываем пустые strong элементы
            document.querySelectorAll('strong').forEach(function (strong) {
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
</div>

</body>

</html>
