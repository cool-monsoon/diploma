## Дипломный проект по профессии «Веб-разработчик»

В работе над проектом использовались:

* PHP ver. 8.2
* Laravel ver. 11.9
* База данных sqlite
* JavaScript


Для запуска проекта потребуется:

* Клонировать репозиторий `git clone https://github.com/cool-monsoon/diploma.git`
* Установить зависимости, выполнив команды `npm install` и `composer install`в терминале
* Создать файл окружения .env, скопировав в корень проекта файл .env.example и переименовав копию
* Проверить настройки БД (DB_CONNECTION=sqlite, DB_DATABASE=database.sqlite) в файле .env
* Сгенерировать ключ приложения, выполнив команду `php artisan key:generate` в терминале
* Примененить миграции для создания таблиц БД, выполнив команду `php artisan migrate` в терминале
* Запустить локальный сервер, выполнив команду `php artisan serve` в терминале, и открыть проект в браузере


Клиентская часть приложения доступна без аутентификации.

Для доступа в административную часть нужно перейти по ссылке`http://127.0.0.1:8000/admin` и выполнить аутентификацию с логином `admin@admin.com` и паролем `Admin987`.

