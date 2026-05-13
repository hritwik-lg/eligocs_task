<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // When updating, ignore the current tenant's own values
        $tenantId = $this->route('tenant')?->id;

        return [
            'name'   => ['required', 'string', 'max:255'],
            'slug'   => [
                'nullable',
                'string',
                'max:63',
                'regex:/^[a-z0-9\-]+$/',
                Rule::unique('tenants', 'slug')->ignore($tenantId),
            ],
            'email'  => [
                'required',
                'email',
                'max:255',
                Rule::unique('tenants', 'email')->ignore($tenantId),
            ],
            'phone'  => ['nullable', 'string', 'max:20'],
            'domain' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('tenants', 'domain')->ignore($tenantId),
            ],
            'status' => ['nullable', Rule::in(['active', 'inactive', 'suspended'])],
        ];
    }

    /**
     * Auto-generate slug from name if not provided.
     */
    protected function prepareForValidation(): void
    {
        if (empty($this->slug) && ! empty($this->name)) {
            $this->merge(['slug' => Str::slug($this->name)]);
        }
    }
}
