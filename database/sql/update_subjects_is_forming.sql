-- Скрипт для обновления is_forming в таблице subjects
-- Скрывает все предметы, кроме: ОГЭ (Русский, Математика) и ЕГЭ (Русский, Математика базовая/профильная)

-- Шаг 1: Отключаем формирование для ВСЕХ предметов
UPDATE subjects SET is_forming = 0;

-- Шаг 2: Включаем формирование ТОЛЬКО для нужных предметов
UPDATE subjects 
SET is_forming = 1 
WHERE class_name IN (
    'RUSS_OGE',        -- ОГЭ Русский язык
    'MAT_OGE',         -- ОГЭ Математика
    'RUSS_EGE',        -- ЕГЭ Русский язык
    'MAT_BAZA_EGE',    -- ЕГЭ Математика базовая
    'MAT_PROF_EGE'     -- ЕГЭ Математика профильная
);

-- Проверка результата
SELECT id, exam_type, class_name, name, is_forming 
FROM subjects 
ORDER BY exam_type, is_forming DESC, name;
