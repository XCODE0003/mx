// ВЫБОР В СЕЛЕКТОРЕ

$(document).ready(function () {
  $(".home_create_setting_selected").on("click", function (e) {
    e.stopPropagation();
    const $select = $(this).closest(".home_create_setting_select");

    // Скрыть все кроме текущего
    $(".home_create_setting_select_rect").not($select.find(".home_create_setting_select_rect")).slideUp(200);

    // Показать/скрыть текущий
    $select.find(".home_create_setting_select_rect").slideToggle(200);
  });

  $(".home_create_setting_select_rect_text").on("click", function (e) {
    if ($(e.target).closest("svg").length) return;

    const text = $(this).text().trim();
    const $this = $(this);
    const $select = $this.closest(".home_create_setting_select");
    const $selectedBlock = $select.find(".home_create_setting_selected");

    // Обновление текста и класса
    $selectedBlock.html(
      text +
        ' <svg xmlns="http://www.w3.org/2000/svg" width="15" height="9" viewBox="0 0 15 9" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M14.4351 1.70711L8.07117 8.07107C7.68065 8.46159 7.04748 8.46159 6.65696 8.07107L0.292998 1.70711C-0.0975266 1.31658 -0.0975266 0.683417 0.292998 0.292893C0.683523 -0.0976317 1.31669 -0.0976317 1.70721 0.292893L7.36407 5.94975L13.0209 0.292893C13.4114 -0.0976312 14.0446 -0.0976311 14.4351 0.292893C14.8257 0.683418 14.8257 1.31658 14.4351 1.70711Z" fill="#393C5B"/></svg>'
    );

    $select.find(".home_create_setting_select_rect_text").removeClass("select_selected");
    $this.addClass("select_selected");

    // Явно скрыть список
    $select.find(".home_create_setting_select_rect").slideUp(200);
  });

  // Клик вне выпадашки — закрываем
  $(document).on("click", function (e) {
    // Исключение: если клик внутри .home_create_setting_select
    if ($(e.target).closest(".home_create_setting_select").length) return;

    $(".home_create_setting_select_rect").slideUp(200);
  });
});

// ОТКРЫТИЕ FAQ

$(document).ready(function () {
  $(".home_faq_item").on("click", function () {
    const clickedItem = $(this).closest(".home_faq_item");

    if (clickedItem.hasClass("home_faq_item_active")) {
      // Сначала скрываем текст, потом удаляем класс
      clickedItem
        .find(".home_faq_item_tittle_text")
        .stop(true)
        .slideUp(200, function () {
          clickedItem.removeClass("home_faq_item_active");
        });
    } else {
      // Закрываем все
      $(".home_faq_item").each(function () {
        const item = $(this);
        item.find(".home_faq_item_tittle_text").stop(true).slideUp(200);
        item.removeClass("home_faq_item_active");
      });

      // Открываем выбранный
      clickedItem.addClass("home_faq_item_active");
      clickedItem.find(".home_faq_item_tittle_text").stop(true).slideDown(200);
    }
  });
});

// МОБИЛЬНОЕ МЕНЮ ОТКРЫТИЕ

$(document).ready(function () {
  const menu = $(".header_mobile_menu");
  const rect = $(".header_mobile_menu_rect");

  $(".header_mobile_burger").on("click", function () {
    menu.fadeIn(200); // плавное появление фона
    rect.animate({ right: "0" }, 400); // выезд панели справа
  });

  $(".header_mobile_menu_close, .header_mobile_menu_overlay").on(
    "click",
    function () {
      rect.animate({ right: "-317px" }, 400, function () {
        menu.fadeOut(200); // скрытие фона после закрытия панели
      });
    }
  );
});

// ОТКРЫТИЕ МОДАЛЬНОГО ОКНА PLANS
$(document).ready(function () {
  $("#openPlans").on("click", function (e) {
    e.preventDefault(); // Предотвращаем переход по ссылке

    const screenWidth = $(window).width();
    let offset = -50; // значение по умолчанию

    // Устанавливаем отступ в зависимости от диапазона ширины
    if (screenWidth >= 320 && screenWidth <= 389) {
      offset = +400;
    } else if (screenWidth >= 390 && screenWidth <= 499) {
      offset = +400;
    } else if (screenWidth >= 500 && screenWidth <= 799) {
      offset = +350;
    }

    if (screenWidth >= 320 && screenWidth <= 799) {
      // Мобильная версия — скроллим к блоку
      const $target = $(".home_plans_mobile");
      if ($target.length) {
        const targetPosition = $target.offset().top + offset;

        $("html, body").animate(
          {
            scrollTop: targetPosition,
          },
          700 // время анимации
        );
      }
    } else {
      // Десктоп — открываем модальное окно
      $("#modalPlans").css("display", "flex");
      $("body, html").addClass("no-scroll");
    }
  });

  // Закрытие модального окна
  $(".modal_overlay, .modal_home_plans_close").on("click", function () {
    $("#modalPlans").css("display", "none");
    $("body, html").removeClass("no-scroll");
  });
});

// ПЕРЕКЛЮЧЕНИЕ БЛОКОВ С ГЕНЕРАЦИЕЙ ВАРИАНТА НА ГЛАВНОЙ

$(document).ready(function () {
  let timerId = null;

  $("#createVariant").on("click", function () {
    if ($(".home_create_create").is(":visible")) {
      // Первый клик: скрываем create, показываем forming и запускаем таймер
      $(".home_create_create").hide();
      $(".home_create_forming").css("display", "grid");

      timerId = setTimeout(function () {
        $(".home_create_forming").hide();
        $(".home_create_saved").css("display", "grid");

        // Автоматическое скачивание файла
        const url = "path/to/your/file.ext"; // <-- путь к файлу
        const a = document.createElement("a");
        a.href = url;
        a.download = "";
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
      }, 5000);
    } else if ($(".home_create_forming").is(":visible")) {
      // Если нажали повторно на кнопку, пока showing forming — можно отменить таймер или игнорировать
      // Например, отменим таймер и вернемся к начальному состоянию
      clearTimeout(timerId);
      $(".home_create_forming").hide();
      $(".home_create_create").css("display", "grid");
      $(".home_create_saved").hide();
    }
  });

  // Скачивание файла по клику на .home_create_forming_rect внутри home_create_saved

$(document).on("click", ".home_create_saved .home_create_block_button", function (e) {
  // Проверяем, находится ли кнопка внутри .profile_constructor_auto
  if ($(this).closest(".profile_constructor_auto a").length > 0) {
    return; // Если да — ничего не делаем
  }

  // Иначе выполняем скачивание
  const url = "path/to/your/file.ext"; // <-- путь к файлу
  const a = document.createElement("a");
  a.href = url;
  a.download = "";
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
});
});

// ПРОЛИСТЫВАНИЯ С intro ДО БЛОКА ГЕНЕРАЦИИ ВАРИАНТА

$(document).ready(function () {
  $(".intro_block_button").on("click", function (e) {
    e.preventDefault();

    const offset = -50; // Здесь укажи нужный сдвиг по высоте (в пикселях), отрицательное — выше, положительное — ниже

    // Получаем позицию блока .home_create_blocks относительно верха документа
    const targetPosition = $(".home_create_blocks").offset().top + offset;

    // Плавно скроллим страницу к нужной позиции
    $("html, body").animate(
      {
        scrollTop: targetPosition,
      },
      700
    ); // 700 мс — время анимации, можно менять
  });
});

// ОГРАНИЧЕНИЕ В НОВОСТЯХ В НАЗВАНИИ 38 СИМВОЛОВ МАКСИМУ

$(document).ready(function () {
  $(".navigation_item_text").each(function () {
    const originalText = $(this).text();
    if (originalText.length > 38) {
      const shortened = originalText.substring(0, 38) + "...";
      $(this).text(shortened);
    }
  });
});

// СИСТЕМА РЕЙТИНГА НА СТРАНИЦЕ REVIEWS

$(document).ready(function () {
  let selectedRating = 0;

  $(".reviews_main_block_item_star").on("mouseenter", function () {
    const index = $(this).data("index");
    highlightStars(index);
  });

  $(".reviews_main_block_item_star").on("mouseleave", function () {
    highlightStars(selectedRating); // показываем активный рейтинг
  });

  $(".reviews_main_block_item_stars").on("mouseleave", function () {
    highlightStars(selectedRating); // если уводим мышь со всего блока
  });

  $(".reviews_main_block_item_star").on("click", function () {
    selectedRating = $(this).data("index");
    highlightStars(selectedRating);
  });

  function highlightStars(rating) {
    $(".reviews_main_block_item_star").each(function () {
      const index = $(this).data("index");
      $(this).toggleClass("hover", index <= rating);
      $(this).toggleClass("active", index <= selectedRating);
    });
  }
});

// МОДАЛЬНОЕ ОКНО ПРИ РЕГИСТРАЦИИ

$(document).ready(function () {
  // Функция открытия модального окна
  function openModal(modalId) {
    $(".modal").css("display", "none"); // Закрыть все открытые модалки
    $(modalId).css("display", "flex");  // Открыть нужную
    $('body').addClass('no-scroll');
  }

  // Функция закрытия всех модалок
  function closeModals() {
    $(".modal").css("display", "none");
    $('body').removeClass('no-scroll');
  }

  // Открытие конкретных модалок
  $("#registerBtn").on("click", function () {
    openModal("#modalReady");
  });

  $("#openRecoverySend").on("click", function () {
    openModal("#modalRecoverySend");
  });

  $("#openRecoveryMail").on("click", function () {
    openModal("#modalRecoveryMail");
  });

  // Закрытие по клику на крестик или overlay
  $(".modal_close, .modal_overlay, #regFinal").on("click", function () {
    closeModals();
  });
});




// ВАЛИДАЦИЯ ФОРМ ВХОДА И РЕГИСТРАЦИИ

$(document).ready(function () {
  $(".signin_main_rect_input").on("input blur", function () {
    const $input = $(this);
    if ($input.val().trim() === "") {
      $input.css("border", "1px solid red");
    } else {
      $input.css("border", "1px solid #ccc"); // или верни свой стандартный цвет
    }
  });
});

$(document).ready(function () {
  $(".profile_account_up_item_input").each(function () {
    const $input = $(this);
    const originalPlaceholder = $input.attr("placeholder");
    const maxWidth = $input.width();

    // создаём canvas для измерения ширины текста
    const canvas = document.createElement("canvas");
    const ctx = canvas.getContext("2d");
    const font = $input.css("font");
    ctx.font = font;

    let truncated = originalPlaceholder;
    while (
      ctx.measureText(truncated + "...").width > maxWidth &&
      truncated.length > 0
    ) {
      truncated = truncated.slice(0, -1);
    }

    if (truncated.length < originalPlaceholder.length) {
      $input.attr("placeholder", truncated + "...");
    }
  });
});

// СКАЧИВАНИЕ ФАЙЛА ИЗ ТАБЛИЦЫ profile_history

$(document).ready(function () {
  $(".profile_history_tabble_download").on("click", function () {
    const fileUrl = $(this).data("file");

    if (fileUrl) {
      // Создаём временную ссылку и инициируем скачивание
      const link = document.createElement("a");
      link.href = fileUrl;
      link.download = ""; // можно задать имя, например: 'download.pdf'
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    } else {
      alert("Файл не найден");
    }
  });
});

// ПОКАЗ ПАРОЛЯ ПО НАЖАТИЮ В  profile_account

$(document).ready(function () {
  $(".profile_account_show_pass").on("click", function () {
    const $input = $(this).siblings(".profile_account_up_item_input");

    // временно снимаем disabled
    const wasDisabled = $input.prop("disabled");
    if (wasDisabled) $input.prop("disabled", false);

    // переключаем тип
    if ($input.attr("type") === "password") {
      $input.attr("type", "text");
    } else {
      $input.attr("type", "password");
    }

    // возвращаем disabled
    if (wasDisabled) $input.prop("disabled", true);
  });
});

// ВОЗМОЖНОСТЬ СМЕНЫ ПОЧТЫ В   profile_account

$(document).ready(function () {
  $(".profile_account_middle_change_button").on("click", function () {
    const $btn = $(this);
    const $input = $btn.siblings(".profile_account_up_item_input");

    if ($input.prop("disabled")) {
      // Включить редактирование
      $input.prop("disabled", false).focus();
      $btn.text("Сохранить");
    } else {
      // Отключить редактирование и "сохранить"
      $input.prop("disabled", true);
      $btn.text("Сменить");

      // Здесь можно отправить значение на сервер (AJAX), если нужно
      const newValue = $input.val();
      console.log("Новое значение:", newValue);
    }
  });
});

// ОТКРЫТИЕ БАНКА ЗАДАНИЙ НА СТРАНИЦЕ banks

$(document).ready(function () {
  $(".banks_main_rect_up_item_button").on("click", function () {
    const $button = $(this);
    const $block = $button.closest(".banks_main_rect");
    const $bottom = $block.find(".banks_main_rect_bottom");

    if ($button.text().trim() === "Обновить") {
      // Обновление данных из БД (эмуляция через console.log)
      console.log("Обновление данных из базы...");

      // Пример AJAX-запроса
      /*
      $.ajax({
        url: "/api/get-latest-data", // или другой URL
        method: "POST",
        data: { id: $block.data("id") }, // если нужен идентификатор
        success: function (response) {
          // Обнови контент в $bottom
          $bottom.html(response.html);
        },
        error: function () {
          alert("Ошибка при обновлении данных.");
        }
      });
      */
      return;
    }

    // В других случаях — просто открываем блок и меняем текст
    $bottom.slideToggle(300, function () {
      if ($bottom.is(":visible")) {
        $button.text("Обновить");
      }
    });
  });
});

// СКРЫТЬ/ПОКАЗЫТЬ СТРАНИЦЫ В banks

$(document).ready(function () {
  $(".banks_main_rect_bottom_show").on("click", function () {
    const $button = $(this);
    const $target = $button.siblings(".banks_main_rect_bottom_pages");

    $target.slideToggle(300, function () {
      // После анимации меняем текст
      if ($target.is(":visible")) {
        $button.text("Скрыть");
      } else {
        $button.text("Показать");
      }
    });
  });
});


// СЛАЙДЕР В СОЗДАНИИ ВАРИАНТА


$(document).ready(function () {
  const $sliderWrapper = $(".profile_constructor_manual_sliders");
  const $slides = $(".profile_constructor_manual_slide");
  const $navButtons = $(".profile_constructor_manual_slider_nav");

  const visibleSlides = 3;
  const totalSlides = $slides.length;
  const maxIndex = totalSlides - visibleSlides;
  let currentIndex = 0;

  function updateSlider(index) {
    index = Math.max(0, Math.min(index, maxIndex)); // не выходим за границы
    const offset = -index * (100 / visibleSlides); // сдвиг в %
    $sliderWrapper.css("transform", `translateX(${offset}%)`);
    currentIndex = index;

    // Обновляем активную точку
    $navButtons.removeClass("profile_constructor_manual_slider_nav_active");
    $navButtons.eq(currentIndex).addClass("profile_constructor_manual_slider_nav_active");
  }

  $navButtons.on("click", function () {
    const index = $(this).index(".profile_constructor_manual_slider_nav");
    updateSlider(index);
  });

  $(".profile_constructor_manual_slider_nav_left").on("click", function () {
    if (currentIndex > 0) {
      updateSlider(currentIndex - 1);
    }
  });

  $(".profile_constructor_manual_slider_nav_right").on("click", function () {
    if (currentIndex < maxIndex) {
      updateSlider(currentIndex + 1);
    }
  });

  updateSlider(0);
});




// ПЕРЕКЛЮЧЕНИЕ ПРИ ОТКРЫТИИ СТРАНИЦЫ АВТОМАТИЧЕСКИ

$(document).ready(function () {
  // Проверяем, что это нужная страница (если нужно, по URL)
  if (window.location.pathname.includes("profile_constructor_auto.html")) {
    setTimeout(function () {
      // Скрыть форму
      $(".profile_constructor_auto .home_create_forming").css("display", "none");

      // Показать блок сохранения
      $(".profile_constructor_auto .home_create_saved").css("display", "grid");
    }, 5000); // 5000 мс = 5 секунд
  }
});



// ПЕРЕКЛЮЧЕНИЕ ПРИ ГЕНЕРАЦИИ БЕСПЛАТНОГО ВАРИАНТА
$(document).ready(function () {
  $("#createFreeVar").on("click", function () {
    // Скрываем кнопку
    $(this).hide();

    // Скрываем верхнюю часть
    $(".profile_constructor_free_up").hide();

    // Показываем блок формирования
    $(".home_create_forming").css("display", "grid");

    // Через 5 секунд скрываем .home_create_forming и показываем .home_create_saved
    setTimeout(function () {
      $(".home_create_forming").hide();
      $(".home_create_saved").css("display", "grid");
    }, 5000);
  });
});