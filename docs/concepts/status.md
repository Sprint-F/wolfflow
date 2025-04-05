**Статусы** - это логические (то есть возвращающие значения `true` или `false`) функции, которые принимают 
на вход весь лог действий над какой-либо конкретной [сущностью](./entity.md), и возвращают ответ на вопрос 
"Находится ли сейчас сущность в некоем интересующем нас состоянии?"

Статусы делятся на "**объективные**", то есть независимые от актора, относительно которого вычисляется статус, 
и "**субъективные**", то есть статусы, вычисляемые с "точки зрения" какого-либо актора.

Статусы в бандле WolffloW реализованы в виде классов-сервисов, имеющих метод `__invoke()`, собственно и возвращающий
статус, то есть ответ на вопрос о нахождении или ненахождении в искомом состоянии (см. примеры ниже).

# Классы и интерфейсы

## #[AsStatus]
Атрибут <code><b>SprintF\Bundle\Wolfflow\Attribute\AsStatus</b></code> применяется к классам статусов и обозначает
тот факт, что класс, отмеченный этим атрибутом, является классом статуса [бизнес-процесса](./workflow.md).

- <code>string <b>$workflow</b></code> Символьное имя <бизнес-процесса>, которому принадлежит статус
- <code>?string <b>$name</b></code> Собственное символьное имя статуса

Всем сервисам, помеченным данным атрибутом и реализующим интерфейс `StatusInterface`, компилятор Symfony добавляет тег
<code><b>workflow.status</b></code>с параметрами:

- <code><b>workflow</b></code> => символьное имя процесса, в который входит данный статус
- <code><b>name</b></code> => собственное символьное имя статуса

## StatusInterface

Общий интерфейс <code><b>SprintF\Bundle\Wolfflow\Status\StatusInterface</b></code> для всех статусов
всех [бизнес-процессов](./workflow.md). Задает следующие обязательные методы:

- Методы, возвращающие символьное имя [бизнес-процесса](./workflow.md), которому принадлежит данный статус,
  и сам объект этого бизнес-процесса:
    - <code><b>getWorkflowName()</b>: string</code>
    - <code><b>getWorkflow()</b>: WorkflowInterface</code>


- Методы, задающие и возвращающие [сущность](./entity.md), статус которой мы хотим узнать:
    - <code><b>setEntity(</b>WorkflowEntityInterface $entity<b>)</b></code>
    - <code><b>getEntity()</b>: WorkflowEntityInterface</code>


- Методы, задающие и возвращающие [актора](./actor.md), относительно которого мы хотим узнать субъективный статус:
    - <code><b>setActor(</b>?ActorInterface $entity<b>)</b></code>
    - <code><b>getActor()</b>: ?ActorInterface</code>


- Основной метод, метод непосредственно получения статуса.
  Такое имя метода выбрано специально, чтобы не выбирать никакое определенное имя:
    - <code><b>__invoke()</b>: bool</code>


Таким образом типовой сценарий получения информации об объективном статусе может выглядеть примерно так:
```php
$isEntityInStatus = ($status
  ->setEntity($entity)
)();
```
а о субъективном статусе - так:
```php
$isEntityInStatusForActor = ($status
  ->setEntity($entity)
  ->setActor($actor)
)();
```

## StatusAbstract

Типовой абстрактный базовый класс <code><b>SprintF\Bundle\Wolfflow\Status\StatusAbstract</b></code> для статусов,
включенный в состав бандла для удобства применения в приложениях. Класс реализует:

- Стандартный способ определения символьного имени бизнес-процесса статуса, исходя из атрибута `#[AsStatus]`
- Методы `getWorkflowName()` и `getWorkflow()`
- Метод `setEntity()` с проверкой на допустимый тип сущности
- Метод `setActor()`

Свои классы статусов в приложении можно наследовать от данного абстрактного класса, добавляя к ним лишь
реализацию метода `__invoke()`:

```php
use SprintF\Bundle\Wolfflow\Status\StatusAbstract;
use SprintF\Bundle\Wolfflow\Status\StatusDoctrineTrait;

class WasSomeActionSuccessStatus extends StatusAbstract
{
    use StatusDoctrineTrait;

    public function invoke()
    {
        return $this->logContainsActionSuccess(SomeAction::class);
    }
}
```
С классом `StatusAbstract` тесно связан трейт `StatusDoctrineTrait`, который рассмотрен ниже.

## StatusDoctrineTrait

Трейт <code><b>SprintF\Bundle\Wolfflow\Status\StatusDoctrineTrait</b></code> содержит ряд полезных методов, которые 
могут пригодится при разработке статусов с использованием Doctrine.

- Методы, проверяющие, была ли ранее хотя бы одна попытка выполнить интересующее нас действие над заданной сущностью:
    - <code><b>logContainsActionAttempt(</b>string $actionClass, ?ActorInterface \$actor = null<b>)</b>: bool</code> Была ли хотя бы одна попытка?
    - <code><b>logContainsActionSuccess(</b>string $actionClass, ?ActorInterface \$actor = null<b>)</b>: bool</code> Была ли хотя бы одна успешная попытка?
    - <code><b>logContainsActionFail(</b>string $actionClass, ?ActorInterface \$actor = null<b>)</b>: bool</code> Была ли хотя бы одна неуспешная попытка?


- Методы, проверяющие, какой была последняя попытка выполнить интересующее нас действие над заданной сущностью:
    - <code><b>wasLastActionAttemptSuccess(</b>string $actionClass, ?ActorInterface \$actor = null<b>)</b>: bool</code> Была ли последняя попытка успешной?
    - <code><b>wasLastActionAttemptFail(</b>string $actionClass, ?ActorInterface \$actor = null<b>)</b>: bool</code> Была ли последняя попытка неуспешной?


