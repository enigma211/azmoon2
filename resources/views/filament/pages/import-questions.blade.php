<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->table }}
        
        <x-filament::section>
            <x-slot name="heading">
                راهنمای فرمت فایل CSV
            </x-slot>
            
            <div class="prose dark:prose-invert max-w-none">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    فایل CSV شما باید دارای ستون‌های زیر به ترتیب باشد:
                </p>
                <ol class="text-sm text-gray-600 dark:text-gray-400 list-decimal mr-5">
                    <li><strong>شماره سوال:</strong> عدد صحیح (مثال: 1، 2، 3، ...)</li>
                    <li><strong>متن سوال:</strong> متن سوال</li>
                    <li><strong>متن گزینه 1:</strong> متن گزینه اول</li>
                    <li><strong>متن گزینه 2:</strong> متن گزینه دوم</li>
                    <li><strong>متن گزینه 3:</strong> متن گزینه سوم</li>
                    <li><strong>متن گزینه 4:</strong> متن گزینه چهارم</li>
                    <li><strong>شماره گزینه صحیح:</strong> عدد بین 1 تا 4</li>
                </ol>
                
                <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <p class="text-sm font-semibold text-blue-800 dark:text-blue-200 mb-2">مثال:</p>
                    <code class="text-xs text-blue-700 dark:text-blue-300 block whitespace-pre">1,"پایتخت ایران کدام شهر است؟","تهران","اصفهان","شیراز","مشهد",1
2,"کدام یک از موارد زیر یک زبان برنامه‌نویسی است؟","HTML","CSS","JavaScript","XML",3</code>
                </div>
                
                <div class="mt-4 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <p class="text-sm font-semibold text-green-800 dark:text-green-200 mb-2">نکته:</p>
                    <p class="text-xs text-green-700 dark:text-green-300">
                        شماره سوال در فایل CSV می‌تواند هر عددی باشد. اگر قبلاً سوالات 1 تا 14 را دستی وارد کرده‌اید، 
                        می‌توانید سوالات جدید را با شماره 15 به بعد در فایل CSV قرار دهید.
                    </p>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
