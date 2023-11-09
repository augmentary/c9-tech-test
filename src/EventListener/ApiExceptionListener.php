<?php

namespace App\EventListener;

use App\Exception\Domain\Booking\ClassAlreadyBookedBookingException;
use App\Exception\Domain\Booking\ClassAlreadyStartedBookingException;
use App\Exception\Domain\Booking\ClassFullyBookedBookingException;
use App\Exception\Domain\Booking\ClassNotBookableBookingException;
use App\Exception\Domain\Booking\ClassNotOpenBookingException;
use App\Exception\Domain\Booking\InsufficientRatingBookingException;
use App\Exception\Domain\Booking\UserBannedBookingException;
use App\Exception\Domain\BookingCancellation\BookedClassStartedCancellationException;
use App\Exception\Domain\BookingCancellation\ClassNotBookedCancellationException;
use App\Exception\Domain\Reservation\ClassAlreadyBookedReservationException;
use App\Exception\Domain\Reservation\ClassAlreadyReservedReservationException;
use App\Exception\Domain\Reservation\ClassAlreadyStartedReservationException;
use App\Exception\Domain\Reservation\ClassNotBookableReservationException;
use App\Exception\Domain\Reservation\ClassNotFullyBookedReservationException;
use App\Exception\Domain\Reservation\ClassNotOpenReservationException;
use App\Exception\Domain\Reservation\InsufficientRatingReservationException;
use App\Exception\Domain\Reservation\UserBannedReservationException;
use App\Exception\Domain\ReservationCancellation\ClassNotReservedCancellationException;
use App\Exception\Domain\ReservationCancellation\ReservedClassStartedCancellationException;
use App\Exception\InvalidDataException;
use App\Exception\MissingDataException;
use App\Exception\NotFoundException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

#[AsEventListener(event: 'kernel.exception')]
class ApiExceptionListener
{
    public function __construct(
        #[Autowire(param: 'kernel.debug')]
        private readonly bool $debug,
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $response = null;
        if ($exception instanceof InvalidDataException) {
            $response = $this->handleInvalidDataException($exception);
        }
        if ($exception instanceof MissingDataException) {
            $response = $this->handleMissingDataException($exception);
        }
        if ($exception instanceof NotFoundException) {
            $response = $this->handleNotFoundException($exception);
        }
        if ($exception instanceof HttpExceptionInterface) {
            $response = $this->handleHttpException($exception);
        }

        if (!$response) {
            if($this->debug) {
                throw $exception;
            }

            $response = $this->handleGenericException($exception);
        }

        $response = new JsonResponse(...$response);

        if ($this->debug) {
            $response->headers->set('X-Debug-Exception', rawurlencode($exception->getMessage()));
            $response->headers->set('X-Debug-Exception-File', rawurlencode($exception->getFile()).':'.$exception->getLine());
        }

        $event->setResponse($response);
    }


    /**
     * @return array{0: array{message: string, ref: string, errors: mixed}, 1: int}
     */
    protected function handleInvalidDataException(InvalidDataException $exception): array
    {
        return [[
            'message' => 'Bad Request',
            'ref' => 'badRequest',
            'errors' => $exception->getVars(),
        ], 400];
    }

    /**
     * @return array{0: array{message: string, ref: string, errors: list<string>}, 1: int}
     */
    protected function handleMissingDataException(MissingDataException $exception): array
    {
        return [[
            'message' => 'Missing required parameter',
            'ref' => 'missingParameter',
            'errors' => $exception->getVars(),
        ], 400];
    }

    /**
     * @return array{0: array{message: string, ref: string, fields: array<string,mixed>}, 1: int}
     */
    protected function handleNotFoundException(NotFoundException $exception): array
    {
        $parameter = $exception->getParameter();
        $value = $exception->getValue();

        return [[
            'message' => "$parameter '$value' not found.",
            'ref' => 'notFound',
            'fields' => [$parameter => $value]
        ], 404];
    }

    /**
     * @return array{0: array{message: string, ref: string}, 1: int}
     */
    protected function handleHttpException(HttpExceptionInterface $exception): array
    {
        return [[
            'message' => $exception->getMessage(),
            'ref' => 'unknownError',
        ], $exception->getStatusCode()];
    }

    /**
     * @return array{0: array{message: string, ref: string, reason: string}, 1: int}
     */
    protected function handleGenericException(\Throwable $exception): array
    {
        return [[
            'message' => 'An unexpected exception occurred',
            'ref' => 'unknownError',
            'reason' => 'unknown',
        ], 500];
    }
}
