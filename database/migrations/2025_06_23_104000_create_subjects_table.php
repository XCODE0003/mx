<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('subject_id', 64)->unique();
            $table->string('name');
            $table->enum('exam_type', ['oge', 'ege']);
            $table->string('class_name')->nullable();
            $table->timestamps();

            $table->index(['exam_type', 'name']);
        });

        $this->insertSubjects();
    }

    private function insertSubjects(): void
    {
        $ogeSubjects = [
            ['subject_id' => '8BBD5C99F37898B6402964AB11955663', 'name' => 'Английский язык', 'exam_type' => 'oge', 'class_name' => 'eng'],
            ['subject_id' => '0E1FA4229923A5CE4FC368155127ED90', 'name' => 'Биология', 'exam_type' => 'oge', 'class_name' => 'bio'],
            ['subject_id' => '0FA4DA9E3AE2BA1547B75F0B08EF6445', 'name' => 'География', 'exam_type' => 'oge', 'class_name' => 'geo'],
            ['subject_id' => '74676951F093A0754D74F2D6E7955F06', 'name' => 'Информатика', 'exam_type' => 'oge', 'class_name' => 'inf'],
            ['subject_id' => '7FF0B02E53DFBCDE4F56B0148BE9A236', 'name' => 'Испанский язык', 'exam_type' => 'oge', 'class_name' => 'esp'],
            ['subject_id' => '3CBBE97571208D9140697A6C2ABE91A0', 'name' => 'История', 'exam_type' => 'oge', 'class_name' => 'his'],
            ['subject_id' => '6B2CD4C77304B2A3478E5A5B61F6899A', 'name' => 'Литература', 'exam_type' => 'oge', 'class_name' => 'lit'],
            ['subject_id' => 'DE0E276E497AB3784C3FC4CC20248DC0', 'name' => 'Математика', 'exam_type' => 'oge', 'class_name' => 'mat'],
            ['subject_id' => 'A2AC67AE354EBC5242C49482CBC13451', 'name' => 'Немецкий язык', 'exam_type' => 'oge', 'class_name' => 'ger'],
            ['subject_id' => 'AE63AB28A2D28E194A286FA5A8EB9A78', 'name' => 'Обществознание', 'exam_type' => 'oge', 'class_name' => 'soc'],
            ['subject_id' => '2F5EE3B12FE2A0EA40B06BF61A015416', 'name' => 'Русский язык', 'exam_type' => 'oge', 'class_name' => 'rus'],
            ['subject_id' => 'B24AFED7DE6AB5BC461219556CCA4F9B', 'name' => 'Физика', 'exam_type' => 'oge', 'class_name' => 'phs'],
            ['subject_id' => '2A4C52ED5AC1ADA644B8BBF169FEC0FC', 'name' => 'Французский язык', 'exam_type' => 'oge', 'class_name' => 'fra'],
            ['subject_id' => '33B3A93C5A6599124B04FB95616C835B', 'name' => 'Химия', 'exam_type' => 'oge', 'class_name' => 'him'],
        ];

        $egeSubjects = [
            ['subject_id' => '4B53A6CB75B0B5E1427E596EB4931A2A', 'name' => 'Английский язык', 'exam_type' => 'ege', 'class_name' => 'eng'],
            ['subject_id' => 'CA9D848A31849ED149D382C32A7A2BE4', 'name' => 'Биология', 'exam_type' => 'ege', 'class_name' => 'bio'],
            ['subject_id' => '20E79180061DB32845C11FC7BD87C7C8', 'name' => 'География', 'exam_type' => 'ege', 'class_name' => 'geo'],
            ['subject_id' => 'B9ACA5BBB2E19E434CD6BEC25284C67F', 'name' => 'Информатика и ИКТ', 'exam_type' => 'ege', 'class_name' => 'inf'],
            ['subject_id' => '8C65A335D93D9DA047C42613F61416F3', 'name' => 'Испанский язык', 'exam_type' => 'ege', 'class_name' => 'esp'],
            ['subject_id' => '068A227D253BA6C04D0C832387FD0D89', 'name' => 'История', 'exam_type' => 'ege', 'class_name' => 'his'],
            ['subject_id' => 'F6298F3470D898D043E18BC680F60434', 'name' => 'Китайский язык', 'exam_type' => 'ege', 'class_name' => null],
            ['subject_id' => '4F431E63B9C9B25246F00AD7B5253996', 'name' => 'Литература', 'exam_type' => 'ege', 'class_name' => 'lit'],
            ['subject_id' => 'E040A72A1A3DABA14C90C97E0B6EE7DC', 'name' => 'Математика. Базовый уровень', 'exam_type' => 'ege', 'class_name' => null],
            ['subject_id' => 'AC437B34557F88EA4115D2F374B0A07B', 'name' => 'Математика. Профильный уровень', 'exam_type' => 'ege', 'class_name' => null],
            ['subject_id' => 'B5963A8D84CF9020461EAE42F37F541F', 'name' => 'Немецкий язык', 'exam_type' => 'ege', 'class_name' => 'ger'],
            ['subject_id' => '756DF168F63F9A6341711C61AA5EC578', 'name' => 'Обществознание', 'exam_type' => 'ege', 'class_name' => 'soc'],
            ['subject_id' => 'AF0ED3F2557F8FFC4C06F80B6803FD26', 'name' => 'Русский язык', 'exam_type' => 'ege', 'class_name' => 'rus'],
            ['subject_id' => 'BA1F39653304A5B041B656915DC36B38', 'name' => 'Физика', 'exam_type' => 'ege', 'class_name' => 'phs'],
            ['subject_id' => '5BAC840990A3AF0A4EE80D1B5A1F9527', 'name' => 'Французский язык', 'exam_type' => 'ege', 'class_name' => 'fra'],
            ['subject_id' => 'EA45D8517ABEB35140D0D83E76F14A41', 'name' => 'Химия', 'exam_type' => 'ege', 'class_name' => 'him'],
        ];

        $subjects = array_merge($ogeSubjects, $egeSubjects);

        foreach ($subjects as $subject) {
            $subject['created_at'] = now();
            $subject['updated_at'] = now();
        }

        DB::table('subjects')->insert($subjects);
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};