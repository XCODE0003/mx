#!/usr/bin/env node
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import createReport from 'html-to-docx';
import { JSDOM } from 'jsdom';
import sanitizeHtml from 'sanitize-html';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

async function main() {
  const [,, inputHtmlPath, outputDocxPath] = process.argv;
  if (!inputHtmlPath || !outputDocxPath) {
    console.error('Usage: node scripts/html-to-docx.js <input.html> <output.docx>');
    process.exit(2);
  }
  let html = fs.readFileSync(path.resolve(inputHtmlPath), 'utf8');
  // Предобработка: удаляем скрипты/стили/MathJax и MS Office мусор, нормализуем теги
  const cleaned = sanitizeHtml(html, {
    allowedTags: false,
    allowedAttributes: false,
    exclusiveFilter: (frame) => false,
    transformTags: {
      'span': 'span',
    },
  })
  .replace(/<script[\s\S]*?<\/script>/gi, '')
  .replace(/<style[\s\S]*?<\/style>/gi, '')
  .replace(/class=\"?Mso[^\"\s>]*\"?/gi, '')
  .replace(/<!--.*?-->/g, '');

  const dom = new JSDOM(cleaned);
  const document = dom.window.document;
  // Удаляем пустые MSO абзацы с &nbsp;
  document.querySelectorAll('p').forEach(p => {
    const text = (p.textContent || '').replace(/\u00A0/g, '').trim();
    if (!text && p.children.length === 0) p.remove();
  });
  // Закрываем незакрытые таблицы/ячейки опосредованно — jsdom починит DOM
  // Встраиваем картинки data: уже в HTML из бекенда
  html = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>' + document.body.innerHTML + '</body></html>';

  const buffer = await createReport(html, {
    page: { margin: { top: 720, right: 720, bottom: 720, left: 720 } },
    table: { row: { cantSplit: true } },
  });
  fs.writeFileSync(path.resolve(outputDocxPath), buffer);
  process.exit(0);
}

main().catch((e) => { console.error(e); process.exit(1); });


