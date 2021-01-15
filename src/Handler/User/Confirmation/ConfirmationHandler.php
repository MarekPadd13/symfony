<?php

namespace App\Handler\User\Confirmation;

use App\Entity\User;

class ConfirmationHandler implements ConfirmationHandlerInterface
{
    /**
     * @var Confirmation
     */
    private $confirmation;

    /**
     * ConfirmationHandler constructor.
     */
    public function __construct(Confirmation $confirmation)
    {
        $this->confirmation = $confirmation;
    }

    /**
     * @param User $user
     *
     * @throws \Exception
     */
    public function handle($user): void
    {
        $this->confirmation->confirm($user, $this->errorMessage());
    }

    public function errorMessage(): string
    {
        return 'Your status is active';
    }
}
