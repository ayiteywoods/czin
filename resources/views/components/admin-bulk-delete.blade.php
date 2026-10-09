@props([
    'action',
    'confirm' => 'Delete the selected items? This cannot be undone.',
    'label' => 'Delete selected',
])

<form
    method="POST"
    action="{{ $action }}"
    class="inline"
    @submit.prevent="
        if (selected.length === 0) return;
        if (!confirm(@js($confirm))) return;
        appendSelectedToForm($el);
        $el.submit();
    "
>
    @csrf
    @method('DELETE')
    <button
        type="submit"
        class="inline-flex items-center rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-medium text-brand-red transition hover:bg-red-50 sm:text-sm"
    >
        {{ $label }}
    </button>
</form>
