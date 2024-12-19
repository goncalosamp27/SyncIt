document.addEventListener('DOMContentLoaded', () => {
    const successMessage = document.querySelector('.success');
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.transition = 'opacity 0.5s';
            successMessage.style.opacity = '0';
            setTimeout(() => successMessage.remove(), 500); 
        }, 3000); 
    }
});