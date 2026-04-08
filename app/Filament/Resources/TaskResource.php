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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

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
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
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
        $task->load(['subject', 'blankText']);
        
        $html = view('pdf.task', [
            'tasks' => collect([$task]),
            'subject' => $task->subject,
            'withAnswers' => true,
        ])->render();

        $pdf = Pdf::loadHTML($html)
            ->setPaper('a4')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
            ]);

        $filename = 'task_' . $task->article_id . '_' . time() . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }
}
