<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tracking para formularios con IDs específicos
    const forms = document.querySelectorAll('form[id]');
    
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            // Obtener información del formulario
            const formId = form.id;
            const formAction = form.action;
            const formMethod = form.method;
            const formClasses = form.className;
            
            // Obtener todos los campos del formulario
            const formFields = form.querySelectorAll('input, select, textarea');
            const fieldNames = Array.from(formFields).map(field => field.name || field.id).filter(Boolean);
            
            // Enviar evento a Google Tag Manager
            if (typeof dataLayer !== 'undefined') {
                dataLayer.push({
                    'event': 'form_submit',
                    'form_id': formId,
                    'form_action': formAction,
                    'form_method': formMethod,
                    'form_classes': formClasses,
                    'form_fields': fieldNames,
                    'form_url': window.location.href,
                    'form_path': window.location.pathname
                });
            }
            
            // También enviar a Google Analytics si está disponible
            if (typeof gtag !== 'undefined') {
                gtag('event', 'form_submit', {
                    'form_id': formId,
                    'form_action': formAction,
                    'form_method': formMethod,
                    'form_classes': formClasses,
                    'form_url': window.location.href,
                    'form_path': window.location.pathname
                });
            }
        });
    });
    
    // Tracking para envíos de formularios sin ID (fallback)
    const allForms = document.querySelectorAll('form:not([id])');
    allForms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const formAction = form.action;
            const formMethod = form.method;
            const formClasses = form.className;
            
            if (typeof dataLayer !== 'undefined') {
                dataLayer.push({
                    'event': 'form_submit',
                    'form_action': formAction,
                    'form_method': formMethod,
                    'form_classes': formClasses,
                    'form_url': window.location.href,
                    'form_path': window.location.pathname
                });
            }
            
            if (typeof gtag !== 'undefined') {
                gtag('event', 'form_submit', {
                    'form_action': formAction,
                    'form_method': formMethod,
                    'form_classes': formClasses,
                    'form_url': window.location.href,
                    'form_path': window.location.pathname
                });
            }
        });
    });
});
</script>
