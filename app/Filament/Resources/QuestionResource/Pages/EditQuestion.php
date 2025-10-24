<?php

namespace App\Filament\Resources\QuestionResource\Pages;

use App\Filament\Resources\QuestionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditQuestion extends EditRecord
{
    protected static string $resource = QuestionResource::class;

    protected function beforeSave(): void
    {
        $data = $this->form->getState();

        $type = $data['type'] ?? null;
        $choices = collect($data['choices'] ?? [])->filter(fn($c) => isset($c['text']) && $c['text'] !== '');
        $correctCount = $choices->where('is_correct', true)->count();

        if (in_array($type, ['single_choice','true_false'])) {
            if ($choices->count() < 2) {
                throw ValidationException::withMessages([
                    'choices' => 'برای سوال تک‌گزینه‌ای/صحیح‌غلط حداقل دو گزینه لازم است.',
                ]);
            }
            if ($correctCount !== 1) {
                throw ValidationException::withMessages([
                    'choices' => 'برای سوال تک‌گزینه‌ای/صحیح‌غلط باید دقیقاً یک گزینه صحیح باشد.',
                ]);
            }
        }

        if ($type === 'multi_choice') {
            if ($choices->count() < 2) {
                throw ValidationException::withMessages([
                    'choices' => 'برای سوال چندگزینه‌ای حداقل دو گزینه لازم است.',
                ]);
            }
            if ($correctCount < 1) {
                throw ValidationException::withMessages([
                    'choices' => 'برای سوال چندگزینه‌ای حداقل یک گزینه باید صحیح باشد.',
                ]);
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\SaveAction::make()
                ->label('ذخیره')
                ->color('primary')
                ->keyBindings(['mod+s']),
            Actions\DeleteAction::make(),
        ];
    }
}
