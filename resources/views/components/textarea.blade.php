@props(['label', 'name'])

<div>
    <label class="block font-medium mb-1">{{ $label }}</label>
    <textarea name="{{ $name }}" {{ $attributes->merge(['class' => 'w-full border-gray-300 rounded shadow-sm']) }}>{{ old($name, $slot) }}</textarea>
    @error($name)
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
