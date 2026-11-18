<?php

namespace KasperStuck\Alunta\Filament;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use KasperStuck\Alunta\Laravel\Contracts\Billable;

/**
 * The read-only Alunta billing state of a linked billable, for a resource's form and infolist.
 *
 * Every entry reads Billable::billingState(), so the entry names are keys only and name no column of
 * the app's model.
 */
final class AluntaBillingSection
{
    public static function make(): Section
    {
        return Section::make('Betaling via Alunta')
            ->columns(3)
            ->visible(OwnedByAlunta::isOwned(...))
            ->schema([
                TextEntry::make('alunta_provider')
                    ->label('Betalingsudbyder')
                    ->state(fn (Billable $record): ?string => $record->billingState()->provider),
                TextEntry::make('alunta_subscription_id')
                    ->label('Alunta-abonnement')
                    ->state(fn (Billable $record): ?string => $record->billingState()->subscriptionId)
                    ->copyable(),
                TextEntry::make('alunta_current_period')
                    ->label('Betalt periode')
                    ->state(fn (Billable $record): ?string => self::currentPeriod($record))
                    ->placeholder('Ingen'),
                TextEntry::make('alunta_subscription_ends_at')
                    ->label('Udløber')
                    ->state(fn (Billable $record): mixed => $record->billingState()->subscriptionEndsAt)
                    ->date('d.m.Y')
                    ->placeholder('Ikke opsagt'),
                TextEntry::make('alunta_payment_failed_at')
                    ->label('Betaling fejlede')
                    ->state(fn (Billable $record): mixed => $record->billingState()->paymentFailedAt)
                    ->date('d.m.Y')
                    ->placeholder('Nej'),
                TextEntry::make('alunta_synced_at')
                    ->label('Synkroniseret')
                    ->state(fn (Billable $record): mixed => $record->billingState()->syncedAt)
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('Aldrig'),
            ]);
    }

    /**
     * The paid period as one string, for example "01.04.2026–30.04.2026", or null without both ends.
     */
    private static function currentPeriod(Billable $record): ?string
    {
        $state = $record->billingState();

        return $state->currentPeriodStartsAt && $state->currentPeriodEndsAt
            ? $state->currentPeriodStartsAt->format('d.m.Y').'–'.$state->currentPeriodEndsAt->format('d.m.Y')
            : null;
    }
}
