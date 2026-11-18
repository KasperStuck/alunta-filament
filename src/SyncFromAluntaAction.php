<?php

namespace KasperStuck\Alunta\Filament;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Database\Eloquent\Model;
use KasperStuck\Alunta\Laravel\Contracts\Billable;
use KasperStuck\Alunta\Laravel\Sync\AluntaSubscriptionSync;
use KasperStuck\Alunta\Sdk\AluntaApiException;

/**
 * Reads a linked billable's Alunta subscription back now instead of waiting for the next webhook or
 * reconciliation, then refreshes the record. Visible only on an Alunta-linked billable.
 *
 * A failed sync cancels the action, so its after() hook runs only after a completed sync. An edit
 * page refills its form there, because EditRecord::fillForm() is protected:
 *
 *     SyncFromAluntaAction::make()->after(fn () => $this->fillForm())
 */
class SyncFromAluntaAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'syncFromAlunta';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Synkroniser fra Alunta')
            ->icon(Heroicon::ArrowPath)
            ->color('gray')
            ->visible(OwnedByAlunta::isOwned(...))
            ->action(fn (Model&Billable $record) => $this->syncFromAlunta($record));
    }

    private function syncFromAlunta(Model&Billable $record): void
    {
        try {
            $synced = app(AluntaSubscriptionSync::class)->sync((string) $record->billingState()->subscriptionId);
        } catch (AluntaApiException $exception) {
            Notification::make()->title('Synkroniseringen fra Alunta mislykkedes')->body($exception->getMessage())->danger()->send();

            $this->cancel();

            return;
        } catch (LockTimeoutException) {
            Notification::make()->title('Abonnementet opdateres lige nu. Prøv igen om lidt.')->warning()->send();

            $this->cancel();

            return;
        }

        $record->refresh();

        if ($synced === null) {
            Notification::make()->title('Alunta ændrede ikke organisationen')->body('Se loggen, hvis du ventede en ændring.')->warning()->send();

            return;
        }

        Notification::make()->title('Organisationen er synkroniseret fra Alunta')->success()->send();
    }
}
