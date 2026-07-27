// =====================================================================
// MediSlot — front-end interactivity
// =====================================================================

document.addEventListener('DOMContentLoaded', function () {

  // ---- mobile nav toggle ----
  const toggle = document.querySelector('.nav-toggle');
  const navbar = document.querySelector('.navbar');
  if (toggle && navbar) {
    toggle.addEventListener('click', () => navbar.classList.toggle('open'));
  }

  // ---- prevent past dates on any date input ----
  document.querySelectorAll('input[type="date"]').forEach(function (input) {
    const today = new Date().toISOString().split('T')[0];
    input.setAttribute('min', today);
  });

  // ---- generic client-side validation for forms with .needs-validation ----
  document.querySelectorAll('form.needs-validation').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      let valid = true;
      let firstInvalid = null;

      form.querySelectorAll('[required]').forEach(function (field) {
        clearError(field);
        if (!field.value || !field.value.trim()) {
          valid = false;
          showError(field, 'This field is required');
          if (!firstInvalid) firstInvalid = field;
        }
      });

      // password match check (register page)
      const pass = form.querySelector('#password');
      const confirm = form.querySelector('#confirm_password');
      if (pass && confirm && pass.value !== confirm.value) {
        valid = false;
        showError(confirm, 'Passwords do not match');
        if (!firstInvalid) firstInvalid = confirm;
      }

      // email format
      const email = form.querySelector('input[type="email"]');
      if (email && email.value) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!re.test(email.value)) {
          valid = false;
          showError(email, 'Enter a valid email address');
          if (!firstInvalid) firstInvalid = email;
        }
      }

      // phone: digits only, 10 chars
      const phone = form.querySelector('#phone');
      if (phone && phone.value) {
        const re = /^[0-9]{10}$/;
        if (!re.test(phone.value)) {
          valid = false;
          showError(phone, 'Enter a valid 10-digit phone number');
          if (!firstInvalid) firstInvalid = phone;
        }
      }

      if (!valid) {
        e.preventDefault();
        if (firstInvalid) firstInvalid.focus();
      }
    });
  });

  function showError(field, msg) {
    field.style.borderColor = '#E2694B';
    let hint = field.parentElement.querySelector('.field-error');
    if (!hint) {
      hint = document.createElement('small');
      hint.className = 'field-error';
      hint.style.color = '#E2694B';
      hint.style.fontSize = '.78rem';
      hint.style.display = 'block';
      hint.style.marginTop = '.3rem';
      field.parentElement.appendChild(hint);
    }
    hint.textContent = msg;
  }
  function clearError(field) {
    field.style.borderColor = '';
    const hint = field.parentElement.querySelector('.field-error');
    if (hint) hint.remove();
  }

  // ---- confirm before cancelling an appointment ----
  document.querySelectorAll('.confirm-cancel').forEach(function (link) {
    link.addEventListener('click', function (e) {
      if (!confirm('Cancel this appointment? This cannot be undone.')) {
        e.preventDefault();
      }
    });
  });

  // ---- confirm before deleting a doctor (admin) ----
  document.querySelectorAll('.confirm-delete').forEach(function (link) {
    link.addEventListener('click', function (e) {
      if (!confirm('Delete this record permanently?')) {
        e.preventDefault();
      }
    });
  });

  // ---- doctor search / filter on doctors.php ----
  const search = document.getElementById('doctorSearch');
  const specFilter = document.getElementById('specFilter');
  if (search || specFilter) {
    const cards = document.querySelectorAll('.doctor-card-item');
    function filterCards() {
      const term = (search?.value || '').toLowerCase();
      const spec = specFilter?.value || '';
      let visibleCount = 0;
      cards.forEach(function (card) {
        const name = card.dataset.name.toLowerCase();
        const cardSpec = card.dataset.spec;
        const matches = name.includes(term) && (spec === '' || cardSpec === spec);
        card.style.display = matches ? '' : 'none';
        if (matches) visibleCount++;
      });
      const emptyMsg = document.getElementById('noResults');
      if (emptyMsg) emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
    }
    search?.addEventListener('input', filterCards);
    specFilter?.addEventListener('change', filterCards);
  }

  // ---- auto-hide flash alerts after 4s ----
  document.querySelectorAll('.alert').forEach(function (alert) {
    setTimeout(() => { alert.style.transition = 'opacity .4s'; alert.style.opacity = '0'; setTimeout(()=>alert.remove(),400); }, 4000);
  });
});
