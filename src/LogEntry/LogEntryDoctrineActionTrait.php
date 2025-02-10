<?php

namespace SprintF\Bundle\Wolfflow\LogEntry;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AssignedGenerator;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * Базовая реализация методов Action::insertLogEntry() и Action::updateLogEntry() для Doctrine.
 */
trait LogEntryDoctrineActionTrait
{
    protected EntityManagerInterface $em;

    #[Required]
    public function setEntityManager(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    protected function insertLogEntry(LogEntryInterface $logEntry): LogEntryInterface
    {
        $this->em->persist($logEntry);

        $metadata = $this->em->getClassMetadata(get_class($logEntry));
        $idGenerator = $metadata->idGenerator;

        $connection = $this->em->getConnection();

        if (empty($metadata->getIdentifierValues($logEntry))) {
            if (!$idGenerator->isPostInsertGenerator()) {
                $generatedIdValue = $idGenerator->generateId($this->em, $logEntry);
                if (!$idGenerator instanceof AssignedGenerator) {
                    $convertedIdValue = $connection->convertToPHPValue(
                        $generatedIdValue,
                        $metadata->getTypeOfField($metadata->getSingleIdentifierFieldName()),
                    );
                    $idValue = [$metadata->getSingleIdentifierFieldName() => $convertedIdValue];
                    $metadata->setIdentifierValues($logEntry, $idValue);
                }
            }
        }

        $qb = $connection->createQueryBuilder();
        $qb->insert($metadata->getTableName());

        if (!empty($metadata->getIdentifierValues($logEntry))) {
            $qb->setValue(
                $metadata->getSingleIdentifierColumnName(),
                $qb->createNamedParameter(
                    $metadata->getIdentifierValues($logEntry)[$metadata->getSingleIdentifierFieldName()],
                    $metadata->getTypeOfField($metadata->getSingleIdentifierFieldName()),
                )
            );
        }

        $column = $metadata->getColumnName('actionClass');
        $type = $metadata->getTypeOfField('actionClass');
        $qb->setValue($column, $qb->createNamedParameter(
            $connection->convertToDatabaseValue(
                $logEntry->getActionClass(),
                $type
            )
        ));

        $column = $metadata->getColumnName('entityClass');
        $type = $metadata->getTypeOfField('entityClass');
        $qb->setValue($column, $qb->createNamedParameter(
            $connection->convertToDatabaseValue(
                $logEntry->getEntityClass(),
                $type
            )
        ));

        $column = $metadata->getSingleAssociationJoinColumnName('entity');
        if (!$logEntry->getEntity()->isNew()) {
            $entityMetadata = $this->em->getClassMetadata(get_class($logEntry->getEntity()));
            $type = $entityMetadata->getTypeOfField($entityMetadata->getSingleIdentifierFieldName());
            $qb->setValue($column, $qb->createNamedParameter(
                $connection->convertToDatabaseValue(
                    $logEntry->getEntityId(),
                    $type
                )
            ));
        } else {
            $qb->setValue($column, null);
        }

        $column = $metadata->getColumnName('contextInDb');
        $type = $metadata->getTypeOfField('contextInDb');
        $qb->setValue($column, $qb->createNamedParameter(
            $connection->convertToDatabaseValue(
                $logEntry->getContext(),
                $type
            )
        ));

        $column = $metadata->getSingleAssociationJoinColumnName('actor');
        if (null !== $logEntry->getActor()) {
            $actorMetadata = $this->em->getClassMetadata(get_class($logEntry->getActor()));
            $type = $actorMetadata->getTypeOfField($actorMetadata->getSingleIdentifierFieldName());
            $qb->setValue($column, $qb->createNamedParameter(
                $connection->convertToDatabaseValue(
                    $logEntry->getActor()->getId(),
                    $type
                )
            ));
        } else {
            $qb->setValue($column, null);
        }

        $column = $metadata->getColumnName('startedAt');
        $type = $metadata->getTypeOfField('startedAt');
        $qb->setValue($column, $qb->createNamedParameter(
            $connection->convertToDatabaseValue(
                $logEntry->getStartedAt(),
                $type
            )
        ));

        $column = $metadata->getColumnName('finishedAt');
        $type = $metadata->getTypeOfField('finishedAt');
        $qb->setValue($column, $qb->createNamedParameter(
            $connection->convertToDatabaseValue(
                $logEntry->getFinishedAt(),
                $type
            )
        ));

        $column = $metadata->getColumnName('result');
        $type = $metadata->getTypeOfField('result');
        $qb->setValue($column, $qb->createNamedParameter(
            $connection->convertToDatabaseValue(
                $logEntry->getResult()->value,
                $type
            )
        ));

        $column = $metadata->getColumnName('reason');
        $type = $metadata->getTypeOfField('reason');
        $qb->setValue($column, $qb->createNamedParameter(
            $connection->convertToDatabaseValue(
                $logEntry->getReason(),
                $type
            )
        ));

        try {
            $connection->beginTransaction();
            $qb->executeStatement();
            $connection->commit();
        } catch (\Throwable $exception) {
            $connection->rollBack();

            return $logEntry;
        }

        if ($idGenerator->isPostInsertGenerator()) {
            $idValue = $idGenerator->generateId($this->em, $logEntry);
            if (!$idGenerator instanceof AssignedGenerator) {
                $convertedIdValue = $connection->convertToPHPValue(
                    $idValue,
                    $metadata->getTypeOfField($metadata->getSingleIdentifierFieldName()),
                );
                $idValue = [$metadata->getSingleIdentifierFieldName() => $convertedIdValue];
                $metadata->setIdentifierValues($logEntry, $idValue);
            }
        }

        $this->em->refresh($logEntry);

        return $logEntry;
    }

    protected function updateLogEntry(LogEntryInterface $logEntry): LogEntryInterface
    {
        $metadata = $this->em->getClassMetadata(get_class($logEntry));

        $connection = $this->em->getConnection();
        $qb = $connection->createQueryBuilder();
        $qb->update($metadata->getTableName());

        $qb->where(
            $qb->expr()->eq(
                $metadata->getSingleIdentifierColumnName(),
                $qb->createNamedParameter(
                    $connection->convertToDatabaseValue(
                        $metadata->getFieldValue($logEntry, $metadata->getSingleIdentifierFieldName()),
                        $metadata->getTypeOfField($metadata->getSingleIdentifierFieldName())
                    )
                )
            )
        );

        $column = $metadata->getColumnName('finishedAt');
        $type = $metadata->getTypeOfField('finishedAt');
        $qb->set($column, $qb->createNamedParameter(
            $connection->convertToDatabaseValue(
                $logEntry->getFinishedAt(),
                $type
            )
        ));

        $column = $metadata->getColumnName('result');
        $type = $metadata->getTypeOfField('result');
        $qb->set($column, $qb->createNamedParameter(
            $connection->convertToDatabaseValue(
                $logEntry->getResult()->value,
                $type
            )
        ));

        $column = $metadata->getColumnName('reason');
        $type = $metadata->getTypeOfField('reason');
        $qb->set($column, $qb->createNamedParameter(
            $connection->convertToDatabaseValue(
                $logEntry->getReason(),
                $type
            )
        ));

        try {
            $connection->beginTransaction();
            $qb->executeStatement();
            $connection->commit();
            $this->em->refresh($logEntry);
        } catch (\Throwable) {
            $connection->rollBack();
        }

        return $logEntry;
    }
}
