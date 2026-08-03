@props(['status'])

@php
    use App\Enums\TableStatus;

    $label = $status->label();
    $toneClass = match ($status->tone()) {
        'success' => 'bg-green-100 text-green-800',
        'warning' => 'bg-amber-100 text-amber-800',
        'info' => 'bg-blue-100 text-blue-800',
        default => 'bg-neutral-100 text-neutral-700',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-semibold {$toneClass}"]) }}>
    {{ $label }}
</span>
