// Real-time email availability check
const emailInput = document.getElementById('email');
const emailError = document.getElementById('email-error');

if (emailInput) {
    let timer = null;

    emailInput.addEventListener('input', function () {
        clearTimeout(timer);
        const val = this.value.trim();

        if (!val || !val.includes('@')) {
            if (emailError) emailError.textContent = '';
            return;
        }

        timer = setTimeout(async () => {
            try {
                const res  = await fetch(`../controllers/CheckEmail.php?email=${encodeURIComponent(val)}`);
                const data = await res.json();

                if (emailError) {
                    if (data.exists) {
                        emailError.textContent = 'This email is already registered.';
                        emailError.style.color = '#e05050';
                        emailInput.classList.add('is-error');
                    } else {
                        emailError.textContent = '✓ Email is available';
                        emailError.style.color = '#2eb87e';
                        emailInput.classList.remove('is-error');
                    }
                }
            } catch (e) {
                // silently ignore network errors
            }
        }, 500); // wait 500ms after user stops typing
    });
}
