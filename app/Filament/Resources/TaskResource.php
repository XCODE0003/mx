<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Browsershot\Browsershot;
use ZipArchive;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $modelLabel = 'Задание';

    protected static ?string $pluralModelLabel = 'Задания';

    protected static ?string $navigationLabel = 'Задания';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\TextInput::make('article_id')
                            ->label('Номер задания')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('subject_id')
                            ->label('Предмет')
                            ->relationship('subject', 'name')
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('mark')
                            ->label('Группа/Номер')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('blank_id')
                            ->label('ID бланка')
                            ->maxLength(64),
                        Forms\Components\TextInput::make('question_id')
                            ->label('ID вопроса')
                            ->maxLength(64),
                    ])->columns(2),

                Forms\Components\Section::make('Содержание задания')
                    ->schema([
                        Forms\Components\Textarea::make('question')
                            ->label('Текст вопроса')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('response')
                            ->label('Правильный ответ')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('additional_text')
                            ->label('Дополнительный текст')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Настройки ответа')
                    ->schema([
                        Forms\Components\Select::make('type_answer')
                            ->label('Тип ответа')
                            ->options([
                                'text' => 'Текстовый',
                                'number' => 'Числовой',
                                'table' => 'Таблица',
                                'detailed' => 'Развернутый',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('table_answer')
                            ->label('Ответ в виде таблицы')
                            ->default(false),
                        Forms\Components\TextInput::make('count_columns')
                            ->label('Количество колонок')
                            ->numeric()
                            ->visible(fn ($get) => $get('table_answer')),
                    ])->columns(3),

                Forms\Components\Section::make('Изображения и файлы')
                    ->schema([
                        Forms\Components\Textarea::make('image')
                            ->label('Изображения (JSON)')
                            ->helperText('Массив URL изображений в формате JSON')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('additional_files')
                            ->label('Дополнительные файлы (JSON)')
                            ->helperText('Массив дополнительных файлов в формате JSON')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('article_id')
                    ->label('Номер')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Предмет')
                    ->sortable(),
                Tables\Columns\TextColumn::make('mark')
                    ->label('Группа')
                    ->sortable(),
                Tables\Columns\TextColumn::make('question')
                    ->label('Вопрос')
                    ->limit(50),
                Tables\Columns\TextColumn::make('response')
                    ->label('Ответ')
                    ->limit(30),
                Tables\Columns\TextColumn::make('type_answer')
                    ->label('Тип ответа')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'text' => 'gray',
                        'number' => 'info',
                        'table' => 'warning',
                        'detailed' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('table_answer')
                    ->label('Таблица')
                    ->boolean(),
                Tables\Columns\TextColumn::make('blank_id')
                    ->label('Бланк')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('question_id')
                    ->label('ID вопроса')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('subject_id')
                    ->label('Предмет')
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('type_answer')
                    ->label('Тип ответа')
                    ->options([
                        'text' => 'Текстовый',
                        'number' => 'Числовой',
                        'table' => 'Таблица',
                        'detailed' => 'Развернутый',
                    ])
                    ->multiple(),
                Tables\Filters\TernaryFilter::make('table_answer')
                    ->label('Ответ в виде таблицы')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('preview')
                    ->label('Предпросмотр')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Task $record): string => route('tasks.view', $record->id))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('downloadPdf')
                    ->label('ZIP')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Task $record) {
                        return static::generateTaskPdf($record);
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'view' => Pages\ViewTask::route('/{record}'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }

    protected static function generateTaskPdf(Task $task)
    {
        $task->load(['subject', 'blankText', 'group']);
        
        $tasks = collect([$task]);
        
        // Создаем временную директорию
        $tempDir = sys_get_temp_dir() . '/task_' . $task->id . '_' . time();
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        
        $baseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $task->subject?->class_name ?? 'task');
        
        // Подготовка HTML с инлайн изображениями
        $questionHtmlMap = [$task->id => static::inlineImages($task->question)];
        
        // Генерируем PDF с заданием (без ответов)
        $htmlNoAnswers = view('pdf.task', [
            'task' => $task,
            'tasks' => $tasks,
            'subject' => $task->subject,
            'questionHtmlMap' => $questionHtmlMap,
            'withAnswers' => false,
        ])->render();
        
        $pdfNoAnswers = $tempDir . '/VARIANT_' . $baseName . '.pdf';
        static::savePdf($htmlNoAnswers, $pdfNoAnswers);
        
        // Генерируем PDF с ответами
        $htmlAnswers = view('pdf.task', [
            'task' => $task,
            'tasks' => $tasks,
            'subject' => $task->subject,
            'questionHtmlMap' => $questionHtmlMap,
            'withAnswers' => true,
        ])->render();
        
        $pdfAnswers = $tempDir . '/ANSWER_' . $baseName . '.pdf';
        static::savePdf($htmlAnswers, $pdfAnswers);
        
        // Создаем ZIP архив
        $zipPath = $tempDir . '/task_' . $task->article_id . '_' . time() . '.zip';
        $zip = new ZipArchive();
        
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            if (is_file($pdfNoAnswers)) {
                $zip->addFile($pdfNoAnswers, basename($pdfNoAnswers));
            }
            if (is_file($pdfAnswers)) {
                $zip->addFile($pdfAnswers, basename($pdfAnswers));
            }
            $zip->close();
        }
        
        // Возвращаем ZIP для скачивания и удаляем временные файлы
        return response()->download($zipPath, basename($zipPath))->deleteFileAfterSend(true);
    }
    
    protected static function savePdf(string $html, string $path): void
    {
        Browsershot::html($html)
            ->format('A4')
            ->margins(15, 15, 20, 15)
            ->setDelay(1000)
            ->waitUntilNetworkIdle()
            ->setDelay(5000)
            ->timeout(300)
            ->noSandbox()
            ->setOption('args', [
                '--allow-file-access-from-files',
                '--disable-web-security',
            ])
            ->savePdf($path);
    }
    
    protected static function inlineImages(?string $html): string
    {
        if (!$html) return '';
        
        return preg_replace_callback('/<img[^>]*src=["\']([^"\']+)["\'][^>]*>/i', function ($m) {
            $src = $m[1] ?? '';
            $resolved = static::resolveImageSrc($src);
            return str_replace($src, $resolved, $m[0]);
        }, $html);
    }
    
    protected static function resolveImageSrc(string $src): string
    {
        if ($src === '' || str_starts_with($src, 'data:')) {
            return $src;
        }
        if (preg_match('#^https?://#i', $src)) {
            return $src;
        }
        
        $path = ltrim($src, '/');
        
        if (str_starts_with($path, 'public/')) {
            $path = substr($path, 7);
        }
        
        $full = public_path($path);
        if (!is_file($full)) {
            $try = public_path('docs/' . ltrim($path, '/'));
            if (is_file($try)) {
                $full = $try;
            }
        }
        
        if (is_file($full)) {
            $ext = strtolower(pathinfo($full, PATHINFO_EXTENSION));
            $map = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'svg' => 'image/svg+xml',
            ];
            $mime = $map[$ext] ?? (@mime_content_type($full) ?: 'image/jpeg');
            $data = @file_get_contents($full);
            if ($data !== false) {
                return 'data:' . $mime . ';base64,' . base64_encode($data);
            }
        }
        
        $abs = url('/' . ltrim($path, '/'));
        $httpData = @file_get_contents($abs);
        if ($httpData !== false) {
            $ext = strtolower(pathinfo(parse_url($abs, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
            $map = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'svg' => 'image/svg+xml',
            ];
            $mime = $map[$ext] ?? 'image/jpeg';
            return 'data:' . $mime . ';base64,' . base64_encode($httpData);
        }
        
        return $abs;
    }
}
