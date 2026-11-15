/* =========================================================
   FORM SUBMISSION LOADING STATES
   Prevents double-clicks and gives visual feedback
   ========================================================= */
document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            // Find the submit button inside this specific form
            const submitBtn = form.querySelector('button[type="submit"]');
            
            // If there is a submit button and it's not already disabled
            if (submitBtn && !submitBtn.disabled) {
                
                // Save the original text so we can restore it if validation fails (optional)
                const originalText = submitBtn.innerHTML;
                
                // Change the text to "Processing..." and disable it
                submitBtn.innerHTML = '<span class="btn-loader"></span> Processing...';
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.8';
                submitBtn.style.cursor = 'not-allowed';

                // Fallback: If the browser fails to submit for some reason, re-enable after 10 seconds
                setTimeout(() => {
                    if (submitBtn.disabled) {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                        submitBtn.style.opacity = '1';
                        submitBtn.style.cursor = 'pointer';
                    }
                }, 10000);
            }
        });
    });
});