Установите docker и docker-compose.

Скопируйте .env

```shell
cp .env.example .env
```

Ставим зависимости
```shell
docker-compose run --rm back composer install
```

Запускаем
```shell
docker-compose up
```

Создаем базу данных
```shell
docker-compose exec db psql -U postgres -c "create database backend;"
```

В браузере открываем http://localhost

Запуск команд artisan
```shell
docker-compose exec back ash # Внутри контейнера выполняем как обычно php artisan foo:bar
# или
docker-compose exec back php artisan foo:bar
# например накатить миграции
docker-compose exec back php artisan migrate
```
