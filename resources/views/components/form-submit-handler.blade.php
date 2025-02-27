{{-- 
    Form Submit Handler
    Prevents form spamming by handling Enter key presses and button clicks
    
    Usage:
    1. Add the data-prevent-spam="true" attribute to any form you want to protect
    2. Optionally specify the submit button with data-submit-btn selector
    3. Include this component once in your main layout or page file
    
    Example:
    <form id="myForm" data-prevent-spam="true" data-submit-btn="#customSubmitBtn">
        <!-- form fields -->
        <button id="customSubmitBtn" type="submit">Submit</button>
    </form>
    
    @include('components.form-submit-handler')
--}}

<script>
    $(document).ready(function() {
        // Target all forms with data-prevent-spam attribute
        const $forms = $('form[data-prevent-spam="true"]');

        // Process each form that needs spam protection
        $forms.each(function() {
            const $form = $(this);

            // Find submit button - either from data attribute or default to first submit button
            const submitBtnSelector = $form.data('submit-btn') || '[type="submit"]';
            const $submitBtn = $form.find(submitBtnSelector);

            // Store original button text
            const originalBtnText = $submitBtn.html();

            // Set default spinner HTML if not specified on the form
            const spinnerHtml = $form.data('spinner-html') ||
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

            // Track form submission state
            let isSubmitting = false;

            // Set form ID for identification in event handlers
            // If form doesn't have ID, generate one
            if (!$form.attr('id')) {
                $form.attr('id', 'form-' + Math.random().toString(36).substr(2, 9));
            }
            const formId = $form.attr('id');

            // Handle form submission (both button click and Enter key)
            $form.on('submit', function(e) {
                // Prevent multiple submissions
                if (isSubmitting) {
                    e.preventDefault();
                    return false;
                }

                if (this.checkValidity()) {
                    // Prevent default browser submission temporarily
                    e.preventDefault();

                    // Mark as submitting to prevent multiple submissions
                    isSubmitting = true;

                    // Disable button and show spinner
                    $submitBtn.prop('disabled', true).html(spinnerHtml);

                    // Store form reference
                    const form = this;

                    // Delay form submission slightly to ensure spinner is visible
                    setTimeout(function() {
                        // Use native form submission to ensure spinner stays visible
                        // during page navigation
                        form.submit();

                        // Ensure button stays disabled with spinner
                        $submitBtn.prop('disabled', true).html(spinnerHtml);

                        // Set a flag in localStorage to indicate form was submitted
                        try {
                            localStorage.setItem('formSubmitted-' + formId, 'true');
                        } catch (e) {
                            // localStorage might be disabled, ignore errors
                        }
                    }, 50); // Small delay to ensure UI updates before submission
                } else {
                    e.preventDefault();
                    $(this).addClass('was-validated');
                    $(this).find(':invalid').first().focus();
                }
            });

            // Handle Enter key in any input field
            $form.find('input:not([type="submit"]):not([type="button"]):not([type="reset"])').on('keypress', function(e) {
                if (e.which === 13 || e.keyCode === 13) {
                    e.preventDefault();

                    // Only trigger submit if not already submitting
                    if (!isSubmitting) {
                        $form.submit();
                    }

                    return false;
                }
            });

            // Reset this specific form when its modal is closed
            const $parentModal = $form.closest('.modal');
            if ($parentModal.length) {
                $parentModal.on('hidden.bs.modal', function() {
                    if (!isSubmitting) {
                        $submitBtn.prop('disabled', false).html(originalBtnText);
                        $form.removeClass('was-validated');
                    }
                });
            }

            // Check for previously submitted form state
            try {
                if (localStorage.getItem('formSubmitted-' + formId) === 'true') {
                    localStorage.removeItem('formSubmitted-' + formId);
                }
            } catch (e) {
                // localStorage might be disabled, ignore errors
            }
        });
    });
</script>
