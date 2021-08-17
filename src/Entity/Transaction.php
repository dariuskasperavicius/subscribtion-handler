<?php

/** @noinspection PhpPropertyOnlyWrittenInspection */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;

/**
 * @ORM\Entity(repositoryClass=SubscribtionRepository::class)
 * @ORM\Table(
 * )
 */
class Transaction
{
    public const INITIAL = 'initial';
    public const DID_RENEW  = 'renew';
    public const DID_FAIL_TO_RENEW = 'failed_renew';
    public const CANCEL = 'cancel';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string")
     */
    private string $transactionId;

    /**
     * @ORM\Column(type="string")
     */
    private string $productId;

    /**
     * @ORM\Column(type="string")
     */
    private string $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Subscribtion", inversedBy="transaction", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private Subscribtion $subscription;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $dateTime;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $startDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTimeInterface $endDate;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function setTransactionId(string $transactionId): void
    {
        $this->transactionId = $transactionId;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function setProductId(string $productId): void
    {
        $this->productId = $productId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getDateTime(): DateTimeInterface
    {
        return $this->dateTime;
    }

    public function setDateTime(DateTimeInterface $dateTime): void
    {
        $this->dateTime = $dateTime;
    }

    public function getStartDate(): DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(DateTimeInterface $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): ?DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?DateTimeInterface $endDate): void
    {
        $this->endDate = $endDate;
    }

    public function getSubscription(): Subscribtion
    {
        return $this->subscription;
    }

    public function setSubscription(Subscribtion $subscription): void
    {
        $this->subscription = $subscription;
    }
}
