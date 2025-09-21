/* Views/landing-page/a/script.js */
/* DOMContentLoaded safe init */
document.addEventListener('DOMContentLoaded', function () {
  /* ---------- Mobile menu animated (hamburger morph) ---------- */
  const menuBtn = document.getElementById('menu-btn');
  const mobileMenu = document.getElementById('mobile-menu');
  const hamburger = document.getElementById('hamburger');

  if (menuBtn && mobileMenu && hamburger) {
    menuBtn.addEventListener('click', function () {
      mobileMenu.classList.toggle('hidden');
      menuBtn.classList.toggle('open'); // activa la animación CSS

      // alternar entre hamburguesa y X
      if (mobileMenu.classList.contains('hidden')) {
        hamburger.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>';
      } else {
        hamburger.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>';
      }
    });
  }

  /* ---------- Toggle Cliente / Profesional ---------- */
  const optCliente = document.getElementById('opt-cliente');
  const optProfesional = document.getElementById('opt-profesional');
  const knob = document.getElementById('knob');
  const clienteText = document.getElementById('clienteText');
  const profesionalText = document.getElementById('profesionalText');

  if (optCliente && optProfesional && knob && clienteText && profesionalText) {
    let active = 'cliente';
    const setCliente = () => {
      active = 'cliente';
      knob.classList.remove('knob-right');
      clienteText.classList.remove('hidden');
      profesionalText.classList.add('hidden');
      optCliente.style.color = '';
      optProfesional.style.color = 'rgba(31,31,31,0.5)';
    };
    const setProfesional = () => {
      active = 'profesional';
      knob.classList.add('knob-right');
      clienteText.classList.add('hidden');
      profesionalText.classList.remove('hidden');
      optCliente.style.color = 'rgba(31,31,31,0.5)';
      optProfesional.style.color = '';
    };

    optCliente.addEventListener('click', (e) => {
      e.preventDefault();
      setCliente();
    });
    optProfesional.addEventListener('click', (e) => {
      e.preventDefault();
      setProfesional();
    });

    optCliente.addEventListener('keydown', (e) => { if (e.key === 'Enter') setCliente(); });
    optProfesional.addEventListener('keydown', (e) => { if (e.key === 'Enter') setProfesional(); });

    setCliente();
  }

  /* ---------- Showcase carousel controls ---------- */
  const showcase = document.getElementById('showcase');
  const prevBtn = document.getElementById('prev');
  const nextBtn = document.getElementById('next');

  if (showcase) {
    const calcStep = () => {
      const item = showcase.querySelector('figure');
      if (!item) return 420;
      const style = window.getComputedStyle(item);
      const marginRight = parseFloat(style.marginRight || 0);
      return Math.round(item.offsetWidth + marginRight);
    };

    if (prevBtn) {
      prevBtn.addEventListener('click', () => {
        const step = calcStep();
        showcase.scrollBy({ left: -step, behavior: 'smooth' });
      });
    }
    if (nextBtn) {
      nextBtn.addEventListener('click', () => {
        const step = calcStep();
        showcase.scrollBy({ left: step, behavior: 'smooth' });
      });
    }

    let auto = null;
    const startAuto = () => {
      stopAuto();
      if (window.innerWidth >= 768) {
        auto = setInterval(() => {
          const step = calcStep();
          if (showcase.scrollLeft + showcase.clientWidth >= showcase.scrollWidth - 10) {
            showcase.scrollTo({ left: 0, behavior: 'smooth' });
          } else {
            showcase.scrollBy({ left: step, behavior: 'smooth' });
          }
        }, 4500);
      }
    };
    const stopAuto = () => { if (auto) clearInterval(auto); auto = null; };

    showcase.addEventListener('mouseenter', stopAuto);
    showcase.addEventListener('mouseleave', startAuto);
    window.addEventListener('resize', startAuto);

    startAuto();
  }

  /* ---------- Accessibility: keyboard for mobile menu close (Esc) ---------- */
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
        mobileMenu.classList.add('hidden');
        if (hamburger) {
          hamburger.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>';
        }
      }
    }
  });
});
