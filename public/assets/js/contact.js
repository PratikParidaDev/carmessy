/**
 * Contact Form JavaScript
 * Handles form validation and submission
 */

document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    const messageField = document.getElementById('message');
    const maxMessageLength = 2000;

    if (!contactForm) {
        return;
    }

    // Character counter for message field
    if (messageField) {
        const messageCounter = document.createElement('small');
        messageCounter.className = 'form-text text-muted mt-1';
        messageCounter.id = 'messageCounter';
        messageField.parentNode.appendChild(messageCounter);

        function updateCounter() {
            const currentLength = messageField.value.length;
            const remaining = maxMessageLength - currentLength;
            messageCounter.textContent = `${currentLength} / ${maxMessageLength} characters`;
            
            if (remaining < 50) {
                messageCounter.classList.add('text-warning');
                messageCounter.classList.remove('text-danger');
            } else if (remaining < 0) {
                messageCounter.classList.add('text-danger');
                messageCounter.classList.remove('text-warning');
            } else {
                messageCounter.classList.remove('text-warning', 'text-danger');
            }
        }

        messageField.addEventListener('input', updateCounter);
        updateCounter(); // Initial count
    }

    // Form submission validation
    contactForm.addEventListener('submit', function(e) {
        const message = messageField ? messageField.value : '';
        
        if (message.length > maxMessageLength) {
            e.preventDefault();
            alert('Message cannot exceed ' + maxMessageLength + ' characters.');
            messageField.focus();
            return false;
        }

        // Basic HTML5 validation will handle other required fields
        // Additional custom validation can be added here if needed
    });
});

