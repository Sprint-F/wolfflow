# Быстрый старт

## Установка бандла
Выполните в своём проекте команду:
```shell
composer require sprintf/wolfflow-bundle
```
Убедитесь, что запись о бандле появилась в файле `config/bundles.php`

## Конфигурация бандла
В данный момент конфигурирование бандла не требуется.

## Подготовка лога действий
Определитесь с первой сущностью, которая получит свой бизнес-процесс. Пусть, к примеру, это будет сущность заказа
в интернет-магазине: класс `App\Entity\Order`.

Начнем с того, что подготовим класс, описывающий лог действий над данной сущностью. Следуйте следующему примеру:
```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use SprintF\Bundle\Wolfflow\LogEntry\LogEntryDoctrineTrait;
use SprintF\Bundle\Wolfflow\LogEntry\LogEntryInterface;

#[ORM\Entity]
#[ORM\Table(name: 'orders_action_log')]
class OrderLogEntry implements LogEntryInterface
{
    use LogEntryDoctrineTrait;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'logEntries')]
    #[ORM\JoinColumn(name: 'entity_id', referencedColumnName: 'id', nullable: true)]
    private ?Order $entity = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true)]
    private ?User $actor = null;
}
```
Основную "работу" здесь делает трейт `LogEntryDoctrineTrait`. Нам нужно лишь реализовать связь с той сущностью, 
лог действий над которой мы готовим (`private ?Order $entity`) и указать, кто будет актором (`private ?User $actor`) - 
пользователь нашего приложения.

> Внимание! В указанном примере намеренно не выбрана никакая стратегия формирования идентификатора сущности! Выберите ее самостоятельно, исходя из общей стратегии вашего приложения!

В принципе, этого достаточно для формирования корректной миграции, которая создаст таблицу с логом действий над заказам.

### Что можно сделать еще?
В реальной эксплуатации бандла в приложении, использующем БД Postgres, рекомендуется предпринять несколько 
дополнительных шагов:

1. Подготовьте в базе доменный тип `action_result`, определенный как перечисление:
```sql
CREATE TYPE action_result AS ENUM ('cannot', 'progress', 'success', 'fail')
```
2. Интегрируйте этот тип в своё приложение
```php
namespace App\Dbal\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class ActionResultType extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'action_result';
    }

    public function getName(): string
    {
        return 'action_result';
    }
}
```

```yaml
doctrine:
    dbal:
        types:
            action_result: 'App\Dbal\Types\ActionResultType'
```
3. Используйте его в метаданных записей в логе действий:
```php
use SprintF\Bundle\Wolfflow\Action\ActionResult;

class OrderLogEntry implements LogEntryInterface
{
...
    #[ORM\Column(name: 'action_result', type: 'action_result', enumType: ActionResult::class, nullable: false)]
    private ActionResult $result;
}
```
_Возможно, что в зависимости от версии Symfony, вам еще потребуется определить свой класс, наследующийся 
от `PostgreSQLPlatform`, указать в нем type mapping для данного типа и упомянуть данный класс в конфиге Doctrine._ 

4. Не забудьте добавить в классе `OrderLogEntry` декларации нужных индексов, в коде выше они намеренно опущены. 

5. Было бы неплохо объявить общую таблицу всех логов действий над всеми сущностями, а конкретные таблицы логов - 
наследовать от нее. Однако это уже выходит за рамки данного руководства. Если вы чувствуете потребность в такой 
общей таблице, вы справитесь с этой задачей самостоятельно.

## Миграция структуры данных
Подготовьте миграцию:
```shell
bin/console do:mi:diff
```
Внимательно просмотрите ее код, скорректируйте при необходимости, выполните:
```shell
bin/console do:mi:mi
```
Убедитесь, что у вас создалась таблица лога действий, нужные индексы и внешние ключи.

## Подготовка сущностей
Возьмите сущность, которая будет участвовать в бизнес-процессе и проведите следующие шаги подготовки:
1. Добавьте к ней атрибут #[AsEntity],
2. Добавьте интерфейс `EntityInterface` и его базовую реализацию из трейта `EntityTrait`,
3. Добавьте связь с `OrderLogEntry`:
```php
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;


use SprintF\Bundle\Wolfflow\Attribute\AsEntity;
use SprintF\Bundle\Wolfflow\Entity\EntityInterface;
use SprintF\Bundle\Wolfflow\Entity\EntityTrait;

#[AsEntity(workflow: 'order', logEntryClass: OrderLogEntry::class)]
class Order implements EntityInterface
{
    use EntityTrait;
    
    #[ORM\OneToMany(targetEntity: OrderLogEntry::class, mappedBy: 'entity')]
    #[ORM\OrderBy(['startedAt' => 'ASC'])]
    private Collection $logEntries;

    public function getLogEntries(): Collection
    {
        return $this->logEntries;
    }
    
    ...
    
    public function __construct()
    {
        $this->logEntries = new ArrayCollection();
    }
}
```


- разработка процессов, действий, контекстов
- применение действий
- разработка и применение статусов