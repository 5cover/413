<?php
namespace Cookie;

require_once 'util.php';

const MAX_COOKIE_TIMESTAMP = 253402300799;  // 9999-12-31T23:59:59+00:00

final readonly class RecentOffers
{
    private const NAME              = 'recent_offers';
    private const MAX_RECENT_OFFERS = 3;

    /**
     * @return int[]
     */
    static function get(): array
    {
        error_log('get cookie ' . self::NAME);
        return mapnull($_COOKIE[self::NAME] ?? null, fn($value) => array_map(intval(...), explode(',', $value))) ?? [];
    }

    static function add(int $offer_id): void
    {
        $value = self::get();
        if (($i_offer_id = array_search($offer_id, $value, true)) !== false) {
            array_pop_key($value, $i_offer_id);
        } elseif (count($value) === self::MAX_RECENT_OFFERS) {
            array_pop($value);
        }
        array_unshift($value, $offer_id);
        notfalse(setcookie(self::NAME, implode(',', $value), MAX_COOKIE_TIMESTAMP, '/'));
    }
}
