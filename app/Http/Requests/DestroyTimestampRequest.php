<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\ConfirmationModalRule;
use Illuminate\Foundation\Http\FormRequest;

class DestroyTimestampRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'confirm' => [
                new ConfirmationModalRule(
                    title: 'Bist du dir wirklich sicher?',
                    description: 'Möchtest du diesen Eintrag entfernen?',
                    confirmButtonText: 'Entfernen',
                ),
            ],
        ];
    }
}
