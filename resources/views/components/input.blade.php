@props(['type' => 'text', 'name' => '', 'value' => ''])

<input type="{{ $type }}" 
       name="{{ $name }}" 
       value="{{ $value }}"
       {{ $attributes->merge(['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500']) }}>