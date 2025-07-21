<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateOrderEmployeeRequest extends FormRequest
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
            'employee_id' => 'nullable|exists:users,id',
        ];
    }
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $employeeId = $this->input('employee_id');

            if ($employeeId) {
                $user = User::find($employeeId);

                if (! $user || ! $user->hasRole('employee')) {
                    $validator->errors()->add('employee_id', 'Selected user is not an employee.');
                }
            }
        });
    }
}
