const cache = {};

export async function loadLanguage(lang) {
  const version = Date.now(); 
  if (!cache[lang]) {
    const res = await fetch(`/trandar_website/newstore/locales/${lang}.json?v=${version}`);
    if (!res.ok) {
      throw new Error(`Failed to load: locales/${lang}.json`);
    }
    cache[lang] = await res.json();
  }
  return cache[lang];
}

export function applyTranslations(dict) {
  document.querySelectorAll('[data-lang]').forEach(el => {
    const key = el.dataset.lang;
    if (!key) return;

    const txt = dict[key];

    if (txt !== undefined) {
      if (el.hasAttribute('placeholder')) {
        el.placeholder = txt;
      } else if (el.hasAttribute('title')) {
        el.title = txt;
      } else if (el.hasAttribute('value') && el.tagName === 'INPUT') {
        el.value = txt;
      } else {
        el.textContent = txt;
      }
    } else {
      console.warn(`Missing translation for key: ${key}`);
    }
  });
}
