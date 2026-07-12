/**
 * Global form-submit loading state.
 * Automatically disables the submit button and shows a spinner
 * whenever ANY <form> on the page is submitted.
 *
 * To opt a form OUT of this behavior (e.g. instant client-side actions),
 * add the attribute: data-no-loading
 */
document.addEventListener('submit', function (e) {
    const form = e.target;

    if (!(form instanceof HTMLFormElement)) return;
    if (form.hasAttribute('data-no-loading')) return;

    const btn = form.querySelector('button[type="submit"]');
    if (!btn || btn.disabled) return;

    // Save original content in case we need it back (e.g. validation fails client-side)
    btn.dataset.originalHtml = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML =
        '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Processing...';
});