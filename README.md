## Разворачивание проекта

1. Развернуть docker-окружение
```
docker-compose up -d
```

2. Переименовать файл .env.dist в .env, при необходимости поменять значение портов MYSQL_PORT, NGINX_PORT

3. Зайти в контейнер php
```
docker exec -it task-tracker_php bash
```

4. Установить зависимости composer
```
composer install
```

5. Выполнить миграции в базу данных
```
php bin/console doctrine:migrations:migrate
```

Если всё прошло удачно, проект должен быть доступен по адресу http://localhost/

## Работа с проектом

1. Выполнение API-запросов через curl (в контейнере nginx)
- посмотреть список задач
```
curl -X GET http://localhost/api/tasks/
```
- посмотреть задачу по id
```
curl -X GET http://localhost/api/tasks/1
```
- создать задачу
```
curl -X POST http://localhost/api/tasks/ \
-H "Content-Type: application/json" \
-d '{
    "title": "New Task Title",
    "description": "New Task Description",
    "status": "new"
}'
```
- обновить задачу
```
curl -X PUT http://localhost/api/tasks/1 \
-H "Content-Type: application/json" \
-d '{
    "title": "Updated Task Title",
    "description": "Updated Task Description",
    "status": "in_progress"
}'
```
- удалить задачу
```
curl -X DELETE http://localhost/api/tasks/1
```
2. Запуск тестов (в контейнере php)
```
php bin/phpunit
```