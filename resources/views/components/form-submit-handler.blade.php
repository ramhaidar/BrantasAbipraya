{{-- 
    Form Submit Handler
    Prevents form spamming by handling Enter key presses and button clicks
    
    Usage:
    1. Include this component once in your main layout
    2. All forms will be protected by default
    3. To exclude a form, add data-prevent-spam="false" attribute
    4. Optionally specify the submit button with data-submit-btn selector
    
    Special Cases:
    - Forms with data-has-price-conversion="true" will ensure price fields are properly processed
    
    Example to opt OUT a specific form:
    <form id="unprotectedForm" data-prevent-spam="false">
        <!-- form fields that shouldn't use this behavior -->
    </form>
    
    @include('components.form-submit-handler')
--}}

<script>
    $(document).ready(function() {
        // Target all forms EXCEPT those that explicitly opt out
        const $forms = $('form:not([data-prevent-spam="false"])');

        // Process each form
        $forms.each(function() {
            const $form = $(this);
            const hasPriceConversion = $form.data('has-price-conversion') === true;

            // Find submit button - either from data attribute or default to first submit button
            const submitBtnSelector = $form.data('submit-btn') || '[type="submit"]';
            const $submitBtn = $form.find(submitBtnSelector);

            // Skip this form if it doesn't have a submit button
            if ($submitBtn.length === 0) return;

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

            // Add specific handling for addDataForm
            const isAddDataForm = formId === 'addDataForm';

            // Function to perform custom validation
            function validateForm() {
                let isValid = true;

                // Special handling for forms with custom validation functions
                if (hasPriceConversion && typeof window.validatePriceForm === 'function') {
                    // Use custom validation function if available
                    isValid = window.validatePriceForm($form);
                } else if (formId === 'addDataForm') {
                    // Reset validation state first
                    $form.find('.is-invalid').removeClass('is-invalid');

                    // Validate all required fields
                    $form.find('[required]:enabled').each(function() {
                        if (!$(this).val()) {
                            $(this).addClass('is-invalid');
                            isValid = false;
                        }
                    });

                    // Special validation for Select2 fields
                    ['#id_alat', '#id_saldo'].forEach(function(selector) {
                        const $select = $(selector);
                        if ($select.length && $select.prop('required') && !$select.val()) {
                            $select.next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            isValid = false;
                        }
                    });

                    // Check quantity validation if it's enabled
                    const quantityInput = $('#quantity');
                    if (quantityInput.length && !quantityInput.prop('disabled')) {
                        const max = parseInt(quantityInput.attr('max')) || 0;
                        const min = parseInt(quantityInput.attr('min')) || 1;
                        const value = parseInt(quantityInput.val()) || 0;

                        if (value > max || value < min || !quantityInput.val()) {
                            quantityInput.addClass('is-invalid');
                            isValid = false;
                        }
                    }
                } else {
                    // Standard form validation
                    isValid = $form[0].checkValidity();
                    if (!isValid) {
                        $form.addClass('was-validated');
                    }
                }

                return isValid;
            }

            // Function to focus on first invalid field
            function focusFirstInvalid() {
                const $firstInvalid = $form.find('.is-invalid').first();
                if ($firstInvalid.length) {
                    setTimeout(function() {
                        // For Select2, focus on the search field
                        if ($firstInvalid.hasClass('select2-hidden-accessible')) {
                            $firstInvalid.next('.select2-container').find('.select2-selection').trigger('focus');
                        } else {
                            $firstInvalid.trigger('focus');
                        }
                    }, 10);
                }
            }

            // Common form submission logic
            function submitFormIfValid() {
                // Do nothing if already submitting
                if (isSubmitting) {
                    return false;
                }

                // Handle special price conversion forms
                if (hasPriceConversion) {
                    // Trigger blur on price display field to update hidden field
                    const $hargaDisplay = $form.find('#harga_display');
                    if ($hargaDisplay.length) {
                        $hargaDisplay.blur();
                    }
                }

                // Run validation
                const isValid = validateForm();

                if (isValid) {
                    // Mark as submitting to prevent multiple submissions
                    isSubmitting = true;

                    // Disable button and show spinner
                    $submitBtn.prop('disabled', true).html(spinnerHtml);

                    // Delay form submission slightly to ensure spinner is visible
                    setTimeout(function() {
                        // Set a flag in localStorage to indicate form was submitted
                        try {
                            localStorage.setItem('formSubmitted-' + formId, 'true');
                        } catch (e) {
                            // localStorage might be disabled, ignore errors
                        }

                        // Use native form submission
                        $form[0].submit();
                    }, 50);
                    return true;
                } else {
                    // Focus first invalid field without reloading
                    focusFirstInvalid();
                    return false;
                }
            }

            // Replace original form submission handler
            $form.off('submit').on('submit', function(e) {
                // Always prevent default browser submit to avoid page reload
                e.preventDefault();
                return submitFormIfValid();
            });

            // Handle all inputs including dynamically enabled ones
            $(document).on('keydown', `#${formId} input, #${formId} select, #${formId} textarea`, function(e) {
                // Only handle Enter key
                if (e.which === 13 || e.keyCode === 13) {
                    // Always prevent default to avoid reload
                    e.preventDefault();
                    e.stopPropagation();

                    // Handle special price conversion forms
                    if (hasPriceConversion) {
                        // Trigger blur on price display field to update hidden field
                        const $hargaDisplay = $form.find('#harga_display');
                        if ($hargaDisplay.length) {
                            $hargaDisplay.blur();
                        }
                    }

                    // Submit the form if valid
                    submitFormIfValid();
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
                        $form.find('.is-invalid').removeClass('is-invalid');
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
