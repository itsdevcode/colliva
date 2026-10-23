@props(['name', 'title'])
<dialog x-ref="{{ $name }}" {{ $attributes->class('colliva-drawer') }} aria-label="{{ $title }}" @click="if ($event.target === $el) $el.close()">
    <div class="drawer-heading">
        <h2>{{ $title }}</h2>
        <button type="button" class="icon-button" @click="$refs.{{ $name }}.close()" aria-label="Close {{ strtolower($title) }}">×</button>
    </div>
    {{ $slot }}
</dialog>
