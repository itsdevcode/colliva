// Native dialogs provide focus containment, Escape handling, and focus restoration.
// Close them before Livewire replaces a page so scroll locks cannot outlive it.
document.addEventListener('livewire:navigating', () => {
    document.querySelectorAll('dialog[data-colliva-dialog][open]').forEach(dialog => dialog.close());
});

function updateKeyboardState() {
    const viewport = window.visualViewport;
    const focused = document.activeElement;
    const editing = focused?.matches('input:not([type="checkbox"]):not([type="radio"]):not([type="button"]):not([type="submit"]), textarea, [contenteditable="true"]');
    const compact = window.matchMedia('(max-width: 900px)').matches;
    document.body.toggleAttribute('data-keyboard-open', compact && editing && (!viewport || window.innerHeight - viewport.height > 120 || window.matchMedia('(pointer: coarse)').matches));
}

window.visualViewport?.addEventListener('resize', updateKeyboardState, { passive: true });
document.addEventListener('focusin', updateKeyboardState);
document.addEventListener('focusout', () => requestAnimationFrame(updateKeyboardState));
document.addEventListener('livewire:navigated', updateKeyboardState);


let supportDiscoveryHeaderObserver;

function observeSupportDiscoveryHeader() {
    supportDiscoveryHeaderObserver?.disconnect();
    document.documentElement.style.removeProperty('--colliva-header-height');
    if (!document.body.hasAttribute('data-support-discovery')) {
        return;
    }

    const header = document.querySelector('.site-header');
    if (!header) {
        return;
    }

    const measureHeader = () => {
        document.documentElement.style.setProperty('--colliva-header-height', `${header.getBoundingClientRect().height}px`);
    };
    measureHeader();
    supportDiscoveryHeaderObserver = new ResizeObserver(measureHeader);
    supportDiscoveryHeaderObserver.observe(header);
}

observeSupportDiscoveryHeader();
document.addEventListener('livewire:navigated', observeSupportDiscoveryHeader);
document.addEventListener('livewire:navigating', () => supportDiscoveryHeaderObserver?.disconnect());
