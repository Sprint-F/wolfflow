
<b>Актор</b> - это тот, кто производит [действие](./action.md).

Актором может быть:
- Пользователь приложения;
- Сервис или микросервис;
- Процесс, работающий в cli-режиме;
- и т.п.

В некоторых ситуациях актор может быть неизвестен. Поэтому код, ожидающий получения объекта актора, должен всегда 
предусматривать ситуацию, когда вместо объекта он получает `null`.

# Классы и интерфейсы

- [ActorInterface](#ActorInterface)
- [ActorProviderInterface](#ActorProviderInterface)
- [DefaultActorProvider](#DefaultActorProvider)

## <a id="ActorInterface"></a>ActorInterface

<p>Интерфейс <code><b>SprintF\Bundle\Wolfflow\Actor\ActorInterface</b></code> задает требования к актору.

Основное (и единственное) требование этого интерфейса - иметь возможность однозначно идентифицировать актора, 
для чего этот интерфейс определяет метод
- <code><b>getActorId()</b>: int|string|\Stringable|null</code>

При использовании бандла в приложении требуется добавить реализацию интерфейса `ActorInterface` к сущности актора: 
в типовом случае к сущности пользователя приложения.

## <a id="ActorProviderInterface"></a>ActorProviderInterface

Интерфейс <code><b>SprintF\Bundle\Wolfflow\Actor\ActorProviderInterface</b></code> задает требования к провайдеру актора, 
то есть к тому классу, экземпляр которого может использоваться [действием](./action.md) для определения и получения актора этого действия.

Интерфейс содержит один единственный метод
- <code><b>getActor()</b>: ?ActorInterface</code>

## <a id="DefaultActorProvider"></a>DefaultActorProvider
Класс <code><b>SprintF\Bundle\Wolfflow\Actor\DefaultActorProvider</b></code> определяет дефолтный провайдер актора. 

Класс реализует интерфейс `ActorProviderInterface` и в методе `getActor()` возвращает пользователя, полученного 
от стандартного хелпера `Symfony\Bundle\SecurityBundle\Security` (то есть текущего пользователя приложения).
В большинстве случаев этого достаточно и переопределения данной логики не потребуется.