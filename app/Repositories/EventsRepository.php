<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Account;
use App\Models\Event;
use App\ValueObjects\Events;
use App\Interface\IEventsRepository;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Services\LogTransferService;
use App\Exceptions\ConcurrencyException;

class EventsRepository implements IEventsRepository
{
    public function getEventsOfAgregate(Account $agregate): Events
    {
        $eventsCollection = Event::where('account_id', $agregate->id)->orderBy('version', 'asc')->get();

        $events = new Events();

        foreach ($eventsCollection as $event) {
            $events->addEvent($event);
        }

        return $events;
    }

    public function persistAgreggateEvents(Account $agregate): void
    {
        $pendingEvents = $agregate->getPendingEvents();

        if (empty($pendingEvents)) {
            return;
        }

        try {
            DB::beginTransaction();

            $version = $this->getVersionOfLastEvent($agregate);

            if ($agregate->versionOfLastEvent !== $version) {
                // Lock otimista.
                throw new ConcurrencyException(
                    "Conflito de concorrência.
                    Versão esperada: {$agregate->versionOfLastEvent}, Versão do banco: {$version}"
                );
            }

            foreach ($pendingEvents as $event) {
                Event::create([
                    'account_id' => $agregate->id,
                    'type' => class_basename($event),
                    'payload' => json_encode($event),
                    'version' => ++$version,
                ]);

                $agregate->versionOfLastEvent = $version;
            }

            // Atualiza a projeção com o balance atualizado.
            $agregate->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            LogTransferService::critical($e->getMessage(), [$e->getTraceAsString()]);
            throw $e; // Exception peronsalizada.
        } catch (ConcurrencyException $e) {
            // TODO: Retry.
            DB::rollBack();
            LogTransferService::warning($e->getMessage(), [$e->getTraceAsString()]);
            throw $e;
        }
    }

    public function getVersionOfLastEvent(Account $agregate): int
    {
        return (int)Event::where('account_id', $agregate->id)->max('version');
    }
}
