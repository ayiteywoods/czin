const prefersReducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

let observer = null;

function createObserver() {
  return new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) {
        return;
      }

      entry.target.classList.add('is-revealed');
      observer?.unobserve(entry.target);
    });
  }, {
    threshold: 0.12,
    rootMargin: '0px 0px -36px 0px',
  });
}

export function initStorefrontReveal(root = document) {
  const elements = root.querySelectorAll('[data-storefront-reveal]:not(.is-revealed)');

  if (elements.length === 0) {
    return;
  }

  if (prefersReducedMotion()) {
    elements.forEach((element) => element.classList.add('is-revealed'));

    return;
  }

  if (!observer) {
    observer = createObserver();
  }

  elements.forEach((element) => observer.observe(element));
}

export function revealStorefrontElements(elements) {
  if (prefersReducedMotion()) {
    elements.forEach((element) => element.classList.add('is-revealed'));

    return;
  }

  if (!observer) {
    observer = createObserver();
  }

  elements.forEach((element) => {
    if (element.classList.contains('is-revealed')) {
      return;
    }

    observer.observe(element);
  });
}

document.addEventListener('DOMContentLoaded', () => initStorefrontReveal());
