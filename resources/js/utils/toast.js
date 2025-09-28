// Lightweight fallback toast helpers (no external deps)

export const showToast = (message, type, params = {}) => {
    if (type === 'error') {
        console.error(message);
    } else if (type === 'success') {
        console.log(message);
    } else {
        console.info(message);
    }
};

export const showErrorToast = (message) => {
    showToast(
        `<b>Ошибка!</b>\n${message}`,
        'error',
        {}
    );
};

export const showSuccessToast = (message) => {
    showToast(
        `<b>Успешно!</b>\n${message}`,
        'success',
        {}
    );
};

export const renderErrorToast = (errorMessages = {}) => {
    if (typeof errorMessages === 'object' && errorMessages !== null) {
        if (errorMessages.value && typeof errorMessages.value === 'object') {
            errorMessages = errorMessages.value;
        }

        Object.values(errorMessages).forEach(fieldErrors => {
            if (Array.isArray(fieldErrors)) {
                fieldErrors.forEach(message => {
                    showErrorToast(message);
                });
            } else if (typeof fieldErrors === 'string') {
                showErrorToast(fieldErrors);
            }
        });
    } else if (Array.isArray(errorMessages)) {
        errorMessages.forEach(message => {
            showErrorToast(message);
        });
    } else if (typeof errorMessages === 'string') {
        showErrorToast(errorMessages);
    }
};

export const renderSuccessToasts = (successMessages = []) => {
    if (Array.isArray(successMessages)) {
        successMessages.forEach(message => {
            showSuccessToast(message);
        });
    } else if (typeof successMessages === 'string') {
        showSuccessToast(successMessages);
    }
};