# Wolfflow
Wolfflow - это бандл для фреймворка Symfony, содержащий реализацию паттерна "Quantum States".

# Базовые понятия паттерна и бандла

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
