@props(['label', 'name', 'options' => [], 'selected' => old($name)])

<div>
    <label class="block font-medium mb-1">{{ $label }}</label>
    <select name="{{ $name }}" {{ $attributes->merge(['class' => 'w-full border-gray-300 rounded shadow-sm']) }}>
        @foreach ($options as $option)
            <option value="{{ $option }}" @if($selected == $option) selected @endif>{{ $option }}</option>
        @endforeach
    </select>
    @error($name)
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
