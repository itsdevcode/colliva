@props(['name'])
@error($name)<span id="error-{{ str_replace('.', '-', $name) }}" class="field-error">{{ $message }}</span>@enderror
