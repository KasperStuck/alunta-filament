<?php

namespace KasperStuck\Alunta\Filament;

use Filament\Forms\Components\Field;
use KasperStuck\Alunta\Laravel\Contracts\Billable;

/**
 * The fields the Alunta subscription sync owns on a linked billable, such as its plan, status and
 * billing ids: shown read-only there and never saved by hand.
 */
final class OwnedByAlunta
{
    /**
     * Makes the field disabled and not dehydrated while the form's record is an Alunta-linked
     * billable. Without a record, as on a create page, the field stays editable.
     *
     * @template TField of Field
     *
     * @param  TField  $field
     * @return TField
     */
    public static function apply(Field $field): Field
    {
        return $field
            ->disabled(self::isOwned(...))
            ->dehydrated(fn (mixed $record): bool => ! self::isOwned($record));
    }

    /**
     * Whether the record is a billable that an Alunta subscription bills now.
     */
    public static function isOwned(mixed $record): bool
    {
        return $record instanceof Billable && $record->billingState()->isAluntaLinked();
    }
}
