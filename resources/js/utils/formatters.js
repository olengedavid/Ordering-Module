export const formatCurrency = (amount, currency = 'KES') => {
    if (amount === null || amount === undefined) return '';
    
    return new Intl.NumberFormat('en-KE', {
        style: 'currency',
        currency: 'KES',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(amount).replace('KES', currency);
};

export const formatNumber = (number, decimals = 2) => {
    if (number === null || number === undefined) return '';
    
    return new Intl.NumberFormat('en-KE', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    }).format(number);
};

export const formatDateOnly = (dateString) => {
    if (!dateString) return '';
    
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('en-KE', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    }).format(date);
};