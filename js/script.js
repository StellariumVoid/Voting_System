document.addEventListener('DOMContentLoaded', () => {
    const wrapper = document.querySelector('.logo-wrapper');
    if (!wrapper) return;

    if (document.body.classList.contains('login-page') || 
        document.body.classList.contains('register-page')) {
        setTimeout(() => {
            wrapper.classList.add('animate');
        }, 300); // delay for animation
    }
});
