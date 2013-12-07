<?php

return [
// config/installation.config.php
'Php.ini option'
    => 'Параметр php.ini',
'Value'
    => 'Значение',
'Requirement'
    => 'Требование',
'ScContent - Installation'
    => 'ScContent - Инсталляция',
'Installing a module ScContent'
    => 'Установка модуля ScContent',
// Pre-test
'Pre-test'
    => 'Предварительная проверка',
'Check the system requirements'
    => 'Проверка системных требований',
'Check the configuration settings and installed PHP extensions.'
    => 'Проверка параметров конфигурации и установленных PHP расширений.',
'Safe mode must be disabled.'
    => 'Безопасный режим должен быть отключен.',
"Directive 'magic_quotes_gpc' should be disabled."
    => "Директива 'magic_quotes_gpc' должна быть отключена.",
"Directive 'magic_quotes_runtime' should be disabled."
    => "Директива 'magic_quotes_runtime' должна быть отключена.",
"Directive 'magic_quotes_sybase' should be disabled."
    => "Директива 'magic_quotes_sybase' должна быть отключена.",
'Available memory must be greater than 64M.'
    => 'Доступной памяти должно быть более 64М.',

'Missing php extension'
    => 'Отсутствующее php расширение',
'Information'
    => 'Информация',
//step 1
'Setting the configuration'
    => 'Установка конфигурации',
'Installing the module configuration and setup parameters of the database connection.'
    => 'Установка конфигурации модуля и настройка параметров соединения с базой данных.',
//step 2
'Database migration'
    => 'Миграция базы данных',
'Create and populate tables in the database.'
    => 'Создание и заполнение таблиц базы данных.',
// step 3
'Assets installation'
    => 'Установка ресурсов',
'Installation of resources and the creation of the directory to files uploads.'
    => 'Установка ресурсов и создание каталога для загрузки файлов.',
// step 4
'Widgets installation'
    => 'Установка виджетов',
'Register widgets for the current theme and existing content.'
    => 'Регистрация виджетов для текущей темы и существующего контента.',
// sc-default-installation-template
'Step'
    => 'Шаг',
'Installation error:'
    => 'Ошибка установки:',
'Continue'
    => 'Продолжить',

// Service\Installation\Uploads
'Failed to create uploads directory %s. Please, check permissions or create this directory manually.'
    => 'Не удалось создать каталог для загрузки файлов %s. Пожалуйста, проверьте права доступа или создайте этот каталог вручную.',

// Service\Installation\Assets
'Unable to extract assets. The target directory %s is not writable.'
    => 'Невозможно извлечь ресурсы. Целевой каталог %s не доступен для записи.',
'Failed to install the assets for an unknown module %s.'
    => 'Не удалось установить ресурсы для неизвестного модуля %s',
'Unable to install assets. Archive of %s is not found.'
    => 'Невозможно установить ресурсы. Архив %s не найден.',
'Unable to open archive %s. The archive is corrupt.'
    => 'Невозможно открыть архив %s. Архив поврежден.',
'A configuration error. The archive %s was extracted, but the directory, specified as the target does not exist or is it not the actual version.'
    => 'Ошибка конфигурации. Архив %s извлечен, но каталог, указанный в качестве целевого, не существует, или это не актуальная версия.',

// Form\Installation\DatabaseForm
'Database driver'
    => 'Драйвер базы данных',
'Path (only for SQLite)'
    => 'Путь (только для SQLite)',
'Host'
    => 'Хост',
'Database name'
    => 'Имя базы данных',
'Username'
    => 'Имя пользователя',
'Password'
    => 'Пароль',
'Password verify'
    => 'Повторите пароль',
'Install'
    => 'Установить',

// backend Navigator
'Content'
    => 'Содержимое',
'Manager'
    => 'Менеджер',
'Search'
    => 'Поиск',

'Apperance'
    => 'Оформление',
'Themes'
    => 'Темы',
'Layout'
    => 'Макет',


// layout/backend.phtml
'Administrator'
    => 'Администратор',

// sc-content/content-manager/index.phtml
'Content Manager'
    => 'Контент-менеджер',

// sc-content/content-manager/pane.phtml
'Web Site'
    => 'Сайт',
'Trash'
    => 'Корзина',
'Search'
    => 'Поиск',
'Go'
    => 'Перейти',
'Go to the first page'
    => 'Перейти на первую страницу',
'Go to the previous page'
    => 'Перейти на предыдущую страницу',
'Go to the next page'
    => 'Перейти на слудующую страницу',
'Go to the last page'
    => 'Перейти на последнюю страницу',
'Current page'
    => 'Текущая страница',
'of'
    => 'из',
'All'
    => 'Все',
'Categories'
    => 'Категории',
'Articles'
    => 'Статьи',
'Files'
    => 'Файлы',
'Reorder'
    => 'Сместить',
'Recovery'
    => 'Восстановить',
'Empty Trash'
    => 'Очистить корзину',
'Move'
    => 'Переместить',
'Move to Trash'
    => 'Удалить',
'Delete'
    => 'Удалить навсегда',
'Add'
    => 'Создать',
'Category'
    => 'Категорию',
'Article'
    => 'Статью',
'File'
    => 'Файл',
'Search Preferences'
    => 'Настроить поиск',
'Back to'
    => 'Вернуться к',
'Site'
    => 'Сайт',
'Trash'
    => 'Корзина',
'Title'
    => 'Заголовок',
'St'  // Status
    => 'Ст',
'Author'
    => 'Автор',
'Editor'
    => 'Редактор',
'Created'
    => 'Создан',
'Modified'
    => 'Изменен',
'Nothing was found.'
    => 'Ничего не найдено.',
'Try to change the filter.'
    => 'Попробуйте изменить фильтр.',
'Use'
    => 'Выбрать',
'Edit'
    => 'Изменить',
'Preview'
    => 'Просмотр',
'Go top'
    => 'Наверх',
'Are you sure, you want to delete the contents?'
    => 'Вы действительно хотите удалить содержимое?',
'Are you sure, you want to empty trash?'
    => 'Вы действительно хотите очистить корзину?',

// sc-content/content-manager/search.phtml
'Content options'
    => 'Параметры содержимого',
'Date options'
    => 'Параметры даты',
'User options'
    => 'Параметры пользователя',
'A word or a few words separated by a space.'
    => 'Слово или несколько слов через пробел.',
'A word or beginning of the word.'
    => 'Слово или начало слова.',

// Form\ContentSearch
'Clean'
    => 'Очистить',

'Containing Text'
    => 'Содержит текст',
'In the title'
    => 'В названии',
'In the content'
    => 'В содержимом',
'In the description'
    => 'В описании',
'Date of the latest changes is unknown'
    => 'Дата последних изменений неизвестна',
'Last week'
    => 'На прошедшей неделе',
'Last month'
    => 'В прошлом месяце',
'In this range'
    => 'В этом диапазоне',
'In the name'
    => 'В имени',
'In the e-mail'
    => 'В e-mail',

// Validator\ContentList\SearchDateRange
'Options for the date range search are not specified.'
    => 'Параметры для поиска по диапазону дат не указаны.',
'The start date for the search is not specified.'
    => 'Дата начала поиска не указана.',
'The end date for the search is not specified.'
    => 'Дата окончания поиска не указана.',

// sc-content/category/edit.phtml
'Save'
    => 'Сохранить',
'Manager'
    => 'Менеджер',
'Category description'
    => 'Описание категории',
'Category options'
    => 'Параметры категории',
'Status'
    => 'Статус',
'Published'
    => 'Опубликовано',
'Draft'
    => 'Черновик',
'Name'
    => 'Имя',
'Category Name'
    => 'Имя категории',

// sc-content/article/edit.phtml
'Article content'
    => 'Содержимое статьи',
'Article description'
    => 'Описание статьи',
'Article options'
    => 'Параметры статьи',
'Name'
    => 'Имя',
'Article Name'
    => 'Имя статьи',
'Permalink:'
    => 'Постоянная ссылка:',

// sc-content/file/add.phtml
'Add Files'
    => 'Добавить файлы',
'File names can only consist of letters of the alphabet, spaces, hyphens, and underscores.'
    => 'Имя файла может содержать только символы латинского алфавита, пробелы, дефисы и нижнее подчеркивание.',
'Maximum upload file size: 2MB.'
    => 'Максимальный размер загружаемого файла: 2Мб.',

// sc-content/file/add.phtml
'Edit Files'
    => 'Редактировать файлы',

// Form\FileAdd
'Upload'
    => 'Загрузить',
'Upload file'
    => 'Загрузить файл',

// View\Helper\ContentFormat
'category'
    => 'категория',
'article'
    => 'статья',
'undefined'
    => 'неизвестно',
'document'
    => 'документ',
'archive'
    => 'архив',
'image'
    => 'изображение',
'audio'
    => 'аудио',
'video'
    => 'видео',
'flash'
    => 'флеш',
'presentation'
    => 'презентация',
'drawing'
    => 'графика',

// ScDefault Theme
'The default theme with several regions.'
    => 'Тема по умолчанию с несколькими областями.',
];
