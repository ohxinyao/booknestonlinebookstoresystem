function showToast(message, type = 'success') {
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        const container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
        toastContainer = container;
    }

    const toastHtml = `
        <div class="toast align-items-center text-white bg-${type} border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    const toastEl = document.createElement('div');
    toastEl.innerHTML = toastHtml;
    toastContainer.appendChild(toastEl);
    const toast = new bootstrap.Toast(toastEl.firstChild, { delay: 3000 });
    toast.show();
    setTimeout(() => toastEl.remove(), 3500);
}

function confirmDelete(message = 'Are you sure you want to delete this item?') {
    return confirm(message);
}

function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById(previewId);
            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function handleDecrement(e) {
    const wrapper = e.target.closest('.input-group');
    if (!wrapper) return;
    const input = wrapper.querySelector('.qty-input');
    if (!input) return;
    let newVal = parseInt(input.value) - 1;
    if (isNaN(newVal)) newVal = 1;
    if (newVal < 1) newVal = 1;
    input.value = newVal;
    input.dispatchEvent(new Event('change', { bubbles: true }));
}

function handleIncrement(e) {
    const wrapper = e.target.closest('.input-group');
    if (!wrapper) return;
    const input = wrapper.querySelector('.qty-input');
    if (!input) return;
    let newVal = parseInt(input.value) + 1;
    if (isNaN(newVal)) newVal = 1;
    input.value = newVal;
    input.dispatchEvent(new Event('change', { bubbles: true }));
}

function handleQuantityChange(e) {
    const input = e.target;
    let val = parseInt(input.value);
    if (isNaN(val) || val < 1) val = 1;
    input.value = val;
    const form = input.closest('form.update-quantity-form');
    if (form) {
        form.submit();
    }
}

function attachCartQuantityEvents() {
    document.querySelectorAll('.qty-decr').forEach(btn => {
        btn.removeEventListener('click', handleDecrement);
        btn.addEventListener('click', handleDecrement);
    });

    document.querySelectorAll('.qty-incr').forEach(btn => {
        btn.removeEventListener('click', handleIncrement);
        btn.addEventListener('click', handleIncrement);
    });

    document.querySelectorAll('.qty-input').forEach(input => {
        input.removeEventListener('change', handleQuantityChange);
        input.addEventListener('change', handleQuantityChange);
    });
}

function setNavbarScrollState() {
    const navbar = document.querySelector('.navbar');
    if (!navbar) return;
    if (window.scrollY > 40) {
        navbar.classList.add('shrink');
    } else {
        navbar.classList.remove('shrink');
    }
}

function updateBackToTopVisibility(button) {
    if (window.scrollY > 300) {
        button.classList.add('show');
    } else {
        button.classList.remove('show');
    }
}

function scrollReveal() {
    const elements = document.querySelectorAll('.reveal, .card, .book-card');
    elements.forEach(el => {
        const rect = el.getBoundingClientRect();
        if (rect.top < window.innerHeight - 80) {
            el.classList.add('visible');
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const addToCartForms = document.querySelectorAll('.add-to-cart-form');
    addToCartForms.forEach(form => {
        form.addEventListener('submit', function() {
            setTimeout(() => {
                showToast('Book added to cart!', 'success');
            }, 100);
        });
    });

    const deleteButtons = document.querySelectorAll('.confirm-delete');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirmDelete()) {
                e.preventDefault();
            }
        });
    });

    attachCartQuantityEvents();
    setNavbarScrollState();
    const backToTop = buildBackToTopButton();
    updateBackToTopVisibility(backToTop);
    scrollReveal();
    attachInteractiveCards();

    window.addEventListener('scroll', () => {
        setNavbarScrollState();
        updateBackToTopVisibility(backToTop);
        scrollReveal();
    });
});
