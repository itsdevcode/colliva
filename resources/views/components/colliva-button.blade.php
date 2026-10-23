@props(['href' => null, 'variant' => 'purple', 'small' => false])
@if($href)
    <a href="{{ $href }}" {{ $attributes->class(['button', 'button-'.$variant, 'button-small' => $small]) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => 'button'])->class(['button', 'button-'.$variant, 'button-small' => $small]) }}>{{ $slot }}</button>
@endif
