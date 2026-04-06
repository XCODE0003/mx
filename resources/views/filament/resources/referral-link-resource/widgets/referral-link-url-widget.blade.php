<x-filament-widgets::widget>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <x-filament::section>
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold">Реферальная ссылка</h3>
            </div>
            
            <div class="flex items-center gap-2" x-data="{ 
                copied: false,
                copyToClipboard() {
                    const text = '{{ $record->url }}';
                    navigator.clipboard.writeText(text).then(() => {
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2000);
                    }).catch(err => {
                        console.error('Ошибка копирования: ', err);
                        alert('Ошибка при копировании ссылки');
                    });
                }
            }">
                <div class="flex-1 bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                    <a href="{{ $record->url }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline font-mono text-lg break-all">
                        {{ $record->url }}
                    </a>
                </div>
                <button 
                    type="button" 
                    @click="copyToClipboard"
                    :class="copied ? 'bg-green-600 hover:bg-green-500' : 'bg-custom-600 hover:bg-custom-500'"
                    class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm text-white focus-visible:ring-custom-500/50 dark:focus-visible:ring-custom-400/50 fi-ac-action fi-ac-btn-action"
                    style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600);"
                >
                    <svg x-show="!copied" class="fi-btn-icon h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                    </svg>
                    <svg x-show="copied" class="fi-btn-icon h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" x-cloak>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span x-text="copied ? 'Скопировано!' : 'Копировать'"></span>
                </button>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-2">
                <div class="bg-blue-50 dark:bg-blue-950 rounded-lg p-3">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Код</div>
                    <div class="text-lg font-semibold font-mono">{{ $record->code }}</div>
                </div>
                <div class="bg-green-50 dark:bg-green-950 rounded-lg p-3">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Кликов</div>
                    <div class="text-lg font-semibold">{{ number_format($record->clicks) }}</div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
