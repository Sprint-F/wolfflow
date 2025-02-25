# Wolfflow
Wolfflow - это бандл для фреймворка Symfony, содержащий реализацию паттерна "Quantum States".

Бандл разработан в российской компании ["Спринт-Ф"](https://sprintf.ru) под руководством технического директора Степанцева Альберта.

# Quantum States
- [Краткое описание паттерна](./docs/pattern/description.md)

# Быстрый старт
- [Инструкция по интеграции бандла](./docs/quick-start.md)

# Документация по базовым понятиям

- [Актор](./docs/concepts/actor.md) 
- [Действие](./docs/concepts/action.md) 
- [Контекст действия](./docs/concepts/context.md)
- [Сущность](./docs/concepts/entity.md)
- [Запись в логе действий](./docs/concepts/log-entry.md) 
- [Исключение](./docs/concepts/exception.md) 
- [Бизнес-процесс](./docs/concepts/workflow.md)

# Для разработчиков

## Code style fix
```shell
php vendor/bin/php-cs-fixer fix
```

## Run Tests
```shell
php vendor/bin/codecept run Unit
```
